<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetMaintenancesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'asset_fixed_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'maintenance_type' => [
                'type'       => 'ENUM',
                'constraint' => ['pencegahan', 'perbaikan', 'darurat', 'rutin'],
                'null'       => false,
            ],
            'maintenance_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'next_maintenance' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'performed_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'cost' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => false,
            ],
            'duration' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'dalam hari',
            ],
            'maintenance_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'deskripsi kerusakan',
            ],
            'maintenance_action' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'tindakan pemeliharaan',
            ],
            'device_status' => [
                'type'       => 'ENUM',
                'constraint' => ['normal', 'rusak_ringan', 'rusak_berat', 'tidak_berfungsi', 'dalam_perbaikan'],
                'null'       => false,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('asset_fixed_id', 'asset_fixeds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('asset_maintenances', true);
    }

    public function down()
    {
        $this->forge->dropTable('asset_maintenances', true);
    }
}
