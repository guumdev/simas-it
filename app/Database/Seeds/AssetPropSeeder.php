<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AssetPropSeeder extends Seeder
{
    public function run()
    {
        $assetManagersData = [
            [
                'code' => 'PRGMR',
                'name' => 'Programmer',
                'description' => 'Programmer Asset Manager',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'HRDWR',
                'name' => 'Hardware',
                'description' => 'Hardware Technician Asset Manager',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'NTWRK',
                'name' => 'Network',
                'description' => 'Network Technician Asset Manager',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('asset_managers')->insertBatch($assetManagersData);

        $assetCategoriesData = [
            [
                'asset_managers_id' => 1,
                'code' => '001',
                'name' => 'Laptop',
                'description' => 'Laptop Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 1,
                'code' => '002',
                'name' => 'Desktop',
                'description' => 'Desktop Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 2,
                'code' => '003',
                'name' => 'Printer',
                'description' => 'Printer Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 3,
                'code' => '004',
                'name' => 'Router',
                'description' => 'Router Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 3,
                'code' => '005',
                'name' => 'CCTV',
                'description' => 'CCTV Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('asset_categories')->insertBatch($assetCategoriesData);

        $assetLocationsData = [
            [
                'code' => 'IT',
                'name' => 'Instalasi IT',
                'description' => 'Lokasi aset di ruang IT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'CNKR',
                'name' => 'Cengkir',
                'description' => 'Lokasi aset di ruang Cengkir',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'KKCN',
                'name' => 'Kidang Kencana',
                'description' => 'Lokasi aset di ruang Kidang Kencana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('asset_locations')->insertBatch($assetLocationsData);
    }
}
