<?php

use dateXFondoPlugin\DateXFondoCommon;
use dateXFondoPlugin\FondoCompletoTableRepository;

class FondoCompletoTable
{
    public static function render_scripts()
    {
        ?>
        <script>

            let id = 0;
            let filteredRecord = joined_record;
            let valore = '';
            let array = [];

            function getFormulaValues() {
                filteredRecord.forEach(art => {
                    if (art.formula !== undefined) {
                        valore = evaluateFormula(art.formula);

                        let element = {};
                        element.formula = art.nome;
                        element.valore = valore;
                        array.push(element);
                        if (isNaN(parseFloat(valore))) {
                            element.valore = 'Rivedere dati inseriti, formula non andata a buon fine';
                        }

                    }
                });
                const payload = {array, city};
                $.ajax({
                    url: '<?= DateXFondoCommon::get_website_url() ?>///wp-json/datexfondoplugin/v1/valuesformula',
                    data: payload,
                    type: "POST",
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            }

            function renderDataTable(section, subsection) {

                let index = Object.keys(sortedSections).indexOf(section);
                $('#dataTemplateTableBody' + index).html('');
                filteredRecord = joined_record;
                filteredRecord = filteredRecord.filter(art => art.sezione === section)
                if (subsection) {
                    filteredRecord = filteredRecord.filter(art => art.sottosezione === subsection)
                }
                let nota = '';
                let id_articolo = '';
                let descrizione = '';
                let link = '';
                let valore_precedente = '';
                let nome_articolo = '';

                filteredRecord.sort(function (a, b) {
                    // GETTING JOIN INDEX KEYS
                    let aJoinKey
                    if (a.formula) {
                        aJoinKey = "F" + a.id
                    } else {
                        aJoinKey = "T" + a.id
                    }
                    let bJoinKey
                    if (b.formula) {
                        bJoinKey = "F" + b.id
                    } else {
                        bJoinKey = "T" + b.id
                    }
                    // Getting order
                    const ajoinOrder = joinedIndexes[aJoinKey]?.ordinamento ?? -1
                    const bjoinOrder = joinedIndexes[bJoinKey]?.ordinamento ?? -1
                    // Sorting
                    return ajoinOrder - bjoinOrder
                })

                filteredRecord.forEach(art => {
                        nota = art.nota ?? "";
                        id_articolo = art.id_articolo ?? "";
                        link = art.link ?? "";
                        let link_button = '';
                        nome_articolo = art.nome_articolo ?? "";
                        descrizione = art.descrizione_articolo ?? ""
                        nota = art.nota ?? ""
                        valore = art.valore ?? ""
                        valore_precedente = art.valore_anno_precedente ?? "";

                        if (art.formula !== undefined) {
                            id_articolo = art.nome ?? "";

                            if (art.text_type === '10') {
                                id_articolo = '<span class="span-bold">' + art.nome + '</span>';
                            } else if (art.text_type === '01') {
                                id_articolo = '<span class="span-higher">' + art.nome + '</span>';

                            } else if (art.text_type === '11') {
                                id_articolo = '<span class="span-bold-higher">' + art.nome + '</span>';

                            } else if (art.text_type === '00') {
                                id_articolo = '<span>' + art.nome + '</span>';

                            }
                            if (art.descrizione !== undefined) {
                                descrizione = art.descrizione;
                            }

                        }
                        if (art.link !== null) {
                            link_button = ` <button class="btn btn-link btn-art-link" data-link='${art.link}'><i class="fa-solid fa-arrow-up-right-from-square"></i></button>`;
                        }

                        $('#dataTemplateTableBody' + index).append(`
                         <tr>
                           <td>${id_articolo}</td>
                           <td>${nome_articolo}</td>
                            <td> ${descrizione}</td>
                           <td>${valore}</td>
                           <td>${valore_precedente}</td>
                           <td>${nota}</td>
                             <td>
                                       <div class="row pr-3">
                                       <div class="col-8">${link}</div>
                                       <div class="col-2">
                                        ${link_button}</div>
                                        </div>
                                        </td>
                         </tr>
                             `);
                    }
                )
                ;
                $('.descrizioneCut').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).prev().attr("style", "display:block");
                });
                $('.descrizioneFull').click(function () {
                    $(this).attr("style", "display:none");
                    $(this).next().attr("style", "display:block");
                });
                $('.btn-art-link').click(function () {
                    var url = '<?= DateXFondoCommon::get_website_url() ?>/date-doc/articoli/' + $(this).attr('data-link');
                    window.open(url, '_blank');
                });
            }


            function resetSubsection() {
                let subsection = $('.class-template-sottosezione').val();
                if (subsection !== 'Seleziona Sottosezione') {
                    $('.class-template-sottosezione').val('Seleziona Sottosezione');
                }
            }

            function evaluateFormula(formula) {


                let articles = extractArticles(formula);

                if (articles.length !== 0) {
                    for (let a of articles) {
                        this[a[0]] = parseInt(getArticleValue(a[0]));

                    }
                }

                let formulas = extractFormulas(formula);
                if (formulas.length !== 0) {
                    for (let f of formulas) {
                        if (this[f[0]] === undefined) {
                            let temp_formula = getFormulaString(f[0]);
                            if (temp_formula !== undefined)
                                this[f[0]] = evaluateFormula(temp_formula);

                        }
                    }

                }
                try {
                    return eval(formula);

                } catch (e) {
                    console.log(e);
                    return " C'è un errore nei valori inseriti, ricontrolla";
                }
            }


