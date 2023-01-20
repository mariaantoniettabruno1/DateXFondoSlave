<?php

namespace dateXFondoPlugin;

class CustomTable
{
    public static function getAllYears()
    {
      
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT anno FROM DATE_entry_chivasso";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all();
        mysqli_close($mysqli);
        return $row;
    }

    public static function getAllFondi()
    {

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT fondo FROM DATE_entry_chivasso";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all();
        mysqli_close($mysqli);
        return $row;
    }

    public static function isReadOnly($year)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT anno FROM DATE_submitted_years WHERE anno=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $year);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $entry = $res->fetch_assoc();
        mysqli_close($mysqli);
        return $entry != NULL;
    }

    public static function getAllEntries($selected_year)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_entry_chivasso WHERE anno=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $selected_year);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $entries = $res->fetch_all();
        mysqli_close($mysqli);
        return $entries;
    }

    public static function getAllEntriesFromYearsFondo($selected_year, $selected_fondo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_entry_chivasso WHERE anno=? AND fondo=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("is", $selected_year, $selected_fondo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $entries = $res->fetch_all();
        mysqli_close($mysqli);
        return $entries;
    }

    public static function getTableNotEditable($year)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_submitted_years (anno) VALUES (?) ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $year);
        $res = $stmt->execute();
        mysqli_close($mysqli);

    }

    public static function duplicateTable($year)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "CREATE TABLE DATE_entry_chivasso_duplicata LIKE DATE_entry_chivasso";
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        $sql = "INSERT INTO DATE_entry_chivasso_duplicata SELECT * from DATE_entry_chivasso WHERE anno=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $year);
        $res = $stmt->execute();
        mysqli_close($mysqli);


    }


}