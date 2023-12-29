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
            $query = $mysqli->prepare("UPDATE DATE_users SET attivo=?  WHERE id_ente=?");
            $query->bind_param("ss", $params['attivo'], $params['id']);
            $query->execute();

            //aggiorna il flag per l'email inerente alla data di scadenza
            if ($params['data_scadenza'] != $params['data_scadenza_precedente']) {
                $query = $mysqli->prepare("UPDATE DATE_ente SET giorni_scadenza=0  WHERE id=?");
                $query->bind_param("s", $params['id']);
                $query->execute();
            }
            //funzione che mi disattiva o attiva un account
            //disabilita l'account se la data di scadenza è superata
            $sql = "SELECT id_user,ruolo,attivo FROM DATE_users WHERE id_ente=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $params['id']);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $users = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $users = [];


            //cerco gli account che hanno come ruolo contributor o subscriber e li disabilito

            for ($i = 0; $i<sizeof($users); $i++) {
                $utente_info = get_userdata($users[$i]['id_user']);

                $ruoli_utente = $utente_info->roles;
                if ($users[$i]['attivo'] === 0 || $users[$i]['attivo'] === '0') {
                    if ($ruoli_utente[0] === 'contributor' || $ruoli_utente[0] === 'subscriber') {
                        $utente_info->set_role('');


                    }
                } else {

                    if ($ruoli_utente[0] === '' || empty($ruoli_utente)) {
                        $utente_info->set_role($users[$i]['ruolo']);
                        print_r($users[$i]['ruolo']);
                        print_r($utente_info->roles);
                    }

                }
            }
        } else {
            $sql = "INSERT INTO DATE_ente (nome,descrizione,data_creazione,data_scadenza,id_consulente)  VALUES (?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssss", $params['nome'], $params['descrizione'], $params['data_creazione'], $params['data_scadenza'], $rows[0]['id']);
            $stmt->execute();

            $idente = $stmt->insert_id;
            $dbente = strtolower('c1date_' . $params['nome']);

            //per settare il ruolo dell'utente nella tabella DATE_users
            $utente_info = get_userdata($rows[0]['id']);
            $ruoli_utente = $utente_info->roles;
            $sql = "INSERT INTO DATE_users (id_user, db, id_ente,ruolo)  VALUES (?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssss", $rows[0]['id'], $dbente, $idente, $ruoli_utente[0]);
            $stmt->execute();

            $sql = "SELECT ALL id_user FROM DATE_users WHERE tutti=1";
            $result = $mysqli->query($sql);
            $ids_user = $result->fetch_all(MYSQLI_ASSOC);
            $tutti = 1;

            //TODO capire perchè mette il tutti a true solo all'ultimo
            $sql = "INSERT INTO DATE_users (id_user,db,id_ente,tutti,ruolo)  VALUES (?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            foreach ($ids_user as $id) {
                $utente_info = get_userdata($id['id_user']);
                $ruoli_utente = $utente_info->roles;
                $stmt->bind_param("sssis", $id['id_user'], $dbente, $idente, $tutti, $ruoli_utente[0]);
                $stmt->execute();
            }
        }


        $mysqli->close();

        return $users;
    }


    public function edit_cities_user($params)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM wp_users WHERE user_login=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $params['nome_utente']);
        $res = $stmt->execute();

        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];

        $sql = "UPDATE DATE_users SET attivo=0 WHERE id_user=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $rows[0]['id']);
        $res = $stmt->execute();

        $sql = "INSERT INTO DATE_users (id_user,id_ente,db,ruolo)  VALUES (?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($params['citiesArray'] as $ente) {
            $utente_info = get_userdata($rows[0]['id']);
            $ruoli_utente = $utente_info->roles;
            $stmt->bind_param("ssss", $rows[0]['id'], $ente['id'], $ente['db'], $ruoli_utente[0]);
            $res = $stmt->execute();
        }
        if ($params['tuttiButton'])
            $params['tuttiButton'] = 1;
        else
            $params['tuttiButton'] = 0;
        $sql = "UPDATE DATE_users SET attivo=1,tutti=? WHERE id_user=? AND id_ente=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($params['citiesArray'] as $ente) {
            $stmt->bind_param("iss", $params['tuttiButton'], $rows[0]['id'], $ente['id']);
            $res = $stmt->execute();
        }
        $rows = $this->getCheckedCities($params['nome_utente']);
        $mysqli->close();
        return $rows;
    }

    public function getConsultants()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT DISTINCT id,user_login FROM wp_users";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $mysqli->close();
        return $row;
    }

    public function getCities()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT id,nome FROM DATE_ente WHERE attivo=1";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        $mysqli->close();
        return $row;
    }

    public function getCheckedCities($user)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        if ($user['bool'] === 1 || $user['bool'] === '1') {

            $sql = "SELECT id FROM wp_users WHERE user_login=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $user['selectedValue']);
            $res = $stmt->execute();

            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];

        } else {
            $sql = "SELECT id FROM wp_users WHERE user_login=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $user);
            $res = $stmt->execute();

            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }


        $sql = "SELECT id_ente FROM DATE_users WHERE id_user=? AND attivo=1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $rows[0]['id']);
        $res = $stmt->execute();

        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];

        return $rows;
    }

}