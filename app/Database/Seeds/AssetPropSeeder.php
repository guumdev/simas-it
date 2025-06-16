<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AssetPropSeeder extends Seeder
{
    public function run()
    {
        $assetManagersData = [
            [
                'code' => 'AMG001',
                'name' => 'Programmer',
                'description' => 'Programmer Asset Manager',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'AMG002',
                'name' => 'Hardware Technician',
                'description' => 'Hardware Technician Asset Manager',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'AMG003',
                'name' => 'Network Technician',
                'description' => 'Network Technician Asset Manager',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('asset_managers')->insertBatch($assetManagersData);

        $assetCategoriesData = [
            [
                'asset_managers_id' => 1,
                'code' => 'ACT001',
                'name' => 'Laptop',
                'description' => 'Laptop Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 1,
                'code' => 'ACT002',
                'name' => 'Desktop',
                'description' => 'Desktop Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 2,
                'code' => 'ACT003',
                'name' => 'Printer',
                'description' => 'Printer Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'asset_managers_id' => 3,
                'code' => 'ACT004',
                'name' => 'Router',
                'description' => 'Router Category',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('asset_categories')->insertBatch($assetCategoriesData);

        $assetLocationsData = [
            [
                'code' => 'AL001',
                'name' => 'Manalagi',
                'description' => 'Lokasi aset di ruang Manalagi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'AL002',
                'name' => 'Cengkir',
                'description' => 'Lokasi aset di ruang Cengkir',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'AL003',
                'name' => 'Kidang Kencana',
                'description' => 'Lokasi aset di ruang Kidang Kencana',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('asset_locations')->insertBatch($assetLocationsData);
    }
}
