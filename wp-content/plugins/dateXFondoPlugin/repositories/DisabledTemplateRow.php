<?php

namespace dateXFondoPlugin;

class DisabledTemplateRow
{
    public static function getDataByCurrentYear(){
        $year = 2022;
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_template_fondo WHERE anno=? AND attivo=0";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $year);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $entries = $res->fetch_all();
        mysqli_close($mysqli);
        return $entries;
    }

}