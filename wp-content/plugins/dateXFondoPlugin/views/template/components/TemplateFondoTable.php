<?php

namespace dateXFondoPlugin;

class TemplateFondoTable
{
    public static function render_scripts()
    {
        ?>

        <script>

            let id = 0;
            let filteredArticoli = articoli;
            let heredity = null;


            function renderDataTable(section, subsection) {
                let index = Object.keys(sezioni).indexOf(section);
                $('#dataTemplateTableBody' + index).html('');
                filteredArticoli = articoli;
                filteredArticoli = filteredArticoli.filter(art => art.sezione === section)
                if (subsection)
                    filteredArticoli = filteredArticoli.filter(art => art.sottosezione === subsection)
                let delete_button = '';
                let edit_button = '';
                let nota = '';
                let id_articolo = '';
                let descrizione = '';
                let sottotitolo = '';
                let link = '';
                let nome_articolo = '';
                let valore = '';
                let valore_precedente = '';
                let link_button = '';


                filteredArticoli.forEach(art => {

                    nota = art.nota ?? '';
                    id_articolo = art.id_articolo ?? '';
                    descrizione = art.descrizione_articolo ?? '';
                    sottotitolo = art.sottotitolo_articolo ?? '';
                    link = art.link ?? '';
                    nome_articolo = art.nome_articolo ?? '';

                    if (art.valore === '' || art.valore === undefined || art.valore === null) {
                        valore = `<medium  class="form-text text-danger">Valore obbligatorio, per favore inseriscilo.</medium>`;
                    } else {
                        valore = art.valore
                    }

                    valore_precedente = art.valore_anno_precedente ?? '';

                    if (art.row_type === 'special') {
                        delete_button = ` <button class="btn btn-link btn-delete-row" data-id='${art.id}' data-toggle="modal" data-target="#deleteModal"><i class="fa-solid fa-trash"></i></button>`;
                    }


                    if (art.link !== null) {
                        link_button = ` <button class="btn btn-link btn-art-link" data-link='${art.link}'><i class="fa-solid fa-arrow-up-right-from-square"></i></button>`;
                    }
                    else{
                        link_button='';
                    }
                    //sistemare meglio
                    if(new Date().getFullYear()!==art.anno){
                        edit_button = '';
                    }
                    else{
                        // if(art.version !== 0){
                        //     edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editModal"><i class="fa-solid fa-pen"></i></button>`;
                        // }
                        // else{
                        //     edit_button = '';
                        // }
                    }
                    edit_button = ` <button class="btn btn-link btn-edit-row" data-id='${art.id}' data-toggle="modal" data-target="#editModal"><i class="fa-solid fa-pen"></i></button>`;

                    $('#dataTemplateTableBody' + index).append(`
                                 <tr>
                                       <td>${nome_articolo}</td>
                                       <td>
                                           <span style='display:none' class="sottotitoloFull">${sottotitolo}</span>
                                           <span style="display:block" class='sottotitoloCut'>${sottotitolo.substr(0, 50).concat('...')}</span>
                                           </td>
                                        <td>
                                           <span style='display:none' class="descrizioneFull">${descrizione}</span>
                                           <span style="display:block" class='descrizioneCut'>${descrizione.substr(0, 50).concat('...')}</span>
                                        </td>
                                       <td>${nota}</td>
                                       <td>${valore}</td>
                                       <td>${valore_precedente}</td>
                                       <td>
                                       <div class="row pr-3">
                                       <div class="col-8">${link}</div>
                                       <div class="col-2">
${link_button}</div>
</div>
</td>
                                       <td><div class="row pr-3">
                <div class="col-3">${edit_button}</div>
                <div class="col-3">${delete_button}</div>
                </div></td>
                                 </tr>
                             `);
                });
                $('.sottotitoloCut').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).prev().attr("style", "display:block");
                });
                $('.sottotitoloFull').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).next().attr("style", "display:block");
                });
                $('.descrizioneCut').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).prev().attr("style", "display:block");
                });
                $('.descrizioneFull').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).next().attr("style", "display:block");
                });
                $('.btn-delete-row').click(function () {
                    id = $(this).attr('data-id');
                    console.log(id)

                });
                $('.btn-art-link').click(function () {
                    var url = '<?= DateXFondoCommon::get_website_url() ?>/date-doc/articoli/' + $(this).attr('data-link');
                    window.open(url, '_blank');
                });
                $('.btn-edit-row').click(function () {
                    id = $(this).attr('data-id');
                    const articolo = articoli.find(art => Number(art.id) === Number(id))
                    if (!articolo) return;
                    $('#idValore').val(articolo.valore)
                    $('#idNotaArticolo').val(articolo.nota)
                    $('#idValorePrecedente').val(articolo.valore_anno_precedente)

                });

            }


            function resetSubsection() {
                let subsection = $('.class-template-sottosezione').val();
                if (subsection !== 'Seleziona Sottosezione') {
                    $('.class-template-sottosezione').val('Seleziona Sottosezione');
                }
            }

            function renderEditArticle() {

                const updateArticolo = articoli.find(art => art.id === Number(id));
                updateArticolo.valore = $('#idValore').val();
                updateArticolo.valore_anno_precedente = $('#idValorePrecedente').val();
                updateArticolo.nota = $('#idNotaArticolo').val();

            }

            $(document).ready(function () {

                renderDataTable();
                resetSubsection();
                let section = '';
                $('.class-accordion-button').click(function () {
                    section = $(this).attr('data-section');

                    renderDataTable(section);
                    $('.class-template-sottosezione').change(function () {
                        let subsection = $(this).val();
                        if (subsection !== 'Seleziona Sottosezione') {
                            renderDataTable(section, subsection);
                        } else {
                            renderDataTable(section);
                        }
                    });
                });

                $('#editRowButton').click(function () {
                    let valore = $('#idValore').val();
                    let valore_anno_precedente = $('#idValorePrecedente').val();
                    let nota = $('#idNotaArticolo').val().replaceAll("[^a-zA-Z0-9]+", "");
                    const payload = {
                        id,
                        valore,
                        valore_anno_precedente,
                        nota,
                        city
                    }
                    console.log(payload)

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/editrow',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#editModal").modal('hide');

                            renderEditArticle();
                            renderDataTable(section);
                            console.log(section);
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

                $('#deleteRowButton').click(function () {
                    const payload = {
                        id,
                        city
                    }

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/delrow',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteModal").modal('hide');
                            articoli = articoli.filter(art => Number(art.id) !== Number(id));
                            renderDataTable(section);
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


            });
        </script>
        <?php
    }

    public static function render()

    {
        $data = new MasterTemplateRepository();
        if (isset($_GET['fondo']) && isset($_GET['anno']) && isset($_GET['descrizione']) && isset($_GET['version'])) {
            $results_articoli = $data->visualize_template($_GET['fondo'], $_GET['anno'], $_GET['descrizione'], $_GET['version'], $_GET['template_name'],$_GET['city']);

        } else {
            if (isset($_GET['template_name']) && isset($_GET['fondo']) && isset($_GET['version']))
                $results_articoli = $data->getArticoli($_GET['template_name'],$_GET['city'],$_GET['fondo'],$_GET['version']);
        }
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
        <div class="accordion mt-2 col" id="accordionTemplateTable">
            <?php
            $section_index = 0;
            foreach ($tot_array as $sezione => $sottosezioni) {
                ?>
                <div class="card" id="templateCard">
                    <div class="card-header" id="headingTemplateTable<?= $section_index ?>">
                        <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                data-target="#collapseTemplate<?= $section_index ?>"
                                aria-expanded="false" aria-controls="collapseTemplate<?= $section_index ?>"
                                data-section="<?= $sezione ?>">
                            <?= $sezione ?>
                        </button>
                    </div>
                    <div id="collapseTemplate<?= $section_index ?>" class="collapse"
                         aria-labelledby="headingTemplateTable<?= $section_index ?>"
                         data-parent="#accordionTemplateTable">
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
                            <table class="table datetable">
                                <thead>
                                <tr>
                                    <th style="width: 140px">Nome Articolo</th>
                                    <th style="width: 170px">Sottotitolo Articolo</th>
                                    <th style="width: 175px">Descrizione Articolo</th>
                                    <th>Nota</th>
                                    <th>Valore</th>
                                    <th>Valore Anno Precedente</th>
                                    <th style="width: 170px">Link</th>
                                    <th>Azioni</th>
                                </tr>

                                </thead>
                                <tbody id="dataTemplateTableBody<?= $section_index ?>">
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
                        <h5 class="modal-title">Modifica riga del fondo:</h5>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <label>Valore</label>
                        <input type="number" class="form-control" id="idValore">
                        <label>Valore Anno precedente</label>
                        <input type="number" class="form-control" id="idValorePrecedente">
                        <label>Nota</label>
                        <textarea class="form-control"
                                  id="idNotaArticolo"></textarea>
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