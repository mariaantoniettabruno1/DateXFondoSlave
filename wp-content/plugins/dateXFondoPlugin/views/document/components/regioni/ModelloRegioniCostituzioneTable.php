<?php

use dateXFondoPlugin\DateXFondoCommon;

class ModelloRegioniCostituzioneTable
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

                let codice = '';
                let importo = '';
                let nota = '';
                let nome_articolo = '';
                filteredArticoli.forEach(art => {

                    nota = art.nota ?? '';
                    importo = art.importo ?? '';
                    codice = art.codice ?? '';
                    nome_articolo = art.nome_articolo ?? '';





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



            });
        </script>
<?php
    }

    public static function render()
    {
        $data = new \dateXFondoPlugin\RegioniDocumentRepository();
        $data_document = new DocumentRepository();
        $results_articoli = $data->getHistoryCostituzioneArticoli($_GET['editor_name'],$_GET['version'],$_GET['city']);
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

        <?php
        self::render_scripts();
    }
}