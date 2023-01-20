<?php

use dateXFondoPlugin\Connection;

class FormulaTable
{

    public static function getArticoli(){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id_articolo, nome_articolo, sottotitolo_articolo, sezione, sottosezione FROM DATE_template_fondo WHERE id_articolo IS NOT NULL";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }



    public static function getAllSections()
    {

       $conn = new Connection();
       $mysqli = $conn->connect();
       $sql = "SELECT DISTINCT sezione FROM DATE_template_fondo";
       $result = $mysqli->query($sql);
       $row = $result->fetch_all();
       mysqli_close($mysqli);
       return $row;
    }

    public static function getAllEntriesFromSection($selected_section)
    {
//        $conn = new Connection();
//        $mysqli = $conn->connect();
//        $sql = "SELECT * FROM DATE_template_fondo WHERE sezione=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("s", $selected_section);
//        $res = $stmt->execute();
//        $res = $stmt->get_result();
//        $entries = $res->fetch_all();
//        mysqli_close($mysqli);
//        return $entries;
    }



    public static function getAllFormulasBySection($selected_section)
    {
//        $conn = new Connection();
//        $mysqli = $conn->connect();
//        $sql = "SELECT * FROM DATE_formula WHERE sezione=?";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("s", $selected_section);
//        $res = $stmt->execute();
//        $res = $stmt->get_result();
//        $entries = $res->fetch_all();
//        mysqli_close($mysqli);
//        return $entries;
    }
}