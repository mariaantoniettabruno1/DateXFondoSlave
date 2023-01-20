<?php

use dateXFondoPlugin\Connection;

class SlaveFormulaTable
{
    public static function getFormulaBySelectedSection($selected_subsection)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_formula WHERE sottosezione=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $selected_subsection);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $entries = $res->fetch_all();
        mysqli_close($mysqli);
        return $entries;
    }

    public static function getValueFromIdCampo($id_campo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT valore FROM DATE_entry_chivasso WHERE id_campo=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $id_campo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $entries = $res->fetch_assoc();
        mysqli_close($mysqli);
        return $entries;
    }

    public static function saveTotal($totale, $formula, $sezione, $fondo, $ente, $anno)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_formula (totale,sezione, formula, fondo, ente, anno) VALUES (?,?,?,?,?,?) ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("issssi", $totale, $sezione, $formula, $fondo, $ente, $anno);
        $res = $stmt->execute();
        mysqli_close($mysqli);
    }
}