<?php

namespace dateXFondoPlugin;

use mysqli;

class RegioniDocumentRepository
{
    public static function getHistoryCostituzioneArticoli($template_name,$version,$city)
    {
        if(!isset($city)){
            $conn = new Connection();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_documento_regioni_autonomie_locali_storico WHERE titolo_tabella='Costituzione fondi per il trattamento accessorio' AND attivo=1 and editor_name=? and version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name,$version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{
            //cambiare il db name concatenando la stringa con il params che gli passo
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_custom';
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_documento_regioni_autonomie_locali_storico WHERE titolo_tabella='Costituzione fondi per il trattamento accessorio' AND attivo=1 and editor_name=? and version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name,$version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }

        mysqli_close($mysqli);
        return $rows;
    }

    public static function getHistoryDestinazioneArticoli($template_name,$version,$city)
    {
        if(!isset($city)) {
            $conn = new Connection();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_documento_regioni_autonomie_locali_storico WHERE titolo_tabella='Destinazione fondi per il trattamento accessorio'AND attivo=1 and editor_name=? and version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{
            //cambiare il db name concatenando la stringa con il params che gli passo
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_custom';
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_documento_regioni_autonomie_locali_storico WHERE titolo_tabella='Destinazione fondi per il trattamento accessorio'AND attivo=1 and editor_name=? and version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        mysqli_close($mysqli);
        return $rows;
    }

}