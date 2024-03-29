<?php

namespace dateXFondoPlugin;

class UserSettings
{
    public static function render()
    {
        your_namespace();
        $id_user = my_get_current_user_id();
        $utente_info = get_userdata($id_user[0]);
        $ruoli_utente = $utente_info->roles;
        if ($ruoli_utente[0] != '') {
            $data = new UserRepository();
            $results_infos = $data->getUserInfos();
            $results_new_template = $data->checkNewTemplate($id_user[0]);
            $user_cities = $data->getAllUserCities($id_user[0]);
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
                <link rel="stylesheet"
                      href="<?= DateXFondoCommon::get_base_url() ?>/wp-content/plugins/dateXFondoPlugin/assets/styles/main.css">


            </head>

            <body>
            <div class="container-fluid">
                <?php if (my_get_current_user_roles()[0] != 'subscriber'): ?>
                    <div class="row pb-3" style="width: 20%">
                        <div class="col-10">

                            <label>Seleziona comune per visualizzare i suoi dati:</label>
                            <select name="enteSelezionato" id="idEnteSelezionato">
                                <?php
                                foreach ($user_cities as $city) {
                                    if ($city[0]['nome'] != '' || $city[0]['nome'] != null){
                                        ?>
                                        <option><?= $city[0]['nome']; ?></option>

                                <?php }}
                                ?>
                            </select>


                        </div>
                        <div class="col-2 align-self-end">
                            <button class="btn btn-primary btn-city" id="selectedCity">Conferma selezione</button>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <?php
                    \UserSettingsForm::render();
                    ?>
                </div>
            </div>
            <div class="modal fade" id="newTemplateModal" tabindex="-1" role="dialog"
                 aria-labelledby="newTemplateModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newTemplateModalLabel"><b>Nuovo Avviso</b></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="text-align: center;">
                            <b>Nuovo template disponibile</b> nella sezione:
                            <br>
                            <i>"Tabella template del fondo"</i>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                            <a href="'<?= DateXFondoCommon::get_website_url() ?>/all-template-table/">
                                <button type="button" class="btn btn-primary" id="toTemplateButton">Vai ai Template
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </body>
            <script>
                let infos = JSON.parse((`<?=json_encode($results_infos);?>`));
                let check = JSON.parse((`<?=json_encode($results_new_template);?>`));
                console.log(check)

                if (check)
                    $('#newTemplateModal').modal('show');

                let citySelected = '';
                $('#selectedCity').click(function () {
                    citySelected = $('#idEnteSelezionato').val().toLowerCase();
                    const payload = {
                        citySelected
                    }
                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/cityuserinfos',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);

                            $('#titoloEnte').val(response['data'][0].titolo_ente)
                            $('#soggettoDeliberante').val(response['data'][0].nome_soggetto_deliberante)
                            $('#responsabileDocumento').val(response['data'][0].responsabile)
                            $('#firma').val(response['data'][0].firma)
                            if (response['data'][0].riduzione_spesa === '2008') {
                                $('#duemilaotto').prop('checked', true);
                            } else if (response['data'][0].riduzione_spesa === 'Media Triennio 2011/2013') {
                                $('#mediaTriennio').prop('checked', true);
                            }
                            $(".alert-data-success").show();
                            $(".alert-data-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-data-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $(".alert-data-wrong").show();
                            $(".alert-data-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-data-wrong").slideUp(500);
                            });
                        }
                    });
                });

            </script>
            <div class="alert alert-success alert-data-success" role="alert"
                 style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
                Dati caricati correttamente!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="alert alert-danger alert-data-wrong" role="alert"
                 style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
                Dati non caricati correttamente, riprovare
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <style>

                #toTemplateButton, #selectedCity {
                    border-color: #26282f;
                    background-color: #26282f;
                }

                #toTemplateButton:hover, #selectedCity:hover {
                    border-color: #870e12;
                    background-color: #870e12;
                }
            </style>
            </html lang="en">
            <?php
        } else {

            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Avviso Utente Disabilitato</title>
                <!-- Link a Bootstrap CSS -->
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
            </head>
            <body>

            <div class="container mt-4" style="display: flex;align-items: center; justify-content: center">
                <div class="card mb-3" style="max-width: 36rem;">
                    <div class="card-body">
                        <a style="font-size: 3em; color:red; display: flex;align-items: center; justify-content: center"><i
                                    class="fa-solid fa-triangle-exclamation"></i></a>
                        <h5 class="card-title" style="display: flex;align-items: center; justify-content: center"><b>Utente
                                Disabilitato</b></h5>
                        <p class="card-text" style="display: flex;align-items: center; justify-content: center">L'utente
                            è stato disabilitato.</p>
                        <p class="card-text" style="display: flex;align-items: center; justify-content: center"> Si
                            prega di contattare l'assistenza per ulteriori informazioni.</p>
                    </div>
                </div>
            </div>

            <!-- Link a Bootstrap JS e script jQuery (necessari per alcune funzionalità di Bootstrap) -->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
                  integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>

            </body>
            </html>

            <?php
        }

    }
}