<?php

namespace dateXFondoPlugin;

use mysqli;

class CitiesRepository
{
    public function get_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,template_name FROM DATE_template_fondo  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }
    public function get_history_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,editable,version,template_name FROM DATE_storico_template_fondo WHERE id_articolo IS NOT NULL and attivo=1  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public function get_row_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT * FROM DATE_template_fondo WHERE attivo = 0 and row_type='special' ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

}