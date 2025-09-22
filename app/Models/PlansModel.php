<?php 

namespace App\Models;

use CodeIgniter\Model;

class PlansModel extends Model
{
    protected $table = 'plans';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'title', 'price', 'product_id'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
}

?>