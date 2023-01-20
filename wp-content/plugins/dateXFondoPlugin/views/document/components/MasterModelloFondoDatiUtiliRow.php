<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloFondoDatiUtiliRow
{
    public static function render_scripts(){
        ?>
        <script>


            function renderDatiUtiliSectionFilterRow() {
                $('#selectNewDatiUtiliRowSezione').html('<option>Seleziona Sezione</option>');
                Object.keys(sezioniDatiUtiliList).forEach(sez => {
                    $('#selectNewDatiUtiliRowSezione').append(`<option>${sez}</option>`);
                });
            }

            function filterDatiUtiliSubsectionsRow(section) {
                $('#selectNewDatiUtiliRowSottosezione').html('<option>Seleziona Sottosezione</option>');
                sezioniDatiUtiliList[section].forEach(ssez => {
                    $('#selectNewDatiUtiliRowSottosezione').append(`<option>${ssez}</option>`);
                });
            }

            function clearInputRow() {
                $('#selectNewDatiUtiliRowSezione').prop('selectedIndex', 0);
                $('#selectNewDatiUtiliRowSottosezione').prop('selectedIndex', -1);
                $('#newDatiUtiliRowSottosezione').val('');
                $('#newDatiUtiliRowOrdinamento').val('');
                $('#newDatiUtiliRowNomeArticolo').val('');
                $('#newDatiUtiliRowFormula').val('');
                $('#newDatiUtiliRowNota').val('');
            }

            $(document).ready(function () {
                clearInputRow();
                renderDatiUtiliSectionFilterRow();
                $('#selectNewDatiUtiliRowSezione').change(function () {
                    const section = $('#selectNewDatiUtiliRowSezione').val();
                    if (section !== 'Seleziona Sezione') {
                        filterDatiUtiliSubsectionsRow(section);
                    } else {
                        $('#selectNewDatiUtiliRowSottosezione').html('');
                    }
                });
                $('.subsectionButtonGroup1').click(function () {
                    $('#selectNewDatiUtiliRowSottosezione').show();
                    $('#newDatiUtiliRowSottosezione').attr('style', 'display:none');
                });
                $('.subsectionButtonGroup2').click(function () {
                    $('#newDatiUtiliRowSottosezione').attr('style', 'display:block');
                    $('#selectNewDatiUtiliRowSottosezione').hide();
                });
                $('#addNewDatiUtiliRowButton').click(function () {
                    {
                        let nome_articolo = $('#newDatiUtiliRowNomeArticolo').val();
                        let ordinamento = $('#newDatiUtiliRowOrdinamento').val();
                        let formula = $('#newDatiUtiliRowFormula').val();
                        let nota = $('#newDatiUtiliRowNota').val();
                        let sezione = '';
                        if (sezione !== 'Seleziona Sezione') {
                            sezione = $('#selectNewDatiUtiliRowSezione').val();
                        }
                        let sottosezione = '';
                        if ($('#selectNewDatiUtiliRowSottosezione').val() != null && $('#selectNewDatiUtiliRowSottosezione').val() !== 'Seleziona Sottosezione') {
                            sottosezione = $('#selectNewDatiUtiliRowSottosezione').val();
                        } else if ($('#newDatiUtiliRowSottosezione').val() != null) {
                            sottosezione = $('#newDatiUtiliRowSottosezione').val();
                        }

                        let document_name = $('#inputDocumentName').val();
                        let anno = $('#inputYear').val();

                        if (articoli_dati_utili.find(art => art.nome_articolo === nome_articolo) === undefined && sezione !== 'Seleziona Sezione' && sottosezione!=='Seleziona Sottosezione') {
                            const payload = {
                                nome_articolo,
                                ordinamento,
                                formula,
                                nota,
                                sezione,
                                sottosezione,
                                document_name,
                                anno
                            }
                            console.log(payload)
                            $.ajax({
                                url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/newdatiutilirow',
                                data: payload,
                                type: "POST",
                                success: function (response) {
                                    $("#addDatiUtiliRowModal").modal('hide');
                                    articoli_dati_utili.push({...payload, id: response['id']});
                                    renderDatiUtiliDataTable();
                                    $(".alert-new-row-success").show();
                                    $(".alert-new-row-success").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-new-row-success").slideUp(500);
                                    });
                                    clearInputRow();
                                },
                                error: function (response) {
                                    $("#addDatiUtiliRowModal").modal('hide');
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
        $results_articoli = $data->getArticoliDatiUtili('Emanuele Lesca');
        $formulas = $data->getFormulas('Emanuele Lesca');
        $ids_articolo = $data->getIdsArticoli('Emanuele Lesca');
        $array = $formulas + $ids_articolo;

        //TODO filter per togliere i valori vuoti
//        for( $i=0; $i<count($results_articoli); $i++){
//            if($results_articoli[$i]['Formula']===null){
//                array_splice($results_articoli[$i], 'Formula', 1);
//            }
//            echo '<pre>';
//            print_r($results_articoli[$i]);
//            echo '</pre>';
//        }
//

        if ($results_articoli[0]['editable'] == '1') {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addDatiUtiliRowModal" id="idAddDatiUtiliRow">Aggiungi riga dati utili
            </button>
            <?php
        } else {
            ?>
            <button class="btn btn-outline-primary" data-toggle="modal"
                    data-target="#addDatiUtiliRowModal" id="idAddDatiUtiliRow" disabled>Aggiungi riga dati utili
            </button>
            <?php
        }
        ?>
        <div class="modal fade" id="addDatiUtiliRowModal" tabindex="-1"
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
                            <label for="selectDatiUtiliRowSezione"><b>Sezione:</b></label>
                            <select class="custom-select" id="selectNewDatiUtiliRowSezione">
                            </select>
                            <small id="errorSection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>
                        <div class="form-group" id="divSelectNewDatiUtiliRowSottosezione">
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
                                <select class="custom-select" id="selectNewDatiUtiliRowSottosezione">
                                </select>
                                <small id="errorSubsection" class="form-text text-danger" style="display: none">Campo
                                    Obbligatorio</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="newDatiUtiliRowSottosezione" style="display:none">
                            <small id="errorSubsection" class="form-text text-danger" style="display: none">Campo
                                Obbligatorio</small>
                        </div>

                        <div class="form-group">
                            <label for="inputOrdinamento"><b>Ordinamento:</b></label>
                            <input type="text" class="form-control" id="newDatiUtiliRowOrdinamento"></div>
                        <div class="form-group">
                            <label for="inputNomeArticolo"><b>Articolo:</b> </label>
                            <input type="text" class="form-control" id="newDatiUtiliRowNomeArticolo">
                        </div>
                        <div class="form-group">
                            <label for="idFormula"><b>Formula: </b></label>

                            <select name="newDatiUtiliRowFormula" id="newDatiUtiliRowFormula">
                                <?php
                                foreach ($array as $item) {
                                    ?>
                                    <option><?= $item[0] ?></option>
                                <?php }
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="inputNota"><b>Nota:</b> </label>
                            <textarea class="form-control" id="newDatiUtiliRowNota"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="addNewDatiUtiliRowButton">Aggiungi riga</button>
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