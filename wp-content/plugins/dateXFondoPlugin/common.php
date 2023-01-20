<?php
namespace dateXFondoPlugin;
class DateXFondoCommon
{
    //Returns the url of the plugin's root folder
    public static function get_base_url()
    {
        return plugins_url('', __FILE__);
    }

    //Returns the physical path of the plugin's root folder
    public static function get_base_path()
    {
        return dirname(__FILE__);
    }
    public static function get_dir_path()
    {
        return plugin_dir_path(__FILE__);
    }

    public static function get_website_url(){
//        return sprintf(
//            "%s://%s",
//            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
//            $_SERVER['SERVER_NAME']
//        );
        return get_site_url();
    }
}