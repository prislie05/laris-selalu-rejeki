<?php

namespace Database\Seeders;

use App\Models\FinancialLedger;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Default Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@tokolily.com'],
            [
                'name' => 'Admin Toko Lily',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_banned' => false,
                'phone' => '081234567890',
            ]
        );

        $seller = User::firstOrCreate(
            ['email' => 'seller@tokolily.com'],
            [
                'name' => 'Kasir Lily',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'is_banned' => false,
                'phone' => '081234567891',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@tokolily.com'],
            [
                'name' => 'Pelanggan Lily',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_banned' => false,
                'phone' => '081234567892',
            ]
        );

        // 2. Seed Retail Products for Toko Lily
        $products = [
            [
                'name' => 'Beras Ramos Super 5kg',
                'slug' => 'beras-ramos-super-5kg',
                'category' => 'Sembako',
                'description' => 'Beras pulen pilihan kualitas premium tanpa pemutih dan pewangi.',
                'price' => 74500,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Minyak Goreng Bimoli 2L',
                'slug' => 'minyak-goreng-bimoli-2l',
                'category' => 'Sembako',
                'description' => 'Minyak kelapa sawit murni kualitas istimewa menghasilkan gorengan renyah.',
                'price' => 38500,
                'stock' => 18,
                'image_url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Gula Pasir Gulaku 1kg',
                'slug' => 'gula-pasir-gulaku-1kg',
                'category' => 'Sembako',
                'description' => 'Gula kristal putih murni dari tebu alami berkualitas.',
                'price' => 17500,
                'stock' => 4, // Menipis untuk trigger warning stok tipis
                'image_url' => 'https://images.unsplash.com/photo-1581441363689-1f3c3c414635?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Telur Ayam Negeri 1kg',
                'slug' => 'telur-ayam-negeri-1kg',
                'category' => 'Sembako',
                'description' => 'Telur ayam segar langsung dari peternakan, isi 15-16 butir.',
                'price' => 28000,
                'stock' => 2, // Kritis
                'image_url' => 'https://images.unsplash.com/photo-1506976785307-8732e854ad03?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Indomie Goreng Spesial',
                'slug' => 'indomie-goreng-spesial',
                'category' => 'Makanan Ringan',
                'description' => 'Mi instan goreng favorit keluarga Indonesia dengan bawang goreng renyah.',
                'price' => 3500,
                'stock' => 120,
                'image_url' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Kopi Kapal Api Spesial 165g',
                'slug' => 'kopi-kapal-api-spesial-165g',
                'category' => 'Minuman',
                'description' => 'Kopi bubuk murni dengan aroma harum mantap citarasa nusantara.',
                'price' => 15500,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Teh Celup Sariwangi 30s',
                'slug' => 'teh-celup-sariwangi-30s',
                'category' => 'Minuman',
                'description' => 'Teh hitam Indonesia berkualitas dalam kantung celup higienis.',
                'price' => 8500,
                'stock' => 22,
                'image_url' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Susu Ultra Milk Cokelat 1L',
                'slug' => 'susu-ultra-milk-cokelat-1l',
                'category' => 'Minuman',
                'description' => 'Susu UHT rasa cokelat lezat bernutrisi tinggi dan kalsium alami.',
                'price' => 21000,
                'stock' => 0, // Stok habis untuk pengujian validasi real-time keranjang
                'image_url' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Sabun Mandi Lifebuoy 4x85g',
                'slug' => 'sabun-mandi-lifebuoy-4x85g',
                'category' => 'Kebutuhan Rumah',
                'description' => 'Sabun batang perlindungan kuman total dengan formula antibakterial.',
                'price' => 16000,
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1607006314181-e2311bca03f8?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Deterjen Daia 850g',
                'slug' => 'deterjen-daia-850g',
                'category' => 'Kebutuhan Rumah',
                'description' => 'Deterjen bubuk dengan butiran pembersih konsentrat wangi tahan lama.',
                'price' => 19500,
                'stock' => 12,
                'image_url' => 'https://images.unsplash.com/photo-1583947215259-38e31be8751f?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(['slug' => $prod['slug']], $prod);
        }

        // 3. Seed Initial Financial Ledgers (Modal & Initial Stock Expense)
        $ledgers = [
            [
                'user_id' => $admin->id,
                'type' => 'income',
                'category' => 'Modal Awal',
                'amount' => 5000000,
                'description' => 'Penyetoran modal awal kas operasional Toko Lily',
                'transaction_date' => Carbon::now()->subDays(14)->toDateString(),
            ],
            [
                'user_id' => $admin->id,
                'type' => 'expense',
                'category' => 'Belanja Stok Barang',
                'amount' => 2500000,
                'description' => 'Pengadaan stok sembako, minuman, dan snack dari distributor',
                'transaction_date' => Carbon::now()->subDays(10)->toDateString(),
            ],
            [
                'user_id' => $admin->id,
                'type' => 'expense',
                'category' => 'Operasional Toko',
                'amount' => 150000,
                'description' => 'Pembelian plastik kresek, kantong belanja, dan kertas thermal kasir',
                'transaction_date' => Carbon::now()->subDays(5)->toDateString(),
            ],
            [
                'user_id' => $seller->id,
                'type' => 'income',
                'category' => 'Penjualan Kasir POS',
                'amount' => 320000,
                'description' => 'Rekapitulasi penjualan kasir shift kemarin',
                'transaction_date' => Carbon::now()->subDays(1)->toDateString(),
            ],
        ];

        foreach ($ledgers as $ledger) {
            FinancialLedger::create($ledger);
        }
    }
}
