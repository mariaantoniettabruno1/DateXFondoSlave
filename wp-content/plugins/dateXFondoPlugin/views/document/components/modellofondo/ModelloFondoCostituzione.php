<?php


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
                console.log(sezioni)
                for (let i = 0; i < sezioni.length; i++) {
                    $('#dataCostituzioneDocumentTableBody' + i).html('');
                    filteredDocArticoli = filteredDocArticoli.filter(art => art.sezione === sezioni[i])
                    filteredDocArticoli.forEach(art => {
                        if (art.preventivo !== undefined)
                            preventivo = art.preventivo;

                        $('#dataCostituzioneDocumentTableBody' + i).append(`
        <tr style="width: auto; padding: 10px 6px; border: 1px solid black; background-color: transparent; color: #457FAF;">
            <td style="padding: 10px 6px; border: 1px solid black;">${art.ordinamento}</td>
            <td style="padding: 10px 6px; border: 1px solid black;">${art.sottosezione}</td>
            <td style="padding: 10px 6px; border: 1px solid black;">${art.nome_articolo}</td>
            <td style="padding: 10px 6px; border: 1px solid black;">${preventivo}</td>
        </tr>
        `);

                    });
                    filteredDocArticoli = articoli;
                }

            }


            const tablesToExcel = (function () {
                const style = "<style>.contentTable {border: none;} .contentTable thead tr {border: none;} .contentTable thead tr, .contentTable tr {border: none;} .contentTable thead tr th, .contentTable tbody tr td {border: none;} .datatable_costituzione {position:relative; z-index: 10; top: 0px; left: 0px; width: 100%; height: auto; margin: 0px; padding: 0px; font-family: Tahoma; font-size: 10pt; text-align: justify; display: block; border: 0.5px solid #303030} .datatable_costituzione thead {position:relative; min-width: 100%; min-height:80px; } .datatable_costituzione thead tr {width: auto; padding: 10px 6px; border: 0.5px solid #303030; background-color: #457FAF;color: #FFFFFF;} .datatable_costituzione thead tr th {padding: 10px 6px; border:  0.5px solid #303030;} .datatable_costituzione tbody tr td {padding: 10px 6px; border:  0.5px solid #303030;} </style>";
                const uri = 'data:application/vnd.ms-excel;base64,'
                    ,
                    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>'
                    , templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--> ' + style + ' </head>'
                    , body = '<body>'
                    , tablevar = '<table>{table'
                    , tablevarend = '}</table>'
                    , bodyend = '</body></html>'
                    , worksheet = '<x:ExcelWorksheet><x:Name>'
                    ,
                    worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>'
                    , worksheetvar = '{worksheet'
                    , worksheetvarend = '}'
                    , base64 = function (s) {
                        return window.btoa(unescape(encodeURIComponent(s)))
                    }
                    , format = function (s, c) {
                        return s.replace(/{(\w+)}/g, function (m, p) {
                            return c[p];
                        })
                    }
                    , wstemplate = ''
                    , tabletemplate = '';


                return function (index, name, filename) {
                    const array1 = [];
                    let temp1 = 0;
                    for (let x = 1; x <= index; x++) {
                        array1[x - 1] = 'contentTable' + temp1;
                        temp1 = temp1 + 1;
                    }
                    console.log(array1)
                    const tables = array1;
                    let wstemplate = '';
                    let tabletemplate = '';

                    wstemplate = worksheet + worksheetvar + '0' + worksheetvarend + worksheetend;
                    for (let i = 0; i < tables.length; ++i) {
                        tabletemplate += tablevar + i + tablevarend;
                    }

                    const allTemplate = template + wstemplate + templateend;
                    const allWorksheet = body + tabletemplate + bodyend;
                    const allOfIt = allTemplate + allWorksheet;

                    const ctx = {};
                    ctx['worksheet0'] = name;
                    let exceltable;
                    for (let k = 0; k < tables.length; ++k) {
                        if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                        ctx['table' + k] = exceltable.innerHTML;
                    }

                    document.getElementById("dlink").href = uri + base64(format(allOfIt, ctx));
                    document.getElementById("dlink").download = filename;
                    document.getElementById("dlink").click();
                }
            })();
            $(document).ready(function () {
                renderDataTable();
            });

        </script>
        <?php

    }

    public static function render()
    {

        $data = new DocumentRepository();
        $tot_sezioni = $data->getSezioni($_GET['editor_name'], $_GET['version'], $_GET['city']);

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
                        <div class="card-body">
                            <table class="content_table" id="contentTable<?= $section_index ?>">
                                <tr>
                                    <td>
                                        <table class="datatable_costituzione" id="exportableTableCostituzione<?= $section_index ?>">
                                            <thead style="position:relative; min-width: 100%;">
                                            <tr style="position:relative; width: auto; padding: 10px 6px; border: 1px solid black; font-weight: 600; background-color: #457FAF; color: #FFFFFF;">
                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                    Ordinamento
                                                </th>
                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                    Sottosezione
                                                </th>
                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                    Nome Articolo
                                                </th>
                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                    Preventivo
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody id="dataCostituzioneDocumentTableBody<?= $section_index ?>">
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr></tr>
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
                    <a id="dlink" style="display:none;"></a>
                    <button class="btn btn-outline-primary"
                            onclick="tablesToExcel(<?= $section_index ?>,'Costituzione', 'ModelloFondo.xls')">Genera Foglio Excel
                    </button>
                </div>
            </div>
        </div>

        <?php
        self::render_scripts();

    }
}