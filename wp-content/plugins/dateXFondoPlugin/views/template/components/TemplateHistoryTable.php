<?php

namespace dateXFondoPlugin;

class TemplateHistoryTable
{
    public static function render_scripts()
    {
        ?>
        <style>
            .btn-duplicate-template {
                color: #26282f;

            }

            .btn-duplicate-template:hover {
                color: #26282f;

            }

            .btn-all-template {
                color: #26282f;
            }

            .btn-all-template:hover {
                color: #26282f;
            }

        </style>
        <script>
            let fondo = '';
            let anno = 0;
            let descrizione = '';
            let template_name = '';
            let version = 0;

            function renderDataTableHistoryTemplate() {
                $('#dataTemplateTableBody').html('');

                articoli.forEach(art => {
                    $('#dataTemplateTableBody').append(`
                                 <tr>
                                       <td >${art.fondo}</td>
                                       <td >${art.anno}</td>
                                       <td >${art.descrizione_fondo}</td>
                                       <td >${art.version}</td>
                                       <td >${art.template_name}</td>
                                           <td>
                <button class="btn btn-link btn-visualize-template" data-fondo='${art.fondo}' data-anno='${art.anno}' data-desc ='${art.descrizione_fondo}' data-version='${art.version}' data-template='${art.template_name}' data-toggle="tooltip" title="Visualizza template"><i class="fa-regular fa-eye"></i></button>
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
                $('.btn-visualize-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/visualizza-template-fondo/?fondo=' + fondo + '&anno=' + anno + '&descrizione=' + descrizione + '&version=' + version + '&template_name=' + template_name + '&city=' + citySelected;
                });
                $('.btn-all-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/tabella-join-template-formula/?fondo=' + fondo + '&anno=' + anno + '&descrizione=' + descrizione + '&version=' + version + '&template_name=' + template_name + '&city=' + citySelected;
                });
            }

            $(document).ready(function () {

                renderDataTableHistoryTemplate();


            });
        </script>
    <?php }

    public static function render()
    {

        ?>
        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
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

        <?php
        self::render_scripts();
    }

}