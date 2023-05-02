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
        </style>
        <script>
            let template_name = '';

            function renderDataTableAllTemplate() {
                $('#dataAllTemplateTableBody').html('');
                console.log(articoli);
                articoli.forEach(art => {
                    $('#dataAllTemplateTableBody').append(`
                                 <tr>
                                       <td >${art.fondo}</td>
                                       <td >${art.anno}</td>
                                       <td >${art.descrizione_fondo}</td>
                                       <td >${art.template_name}</td>
                                       <td >${art.version}</td>
                                           <td>
                <button class="btn btn-link btn-vis-templ" data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}' data-descrizione='${art.descrizione_fondo}' data-toggle="tooltip" title="Visualizza e modifica template"><i class="fa-solid fa-eye"></i></button>
                <button class="btn btn-link btn-visualize-complete-template" data-name='${art.template_name}' data-fondo='${art.fondo}' data-anno='${art.anno}' data-version='${art.version}'  data-descrizione='${art.descrizione_fondo}'>Fondo Completo <i class="fa-solid fa-chevron-right"></i></button>
                </td>
                </tr>
                             `);

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
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/visualizza-template-fondo/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo + '&version=' + version ;
                });
                $('.btn-visualize-complete-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/tabella-join-template-formula/?template_name=' + template_name + '&city=' + citySelected + '&fondo=' + fondo;
                });
            }

            $(document).ready(function () {

                renderDataTableAllTemplate();

            });
        </script>
    <?php }

    public static function render()
    {
        ?>
        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
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
        <?php
        self::render_scripts();
    }
}