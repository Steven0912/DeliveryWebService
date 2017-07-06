<?php

/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 6/07/2017
 * Time: 9:19 AM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../DatabaseConnection/Database.php';

class AssignedOrderDelivery
{
    function __construct()
    {
    }

    public static function getAssignedOrderDeliveryById($id)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT id_pedido FROM pedidos_asignados_domiciliario
                             WHERE id_usuario_domiciliario = ?";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute(array($id));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

}