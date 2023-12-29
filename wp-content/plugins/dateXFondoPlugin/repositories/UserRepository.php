<?php

namespace dateXFondoPlugin;

use mysqli;
use PHPMailer\PHPMailer\Exception;

class UserRepository
{

    public static function getUserInfos()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_user_form";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function checkNewTemplate($id_user)
    {
        $bool = false;
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT MAX(id) FROM DATE_template_fondo";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id_template FROM DATE_users WHERE id_user=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $id_user);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $id_template = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $id_template = [];
        if ($rows[0]['MAX(id)'] != $id_template[0]['id_template']) {
            $sql = "UPDATE DATE_users SET id_template=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s",
                $rows[0]['MAX(id)']);
            $res = $stmt->execute();
            $bool = true;

        }
        mysqli_close($mysqli);
        return $bool;
    }

    function update_user_settings($request)
    {
        if ($request['citySelected'] == '') {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "UPDATE DATE_user_form SET titolo_ente=?, nome_soggetto_deliberante=?,responsabile=?,firma=?,riduzione_spesa=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssss",
                $request['titolo_ente'],
                $request['soggetto_deliberante'],
                $request['responsabile_documento'],
                $request['firma'],
                $request['riduzione_spesa']);
            $res = $stmt->execute();
        } else {

            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $request['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "UPDATE DATE_user_form SET titolo_ente=?, nome_soggetto_deliberante=?,responsabile=?,firma=?,riduzione_spesa=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssss",
                $request['titolo_ente'],
                $request['soggetto_deliberante'],
                $request['responsabile_documento'],
                $request['firma'],
                $request['riduzione_spesa']);
            $res = $stmt->execute();
        }
        $mysqli->close();
        return $res;
    }

    /*function check_new_template($request){
        if($request['citySelected'] == ''){
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_template_fondo WHERE new=1";
            $result = $mysqli->query($sql);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
        }
    else{

        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_'.$request['citySelected'];
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT * FROM DATE_template_fondo WHERE new=1";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
    }
        $mysqli->close();
        return $rows;
    }*/
    public static function getAllUserCities($id_user)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id_ente FROM DATE_users WHERE id_user=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $id_user);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $cities = $res->fetch_all(MYSQLI_ASSOC);
        $sql = "SELECT nome FROM DATE_ente WHERE id=? AND attivo=1";
        $stmt = $mysqli->prepare($sql);
        foreach ($cities as $city) {
            $stmt->bind_param("s", $city['id_ente']);
            $res = $stmt->execute();

            if ($res === false) {
                die('Errore nell\'esecuzione della query: ' . $stmt->error);
            }

            // Ottieni i risultati e aggiungili all'array
            $res = $stmt->get_result();
            $risultati[] = $res->fetch_all(MYSQLI_ASSOC);

        }
        mysqli_close($mysqli);
        return $risultati;
    }
}