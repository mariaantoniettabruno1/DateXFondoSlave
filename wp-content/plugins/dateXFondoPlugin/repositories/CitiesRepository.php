<?php

namespace dateXFondoPlugin;

use mysqli;
use PHPMailer\PHPMailer\Exception;

class CitiesRepository
{
    public function get_data($params)
    {
        if ($params['citySelected'] !== "" && isset($params['citySelected'])) {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $params['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
        }

        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,template_name,version,ufficiale FROM DATE_template_fondo  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        $history_data = $this->get_history_data($params);
        return array($row, $history_data);
    }

    public function get_history_data($params)
    {

        if ($params['citySelected'] !== "" && isset($params['citySelected'])) {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $params['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
        }
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,editable,version,template_name,ufficiale FROM DATE_storico_template_fondo WHERE id_articolo IS NOT NULL and attivo=1  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public function get_row_data($params)
    {

        if ($params['citySelected'] !== "" && isset($params['citySelected'])) {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $params['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
        }
        $sql = "SELECT * FROM DATE_template_fondo WHERE attivo = 0 and row_type='special' ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public function get_city_user_data($params)
    {
        if ($params['citySelected'] !== "" && isset($params['citySelected'])) {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $params['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
        }
        $sql = "SELECT * FROM DATE_user_form";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public function get_city_document_data($params)
    {
        $document_repository = new \DocumentRepository();
        $documents = array_merge(
            array_map(function ($doc) {
                $doc['page'] = 'documento-modello-fondo';
                return $doc;
            }, $document_repository->getDataDocument('DATE_documento_modello_fondo_storico', $params['citySelected'])),
            array_map(function ($doc) {
                $doc['page'] = 'regioni_autonomie_locali';

                return $doc;
            }, $document_repository->getDataDocument('DATE_documento_regioni_autonomie_locali_storico', $params['citySelected'])),
            $document_repository->getDataOdtDocument('DATE_documenti_odt_storico', $params['citySelected'])
        );

        return $documents;
    }

    public function getAllCities()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT id,nome,descrizione,data_creazione,data_scadenza,id_consulente,attivo FROM DATE_ente";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);

        $sql = "SELECT DISTINCT id,user_login FROM wp_users WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($row as $entry) {
            $stmt->bind_param("i", $entry['id_consulente']);
            $res = $stmt->execute();
        }

        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        return array($row, $rows);
    }

    public function edit_city_row($params)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM wp_users WHERE user_login=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $params['id_consulente']);
        $res = $stmt->execute();

        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        if ($params['id'] != '') {
            $query = $mysqli->prepare("UPDATE DATE_ente SET nome=?, descrizione=?, data_creazione=?, data_scadenza=?, attivo=?, id_consulente=?  WHERE id=?");
            $query->bind_param("sssssss", $params['nome'], $params['descrizione'], $params['data_creazione'], $params['data_scadenza'], $params['attivo'], $rows[0]['id'], $params['id']);
            $query->execute();
        } else {

            $sql = "INSERT INTO DATE_ente (nome, descrizione, data_creazione, data_scadenza, attivo, id_consulente)  VALUES (?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssssss", $params['nome'], $params['descrizione'], $params['data_creazione'], $params['data_scadenza'], $params['attivo'], $rows[0]['id']);
            $stmt->execute();
        }


        $mysqli->close();
        return $stmt->insert_id;
    }

    public function getConsultants()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT DISTINCT id,user_login FROM wp_users ";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $mysqli->close();
        return $row;
    }

}