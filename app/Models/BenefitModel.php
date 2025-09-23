<?php 

namespace App\Models;

use CodeIgniter\Model;

class BenefitModel extends Model
{
    protected $table = 'benefits';
    protected $primaryKey = 'id';
    protected $allowedFields = ['plans_id', 'description'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

?>