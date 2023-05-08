<?php

namespace dateXFondoPlugin;

use dateXFondoPlugin\MasterTemplateRepository;
use FondoCompletoTable;

header('Content-Type: text/javascript');

class FondoCompleto
{
    public static function render()
    {
        $data = new FondoCompletoTableRepository();

        $results_articoli = [];
        $results_formula = [];
        $results_joined = [];
        if (isset($_GET['city'])) {
            $city = $_GET['city'];
        } else {
            $city = '';
        }

        if (isset($_GET['fondo']) && isset($_GET['anno']) && isset($_GET['version']) && isset($_GET['template_name'])) {

            if (!isset($_GET['descrizione'])) {
                $descrizione = '';

            } else {
                $descrizione = $_GET['descrizione'];
            }

            $results_articoli = $data->getHistoryArticles($_GET['fondo'], $_GET['anno'], $descrizione, $_GET['version'], $_GET['template_name'], $city);
            $results_formula = $data->getHistoryFormulas($_GET['template_name'], $_GET['anno'], $city);
            $results_joined = $data->getHistoryJoinedRecords($_GET['anno'], $city);


        } else {

            if (isset($_GET['template_name'])) {

                $results_articoli = $data->getJoinedArticoli($_GET['template_name'], $_GET['version'], $_GET['fondo'], $city);
                $results_formula = $data->getJoinedFormulas($_GET['template_name'], $city);
                $results_joined = $data->getJoinedRecords($city);
            }
        }
        foreach ($results_formula as $key => $value) {
            $results_formula[$key]["descrizione"] = str_replace('"', '\"', $value["descrizione"]);
        }

        foreach ($results_articoli as $key => $value) {
            $results_articoli[$key]["sottotitolo_articolo"] = str_replace('"', '\"', $value["sottotitolo_articolo"]);
            $results_articoli[$key]["nome_articolo"] = str_replace('"', '\"', $value["nome_articolo"]);
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
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/jointable.css">

            <script>
                const articoli = JSON.parse(`<?=json_encode($results_articoli);?>`);
                const formulas = JSON.parse(`<?=json_encode($results_formula);?>`);
                const joined = JSON.parse(`<?=json_encode($results_joined);?>`);

                let city = '<?=($_GET['city']);?>';

                let anno = '';
                <?php if (isset($_GET['anno'])): ?>
                anno = <?=$_GET['anno'];?>
                <?php endif; ?>

                let joined_record = [
                    ...articoli,
                    ...formulas
                ];

                const joinedIndexes = {}
                joined.forEach(r => {
                    if (r.type === "0") {
                        joinedIndexes["T" + r.external_id] = r
                    } else {
                        joinedIndexes["F" + r.external_id] = r
                    }
                })


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
            <div class="row">
                <?php
                FondoCompletoTable::render();
                ?>

            </div>
        </div>
        </body>
        </html lang="en">
        <?php
    }
}