<?php

use dateXFondoPlugin\DateXFondoCommon;

class ModelloFondoUtilizzo
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

                for (let i = 0; i < sezioni_utilizzo.length; i++) {
                    $('#dataUtilizzoDocumentTableBody' + i).html('');
                    filteredUtilizzoArticoli = filteredUtilizzoArticoli.filter(art => art.sezione === sezioni_utilizzo[i])
                    filteredUtilizzoArticoli.forEach(art => {
                        if (art.preventivo !== undefined)
                            preventivo = art.preventivo;
                        if (art.consuntivo !== undefined)
                            consuntivo = art.consuntivo;

                        $('#dataUtilizzoDocumentTableBody' + i).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${art.nome_articolo}</td>
                                       <td>${preventivo}</td>
                                       <td>${consuntivo}</td>


                                 </tr>
                             `);

                    });
                    filteredUtilizzoArticoli = articoli_utilizzo;
                }



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



            });

        </script>
        <?php
    }

    public static function render()
    {
        $data = new DocumentRepository();
        $tot_sezioni = $data->getSezioniUtilizzo($_GET['editor_name'],$_GET['version']);


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


        <?php
        self::render_scripts();

    }
}