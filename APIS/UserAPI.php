<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/User.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../APIS/Security.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Utils/Exceptions.php';
error_reporting(0);

class UserAPI
{
    public function API()
    {
        header('Content-Type: application/JSON');
        $method = $_SERVER['REQUEST_METHOD'];

        $obj = new Security();
        if ($obj->autorizar() == 10) {

            switch ($method) {
                case 'GET':
                    if ($_GET['action'] == 'users') {
                        $this->getUsers();
                    }
                    break;
                case 'POST':
                    $this->createUser();
                    break;

                default:
                    new Exceptions(405);
                    break;
            }
        }
    }

    private
    function getUsers()
    {
        if ($_GET['action'] == 'users') {

            if (isset($_GET['id'])) {//muestra 1 solo registro si es que existiera ID
                $response = User::getUser($_GET['id']);
                echo json_encode($response, JSON_PRETTY_PRINT);
            } else { //muestra todos los registros
                $response = User::getUsers();
                if ($response) {
                    echo json_encode($response, JSON_PRETTY_PRINT);
                } else {
                    echo json_encode(array(
                        'state' => '2',
                        'message' => 'No hay usuarios en la bd'
                    ), JSON_PRETTY_PRINT);
                }
            }
        } else {
            new Exceptions(400);
            //$this->response(400);
        }
    }

    private
    function createUser()
    {
        if ($_GET['action'] == 'checkLogin') {
            //Decodifica un string de JSON
            $obj = json_decode(file_get_contents('php://input'), true);

            if (empty((array)$obj)) {
                new Exceptions(422, "error", "Nada para añadir, revisa los datos");
                //$this->response(422, "error", "Nada para añadir, revisa los datos");
            } else if (
                isset($obj['mail']) &&
                isset($obj['password']) &&
                isset($obj['token'])
            ) {
                $response = User::validateEmail($obj['mail']);
                if ($response) {
                    // Correo correcto
                    $response = User::checkLogin($obj['mail'], md5($obj['password']));
                    if ($response) {
                        User::setUserToken($response["id"], $obj['token']);
                        $response = User::getUser($response["id"]);

                        $user["state"] = 1;


                        $user["user"] = $response;
                        echo json_encode($user, JSON_PRETTY_PRINT);
                    } else {
                        echo json_encode(array(
                            'state' => '2',
                            'message' => 'Contraseña incorrecta'
                        ), JSON_PRETTY_PRINT);
                    }
                } else {
                    // Correo incorrecto
                    echo json_encode(array(
                        'state' => '2',
                        'message' => 'Correo incorrecto'
                    ), JSON_PRETTY_PRINT);
                }
            } else {
                new Exceptions(422, "error", "Alguna propiedad no esta definida o es incorrecta");
                //$this->response(422, "error", "Alguna propiedad no esta definida o es incorrecta");
            }
        } else {
            new Exceptions(400);
            //$this->response(400);
        }
    }

}