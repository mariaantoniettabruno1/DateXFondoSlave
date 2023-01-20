<?php

use dateXFondoPlugin\DateXFondoCommon;

class ModelloRegioniDestinazioneRow
{
    public static function render_scripts()    {
        ?>

        <script>


            function renderDestinazioneSectionFilterRow() {
                $('#selectDestinazioneNewRowSezione').html('<option>Seleziona Sezione</option>');
                Object.keys(sezioni_destinazione).forEach(sez => {
                    $('#selectDestinazioneNewRowSezione').append(`<option>${sez}</option>`);
                });
            }

            function filterDestinazioneSubsectionsRow(section) {
                $('#selectNewRowDestinazioneSottosezione').html('<option>Seleziona Sottosezione</option>');
                sezioni_destinazione[section].forEach(ssez => {
                    $('#selectNewRowDestinazioneSottosezione').append(`<option>${ssez}</option>`);
                });
            }

            function clearInputRow() {
                $('#selectDestinazioneNewRowSezione').prop('selectedIndex', 0);
                $('#selectNewRowDestinazioneSottosezione').prop('selectedIndex', -1);
                $('#newRowDestinazioneSottosezione').val('');
                $('#newRowDestinazioneOrdinamento').val('');
                $('#newRowDestinazioneNomeArticolo').val('');
                $('#newRowDestinazioneCodice').val('');
                $('#newRowDestinazioneImporto').val('');
                $('#newRowDestinazioneNota').val('');
            }

            $(document).ready(function () {
                clearInputRow();
                renderDestinazioneSectionFilterRow();
                $('#selectDestinazioneNewRowSezione').change(function () {
                    const section = $('#selectDestinazioneNewRowSezione').val();
                    if (section !== 'Seleziona Sezione') {
                        filterDestinazioneSubsectionsRow(section);
                    } else {
                        $('#selectNewRowDestinazioneSottosezione').html('');
                    }
                });
                $('.subsectionButtonGroup1').click(function () {
                    $('#selectNewRowDestinazioneSottosezione').show();
                    $('#newRowDestinazioneSottosezione').attr('style', 'display:none');
                });
                $('.subsectionButtonGroup2').click(function () {
                    $('#newRowDestinazioneSottosezione').attr('style', 'display:block');
                    $('#selectNewRowDestinazioneSottosezione').hide();
                });
                $('#addNewRowDestinazioneButton').click(function () {
                    {
                        let ordinamento = $('#newRowDestinazioneOrdinamento').val();
                        let nome_articolo = $('#newRowDestinazioneNomeArticolo').val();
                        let codice = $('#newRowDestinazioneCodice').val();
                        let importo = $('#newRowDestinazioneImporto').val();
                        let titolo_documento = $('#inputTitoloDocumento').val();
                        let titolo_tabella = $('#newRowDestinazioneTitoloTabella').val();
                        let sezione = '';
                        if (sezione !== 'Seleziona Sezione') {
                            sezione = $('#selectDestinazioneNewRowSezione').val();
                        }
                        let sottosezione = '';
                        if ($('#selectNewRowDestinazioneSottosezione').val() != null && $('#selectNewRowDestinazioneSottosezione').val() !== 'Seleziona Sottosezione') {
                            sottosezione = $('#selectNewRowDestinazioneSottosezione').val();
                        } else if ($('#newRowDestinazioneSottosezione').val() != null) {
                            sottosezione = $('#newRowDestinazioneSottosezione').val();
                        }
                        let nota = $('#newRowDestinazioneNota').val();
                        let anno = $('#inputYear').val();
                        let document_name = $('#inputDocumentName').val();

                        if (articoli_costituzione.find(art => art.nome_articolo === nome_articolo) === undefined && sezione !== 'Seleziona Sezione' && sottosezione!=='Seleziona Sottosezione') {
                            const payload = {
                                titolo_tabella,
                                titolo_documento,
                                anno,
                                ordinamento,
                                nome_articolo,
                                codice,
                                importo,
                                sezione,
                                sottosezione,
                                nota,
                                document_name
                            }
                            console.log(payload)
                            $.ajax({
                                url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/regioninewrow',
                                data: payload,
                                type: "POST",
                                success: function (response) {
                                    $("#addRegioniRowModal").modal('hide');
                                    articoli_costituzione.push({...payload, id: response['id']});
                                    renderRegioniDocumentDataTable(sezione);
                                    $(".alert-new-row-success").show();
                                    $(".alert-new-row-success").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-new-row-success").slideUp(500);
                                    });
                                    clearInputRow();
                                },
                                error: function (response) {
                                    $("#addRegioniRowModal").modal('hide');
                                    console.error(response);
                                    $(".alert-new-row-wrong").show();
                                    $(".alert-new-row-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-new-row-wrong").slideUp(500);
                                    });
                                }
                            });
                        }else if (sezione !== 'Seleziona Sezione') {
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
        $data = new \dateXFondoPlugin\RegioniDocumentRepository();
        $data_document = new DocumentRepository();
        $formulas = $data_document->getFormulas($_GET['editor_name']);
        $ids_articolo = $data_document->getIdsArticoli($_GET['editor_name']);
        $array = $formulas + $ids_articolo;
        $results_articoli = $data->getDestinazioneArticoli($_GET['editor_name']);
        $table_title = [];

        //TODO da ottimizzare
        for($i=0; $i<sizeof($results_articoli);$i++){
            if(!in_array($results_articoli[$i]['titolo_tabella'],$table_title)){
                array_push($table_title,$results_articoli[$i]['titolo_tabella']);
            }
        }

        if ($results_articoli[0]['editable'] == '1') {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addRegioniRowModal" id="idAddRegioniRow">Nuova riga destinazione
            </button>
            <?php
        } else {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addRegioniRowModal" id="idAddRegioniRow" disabled>Nuova riga destinazione
            </button>
            <?php
        }
        ?>
        <div class="modal fade" id="addRegioniRowModal" tabindex="-1"
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
                            <label for="idTitoloTabella"><b>Titolo tabella: </b></label>

                            <select name="newRowDestinazioneTitoloTabella" id="newRowDestinazioneTitoloTabella">
                                <?php
                                foreach ($table_title as $title) {
                                    ?>

                                    <option><?= $title ?></option>

                                <?php }
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="selectRowSezione"><b>Sezione:</b></label>
                            <select class="custom-select" id="selectDestinazioneNewRowSezione">
                            </select>
                            <small id="errorSection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>
                        <div class="form-group" id="divSelectNewRowDestinazioneSottosezione">
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
                                <select class="custom-select" id="selectNewRowDestinazioneSottosezione">
                                </select>
                                <small id="errorSubsection" class="form-text text-danger" style="display: none">Campo
                                    Obbligatorio</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="newRowDestinazioneSottosezione" style="display:none">
                            <small id="errorSubsection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>
                        <div class="form-group">
                            <label for="idOrdinamento"><b>Ordinamento: </b></label>
                            <input type="text" class="form-control" id="newRowDestinazioneOrdinamento">
                        </div>
                        <div class="form-group">
                            <label for="inputRegioniNomeArticolo"><b>Articolo:</b> </label>
                            <input type="text" class="form-control" id="newRowDestinazioneNomeArticolo">
                        </div>
                        <div class="form-group">
                            <label for="idCodice"><b>Codice: </b></label>

                            <select name="newRowDestinazioneCodice" id="newRowDestinazioneCodice">
                                <?php
                                foreach ($array as $res) {
                                    ?>
                                    <option><?= $res[0] ?></option>

                                <?php }
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="inputRegioniImporto"><b>Importo:</b> </label>
                            <input type="text" class="form-control" id="newRowDestinazioneImporto">
                        </div>
                        <div class="form-group">
                            <label for="idNota"><b>Nota:</b></label>
                            <textarea class="form-control"
                                      id="newRowDestinazioneNota"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" id="addNewRowDestinazioneButton">Aggiungi riga</button>
                        </div>
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