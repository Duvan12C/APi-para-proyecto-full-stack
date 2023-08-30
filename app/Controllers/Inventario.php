<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Inventario as ModelsInventario;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Inventario extends BaseController
{
    use ResponseTrait;


    protected $format = 'json';
    protected $modelName = 'App\Models\InventarioModel';

    public function index()
    {
        $model = new ModelsInventario();
        $inventario = $model->findAll();

        return $this->respond($inventario);
    }


    public function store()
    {



        $model = new ModelsInventario();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'producto_id' => 'required|numeric',
            'cantidad_disponible' => 'required|numeric',
            'cantidad_minima' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();

            $errorMsg = '';
            if (isset($errors['producto_id']) && $this->request->getVar('producto_id') === '') {
                $errorMsg .= 'El ID del producto es requerido. ';
            }
            if (isset($errors['cantidad_disponible']) && $this->request->getVar('cantidad_disponible') === '') {
                $errorMsg .= 'La cantidad disponible es requerida. ';
            }
            if (isset($errors['cantidad_minima']) && $this->request->getVar('cantidad_minima') === '') {
                $errorMsg .= 'La cantidad mínima es requerida. ';
            }

            if ($this->request->getVar('producto_id') !== '' && !is_numeric($this->request->getVar('producto_id'))) {
                $errorMsg .= 'El ID del producto debe ser numérico. ';
            }
            if ($this->request->getVar('cantidad_disponible') !== '' && !is_numeric($this->request->getVar('cantidad_disponible'))) {
                $errorMsg .= 'La cantidad disponible debe ser numérica. ';
            }
            if ($this->request->getVar('cantidad_minima') !== '' && !is_numeric($this->request->getVar('cantidad_minima'))) {
                $errorMsg .= 'La cantidad mínima debe ser numérica. ';
            }

            return $this->failValidationErrors($errorMsg);
        }
        $producto_id = $this->request->getVar('producto_id');
        $existingInventory = $model->where('producto_id', $producto_id)->first();

        if ($existingInventory) {
            return $this->fail('Ya existe un inventario para el producto');
        }

        $insertData = [
            'producto_id' => $producto_id,
            'cantidad_disponible' => $this->request->getVar('cantidad_disponible'),
            'cantidad_minima' => $this->request->getVar('cantidad_minima')
        ];

        $model->insert($insertData);

        return $this->respondCreated(['message' => 'Inventario creado exitosamente']);
    }


    public function update($id = null)
    {

        $model = new ModelsInventario();

        $data = [
            'cantidad_disponible' => $this->request->getVar('cantidad_disponible'),
            'cantidad_minima' => $this->request->getVar('cantidad_minima')
        ];

        $model->where("id", $id)->update($id, $data);

        return $this->respond([
            'message' => 'Inventario actualizado exitosamente'
        ]);
    }


    public function getInventario($id = null)
    {

        $model = new ModelsInventario();

        $producto = $model->find($id);

        if (!$producto) {
            return $this->fail('No hay inventario de este producto', 404);
        }

        return $this->respond($producto);
    }


    public function delete($id = null)
    {
        $model = new ModelsInventario();
        $model->delete($id);

        return $this->respondDeleted(['message' => 'Inventario eliminado exitosamente']);
    }
}
