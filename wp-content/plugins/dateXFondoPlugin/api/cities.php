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

function create_endpoint_datefondo_get_history_city_data()
{

    register_rest_route('datexfondoplugin/v1', 'cityhistorydata', array(
        'methods' => 'POST',
        'callback' => 'get_history_city_data'
    ));


}

function get_history_city_data($params)
{
   $data =  (new dateXFondoPlugin\CitiesRepository)->get_history_data($params);
    $data = ['data'=> $data, 'message' => 'Operazione andata a buon fine'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_get_history_city_data');

function create_endpoint_datefondo_get_row_city_data()
{

    register_rest_route('datexfondoplugin/v1', 'cityrow', array(
        'methods' => 'POST',
        'callback' => 'get_row_city_data'
    ));


}

function get_row_city_data($params)
{
   $data =  (new dateXFondoPlugin\CitiesRepository)->get_row_data($params);
    $data = ['data'=> $data, 'message' => 'Operazione andata a buon fine'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_get_row_city_data');

function create_endpoint_datefondo_get_city_documents()
{

    register_rest_route('datexfondoplugin/v1', 'citydocuments', array(
        'methods' => 'POST',
        'callback' => 'get_city_documents'
    ));


}

function get_city_documents($params)
{
   $data =  (new dateXFondoPlugin\CitiesRepository)->get_city_document_data($params);
    $data = ['data'=> $data, 'message' => 'Operazione andata a buon fine'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_get_city_documents');