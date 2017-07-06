<?php

/**
 * Created by PhpStorm.
 * User: DESARROLLO HAPPY INC
 * Date: 12/05/2017
 * Time: 10:31 AM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../Utils/Exceptions.php';

class Security
{

    function __construct()
    {
    }

    private
    function generarClaveApi()
    {
        $mydate = getdate(date("U"));
        $day = "";
        $lon = strlen($mydate[mday]);
        if ($lon == 1) {
            $day = "0" . $mydate[mday];
        } else {
            $day = $mydate[mday];
        }

        $key = "H*2017*" . $day;
        return md5($key);
    }

    public
    function autorizar()
    {
        $cabeceras = apache_request_headers();

        if (isset($cabeceras["Authorizationh"])) {
            $claveApi = $cabeceras["Authorizationh"];

            if ($claveApi === $this->generarClaveApi()) {
                return 10;
            } else {
                new Exceptions(422, "error", "Clave de API no autorizada");
            }

        } else {
            new Exceptions(422, "error", "Se requiere Clave del API para autenticación");
        }
    }
}