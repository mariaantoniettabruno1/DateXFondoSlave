<?php

use dateXFondoPlugin\DateXFondoCommon;
use dateXFondoPlugin\MasterTemplateStopEditingButton;
class ModelloFondoCostituzione
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
                console.log(sezioni)
                for (let i = 0; i < sezioni.length; i++) {
                    $('#dataCostituzioneDocumentTableBody' + i).html('');
                    filteredDocArticoli = filteredDocArticoli.filter(art => art.sezione === sezioni[i])
                    filteredDocArticoli.forEach(art => {
                        if (art.preventivo !== undefined)
                            preventivo = art.preventivo;

                        $('#dataCostituzioneDocumentTableBody' + i).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${art.sottosezione}</td>
                                       <td>${art.nome_articolo}</td>
                                       <td>${preventivo}</td>
                                 </tr>
                             `);

                    });
                    filteredDocArticoli = articoli;
                }

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
                var currentdate = new Date();
                XLSX.writeFile(new_workbook, ('xlsx' + ('ModelloFondo' + "_" + currentdate.getDate() + "-"
                    + (currentdate.getMonth() + 1) + "-"
                    + currentdate.getFullYear() + '-' + 'h' +
                    +currentdate.getHours() + '-'
                    + currentdate.getMinutes() + '-'
                    + currentdate.getSeconds() + '.xlsx')));
            }




            $(document).ready(function () {
                renderDataTable();




            });

        </script>
        <?php

    }

    public static function render()
    {

        $data = new DocumentRepository();
        $tot_sezioni = $data->getSezioni($_GET['editor_name'],$_GET['version']);

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

        <?php
        self::render_scripts();

    }
}