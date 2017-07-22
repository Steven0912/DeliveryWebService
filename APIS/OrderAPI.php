<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/User.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/AssignedOrderDelivery.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/Product.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/Order.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/LocationClient.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/State.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Models/Ticket.php';
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

                    if ($_GET['action'] == 'orders' || $_GET['action'] == 'orders_states') {
                        $this->getOrdersList();
                    }
                    break;
                case 'POST':
                    $this->createOrder();
                    break;

                case 'PUT':
                    if ($_GET['action'] == 'orders') {
                        $this->updateOrder();
                    }
                    break;

                default:
                    new Exceptions(405);
                    break;
            }
        }
    }

    private function createOrder()
    {
        if ($_GET['action'] == 'orders') {
            //Decodifica un string de JSON
            $obj = json_decode(file_get_contents('php://input'), true);

            if (empty((array)$obj)) {
                new Exceptions(422, "error", "Nada para añadir, revisa los datos");
                //$this->response(422, "error", "Nada para añadir, revisa los datos");
            } else if (
                isset($obj['id_pedido']) &&
                isset($obj['id_estado']) &&
                isset($obj['fecha']) &&
                isset($obj['hora'])
            ) {

                if ($obj['id_estado'] == 15) {
                    $ifDispatched = Order::validateIfOrderWasDispatched($obj['id_pedido'], 8);
                    if ($ifDispatched) {
                        $response = Order::create(
                            $obj['id_pedido'],
                            $obj['id_estado'],
                            $obj['fecha'],
                            $obj['hora']
                        );

                        if ($response == -1) {
                            new Exceptions(200, "2", "Error al cambiar el estado");
                        } else if ($response == 1) {
                            Order::updateStateOrder($obj['id_estado'], $obj['id_pedido']);
                            new Exceptions(200, "1", "Estado actualizado");
                        }
                    } else {
                        new Exceptions(200, "2", "El emprendedor debe cambiar el estado del pedido a 'DESPACHADO'");
                    }
                } else {
                    $response = Order::create(
                        $obj['id_pedido'],
                        $obj['id_estado'],
                        $obj['fecha'],
                        $obj['hora']
                    );

                    if ($response == -1) {
                        new Exceptions(200, "2", "Error al cambiar el estado");
                    } else if ($response == 1) {
                        Order::updateStateOrder($obj['id_estado'], $obj['id_pedido']);
                        new Exceptions(200, "1", "Estado actualizado");
                    }
                }

                // *611

            } else {
                new Exceptions(422, "error", "Alguna propiedad no esta definida o es incorrecta");
                //$this->response(422, "error", "Alguna propiedad no esta definida o es incorrecta");
            }
        } else if ($_GET['action'] == 'chat') {
            //Decodifica un string de JSON
            $obj = json_decode(file_get_contents('php://input'), true);

            if (empty((array)$obj)) {
                new Exceptions(422, "error", "Nada para añadir, revisa los datos");
                //$this->response(422, "error", "Nada para añadir, revisa los datos");
            } else if (
                isset($obj['id_pedido']) &&
                isset($obj['id_origen']) &&
                isset($obj['id_destino']) &&
                isset($obj['fecha_mensaje']) &&
                isset($obj['mensaje'])
            ) {

                $timestamp = strtotime($obj['fecha_mensaje']);

                $responseTicket = Ticket::validateIfStartTicketChat($obj['id_pedido'], $obj['id_origen'], $obj['id_destino']);
                if (!$responseTicket) {
                    $response = Ticket::createTicket($obj['id_pedido'], $obj['id_origen'], $obj['id_destino'], date("Y-m-d H:i:s", $timestamp));
                    $responseTicket = Ticket::validateIfStartTicketChat($obj['id_pedido'], $obj['id_origen'], $obj['id_destino']);
                    if ($response) {
                        new Exceptions(200, "1", "Ticket registrado");
                    } else {
                        new Exceptions(422, "error", "No se pudo registrar el Ticktet");
                    }
                }

                Ticket::createBitacoraTicket($responseTicket['id'], date("Y-m-d H:i:s", $timestamp), $obj['id_origen'], $obj['mensaje']);
                $response = Ticket::getBitacoraTickets();
                $messagelist["state"] = 1;
                $messagelist["orderslist"] = $response;
                echo json_encode($messagelist, JSON_PRETTY_PRINT);

            } else {
                new Exceptions(422, "error", "Alguna propiedad no esta definida o es incorrecta");
            }
        } else {
            new Exceptions(400);
            //$this->response(400);
        }
    }

    private
    function getOrdersList()
    {
        if ($_GET['action'] == 'orders') {

            if (isset($_GET['id'])) {//muestra 1 solo registro si es que existiera ID
                /*
                 * $_GET['id'] id del domiciliario el que llega
                 */
                $ids_pedidos = AssignedOrderDelivery::getAssignedOrderDeliveryById($_GET['id']);

                if ($ids_pedidos) {
                    $i = 0;
                    $response = null;
                    /*print_r($ids_pedidos[1]['id_pedido']);
                    die();*/
                    foreach ($ids_pedidos as $id_pedido) {
                        /*print_r($id_pedido['id_pedido']);
                        die();*/
                        if (Order::validateIfOrderWasDispatched($id_pedido['id_pedido'], 16) == null) {

                            $pedido = Order::getOrderById($id_pedido['id_pedido']);
                            $estadoPedidoAsigandoAlDomiciliario = AssignedOrderDelivery::getListAssignedOrderDeliveryByIds($id_pedido['id_pedido'], $_GET['id']);

                            if (($pedido) && ($estadoPedidoAsigandoAlDomiciliario['id_estado'] == 1)) {

                                $cliente = User::getUser($pedido['id_usuario']);
                                $ubicacion_cliente = LocationClient::getLocationClientById($pedido['id_ubicacion_cliente']);
                                $zona = Zone::getZoneById($pedido['id_zona']);
                                $estado = State::getStateById($pedido['id_estado']);
                                $producto = Product::getProductById($pedido['id_producto']);
                                $asociado = User::getUser($producto['id_usuario']);

                                $response[$i]['id'] = $pedido['id'];
                                $response[$i]['id_client'] = $pedido['id_usuario'];
                                $response[$i]['full_name_client'] = $cliente['nombre_completo'];
                                $response[$i]['id_location_client'] = $pedido['id_ubicacion_cliente'];
                                $response[$i]['latitude_c'] = "" . $ubicacion_cliente['latitud'];
                                $response[$i]['longitude_c'] = "" . $ubicacion_cliente['longitud'];
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
                                $response[$i]['latitude_a'] = "" . $asociado['latitud'];
                                $response[$i]['longitude_a'] = "" . $asociado['longitud'];
                                $response[$i]['address_a'] = $asociado['direccion'];

                                $i++;
                            }

                        }

                    }


                    if ($response == null) {
                        echo json_encode(array(
                            'state' => '2',
                            'message' => 'No tienes pedidos asignados'
                        ), JSON_PRETTY_PRINT);
                    } else {
                        $orderslist["state"] = 1;
                        $orderslist["orderslist"] = $response;
                        echo json_encode($orderslist, JSON_PRETTY_PRINT);
                    }

                } else {
                    echo json_encode(array(
                        'state' => '2',
                        'message' => 'No tienes pedidos asignados'
                    ), JSON_PRETTY_PRINT);
                }
            }
        } else if ($_GET['action'] == 'orders_states') {
            if (isset($_GET['id'])) {
                /*
                 * $_GET['id'] id del pedido el que llega
                 */


                $fecha_aceptacion = AssignedOrderDelivery::getAcceptDateByIdOrder($_GET['id']);
                if ($fecha_aceptacion['fecha_aceptacion'] == null) {
                    new Exceptions(200, "1", "Botones Visibles y Slides Ocultos");
                } else {
                    $estado_slide = Order::getStateByIdOrder($_GET['id']);
                    $id_state = "";
                    if ($estado_slide == null) {
                        $id_state = -1;
                    } else {
                        foreach ($estado_slide as $last_state) {
                            $id_state = $last_state['id_estado'];
                            break;
                        }
                    }

                    $response['status'] = 2;
                    $response['slide_state'] = $id_state;
                    echo json_encode($response, JSON_PRETTY_PRINT);
                }
            }
        } else {
            new Exceptions(400);
            //$this->response(400);
        }
    }

    private function updateOrder()
    {
        if ($_GET['action'] == 'orders') {

            $obj = json_decode(file_get_contents('php://input'), true);

            if (empty((array)$obj)) {
                new Exceptions(422, "error", "Nada para actualizar, revisa los datos");
            } else if (
                isset($obj['id_pedido']) &&
                isset($obj['id_domiciliario']) &&
                isset($obj['id_estado']) &&
                isset($obj['reasons']) &&
                isset($obj['current_datetime']) &&
                count($obj) == 5
            ) {
                $timestamp = strtotime($obj['current_datetime']);
                if ($timestamp != null) {
                    AssignedOrderDelivery::updateOrderStateDecline(
                        $obj['id_pedido'],
                        $obj['id_domiciliario'],
                        $obj['id_estado'],
                        $obj['reasons'],
                        date("Y-m-d H:i:s", $timestamp)
                    );
                    new Exceptions(200, "1", "Estado actualizado a rechazado");

                } else {
                    new Exceptions(422, "2", "Error al actualizar");
                }

            } else if (
                isset($obj['id_pedido']) &&
                isset($obj['id_domiciliario']) &&
                isset($obj['current_datetime']) &&
                count($obj) == 3
            ) {

                $timestamp = strtotime($obj['current_datetime']);
                if ($timestamp != null) {
                    AssignedOrderDelivery::updateOrderStateAccept(
                        $obj['id_pedido'],
                        $obj['id_domiciliario'],
                        date("Y-m-d H:i:s", $timestamp)
                    );

                    new Exceptions(200, "1", "Estado actualizado a aceptado");

                } else {
                    new Exceptions(422, "2", "Error al actualizar");
                }

            } else {
                new Exceptions(422, "error", "Alguna propiedad no esta definida o es incorrecta");
            }
        } else {
            new Exceptions(400);
        }
    }
}