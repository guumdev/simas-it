<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetMovementTable extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'asset_fixed_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'from_location_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'to_location_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'moved_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'movement_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['transfer', 'maintenance', 'return'],
                    'null'       => false,
                ],
                'movement_date' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
                'condition_before' => [
                    'type'       => 'ENUM',
                    'constraint' => ['normal', 'rusak_ringan', 'rusak_berat', 'tidak_berfungsi'],
                    'null'       => true,
                ],
                'condition_after' => [
                    'type'       => 'ENUM',
                    'constraint' => ['normal', 'rusak_ringan', 'rusak_berat', 'tidak_berfungsi'],
                    'null'       => true,
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
            ],
        );

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('asset_fixed_id', 'asset_fixed', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('from_location_id', 'asset_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('to_location_id', 'asset_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('asset_movement', true);
    }

    public function down()
    {
        $this->forge->dropTable('asset_movement', true);
    }
}
