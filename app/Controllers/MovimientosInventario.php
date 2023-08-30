<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Inventario;
use App\Models\MovimientosInventario as ModelsMovimientosInventario;
use App\Models\Productos;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class MovimientosInventario extends BaseController
{

    use ResponseTrait;
    protected $format = 'json';
    protected $modelName = 'App\Models\ModelsMovimientosInventario';

    public function index()
    {
        $productosModel = new ModelsMovimientosInventario();
        $productos = $productosModel->findAll();


        return $this->respond($productos);
    }


    public function store()
    {

        $model = new ModelsMovimientosInventario();
        $productosModel = new Productos();
        $inventarioModel = new Inventario();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'producto_id' => 'required|numeric',
            'cantidad' => 'required|numeric',
            'fecha' => 'required',
            'descripcion' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();

            $errorMsg = '';
            if (isset($errors['producto_id']) && $this->request->getVar('producto_id') === '') {
                $errorMsg .= 'El ID del producto es requerido. ';
            }
            if (isset($errors['cantidad']) && $this->request->getVar('cantidad') === '') {
                $errorMsg .= 'La cantidad disponible es requerida. ';
            }
            if (!strtotime($this->request->getVar('fecha'))) {
                $errorMsg .= 'La fecha no es válida. ';
            }

            if (isset($errors['descripcion']) && $this->request->getVar('descripcion') === '') {
                $errorMsg .= 'La descripcion  es requerida. ';
            }

            if ($this->request->getVar('producto_id') !== '' && !is_numeric($this->request->getVar('producto_id'))) {
                $errorMsg .= 'El ID del producto debe ser numérico. ';
            }
            if ($this->request->getVar('cantidad') !== '' && !is_numeric($this->request->getVar('cantidad'))) {
                $errorMsg .= 'La cantidad disponible debe ser numérica. ';
            }
            if ($this->request->getVar('fecha') !== '' && !is_numeric($this->request->getVar('fecha'))) {
                $errorMsg .= 'La fecha  debe ser fecha. ';
            }


            return $this->failValidationErrors($errorMsg);
        }

        $descripcion = $this->request->getVar('descripcion');
        $fecha = $this->request->getVar('fecha');
        $productoId = $this->request->getVar('producto_id');
        $cantidadSolicitada = $this->request->getVar('cantidad');


        $inventario = $inventarioModel->where('producto_id', $productoId)->first();

        if ($inventario == null) {
            return $this->respond(['message' => 'El producto no existe.']);
        }

        if ($cantidadSolicitada > $inventario['cantidad_disponible']) {
            return $this->respond(['message' => 'La cantidad solicitada es mayor que la cantidad disponible en inventario.']);
        }


        $nuevaCantidadDisponible = $inventario['cantidad_disponible'] - $cantidadSolicitada;
        $inventarioModel->update($inventario['id'], ['cantidad_disponible' => $nuevaCantidadDisponible]);

        $model->save([
            'producto_id' => $productoId,
            'tipo' => 'salida',
            'cantidad' => $cantidadSolicitada,
            'descripcion' => $descripcion,
            'fecha' => $fecha
        ]);

        return $this->respond(['message' => 'Movimiento creado exitosamente']);
    }

    public function update($id)
    {
        $model = new ModelsMovimientosInventario();

        // Obtén y valida los datos
        $descripcion = $this->request->getVar('descripcion');
        $fecha = $this->request->getVar('fecha');

        $model->update($id, [
            'descripcion' => $descripcion,
            'fecha' => $fecha
        ]);

        return $this->respond(['message' => 'Movimiento actualizado exitosamente']);
    }

    public function delete($id)
    {
        $model = new ModelsMovimientosInventario();
        $model->delete($id);
        return $this->respond(['message' => 'Movimiento eliminado exitosamente']);
    }


    public function getMovimiento($id = null)
    {

        $model = new ModelsMovimientosInventario();

        $producto = $model->find($id);

        if (!$producto) {
            return $this->fail('No hay movimientos', 404);
        }

        return $this->respond($producto);
    }


    protected function respond(array $data, int $statusCode = ResponseInterface::HTTP_OK)
    {
        return $this->response->setStatusCode($statusCode)->setJSON($data);
    }
}
