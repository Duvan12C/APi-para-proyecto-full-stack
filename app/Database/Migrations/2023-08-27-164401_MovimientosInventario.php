<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MovimientosInventario extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'producto_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'tipo' => [
                'type' => 'ENUM("Entrada", "Salida")',
            ],
            'cantidad' => [
                'type' => 'INT',
            ],
            'fecha' => [
                'type' => 'DATETIME',
            ],
            'descripcion' => [
                'type' => 'TEXT',
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('producto_id', 'productos', 'id');
        $this->forge->createTable('movimientos_inventario');
    }

    public function down()
    {
        $this->forge->dropTable('movimientos_inventario');
    }
}
