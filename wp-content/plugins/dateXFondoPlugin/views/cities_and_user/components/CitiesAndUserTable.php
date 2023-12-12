<?php

use dateXFondoPlugin\CitiesRepository;
use dateXFondoPlugin\DateXFondoCommon;

class CitiesAndUserTable
{
    public static function render_scripts()
    {
        ?>
        <style>
            #selectedCities:hover {
                background-color: #870e12;

            }

            #selectedCities {
                border-color: #26282f;
                background-color: #26282f;
            }

        </style>
        <script>
            function checkCheckboxes(checkboxValues) {
                let itemForm = document.getElementById('itemForm');
                let checkBoxes = itemForm.querySelectorAll('input[type="checkbox"]');

                checkBoxes.forEach(value => {
                    let id = value.value.split(',')[1];
                    checkboxValues.forEach(city => {
                        if (parseInt(id) === city['id_ente'])
                            value.checked = true;
                    })

                });
            }

            function clearCheckboxes() {
                let itemForm = document.getElementById('itemForm');
                let checkBoxes = itemForm.querySelectorAll('input[type="checkbox"]');
                checkBoxes.forEach(value => {
                    value.checked = false;
                });
            }

            function selectAllCheckboxes() {
                let itemForm = document.getElementById('itemForm');
                let tutticheckbox = document.getElementById('selectAll');
                let checkBoxes = itemForm.querySelectorAll('input[type="checkbox"]');
                if (tutticheckbox.checked) {
                    checkBoxes.forEach(value => {
                        value.checked = true;
                    });
                }
                else{
                    checkBoxes.forEach(value => {
                        value.checked = false;
                    });
                }

            }

            function onSelectChange() {
                const selectedValue = document.getElementById('idUtenteSelezionato').value;
                clearCheckboxes();
                let payload = {
                    selectedValue,
                    bool: 1
                }
                $.ajax({
                    url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/selectuser',
                    data: payload,
                    type: "POST",
                    success: function (response) {
                        console.log(response);
                        checkCheckboxes(response['data']);
                        $(".alert-edit-success").show();
                        $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                            $(".alert-edit-success").slideUp(500);
                        });
                    },
                    error: function (response) {
                        console.error(response);
                        $(".alert-edit-wrong").show();
                        $(".alert-edit-wrong").fadeTo(2000, 500).slideUp(500, function () {
                            $(".alert-edit-wrong").slideUp(500);
                        });
                    }
                });

            }

            $(document).ready(function () {
                onSelectChange();
                document.getElementById('idUtenteSelezionato').addEventListener('change', onSelectChange);
                document.getElementById('selectAll').addEventListener('change', selectAllCheckboxes);

                $('#selectedCities').click(function () {
                    let nome_utente = $('#idUtenteSelezionato').val();
                    let itemForm = document.getElementById('itemForm');
                    let tuttiButton = document.getElementById('selectAll').checked;
                    let checkBoxes = itemForm.querySelectorAll('input[type="checkbox"]');
                    let citiesArray = [];
                    checkBoxes.forEach(item => {
                        if (item.checked) {
                            let array = item.value.split(",");
                            let data = {
                                ente: array[0].replace(/\s/g, ''),
                                db: "c1date_" + array[0].replace(/\s/g, '').toLowerCase(),
                                id: array[1]
                            }
                            citiesArray.push(data);
                        }
                    })
                    const payload = {
                        nome_utente,
                        citiesArray,
                        tuttiButton

                    }
                    console.log(payload);

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/citiesuser',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            checkCheckboxes(response['data']);
                            $(".alert-edit-success").show();
                            $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $(".alert-edit-wrong").show();
                            $(".alert-edit-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-wrong").slideUp(500);
                            });
                        }
                    });
                });

            });
        </script>
    <?php }

    public static function render()
    {
        $data = new CitiesRepository();
        $users = $data->getConsultants();
        $cities = $data->getCities();
        ?>
        <div class="container d-flex justify-content-center">
            <div class="columns-6">
                <div class="row" style="padding-bottom:12px">
                    <label>Seleziona Utente:</label>
                    <select name="utenteSelezionato" id="idUtenteSelezionato">
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <option><?= $user['user_login']; ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
                <div class="row">
                    <div class="card" style="width: 32rem;">
                        <div class="card-header">
                            Seleziona Enti:
                        </div>
                        <div id="itemForm">
                            <div class="item">
                                <li class="list-group-item">
                                    <input type="checkbox" id="selectAll"> <label> Tutti</label>
                                </li>
                            </div>

                            <?php
                            foreach ($cities as $city) {
                                ?>
                                <div class="item">
                                    <li class="list-group-item">
                                        <input id="id_<?= $city['nome']; ?>" type="checkbox"
                                               value=" <?= $city['nome'] . ','; ?><?= $city['id']; ?>">
                                        <label for=" <?= $city['nome']; ?>"> <?= $city['nome']; ?></label>
                                    </li>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="row" style="padding-left: 150px; padding-top: 34px;">
                    <button class="btn btn-primary" id="selectedCities">Conferma selezione</button>
                </div>
            </div>
        </div>


        <div class="alert alert-success alert-edit-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica andata a buon fine!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <div class="alert alert-danger alert-edit-wrong" role="alert"
         style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica riga non riuscita
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        <?php
        self::render_scripts();
    }
}