<?php
namespace dateXFondoPlugin;

class UserSettings
{
public static function render(){
    $data = new UserRepository();
    $results_infos = $data->getUserInfos();
    your_namespace();


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
        <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/wp-content/plugins/dateXFondoPlugin/assets/styles/main.css">




    </head>

    <body>
    <div class="container-fluid">
        <?php if (my_get_current_user_roles()[0] == 'subscriber'): ?>
            <div class="row pb-3" style="width: 20%">
                <div class="col-10">

                    <label>Seleziona comune per visualizzare i suoi dati:</label>

                    <select name="comune" id="idComune">
                        <option value="rubiana">Rubiana</option>
                        <option value="spotorno">Spotorno</option>
                        <option value="robassomero">Robassomero</option>
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
    </body>
    <script>
        let infos = JSON.parse((`<?=json_encode($results_infos);?>`));

        let citySelected = '';
        $('#selectedCity').click(function () {
            citySelected = $("#idComune").val();
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
        #selectedCity {
            border-color: #26282f !important;
            background-color: #26282f !important;
        }
    </style>
    </html lang="en">
    <?php
}
}