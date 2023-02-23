<?php

use dateXFondoPlugin\DateXFondoCommon;

class ModelloRegioniDestinazioneTable
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

                let codice = '';
                let importo = '';
                let nota = '';
                let nome_articolo = '';
                filteredDestinazioneArticoli.forEach(art => {

                    nota = art.nota ?? '';
                    importo = art.importo ?? '';
                    codice = art.codice ?? '';
                    nome_articolo = art.nome_articolo ?? '';





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

            }
            function resetRegioniSubsection() {
                let subsection = $('.class-template-sottosezione').val();
                if (subsection !== 'Seleziona Sottosezione') {
                    $('.class-template-sottosezione').val('Seleziona Sottosezione');
                }
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


            });
        </script>
        <?php
    }

    public static function render()
    {
        $data = new \dateXFondoPlugin\RegioniDocumentRepository();
        $data_document = new DocumentRepository();
        $results_articoli = $data->getHistoryDestinazioneArticoli($_GET['editor_name'],$_GET['version'],$_GET['city']);
        $formulas = $data_document->getFormulas($_GET['editor_name'],$_GET['city']);
        $ids_articolo = $data_document->getIdsArticoli($_GET['editor_name'],$_GET['city']);
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

        <?php
        self::render_scripts();
    }
}