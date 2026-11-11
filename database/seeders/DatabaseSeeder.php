<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users for each role
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@pos.com'],
            [
                'name' => 'Budi Kasir',
                'password' => Hash::make('password123'),
                'role' => 'UKasir',
            ]
        );

        $gudang = User::updateOrCreate(
            ['email' => 'gudang@pos.com'],
            [
                'name' => 'Siti Gudang',
                'password' => Hash::make('password123'),
                'role' => 'UGudang',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Hendro Admin',
                'password' => Hash::make('password123'),
                'role' => 'UAdmin',
            ]
        );

        // 2. 20 Sample Products with realistic barcodes, stock, cost_price, and price
        $productsData = [
            [
                'barcode' => '899999900101',
                'name' => 'Indomie Goreng Spesial 85g',
                'description' => 'Mi instan goreng rasa spesial dengan bawang goreng renyah.',
                'stock' => 120,
                'cost_price' => 2800,
                'price' => 3500,
            ],
            [
                'barcode' => '899999900102',
                'name' => 'Aqua Air Mineral 600ml',
                'description' => 'Air mineral pegunungan alami botol 600ml.',
                'stock' => 75,
                'cost_price' => 2500,
                'price' => 4000,
            ],
            [
                'barcode' => '899999900103',
                'name' => 'Teh Botol Sosro Original 350ml',
                'description' => 'Minuman teh melati dalam kemasan botol plastik pet.',
                'stock' => 45,
                'cost_price' => 3500,
                'price' => 5000,
            ],
            [
                'barcode' => '899999900104',
                'name' => 'Ultra Milk Cokelat 250ml',
                'description' => 'Susu UHT segar rasa cokelat kaya kalsium dan vitamin.',
                'stock' => 6, // CRITICAL STOCK (<10)
                'cost_price' => 5200,
                'price' => 7000,
            ],
            [
                'barcode' => '899999900105',
                'name' => 'Kopi Kapal Api Spesial Mix 24g',
                'description' => 'Kopi bubuk instan plus gula aroma mantap.',
                'stock' => 90,
                'cost_price' => 1500,
                'price' => 2500,
            ],
            [
                'barcode' => '899999900106',
                'name' => 'SilverQueen Cashew 58g',
                'description' => 'Cokelat susu lezat dengan kacang mete renyah.',
                'stock' => 5, // CRITICAL STOCK (<10)
                'cost_price' => 13500,
                'price' => 17500,
            ],
            [
                'barcode' => '899999900107',
                'name' => 'Chitato Sapi Panggang 68g',
                'description' => 'Keripik kentang bergelombang rasa daging sapi panggang gurih.',
                'stock' => 32,
                'cost_price' => 8500,
                'price' => 11500,
            ],
            [
                'barcode' => '899999900108',
                'name' => 'Oreo Sandwich Vanilla 133g',
                'description' => 'Biskuit lapis cokelat dengan krim vanila manis.',
                'stock' => 40,
                'cost_price' => 7200,
                'price' => 9500,
            ],
            [
                'barcode' => '899999900109',
                'name' => 'Pringles Original 107g',
                'description' => 'Keripik kentang renyah kemasan tabung higienis.',
                'stock' => 8, // CRITICAL STOCK (<10)
                'cost_price' => 18000,
                'price' => 24000,
            ],
            [
                'barcode' => '899999900110',
                'name' => 'Beng Beng Chocolate Wafer 25g',
                'description' => 'Wafer lapis karamel berbalut cokelat dan rice crispy.',
                'stock' => 150,
                'cost_price' => 1800,
                'price' => 3000,
            ],
            [
                'barcode' => '899999900111',
                'name' => 'Pepsodent Pencegah Gigi Berlubang 190g',
                'description' => 'Pasta gigi dengan pro-fluoride dan kalsium aktif.',
                'stock' => 55,
                'cost_price' => 12500,
                'price' => 16500,
            ],
            [
                'barcode' => '899999900112',
                'name' => 'Lifebuoy Sabun Cair Total 10 450ml',
                'description' => 'Sabun mandi cair antibakteri formula activ-silver.',
                'stock' => 3, // CRITICAL STOCK (<10)
                'cost_price' => 21000,
                'price' => 27000,
            ],
            [
                'barcode' => '899999900113',
                'name' => 'Sunsilk Black Shine Shampoo 170ml',
                'description' => 'Shampoo rambut hitam berkilau dengan urang-aring.',
                'stock' => 24,
                'cost_price' => 19000,
                'price' => 25000,
            ],
            [
                'barcode' => '899999900114',
                'name' => 'Sunlight Jeruk Nipis 750ml',
                'description' => 'Cairan pencuci piring ekstrak jeruk nipis 100x bersihkan lemak.',
                'stock' => 38,
                'cost_price' => 14000,
                'price' => 18500,
            ],
            [
                'barcode' => '899999900115',
                'name' => 'Rinso Anti Noda Classic 770g',
                'description' => 'Deterjen bubuk konsentrat hilangkan noda membandel 1x kucek.',
                'stock' => 29,
                'cost_price' => 22000,
                'price' => 28000,
            ],
            [
                'barcode' => '899999900116',
                'name' => 'Baygon Aerosol Lavender 600ml',
                'description' => 'Obat nyamuk semprot wangi lavender basmi serangga cepat.',
                'stock' => 18,
                'cost_price' => 34000,
                'price' => 42000,
            ],
            [
                'barcode' => '899999900117',
                'name' => 'Paseo Tissue Facial 250 Sheets',
                'description' => 'Tisu wajah lembut berbahan 100% serat alami berkualitas.',
                'stock' => 60,
                'cost_price' => 13000,
                'price' => 17000,
            ],
            [
                'barcode' => '899999900118',
                'name' => 'Beras Ramos Super Premium 5kg',
                'description' => 'Beras pulen pilihan tanpa pemutih dan tanpa pengawet.',
                'stock' => 22,
                'cost_price' => 64000,
                'price' => 75000,
            ],
            [
                'barcode' => '899999900119',
                'name' => 'Minyak Goreng Bimoli Klasik Pouch 2L',
                'description' => 'Minyak goreng kelapa sawit higienis jernih tahan panas.',
                'stock' => 35,
                'cost_price' => 33000,
                'price' => 39000,
            ],
            [
                'barcode' => '899999900120',
                'name' => 'Gulaku Gula Pasir Tebu Kuning 1kg',
                'description' => 'Gula kristal tebu murni manis alami butiran halus.',
                'stock' => 45,
                'cost_price' => 14500,
                'price' => 18000,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $createdProducts[] = Product::updateOrCreate(
                ['barcode' => $data['barcode']],
                $data
            );
        }

        // 3. Create realistic sample transactions for the last 5 days
        // This ensures the Admin Dashboard charts, profit analysis, and activity stream look lively immediately.
        $paymentMethods = ['Cash', 'QRIS', 'Debit Card'];

        // Sample past dates
        $transactionsSeed = [
            [
                'days_ago' => 4,
                'items' => [
                    ['product_idx' => 0, 'qty' => 5], // Indomie
                    ['product_idx' => 1, 'qty' => 3], // Aqua
                    ['product_idx' => 4, 'qty' => 4], // Kopi
                ],
                'method' => 'Cash',
            ],
            [
                'days_ago' => 3,
                'items' => [
                    ['product_idx' => 17, 'qty' => 1], // Beras
                    ['product_idx' => 18, 'qty' => 1], // Minyak Bimoli
                    ['product_idx' => 19, 'qty' => 2], // Gulaku
                ],
                'method' => 'Debit Card',
            ],
            [
                'days_ago' => 2,
                'items' => [
                    ['product_idx' => 5, 'qty' => 2], // SilverQueen
                    ['product_idx' => 6, 'qty' => 2], // Chitato
                    ['product_idx' => 7, 'qty' => 1], // Oreo
                ],
                'method' => 'QRIS',
            ],
            [
                'days_ago' => 1,
                'items' => [
                    ['product_idx' => 10, 'qty' => 2], // Pepsodent
                    ['product_idx' => 11, 'qty' => 1], // Lifebuoy
                    ['product_idx' => 13, 'qty' => 2], // Sunlight
                    ['product_idx' => 14, 'qty' => 1], // Rinso
                ],
                'method' => 'Cash',
            ],
            [
                'days_ago' => 0, // Today
                'items' => [
                    ['product_idx' => 0, 'qty' => 10], // Indomie
                    ['product_idx' => 1, 'qty' => 6],  // Aqua
                    ['product_idx' => 2, 'qty' => 4],  // Teh botol
                    ['product_idx' => 9, 'qty' => 5],  // Beng Beng
                ],
                'method' => 'QRIS',
            ],
        ];

        foreach ($transactionsSeed as $idx => $tData) {
            $date = Carbon::now()->subDays($tData['days_ago'])->setTime(rand(9, 19), rand(10, 55));
            $invoiceNumber = 'TRX-' . $date->format('Ymd') . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);

            // Compute total
            $totalAmount = 0;
            $itemsToCreate = [];

            foreach ($tData['items'] as $item) {
                $p = $createdProducts[$item['product_idx']];
                $subtotal = $p->price * $item['qty'];
                $totalAmount += $subtotal;
                $itemsToCreate[] = [
                    'product' => $p,
                    'qty' => $item['qty'],
                    'price' => $p->price,
                    'cost_price' => $p->cost_price,
                    'subtotal' => $subtotal,
                ];
            }

            $paymentAmount = $tData['method'] === 'Cash' ? ceil($totalAmount / 10000) * 10000 : $totalAmount;
            $changeAmount = $paymentAmount - $totalAmount;

            $transaction = Transaction::updateOrCreate(
                ['invoice_number' => $invoiceNumber],
                [
                    'user_id' => $kasir->id,
                    'total_amount' => $totalAmount,
                    'payment_amount' => $paymentAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $tData['method'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );

            // Clear previous details if re-seeding
            TransactionDetail::where('transaction_id', $transaction->id)->delete();

            foreach ($itemsToCreate as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }

        // 4. Sample warehouse log
        StockAdjustment::updateOrCreate(
            ['reason' => 'Restock pengiriman awal distributor PT Retail Indo'],
            [
                'product_id' => $createdProducts[0]->id,
                'user_id' => $gudang->id,
                'type' => 'INCOMING',
                'quantity_before' => 70,
                'quantity_change' => 50,
                'quantity_after' => 120,
            ]
        );

        // 5. Sample activity logs for Admin monitoring
        \App\Models\ActivityLog::truncate();
        \App\Models\ActivityLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'user_role' => $admin->role,
            'action' => 'LOGIN',
            'description' => 'Admin Hendro berhasil masuk ke sistem monitoring',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(8),
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $gudang->id,
            'user_name' => $gudang->name,
            'user_role' => $gudang->role,
            'action' => 'STOCK_INCOMING',
            'description' => 'Menerima pasokan stok barang: Indomie Goreng Spesial 85g (+50 pcs) dari PT Retail Indo',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(5),
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $gudang->id,
            'user_name' => $gudang->name,
            'user_role' => $gudang->role,
            'action' => 'STOCK_ADJUSTMENT',
            'description' => 'Penyesuaian stok opname: Lifebuoy Sabun Cair (-2 pcs) alasan: kemasan bocor saat display',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(3),
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $kasir->id,
            'user_name' => $kasir->name,
            'user_role' => $kasir->role,
            'action' => 'TRANSACTION_CREATED',
            'description' => 'Transaksi penjualan baru TRX-20261026-0005 total Rp 79.000 (QRIS)',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(1),
        ]);
    }
}
