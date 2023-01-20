<?php

namespace dateXFondoPlugin;

class FormulaAllTemplateTable
{
    public static function render_scripts()
    {
        ?>
 
        <script>
            let template_name = '';

            function renderDataTable() {
                $('#dataAllTemplateTableBody').html('');

                articoli.forEach(art => {
                    $('#dataAllTemplateTableBody').append(`
                                 <tr>
                                       <td >${art.fondo}</td>
                                       <td >${art.anno}</td>
                                       <td >${art.descrizione_fondo}</td>
                                       <td >${art.template_name}</td>
                                           <td>
                <button class="btn btn-primary btn-visualize-template" data-name='${art.template_name}'>Visualizza e Modifica</button>
                </td>
                </tr>
                             `);

                });

                $('.btn-visualize-template').click(function () {
                    template_name = $(this).attr('data-name');
                });
            }

            $(document).ready(function () {

                renderDataTable();

                $('.btn-visualize-template').click(function () {
                    location.href = '<?= DateXFondoCommon::get_website_url()?>/visualizza-template-fondo/?template_name=' + template_name;
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
                <th style="width: 200px">Fondo</th>
                <th style="width: 100px">Anno</th>
                <th>Descrizione fondo</th>
                <th style="width: 100px">Nome Template</th>
                <th style="width: 12.625rem">Azioni</th>
            </tr>

            </thead>
            <tbody id="dataAllTemplateTableBody">
            </tbody>
        </table>
        <?php
        self::render_scripts();
    }
}