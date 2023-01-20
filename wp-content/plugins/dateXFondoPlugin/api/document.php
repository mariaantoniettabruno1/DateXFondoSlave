<?php
function create_endpoint_datefondo_edit_document_row()
{

    register_rest_route('datexfondoplugin/v1', 'document/row', array(
        'methods' => 'POST',
        'callback' => 'modifica_riga_documento'
    ));


}

function modifica_riga_documento($params)
{
    $bool_res = DocumentRepository::edit_document_row($params);
    $data = ['update' => $bool_res, 'message' => 'Modifica riga del documento effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_edit_document_row');

function create_endpoint_datefondo_edit_utilizzo_document_row()
{

    register_rest_route('datexfondoplugin/v1', 'utilizzo/document/row', array(
        'methods' => 'POST',
        'callback' => 'modifica_riga_documento_utilizzo'
    ));


}

function modifica_riga_documento_utilizzo($params)
{
    $bool_res = DocumentRepository::edit_utilizzo_document_row($params);
    $data = ['update' => $bool_res, 'message' => 'Modifica riga del documento "Utilizzo" effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_edit_utilizzo_document_row');

function create_endpoint_datefondo_edit_dati_utili_document_row()
{

    register_rest_route('datexfondoplugin/v1', 'datiutili/document/row', array(
        'methods' => 'POST',
        'callback' => 'modifica_riga_documento_dati_utili'
    ));


}

function modifica_riga_documento_dati_utili($params)
{
    $bool_res = DocumentRepository::edit_dati_utili_document_row($params);
    $data = ['update' => $bool_res, 'message' => 'Modifica riga del documento "Dati Utili" effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_edit_dati_utili_document_row');

function create_endpoint_datefondo_disattiva_riga_documento()
{

    register_rest_route('datexfondoplugin/v1', 'document/row/del', array(
        'methods' => 'POST',
        'callback' => 'esegui_cancellazione_riga_documento'
    ));


}

function esegui_cancellazione_riga_documento($params)
{
    DocumentRepository::delete_document_row($params);
    $data = ['message' => 'Riga cancellata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_disattiva_riga_documento');


function create_endpoint_datefondo_disattiva_riga_utilizzo()
{

    register_rest_route('datexfondoplugin/v1', 'document/utilizzo/row/del', array(
        'methods' => 'POST',
        'callback' => 'esegui_cancellazione_riga_documento_utilizzo'
    ));


}

function esegui_cancellazione_riga_documento_utilizzo($params)
{
    DocumentRepository::delete_utilizzo_row($params);
    $data = ['message' => 'Riga cancellata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_disattiva_riga_utilizzo');

function create_endpoint_datefondo_disattiva_riga_dati_utili()
{

    register_rest_route('datexfondoplugin/v1', 'document/datiutili/row/del', array(
        'methods' => 'POST',
        'callback' => 'esegui_cancellazione_riga_documento_dati_utili'
    ));


}

function esegui_cancellazione_riga_documento_dati_utili($params)
{
    DocumentRepository::delete_dati_utili_row($params);
    $data = ['message' => 'Riga cancellata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_disattiva_riga_dati_utili');

function create_endpoint_datefondo_edit_header_modello_document()
{

    register_rest_route('datexfondoplugin/v1', 'modellodocumentheader', array(
        'methods' => 'POST',
        'callback' => 'esegui_modifica_header_modello_document'
    ));


}

function esegui_modifica_header_modello_document($params)
{
    DocumentRepository::edit_modello_document_header($params);
    $data = ['message' => 'Modifica header documento effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_edit_header_modello_document');

function create_endpoint_datefondo_not_editable_modello_document()
{

    register_rest_route('datexfondoplugin/v1', 'disablemodellodocument', array(
        'methods' => 'POST',
        'callback' => 'esegui_blocca_modifica_modello_document'
    ));


}

function esegui_blocca_modifica_modello_document($params)
{
    $bool_res = DocumentRepository::set_modello_document_not_editable($params);
    $data = ['update' => $bool_res, 'message' => 'Blocco modifica documento modello andata a buon fine'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_not_editable_modello_document');

function create_endpoint_datefondo_creazione_riga_costituzione()
{

    register_rest_route('datexfondoplugin/v1', 'newcostrow', array(
        'methods' => 'POST',
        'callback' => 'esegui_creazione_riga_costituzione'
    ));


}

function esegui_creazione_riga_costituzione($params)
{
    $insert_id = DocumentRepository::create_new_row_costituzione($params);
    $data = ['id' => $insert_id, 'message' => 'Creazione riga costituzione effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_creazione_riga_costituzione');

function create_endpoint_datefondo_creazione_riga_utilizzo()
{

    register_rest_route('datexfondoplugin/v1', 'newutilizzorow', array(
        'methods' => 'POST',
        'callback' => 'esegui_creazione_riga_utilizzo'
    ));


}

function esegui_creazione_riga_utilizzo($params)
{
    $insert_id = DocumentRepository::create_new_row_utilizzo($params);
    $data = ['id' => $insert_id, 'message' => 'Creazione riga utilizzo effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_creazione_riga_utilizzo');

function create_endpoint_datefondo_creazione_riga_dati_utili()
{

    register_rest_route('datexfondoplugin/v1', 'newdatiutilirow', array(
        'methods' => 'POST',
        'callback' => 'esegui_creazione_riga_dati_utili'
    ));


}

function esegui_creazione_riga_dati_utili($params)
{
    $insert_id = DocumentRepository::create_new_row_dati_utili($params);
    $data = ['id' => $insert_id, 'message' => 'Creazione riga dati utili effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_creazione_riga_dati_utili');