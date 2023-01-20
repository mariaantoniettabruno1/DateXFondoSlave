<?php

namespace dateXFondoPlugin;

use FormulaRepository;
use dateXFondoPlugin\FormulaSidebar;
use dateXFondoPlugin\FormulaCard;

header('Content-Type: text/javascript');

class Formula
{
    public static function render()
    {
        $data = new FormulaRepository();
        $result_articoli = $data->getArticoli();
        $result_formule = $data->getFormule();

        foreach ($result_formule as $key => $value){
            $result_formule[$key]["formula"] = str_replace('"','\"' ,$value["formula"]);
        }
        foreach ($result_articoli as $key => $value){
            $result_articoli[$key]["sottotitolo_articolo"] = str_replace('"','\"' ,$value["sottotitolo_articolo"]);
            //$result_articoli[$key]["descrizione"] = str_replace('"','\"' ,$value["descrizione"]);
        }
        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>
            <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
                  integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/formulacard.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/formulasidebar.css">

            <script>
                console.log((`<?=json_encode($result_formule);?>`));

                let articoli = JSON.parse((`<?=json_encode($result_articoli);?>`));
                let formule = JSON.parse((`<?=json_encode($result_formule);?>`));
                let sezioni = {}
                let owners = []
                articoli.forEach(a => {
                    if(!sezioni[a.sezione]){
                        sezioni[a.sezione] = [];
                    }
                    if(!sezioni[a.sezione].includes(a.sottosezione)){
                        sezioni[a.sezione].push(a.sottosezione);
                    }
                   if(!owners.includes(a.template_name)){
                       owners.push(a.template_name)
                   }
                })
            </script>
        </head>

        <body>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <?php
                        FormulaCard::render();
                        ?>
                        </div>
                    <div class="col-6">
                        <?php
                        FormulaSidebar::render();
                        ?>
                    </div>
                </div>
                <?php PreviewArticolo::render(); ?>

            </div>

        </body>
        </html lang="en">

        <?php


    }
}