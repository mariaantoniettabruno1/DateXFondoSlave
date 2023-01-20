<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloRegioniDestinazioneTable
{
    public static function render_scripts()
    {
        ?>
        <script>
            let id_destinazione = 0;
            let filteredDestinazioneArticoli = articoli_destinazione;
            function renderRegioniDestinazioneDataTable(section,subsection){
                let index = Object.keys(sezioni_destinazione).indexOf(section);
                $('#dataRegioniDestinazioneTableBody' + index).html('');
                filteredDestinazioneArticoli = articoli_destinazione;
                filteredDestinazioneArticoli = filteredDestinazioneArticoli.filter(art => art.sezione === section)
                if (subsection)
                    filteredDestinazioneArticoli = filteredDestinazioneArticoli.filter(art => art.sottosezione === subsection)
                let button = '';
                let delete_button = '';
                let codice = '';
                let importo = '';
                let nota = '';
                let nome_articolo = '';
                filteredDestinazioneArticoli.forEach(art => {

                    nota = art.nota ?? '';
                    importo = art.importo ?? '';
                    codice = art.codice ?? '';
                    nome_articolo = art.nome_articolo ?? '';


                    if (Number(art.editable) === 0) {
                        button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editRegioniDestinazioneModal" disabled><i class="fa-solid fa-pen"></i></button>`;
                        delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteRegioniDestinazioneModal" disabled><i class="fa-solid fa-trash"></i></button>`;
                    } else {
                        button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editRegioniDestinazioneModal"><i class="fa-solid fa-pen"></i></button>`;
                        delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteRegioniDestinazioneModal"><i class="fa-solid fa-trash"></i></button>`;
                    }



                    $('#dataRegioniDestinazioneTableBody' + index).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${nome_articolo}</td>
                                       <td>${codice}</td>
                                       <td>${importo}</td>
                                       <td>
                                           <span style='display:none' class="notaFull">${nota}</span>
                                           <span style="display:block" class='notaCut'>${nota.substr(0, 50).concat('...')}</span>
                                           </td>

                                       <td><div class="row pr-3">
                <div class="col-3">${button}</div>
                <div class="col-3">${delete_button}</div>
                </div></td>
                                 </tr>
                             `);
                });
                $('.notaCut').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).prev().attr("style", "display:block");
                });
                $('.notaFull').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).next().attr("style", "display:block");
                });
                $('.btn-delete-row').click(function () {
                    id_destinazione = $(this).attr('data-id');
                    console.log(id_destinazione)

                });
                $('.btn-edit-row').click(function () {
                    id_destinazione = $(this).attr('data-id');
                    const articolo = articoli_destinazione.find(art => Number(art.id) === Number(id_destinazione))
                    if (!articolo) return;
                    $('#idRegioniDestinazioneOrdinamento').val(articolo.ordinamento)
                    $('#idRegioniDestinazioneNomeArticolo').val(articolo.nome_articolo)
                    $('#idRegioniDestinazioneCodice').val(articolo.codice)
                    $('#idRegioniDestinazioneImporto').val(articolo.importo)
                    $('#idRegioniDestinazioneNota').val(articolo.nota)
                });
            }
            function resetRegioniSubsection() {
                let subsection = $('.class-template-sottosezione').val();
                if (subsection !== 'Seleziona Sottosezione') {
                    $('.class-template-sottosezione').val('Seleziona Sottosezione');
                }
            }
            function renderEditRegioniArticle() {

                const updateArticolo = articoli_destinazione.find(art => art.id === Number(id_destinazione));
                updateArticolo.ordinamento = $('#idRegioniDestinazioneOrdinamento').val();
                updateArticolo.nome_articolo = $('#idRegioniDestinazioneNomeArticolo').val();
                updateArticolo.codice = $('#idRegioniDestinazioneCodice').val();
                updateArticolo.importo = $('#idRegioniDestinazioneImporto').val();
                updateArticolo.nota = $('#idRegioniDestinazioneNota').val();

            }
            function ExportDestinazioneToExcel() {

                let worksheet_tmp1, a, sectionTable,worksheet;
                let temp = [''];
                let index = Object.keys(sezioni_destinazione).length;
                for (let i = 0; i < index; i++) {
                    sectionTable = document.getElementById('exportableTableDest' + i);
                    worksheet_tmp1 = XLSX.utils.table_to_sheet(sectionTable);
                    a = XLSX.utils.sheet_to_json(worksheet_tmp1, {header: 1})
                    temp = temp.concat(['']).concat(a)
                }

                worksheet  = XLSX.utils.json_to_sheet(temp, {skipHeader: true})
                return worksheet;
            }
            $(document).ready(function () {
                renderRegioniDestinazioneDataTable();
                resetRegioniSubsection();
                let section = '';
                $('.class-accordion-button').click(function () {
                    section = $(this).attr('data-section');

                    renderRegioniDestinazioneDataTable(section);
                    $('.class-template-sottosezione').change(function () {
                        let subsection = $(this).val();
                        if (subsection !== 'Seleziona Sottosezione') {
                            renderRegioniDestinazioneDataTable(section, subsection);
                        } else {
                            renderRegioniDestinazioneDataTable(section);
                        }
                    });
                });

                $('#editRegioniDestinazioneRowButton').click(function () {
                    let ordinamento = $('#idRegioniDestinazioneOrdinamento').val();
                    let nome_articolo = $('#idRegioniDestinazioneNomeArticolo').val();
                    let codice = $('#idRegioniDestinazioneCodice').val();
                    let importo = $('#idRegioniDestinazioneImporto').val();
                    let nota = $('#idRegioniDestinazioneNota').val();
                    const payload = {
                        id_destinazione,
                        ordinamento,
                        nome_articolo,
                        codice,
                        importo,
                        nota

                    }
                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/editregionirow',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#editRegioniDestinazioneModal").modal('hide');
                            renderEditRegioniArticle();
                            renderRegioniDestinazioneDataTable(section);
                            console.log(section);
                            $(".alert-edit-success").show();
                            $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $("#editRegioniDestinazioneModal").modal('hide');
                            $(".alert-edit-wrong").show();
                            $(".alert-edit-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-wrong").slideUp(500);
                            });
                        }
                    });
                });
                $('#deleteRegioniDestinazioneRowButton').click(function () {
                    const payload = {
                        id_destinazione
                    }

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/delregionirow',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteRegioniDestinazioneModal").modal('hide');
                            articoli_destinazione = articoli_destinazione.filter(art => Number(art.id) !== Number(id_destinazione));
                            renderRegioniDestinazioneDataTable(section);
                            $(".alert-delete-success").show();
                            $(".alert-delete-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-success").slideUp(500);
                            });

                        },
                        error: function (response) {
                            console.error(response);
                            $("#deleteRegioniDestinazioneModal").modal('hide');
                            $(".alert-delete-wrong").show();
                            $(".alert-delete-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-wrong").slideUp(500);
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
        $data = new \dateXFondoPlugin\RegioniDocumentRepository();
        $data_document = new DocumentRepository();
        $results_articoli = $data->getDestinazioneArticoli($_GET['editor_name']);
        $formulas = $data_document->getFormulas($_GET['editor_name']);
        $ids_articolo = $data_document->getIdsArticoli($_GET['editor_name']);
        $array = $formulas + $ids_articolo;
        $sezioni = [];
        $tot_array = [];
        foreach ($results_articoli as $articolo) {
            if (!in_array($articolo['sezione'], $sezioni)) {
                array_push($sezioni, $articolo['sezione']);
                $tot_array = array_fill_keys($sezioni, []);
            }
        }

        foreach ($tot_array as $key => $value) {
            foreach ($results_articoli as $articolo) {
                if ($key === $articolo['sezione'] && array_search($articolo['sottosezione'], $tot_array[$key]) === false) {
                    array_push($tot_array[$key], $articolo['sottosezione']);
                }
            }
        }
        ?>
        <div class="accordion mt-2 col" id="accordionRegioniDestinazioneTable">
            <?php
            $section_index = 0;
            foreach ($tot_array as $sezione => $sottosezioni) {
                ?>
                <div class="card" id="regioniDestinazioneCard">
                    <div class="card-header" id="headingRegioniDestinazione<?= $section_index ?>">
                        <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                data-target="#collapseRegioniDestinazione<?= $section_index ?>"
                                aria-expanded="false" aria-controls="collapseRegioniDestinazione<?= $section_index ?>"
                                data-section="<?= $sezione ?>">
                            <?= $sezione ?>
                        </button>
                    </div>
                    <div id="collapseRegioniDestinazione<?= $section_index ?>" class="collapse"
                         aria-labelledby="headingRegioniDestinazione<?= $section_index ?>"
                         data-parent="#accordionRegioniDestinazioneTable">
                        <div class="card-body">
                            <div class="row pb-2 pt-2">
                                <div class="col-3">
                                    <select class="custom-select class-template-sottosezione"
                                            id="select <?= $sezione ?>">
                                        <option selected>Seleziona Sottosezione</option>
                                        <?php
                                        foreach ($sottosezioni as $sottosezione) {
                                            ?>
                                            <option><?= $sottosezione ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <table class="table regionidestinazionetable" id="exportableTableDest<?= $section_index ?>">
                                <thead>
                                <tr>
                                    <th>Ordinamento</th>
                                    <th style="width: 140px">Nome Articolo</th>
                                    <th style="width: 170px">Codice</th>
                                    <th style="width: 175px">Importo</th>
                                    <th>Nota</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody id="dataRegioniDestinazioneTableBody<?= $section_index ?>">
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
        <div class="modal fade" id="deleteRegioniDestinazioneModal" tabindex="-1" role="dialog" aria-labelledby="deleteRegioniDestinazioneModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteRegioniDestinazioneModalLabel">Cancella riga </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi veramente eliminare questa riga?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="deleteRegioniDestinazioneRowButton">Cancella</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editRegioniDestinazioneModal" tabindex="-1"
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
                        <input type="text" class="form-control" id="idRegioniDestinazioneOrdinamento">

                        <label>Nome Articolo</label>
                        <input type="text" class="form-control" id="idRegioniDestinazioneNomeArticolo">

                        <label>Codice</label>
                        <select name="codice" id="idRegioniDestinazioneCodice">
                            <?php
                            foreach ($array as $item) {
                                ?>
                                <option value="GFG_2" selected="selected">
                                <option><?= $item[0] ?></option>
                            <?php }
                            ?>
                        </select>

                        <label>Importo</label>
                        <input type="text" class="form-control" id="idRegioniDestinazioneImporto">


                        <label>Nota</label>
                        <textarea class="form-control"
                                  id="idRegioniDestinazioneNota"></textarea>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="editRegioniDestinazioneRowButton">Salva Modifica</button>
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