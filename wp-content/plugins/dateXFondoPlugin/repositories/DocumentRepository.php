<?php

use dateXFondoPlugin\Connection;

class DocumentRepository
{
    public static function getDataDocument($table_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = 'SELECT DISTINCT document_name, editor_name, anno, version FROM ' . $table_name;
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;

    }

    public static function getDataOdtDocument($table_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = 'SELECT DISTINCT document_name, editor_name, anno,page,version FROM ' . $table_name;
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;

    }

    public static function getArticoli($editor_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_documento_modello_fondo WHERE  attivo=1 and editor_name=? ORDER BY ordinamento ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $editor_name);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }
    public static function getHistoryArticoli($editor_name, $version)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_documento_modello_fondo_storico WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $editor_name,$version);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }



    public static function getHistoryArticoliUtilizzo($editor_name,$version)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_documento_modello_fondo_utilizzo_storico WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $editor_name,$version);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getHistoryArticoliDatiUtili($editor_name,$version)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_documento_modello_fondo_dati_utili WHERE  attivo=1 and editor_name=? AND version=? ORDER BY ordinamento ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $editor_name,$version);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }
    public static function getIdsArticoli($editor_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id_articolo FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $editor_name);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all();
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getSezioni($editor_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo WHERE editor_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $editor_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getSezioniUtilizzo($template_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_utilizzo WHERE editor_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $template_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getSezioniDatiUtili($template_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT sezione FROM DATE_documento_modello_fondo_dati_utili WHERE editor_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $template_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getFormulas($document_name)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT nome FROM DATE_formula WHERE formula_template_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $document_name);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();
        mysqli_close($mysqli);
        return $rows;
    }

    public static function edit_document_row($request)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "UPDATE DATE_documento_modello_fondo SET 
                               nome_articolo=?,
                               ordinamento=?,
                               preventivo=?
                               
WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sisi",
            $request['nome_articolo'],
            $request['ordinamento'],
            $request['preventivo'],
            $request['id']);
        $res = $stmt->execute();
        $mysqli->close();
        return $res;

    }

    public static function edit_utilizzo_document_row($request)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "UPDATE DATE_documento_modello_fondo_utilizzo SET 
                               nome_articolo=?,
                               ordinamento=?,
                               preventivo=?,
                               consuntivo=?
                               
WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sissi",
            $request['nome_articolo'],
            $request['ordinamento'],
            $request['preventivo'],
            $request['consuntivo'],
            $request['id_utilizzo']);
        $res = $stmt->execute();
        $mysqli->close();
        return $res;

    }

    public static function edit_dati_utili_document_row($request)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "UPDATE DATE_documento_modello_fondo_dati_utili SET 
                               nome_articolo=?,
                               ordinamento=?,
                               formula=?,
                               nota=?
                               
WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sissi",
            $request['nome_articolo'],
            $request['ordinamento'],
            $request['formula'],
            $request['nota'],
            $request['id_dati_utili']);
        $res = $stmt->execute();
        $mysqli->close();
        return $res;

    }

    public static function delete_document_row($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_modello_fondo SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id']);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public static function delete_utilizzo_row($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_modello_fondo_utilizzo SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id_utilizzo']);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public static function delete_dati_utili_row($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_modello_fondo_dati_utili SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id_dati_utili']);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public static function set_modello_document_not_editable($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_modello_fondo SET editable=0";
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        $sql = "INSERT INTO DATE_documento_modello_fondo_storico 
                    (ordinamento,sezione,sottosezione,nome_articolo,preventivo,document_name,anno,
                     attivo,editable)
                        SELECT  ordinamento,sezione,sottosezione,nome_articolo,preventivo,document_name,anno,
                     attivo,editable
FROM DATE_documento_modello_fondo WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        $sql = "DELETE FROM DATE_documento_modello_fondo WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();

        //per tabella documento fondo utilizzo
        $sql = "UPDATE DATE_documento_modello_fondo_utilizzo SET editable=0";
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        $sql = "INSERT INTO DATE_documento_modello_fondo_utilizzo_storico 
                    (ordinamento,sezione,nome_articolo,preventivo,consuntivo,document_name,anno,
                     attivo,editable)
                        SELECT  ordinamento,sezione,nome_articolo,preventivo,consuntivo,document_name,anno,
                     attivo,editable
FROM DATE_documento_modello_fondo_utilizzo WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        $sql = "DELETE FROM DATE_documento_modello_fondo_utilizzo WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        // per tabella documento dati utili
        $sql = "UPDATE DATE_documento_modello_fondo_dati_utili SET editable=0";
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        $sql = "INSERT INTO DATE_documento_modello_fondo_dati_utili_storico 
                    (ordinamento,sezione,sottosezione,nome_articolo,formula,document_name,anno,
                     attivo,editable)
                        SELECT  ordinamento,sezione,sottosezione,nome_articolo,formula,document_name,anno,
                     attivo,editable
FROM DATE_documento_modello_fondo_dati_utili WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        $sql = "DELETE FROM DATE_documento_modello_fondo_dati_utili WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        mysqli_close($mysqli);
        return $res;
    }

    public static function edit_modello_document_header($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_modello_fondo SET document_name=?, anno=? WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $request['document_name'], $request['anno'], $request['old_document_name']);
        $stmt->execute();
        $sql = "UPDATE DATE_documento_modello_fondo_utilizzo SET document_name=?, anno=? WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $request['document_name'], $request['anno'], $request['old_document_name']);
        $stmt->execute();
        $sql = "UPDATE DATE_documento_modello_fondo_dati_utili SET document_name=?, anno=? WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $request['document_name'], $request['anno'], $request['old_document_name']);
        $stmt->execute();
        $mysqli->close();
    }

    public static function create_new_row_costituzione($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_documento_modello_fondo 
                    (ordinamento,sezione,sottosezione,nome_articolo,preventivo,document_name,anno) VALUES(?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("isssssi", $request['ordinamento'], $request['sezione'], $request['sottosezione'], $request['nome_articolo'],
            $request['preventivo'], $request['document_name'], $request['anno']);
        $stmt->execute();
        $mysqli->close();

    }

    public static function create_new_row_utilizzo($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_documento_modello_fondo_utilizzo 
                    (ordinamento,sezione,nome_articolo,preventivo,consuntivo,document_name,anno) VALUES(?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("isssssi", $request['ordinamento'], $request['sezione'], $request['nome_articolo'],
            $request['preventivo'], $request['consuntivo'], $request['document_name'], $request['anno']);
        $stmt->execute();
        $mysqli->close();

    }

    public static function create_new_row_dati_utili($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_documento_modello_fondo_dati_utili
                    (ordinamento,sezione,sottosezione,nome_articolo,formula,nota,document_name,anno) VALUES(?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("issssssi", $request['ordinamento'], $request['sezione'], $request['sottosezione'], $request['nome_articolo'],
            $request['formula'], $request['nota'], $request['document_name'], $request['anno']);
        $stmt->execute();
        $mysqli->close();

    }
}
