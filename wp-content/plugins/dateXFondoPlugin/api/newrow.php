<?php


function create_endpoint_datefondo_disattiva_riga()
{

    register_rest_route('datexfondoplugin/v1', 'delrow', array(
        'methods' => 'POST',
        'callback' => 'esegui_cancellazione_riga'
    ));


}

function esegui_cancellazione_riga($params)
{
     \dateXFondoPlugin\MasterTemplateRowRepository::delete_row($params);
    $data = [ 'message' => 'Riga cancellata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_disattiva_riga');