<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloFondoNewUtilizzoRow
{
    public static function render_scripts()
    {
        ?>
        <script>


            function renderUtilizzoSectionFilterRow() {
                $('#selectNewUtilizzoRowSezione').html('<option>Seleziona Sezione</option>');
                Object.keys(sezioniUtilizzoList).forEach(sez => {
                    $('#selectNewUtilizzoRowSezione').append(`<option>${sez}</option>`);
                });
            }


            function clearInputRow() {
                $('#selectNewUtilizzoRowSezione').prop('selectedIndex', 0);
                $('#newUtilizzoRowOrdinamento').val('');
                $('#newUtilizzoRowNomeArticolo').val('');
                $('#newUtilizzoRowConsuntivo').val('');
                $('#newUtilizzoRowPreventivo').val('');
            }

            $(document).ready(function () {
                clearInputRow();
                renderUtilizzoSectionFilterRow();

                $('#addNewUtilizzoRowButton').click(function () {
                    {
                        let nome_articolo = $('#newUtilizzoRowNomeArticolo').val();
                        let ordinamento = $('#newUtilizzoRowOrdinamento').val();
                        let consuntivo = $('#newUtilizzoRowConsuntivo').val();
                        let preventivo = $('#newUtilizzoRowPreventivo').val();
                        let sezione = '';
                        if (sezione !== 'Seleziona Sezione') {
                            sezione = $('#selectNewUtilizzoRowSezione').val();
                        }

                        let document_name = $('#inputDocumentName').val();
                        let anno = $('#inputYear').val();

                        if (articoli_dati_utili.find(art => art.nome_articolo === nome_articolo) === undefined && sezione !== 'Seleziona Sezione' && sottosezione !== 'Seleziona Sottosezione') {
                            const payload = {
                                nome_articolo,
                                ordinamento,
                                consuntivo,
                                preventivo,
                                sezione,
                                document_name,
                                anno
                            }
                            console.log(payload)
                            $.ajax({
                                url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/newutilizzorow',
                                data: payload,
                                type: "POST",
                                success: function (response) {
                                    $("#addUtilizzoRowModal").modal('hide');
                                    articoli_dati_utili.push({...payload, id: response['id']});
                                    renderUtilizzoSectionFilterRow();
                                    $(".alert-new-row-success").show();
                                    $(".alert-new-row-success").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-new-row-success").slideUp(500);
                                    });
                                    clearInputRow();
                                },
                                error: function (response) {
                                    $("#addUtilizzoRowModal").modal('hide');
                                    console.error(response);
                                    $(".alert-new-row-wrong").show();
                                    $(".alert-new-row-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-new-row-wrong").slideUp(500);
                                    });
                                }
                            });
                        } else if (sezione !== 'Seleziona Sezione') {
                            $("#errorSection").attr('style', 'display:block');
                        }

                    }
                });
            })
        </script>
        <?php
    }

    public static function render()
    {

        $data = new DocumentRepository();
        $results_articoli = $data->getArticoliUtilizzo('Emanuele Lesca');
        $formulas = $data->getFormulas('Emanuele Lesca');
        $ids_articolo = $data->getIdsArticoli('Emanuele Lesca');
        $array = $formulas + $ids_articolo;

        if ($results_articoli[0]['editable'] == '1') {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addUtilizzoRowModal" id="idAddUtilizzoRow">Aggiungi riga utilizzo
            </button>
            <?php
        } else {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addUtilizzoRowModal" id="idAddUtilizzoRow" disabled>Aggiungi riga utilizzo
            </button>
            <?php
        }
        ?>
        <div class="modal fade" id="addUtilizzoRowModal" tabindex="-1"
             role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><b>Nuova riga:</b></h5>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="selectUtilizzoRowSezione"><b>Sezione:</b></label>
                            <select class="custom-select" id="selectNewUtilizzoRowSezione">
                            </select>
                            <small id="errorSection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>

                        <div class="form-group">
                            <label for="inputOrdinamento"><b>Ordinamento:</b></label>
                            <input type="text" class="form-control" id="newUtilizzoRowOrdinamento"></div>
                        <div class="form-group">
                            <label for="inputNomeArticolo"><b>Articolo:</b> </label>
                            <input type="text" class="form-control" id="newDatiUtiliRowNomeArticolo">
                        </div>
                        <div class="form-group">
                            <label for="idFormula"><b>Preventivo: </b></label>

                            <select name="newUtilizzoRowPreventivo" id="newUtilizzoRowPreventivo">
                                <?php
                                foreach ($array as $item) {
                                    ?>
                                    <option><?= $item[0] ?></option>
                                <?php }
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="inputConsuntivo"><b>Consuntivo:</b> </label>
                            <input type="text" class="form-control" id="newUtilizzoRowConsuntivo">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="addNewUtilizzoRowButton">Aggiungi riga</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-success alert-new-row-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Nuova riga aggiunta correttamente!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-new-row-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Aggiunta nuova riga non riuscita
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();
    }
}