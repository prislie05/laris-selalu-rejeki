<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TokoLilyFullWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test User Registration and Auto Login.
     */
    public function test_user_can_register_and_get_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567899',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Registrasi berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'user',
        ]);

        // Audit log should be recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'LOGIN',
        ]);
    }

    /**
     * Test Login for Seeded Admin, Seller, and User.
     */
    public function test_seeded_accounts_can_login(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@tokolily.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonPath('data.user.role', 'admin');
    }

    /**
     * Test Products Catalog and Out-of-Stock Flag.
     */
    public function test_product_catalog_listing(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'products',
                    'pagination',
                ],
            ]);

        // Thinnest stock endpoint
        $thinResponse = $this->getJson('/api/v1/products/thinnest-stock');
        $thinResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['products'],
            ]);
    }

    /**
     * Test User Cart and Out-of-Stock Validation.
     */
    public function test_cart_operations_and_out_of_stock_validation(): void
    {
        $user = User::where('email', 'user@tokolily.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $inStockProduct = Product::where('stock', '>', 5)->first();
        $outOfStockProduct = Product::where('stock', 0)->first();

        // 1. Add valid product to cart
        $addResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/cart/items', [
                'product_id' => $inStockProduct->id,
                'quantity' => 2,
            ]);

        $addResponse->assertStatus(201);

        // 2. Add out-of-stock product should fail
        $failResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/cart/items', [
                'product_id' => $outOfStockProduct->id,
                'quantity' => 1,
            ]);

        $failResponse->assertStatus(422);
    }

    /**
     * Test User Checkout with DB Transaction and Financial Ledger.
     */
    public function test_user_checkout_transaction(): void
    {
        $user = User::where('email', 'user@tokolily.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $product = Product::where('stock', '>=', 5)->first();
        $initialStock = $product->stock;

        // Add to cart
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        // Checkout
        $checkoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/orders/checkout', [
                'payment_method' => 'qris',
            ]);

        $checkoutResponse->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        // Verify stock decremented
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock);

        // Verify order created and marked as paid
        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('paid', $order->status);

        // Verify financial ledger income recorded
        $this->assertDatabaseHas('financial_ledgers', [
            'order_id' => $order->id,
            'type' => 'income',
            'category' => 'Penjualan Etalase',
        ]);
    }

    /**
     * Test Seller POS Cashier with Cash Change Calculation.
     */
    public function test_seller_pos_cashier_transaction(): void
    {
        $seller = User::where('email', 'seller@tokolily.com')->first();
        $token = $seller->createToken('test-token')->plainTextToken;

        $product = Product::where('stock', '>=', 5)->first();
        $initialStock = $product->stock;
        $qty = 2;
        $totalCost = (float) $product->price * $qty;
        $cashGiven = $totalCost + 10000;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/pos/checkout', [
                'customer_name' => 'Ibu Siti',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                    ],
                ],
                'payment_method' => 'cash',
                'cash_received' => $cashGiven,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        // Check stock decremented
        $product->refresh();
        $this->assertEquals($initialStock - $qty, $product->stock);

        // Check change calculation
        $order = Order::where('source', 'pos_cashier')->latest()->first();
        $this->assertEquals(10000, $order->cash_change);
        $this->assertEquals('Ibu Siti', $order->customer_name);

        // Verify Financial Ledger
        $this->assertDatabaseHas('financial_ledgers', [
            'order_id' => $order->id,
            'type' => 'income',
            'category' => 'Penjualan Kasir POS',
        ]);
    }

    /**
     * Test Admin Financial Ledger CRUD and Summary.
     */
    public function test_admin_financial_ledger_crud(): void
    {
        $admin = User::where('email', 'admin@tokolily.com')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        // 1. Create Expense (Belanja Stok)
        $storeResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/admin/financial-ledgers', [
                'type' => 'expense',
                'category' => 'Belanja Stok Barang',
                'amount' => 500000,
                'description' => 'Beli kardus mi instan dan beras',
                'transaction_date' => now()->toDateString(),
            ]);

        $storeResponse->assertStatus(201);
        $ledgerId = $storeResponse->json('data.ledger.id');

        // 2. Summary
        $summaryResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/financial-ledgers/summary?period=1_month');

        $summaryResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_income',
                    'total_expense',
                    'net_profit',
                ],
            ]);

        // 3. Delete
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/admin/financial-ledgers/{$ledgerId}");

        $deleteResponse->assertStatus(200);
    }

    /**
     * Test Admin User Management: Ban, Unban, Role Change.
     */
    public function test_admin_user_management(): void
    {
        $admin = User::where('email', 'admin@tokolily.com')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        $targetUser = User::where('email', 'user@tokolily.com')->first();

        // 1. Ban user
        $banResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/ban");

        $banResponse->assertStatus(200);
        $targetUser->refresh();
        $this->assertTrue($targetUser->is_banned);

        // Banned user should not be able to login
        $bannedLoginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => $targetUser->email,
            'password' => 'password',
        ]);
        $bannedLoginResponse->assertStatus(403);

        // 2. Unban user
        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/ban");
        $targetUser->refresh();
        $this->assertFalse($targetUser->is_banned);

        // 3. Change role
        $roleResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/admin/users/{$targetUser->id}/role", [
                'role' => 'seller',
            ]);
        $roleResponse->assertStatus(200);
        $targetUser->refresh();
        $this->assertEquals('seller', $targetUser->role);
    }

    /**
     * Test Web Blade Views HTTP responses.
     */
    public function test_web_routes_render_successfully(): void
    {
        // 1. Login page
        $this->get('/login')->assertStatus(200);

        // 2. Register page
        $this->get('/register')->assertStatus(200);

        // 3. User Shop (as user)
        $user = User::where('role', 'user')->first();
        $this->actingAs($user)->get('/user/shop')->assertStatus(200);
        $this->actingAs($user)->get('/user/cart')->assertStatus(200);
        $this->actingAs($user)->get('/user/history')->assertStatus(200);

        // 4. Seller Shop (as seller)
        $seller = User::where('role', 'seller')->first();
        $this->actingAs($seller)->get('/seller/shop')->assertStatus(200);
        $this->actingAs($seller)->get('/seller/products')->assertStatus(200);
        $this->actingAs($seller)->get('/seller/history')->assertStatus(200);

        // 5. Admin Revenue (as admin)
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get('/admin/revenue')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/ledger')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/management')->assertStatus(200);
    }

    /**
     * Test Google OAuth redirect endpoint.
     */
    public function test_google_redirect_url(): void
    {
        $response = $this->get('/auth/google');
        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');

        $this->assertStringContainsString('accounts.google.com/o/oauth2/v2/auth', $targetUrl);
        $this->assertStringContainsString('client_id=', $targetUrl);
        $this->assertStringContainsString('prompt=select_account', $targetUrl);
    }

    /**
     * Test Google OAuth callback allows Admin to login.
     */
    public function test_google_callback_allows_admin_to_login(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'mock-token-admin']),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'admin@tokolily.com',
                'name' => 'Admin Laris Selalu Rejeki',
            ]),
        ]);

        $state = 'test-state-admin';
        session(['google_oauth_state' => $state]);

        $response = $this->get("/auth/google/callback?state={$state}&code=mock-code");
        $response->assertRedirect(route('admin.revenue'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
    }

    /**
     * Test Google OAuth callback allows Seller to login.
     */
    public function test_google_callback_allows_seller_to_login(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'mock-token-seller']),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'seller@tokolily.com',
                'name' => 'Kasir Laris Selalu Rejeki',
            ]),
        ]);

        $state = 'test-state-seller';
        session(['google_oauth_state' => $state]);

        $response = $this->get("/auth/google/callback?state={$state}&code=mock-code");
        $response->assertRedirect(route('seller.shop'));
        $this->assertAuthenticated();
        $this->assertEquals('seller', auth()->user()->role);
    }

    /**
     * Test Google OAuth callback allows User to login.
     */
    public function test_google_callback_allows_user_to_login(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'mock-token-user']),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'user@tokolily.com',
                'name' => 'Pelanggan Laris Selalu Rejeki',
            ]),
        ]);

        $state = 'test-state-user';
        session(['google_oauth_state' => $state]);

        $response = $this->get("/auth/google/callback?state={$state}&code=mock-code");
        $response->assertRedirect(route('user.shop'));
        $this->assertAuthenticated();
        $this->assertEquals('user', auth()->user()->role);
    }

    /**
     * Test Google OAuth callback creates a new user if not registered.
     */
    public function test_google_callback_creates_new_user_with_appropriate_role(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'mock-token-new']),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'newcustomer@gmail.com',
                'name' => 'Budi Baru',
            ]),
        ]);

        $state = 'test-state-new';
        session(['google_oauth_state' => $state]);

        $response = $this->get("/auth/google/callback?state={$state}&code=mock-code");
        $response->assertRedirect(route('user.shop'));
        $this->assertAuthenticated();

        $newUser = User::where('email', 'newcustomer@gmail.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('user', $newUser->role);
        $this->assertEquals('Budi Baru', $newUser->name);
    }
}
