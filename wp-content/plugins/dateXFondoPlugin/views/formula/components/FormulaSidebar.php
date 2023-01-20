<?php

namespace dateXFondoPlugin;

class FormulaSidebar
{
    public static function render_scripts()
    {
        ?>

        <script>
            let id = 0;

            function renderDataTable(section, subsection, owner) {
                $('#dataTableBody').html('');
                let filteredArticoli = articoli;
                console.log(formule);

                filteredArticoli = filteredArticoli.filter(art => art.template_name === owner)

                if (section) {
                    filteredArticoli = filteredArticoli.filter(art => art.sezione === section)
                }
                if (subsection) {
                    filteredArticoli = filteredArticoli.filter(art => art.sottosezione === subsection)
                }
                filteredArticoli.forEach(art => {

                    let button = "";
                    if (art.row_type !== "decurtazione") {
                        button = `<button type="button" class="btn btn-sm btn-outline-primary" title="Aggiungi ${art.id_articolo} alla formula" onclick="insertIntoFormula('${art.id_articolo}')"><i class="fa-solid fa-plus"></i></button>`
                    } else {
                        button = `<button type="button" class="btn btn-sm btn-outline-success" title="Aggiungi decurtazione ${art.id_articolo} alla formula" onclick="insertDecurtazioneIntoFormula('${art.link}', '${art.id_articolo}')"><i class="fa-solid fa-plus"></i></button>`
                    }

                    $('#dataTableBody').append(`
                        <tr>
                          <td class="text-truncate" data-toggle="tooltip" title="${art.id_articolo}">
                           ${art.id_articolo}
                          </td>
                          <td class="text-truncate" data-toggle="tooltip" title="${art.nome_articolo}">
                           ${art.nome_articolo}
                          </td>
                          <td class="text-truncate" data-toggle="tooltip" title="${art.sottotitolo_articolo}">
                           ${art.sottotitolo_articolo}
                          </td>
                          <td>
                            ${button}
                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Visualizza" data-toggle="modal" data-target="#modalPreviewArticolo" data-id="${art.id}"><i class="fa-solid fa-eye"></i></button>
                          </td>
                        </tr>
                    `);
                });
            }

            function renderFormulaTables(section, subsection, owner) {
                $('#formulaTableBody').html('');
                $('#conditionalTableBody').html('');
                let filteredFormule = formule;
                filteredFormule = filteredFormule.filter(f => f.formula_template_name === owner)
                if (section) {
                    filteredFormule = filteredFormule.filter(f => f.sezione === section)
                }
                if (subsection) {
                    filteredFormule = filteredFormule.filter(f => f.sottosezione === subsection)
                }
                filteredFormule.forEach(f => {
                    let table;
                    let nome_formula = '';
                    if (Number(f.condizione) === 1) { <?php // QUANDO FACCIAMO IL JSON PARSE VIENE TUTTO CONVERTITO IN STRINGA. ?>
                        table = $('#conditionalTableBody');
                    } else {
                        table = $('#formulaTableBody');
                    }
                    if (f.text_type === '10') {
                        nome_formula = '<span class="span-bold">' + f.nome + '</span>';
                    } else if (f.text_type === '01') {
                        nome_formula = '<span class="span-higher">' + f.nome + '</span>';

                    } else if (f.text_type === '11') {
                        nome_formula = '<span class="span-bold-higher">' + f.nome + '</span>';

                    } else {
                        nome_formula = '<span>' + f.nome + '</span>';

                    }
                    table.append(`
                        <tr>
                          <td class="text-truncate" data-toggle="tooltip" title="${f.nome}">${nome_formula}</td>
                          <td class="text-truncate" data-toggle="tooltip" title="${f.descrizione}">${f.descrizione}</td>
                          <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" title="Aggiungi ${f.nome} alla formula" onclick="insertIntoFormula('${f.nome}')"><i class="fa-solid fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Visualizza" onclick="editFormula('${f.id}')"><i class="fa-solid fa-pencil"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-delete-formula" title="Elimina" data-id="${f.id}" data-toggle="modal" data-target="#deleteFormulaModal"><i class="fa-solid fa-trash"></i></button>
                          </td>
                        </tr>
                    `);
                    $('.btn-delete-formula').click(function () {
                        id = $(this).attr('data-id');
                    });
                });
            }

            function renderOwnerFilter() {
                $('#inputSelectOwner').html('<option>Seleziona Template</option>');
                for (let i = 0; i < owners.length; i++) {
                    $('#inputSelectOwner').append(`<option>${owners[i]}</option>`);
                }
            }

            function renderSectionFilter() {
                $('#inputSelectSezione').html('<option>Seleziona Sezione</option>');
                Object.keys(sezioni).forEach(sez => {
                    $('#inputSelectSezione').append(`<option>${sez}</option>`);
                });
            }

            function filterSubsections(section) {
                $('#inputSelectSottosezione').html('<option>Seleziona Sottosezione</option>');
                sezioni[section].forEach(ssez => {
                    $('#inputSelectSottosezione').append(`<option>${ssez}</option>`);
                });
            }

            function handleFilter() {
                let subsection = $('#inputSelectSottosezione').val();
                let section = $('#inputSelectSezione').val();
                let owner = $('#inputSelectOwner').val();
                if (subsection === 'Seleziona Sottosezione' || subsection === "") {
                    subsection = null
                }
                if (section === 'Seleziona Sezione') {
                    section = null
                }
                if (owner === 'Seleziona Template') {
                    owner = null
                }
                console.log(section, subsection,owner);
                renderDataTable(section, subsection, owner);
                renderFormulaTables(section, subsection, owner);
            }

            $(document).ready(function () {
                renderDataTable();
                renderFormulaTables();
                renderSectionFilter();
                renderOwnerFilter();

                $('#inputSelectSezione').change(function () {
                    const section = $('#inputSelectSezione').val();
                    if (section !== 'Seleziona Sezione') {
                        $('#inputSelectSottosezione').attr('disabled', false);
                        filterSubsections(section);
                        renderDataTable(section);
                        renderFormulaTables(section);
                    } else {
                        $('#inputSelectSottosezione').attr('disabled', true);
                        $('#inputSelectSottosezione').html('');
                        renderDataTable();
                        renderFormulaTables();
                    }
                });
                $('#inputSelectSottosezione').change(function () {
                    handleFilter();
                });
                $('#inputSelectOwner').change(function () {
                    const owner = $('#inputSelectOwner').val();
                    if(owner!=='Seleziona Proprietario'){
                        handleFilter();
                    }
                });
                $("#deleteFormulaButton").click(function () {
                    const payload = {
                        id
                    }

                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/deleteformula',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            $("#deleteFormulaModal").modal('hide');
                            formule = formule.filter(art => Number(art.id) !== Number(id));
                            renderFormulaTables();
                            $(".alert-delete-formula-success").show();
                            $(".alert-delete-formula-success").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-formula-success").slideUp(500);
                            });

                        },
                        error: function (response) {
                            console.error(response);
                            $("#deleteFormulaModal").modal('hide');
                            $(".alert-delete-formula-wrong").show();
                            $(".alert-delete-formula-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-delete-formula-wrong").slideUp(500);
                            });
                        }
                    });
                })
            })
        </script>
        <?php
    }

    public static function render()
    {

        ?>
        <div id="accordionSidebar">
            <div class="card">
                <div class="card-header" id="headingDati">
                    <h5 class="mb-0">Filtri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <select class="custom-select" id="inputSelectOwner">
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="custom-select" id="inputSelectSezione">
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="custom-select" id="inputSelectSottosezione" disabled>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="card">
                <div class="card-header" id="headingDati">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseDati"
                                aria-expanded="true" aria-controls="collapseDati">
                            Dati
                        </button>

                    </h5>
                </div>

                <div id="collapseDati" class="collapse show" aria-labelledby="headingDati"
                     data-parent="#accordionSidebar">
                    <div class="card-body">
                        <table class="table" style="table-layout: fixed">
                            <thead>
                            <tr>
                                <th style="width: 110px">Id Articolo</th>
                                <th>Nome</th>
                                <th>Sottotitolo</th>
                                <th style="width: 94px"></th>
                            </tr>
                            </thead>
                            <tbody id="dataTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingFormule">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFormule"
                                aria-expanded="false" aria-controls="collapseFormule">
                            Formule
                        </button>
                    </h5>
                </div>
                <div id="collapseFormule" class="collapse" aria-labelledby="headingFormule"
                     data-parent="#accordionSidebar">
                    <div class="card-body">
                        <table class="table" style="table-layout: fixed">
                            <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Descrizione</th>
                            </tr>
                            </thead>
                            <tbody id="formulaTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingCondizionali">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                data-target="#collapseCondizionali" aria-expanded="false"
                                aria-controls="collapseCondizionali">
                            Condizionali
                        </button>
                    </h5>
                </div>
                <div id="collapseCondizionali" class="collapse" aria-labelledby="headingCondizionali"
                     data-parent="#accordionSidebar">
                    <div class="card-body">
                        <table class="table" style="table-layout: fixed">
                            <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Descrizione</th>
                            </tr>
                            </thead>
                            <tbody id="conditionalTableBody">
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteFormulaModal" tabindex="-1" role="dialog"
             aria-labelledby="deleteFormulaModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteFormulaModalLabel">Cancella formula </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi veramente eliminare questa formula?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="deleteFormulaButton">Cancella</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-success alert-delete-formula-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Cancellazione formula andata a buon fine!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-delete-formula-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Cancellazione formula non riuscita
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();
    }

}