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

    public function __construct()
    {
        $this->modelPlan = new PlansModel();
        $this->modelBenefit = new BenefitModel();
    }

    public function index()
    {
        $plans = $this->modelPlan->findAll();

        foreach ($plans as $key => $plan) {
            $benefits = $this->modelBenefit->where('plans_id', $plan['id'])->findAll();
            $plans[$key]['benefits'] = $benefits ?? [];
        }

        return $this->respond($plans);
    }

    public function show($id = null)
    {
        $plan = $this->modelPlan->find($id);

        if (!$plan) {
            return $this->failNotFound('data not found');
        }

        $benefits = $this->modelBenefit->where('plans_id', $plan['id'])->findAll();
        $plan['benefits'] = $benefits;

        return $this->respond($plan);
    }

    public function create()
    {
        $data = request()->getJSON(true);

        if (!$data) {
            return $this->failValidationErrors('No data provided');
        }

        try {
            //  CRIAR O PLANO //
            $planId = $this->modelPlan->insert([
                'name' => $data['name'],
                'title' => $data['title'],
                'price' => $data['price'],
                'product_id' => $data['product_id']
            ]);

            // VINCULAR OS BENEFICIOS //
            foreach ($data['benefits'] as $benefit) {
                $this->modelBenefit->insert([
                    'plans_id' => $planId,
                    'description' => $benefit
                ]);
            }

            return $this->respondCreated([
                'id' => $planId,
                'message' => 'Plan created, benefits vinculaded'
            ]);

        } catch (\Exception $e) {

            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        $data = request()->getJSON(true);

        if (!$data) {
            return $this->failValidationErrors('data no provided');
        }

        try {

            $plan = $this->modelPlan->where('id', $id)->find();
            if (!$plan)
            {
                return $this->failNotFound('data not found');
            }

            $this->modelPlan->update($id, $data);
            return $this->respond(['status' => 'updated']);

        } catch (\Exception $e) {

            return $this->failServerError($e->getMessage());
        }
    }

    public function delete($id = null) {

        try 
        {

            $plan = $this->modelPlan->where('id', $id)->find();

            if (!$plan)
            {
                return $this->failValidationErrors('data not found');
            }

            $this->modelPlan->delete($id);
            return $this->respondDeleted(['status'=>'deleted']);

        } catch (\Exception $e)
        {
            return $this->failServerError($e->getMessage());
        }

    }
}
