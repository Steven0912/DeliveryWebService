<?php

/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 21/07/2017
 * Time: 4:00 PM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../DatabaseConnection/Database.php';

class Ticket
{
    function __construct()
    {
    }

    public static function validateIfStartTicketChat($id_pedido, $id_usuario_origen, $id_usuario_destino)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT * FROM ticket
                             WHERE id_pedido = ? and id_usuario_o = ? and id_usuario_d = ?";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute(array($id_pedido, $id_usuario_origen, $id_usuario_destino));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return false;
        }
    }

    public static function createTicket(
        $id_pedido,
        $id_usuario_origen,
        $id_usuario_destino,
        $fecha_inicio,
        $estado = 1
    )
    {
        // Sentencia INSERT
        $query = "INSERT INTO ticket ( " .
            " fecha_i," .
            " id_usuario_o," .
            " id_usuario_d," .
            " id_pedido," .
            " estado)" .
            " VALUES( ?,?,?,?,? )";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            $command->execute(
                array(
                    $fecha_inicio,
                    $id_usuario_origen,
                    $id_usuario_destino,
                    $id_pedido,
                    $estado
                )
            );

            return true;

        } catch (PDOException $e) {
            return false;
        }
    }

    public static function createBitacoraTicket(
        $id_ticket,
        $fecha,
        $id_usuario_origen,
        $mensaje,
        $estado = 1
    )
    {
        // Sentencia INSERT
        $query = "INSERT INTO bitacora_ticket ( " .
            " id_ticket," .
            " fecha," .
            " id_usuario," .
            " msg," .
            " estado)" .
            " VALUES( ?,?,?,?,? )";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            $command->execute(
                array(
                    $id_ticket,
                    $fecha,
                    $id_usuario_origen,
                    $mensaje,
                    $estado
                )
            );

            return true;

        } catch (PDOException $e) {
            return false;
        }
    }
}