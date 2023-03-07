<?php

namespace dateXFondoPlugin;

class FondoCompletoTableRepository
{
    public static function getJoinedArticoli($template_name)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_template_fondo  WHERE attivo =1 AND template_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $template_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getJoinedFormulas($template_name)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_formula  WHERE attivo=1 AND visibile = 1 AND formula_template_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $template_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getJoinedRecords()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT id, external_id, type, ordinamento FROM DATE_template_formula";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function updateJoinedIndex($id, $ordinamento)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_template_formula SET ordinamento = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $ordinamento, $id);
        $stmt->execute();
        mysqli_close($mysqli);
        return $stmt->affected_rows;
    }

    public static function insertJoinedIndex($external_id, $type, $ordinamento)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_template_formula (external_id, type, ordinamento) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iii", $external_id, $type, $ordinamento);
        $stmt->execute();
        $id = $mysqli->insert_id;
        mysqli_close($mysqli);
        return $id;
    }

}