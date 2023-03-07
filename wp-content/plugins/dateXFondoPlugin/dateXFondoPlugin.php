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
require_once(plugin_dir_path(__FILE__) . 'repositories/Connection.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/ConnectionFirstCity.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/MasterTemplateRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/TemplateRowRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/DisabledTemplateRow.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/DocumentRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/DeliberaDocumentRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/FormulaRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/RegioniDocumentRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/FondoCompletoTableRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/UserRepository.php');
require_once(plugin_dir_path(__FILE__) . 'repositories/CitiesRepository.php');
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
require_once(plugin_dir_path(__FILE__) . 'views/document/ModelloRegioniDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/ModelloRegioniHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/ModelloRegioniTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/ModelloRegioniCostituzioneTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/ModelloRegioniDestinazioneTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniStopEdit.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/MasterModelloRegioniCostituzioneRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/regioni/ModelloRegioniDestinazioneRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/ModelloFondoDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/ModelloFondoHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloStopEditTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoNewCostituzioneRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoNewUtilizzoRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/MasterModelloFondoDatiUtiliRow.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/ModelloFondoDocumentTable.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/ModelloFondoCostituzione.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/ModelloFondoDatiUtili.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/modellofondo/ModelloFondoUtilizzo.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/DeliberaIndirizziDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/DeterminaCostituzioneDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/RelazioneIllustrativaDocument.php');
require_once(plugin_dir_path(__FILE__) . 'views/document/components/delibera/DeliberaDocumentHeader.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/components/FormulaCard.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/components/FormulaSidebar.php');
require_once(plugin_dir_path(__FILE__) . 'views/formula/components/PreviewArticolo.php');
require_once(plugin_dir_path(__FILE__) . 'views/settings/UserSettings.php');
require_once(plugin_dir_path(__FILE__) . 'views/settings/components/UserSettingsForm.php');
require_once(plugin_dir_path(__FILE__) . 'api/formula.php');
require_once(plugin_dir_path(__FILE__) . 'api/document.php');
require_once(plugin_dir_path(__FILE__) . 'api/regionidocument.php');
require_once(plugin_dir_path(__FILE__) . 'api/deliberadocument.php');
require_once(plugin_dir_path(__FILE__) . 'api/template.php');
require_once(plugin_dir_path(__FILE__) . 'api/newrow.php');
require_once(plugin_dir_path(__FILE__) . 'api/user.php');
require_once(plugin_dir_path(__FILE__) . 'api/joinTable.php');
require_once(plugin_dir_path(__FILE__) . 'api/cities.php');


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
    add_shortcode('post_user_settings', 'slave_user_settings');
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


function document_template()
{
    \dateXFondoPlugin\ModelloFondoDocument::render();

}

function regioni_autonomie_locali_template()
{
    \dateXFondoPlugin\ModelloRegioniDocument::render();

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
    (new dateXFondoPlugin\RelazioneIllustrativaDocument)->render();


}

function document_table_template()
{
    $document = new \dateXFondoPlugin\DocumentHistory();
    $document->render();

}

function slave_user_settings()
{
    $document = new \dateXFondoPlugin\UserSettings();
    $document->render();
}


function admin_default_page()
{
    return DateXFondoCommon::get_website_url() . '/impostazioni-utente/';
}

add_filter('login_redirect', 'admin_default_page');


add_action('wp_head', 'my_get_current_user_roles');

function my_get_current_user_roles()
{

    if (is_user_logged_in()) {

        $user = wp_get_current_user();

        $roles = ( array )$user->roles;
        //return $roles; // This will returns an array, per cui per il value [0]
        return array_values($roles);

    } else {

        return array();

    }

}
function your_namespace() {
    wp_register_style('your_namespace', plugins_url('main.css','wp-content/plugins/dateXFondoPlugin/assets/styles/main.css' ));
    wp_enqueue_style('your_namespace');
}

add_action( 'admin_init','your_namespace');