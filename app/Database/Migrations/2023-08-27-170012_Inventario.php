<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Inventario extends Migration
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
            'cantidad_disponible' => [
                'type' => 'INT',
            ],
            'cantidad_minima' => [
                'type' => 'INT',
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('producto_id', 'productos', 'id');
        $this->forge->createTable('inventario');
    }

    public function down()
    {
        $this->forge->dropTable('inventario');
    }
}
