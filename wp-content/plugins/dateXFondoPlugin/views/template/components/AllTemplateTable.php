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

            .btn-create-template, .btn-create-template:hover {
                color: #26282f;
            }

            #duplicateTemplateButton, #mainTemplateButton, #notMainTemplateButton, #createTemplateButton {
                border-color: #26282f;
                background-color: #26282f;
            }

            #duplicateTemplateButton:hover, #mainTemplateButton:hover, #notMainTemplateButton:hover, #createTemplateButton:hover {
                border-color: #870e12;
                background-color: #870e12;
            }
            .btn-not-main-template {
                width:65%;
            }
        </style>
        <script>
            let template_name = '';
            let id_db = '';
            let check_variable = false;

            function renderFondoSelectForDuplicate() {
                $('#selectTemplateFondo').html('<option >Seleziona Fondo</option>');
                template_fondo.forEach(fondo => {
                    if (fondo.ufficiale === '1' || fondo.ufficiale === 1)
                        $('#selectTemplateFondo').append(`<option style='color:green'>${fondo.fondo}, anno: ${fondo.anno}, versione: ${fondo.version}</option>`);
                    else
                        $('#selectTemplateFondo').append(`<option>${fondo.fondo}, anno: ${fondo.anno}, versione: ${fondo.version}</option>`);
                });
            }

            function renderDataTableAllTemplate() {
                $('#dataAllTemplateTableBody').html('');
                id_db = Object.keys(articoli).length;
                articoli.forEach(art => {
                    let ufficiale_button = '';
                    if (art.ufficiale === '0' || art.ufficiale === 0) {
                        ufficiale_button = ` <div class="text-center" style="padding-top:20px">
                                   <button  id='${id_db}' type="button" disabled class="btn btn-outline-dark btn-main-template" data-id='${id_db}' data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="modal" data-target="#mainTemplateModal"> Non Ufficiale</button>
                                   </div>
                                    `;
                    } else {
                        ufficiale_button = `
                            <div class="text-center" style="padding-top:20px;">
                            <button type="button" id='${id_db}' class="btn btn-success btn-not-main-template" data-id='${id_db}' data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="modal" data-target="#notMainTemplateModal">Ufficiale</button>
                           </div>
                           `;
                        check_variable = true;
                    }
                    $('#dataAllTemplateTableBody').append(`
                                 <tr>
                                    <td>${ufficiale_button}</td>
                                        <td ><div class="text-center" style="padding-top:20px">${art.fondo}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.anno}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.descrizione_fondo}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.template_name}</div></td>
                                       <td ><div class="text-center" style="padding-top:20px">${art.version}</div></td>
                                           <td>
                <button class="btn btn-link btn-vis-templ" data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="tooltip" title="Visualizza e modifica template"><i class="fa-solid fa-eye"></i></button>
                <button class="btn btn-link btn-duplicate-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-descrizione ='${art.descrizione_fondo}' data-version='${art.version}' data-name='${art.template_name}' data-toggle="modal" data-target="#duplicateModal" data-toggle="tooltip" title="Duplica template"><i class="fa-regular fa-copy"></i></button>
                <button class="btn btn-link btn-create-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-descrizione ='${art.descrizione_fondo}' data-version='${art.version}' data-name='${art.template_name}' data-toggle="modal" data-target="#createModal" data-toggle="tooltip" title="Crea nuovo"><i class="fa-solid fa-plus"></i></button>
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
                $('.btn-create-template').click(function () {
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

                            articoli.find(art => {
                                if (art.fondo === fondo && art.anno === anno && art.descrizione_fondo === descrizione && art.version === version && art.template_name === template_name) {
                                    art.ufficiale = 1;
                                }
                            })
                            template_fondo.find(art => {
                                if (art.fondo === fondo && art.anno === anno && art.descrizione_fondo === descrizione && art.version === version && art.template_name === template_name) {
                                    art.ufficiale = 1;
                                    console.log(art)
                                }
                            })
                            renderDataTableAllTemplate();
                            renderFondoSelectForDuplicate();


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
                            articoli.find(art => {
                                if (art.fondo === fondo && art.anno === anno && art.descrizione_fondo === descrizione && art.version === version && art.template_name === template_name) {
                                    art.ufficiale = 0;
                                }
                            })
                            template_fondo.find(art => {
                                if (art.fondo === fondo && art.anno === anno && art.descrizione_fondo === descrizione && art.version === version && art.template_name === template_name) {
                                    art.ufficiale = 0;
                                }
                            })
                            renderDataTableAllTemplate();
                            renderFondoSelectForDuplicate();
                        },
                        error: function (response) {
                            $("#notMainTemplateModal").modal('hide');
                            console.error(response);
                        }
                    });
                })
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
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/duplicatetemplate',
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
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/createtemplate',
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
        <div class="modal fade" id="mainTemplateModal" tabindex="-1" role="dialog"
             aria-labelledby="mainTemplateModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mainTemplateModalLabel">Rendi template ufficiale </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi rendere questo template il ufficiale?
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
                        <h5 class="modal-title" id="notMainTemplateModal">Cancella template da ufficiale </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi che il template selezionato venga rimosso da quelli ufficiali?
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