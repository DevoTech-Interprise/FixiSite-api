<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TenantModel;

class TenantController extends ResourceController
{
    protected $modelName = TenantModel::class;
    protected $format = 'json';

    public function index()
    {
        try {

            $tenants = $this->model->findAll();
            return $this->respond($tenants);
        } catch (\Exception $e) {
            $this->failServerError($e->getMessage());
        }
    }

    public function show($id = null)
    {
        try {

            $tenant = $this->model->find($id);

            if (!$tenant) {
                return $this->failNotFound('data not found');
            }

            return $this->respond($tenant);
        } catch (\Exception $e) {
            $this->failServerError($e->getMessage());
        }
    }

    public function create()
    {
        $data = request()->getPost();

        if (!$data) {
            return $this->failValidationErrors('data not provied');
        }

        $rules = [
            'name'   => 'permit_empty|string|min_length[3]',
            'domain' => 'permit_empty|string|is_unique[tenants.domain]',
            'email'  => 'permit_empty|valid_email',
            'logo'   => 'permit_empty|uploaded[logo]|max_size[logo,2048]|is_image[logo]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        try {

            $logoFile = request()->getFile('logo');
            $logoPath = null;

            if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
                // Define a pasta (pode ser writable/uploads/tenants)
                $newName = $logoFile->getRandomName();
                $logoFile->move(FCPATH . 'uploads/tenants', $newName);

                // Caminho relativo que será salvo no banco
                $logoPath = '/uploads/tenants/' . $newName;
            }

            $bodyTenant = [
                'name' => $data['name'],
                'domain' => $data['domain'],
                'logo'   => $logoPath,
                'theme'  => isset($data['theme'])
                    ? (is_array($data['theme']) ? json_encode($data['theme']) : $data['theme'])
                    : json_encode(['primary' => '#000000', 'secondary' => '#FFFFFF']),
                'cpf_cnpj' => $data['cpf_cnpj']
            ];

            $tenantId = $this->model->insert($bodyTenant);

            if (!$tenantId) {
                return $this->fail('Erro ao criar tenant');
            }

            return $this->respondCreated(['id' => $tenantId, 'status' => 'created']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        // Pega o ID do form-data
        $id = request()->getPost('id');
        if (!$id) {
            return $this->failValidationErrors('ID do tenant é obrigatório');
        }

        $tenant = $this->model->find($id);
        if (!$tenant) {
            return $this->failNotFound('Tenant não encontrado');
        }

        // Regras de validação
        $rules = [
            'name'      => 'permit_empty|string|min_length[3]',
            'domain'    => "permit_empty|string|is_unique[tenants.domain,id,{$id}]",
            'cpf_cnpj'  => 'permit_empty|string',
            'theme'     => 'permit_empty',
            'logo'      => 'permit_empty|uploaded[logo]|max_size[logo,2048]|is_image[logo]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $updateData = [];

        // Campos de texto
        $name      = request()->getPost('name');
        $domain    = request()->getPost('domain');
        $cpf_cnpj  = request()->getPost('cpf_cnpj');
        $theme     = request()->getPost('theme');

        if ($name)     $updateData['name'] = $name;
        if ($domain)   $updateData['domain'] = $domain;
        if ($cpf_cnpj) $updateData['cpf_cnpj'] = $cpf_cnpj;
        if ($theme) {
            $updateData['theme'] = is_array($theme) ? json_encode($theme) : $theme;
        }

        // Upload de logo
        $file = request()->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/tenants/', $newName);

            // Remove logo antiga
            if (!empty($tenant['logo']) && file_exists(FCPATH . $tenant['logo'])) {
                unlink(FCPATH . $tenant['logo']);
            }

            $updateData['logo'] = 'uploads/tenants/' . $newName;
        } else {
            // Mantém logo antiga se não enviar arquivo
            $updateData['logo'] = $tenant['logo'];
        }

        // Atualiza no banco
        $this->model->update($id, $updateData);

        // Retorna tenant atualizado
        return $this->respondUpdated([
            'message' => 'Tenant atualizado com sucesso',
            'tenant'  => $this->model->find($id)
        ]);
    }



    public function delete($id = null)
    {
        try {

            $this->model->delete($id);
            return $this->respondDeleted(['status' => 'deleted']);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
