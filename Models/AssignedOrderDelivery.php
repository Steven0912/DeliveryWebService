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
            $row = $command->fetchAll(PDO::FETCH_ASSOC);
            return $row;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

    public static function getAcceptDateByIdOrder($id)
    {
        $query = "SELECT fecha_aceptacion FROM pedidos_asignados_domiciliario
                             WHERE id_pedido = ?";

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

    public static function getListAssignedOrderDeliveryByIds($idPedido, $idDomiciliario)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT id_estado FROM pedidos_asignados_domiciliario
                             WHERE id_pedido = ? and id_usuario_domiciliario = ?";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute(array($idPedido, $idDomiciliario));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

    public static function updateOrderStateAccept($idPedido, $idDomiciliario, $fecha)
    {
        // Creando query UPDATE
        $query = "UPDATE pedidos_asignados_domiciliario" .
            " SET fecha_aceptacion=?" .
            "WHERE id_pedido=? and id_usuario_domiciliario=?";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute(array($fecha, $idPedido, $idDomiciliario));

            return 1;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

    public static function updateOrderStateDecline($idPedido, $idDomiciliario, $idEstado, $motivos, $fecha)
    {
        // Creando query UPDATE
        $query = "UPDATE pedidos_asignados_domiciliario" .
            " SET id_estado = ?, motivo_cancelacion = ?, fecha_cancelacion = ?" .
            "WHERE id_pedido=? and id_usuario_domiciliario=?";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute(array($idEstado, $motivos, $fecha, $idPedido, $idDomiciliario));

            return 1;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

}