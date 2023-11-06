<?php


function create_endpoint_datefondo_edit_row()
{

    register_rest_route('datexfondoplugin/v1', 'editrow', array(
        'methods' => 'POST',
        'callback' => 'esegui_modifica_riga'
    ));


}

function esegui_modifica_riga($params)
{
    $bool_res = (new dateXFondoPlugin\MasterTemplateRepository)->edit_row($params);
    $data = ['update' => $bool_res, 'message' => 'Modifica riga effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_edit_row');



function create_endpoint_datefondo_active_row()
{

    register_rest_route('datexfondoplugin/v1', 'activerow', array(
        'methods' => 'POST',
        'callback' => 'esegui_attiva_riga'
    ));


}

function esegui_attiva_riga($params)
{
    $bool_res = \dateXFondoPlugin\MasterTemplateRepository::active_row($params);
    $data = ['update' => $bool_res, 'message' => 'Riga attivata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_active_row');

function create_endpoint_datefondo_duplicate_template()
{

    register_rest_route('datexfondoplugin/v1', 'duplicatetemplate', array(
        'methods' => 'POST',
        'callback' => 'esegui_duplicazione_template'
    ));


}

function esegui_duplicazione_template($params)
{
    $bool_res = (new dateXFondoPlugin\MasterTemplateRepository)->duplicate_template($params);
    $data = ['duplicated template' => $bool_res, 'message' => 'TemplateFondo duplicato correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_duplicate_template');



