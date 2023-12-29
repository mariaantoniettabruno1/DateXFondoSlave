<?php

namespace dateXFondoPlugin;

class MailSenderRepository
{
    public function getExpireDate()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT data_scadenza,id,id_consulente,giorni_scadenza FROM DATE_ente";
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();

        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        $mysqli->close();
        return $rows;
    }

    public function getUserID($enti_array)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id_user FROM DATE_users WHERE id_ente=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($enti_array as $ente_id) {
            $stmt->bind_param("i", $ente_id);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows[] = $res->fetch_all(MYSQLI_ASSOC)[0];
            }
        }

        $mysqli->close();
        return $rows;
    }
    public function updateExpireDays($enti_array) {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "UPDATE DATE_ente SET giorni_scadenza=1 WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($enti_array as $ente_id) {
            $stmt->bind_param("i", $ente_id);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows[] = $res->fetch_all(MYSQLI_ASSOC)[0];
            }
        }
        $mysqli->close();
        return $rows;
    }
    public function updateCitiesStatus($enti_array) {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "UPDATE DATE_ente SET attivo=0 WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($enti_array as $ente_id) {
            $stmt->bind_param("i", $ente_id);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows[] = $res->fetch_all(MYSQLI_ASSOC)[0];
            }
        }

        $sql = "UPDATE DATE_users SET attivo=0 WHERE id_ente=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($enti_array as $ente_id) {
            $stmt->bind_param("i", $ente_id);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows[] = $res->fetch_all(MYSQLI_ASSOC)[0];
            }
        }
        $mysqli->close();
        return $rows;
    }
}


