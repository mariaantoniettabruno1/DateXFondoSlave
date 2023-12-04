<?php
function create_endpoint_datefondo_update_user_settings(){

     register_rest_route('datexfondoplugin/v1', 'usersettings', array(
         'methods' => 'POST',
         'callback' => 'esegui_update_user_settings'
     ));
}
function esegui_update_user_settings($params){
    $bool_res = (new dateXFondoPlugin\UserRepository)->update_user_settings($params);
    $data = ['user settings updated' => $bool_res, 'message' => 'Impostazioni modificate correttamente'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}
add_action('rest_api_init', 'create_endpoint_datefondo_update_user_settings');

/*function create_endpoint_datefondo_check_new_template(){

     register_rest_route('datexfondoplugin/v1', 'cityusertemplate', array(
         'methods' => 'POST',
         'callback' => 'esegui_check_new_template'
     ));
}
function esegui_check_new_template($params){
    $bool_res = (new dateXFondoPlugin\UserRepository)->check_new_template($params);
    $data = ['check done' => $bool_res, 'message' => 'Nuovo template trovato'];
    $response = new WP_REST_Response($data);
    $response->set_status(201);
    return $response;
}
add_action('rest_api_init', 'create_endpoint_datefondo_check_new_template');*/