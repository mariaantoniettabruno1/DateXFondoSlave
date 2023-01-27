<?php
function create_endpoint_datefondo_aggiornamento_join_table()
{

    register_rest_route('datexfondoplugin/v1', 'join-table', array(
        'methods' => 'POST',
        'callback' => 'esegui_modifica_ordinamento'
    ));
}

function esegui_modifica_ordinamento($params)
{
    if(isset($params["id"]) && $params["id"] > 0 ) {
        $success = \dateXFondoPlugin\FondoCompletoTableRepository::updateJoinedIndex($params["id"], $params["ordinamento"]);
        if($success){
            $data = ['id' => $params["id"], 'updated' => true, "affectedRows" => $success, 'message' => 'Ordinamento aggiornato correttamente'];
            $response = new WP_REST_Response($data);
            $response->set_status(200);
            return $response;
        } else {
            $data = ['message' => 'Ordinamento non aggiornato', "affectedRows" => $success,];
            $response = new WP_REST_Response($data);
            $response->set_status(400);
            return $response;
        }

    } else {
        $insert_id = \dateXFondoPlugin\FondoCompletoTableRepository::insertJoinedIndex($params["external_id"], $params["type"], $params["ordinamento"]);
        if($insert_id){
            $data = ['id' => $insert_id, 'message' => 'Ordinamento inserito correttamente'];
            $response = new WP_REST_Response($data);
            $response->set_status(201);
            return $response;
        } else {
            $data = ['message' => 'Ordinamento non inserito'];
            $response = new WP_REST_Response($data);
            $response->set_status(400);
            return $response;
        }
    }
}

add_action('rest_api_init', 'create_endpoint_datefondo_aggiornamento_join_table');
