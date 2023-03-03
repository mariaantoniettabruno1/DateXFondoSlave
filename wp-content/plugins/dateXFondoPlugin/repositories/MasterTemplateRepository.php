<?php

namespace dateXFondoPlugin;

use mysqli;

class MasterTemplateRepository
{
    public static function getArticoli($template_name, $city)
    {
        if (isset($city) && $city != '') {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $template_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        } else {
            $conn = new Connection();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $template_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];

        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getStoredArticoli()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,editable,version,template_name FROM DATE_storico_template_fondo WHERE id_articolo IS NOT NULL and attivo=1  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public static function getDisabledArticoli()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_template_fondo WHERE attivo = 0 and row_type='special' ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public static function getAllTemplate()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,template_name FROM DATE_template_fondo  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }


    public static function edit_row($request)
    {
        if ($request['city'] == '') {
            $conn = new Connection();
            $mysqli = $conn->connect();

            $sql = "UPDATE DATE_template_fondo SET valore=?,
                               valore_anno_precedente=?,
                               nota=?
WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iisi",
                $request['valore'],
                $request['valore_anno_precedente'],
                $request['nota'],
                $request['id']);
            $res = $stmt->execute();
        } else {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$request['city'];
            $mysqli = new mysqli($url, $username, $password, $dbname);

            $sql = "UPDATE DATE_template_fondo SET valore=?,
                               valore_anno_precedente=?,
                               nota=?
WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iisi",
                $request['valore'],
                $request['valore_anno_precedente'],
                $request['nota'],
                $request['id']);
            $res = $stmt->execute();
        }

        $mysqli->close();
        return $res;
    }


    public static function active_row($request)
    {
        if(!isset($request['citySelected']) || $request['citySelected'] == ''){
            $conn = new Connection();
            $mysqli = $conn->connect();
            $sql = "UPDATE DATE_template_fondo SET attivo=1 WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
            $sql = "UPDATE DATE_storico_template_fondo SET attivo=1 WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisi", $request['fondo'], $request['anno'], $request['descrizione'], $request['version']);
            $res = $stmt->execute();
        }
        else{
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$request['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "UPDATE DATE_template_fondo SET attivo=1 WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
            $sql = "UPDATE DATE_storico_template_fondo SET attivo=1 WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisi", $request['fondo'], $request['anno'], $request['descrizione'], $request['version']);
            $res = $stmt->execute();
        }

        mysqli_close($mysqli);
        return $res;
    }


    public static function visualize_template($fondo, $anno, $descrizione, $version, $template_name, $city)
    {
        if (isset($city) && $city!= '') {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_storico_template_fondo WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=? AND template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisis", $fondo, $anno, $descrizione, $version, $template_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        } else {
            $conn = new Connection();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_storico_template_fondo WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=? AND template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisis", $fondo, $anno, $descrizione, $version, $template_name);
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