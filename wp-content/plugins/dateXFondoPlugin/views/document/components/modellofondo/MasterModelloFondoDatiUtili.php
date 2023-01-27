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

                for (let i = 0; i < sezioni_dati_utili.length; i++) {
                    $('#dataDatiUtiliDocumentTableBody' + i).html('');
                    filteredDatiUtiliArticoli = filteredDatiUtiliArticoli.filter(art => art.sezione === sezioni_dati_utili[i])
                    filteredDatiUtiliArticoli.forEach(art => {
                        if (art.formula !== undefined)
                            formula = art.formula;
                        if (art.nota !== undefined)
                            nota = art.nota;

                        $('#dataDatiUtiliDocumentTableBody' + i).append(`
                                 <tr>
                                       <td>${art.ordinamento}</td>
                                       <td>${art.sottosezione}</td>
                                       <td>${art.nome_articolo}</td>
                                       <td>${formula}</td>
                                       <td>${nota}</td>


                                 </tr>
                             `);

                    });
                    filteredDatiUtiliArticoli = articoli_dati_utili;
                }




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


            $(document).ready(function () {
                renderDatiUtiliDataTable();




            });

        </script>
        <?php
    }

    public static function render()
    {
        $data = new DocumentRepository();
        $tot_sezioni = $data->getSezioniDatiUtili($_GET['editor_name']);
        $formulas = $data->getFormulas($_GET['editor_name']);
        $ids_articolo = $data->getIdsArticoli($_GET['editor_name']);
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


        <?php
        self::render_scripts();

    }
}