<?php
/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 23/02/2017
 * Time: 4:48 PM
 */

require_once "APIS/UserAPI.php";
require_once "APIS/OrderAPI.php";

$userAPI = new UserAPI();
$userAPI->API();

$orderAPI = new OrderAPI();
$orderAPI->API();


