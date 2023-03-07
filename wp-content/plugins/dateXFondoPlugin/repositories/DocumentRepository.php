<?php

use dateXFondoPlugin\Connection;
use dateXFondoPlugin\ConnectionFirstCity;

class DocumentRepository
{
    public static function getDataDocument($table_name, $city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = 'SELECT DISTINCT document_name, editor_name, anno, version FROM ' . $table_name;
            $stmt = $mysqli->prepare($sql);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        } else {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = 'SELECT DISTINCT document_name, editor_name, anno, version FROM ' . $table_name;
            $stmt = $mysqli->prepare($sql);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }

        mysqli_close($mysqli);
        return $rows;

    }

    public static function getDataOdtDocument($table_name, $city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = 'SELECT DISTINCT document_name, editor_name, anno,page,version FROM ' . $table_name;
            $stmt = $mysqli->prepare($sql);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        } else {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = 'SELECT DISTINCT document_name, editor_name, anno,page,version FROM ' . $table_name;
            $stmt = $mysqli->prepare($sql);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        mysqli_close($mysqli);
        return $rows;

    }

    public static function getArticoli($editor_name,$city)
    {
        if (!isset($city)) {

            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_documento_modello_fondo WHERE  attivo=1 and editor_name=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $editor_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_documento_modello_fondo WHERE  attivo=1 and editor_name=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $editor_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];

        }

        mysqli_close($mysqli);
        return $rows;
    }

    public static function getHistoryArticoli($editor_name, $version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_documento_modello_fondo_storico WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);

            $sql = "SELECT * FROM DATE_documento_modello_fondo_storico WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        mysqli_close($mysqli);
        return $rows;
    }


    public static function getHistoryArticoliUtilizzo($editor_name, $version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_documento_modello_fondo_utilizzo_storico WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);

            $sql = "SELECT * FROM DATE_documento_modello_fondo_utilizzo_storico WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getHistoryArticoliDatiUtili($editor_name, $version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_documento_modello_fondo_dati_utili WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_documento_modello_fondo_dati_utili WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getIdsArticoli($editor_name,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT id_articolo,valore FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $editor_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        else{

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT id_articolo,valore FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $editor_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getSezioni($editor_name, $version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_storico WHERE editor_name=? and version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
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
            $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_storico WHERE editor_name=? and version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $editor_name, $version);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getSezioniUtilizzo($template_name, $version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_utilizzo_storico WHERE editor_name=? and version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $version);
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
            $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_utilizzo_storico WHERE editor_name=? and version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $version);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getSezioniDatiUtili($template_name, $version,$city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_dati_utili_storico WHERE editor_name=? and version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $version);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else {

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_dati_utili_storico WHERE editor_name=? and version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $version);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getFormulas($document_name, $city)
    {
        if (!isset($city)) {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT nome,valore FROM DATE_formula WHERE formula_template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $document_name);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else {

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_'.$city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT nome,valore FROM DATE_formula WHERE formula_template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $document_name);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }

        mysqli_close($mysqli);
        return $rows;
    }


}
