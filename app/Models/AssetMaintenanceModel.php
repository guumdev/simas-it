<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetMaintenanceModel extends Model
{
    protected $table            = 'asset_maintenance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['asset_fixed_id', 'maintenance_type', 'maintenance_date', 'next_maintenance', 'performed_by', 'cost', 'duration', 'maintenance_location', 'description', 'maintenance_action', 'device_status', 'notes'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'asset_fixed_id'        => 'required|is_not_unique[asset_fixed.id]',
        'maintenance_type'      => 'required|in_list[pencegahan,perbaikan,darurat,rutin]',
        'maintenance_date'      => 'required|valid_date[Y-m-d]',
        'description'           => 'permit_empty|string',
        'maintenance_action'    => 'permit_empty|string',
        'maintenance_location'  => 'required|string|max_length[255]',
        'device_status'         => 'required|in_list[normal,rusak_ringan,rusak_berat,tidak_berfungsi,dalam_perbaikan]',
        'performed_by'          => 'required|string|max_length[100]',
        'cost'                  => 'permit_empty|decimal',
        'duration'              => 'permit_empty|numeric',
        'notes'                 => 'permit_empty|string',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
