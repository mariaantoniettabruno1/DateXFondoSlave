<?php
/***
 * Plugin Name: dateXFondo Plugin
 * Plugin URI:
 * Description:
 * Version: 0.1
 * Author: MG3
 * Author URI:
 */

use dateXFondoPlugin\DateXFondoCommon;

require_once(plugin_dir_path(__FILE__) . 'common.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/CustomTable.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/Connection.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/MasterTemplateRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/MasterTemplateRowRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/DisabledTemplateRow.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/DocumentRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/DeliberaDocumentRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/FormulaRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/RegioniDocumentRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/SlaveFormulaTable.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/MasterJoinTableRepository.php');
require_once(plugin_dir_path(__FILE__) . 'views/table/live_edit.php');
require_once(plugin_dir_path(__FILE__) . 'views/table/FondoCompleto.php');
require_once(plugin_dir_path(__FILE__) . 'views/table/components/FondoCompletoTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/TemplateFondo.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/AllTemplate.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/TemplateToActive.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/TemplateHistory.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/components/TemplateHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/components/TemplateFondoTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/components/AllTemplateTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/components/TemplateHistoryTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/components/TemplateFondoToActiveRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/template/ShortCodeDisabledTemplateRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/Formula.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/SlaveShortCodeFormulaTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/AllDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/DocumentHistory.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/AllDocumentTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/MasterModelloRegioniDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniCostituzioneTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniDestinazioneTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniStopEdit.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniCostituzioneRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/ModelloRegioniDestinazioneRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/MasterModelloFondoDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloStopEditTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoNewCostituzioneRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoNewUtilizzoRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoDatiUtiliRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoDocumentTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoCostituzione.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoDatiUtili.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoUtilizzo.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/DeliberaIndirizziDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/DeterminaCostituzioneDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/RelazioneIllustrativaDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/delibera/DeliberaDocumentHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/components/FormulaCard.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/components/FormulaSidebar.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/components/PreviewArticolo.php');
require_once(plugin_dir_path(__FILE__) . 'api/formula.php');
require_once(plugin_dir_path(__FILE__) . 'api/document.php');
require_once(plugin_dir_path(__FILE__) . 'api/regionidocument.php');
require_once(plugin_dir_path(__FILE__) . 'api/deliberadocument.php');
require_once(plugin_dir_path(__FILE__) . 'api/template.php');
require_once(plugin_dir_path(__FILE__) . 'api/newrow.php');
require_once(plugin_dir_path(__FILE__) . 'api/joinTable.php');


/**
 * Aggiungo librerie javascript a wordpress
 */


