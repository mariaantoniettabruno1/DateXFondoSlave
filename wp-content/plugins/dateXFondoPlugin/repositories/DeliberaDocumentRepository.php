<?php

namespace dateXFondoPlugin;

use mysqli;

class DeliberaDocumentRepository
{

    public static function getAllHistoryValues($document_name, $editor_name,$version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT chiave, valore, document_name, editor_name, anno, editable FROM DATE_documenti_odt_storico WHERE document_name=? AND editor_name=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $document_name, $editor_name, $version);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }
        else{

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT chiave, valore, document_name, editor_name, anno, editable FROM DATE_documenti_odt_storico WHERE document_name=? AND editor_name=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $document_name, $editor_name, $version);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }
        mysqli_close($mysqli);
        return $rows;
    }


}