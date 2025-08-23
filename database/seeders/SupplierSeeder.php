<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP00001',
                'name' => 'CV. Gula Manis',
                'email' => 'sales@gulamanis.co.id',
                'phone' => '021-7771234',
                'address' => 'Jl. Industri No. 45, Kawasan Industri Pulogadung, Jakarta Timur, DKI Jakarta',
                'city' => 'Jakarta Timur',
                'postal_code' => '13260',
                'tax_id' => '11.111.111.1-111.000',
                'contact_person' => 'Budi Santoso',
                'payment_terms' => 30,
                'lead_time_days' => 3,
                'minimum_order_value' => 1000000,
                'rating' => 4.5,
                'is_active' => true,
                'notes' => 'Pemasok utama untuk gula cair dan gula pasir. Kualitas terjamin.',
            ],
            [
                'code' => 'SUP00002',
                'name' => 'PT. Herbal Nusantara',
                'email' => 'info@herbalnusantara.com',
                'phone' => '022-8882345',
                'address' => 'Jl. Raya Lembang No. 78, Bandung, Jawa Barat',
                'city' => 'Bandung',
                'postal_code' => '40391',
                'tax_id' => '22.222.222.2-222.000',
                'contact_person' => 'Siti Nurhaliza',
                'payment_terms' => 15,
                'lead_time_days' => 2,
                'minimum_order_value' => 500000,
                'rating' => 4.8,
                'is_active' => true,
                'notes' => 'Pemasok temulawak, jahe, dan rempah-rempah herbal berkualitas.',
            ],
            [
                'code' => 'SUP00003',
                'name' => 'CV. Kemasan Jaya',
                'email' => 'kemasan.jaya@gmail.com',
                'phone' => '031-5553456',
                'address' => 'Jl. Rungkut Industri No. 23, Surabaya, Jawa Timur',
                'city' => 'Surabaya',
                'postal_code' => '60293',
                'tax_id' => '33.333.333.3-333.000',
                'contact_person' => 'Ahmad Fauzi',
                'payment_terms' => 0,
                'lead_time_days' => 1,
                'minimum_order_value' => 250000,
                'rating' => 4.2,
                'is_active' => true,
                'notes' => 'Pemasok botol, plastik kemasan, dan label produk.',
            ],
            [
                'code' => 'SUP00004',
                'name' => 'UD. Tepung Sejahtera',
                'email' => 'sales@tepungsejahtera.co.id',
                'phone' => '021-8884567',
                'address' => 'Jl. Cikarang Barat No. 90, Bekasi, Jawa Barat',
                'city' => 'Bekasi',
                'postal_code' => '17530',
                'tax_id' => '44.444.444.4-444.000',
                'contact_person' => 'Dewi Lestari',
                'payment_terms' => 45,
                'lead_time_days' => 5,
                'minimum_order_value' => 2000000,
                'rating' => 4.6,
                'is_active' => true,
                'notes' => 'Pemasok tepung tapioka dan tepung terigu berkualitas.',
            ],
            [
                'code' => 'SUP00005',
                'name' => 'CV. Minyak Sejahtera',
                'email' => 'order@minyaksejahtera.com',
                'phone' => '024-7775678',
                'address' => 'Jl. Industri Kecil No. 12, Semarang, Jawa Tengah',
                'city' => 'Semarang',
                'postal_code' => '50198',
                'tax_id' => '55.555.555.5-555.000',
                'contact_person' => 'Joko Widodo',
                'payment_terms' => 21,
                'lead_time_days' => 3,
                'minimum_order_value' => 1500000,
                'rating' => 4.3,
                'is_active' => true,
                'notes' => 'Pemasok minyak goreng untuk produksi krupuk.',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('Suppliers seeded successfully!');
    }
}