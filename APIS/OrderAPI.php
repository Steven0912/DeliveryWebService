<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/User.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/AssignedOrderDelivery.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/Order.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/LocationClient.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/State.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/Zone.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../APIS/Security.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Utils/Exceptions.php';
error_reporting(0);

/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 6/07/2017
 * Time: 11:48 AM
 */
class OrderAPI
{
    public function API()
    {
        header('Content-Type: application/JSON');
        $method = $_SERVER['REQUEST_METHOD'];

        $obj = new Security();
        if ($obj->autorizar() == 10) {

            switch ($method) {
                case 'GET':
                    if ($_GET['action'] == 'orders') {
                        $this->getOrdersList();
                    }
                    break;
                case 'POST':
                    //$this->createUser();
                    break;

                default:
                    new Exceptions(405);
                    break;
            }
        }
    }

    private
    function getOrdersList()
    {
        if ($_GET['action'] == 'orders') {

            if (isset($_GET['id'])) {//muestra 1 solo registro si es que existiera ID
                $ids_pedidos = AssignedOrderDelivery::getAssignedOrderDeliveryById($_GET['id']);

                $i = 0;
                $response = null;

                foreach ($ids_pedidos as $id_pedido) {
                    $pedido = Order::getOrderById($id_pedido);
                    if ($pedido) {
                        $cliente = User::getUser($pedido['id_usuario']);
                        $ubicacion_cliente = LocationClient::getLocationClientById($pedido['id_ubicacion_cliente']);
                        $zona = Zone::getZoneById($pedido['id_zona']);
                        $estado = State::getStateById($pedido['id_estado']);
                        $producto = Product::getProductById($pedido['id_producto']);
                        $asociado = User::getUser($producto['id_usuario_crea']);

                        $response[$i]['id'] = $pedido['id'];
                        $response[$i]['id_client'] = $pedido['id_usuario'];
                        $response[$i]['full_name_client'] = $cliente['nombre_completo'];
                        $response[$i]['id_location_client'] = $pedido['id_ubicacion_cliente'];
                        $response[$i]['latitude_c'] = $ubicacion_cliente['latitud'];
                        $response[$i]['longitude_c'] = $ubicacion_cliente['longitud'];
                        $response[$i]['address_c'] = $ubicacion_cliente['direccion'];
                        $response[$i]['id_zone'] = $pedido['id_zona'];
                        $response[$i]['zone_description'] = $zona['descripcion'];
                        $response[$i]['id_state_order'] = $pedido['id_estado'];
                        $response[$i]['state_description'] = $estado['descripcion'];
                        $response[$i]['id_product'] = $pedido['id_producto'];
                        $response[$i]['product_name'] = $producto['nombre'];
                        $response[$i]['quantity_order'] = $pedido['cantidad'];
                        $response[$i]['order_date'] = $pedido['fecha'];
                        $response[$i]['price'] = $pedido['precio'];
                        $response[$i]['id_associate'] = $asociado['id'];
                        $response[$i]['full_name_associate'] = $asociado['nombre_completo'];
                        $response[$i]['latitude_a'] = $asociado['latitud'];
                        $response[$i]['longitude_a'] = $asociado['longitud'];
                        $response[$i]['address_a'] = $asociado['direccion'];

                        $i++;
                    }
                }

                $orderslist["state"] = 1;
                $orderslist["orderslist"] = $response;
                echo json_encode($orderslist, JSON_PRETTY_PRINT);
            }
        } else {
            new Exceptions(400);
            //$this->response(400);
        }
    }
}