<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloFondoNewCostituzioneRow
{
public static function render_scripts(){
    ?>
    <script>


            function renderCostituzioneSectionFilterRow() {
                $('#selectNewCostRowSezione').html('<option>Seleziona Sezione</option>');
                Object.keys(sezioniList).forEach(sez => {
                    $('#selectNewCostRowSezione').append(`<option>${sez}</option>`);
                });
            }

            function filterCostituzioneSubsectionsRow(section) {
                $('#selectNewCostRowSottosezione').html('<option>Seleziona Sottosezione</option>');
                sezioniList[section].forEach(ssez => {
                    $('#selectNewCostRowSottosezione').append(`<option>${ssez}</option>`);
                });
            }

            function clearInputRow() {
                $('#selectNewCostRowSezione').prop('selectedIndex', 0);
                $('#selectNewCostRowSottosezione').prop('selectedIndex', -1);
                $('#newConstRowSottosezione').val('');
                $('#newConstRowOrdinamento').val('');
                $('#newConstRowNomeArticolo').val('');
                $('#newConstRowPreventivo').val('');
            }

            $(document).ready(function () {
                clearInputRow();
                renderCostituzioneSectionFilterRow();
                $('#selectNewCostRowSezione').change(function () {
                    const section = $('#selectNewCostRowSezione').val();
                    if (section !== 'Seleziona Sezione') {
                        filterCostituzioneSubsectionsRow(section);
                    } else {
                        $('#selectNewCostRowSottosezione').html('');
                    }
                });
                $('.subsectionButtonGroup1').click(function () {
                    $('#selectNewCostRowSottosezione').show();
                    $('#newConstRowSottosezione').attr('style', 'display:none');
                });
                $('.subsectionButtonGroup2').click(function () {
                    $('#newConstRowSottosezione').attr('style', 'display:block');
                    $('#selectNewCostRowSottosezione').hide();
                });
                $('#addNewCostRowButton').click(function () {
                    {
                        let nome_articolo = $('#newConstRowNomeArticolo').val();
                        let ordinamento = $('#newConstRowOrdinamento').val();
                        let preventivo = $('#newConstRowPreventivo').val();
                        let sezione = '';
                        if (sezione !== 'Seleziona Sezione') {
                            sezione = $('#selectNewCostRowSezione').val();
                        }
                        let sottosezione = '';
                        if ($('#selectNewCostRowSottosezione').val() != null && $('#selectNewCostRowSottosezione').val() !== 'Seleziona Sottosezione') {
                            sottosezione = $('#selectNewCostRowSottosezione').val();
                        } else if ($('#newConstRowSottosezione').val() != null) {
                            sottosezione = $('#newConstRowSottosezione').val();
                        }

                        let document_name = $('#inputDocumentName').val();
                        let anno = $('#inputYear').val();

                        if (articoli.find(art => art.nome_articolo === nome_articolo) === undefined && sezione !== 'Seleziona Sezione' && sottosezione!=='Seleziona Sottosezione') {
                        const payload = {
                            nome_articolo,
                            ordinamento,
                            preventivo,
                            sezione,
                            sottosezione,
                            document_name,
                            anno
                            }
                            console.log(payload)
                            $.ajax({
                                url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/newcostrow',
                                data: payload,
                                type: "POST",
                                success: function (response) {
                            $("#addCostRowModal").modal('hide');
                            articoli.push({...payload, id: response['id']});
                                    renderDataTable();
                                    $(".alert-new-row-success").show();
                                    $(".alert-new-row-success").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-new-row-success").slideUp(500);
                                    });
                                    clearInputRow();
                                },
                                error: function (response) {
                            $("#addCostRowModal").modal('hide');
                            console.error(response);
                            $(".alert-new-row-wrong").show();
                            $(".alert-new-row-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-new-row-wrong").slideUp(500);
                            });
                        }
                            });
                        }  else if (sezione !== 'Seleziona Sezione') {
                        $("#errorSection").attr('style', 'display:block');
                    }
                    else if(sottosezione !== 'Seleziona Sottosezione'){
                        $("#errorSubsection").attr('style', 'display:block');

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
        $results_articoli = $data->getArticoli($_GET['editor_name']);
        $formulas = $data->getFormulas($_GET['editor_name']);
        $ids_articolo = $data->getIdsArticoli($_GET['editor_name']);
        $array = $formulas + $ids_articolo;

        //TODO filter per togliere i valori vuoti
//        for( $i=0; $i<count($results_articoli); $i++){
//            if($results_articoli[$i]['preventivo']===null){
//                array_splice($results_articoli[$i], 'preventivo', 1);
//            }
//            echo '<pre>';
//            print_r($results_articoli[$i]);
//            echo '</pre>';
//        }
//

        if ($results_articoli[0]['editable'] == '1') {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addCostRowModal" id="idAddCostRow">Aggiungi riga costituzione
            </button>
            <?php
        } else {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addCostRowModal" id="idAddCostRow" disabled>Aggiungi riga costituzione
            </button>
            <?php
        }
        ?>
        <div class="modal fade" id="addCostRowModal" tabindex="-1"
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
                            <label for="selectCostRowSezione"><b>Sezione:</b></label>
                            <select class="custom-select" id="selectNewCostRowSezione">
                            </select>
                            <small id="errorSection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>
                        <div class="form-group" id="divSelectNewCostRowSottosezione">
                            <br>
                            <div class="btn-group pb-3" role="group" aria-label="Basic example">
                                <button type="button" class="btn  btn-outline-primary subsectionButtonGroup1">
                                    Seleziona Sottosezione
                                </button>
                                <button type="button" class="btn btn-outline-primary subsectionButtonGroup2">
                                    Nuova Sottosezione
                                </button>
                            </div>
                            <div class="form-group">
                                <select class="custom-select" id="selectNewCostRowSottosezione">
                                </select>
                                <small id="errorSubsection" class="form-text text-danger" style="display: none">Campo
                                    Obbligatorio</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="newConstRowSottosezione" style="display:none">
                            <small id="errorSubsection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>

                        <div class="form-group">
                            <label for="inputOrdinamento"><b>Ordinamento:</b></label>
                            <input type="text" class="form-control" id="newConstRowOrdinamento"></div>
                        <div class="form-group">
                            <label for="inputNomeArticolo"><b>Articolo:</b> </label>
                            <input type="text" class="form-control" id="newConstRowNomeArticolo">
                        </div>
                        <div class="form-group">
                            <label for="idPreventivo"><b>Preventivo: </b></label>

                            <select name="newConstRowPreventivo" id="newConstRowPreventivo">
                                <?php
                                foreach ($array as $item) {
                                    ?>
                                    <option><?= $item[0] ?></option>
                                <?php }
                                ?>
                            </select>

                        </div>
                    </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" id="addNewCostRowButton">Aggiungi riga</button>
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