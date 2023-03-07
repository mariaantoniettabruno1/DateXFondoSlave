<?php

namespace dateXFondoPlugin;

class TemplateHistory
{
public static function getAllYears(){
    $conn = new ConnectionFirstCity();
    $mysqli = $conn->connect();
    $sql = "SELECT DISTINCT anno FROM DATE_storico_template_fondo";
    $result = $mysqli->query($sql);
    $years = $result->fetch_all();
    mysqli_close($mysqli);
    return $years;
}

public static function getCurrentDataByYear($year){
    $conn = new ConnectionFirstCity();
    $mysqli = $conn->connect();
    $sql = "SELECT * FROM DATE_storico_template_fondo WHERE anno=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $year);
    $res = $stmt->execute();
    $res = $stmt->get_result();
    $entries = $res->fetch_all();
    mysqli_close($mysqli);
    return $entries;
}

}