<?php
function create_endpoint_datefondo_creazione_formula()
{

    register_rest_route('datexfondoplugin/v1', 'formula', array(
        'methods' => 'POST',
        'callback' => 'esegui_creazione_formula'
    ));
}

function esegui_creazione_formula($params)
{
    if (!isset($params['visibile'])) {
        $params['visibile'] = 1;
    }
    if(isset($params["id"]) && $params["id"] > 0 ) {
        $success = FormulaRepository::update_formula($params);
        if($success){
            $data = ['id' => $params["id"], 'updated' => true, 'message' => 'Formula aggiornata correttamente'];
            $response = new WP_REST_Response($data);
            $response->set_status(200);
            return $response;
        } else {
            $data = ['message' => 'Formula non aggiornata'];
            $response = new WP_REST_Response($data);
            $response->set_status(400);
            return $response;
        }

    } else {
        $insert_id = FormulaRepository::create_formula($params);
        if($insert_id){
            $data = ['id' => $insert_id, 'message' => 'Formula creata correttamente'];
            $response = new WP_REST_Response($data);
            $response->set_status(201);
            return $response;
        } else {
            $data = ['message' => 'Formula non creata'];
            $response = new WP_REST_Response($data);
            $response->set_status(400);
            return $response;
        }
    }
}

add_action('rest_api_init', 'create_endpoint_datefondo_creazione_formula');

function create_endpoint_datefondo_delete_formula()
{

    register_rest_route('datexfondoplugin/v1', 'deleteformula', array(
        'methods' => 'POST',
        'callback' => 'esegui_cancellazione_formula'
    ));


}

function esegui_cancellazione_formula($params)
{
    $bool_res = FormulaRepository::delete_formula($params);
    $data = ['deleted formula' => $bool_res, 'message' => 'Formula cancellata correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}

add_action('rest_api_init', 'create_endpoint_datefondo_delete_formula');