            function extractFormulas(formula) {
                const regexp = /F\w+/g;
                return [...formula.matchAll(regexp)];
            }

            function extractArticles(formula) {
                const regexp = /R\w+/g;
                return [...formula.matchAll(regexp)];
            }

            function getArticleValue(article) {
                let value = '';
                articoli.forEach(art => {
                    if (art.id_articolo === article) {
                        value = art.valore;
                    }
                });
                return value;
            }

            function getFormulaString(formula) {
                let value;
                formulas.forEach(form => {
                    if (form.nome === formula) {
                        value = form.formula;
                    }

                });
                return value;
            }


            let current_section;
            let current_subsection;

            $(document).ready(function () {
                getFormulaValues();
                renderDataTable();
                resetSubsection();


                $('.class-accordion-button').click(function () {
                    let section = $(this).attr('data-section');
                    current_section = section;

                    renderDataTable(section);
                    $('.class-template-sottosezione').change(function () {
                        let subsection = $(this).val();
                        if (subsection !== 'Seleziona Sottosezione') {
                            current_subsection = subsection;
                            renderDataTable(section, subsection);
                        } else {
                            current_subsection = null;
                            renderDataTable(section);
                        }
                    });
                });
            });
        </script>
        <?php
    }

    public static function render()
    {
        $data = new FondoCompletoTableRepository();

        $results_articoli = [];
        $results_formula = [];

        if (isset($_GET['city'])) {
            $city = $_GET['city'];
        } else {
            $city = '';
        }

        if (isset($_GET['fondo']) && isset($_GET['anno']) && isset($_GET['descrizione']) && isset($_GET['version']) && isset($_GET['template_name'])) {
            if (!isset($_GET['descrizione']))
                $descrizione = '';
            else
                $descrizione = $_GET['descrizione'];
            $results_articoli = $data->getHistoryArticles($_GET['fondo'], $_GET['anno'], $descrizione, $_GET['version'], $_GET['template_name'], $city);
            $results_formula = $data->getHistoryFormulas($_GET['template_name'], $_GET['anno'], $city);
            $results_joined = $data->getHistoryJoinedRecords($_GET['anno'], $city);


        } else {

            if (isset($_GET['template_name'])) {

                $results_articoli = $data->getJoinedArticoli($_GET['template_name'], $_GET['version'], $_GET['fondo'], $city);
                $results_formula = $data->getJoinedFormulas($_GET['template_name'], $city);
            }
        }

        $sezioni = [];
        $tot_array = [];
        foreach ($results_articoli as $articolo) {
            if (!in_array($articolo['sezione'], $sezioni)) {
                if ($articolo['sezione'] != '') {
                    array_push($sezioni, $articolo['sezione']);
                    $tot_array = array_fill_keys($sezioni, []);
                }

            }
        }
        foreach ($results_formula as $formula) {
            if (!in_array($formula['sezione'], $sezioni)) {
                if ($formula['sezione'] != '') {
                    array_push($sezioni, $formula['sezione']);
                    $tot_array = array_fill_keys($sezioni, []);
                }
            }
        }

        foreach ($tot_array as $key => $value) {
            foreach ($results_articoli as $articolo) {
                if ($key === $articolo['sezione'] && array_search($articolo['sottosezione'], $tot_array[$key]) === false) {
                    if ($formula['sottosezione'] != "")
                        array_push($tot_array[$key], $articolo['sottosezione']);
                }
                foreach ($results_formula as $formula) {
                    if ($key === $formula['sezione'] && array_search($formula['sottosezione'], $tot_array[$key]) === false) {
                        if ($formula['sottosezione'] != "")
                            array_push($tot_array[$key], $formula['sottosezione']);
                    }
                }
            }
        }
        $customOrder = array(
            'Risorse fisse aventi carattere di certezza e stabilità',
            'Risorse variabili',
            'Decurtazioni rispetto anni precedenti',
            'Informazioni utili per calcolare le decurtazioni',
            'Totale salario accessorio per rispetto tetto art. 23 c. 2 del D.Lgs 75/2017',
            'Utilizzo del fondo',
            'Fondo Straordinario'
        );
        $sortedArray = array();

        // Ordina l'array in base all'ordine personalizzato
        foreach ($customOrder as $key) {
            if (array_key_exists($key, $tot_array)) {
                $sortedArray[$key] = $tot_array[$key];
            }
        }

        // Aggiungi chiavi mancanti nell'array originale
        foreach ($tot_array as $key => $value) {
            if (!array_key_exists($key, $sortedArray)) {
                $sortedArray[$key] = $value;
            }
        }
        ?>
        <div class="accordion mt-2 col" id="accordionTemplateTable">
            <?php
            $section_index = 0;

            foreach ($sortedArray as $sezione => $sottosezioni) {
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

                                    <th>Id Articolo</th>
                                    <th>Nome Articolo</th>
                                    <th>Descrizione Articolo</th>
                                    <th>Valore</th>
                                    <th>Valore anno precedente</th>
                                    <th>Nota</th>
                                    <th>Link</th>
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
        <div class="alert alert-success alert-ordinamento-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica campo andata a buon fine!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-ordinamento-fail" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica non andata a buon fine
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();
    }

}