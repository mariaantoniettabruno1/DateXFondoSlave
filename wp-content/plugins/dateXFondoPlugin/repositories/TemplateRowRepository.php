<?php

namespace dateXFondoPlugin;

use mysql_xdevapi\Exception;
use mysqli;

class TemplateRowRepository
{


    public static function delete_row($request)
    {
        if($request['city'] == '' || !isset($request['city'])){
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
        }
        else{
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$request['city'];
            $mysqli = new mysqli($url, $username, $password, $dbname);

        }
        $sql = "UPDATE DATE_template_fondo SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id']);
        $res = $stmt->execute();
        $mysqli->close();
        return $res;

    }
    public function make_template_main($request)
    {
        if($request['citySelected'] == '' || !isset($request['citySelected'])){
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();

        }
        else{
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$request['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);

        }
        if($request['check'] ==='1' || $request['check'] === 1){
            $query = $mysqli->prepare("UPDATE DATE_template_fondo SET ufficiale=1  WHERE fondo=? AND anno=? AND descrizione_fondo=? AND template_name=? AND version=? ");
        }
        else{
            $query = $mysqli->prepare("UPDATE DATE_template_fondo SET ufficiale=0  WHERE fondo=? AND anno=? AND descrizione_fondo=? AND template_name=? AND version=? ");
        }

        $query->bind_param("sissi", $request['fondo'],$request['anno'],$request['descrizione'],$request['template_name'],$request['version']);
        $result = $query->execute();

        $mysqli->close();
        return $result;

    }
}