<?php

namespace dateXFondoPlugin;

use mysqli;

class CitiesRepository
{
    public function get_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,template_name FROM DATE_template_fondo  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public function get_history_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT DISTINCT fondo,anno,descrizione_fondo,editable,version,template_name FROM DATE_storico_template_fondo WHERE id_articolo IS NOT NULL and attivo=1  ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }

    public function get_row_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT * FROM DATE_template_fondo WHERE attivo = 0 and row_type='special' ORDER BY ordinamento ASC";
        $result = $mysqli->query($sql);
        $row = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $row;
    }
    public function get_city_user_data($params)
    {
        //cambiare il db name concatenando la stringa con il params che gli passo
        $url = DB_HOST . ":" . DB_PORT . "/";
        $username = DB_USER;
        $password = DB_PASSWORD;
        $dbname = 'c1date_custom';
        $mysqli = new mysqli($url, $username, $password, $dbname);
        $sql = "SELECT * FROM DATE_user_form";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_close($mysqli);
        return $rows;
    }

    public function get_city_document_data($params)
    {
        $document_repository = new \DocumentRepository();
        $documents = array_merge(
            array_map(function ($doc) {
                $doc['page'] = 'documento-modello-fondo';
                return $doc;
            }, $document_repository->getDataDocument('DATE_documento_modello_fondo_storico', $params['citySelected'])),
            array_map(function ($doc) {
                $doc['page'] = 'regioni_autonomie_locali_storico';

                return $doc;
            }, $document_repository->getDataDocument('DATE_documento_regioni_autonomie_locali_storico', $params['citySelected'])),
            $document_repository->getDataOdtDocument('DATE_documenti_odt_storico', $params['citySelected'])
        );

        return $documents;
    }

}