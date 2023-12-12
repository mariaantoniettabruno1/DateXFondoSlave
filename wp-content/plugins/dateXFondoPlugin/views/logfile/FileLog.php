<?php
namespace dateXFondoPlugin;

class FileLog
{
public static function render(){
    $file_path = 'wp-content/plugins/dateXFondoPlugin/log/logDatexFondo.log';

    // Verifica se il file esiste
    if (file_exists($file_path)) {
        // Leggi il contenuto del file di log
        $log_content = file_get_contents($file_path);


        // Visualizza o elabora il contenuto del file di log
        echo '<pre>';
        print_r($log_content);
        echo '<br>';
        echo '</pre>';

    } else {
        echo 'Il file di log non esiste o non è accessibile.';
    }
}
}