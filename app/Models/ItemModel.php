<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table            = 'items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['asset_managers_id', 'asset_categories_id', 'name', 'brand', 'model', 'serial_number', 'vendor', 'image', 'description', 'acquisition_date'];

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
    protected $validationRules      = [];
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

    public function itemCounter()
    {
        return [
            'allItems' => $this->countAllResults(),
            'programmerItems' => $this->where('asset_managers_id', 1)
                ->where('deleted_at', null)
                ->countAllResults(),
            'hardwareItems' => $this->where('asset_managers_id', 2)
                ->where('deleted_at', null)
                ->countAllResults(),
            'networkItems' => $this->where('asset_managers_id', 3)
                ->where('deleted_at', null)
                ->countAllResults(),
        ];
    }
}
