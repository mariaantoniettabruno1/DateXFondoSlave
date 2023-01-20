<?php

use dateXFondoPlugin\DateXFondoCommon;
use dateXFondoPlugin\MasterTemplateStopEditingButton;
class MasterModelloFondoCostituzione
{
    public static function render_scripts()
    {
        ?>
        <script>
            let id = 0;

            function renderDataTable() {
                let filteredDocArticoli = articoli;
                let preventivo = '';
                let edit_button = '';
                let delete_button = '';
                for (let i = 0; i < sezioni.length; i++) {
                    $('#dataCostituzioneDocumentTableBody' + i).html('');
                    filteredDocArticoli = filteredDocArticoli.filter(art => art.sezione === sezioni[i])
                    filteredDocArticoli.forEach(art => {
                        if (art.preventivo !== undefined)
                            preventivo = art.preventivo;
                        if (Number(art.editable) === 0) {
                            edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editModal" disabled><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteModal" disabled><i class="fa-solid fa-trash"></i></button>`;
                        } else {
                            edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editModal"><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteModal"><i class="fa-solid fa-trash"></i></button>`;
                        }
                        $('#dataCostituzioneDocumentTableBody' + i).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${art.sottosezione}</td>
                                       <td>${art.nome_articolo}</td>
                                       <td>${preventivo}</td>

                     <td><div class="row pr-3">
                <div class="col-3">${edit_button}</div>
                <div class="col-3">${delete_button}</div>
                                    </td>
                                 </tr>
                             `);

                    });
                    filteredDocArticoli = articoli;
                }
                $('.btn-delete-row').click(function () {
                    id = $(this).attr('data-id');
                    console.log(id)

                });
                $('.btn-edit-row').click(function () {
                    id = $(this).attr('data-id');
                    const articolo = articoli.find(art => Number(art.id) === Number(id))
                    if (!articolo) return;
                    $('#idOrdinamento').val(articolo.ordinamento)
                    $('#idNomeArticolo').val(articolo.nome_articolo)
                    $('#idPreventivo').val(articolo.preventivo)

                });

            }

            function ExportExcel(index) {
                let worksheet_tmp1, a, sectionTable;
                let temp = [''];
                for (let i = 0; i < index; i++) {
                    sectionTable = document.getElementById('exportableTableCostituzione' + i);
                    worksheet_tmp1 = XLSX.utils.table_to_sheet(sectionTable);
                    a = XLSX.utils.sheet_to_json(worksheet_tmp1, {header: 1})
                    temp = temp.concat(['']).concat(a)
                }

                let worksheet_costituzione = XLSX.utils.json_to_sheet(temp, {skipHeader: true})
                let worksheet_utilizzo = ExportUtilizzoSheetOnExcel();
                let worksheet_dati_utili = ExportDatiUtilioSheetOnExcel();
                const new_workbook = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(new_workbook, worksheet_costituzione, "Costituzione")
                XLSX.utils.book_append_sheet(new_workbook, worksheet_utilizzo, "Utilizzo")
                XLSX.utils.book_append_sheet(new_workbook, worksheet_dati_utili, "Dati Utili")
                XLSX.writeFile(new_workbook, ('xlsx' + 'Dasein1.xlsx'))
            }

            function renderEditArticle() {
                const updateArticolo = articoli.find(art => art.id === Number(id));
                updateArticolo.nome_articolo = $('#idNomeArticolo').val();
                updateArticolo.ordinamento = $('#idOrdinamento').val();
                updateArticolo.preventivo = $('#idPreventivo').val();
            }


            $(document).ready(function () {
                renderDataTable();


                $('#deleteRowButton').click(function () {
                    const payload = {
                        id
                    }

                    $.ajax({

                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/document/row/del',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteModal").modal('hide');
                            articoli = articoli.filter(art => Number(art.id) !== Number(id));
                            renderDataTable();
                            $(".alert-delete-success").show();
                            $(".alert-delete-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-success").slideUp(500);
                            });

                        },
                        error: function (response) {
                            console.error(response);
                            $("#deleteModal").modal('hide');
                            $(".alert-delete-wrong").show();
                            $(".alert-delete-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-wrong").slideUp(500);
                            });
                        }
                    });
                });
                $('#editRowButton').click(function () {
                    let nome_articolo = $('#idNomeArticolo').val();
                    let ordinamento = $('#idOrdinamento').val();
                    let preventivo = $('#idPreventivo').val();


                    const payload = {
                        id,
                        nome_articolo,
                        preventivo,
                        ordinamento

                    }
                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/document/row',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#editModal").modal('hide');
                            renderEditArticle();
                            renderDataTable();
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
        <?php

    }

    public static function render()
    {

        $data = new DocumentRepository();
        $tot_sezioni = $data->getSezioni('Emanuele Lesca');
        $formulas = $data->getFormulas('Emanuele Lesca');
        $ids_articolo = $data->getIdsArticoli('Emanuele Lesca');
        $array = $formulas + $ids_articolo;

        ?>
        <div class="accordion mt-2 col" id="accordionCostituzioneDocumentTable">
            <?php
            $section_index = 0;
            foreach ($tot_sezioni as $sezione) {
                ?>
                <div class="card" id="costituzioneDocumentCard">
                    <div class="card-header" id="headingCostituzioneDocument<?= $section_index ?>">
                        <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                data-target="#collapseCostituzioneDocument<?= $section_index ?>"
                                aria-expanded="false" aria-controls="collapseCostituzioneDocument<?= $section_index ?>"
                                data-section="<?= $sezione['sezione'] ?>">
                            <?= $sezione['sezione'] ?>
                        </button>
                    </div>
                    <div id="collapseCostituzioneDocument<?= $section_index ?>" class="collapse"
                         aria-labelledby="headingCostituzioneDocument<?= $section_index ?>"
                         data-parent="#accordionCostituzioneDocumentTable">
                        <div class="card-body ">
                            <table class="table datatable_costituzione" id="exportableTableCostituzione<?= $section_index ?>">
                                <thead>
                                <tr>
                                    <th>Ordinamento</th>
                                    <th>Sottosezione</th>
                                    <th>Nome Articolo</th>
                                    <th>Preventivo</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody id="dataCostituzioneDocumentTableBody<?= $section_index ?>">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                $section_index++;
            }
            ?>
        </div>
        <div class="container">
            <div class="row d-flex flex-row-reverse ">
                    <div class="p-2">
                        <button class="btn btn-outline-primary" onclick="ExportExcel(<?= $section_index ?>)">Genera Foglio
                            Excel
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Cancella riga </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi veramente eliminare questa riga?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="deleteRowButton">Cancella</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1"
             role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifica riga del documento:</h5>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <label>Ordinamento</label>
                        <input type="text" class="form-control" id="idOrdinamento">

                        <label>Nome Articolo</label>
                        <input type="text" class="form-control" id="idNomeArticolo">

                        <label>Preventivo</label>

                        <select name="preventivo" id="idPreventivo">
                            <?php
                            foreach ($array as $item) {
                                ?>
                                <option value="GFG_2" selected="selected">
                                <option><?= $item[0] ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="editRowButton">Salva Modifica</button>
                    </div>
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
        </div>
        <div class="alert alert-success alert-delete-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Cancellazione riga andata a buon fine!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-delete-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Cancellazione riga non riuscita
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <?php
        self::render_scripts();

    }
}