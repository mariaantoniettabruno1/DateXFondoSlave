<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloFondoDatiUtili
{
    public static function render_scripts()
    {
        ?>
        <script>
            let id_dati_utili = 0;

            function renderDatiUtiliDataTable() {
                let filteredDatiUtiliArticoli = articoli_dati_utili;
                let nota = '';
                let = formula = '';
                let edit_button = '';
                let delete_button = '';
                for (let i = 0; i < sezioni_dati_utili.length; i++) {
                    $('#dataDatiUtiliDocumentTableBody' + i).html('');
                    filteredDatiUtiliArticoli = filteredDatiUtiliArticoli.filter(art => art.sezione === sezioni_dati_utili[i])
                    filteredDatiUtiliArticoli.forEach(art => {
                        if (art.formula !== undefined)
                            formula = art.formula;
                        if (art.nota !== undefined)
                            nota = art.nota;
                        if (Number(art.editable) === 0) {
                            edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editDatiUtiliModal" disabled><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteDatiUtiliModal" disabled><i class="fa-solid fa-trash"></i></button>`;
                        } else {
                            edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editDatiUtiliModal"><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteDatiUtiliModal"><i class="fa-solid fa-trash"></i></button>`;
                        }
                        $('#dataDatiUtiliDocumentTableBody' + i).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${art.sottosezione}</td>
                                       <td>${art.nome_articolo}</td>
                                       <td>${formula}</td>
                                       <td>${nota}</td>

                     <td><div class="row pr-3">
                <div class="col-3">${edit_button}</div>
                <div class="col-3">${delete_button}</div>
                                    </td>
                                 </tr>
                             `);

                    });
                    filteredDatiUtiliArticoli = articoli_dati_utili;
                }

                $('.btn-delete-row').click(function () {
                    id_dati_utili = $(this).attr('data-id');
                });
                $('.btn-edit-row').click(function () {
                    id_dati_utili = $(this).attr('data-id');
                    const articolo = articoli_dati_utili.find(art => Number(art.id) === Number(id_dati_utili))
                    if (!articolo) return;
                    $('#idDatiUtiliOrdinamento').val(articolo.ordinamento)
                    $('#idDatiUtiliNomeArticolo').val(articolo.nome_articolo)
                    $('#idDatiUtiliFormula').val(articolo.formula)
                    $('#idDatiUtiliNota').val(articolo.nota)

                });


            }

            function ExportDatiUtilioSheetOnExcel() {

                let worksheet_tmp1, a, sectionTable,worksheet;
                let temp = [''];
                let index = sezioni_dati_utili.length;
                for (let i = 0; i < index; i++) {
                    sectionTable = document.getElementById('exportableTableDatiUtili' + i);
                    worksheet_tmp1 = XLSX.utils.table_to_sheet(sectionTable);
                    a = XLSX.utils.sheet_to_json(worksheet_tmp1, {header: 1})
                    temp = temp.concat(['']).concat(a)
                }

                worksheet  = XLSX.utils.json_to_sheet(temp, {skipHeader: true})
                return worksheet;
            }

            function renderEditArticle() {
                const updateArticolo = articoli_dati_utili.find(art => art.id === Number(id_dati_utili));
                updateArticolo.nome_articolo = $('#idDatiUtiliNomeArticolo').val();
                updateArticolo.ordinamento = $('#idDatiUtiliOrdinamento').val();
                updateArticolo.formula = $('#idDatiUtiliFormula').val();
                updateArticolo.nota = $('#idDatiUtiliNota').val();
            }


            $(document).ready(function () {
                renderDatiUtiliDataTable();


                $('#deleteDatiUtiliRowButton').click(function () {
                    const payload = {
                        id_dati_utili
                    }

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/document/datiutili/row/del',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteDatiUtiliModal").modal('hide');
                            articoli_dati_utili = articoli_dati_utili.filter(art => Number(art.id) !== Number(id_dati_utili));
                            renderDatiUtiliDataTable();
                            $(".alert-delete-success").show();
                            $(".alert-delete-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-success").slideUp(500);
                            });

                        },
                        error: function (response) {
                            console.error(response);
                            $("#deleteDatiUtiliModal").modal('hide');
                            $(".alert-delete-wrong").show();
                            $(".alert-delete-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-wrong").slideUp(500);
                            });
                        }
                    });
                });
                $('#editDatiUtiliRowButton').click(function () {
                    let nome_articolo = $('#idDatiUtiliNomeArticolo').val();
                    let ordinamento = $('#idDatiUtiliOrdinamento').val();
                    let formula = $('#idDatiUtiliFormula').val();
                    let nota = $('#idDatiUtiliNota').val();


                    const payload = {
                        id_dati_utili,
                        nome_articolo,
                        formula,
                        ordinamento,
                        nota

                    }
                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/datiutili/document/row',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#editDatiUtiliModal").modal('hide');
                            renderEditArticle();
                            renderDatiUtiliDataTable();
                            $(".alert-edit-success").show();
                            $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $("#editDatiUtiliModal").modal('hide');
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
        $tot_sezioni = $data->getSezioniDatiUtili('Emanuele Lesca');
        $formulas = $data->getFormulas('Emanuele Lesca');
        $ids_articolo = $data->getIdsArticoli('Emanuele Lesca');
        $array = $formulas + $ids_articolo;

        ?>
        <div class="accordion mt-2 col" id="accordionDatiUtiliDocumentTable">
            <?php
            $section_index = 0;
            foreach ($tot_sezioni as $sezione) {
                ?>
                <div class="card" id="datiUtiliDocumentCard">
                    <div class="card-header" id="headingDatiUtiliDocument<?= $section_index ?>">
                        <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                data-target="#collapseDatiUtiliDocument<?= $section_index ?>"
                                aria-expanded="false" aria-controls="collapseDatiUtiliDocument<?= $section_index ?>"
                                data-section="<?= $sezione['sezione'] ?>">
                            <?= $sezione['sezione'] ?>
                        </button>
                    </div>
                    <div id="collapseDatiUtiliDocument<?= $section_index ?>" class="collapse"
                         aria-labelledby="headingDatiUtiliDocument<?= $section_index ?>"
                         data-parent="#accordionDatiUtiliDocumentTable">
                        <div class="card-body ">
                            <table class="table datatable_dati_utili" id="exportableTableDatiUtili<?= $section_index ?>">
                                <thead>
                                <tr>
                                    <th>Ordinamento</th>
                                    <th>Sottosezione</th>
                                    <th>Nome Articolo</th>
                                    <th>formula</th>
                                    <th>nota</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody id="dataDatiUtiliDocumentTableBody<?= $section_index ?>">
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
        <div class="modal fade" id="deleteDatiUtiliModal" tabindex="-1" role="dialog" aria-labelledby="deleteDatiUtiliModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteDatiUtiliModalLabel">Cancella riga </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi veramente eliminare questa riga?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="deleteDatiUtiliRowButton">Cancella</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editDatiUtiliModal" tabindex="-1"
             role="dialog"
             aria-labelledby="myDatiUtiliModalLabel" aria-hidden="true">
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
                        <input type="text" class="form-control" id="idDatiUtiliOrdinamento">

                        <label>Nome Articolo</label>
                        <input type="text" class="form-control" id="idDatiUtiliNomeArticolo">

                        <label>Formula</label>

                        <select name="formula" id="idDatiUtiliFormula">
                            <?php
                            foreach ($array as $item) {
                                ?>
                                <option value="GFG_2" selected="selected">
                                <option><?= $item[0] ?></option>
                            <?php }
                            ?>
                        </select>
                        <label>Nota</label>
                        <textarea class="form-control" id="idDatiUtiliNota"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="editDatiUtiliRowButton">Salva Modifica</button>
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