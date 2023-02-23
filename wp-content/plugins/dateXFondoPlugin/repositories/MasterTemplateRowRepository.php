<?php

namespace dateXFondoPlugin;

use mysqli;

class MasterTemplateRowRepository
{


    public static function delete_row($request)
    {
        if($request['city'] == ''){
            $conn = new Connection();
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
            $dbname = 'c1date_custom';
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "UPDATE DATE_template_fondo SET attivo=0  WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
        }

        $mysqli->close();

    }
}