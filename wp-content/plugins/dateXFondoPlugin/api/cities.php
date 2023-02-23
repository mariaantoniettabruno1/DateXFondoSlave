<?php

use dateXFondoPlugin\CitiesRepository;

function create_endpoint_datefondo_get_city_data()
{

    register_rest_route('datexfondoplugin/v1', 'citydata', array(
        'methods' => 'POST',
        'callback' => 'get_city_data'
    ));


}

function get_city_data($params)
{
   $data =  (new dateXFondoPlugin\CitiesRepository)->get_data($params);
    $data = ['data'=> $data, 'message' => 'Operazione andata a buon fine'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_get_city_data');