<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BenefitModel;

class BenefitController extends ResourceController
{
    protected $modelName = BenefitModel::class;
    protected $format = 'json';

    public function index()
    {
        try {

            $benefits = $this->model->findAll();
            if (!$benefits) {
                return $this->failValidationErrors('no benefits');
            }

            return $this->respond($benefits);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function show($id = null)
    {
        try {
            $benefit = $this->model->where('id', $id)->find();

            if (!$benefit) {
                return $this->failNotFound('Benefit not found');
            }

            $benefit = $this->model->where('id', $id)->find();

            return $this->respond($benefit);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function create()
    {
        $data = request()->getJSON();

        if (!$data) {
            return $this->failValidationErrors('data no provied');
        }

        try {

            $benefitId = $this->model->insert($data);

            if (!$benefitId) {
                return $this->failValidationErrors('benefit no created');
            }

            return $this->respondCreated(['id' => $benefitId, 'status' => 'created']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        $data = request()->getJSON();

        if (!$data) {
            return $this->failValidationErrors('data no provied');
        }

        try {

            $benefitId = $this->model->update($id, $data);

            if (!$benefitId) {
                return $this->failValidationErrors('benefit no created');
            }

            return $this->respondCreated(['id' => $id, 'status' => 'created']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function delete($id = null)
    {
        try {

            $benefit = $this->model->where('id', $id)->find();

            if (!$benefit) {
                return $this->failNotFound('Benefit not found');
            }

            $this->model->delete($id);

            return $this->respondDeleted(['id' => $id, 'status' => 'deleted']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
