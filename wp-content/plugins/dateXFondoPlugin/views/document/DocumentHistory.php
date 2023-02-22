<?php

namespace dateXFondoPlugin;

use AllDocumentTable;

class DocumentHistory
{
    private $documents = [];
    private $allDocuments;


    public function __construct()
    {
        $document_repository = new \DocumentRepository();
        $this->documents = array_merge(
            array_map(function ($doc) {
                $doc['page'] = 'documento-modello-fondo';
                return $doc;
            }, $document_repository->getDataDocument('DATE_documento_modello_fondo_storico')),
            array_map(function ($doc) {
                $doc['page'] = 'regioni_autonomie_locali_storico';

                return $doc;
            }, $document_repository->getDataDocument('DATE_documento_regioni_autonomie_locali_storico')),
            $document_repository->getDataOdtDocument('DATE_documenti_odt_storico')
        );

        $this->allDocuments = new AllDocumentTable($this->documents);

    }


    public function render()
    {

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
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/alltemplate.css">

            <script>
            </script>
        </head>

        <body>
        <div class="container-fluid">
            <?php if(my_get_current_user_roles()[0]=='subscriber'): ?>
                <div class="row pb-3" style="width: 20%">

                        <label>Seleziona comune per visualizzare i suoi dati:</label>

                        <select name="comune" id="idComune">
                            <option>Torino</option>
                            <option>Ivrea</option>
                        </select>

                </div>
            <?php endif; ?>
            <div class="row">
                <?php
                $this->allDocuments->render();
                ?>
            </div>

        </body>
        </html lang="en">

        <?php
    }
}