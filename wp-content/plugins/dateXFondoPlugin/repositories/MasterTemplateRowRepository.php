<?php

namespace dateXFondoPlugin;

class MasterTemplateRowRepository
{
    public static function create_new_row($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
//TODO aggiungere descrizione articolo
        $sql = "INSERT INTO DATE_template_fondo (fondo,anno,descrizione_fondo,sezione,sottosezione,id_articolo,nome_articolo,descrizione_articolo,
                                 sottotitolo_articolo,nota,link,row_type,template_name) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sisssssssssss",
            $request["fondo"],
            $request["anno"],
            $request["descrizione_fondo"],
            $request["sezione"],
            $request["sottosezione"],
            $request["id_articolo"],
            $request["nome_articolo"],
            $request["descrizione_articolo"],
            $request["sottotitolo_articolo"],
            $request["nota"],
            $request["link"],
            $request["row_type"],
            $request["template_name"]);
        $res = $stmt->execute();
        $mysqli->close();
        return $stmt->insert_id;
    }

    public static function create_new_dec($request)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        //TODO aggiungere descrizione articolo

        $sql = "INSERT INTO DATE_template_fondo (fondo,
                                 anno,
                                 sezione,
                                 sottosezione,
                                 id_articolo,
                                 descrizione_fondo,nota,link,row_type,template_name) VALUES(?,?,?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sissssssss",
            $request['fondo'],
            $request['anno'],
            $request["sezione"],
            $request["sottosezione"],
            $request["id_articolo"],
            $request['descrizione_fondo'],
            $request['nome_articolo'],
            $request["link"],
            $request["row_type"],
            $request['template_name']);
        $res = $stmt->execute();
        $mysqli->close();
        return $stmt->insert_id;
    }

    public static function delete_row($request)
    {
        $input = (array)$request->get_body_params();
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_template_fondo SET attivo=0  WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request['id']);
        $res = $stmt->execute();
        $mysqli->close();

    }
}