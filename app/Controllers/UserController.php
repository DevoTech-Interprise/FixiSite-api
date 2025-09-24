<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class UserController extends ResourceController
{
    protected $format = 'json';
    protected $model;

    public function __construct()
    {
        // Garante que $this->model não seja null
        $this->model = new UserModel();
    }

    // Listar todos os usuários
    public function index()
    {
        $users = $this->model->findAll();
        return $this->respond($users);
    }

    // Mostrar usuário específico
    public function show($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }
        return $this->respond($user);
    }

    // Criar usuário
    public function create()
    {
        $data = request()->getJSON(true); // Array associativo
        if (!$data) {
            return $this->failValidationErrors('No data provided');
        }

        try {
            $userId = $this->model->insert($data);
            return $this->respondCreated(['id' => $userId]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Atualizar usuário
    public function update($id = null)
    {
        $data = request()->getJSON(true);
        if (!$data) {
            return $this->failValidationErrors('No data provided');
        }

        try {
            $this->model->update($id, $data);
            return $this->respond(['status' => 'updated']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Deletar usuário
    public function delete($id = null)
    {
        try {
            $this->model->delete($id);
            return $this->respondDeleted(['status' => 'deleted']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Login (retorna JWT)
    public function login()
    {
        $data = request()->getJSON(true);

        if (!isset($data['email'], $data['password'])) {
            return $this->failValidationErrors('Email and password required');
        }

        $user = $this->model->where('email', $data['email'])->first();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->fail('Invalid credentials', 401);
        }

        $key = getenv('jwt.secret');
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // 1 hora de validade
            'uid' => $user['id'],
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return $this->respond([
            'token' => $jwt,
            'name' => $user['name'],
            'email' => $user['email'],
            'tenant_id' => $user['tenant_id']
        ]);
    }
}
