<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloRegioniCostituzioneTable
{
    public static function render_scripts()
    {
        ?>
        <script>
            let id = 0;
            let filteredArticoli = articoli_costituzione;
            function renderRegioniDocumentDataTable(section,subsection){
                let index = Object.keys(sezioni_costituzione).indexOf(section);
                $('#dataRegioniDocumentTableBody' + index).html('');
                filteredArticoli = articoli_costituzione;
                filteredArticoli = filteredArticoli.filter(art => art.sezione === section)
                if (subsection)
                    filteredArticoli = filteredArticoli.filter(art => art.sottosezione === subsection)
                let button = '';
                let delete_button = '';
                let codice = '';
                let importo = '';
                let nota = '';
                let nome_articolo = '';
                filteredArticoli.forEach(art => {

                    nota = art.nota ?? '';
                    importo = art.importo ?? '';
                    codice = art.codice ?? '';
                    nome_articolo = art.nome_articolo ?? '';


                        if (Number(art.editable) === 0) {
                            button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editRegioniDocumentModal" disabled><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteRegioniDocumentModal" disabled><i class="fa-solid fa-trash"></i></button>`;
                        } else {
                            button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editRegioniDocumentModal"><i class="fa-solid fa-pen"></i></button>`;
                            delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteRegioniDocumentModal"><i class="fa-solid fa-trash"></i></button>`;
                        }



                    $('#dataRegioniDocumentTableBody' + index).append(`
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
                    id = $(this).attr('data-id');
                    console.log(id)

                });
                $('.btn-edit-row').click(function () {
                    id = $(this).attr('data-id');
                    const articolo = articoli_costituzione.find(art => Number(art.id) === Number(id))
                    if (!articolo) return;
                    $('#idRegioniOrdinamento').val(articolo.ordinamento)
                    $('#idRegioniNomeArticolo').val(articolo.nome_articolo)
                    $('#idRegioniCodice').val(articolo.codice)
                    $('#idRegioniImporto').val(articolo.importo)
                    $('#idRegioniNota').val(articolo.nota)
                });
            }
            function resetRegioniSubsection() {
                let subsection = $('.class-template-sottosezione').val();
                if (subsection !== 'Seleziona Sottosezione') {
                    $('.class-template-sottosezione').val('Seleziona Sottosezione');
                }
            }
            function renderEditRegioniArticle() {

                const updateArticolo = articoli_costituzione.find(art => art.id === Number(id));
                updateArticolo.ordinamento = $('#idRegioniOrdinamento').val();
                updateArticolo.nome_articolo = $('#idRegioniNomeArticolo').val();
                updateArticolo.codice = $('#idRegioniCodice').val();
                updateArticolo.importo = $('#idRegioniImporto').val();
                updateArticolo.nota = $('#idRegioniNota').val();

            }
            function ExportCostituzioneToExcel() {

                let worksheet_tmp1, a, sectionTable,worksheet;
                let temp = [''];
                let index = Object.keys(sezioni_costituzione).length;
                for (let i = 0; i < index; i++) {
                    sectionTable = document.getElementById('exportableTableCost' + i);
                    worksheet_tmp1 = XLSX.utils.table_to_sheet(sectionTable);
                    a = XLSX.utils.sheet_to_json(worksheet_tmp1, {header: 1})
                    temp = temp.concat(['']).concat(a)
                }

                worksheet  = XLSX.utils.json_to_sheet(temp, {skipHeader: true})
                return worksheet;
            }
            $(document).ready(function () {

                renderRegioniDocumentDataTable();
                resetRegioniSubsection();
                let section = '';
                $('.class-accordion-button').click(function () {
                    section = $(this).attr('data-section');

                    renderRegioniDocumentDataTable(section);
                    $('.class-template-sottosezione').change(function () {
                        let subsection = $(this).val();
                        if (subsection !== 'Seleziona Sottosezione') {
                            renderRegioniDocumentDataTable(section, subsection);
                        } else {
                            renderRegioniDocumentDataTable(section);
                        }
                    });
                });

                $('#editRegioniRowButton').click(function () {
                    let ordinamento = $('#idRegioniOrdinamento').val();
                    let nome_articolo = $('#idRegioniNomeArticolo').val();
                    let codice = $('#idRegioniCodice').val();
                    let importo = $('#idRegioniImporto').val();
                    let nota = $('#idRegioniNota').val();

                    const payload = {
                        id,
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
                            $("#editRegioniDocumentModal").modal('hide');
                            renderEditRegioniArticle();
                            renderRegioniDocumentDataTable(section);
                            console.log(section);
                            $(".alert-edit-success").show();
                            $(".alert-edit-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-success").slideUp(500);
                            });
                        },
                        error: function (response) {
                            console.error(response);
                            $("#editRegioniDocumentModal").modal('hide');
                            $(".alert-edit-wrong").show();
                            $(".alert-edit-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-edit-wrong").slideUp(500);
                            });
                        }
                    });
                });

                $('#deleteRegioniRowButton').click(function () {
                    const payload = {
                        id
                    }

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/delregionirow',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteRegioniDocumentModal").modal('hide');
                            articoli_costituzione = articoli_costituzione.filter(art => Number(art.id) !== Number(id));
                            renderRegioniDocumentDataTable(section);
                            $(".alert-delete-success").show();
                            $(".alert-delete-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-success").slideUp(500);
                            });

                        },
                        error: function (response) {
                            console.error(response);
                            $("#deleteRegioniDocumentModal").modal('hide');
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
        $results_articoli = $data->getCostituzioneArticoli($_GET['editor_name']);
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
         <div class="accordion mt-2 col" id="accordionRegioniDocumentTable">
            <?php
            $section_index = 0;
            foreach ($tot_array as $sezione => $sottosezioni) {
                ?>
                <div class="card" id="regioniDocumentCard">
                    <div class="card-header" id="headingRegioniDocument<?= $section_index ?>">
                        <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                data-target="#collapseRegioniDocument<?= $section_index ?>"
                                aria-expanded="false" aria-controls="collapseRegioniDocument<?= $section_index ?>"
                                data-section="<?= $sezione ?>">
                            <?= $sezione ?>
                        </button>
                    </div>
                    <div id="collapseRegioniDocument<?= $section_index ?>" class="collapse"
                         aria-labelledby="headingRegioniDocument<?= $section_index ?>"
                         data-parent="#accordionRegioniDocumentTable">
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
                            <table class="table regionidocumenttable" id="exportableTableCost<?= $section_index ?>">
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
                                <tbody id="dataRegioniDocumentTableBody<?= $section_index ?>">
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
        <div class="modal fade" id="deleteRegioniDocumentModal" tabindex="-1" role="dialog" aria-labelledby="deleteRegioniDocumentModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteRegioniDocumentModalLabel">Cancella riga </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi veramente eliminare questa riga?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="deleteRegioniRowButton">Cancella</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editRegioniDocumentModal" tabindex="-1"
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
                        <input type="text" class="form-control" id="idRegioniOrdinamento">

                        <label>Nome Articolo</label>
                        <input type="text" class="form-control" id="idRegioniNomeArticolo">

                        <label>Codice</label>
                        <select name="codice" id="idRegioniCodice">
                            <?php
                            foreach ($array as $item) {
                                ?>
                                <option value="GFG_2" selected="selected">
                                <option><?= $item[0] ?></option>
                            <?php }
                            ?>
                        </select>

                        <label>Importo</label>
                        <input type="text" class="form-control" id="idRegioniImporto">


                        <label>Nota</label>
                        <textarea class="form-control"
                                  id="idRegioniNota"></textarea>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="editRegioniRowButton">Salva Modifica</button>
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