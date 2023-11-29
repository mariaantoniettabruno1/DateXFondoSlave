<?php

namespace dateXFondoPlugin;

class TemplateHistoryTable
{
    public static function render_scripts()
    {
        ?>
        <style>
            .btn-duplicate-template, .btn-create-template {
                color: #26282f;

            }

            .btn-duplicate-template:hover, .btn-create-template:hover {
                color: #26282f;

            }

            .btn-all-template {
                color: #26282f;
            }

            .btn-all-template:hover {
                color: #26282f;
            }

            #duplicateTemplateButton, #createTemplateButton {
                border-color: #26282f;
                background-color: #26282f;
            }

            #duplicateTemplateButton:hover, #createTemplateButton:hover {
                border-color: #870e12;
                background-color: #870e12;
            }
        </style>
        <script>
            let fondo = '';
            let anno = 0;
            let descrizione = '';
            let template_name = '';
            let version = 0;

            function renderFondoSelectForDuplicate() {
                $('#selectTemplateFondo').html('<option >Seleziona Fondo</option>');
                template_fondo.forEach(fondo => {
                    if (fondo.ufficiale === '1')
                        $('#selectTemplateFondo').append(`<option style="color: green">${fondo.fondo}, anno: ${fondo.anno}, versione: ${fondo.version}</option>`);
                    else
                        $('#selectTemplateFondo').append(`<option>${fondo.fondo}, anno: ${fondo.anno}, versione: ${fondo.version}</option>`);

                });
            }

            function renderDataTableHistoryTemplate() {
                $('#dataTemplateTableBody').html('');

                articoli.forEach(art => {
                    if(art.ufficiale === 0 || art.ufficiale === '0'){
                        art.ufficiale = ` <div class="text-center" style="padding-top:20px">
                                    <p>Non ufficiale</p>
                                </div>
                                    `;
                    }
                    else{
                        art.ufficiale = ` <div class="text-center" style="padding-top:20px">
                                    <p><b>Non ufficiale</b></p>
                                </div>
                                    `;
                    }
                    $('#dataTemplateTableBody').append(`
                                 <tr>
                                       <td >${art.ufficiale}</td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.fondo}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.anno}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.descrizione_fondo}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.version}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.template_name}</div></td>
                                           <td>
                <button class="btn btn-link btn-visualize-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-desc ='${art.descrizione_fondo}' data-version='${art.version}' data-template='${art.template_name}' data-toggle="tooltip" title="Visualizza template"><i class="fa-regular fa-eye"></i></button>
                <button class="btn btn-link btn-duplicate-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-descrizione ='${art.descrizione_fondo}' data-version='${art.version}' data-name='${art.template_name}' data-toggle="modal" data-target="#duplicateModal" data-toggle="tooltip" title="Duplica template"><i class="fa-regular fa-copy"></i></button>
                <button class="btn btn-link btn-create-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-descrizione ='${art.descrizione_fondo}' data-version='${art.version}' data-name='${art.template_name}' data-toggle="modal" data-target="#createModal" data-toggle="tooltip" title="Crea nuovo"><i class="fa-solid fa-plus"></i></button>
                <button class="btn btn-link btn-all-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-desc ='${art.descrizione_fondo}' data-version='${art.version}' data-template='${art.template_name}'>Fondo Completo <i class="fa-solid fa-chevron-right"></i></button>
                </td>
                </tr>
                             `);

                });

                $('.btn-visualize-template').click(function () {
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    descrizione = $(this).attr('data-desc');
                    version = $(this).attr('data-version');
                    template_name = $(this).attr('data-template');
                });
                $('.btn-all-template').click(function () {
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    descrizione = $(this).attr('data-desc');
                    version = $(this).attr('data-version');
                    template_name = $(this).attr('data-template');
                });
                $('.btn-duplicate-template').click(function () {
                    template_name = $(this).attr('data-name');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });
                $('.btn-create-template').click(function () {
                    template_name = $(this).attr('data-name');
                    fondo = $(this).attr('data-fondo');
                    anno = $(this).attr('data-anno');
                    version = $(this).attr('data-version');
                    descrizione = $(this).attr('data-descrizione');

                });
                $('.btn-visualize-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/visualizza-template-fondo/?fondo=' + fondo + '&anno=' + anno + '&descrizione=' + descrizione + '&version=' + version + '&template_name=' + template_name + '&city=' + citySelected;
                });
                $('.btn-all-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/tabella-join-template-formula/?fondo=' + fondo + '&anno=' + anno + '&descrizione=' + descrizione + '&version=' + version + '&template_name=' + template_name + '&city=' + citySelected;
                });
            }

            $(document).ready(function () {

                renderDataTableHistoryTemplate();
                renderFondoSelectForDuplicate();

                $('#duplicateTemplateButton').click(function () {

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
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/duplicatehistorytemplate',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            template_name = template_name + ' - duplicato';
                            console.log(template_name);
                            $("#duplicateModal").modal('hide');
                            location.href = '<?= DateXFondoCommon::get_website_url() ?>/visualizza-template-fondo/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo + '&version=' + version;
                        },
                        error: function (response) {
                            $("#duplicateModal").modal('hide');
                            console.error(response);
                        }
                    });
                });

                $('#createTemplateButton').click(function () {
                    //prendo il fondo selezionato dall'utente, eliminando dalla stringa l'anno
                    let fondo_dati = $('#selectTemplateFondo').val();

                    let nome_fondo = fondo_dati.split(",")[0];
                    let anno_fondo = fondo_dati.split(",")[1].split("anno: ")[1];
                    let versione_fondo = fondo_dati.split(",")[2].split("versione: ")[1];

                    const payload = {
                        fondo,
                        anno,
                        descrizione,
                        version,
                        template_name,
                        citySelected,
                        nome_fondo,
                        anno_fondo,
                        versione_fondo
                    }
                    console.log(payload)
                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/createhistorytemplate',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#createModal").modal('hide');
                            template_name = template_name + ' - nuovo';
                            console.log(template_name);
                            location.href = '<?= DateXFondoCommon::get_website_url() ?>/visualizza-template-fondo/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo + '&version=' + version;
                        },
                        error: function (response) {
                            console.error(response);
                            $("#createModal").modal('hide');
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
                <th style="width: 12.5rem">Template Ufficiale</th>
                <th style="width: 200px">Fondo</th>
                <th style="width: 100px">Anno</th>
                <th>Descrizione fondo</th>
                <th style="width: 100px">Versione</th>
                <th style="width: 100px">Template Name</th>
                <th style="width: 15rem">Azioni</th>
            </tr>

            </thead>
            <tbody id="dataTemplateTableBody">
            </tbody>
        </table>
        <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crea Template </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi creare questo template?
                    </div>
                    <div class="form-group" style="width: 90%;padding-left: 40px">
                        <label for="selectFondo"><b>Seleziona Fondo dal quale prendere i valori:</b></label>
                        <select class="custom-select" id="selectTemplateFondo">
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="createTemplateButton">Crea</button>
                    </div>
                </div>
            </div>
        </div>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="duplicateTemplateButton">Duplica</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        self::render_scripts();
    }

}