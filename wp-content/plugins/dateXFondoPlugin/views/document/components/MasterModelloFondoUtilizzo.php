<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloFondoUtilizzo
{
    public static function render_scripts()
    {
        ?>
        <script>
            let id_utilizzo = 0;

            function renderUtilizzoDataTable() {
                let filteredUtilizzoArticoli = articoli_utilizzo;
                let preventivo = '';
                let consuntivo = '';
                let edit_button = '';
                let delete_button = '';
                for (let i = 0; i < sezioni_utilizzo.length; i++) {
                    $('#dataUtilizzoDocumentTableBody' + i).html('');
                    filteredUtilizzoArticoli = filteredUtilizzoArticoli.filter(art => art.sezione === sezioni_utilizzo[i])
                    filteredUtilizzoArticoli.forEach(art => {
                        if (art.preventivo !== undefined)
                            preventivo = art.preventivo;
                        if (art.consuntivo !== undefined)
                            consuntivo = art.consuntivo;
                        if (Number(art.editable) === 0) {
                            edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editUtilizzoModal" disabled><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteUtilizzoModal" disabled><i class="fa-solid fa-trash"></i></button>`;
                        } else {
                            edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editUtilizzoModal"><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteUtilizzoModal"><i class="fa-solid fa-trash"></i></button>`;
                        }
                        $('#dataUtilizzoDocumentTableBody' + i).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${art.nome_articolo}</td>
                                       <td>${preventivo}</td>
                                       <td>${consuntivo}</td>

                     <td><div class="row pr-3">
                <div class="col-3">${edit_button}</div>
                <div class="col-3">${delete_button}</div>
                                    </td>
                                 </tr>
                             `);

                    });
                    filteredUtilizzoArticoli = articoli_utilizzo;
                }

                 $('.btn-delete-row').click(function () {
                     id_utilizzo = $(this).attr('data-id');
                 });
                 $('.btn-edit-row').click(function () {
                     id_utilizzo = $(this).attr('data-id');
                     const articolo = articoli_utilizzo.find(art => Number(art.id) === Number(id_utilizzo))
                     if (!articolo) return;
                     $('#idUtilizzoOrdinamento').val(articolo.ordinamento)
                     $('#idUtilizzoNomeArticolo').val(articolo.nome_articolo)
                     $('#idUtilizzoPreventivo').val(articolo.preventivo)
                     $('#idUtilizzoConsuntivo').val(articolo.consuntivo)

                 });


            }

            function ExportUtilizzoSheetOnExcel() {

                let worksheet_tmp1, a, sectionTable,worksheet;
                let temp = [''];
                let index = sezioni_utilizzo.length;
                for (let i = 0; i < index; i++) {
                    sectionTable = document.getElementById('exportableTableUtilizzo' + i);
                    worksheet_tmp1 = XLSX.utils.table_to_sheet(sectionTable);
                    a = XLSX.utils.sheet_to_json(worksheet_tmp1, {header: 1})
                    temp = temp.concat(['']).concat(a)
                }

                worksheet  = XLSX.utils.json_to_sheet(temp, {skipHeader: true})
                return worksheet;
            }

             function renderEditArticle() {
                 const updateArticolo = articoli_utilizzo.find(art => art.id === Number(id_utilizzo));
                 updateArticolo.nome_articolo = $('#idUtilizzoNomeArticolo').val();
                 updateArticolo.ordinamento = $('#idUtilizzoOrdinamento').val();
                 updateArticolo.preventivo = $('#idUtilizzoPreventivo').val();
                 updateArticolo.consuntivo = $('#idUtilizzoConsuntivo').val();
             }


            $(document).ready(function () {
                renderUtilizzoDataTable();


                $('#deleteUtilizzoRowButton').click(function () {
                    const payload = {
                        id_utilizzo
                    }

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/document/utilizzo/row/del',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteUtilizzoModal").modal('hide');
                            articoli_utilizzo = articoli_utilizzo.filter(art => Number(art.id) !== Number(id_utilizzo));
                            renderUtilizzoDataTable();
                            $(".alert-delete-success").show();
                            $(".alert-delete-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-success").slideUp(500);
                            });

                        },
                        error: function (response) {
                            console.error(response);
                            $("#deleteUtilizzoModal").modal('hide');
                            $(".alert-delete-wrong").show();
                            $(".alert-delete-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-wrong").slideUp(500);
                            });
                        }
                    });
                });
                $('#editUtilizzoRowButton').click(function () {
                    let nome_articolo = $('#idUtilizzoNomeArticolo').val();
                    let ordinamento = $('#idUtilizzoOrdinamento').val();
                    let preventivo = $('#idUtilizzoPreventivo').val();
                    let consuntivo = $('#idUtilizzoConsuntivo').val();


                    const payload = {
                        id_utilizzo,
                        nome_articolo,
                        preventivo,
                        ordinamento,
                        consuntivo

                    }
                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/utilizzo/document/row',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#editUtilizzoModal").modal('hide');
                            renderEditArticle();
                            renderUtilizzoDataTable();
                            $(".alert-edit-success").show();
                            $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $("#editUtilizzoModal").modal('hide');
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
        $tot_sezioni = $data->getSezioniUtilizzo('Emanuele Lesca');
        $formulas = $data->getFormulas('Emanuele Lesca');
        $ids_articolo = $data->getIdsArticoli('Emanuele Lesca');
        $array = $formulas + $ids_articolo;

        ?>
        <div class="accordion mt-2 col" id="accordionUtilizzoDocumentTable">
            <?php
            $section_index = 0;
            foreach ($tot_sezioni as $sezione) {
                ?>
                <div class="card" id="utilizzoDocumentCard">
                    <div class="card-header" id="headingUtilizzoDocument<?= $section_index ?>">
                        <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                data-target="#collapseUtilizzoDocument<?= $section_index ?>"
                                aria-expanded="false" aria-controls="collapseUtilizzoDocument<?= $section_index ?>"
                                data-section="<?= $sezione['sezione'] ?>">
                            <?= $sezione['sezione'] ?>
                        </button>
                    </div>
                    <div id="collapseUtilizzoDocument<?= $section_index ?>" class="collapse"
                         aria-labelledby="headingUtilizzoDocument<?= $section_index ?>"
                         data-parent="#accordionUtilizzoDocumentTable">
                        <div class="card-body ">
                            <table class="table datatable_utilizzo" id="exportableTableUtilizzo<?= $section_index ?>">
                                <thead>
                                <tr>
                                    <th>Ordinamento</th>
                                    <th>Nome Articolo</th>
                                    <th>Preventivo</th>
                                    <th>Consuntivo</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody id="dataUtilizzoDocumentTableBody<?= $section_index ?>">
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
        <div class="modal fade" id="deleteUtilizzoModal" tabindex="-1" role="dialog" aria-labelledby="deleteUtilizzoModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUtilizzoModalLabel">Cancella riga </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi veramente eliminare questa riga?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="deleteUtilizzoRowButton">Cancella</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editUtilizzoModal" tabindex="-1"
             role="dialog"
             aria-labelledby="myUtilizzoModalLabel" aria-hidden="true">
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
                        <input type="text" class="form-control" id="idUtilizzoOrdinamento">

                        <label>Nome Articolo</label>
                        <input type="text" class="form-control" id="idUtilizzoNomeArticolo">

                        <label>Preventivo</label>

                        <select name="preventivo" id="idUtilizzoPreventivo">
                            <?php
                            foreach ($array as $item) {
                                ?>
                                <option value="GFG_2" selected="selected">
                                <option><?= $item[0] ?></option>
                            <?php }
                            ?>
                        </select>
                        <label>Consuntivo</label>
                        <input type="text" class="form-control" id="idUtilizzoConsuntivo">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="editUtilizzoRowButton">Salva Modifica</button>
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