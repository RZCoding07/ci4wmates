<?php
namespace App\Models;
use CodeIgniter\Model;

class MasterdataModel extends Model {
    
	protected $table = 'master_data';
	protected $primaryKey = 'id';
	protected $returnType = 'object';
	protected $useSoftDeletes = false;
	protected $allowedFields = ['tahun', 'quarter', 'penjualan', 'jenis'];
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';
	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = true;    
	
}