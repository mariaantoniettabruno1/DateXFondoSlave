<?php

namespace dateXFondoPlugin;

use MongoDB\Driver\Exception\CommandException;
use mysqli;
use PHPMailer\PHPMailer\Exception;

class MasterTemplateRepository
{
    public static function getArticoli($template_name, $city, $fondo, $version)
    {
        if (isset($city) && $city != '') {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=? AND fondo=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $template_name, $fondo, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_template_fondo WHERE id_articolo IS NOT NULL and attivo=1 and template_name=? AND fondo=? AND version=? ORDER BY ordinamento ASC";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $template_name, $fondo, $version);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];

        }
        mysqli_close($mysqli);
        return $rows;
    }

    public static function getStoredArticoli()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,editable,version,template_name FROM DATE_storico_template_fondo WHERE id_articolo IS NOT NULL and attivo=1  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public static function getDisabledArticoli()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_template_fondo WHERE attivo = 0 and row_type='special' ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public static function getAllTemplate()
    {
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,template_name,version,principale FROM DATE_template_fondo  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }


    public function edit_row($request)
    {
        if ($request['city'] == '') {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();

            $sql = "UPDATE DATE_template_fondo SET valore=?,
                               valore_anno_precedente=?,
                               nota=?
WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssi",
                $request['valore'],
                $request['valore_anno_precedente'],
                $request['nota'],
                $request['id']);
            $res = $stmt->execute();
        } else {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $request['city'];
            $mysqli = new mysqli($url, $username, $password, $dbname);

            $sql = "UPDATE DATE_template_fondo SET valore=?,
                               valore_anno_precedente=?,
                               nota=?
WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssi",
                $request['valore'],
                $request['valore_anno_precedente'],
                $request['nota'],
                $request['id']);
            $res = $stmt->execute();
        }

        $mysqli->close();
        return $res;
    }


    public static function active_row($request)
    {
        if (!isset($request['citySelected']) || $request['citySelected'] == '') {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "UPDATE DATE_template_fondo SET attivo=1 WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
            $sql = "UPDATE DATE_storico_template_fondo SET attivo=1 WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisi", $request['fondo'], $request['anno'], $request['descrizione'], $request['version']);
            $res = $stmt->execute();
        } else {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $request['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "UPDATE DATE_template_fondo SET attivo=1 WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $request['id']);
            $res = $stmt->execute();
            $sql = "UPDATE DATE_storico_template_fondo SET attivo=1 WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisi", $request['fondo'], $request['anno'], $request['descrizione'], $request['version']);
            $res = $stmt->execute();
        }

        mysqli_close($mysqli);
        return $res;
    }


    public static function visualize_template($fondo, $anno, $descrizione, $version, $template_name, $city)
    {
        if (isset($city) && $city != '') {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT * FROM DATE_storico_template_fondo WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=? AND template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisis", $fondo, $anno, $descrizione, $version, $template_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT * FROM DATE_storico_template_fondo WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=? AND template_name=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisis", $fondo, $anno, $descrizione, $version, $template_name);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
        }

        mysqli_close($mysqli);
        return $rows;

    }

    public function duplicate_template($request)
    {
        if (isset($request['citySelected']) && $request['citySelected'] != '') {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $request['citySelected'];
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "SELECT fondo,anno,descrizione_fondo,ordinamento,id_articolo,sezione,sottosezione,
                     nome_articolo,descrizione_articolo,sottotitolo_articolo,nota,link,attivo,version,row_type,heredity,template_name,valore,valore_anno_precedente
FROM DATE_template_fondo WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=? AND template_name=? AND id_articolo IS NOT NULL AND attivo=1";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisis", $request['fondo'], $request['anno'], $request['descrizione'], $request['version'], $request['template_name']);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
            $sql = "INSERT INTO DATE_template_fondo 
                    (fondo,anno,descrizione_fondo,ordinamento,id_articolo,sezione,sottosezione,
                     nome_articolo,descrizione_articolo,sottotitolo_articolo,valore,valore_anno_precedente,nota,link,attivo,version,row_type,heredity,template_name) 
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $version = $rows[0]['version'];
            $template_name = $rows[0]['template_name'] . ' - ipotesi';
            foreach ($rows as $entry) {
                $stmt->bind_param("sisissssssssssiisis", $entry['fondo'], $entry['anno'], $entry['descrizione_fondo'], $entry['ordinamento'], $entry['id_articolo'],
                    $entry['sezione'], $entry['sottosezione'], $entry['nome_articolo'], $entry['descrizione_articolo'], $entry['sottotitolo_articolo'],$entry['valore'],$entry['valore_anno_precedente'],
                    $entry['nota'], $entry['link'], $entry['attivo'], $version, $entry['row_type'], $entry['heredity'], $template_name);
                $res = $stmt->execute();
            }

        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "SELECT fondo,anno,descrizione_fondo,ordinamento,id_articolo,sezione,sottosezione,
                     nome_articolo,descrizione_articolo,sottotitolo_articolo,nota,link,attivo,version,row_type,heredity,template_name,valore,valore_anno_precedente
FROM DATE_template_fondo WHERE fondo=? AND anno=? AND descrizione_fondo=? AND version=? AND template_name=? AND id_articolo IS NOT NULL AND attivo=1";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sisis", $request['fondo'], $request['anno'], $request['descrizione'], $request['version'], $request['template_name']);
            $res = $stmt->execute();
            if ($res = $stmt->get_result()) {
                $rows = $res->fetch_all(MYSQLI_ASSOC);
            } else
                $rows = [];
            $sql = "INSERT INTO DATE_template_fondo 
                    (fondo,anno,descrizione_fondo,ordinamento,id_articolo,sezione,sottosezione,
                     nome_articolo,descrizione_articolo,sottotitolo_articolo,valore,valore_anno_precedente,nota,link,attivo,version,row_type,heredity,template_name) 
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $version = $rows[0]['version'];
            $template_name = $rows[0]['template_name'] . ' - ipotesi';
            foreach ($rows as $entry) {
                $stmt->bind_param("sisissssssssssiisis", $entry['fondo'], $entry['anno'], $entry['descrizione_fondo'], $entry['ordinamento'], $entry['id_articolo'],
                    $entry['sezione'], $entry['sottosezione'], $entry['nome_articolo'], $entry['descrizione_articolo'], $entry['sottotitolo_articolo'],$entry['valore'],$entry['valore_anno_precedente'],
                    $entry['nota'], $entry['link'], $entry['attivo'], $version, $entry['row_type'], $entry['heredity'], $template_name);
                $res = $stmt->execute();
            }
        }
        mysqli_close($mysqli);

        $this->getTemplateFormulas($template_name, $request['anno'], $request['citySelected']);
        return array($res);
    }

    public function getTemplateFormulas($template_name, $year, $city)
    {

        if (isset($city) && $city != '') {
            $url = DB_HOST . ":" . DB_PORT . "/";
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = 'c1date_' . $city;
            $mysqli = new mysqli($url, $username, $password, $dbname);
            $sql = "INSERT INTO DATE_formula 
                    (sezione,sottosezione,nome,descrizione,condizione,formula,visibile,formula_template_name,text_type,anno)
                        SELECT  sezione,sottosezione,nome,descrizione,condizione,formula,visibile,formula_template_name,text_type,anno 
FROM DATE_formula WHERE formula_template_name=? AND anno=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $year);
            $res = $stmt->execute();
        } else {
            $conn = new ConnectionFirstCity();
            $mysqli = $conn->connect();
            $sql = "INSERT INTO DATE_formula 
                    (sezione,sottosezione,nome,descrizione,condizione,formula,visibile,formula_template_name,text_type,anno)
                        SELECT  sezione,sottosezione,nome,descrizione,condizione,formula,visibile,formula_template_name,text_type,anno 
FROM DATE_formula WHERE formula_template_name=? AND anno=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $template_name, $year);
            $res = $stmt->execute();
        }
        mysqli_close($mysqli);
        return $res;
    }

    public function duplicateValuesOnTemplate($fondo, $year, $version, $citySelected, $template_name, $fondo_template, $anno_template, $version_template)
    {
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_' . $citySelected;
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT id_articolo,nome_articolo,valore,valore_anno_precedente
FROM DATE_template_fondo WHERE fondo=? AND anno=? AND version=? AND id_articolo IS NOT NULL";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sii", $fondo, $year, $version);
        $res = $stmt->execute();
        if ($res = $stmt->get_result()) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        } else
            $rows = [];

        $sql = 'UPDATE DATE_template_fondo SET valore=?, valore_anno_precedente=? WHERE id_articolo=? AND nome_articolo=? AND fondo=? AND anno=? AND version=? AND template_name=?';
        $stmt = $mysqli->prepare($sql);
        foreach ($rows as $entry) {
            print_r($entry['valore']);
            print_r($entry['valore_anno_precedente']);
            $stmt->bind_param("sssssiis",
                $entry['valore'],
                $entry['valore_anno_precedente'],
                $entry['id_articolo'],
                $entry['nome_articolo'],
                $fondo_template,
                $anno_template,
                $version_template,
                $template_name);
            $res = $stmt->execute();
        }
        $mysqli->close();
        return $res;
    }


}