<?php 

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PlansModel;
use App\Models\BenefitModel;

class PlansController extends ResourceController 
{
    protected $format = 'json';
    protected $modelPlan;
    protected $modelBenefit;

    public function __construct() {
        $this->modelPlan = new PlansModel();
        $this->modelBenefit = new BenefitModel();
    }

    public function index()
    {
        
    }

    public function show($id = null)
    {
        
    }

    public function create()
    {
        $data = request()->getJSON(true);

        if (!$data)
        {
            return $this->failValidationErrors('No data provided');
        }
    }

    public function update($id = null)
    {
        
    }

    public function delete($id = null)
    {
        
    }
}

?>