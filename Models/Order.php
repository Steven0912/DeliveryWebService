<?php

/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 6/07/2017
 * Time: 11:36 AM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../DatabaseConnection/Database.php';

class Order
{
    function __construct()
    {
    }

    public static function getOrderById($id)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT * FROM pedido
                             WHERE id = ?";

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

    public static function validateIfOrderWasDispatched($id_pedido, $id_estado)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT * FROM detalle_pedido
                             WHERE id_pedido = ? and id_estado = ?";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute(array($id_pedido, $id_estado));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

    public static function getStateByIdOrder($id)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT id_estado FROM detalle_pedido
                             WHERE id_pedido = ? 
                             order by id_estado desc";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $limit = 1;
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

    public static function updateStateOrder($id_estado, $id_pedido)
    {
        // Creando query UPDATE
        $query = "UPDATE pedido" .
            " SET id_estado=? " .
            "WHERE id=?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);

            // Relacionar y ejecutar la sentencia
            $command->execute(array($id_estado, $id_pedido));

            return $command;

        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function create(
        $id_pedido,
        $id_estado,
        $fecha,
        $hora
    )
    {

        // Sentencia INSERT
        $query = "INSERT INTO detalle_pedido ( " .
            " id_pedido," .
            " id_estado," .
            " fecha," .
            " hora)" .
            " VALUES( ?,?,?,? )";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            $command->execute(
                array(
                    $id_pedido,
                    $id_estado,
                    $fecha,
                    $hora
                )
            );

            return 1;

        } catch (PDOException $e) {
            return -1;
        }

    }
}