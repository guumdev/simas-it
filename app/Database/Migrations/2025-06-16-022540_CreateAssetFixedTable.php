<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetFixedTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'asset_manager_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'asset_location_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'qr_code_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pcs',
            ],
            'condition' => [
                'type' => 'ENUM',
                'constraint' => ['baru', 'bekas', 'lama', 'rusak', 'hilang'],
                'default' => 'baru',
            ],
            'responsible_person' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'economic_life' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'acquisition_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('asset_manager_id', 'asset_managers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('asset_location_id', 'asset_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('qr_code_id', 'qr_codes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('asset_fixed');
    }

    public function down()
    {
        $this->forge->dropTable('asset_fixed', true);
    }
}
