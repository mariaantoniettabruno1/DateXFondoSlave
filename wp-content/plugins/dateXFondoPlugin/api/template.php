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

function create_endpoint_datefondo_edit_template_header()
{

    register_rest_route('datexfondoplugin/v1', 'templateheader', array(
        'methods' => 'POST',
        'callback' => 'esegui_modifica_header'
    ));


}

function esegui_modifica_header($params)
{
    $bool_res = (new dateXFondoPlugin\MasterTemplateRepository)->edit_header($params);
    $data = ['update' => $bool_res, 'message' => 'Modifica header effettuata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_edit_template_header');



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

function create_endpoint_datefondo_duplicate_history_template()
{

    register_rest_route('datexfondoplugin/v1', 'duplicatehistorytemplate', array(
        'methods' => 'POST',
        'callback' => 'esegui_duplicazione_history_template'
    ));


}

function esegui_duplicazione_history_template($params)
{
    $bool_res = (new dateXFondoPlugin\MasterTemplateRepository)->duplicate_history_template($params);
    $data = ['duplicated template' => $bool_res, 'message' => 'TemplateFondo duplicato correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_duplicate_history_template');

function create_endpoint_datefondo_create_template()
{

    register_rest_route('datexfondoplugin/v1', 'createtemplate', array(
        'methods' => 'POST',
        'callback' => 'esegui_creazione_template'
    ));


}

function esegui_creazione_template($params)
{
    $bool_res = (new dateXFondoPlugin\MasterTemplateRepository)->create_template($params);
    $data = ['created template' => $bool_res, 'message' => 'TemplateFondo creato correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_create_template');

function create_endpoint_datefondo_create_history_template()
{

    register_rest_route('datexfondoplugin/v1', 'createhistorytemplate', array(
        'methods' => 'POST',
        'callback' => 'esegui_creazione_history_template'
    ));


}

function esegui_creazione_history_template($params)
{
    $bool_res = (new dateXFondoPlugin\MasterTemplateRepository)->create_history_template($params);
    $data = ['created template' => $bool_res, 'message' => 'TemplateFondo creato correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_create_history_template');

function create_endpoint_datefondo_official_template()
{

    register_rest_route('datexfondoplugin/v1', 'checkmaintmpl', array(
        'methods' => 'POST',
        'callback' => 'esegui_modifica_template_ufficiale'
    ));


}

function esegui_modifica_template_ufficiale($params)
{
    $bool_res = (new dateXFondoPlugin\TemplateRowRepository)->make_template_main($params);
    $data = ['main template' => $bool_res, 'message' => 'TemplateFondo modificato in ufficiale'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_official_template');



