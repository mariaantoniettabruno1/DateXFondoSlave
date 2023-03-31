<?php

namespace dateXFondoPlugin;


use DocumentRepository;
use ModelloFondoCostituzione;
use ModelloFondoDocumentTable;


class ModelloFondoDocument
{
    public static function render()
    {
        $data = new DocumentRepository();
    
            $results_articoli = $data->getHistoryArticoli($_GET['editor_name'],$_GET['version'],$_GET['city']);
            $results_articoli_utilizzo = $data->getHistoryArticoliUtilizzo($_GET['editor_name'],$_GET['version'],$_GET['city']);
            $results_articoli_dati_utili = $data->getHistoryArticoliDatiUtili($_GET['editor_name'],$_GET['version'],$_GET['city']);

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
            <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

        </head>

        <script>
            let articoli = JSON.parse((`<?=json_encode($results_articoli);?>`));
            let articoli_utilizzo = JSON.parse((`<?=json_encode($results_articoli_utilizzo);?>`));
            let articoli_dati_utili = JSON.parse((`<?=json_encode($results_articoli_dati_utili);?>`));
            const sezioni = []
            const sezioni_utilizzo = []
            const sezioni_dati_utili = []

            articoli.forEach(a => {
                if (!sezioni.includes(a.sezione)) {
                    sezioni.push(a.sezione);
                }
            });
            const sezioniList = {}
            articoli.forEach(a => {
                if (!sezioniList[a.sezione]) {
                    sezioniList[a.sezione] = [];
                }
                if (!sezioniList[a.sezione].includes(a.sottosezione)) {
                    sezioniList[a.sezione].push(a.sottosezione);
                }
            });
            articoli_utilizzo.forEach(a => {
                if (!sezioni_utilizzo.includes(a.sezione)) {
                    sezioni_utilizzo.push(a.sezione);
                }
            });
            const sezioniUtilizzoList = {}
            articoli_utilizzo.forEach(a => {
                if (!sezioniUtilizzoList[a.sezione]) {
                    sezioniUtilizzoList[a.sezione] = [];
                }
                if (!sezioniUtilizzoList[a.sezione].includes(a.sottosezione)) {
                    sezioniUtilizzoList[a.sezione].push(a.sottosezione);
                }
            });
            articoli_dati_utili.forEach(a => {
                if (!sezioni_dati_utili.includes(a.sezione)) {
                    sezioni_dati_utili.push(a.sezione);
                }
            });
            const sezioniDatiUtiliList = {}
            articoli_dati_utili.forEach(a => {
                if (!sezioniDatiUtiliList[a.sezione]) {
                    sezioniDatiUtiliList[a.sezione] = [];
                }
                if (!sezioniDatiUtiliList[a.sezione].includes(a.sottosezione)) {
                    sezioniDatiUtiliList[a.sezione].push(a.sottosezione);
                }
            });
            window.onbeforeunload = confirmExit;
            function confirmExit() {
                return "You have attempted to leave this page. Are you sure?";
            }
        </script>
        <body>
        <div class="container-fluid">

            <div class="row">
                <?php
                ModelloFondoCostituzione::render();
                ?>
            </div>
        </div>

        </body>
        </html lang="en">

        <?php
    }
}