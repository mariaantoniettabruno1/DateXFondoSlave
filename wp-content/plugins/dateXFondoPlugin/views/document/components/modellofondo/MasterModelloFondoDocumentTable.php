<?php


class MasterModelloFondoDocumentTable
{
    public static function render_scripts()
    {
    }

    public static function render()
    {
        ?>
            <div class="container pt-3" style="width: 100%">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-link active" id="costituzione-tab" href="#costituzione" role="tab"
                           aria-controls="costituzione" aria-selected="true" data-toggle="pill">Costituzione</a>
                        <a class="nav-link" id="utilizzo-tab" href="#utilizzo" role="tab" aria-controls="utilizzo"
                           aria-selected="false" data-toggle="pill">Utilizzo</a>
                        <a class="nav-link" id="dati-tab" href="#dati" role="tab" aria-controls="dati_utili"
                           aria-selected="false" data-toggle="pill">Dati utili fondo</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="costituzione" role="tabpanel" aria-labelledby="costituzione-tab" aria-selected="true">
                        <?php
                        MasterModelloFondoCostituzione::render();
                        ?>
                    </div>
                    <div class="tab-pane fade" id="utilizzo" role="tabpanel" aria-labelledby="utilizzo-tab" aria-selected="false">
                        <?php
                        MasterModelloFondoUtilizzo::render();
                        ?>
                    </div>
                    <div class="tab-pane fade" id="dati" role="tabpanel" aria-labelledby="dati-tab" aria-selected="false">
                        <?php
                        MasterModelloFondoDatiUtili::render();
                        ?>
                    </div>
                </div>
            </div>


        <?php
        self::render_scripts();
    }
}