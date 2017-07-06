<?php

/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 6/07/2017
 * Time: 11:39 AM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../DatabaseConnection/Database.php';

class LocationClient
{
    function __construct()
    {
    }

    public static function getLocationClientById($id)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT * FROM ubicaciones_cliente
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
}