<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Inventario;
use App\Models\Productos as ModelsProductos;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Productos extends BaseController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\ProductosModel';
    protected $format = 'json';

    public function index()
    {
        $model = new ModelsProductos();
        $productos = $model->findAll();

        return $this->respond($productos);
    }


    public function store()
    {

        $model = new ModelsProductos();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            $missingFields = [];


            if (isset($errors['nombre'])) {
                $missingFields[] = 'nombre';
            }
            if (isset($errors['descripcion'])) {
                $missingFields[] = 'descripcion';
            }
            if (isset($errors['precio'])) {
                $missingFields[] = 'precio';
            }

            if (!empty($missingFields)) {
                $errorMsg = 'Faltan los siguientes campos: ' . implode(', ', $missingFields);
            } else {
                $errorMsg = 'Datos incorrectos. Verifica los valores ingresados.';
            }

            return $this->fail($errorMsg, 400);
        }
        $data = [
            'nombre' => $this->request->getVar('nombre'),
            'descripcion' => $this->request->getVar('descripcion'),
            'precio' => $this->request->getVar('precio')
        ];

        $model->insert($data);

        return $this->respond(['message' => 'Producto creado exitosamente']);
    }


    public function update($id = null)
    {
        $model = new ModelsProductos();

        $data = [
            'nombre' => $this->request->getVar('nombre'),
            'descripcion' => $this->request->getVar('descripcion'),
            'precio' => $this->request->getVar('precio')
        ];

        $model->update($id, $data);

        return $this->respond(['message' => 'Producto actualizado exitosamente']);
    }

    public function delete($id = null)
    {
        $productoModel = new ModelsProductos();
        $inventarioModel = new Inventario();

        // Verificar si existe inventario asociado al producto
        $existingInventory = $inventarioModel->where('producto_id', $id)->first();

        if ($existingInventory) {
            return $this->failValidationErrors('No se puede eliminar el producto, existe inventario asociado.');
        }

        $productoModel->delete($id);

        return $this->respond(['message' => 'Producto eliminado exitosamente']);
    }

    public function getProducto($id = null)
    {

        $model = new ModelsProductos();

        $producto = $model->find($id);

        if (!$producto) {
            return $this->fail('Producto no encontrado', 404);
        }

        return $this->respond($producto);
    }



    protected function respond(array $data, int $statusCode = ResponseInterface::HTTP_OK)
    {
        return $this->response->setStatusCode($statusCode)->setJSON($data);
    }
}
