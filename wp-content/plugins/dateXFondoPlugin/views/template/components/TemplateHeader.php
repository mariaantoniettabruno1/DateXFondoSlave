<?php

namespace dateXFondoPlugin;

class TemplateHeader
{
    public static function render_scripts()
    {
        ?>

        <script>
            $(document).ready(function () {
                $('#inputFondo').val(`${articoli[0].fondo}`);
                $('#inputAnno').val(`${articoli[0].anno}`);
                $('#inputDescrizioneFondo').val(`${articoli[0].descrizione_fondo}`);
                $('#inputNomeTemplate').val(`${articoli[0].template_name}`);
            });

        </script>
        <?php
    }

    public static function render()
    {
             ?>
        <div class="col-2">
            <input type="text" placeholder="Fondo" id="inputFondo" readonly>
        </div>
        <div class="col-1 pl-0">
            <input type="text" placeholder="Anno" id="inputAnno" readonly>
        </div>
        <div class="col-4 pl-0">
            <input type="text" placeholder="Descrizione Fondo" id="inputDescrizioneFondo" readonly>
        </div>
        <div class="col-3 pl-0">
            <input type="text" placeholder="Nome Template" id="inputNomeTemplate" readonly>
        </div>

        <?php
        self::render_scripts();

    }
}