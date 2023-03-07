<?php

use dateXFondoPlugin\Connection;
use dateXFondoPlugin\ConnectionFirstCity;

class FormulaRepository
{

    public static function getArticoli()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_template_fondo WHERE id_articolo IS NOT NULL AND attivo=1";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }


    public static function getFormule()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_formula WHERE attivo = 1";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public static function create_formula($request)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_formula (sezione,sottosezione,nome,descrizione,condizione,formula,visibile,formula_template_name,text_type) VALUES (?,?,?,?,?,?,?,?,?) ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssssisiss",
            $request['sezione'],
            $request['sottosezione'],
            $request['nome'],
            $request['descrizione'],
            $request['condizione'],
            $request['formula'],
            $request['visibile'],
            $request['formula_template_name'],
            $request['text_type']);
        $stmt->execute();
        mysqli_close($mysqli);
        return $stmt->insert_id;
    }

    public static function update_formula($request)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_formula SET sezione = ?, sottosezione = ?, nome = ?, descrizione = ?, condizione = ?, formula = ?, visibile = ?, formula_template_name=? WHERE ID = ?;";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssssisisi",
            $request['sezione'],
            $request['sottosezione'],
            $request['nome'],
            $request['descrizione'],
            $request['condizione'],
            $request['formula'],
            $request['visibile'],
            $request['formula_template_name'],
            $request['id']);
        $stmt->execute();
        mysqli_close($mysqli);
        return $stmt->affected_rows;
    }

    public static function valorize_formula($request)
    {
        //(opzionale) Aggiungere il formula_template_name
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_formula SET valore = ? WHERE nome = ?";
        $stmt = $mysqli->prepare($sql);
        $i = 0;
        foreach($request as $item){
            $stmt->bind_param("ss",
                $item[$i]['valore'],  $item[$i]['formula']);
            $stmt->execute();$i++;
        }

        mysqli_close($mysqli);
        return $stmt->affected_rows;
    }

    public static function delete_formula($request)
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_formula SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id']);
        $res = $stmt->execute();
        $mysqli->close();
        return $res;
    }
}