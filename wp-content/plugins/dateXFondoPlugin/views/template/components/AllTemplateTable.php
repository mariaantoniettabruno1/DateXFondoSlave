<?php

namespace dateXFondoPlugin;

class AllTemplateTable
{
    public static function render_scripts()
    {
        ?>
        <style>
            .btn-vis-templ, .btn-vis-templ:hover {
                color: #26282f;
            }


            .btn-visualize-complete-template, .btn-visualize-complete-template:hover {
                color: #26282f;
            }

            .btn-duplicate-template, .btn-duplicate-template:hover {
                color: #26282f;
            }

            #duplicateTemplateButton, #mainTemplateButton, #notMainTemplateButton {
                border-color: #26282f;
                background-color: #26282f;
            }

            #duplicateTemplateButton:hover, #mainTemplateButton:hover, #notMainTemplateButton:hover {
                border-color: #870e12;
                background-color: #870e12;
            }
        </style>
        <script>
            let template_name = '';
            let id_db = '';
            let check_variable = false;

            function renderFondoSelectForDuplicate() {
                $('#selectTemplateFondo').html('<option>Seleziona Fondo</option>');
                template_fondo.forEach(fondo => {
                    $('#selectTemplateFondo').append(`<option>${fondo.fondo} anno: ${fondo.anno} versione: ${fondo.versione}</option>`);
                });
            }

            function renderDataTableAllTemplate() {
                $('#dataAllTemplateTableBody').html('');
                id_db = Object.keys(articoli).length;
                articoli.forEach(art => {
                    let principale_radio = '';
                    if (art.principale === '0') {
                        principale_radio = `
                                   <button  id='${id_db}' type="button" disabled class="btn btn-outline-dark btn-main-template" data-id='${id_db}' data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="modal" data-target="#mainTemplateModal"> Non Principale</button>
`;
                    } else {
                        principale_radio = `
                            <button type="button" id='${id_db}' class="btn btn-success btn-not-main-template" data-id='${id_db}' data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="modal" data-target="#notMainTemplateModal">Principale</button>
                           `;
                        check_variable = true;
                    }
                    $('#dataAllTemplateTableBody').append(`
                                 <tr>
                                    <td>${principale_radio}</td>
                                        <td >${art.fondo}</td>
                                       <td >${art.anno}</td>
                                       <td >${art.descrizione_fondo}</td>
                                       <td >${art.template_name}</td>
                                       <td >${art.version}</td>
                                           <td>
                <button class="btn btn-link btn-vis-templ" data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="tooltip" title="Visualizza e modifica template"><i class="fa-solid fa-eye"></i></button>
                <button class="btn btn-link btn-duplicate-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-descrizione ='${art.descrizione_fondo}' data-version='${art.version}' data-name='${art.template_name}' data-toggle="modal" data-target="#duplicateModal" data-toggle="tooltip" title="Duplica template"><i class="fa-regular fa-copy"></i></button>
                <button class="btn btn-link btn-visualize-complete-template" data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}'  data-descrizione='${art.descrizione_fondo}'>Fondo Completo <i class="fa-solid fa-chevron-right"></i></button>
                </td>
                </tr>
                             `);
                    id_db--;
                });
                id_db = Object.keys(articoli).length;
                articoli.forEach(art => {
                    if (!check_variable) {
                        let checkButton = document.getElementById(id_db);
                        checkButton.disabled = false;
                        id_db--;
                    }
                });
                check_variable = false;

                $('.btn-main-template').click(function () {

                    template_name = $(this).attr('data-name');
                    id_db_articolo = $(this).attr('data-id');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });
                $('.btn-not-main-template').click(function () {
                    id_db_articolo = $(this).attr('data-id');
                    template_name = $(this).attr('data-name');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });

                $('.btn-duplicate-template').click(function () {
                    template_name = $(this).attr('data-name');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });

                $('.btn-vis-templ').click(function () {
                    template_name = $(this).attr('data-name');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });
                $('.btn-visualize-complete-template').click(function () {
                    template_name = $(this).attr('data-name');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });
                $('.btn-vis-templ').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/visualizza-template-fondo/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo + '&version=' + version;
                });
                $('.btn-visualize-complete-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/tabella-join-template-formula/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo + '&version=' + version;
                });
            }

            $(document).ready(function () {

                let check = 0;
                renderDataTableAllTemplate();
                renderFondoSelectForDuplicate();


                $('#mainTemplateButton').click(function () {
                    check = 1;
                    const payload = {
                        fondo,
                        anno,
                        descrizione,
                        version,
                        template_name,
                        citySelected,
                        check
                    }
                    console.log(payload)
                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/checkmaintmpl',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            $("#mainTemplateModal").modal('hide');
                            console.log(response);
                            //capire perchè non funziona
                            //getDataByCitySelected();

                        },
                        error: function (response) {
                            $("#mainTemplateModal").modal('hide');
                            console.error(response);
                        }
                    });
                })
                $('#notMainTemplateButton').click(function () {
                    check = 0;
                    const payload = {
                        fondo,
                        anno,
                        descrizione,
                        version,
                        template_name,
                        citySelected,
                        check
                    }
                    console.log(payload)
                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/checkmaintmpl',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            $("#notMainTemplateModal").modal('hide');
                            console.log(response);
                            location.reload();

                        },
                        error: function (response) {
                            $("#notMainTemplateModal").modal('hide');
                            console.error(response);
                        }
                    });
                })
                $('#duplicateTemplateButton').click(function () {
                    //prendo il fondo selezionato dall'utente, eliminando dalla stringa l'anno
                    let fondo_dati = $('#selectTemplateFondo').val();
                    fondo_dati = fondo_dati.split(",");
                    let nome_fondo = fondo_dati[0];
                    let anno_fondo = fondo_dati[1].split("anno:")[1];
                    //TODO aggiungere altri campi per la ricerca del fondo
                    let fondo_found = template_fondo.find(fondo => fondo.fondo === nome_fondo && fondo.anno === anno_fondo);
                    console.log("Ho stampato il fondo");
                    const payload = {
                        fondo,
                        anno,
                        descrizione,
                        version,
                        template_name,
                        citySelected
                    }
                    console.log(payload)
                    $.ajax({
                        // url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/duplicatetemplate',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#duplicateModal").modal('hide');
                            template_name = template_name + ' - ipotesi';
                            console.log(template_name);
                            // location.href = '<?= DateXFondoCommon::get_website_url() ?>/visualizza-template-fondo/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo + '&version=' + version;
                        },
                        error: function (response) {
                            console.error(response);
                            $("#duplicateModal").modal('hide');
                        }
                    });
                });

            });
        </script>
    <?php }

    public static function render()
    {
        ?>
        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
                <th style="width: 12.5rem">Template Principale</th>
                <th style="width: 12.5rem">Fondo</th>
                <th style="width: 6.25rem">Anno</th>
                <th>Descrizione fondo</th>
                <th style="width: 6.25rem">Nome Template</th>
                <th style="width: 6.25rem">Versione</th>
                <th style="width:15rem">Azioni</th>
            </tr>

            </thead>
            <tbody id="dataAllTemplateTableBody">
            </tbody>
        </table>
        <div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="duplicateModalLabel">Duplica Template </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi duplicare questo template?
                    </div>
                    <div class="form-group">
                        <label for="selectFondo"><b>Seleziona Fondo dal quale prendere i valori:</b></label>
                        <select class="custom-select" id="selectTemplateFondo">
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="duplicateTemplateButton">Duplica</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mainTemplateModal" tabindex="-1" role="dialog"
             aria-labelledby="mainTemplateModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mainTemplateModalLabel">Rendi template principale </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi rendere questo template il principale?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="mainTemplateButton">Conferma</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="notMainTemplateModal" tabindex="-1" role="dialog"
             aria-labelledby="notMainTemplateModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notMainTemplateModal">Cancella template da principale </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi che il template selezionato venga rimosso da quelli principali?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="notMainTemplateButton">Conferma</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        self::render_scripts();
    }
}