function custom_scripts_method()
{
    wp_register_script('customscripts', DateXFondoCommon::get_base_url() . '/libs/jquery.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('customscripts');
}

/**
 * Action per l'inizializzazione di tutte le function collegate agli shortcode del plugin
 */


add_action('init', 'shortcodes_init');

function shortcodes_init()
{
    add_shortcode('post_join_table', 'visualize_join_table');
    add_shortcode('post_visualize_master_template', 'visualize_master_template');
    add_shortcode('post_visualize_master_all_template', 'visualize_master_all_template');
    add_shortcode('post_visualize_history_template', 'visualize_history_template');
    add_shortcode('post_visualize_disabled_template_row', 'visualize_disabled_template_row');
    add_shortcode('post_visualize_formula_template', 'visualize_formula_template');
    add_shortcode('post_document_template', 'document_template');
    add_shortcode('post_document_table_template', 'document_table_template');
    add_shortcode('post_regioni_autonomie_locali_template', 'regioni_autonomie_locali_template');
    add_shortcode('post_delibera_template', 'delibera_template');
    add_shortcode('post_determina_costituzione_template', 'determina_costituzione_template');
    add_shortcode('post_relazione_illustrativa_template', 'relazione_illustrativa_template');
}



function visualize_join_table()
{
    \dateXFondoPlugin\FondoCompleto::render();
}



function visualize_master_all_template()
{
    \dateXFondoPlugin\AllTemplate::render();

}

function visualize_master_template()
{
    \dateXFondoPlugin\TemplateFondo::render();

}

function visualize_history_template()
{
    \dateXFondoPlugin\TemplateHistory::render();

}

function visualize_disabled_template_row()
{
    \dateXFondoPlugin\TemplateToActive::render();

}

function visualize_formula_template()
{
    \dateXFondoPlugin\Formula::render();
}

function visualize_slave_formula_template()
{
    \dateXFondoPlugin\SlaveShortCodeFormulaTable::visualize_slave_formula_template();

}

function document_template()
{
    \dateXFondoPlugin\MasterModelloFondoDocument::render();

}

function regioni_autonomie_locali_template()
{
    \dateXFondoPlugin\MasterModelloRegioniDocument::render();

}

function delibera_template()
{
    $document = new \dateXFondoPlugin\DeliberaIndirizziDocument();
    $document->render();

}
function determina_costituzione_template()
{
    $document = new \dateXFondoPlugin\DeterminaCostituzioneDocument();
   $document->render();

}
function relazione_illustrativa_template()
{
    $document = new \dateXFondoPlugin\RelazioneIllustrativaDocument();
    $document->render();

}
function document_table_template()
{
    $document = new \dateXFondoPlugin\AllDocument();
    $document->render();

}


//route ed endpoint per far funzionare la modifica campi della table contenente i dati dell'anno corrente per il master
//function create_endpoint_datefondo()
//{
//
//    register_rest_route('datexfondoplugin/v1', 'table/edit', array(
//        'methods' => 'POST',
//        'callback' => 'esegui_modifica_campi'
//    ));
//
//
//}
//
//function esegui_modifica_campi($params)
//{
//    \dateXFondoPlugin\modifica_campi($params);
//    $data = ['params' => $params, 'message' => 'Endpoint di edit'];
//    $response = new WP_REST_Response($data);
//    $response->set_status(200);
//    return $response;
//}
//
//add_action('rest_api_init', 'create_endpoint_datefondo');
//
////route ed endpoint per far funzionare la modifica campi della table contenente i dati dell'anno corrente per lo svale
//function create_endpoint_datefondo_slave()
//{
//
//    register_rest_route('datexfondoplugin/v1', 'table/editslave', array(
//        'methods' => 'POST',
//        'callback' => 'esegui_modifica_campi_slave'
//    ));
//
//
//}
//
//function esegui_modifica_campi_slave($params)
//{
//    \dateXFondoPlugin\modifica_campi_slave($params);
//    $data = ['params' => $params, 'message' => 'Endpoint di edit per lo slave'];
//    $response = new WP_REST_Response($data);
//    $response->set_status(200);
//    return $response;
//}
//
//add_action('rest_api_init', 'create_endpoint_datefondo_slave');
//
////route ed endpoint per far funzionare la modifica campi del table template che viene duplicato in fase di creazione di un nuovo fondo
//function create_endpoint_datefondo_nuovo()
//{
//
//    register_rest_route('datexfondoplugin/v1', 'table/editnewfondo', array(
//        'methods' => 'POST',
//        'callback' => 'esegui_modifica_campi_nuovo_template'
//    ));
//
//
//}
//
//function esegui_modifica_campi_nuovo_template($params)
//{
//    return \dateXFondoPlugin\modifica_campi_nuovo_template($params);
//}
//
//add_action('rest_api_init', 'create_endpoint_datefondo_nuovo');
//
//
//function create_endpoint_datefondo_attiva_riga()
//{
//
//    register_rest_route('datexfondoplugin/v1', 'table/enablerow', array(
//        'methods' => 'POST',
//        'callback' => 'esegui_abilitazione_riga'
//    ));
//
//
//}
//
//function esegui_abilitazione_riga($params)
//{
//    return \dateXFondoPlugin\abilita_riga($params);
//}
//
//add_action('rest_api_init', 'create_endpoint_datefondo_attiva_riga');
//
//function create_endpoint_datefondo_ereditarieta_nota_valore()
//{
//
//    register_rest_route('datexfondoplugin/v1', 'table/heredity', array(
//        'methods' => 'POST',
//        'callback' => 'esegui_eredita_nota_valore'
//    ));
//
//
//}
//
//function esegui_eredita_nota_valore($params)
//{
//    return \dateXFondoPlugin\eredita_nome_valore($params);
//}
//
//add_action('rest_api_init', 'create_endpoint_datefondo_ereditarieta_nota_valore');




