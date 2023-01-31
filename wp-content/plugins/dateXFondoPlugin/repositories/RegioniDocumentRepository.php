<?php

namespace dateXFondoPlugin;

class RegioniDocumentRepository
{
    public static function getHistoryCostituzioneArticoli($template_name,$version)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_documento_regioni_autonomie_locali_storico WHERE titolo_tabella='Costituzione fondi per il trattamento accessorio' AND attivo=1 and editor_name=? and version=? ORDER BY ordinamento ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $template_name,$version);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getHistoryDestinazioneArticoli($template_name,$version)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_documento_regioni_autonomie_locali_storico WHERE titolo_tabella='Destinazione fondi per il trattamento accessorio'AND attivo=1 and editor_name=? and version=? ORDER BY ordinamento ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $template_name,$version);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];
        mysqli_close($mysqli);
        return $rows;
    }

    public static function edit_regioni_document_header($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_regioni_autonomie_locali SET editor_name=?, anno=?, document_name=? WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("siss", $request['editor_name'], $request['anno'], $request['document_name'], $request['old_document_name']);
        $stmt->execute();
        $mysqli->close();
    }

    public static function delete_regioni_row($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_regioni_autonomie_locali SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id']);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public static function edit_regioni_document($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        if ($request['id']) {
            $sql = "UPDATE DATE_documento_regioni_autonomie_locali SET 
                               nome_articolo=?,
                               ordinamento=?,
                               codice=?,
                               importo=?,                                                   
                               nota=?
                               
WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisssi",
                $request['nome_articolo'],
                $request['ordinamento'],
                $request['codice'],
                $request['importo'],
                $request['nota'],
                $request['id']);
            $res = $stmt->execute();
        } else {
            $sql = "UPDATE DATE_documento_regioni_autonomie_locali SET 
                               nome_articolo=?,
                               ordinamento=?,
                               codice=?,
                               importo=?,                                                   
                               nota=?
                               
WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisssi",
                $request['nome_articolo'],
                $request['ordinamento'],
                $request['codice'],
                $request['importo'],
                $request['nota'],
                $request['id_destinazione']);
            $res = $stmt->execute();
        }

        $mysqli->close();
        return $res;
    }

    public static function set_regioni_document_not_editable($request)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_documento_regioni_autonomie_locali SET editable=0";
        $stmt = $mysqli->prepare($sql);
        $res = $stmt->execute();
        $sql = "INSERT INTO DATE_documento_regioni_autonomie_locali_storico 
                    (ordinamento,titolo_documento,titolo_tabella,sezione,sottosezione,nome_articolo,codice,importo,nota,document_name,anno,
                     attivo,editable)
                        SELECT  ordinamento,titolo_documento,titolo_tabella,sezione,sottosezione,nome_articolo,codice,importo,nota,document_name,anno,
                     attivo,editable
FROM DATE_documento_regioni_autonomie_locali WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        $sql = "DELETE FROM DATE_documento_regioni_autonomie_locali WHERE document_name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $request['document_name']);
        $res = $stmt->execute();
        $mysqli->close();
        return $res;
    }

    public static function create_new_row_regioni($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO DATE_documento_regioni_autonomie_locali
                    (ordinamento,titolo_documento,titolo_tabella,sezione,sottosezione,nome_articolo,codice,importo,nota,document_name,anno) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("isssssssssi", $request['ordinamento'], $request['titolo_documento'], $request['titolo_tabella'], $request['sezione'], $request['sottosezione'], $request['nome_articolo'],
            $request['codice'], $request['importo'], $request['nota'], $request['document_name'], $request['anno']);
        $stmt->execute();
        $mysqli->close();
    }
}