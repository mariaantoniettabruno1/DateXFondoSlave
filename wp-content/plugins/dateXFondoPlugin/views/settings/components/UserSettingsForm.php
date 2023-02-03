<?php


use dateXFondoPlugin\DateXFondoCommon;


class UserSettingsForm
{

    public static function render_scripts()
    {
        ?>

        <style>
            form {
                width: 300px;
                margin: 0 auto;
            }

        </style>
        <script>
            $(document).ready(function () {
                $('#titoloEnte').val(infos[0].titolo_ente)
                $('#soggettoDeliberante').val(infos[0].nome_soggetto_deliberante)
                $('#responsabileDocumento').val(infos[0].responsabile)
                $('#firma').val(infos[0].firma)
                if (infos[0].riduzione_spesa === '2008') {
                    $('#duemilaotto').prop('checked', true);
                } else if (infos[0].riduzione_spesa === 'Media Triennio 2011/2013') {
                    $('#mediaTriennio').prop('checked', true);
                }
                $('#saveSettings').click(function () {
                    let titolo_ente = $('#titoloEnte').val();
                    let soggetto_deliberante = $('#soggettoDeliberante').val();
                    let responsabile_documento = $('#responsabileDocumento').val();
                    let firma = $('#firma').val();
                    let riduzione_spesa = $('input:radio[name=typeRiduzione]:checked').val();
                    

                    const payload = {
                        titolo_ente,
                        soggetto_deliberante,
                        responsabile_documento,
                        firma,
                        riduzione_spesa
                    }

                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/usersettings',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $(".alert-save-success").show();
                            $(".alert-save-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-save-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $(".alert-save-wrong").show();
                            $(".alert-save-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-save-wrong").slideUp(500);
                            });
                        }
                    });
                });


            });
        </script>
        <?php
    }

    public static function render()
    {
        ?>
        <div class="container-fluid">
            <div class="row">
                <form>
                    <div class="form-group">
                        <label for="titoloEnte">Titolo Ente</label>
                        <input type="text" class="form-control" id="titoloEnte" placeholder="Inserisci il titolo ente">
                    </div>
                    <div class="form-group">
                        <label for="soggettoDeliberante">Nome Soggetto Deliberante</label>
                        <input type="text" class="form-control" id="soggettoDeliberante"
                               placeholder="Inserisci il nome">
                    </div>
                    <div class="form-group">
                        <label for="responsabileDocumento">Responsabile Documento</label>
                        <input type="text" class="form-control" id="responsabileDocumento"
                               placeholder="Inserisci il nome">
                    </div>

                    <div class="form-group">
                        <label for="firma">Documento a firma di:</label>
                        <input type="text" class="form-control" id="firma" placeholder="Inserisci il nome">
                    </div>
                    <label for="inputRiduzioneSpesa"><b>Riduzione Spesa</b> </label>
                    <div class="form-check user-checked">
                        <input class="form-check-input" type="radio" name="typeRiduzione" id="duemilaotto"
                               value="2008">
                        <label class="form-check-label" for="duemilaotto">
                            2008
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="typeRiduzione" id="mediaTriennio"
                               value="Media Triennio 2011/2013">
                        <label class="form-check-label" for="mediaTriennio">
                            Media Triennio 2011/2013
                        </label>
                    </div>
                </form>
            </div>
            <div class="row">
                <button class="btn btn-primary" id="saveSettings">Salva Modifiche</button>
            </div>

        </div>


        <div class="alert alert-success alert-save-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifiche salvate con successo!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-save-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica non riuscita
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();
    }
}