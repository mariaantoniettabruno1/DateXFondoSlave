<?php

namespace dateXFondoPlugin;

use mysqli;

class TemplateRowRepository
{


    public static function delete_row($request)
    {
        if($request['city'] == ''){
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "UPDATE DATE_template_fondo SET attivo=0  WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
        }
        else{
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$request['city'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "UPDATE DATE_template_fondo SET attivo=0  WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
        }

        $mysqli->close();

    }
    public function make_template_main($request)
    {
        if($request['citySelected'] == ''){
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "UPDATE DATE_template_fondo SET principale=1  WHERE fondo=? AND anno=? AND descrizione_fondo=? AND template_name=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sissi", $request['fondo'], $request['anno'],$request['descrizione'],$request['template_name'],$request['version']);
            $res = $stmt->execute();
        }
        else{
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$request['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "UPDATE DATE_template_fondo SET principale=1  WHERE fondo=? AND anno=? AND descrizione_fondo=? AND template_name=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sissi", $request['fondo'], $request['anno'],$request['descrizione'],$request['template_name'],$request['version']);
            $res = $stmt->execute();
        }

        $mysqli->close();

    }
}