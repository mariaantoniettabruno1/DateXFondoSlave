<?php

namespace dateXFondoPlugin;

use dateXFondoPlugin\MasterTemplateRepository;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: text/javascript');

class TemplateFondo
{
    public static function render()
    {

        $data = new MasterTemplateRepository();
        $results_articoli = [];

        if (isset($_GET['fondo']) && isset($_GET['anno']) && isset($_GET['descrizione']) && isset($_GET['version']) && isset($_GET['template_name']) ) {
            $results_articoli = $data->visualize_template($_GET['fondo'], $_GET['anno'], $_GET['descrizione'], $_GET['version'], $_GET['template_name'],$_GET['city']);

        } else {
            if (isset($_GET['template_name']) && isset($_GET['fondo']) && isset($_GET['version']))
                $results_articoli = $data->getArticoli($_GET['template_name'],$_GET['city'],$_GET['fondo'],$_GET['version']);
        }
        foreach ($results_articoli as $key => $value) {
            $results_articoli[$key]["sottotitolo_articolo"] = str_replace('"', '\"', $value["sottotitolo_articolo"]);
            $results_articoli[$key]["descrizione_articolo"] = str_replace('"', '\"', $value["descrizione_articolo"]);
        }




        ?>

        <!DOCTYPE html>

        <html lang="en">

        <head>
            <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
                    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
                    crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
                    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
                    crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
                  integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/main.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/mastertable.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/templateheader.css">

            <script>
                let articoli = JSON.parse((`<?=json_encode($results_articoli);?>`));
                let city ='<?=($_GET['city']);?>';

                console.log(articoli)

                const sezioni = {}
                articoli.forEach(a => {
                    if (!sezioni[a.sezione]) {
                        sezioni[a.sezione] = [];
                    }
                    if (!sezioni[a.sezione].includes(a.sottosezione)) {
                        sezioni[a.sezione].push(a.sottosezione);
                    }
                });
            </script>
        </head>

        <body>
        <div class="container-fluid">
            <div class="row pb-2">
                <?php
                TemplateHeader::render();
                ?>
            </div>

            <div class="row">
                <?php
                TemplateFondoTable::render();
                ?>
            </div>
        </div>
        </body>
        </html lang="en">

        <?php
    }


}