<?php 

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;

class ProductController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    function __construct()
    {
        $this->model = new ProductModel();
    }

    public function index()
    {
        $products = $this->model->findAll();
        return $this->respond($products);
    }

    public function show($id = null)
    {
        $product = $this->model->find($id);
        if (!$product)
        {
            return $this->failNotFound('Product not found');
        }

        return $this->respond($product);
    }

    public function create()
    {
        $data = request()->getJSON();

        if (!$data) {
            return $this->failValidationErrors('No data provided');
        }

        try {

            $productId = $this->model->insert($data);
            return $this->respondCreated(['id' => $productId, 'message' => 'created']);

        } catch (\Exception $e)
        {
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        $data = request()->getJSON(true);

        if (!$data)
        {
            return $this->failValidationErrors('No data provided');
        }

        try {
            $this->model->update($id, $data);
            return $this->respond(['status' => 'updated']);

        } catch (\Exception $e)
        {
            return $this->failServerError($e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $data = request()->getJSON(true);
        if (!$data)
        {
            return $this->failValidationErrors('No data provided');
        }

        try {
            $this->model->delete($id);
            return $this->respond(['status' => 'deleted']);

        }
        catch (\Exception $e)
        {
            return $this->failServerError($e->getMessage());
        }
    }
}

?>