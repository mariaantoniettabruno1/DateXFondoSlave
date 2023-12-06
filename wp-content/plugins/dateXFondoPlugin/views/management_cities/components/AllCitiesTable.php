<?php

use dateXFondoPlugin\CitiesRepository;
use dateXFondoPlugin\DateXFondoCommon;

class AllCitiesTable
{
    public static function render_scripts()
    {
        ?>
        <style>
            .btn-edit-row, .btn-edit-row:hover {
                color: #870e12;
            }

            .btn-create-ente, #editRowButton {
                border-color: #26282f;
                background-color: #26282f;
            }

            .btn-create-ente:hover, #editRowButton:hover {
                border-color: #870e12;
                background-color: #870e12;
            }

        </style>
        <script>
            let id = '';

            function renderDataCitiesTable() {
                $('#dataCitiesTableBody').html('');
                attivo_button = ''
                cities.forEach(city => {
                    if (city.attivo === 1 || city.attivo === '1') {
                        city.attivo = "Attivo";
                    } else
                        city.attivo = "Non attivo";

                    edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${city.id}' data-toggle="modal" data-target="#editModal"><i class="fa-solid fa-pen"></i></button>`;

                    consultants.forEach(consultant => {
                        if (consultant.id === city.id_consulente)
                            city.id_consulente = consultant.user_login
                    })
                    $('#dataCitiesTableBody').append(`
                                 <tr>
                                        <td ><div style="padding-top:20px">${city.nome}</div></td>
                                       <td ><div style="padding-top:20px">${city.descrizione}</div></td>
                                       <td ><div style="padding-top:20px">${city.data_creazione}</div></td>
                                       <td ><div style="padding-top:20px">${city.data_scadenza}</div></td>
                                       <td ><div style="padding-top:20px">${city.id_consulente}</div></td>
                                       <td ><div style="padding-top:20px">${city.attivo}</div></td>
                                       <td ><div style="padding-top:20px">${edit_button}</div></td>
                </tr>
                             `);
                });
                $('.btn-edit-row').click(function () {
                    id = $(this).attr('data-id');
                    const city = cities.find(art => Number(art.id) === Number(id))
                    if (!city) return;
                    $('#idNome').val(city.nome)
                    $('#idDescrizione').val(city.descrizione)
                    $('#idDataCreazione').val(city.data_creazione)
                    $('#idDataScadenza').val(city.data_scadenza)
                    $('#idConsulenteSelezionato').val(city.id_consulente);

                    if (city.attivo === 1 || city.attivo === '1' || city.attivo === 'Attivo')
                        $('#idDataAttivo').prop('checked', true);

                });
            }

            $('.btn-create-ente').click(function () {
                $('#idNome').val('');
                $('#idDescrizione').val('')
                $('#idDataCreazione').val('')
                $('#idDataScadenza').val('')
                $('#idDataAttivo').prop('checked', true);

            });

            $(document).ready(function () {
                renderDataCitiesTable();

                $('#editRowButton').click(function () {
                    let nome = $('#idNome').val();
                    let descrizione = $('#idDescrizione').val();
                    let data_creazione = $('#idDataCreazione').val();
                    let data_scadenza = $('#idDataCreazione').val();
                    let attivo = $("input:radio[name=typeActiveEdit]:checked").val();
                    let id_consulente = $('#idConsulenteSelezionato').val();

                    const payload = {
                        id,
                        nome,
                        descrizione,
                        data_creazione,
                        data_scadenza,
                        attivo,
                        id_consulente
                    }
                    console.log(payload);

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/editcityrow',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#editModal").modal('hide');
                            if (id !== '') {
                                const city = cities.find(city => Number(city.id) === Number(id));
                                city.nome = nome;
                                city.descrizione = descrizione;
                                city.data_creazione = data_creazione;
                                city.data_scadenza = data_scadenza;
                                city.attivo = attivo;
                                city.id_consulente = id_consulente;

                            } else {
                                cities.push({...payload, id: response['id']});
                            }
                            renderDataCitiesTable();
                            $(".alert-edit-success").show();
                            $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $("#editModal").modal('hide');
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
        $consulenti = $data->getConsultants();

        ?>
        <div style="padding-bottom:12px; margin-left: 1148px">
            <button type="button" class="btn btn-primary btn-create-ente" data-toggle="modal" data-target="#editModal">
                Crea nuovo ente
            </button>
        </div>
        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
                <th style="width: 12.5rem">Nome Ente</th>
                <th>Descrizione</th>
                <th style="width: 10rem">Data creazione</th>
                <th style="width: 10rem">Data scadenza</th>
                <th style="width: 7rem">Consulente</th>
                <th style="width: 6.25rem">Stato</th>
                <th style="width: 6.25rem">Azioni</th>
            </tr>

            </thead>
            <tbody id="dataCitiesTableBody">
            </tbody>
        </table>

        <div class="modal fade" id="editModal" tabindex="-1"
             role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dati Ente:</h5>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="idNome">Nome</label>
                        <input class="form-control" id="idNome">
                    </div>
                    <div class="form-group">
                        <label for="idDescrizione">Descrizione</label>
                        <textarea class="form-control"
                                  id="idDescrizione"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="active" for="idDataCreazione">Data creazione:</label>
                        <input type="date" id="idDataCreazione">
                    </div>
                    <div class="form-group">
                        <label class="active" for="idDataScadenza">Data scadenza:</label>
                        <input type="date" id="idDataScadenza">
                    </div>
                    <div class="form-group">
                        <form>
                            <div class="form-group">
                                <label>Seleziona Consulente:</label>
                                <select name="consulenteSelezionato" id="idConsulenteSelezionato">
                                    <?php
                                    foreach ($consulenti as $consulente) {
                                        ?>
                                        <option><?= $consulente['user_login']; ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="typeActiveEdit"
                               id="idDataAttivo"
                               value="1">
                        <label class="form-check-label" for="attivoSelected">
                            Attivo
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="editRowButton">Salva</button>
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