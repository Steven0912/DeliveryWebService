<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../DatabaseConnection/Database.php';

// esto es un comentario

class User
{
    function __construct()
    {
    }

    public static function getUsers()
    {
        $query = "SELECT * FROM usuarios";
        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar sentencia preparada
            $command->execute();

            return $command->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getUser($id)
    {
        // Consulta de un usuario en especifico
        $query = "SELECT * FROM usuarios
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

    public static function getLastUser()
    {
        $query = "SELECT * FROM usuarios ORDER BY id DESC LIMIT 1";

        try {
            // Preparar sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Capturar primera fila del resultado
            $command->execute();
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;

        } catch (PDOException $e) {
            // Aquí puedes clasificar el error dependiendo de la excepción
            // para presentarlo en la respuesta Json
            return -1;
        }
    }

    public static function validateEmail($email)
    {
        $query = "SELECT * FROM usuarios WHERE correo = ?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar la sentencia preparada
            $command->execute(array($email));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function validatePhone($phone)
    {
        $query = "SELECT * FROM usuarios WHERE telefono = ?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar la sentencia preparada
            $command->execute(array($phone));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function getUserToken($id)
    {
        // Consulta de el token de un usuario en especifico
        $query = "SELECT token FROM usuarios WHERE id = ?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            // Ejecutar la sentencia preparada
            $command->execute(array($id));
            // Capturar primera fila del resultado
            $row = $command->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function create(
        $id_doc = 1,
        $id_termino = 1,
        $id_estado = 1,
        $id_roles = 1,
        $nombre_completo,
        $nombre,
        $apellido,
        $genero,
        $telefono,
        $correo,
        $password,
        $numero_doc,
        $foto_perfil = null,
        $direccion = null,
        $latitud = null,
        $longitud = null,
        $imagen_pin = null,
        $pin = null,
        $token
    )
    {
        // Sentencia INSERT
        $query = "INSERT INTO usuarios ( " .
            " id_doc," .
            " id_termino," .
            " id_estado," .
            " id_roles," .
            " nombre_completo," .
            " nombre," .
            " apellido," .
            " genero," .
            " telefono," .
            " correo," .
            " password," .
            " numero_doc," .
            " foto_perfil," .
            " direccion," .
            " latitud," .
            " longitud," .
            " imagen_pin," .
            " pin," .
            " token)" .
            " VALUES( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);
            $command->execute(
                array(
                    $id_doc,
                    $id_termino,
                    $id_estado,
                    $id_roles,
                    $nombre_completo,
                    $nombre,
                    $apellido,
                    $genero,
                    $telefono,
                    $correo,
                    $password,
                    $numero_doc,
                    $foto_perfil,
                    $direccion,
                    $latitud,
                    $longitud,
                    $imagen_pin,
                    $pin,
                    $token
                )
            );
            return $command;

        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function update(
        $identification,
        $firstName,
        $lastName,
        $name,
        $nickName,
        $email,
        $mobile,
        $password,
        $gender,
        $state,
        $rol,
        $id
    )
    {
        // Creando query UPDATE
        $query = "UPDATE usuarios" .
            " SET identification=?, firstName=?, lastName=?, name=?, nickName=?, email=?, mobile=?, password=?, gender=?, States_id=?, Roles_id=? " .
            "WHERE id=?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);

            // Relacionar y ejecutar la sentencia
            $command->execute(array($identification, $firstName, $lastName, $name, $nickName, $email, $mobile, $password, $gender, $state, $rol, $id));

            return $command;

        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function setUserToken($id, $token)
    {
        // Creando query UPDATE
        $query = "UPDATE usuarios" .
            " SET token=? " .
            "WHERE id=?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);

            // Relacionar y ejecutar la sentencia
            $command->execute(array($token, $id));

            return $command;

        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function delete($id)
    {
        // Sentencia DELETE
        $query = "DELETE FROM usuarios WHERE id=?";

        try {
            // Preparar la sentencia
            $command = Database::getInstance()->getDb()->prepare($query);

            $command->execute(array($id));

            return $command;

        } catch (PDOException $e) {
            return -1;
        }
    }

    public static function checkLogin($correo, $password)
    {
        // Consulta de la meta
        $query = "SELECT * FROM usuarios
                             WHERE correo = ? and password = ?";

        try {
            $command = Database::getInstance()->getDb()->prepare($query);

            $command->execute(array($correo, $password));

            $row = $command->fetch(PDO::FETCH_ASSOC);

            return $row;

        } catch (PDOException $e) {
            return -1;
        }
    }
}

?>