<?php


class ModelloFondoCostituzione
{
    public static function render_scripts()
    {
        ?>
        <style>
            .class-accordion-button {
                color: #26282f;
            }

            .class-accordion-button:hover {
                color: #26282f;
            }

            .btn-excel {
                border-color: #26282f;
                color: #26282f;
            }

            .btn-excel:hover {
                border-color: #870e12;
                color: #870e12;
                background-color: white;
            }

        </style>
        <script src="https://cdn.jsdelivr.net/alasql/0.3/alasql.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.12/xlsx.core.min.js"></script>
        <script>
            let id = 0;

            function renderDataTable() {
                let filteredDocArticoli = articoli;
                let preventivo = '';

                for (let i = 0; i < sezioni.length; i++) {
                    $('#dataCostituzioneDocumentTableBody' + i).html('');
                    filteredDocArticoli = filteredDocArticoli.filter(art => art.sezione === sezioni[i])
                    filteredDocArticoli.forEach(art => {
                        if (art.preventivo !== undefined)
                            preventivo = art.preventivo;

                        if (art.sezione !== 'Nota') {
                            $('#dataCostituzioneDocumentTableBody' + i).append(`
                                 <tr style="width: auto; padding: 10px 6px; border: 1px solid black; background-color: transparent; color: #427AA8;">
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.ordinamento}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;"> ${art.sottosezione}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.nome_articolo}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${preventivo}</td>
                                 </tr>
                             `);
                        } else {
                            $('#dataCostituzioneDocumentTableBody' + i).append(`
                                 <tr style="width: auto; padding: 10px 6px; border: 1px solid black; background-color: transparent; color: #427AA8;">
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.nome_articolo}</td>
                                 </tr>
                             `);
                        }


                    });
                    filteredDocArticoli = articoli;
                }

            }

            id_dati_utili = 0;

            function renderDatiUtiliDataTable() {
                let filteredDatiUtiliArticoli = articoli_dati_utili;
                let nota = '';
                let =
                formula = '';

                for (let i = 0; i < sezioni_dati_utili.length; i++) {
                    $('#dataDatiUtiliDocumentTableBody' + i).html('');
                    filteredDatiUtiliArticoli = filteredDatiUtiliArticoli.filter(art => art.sezione === sezioni_dati_utili[i])
                    filteredDatiUtiliArticoli.forEach(art => {
                        if (art.formula !== undefined)
                            formula = art.formula;
                        if (art.nota !== undefined)
                            nota = art.nota;

                        $('#dataDatiUtiliDocumentTableBody' + i).append(`
                                 <tr style="width: auto; padding: 10px 6px; border: 1px solid black; background-color: transparent; color: #427AA8;">
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.ordinamento}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.sottosezione}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.nome_articolo}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${formula}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${nota}</td>


                                 </tr>
                             `);

                    });
                    filteredDatiUtiliArticoli = articoli_dati_utili;
                }

            }

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

                        if (art.sezione !== 'Nota') {
                            $('#dataUtilizzoDocumentTableBody' + i).append(`
                                 <tr style="width: auto; padding: 10px 6px; border: 1px solid black; background-color: transparent; color: #427AA8;">
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.ordinamento}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.nome_articolo}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${preventivo}</td>
                                       <td style="padding: 10px 6px; border: 1px solid black;">${consuntivo}</td>
                                 </tr>
                             `);
                        } else {
                            $('#dataUtilizzoDocumentTableBody' + i).append(`
                                 <tr style="width: auto; padding: 10px 6px; border: 1px solid black; background-color: transparent; color: #427AA8;">
                                       <td style="padding: 10px 6px; border: 1px solid black;">${art.nome_articolo}</td>
                                 </tr>
                             `);
                        }

                    });
                    filteredUtilizzoArticoli = articoli_utilizzo;
                }


            }


            var tablesToExcel = (function ($) {
                var uri = 'data:application/vnd.ms-excel;base64,'
                    ,
                    html_start = `<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">`
                    ,
                    template_ExcelWorksheet = `<x:ExcelWorksheet><x:Name>{SheetName}</x:Name><x:WorksheetSource HRef="sheet{SheetIndex}.htm"/></x:ExcelWorksheet>`
                    , template_ListWorksheet = `<o:File HRef="sheet{SheetIndex}.htm"/>`
                    , template_HTMLWorksheet = `
------=_NextPart_dummy
Content-Location: sheet{SheetIndex}.htm
Content-Type: text/html; charset=windows-1252

` + html_start + `
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <link id="Main-File" rel="Main-File" href="../WorkBook.htm">
    <link rel="File-List" href="filelist.xml">
</head>
<body><table>{SheetContent}</table></body>
</html>`
                    , template_WorkBook = `MIME-Version: 1.0
X-Document-Type: Workbook
Content-Type: multipart/related; boundary="----=_NextPart_dummy"

------=_NextPart_dummy
Content-Location: WorkBook.htm
Content-Type: text/html; charset=windows-1252

` + html_start + `
<head>
<meta name="Excel Workbook Frameset">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="File-List" href="filelist.xml">
<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
    <x:ExcelWorksheets>{ExcelWorksheets}</x:ExcelWorksheets>
    <x:ActiveSheet>0</x:ActiveSheet>
 </x:ExcelWorkbook>
</xml><![endif]-->
</head>
<frameset>
    <frame src="sheet0.htm" name="frSheet">
    <noframes><body><p>This page uses frames, but your browser does not support them.</p></body></noframes>
</frameset>
</html>
{HTMLWorksheets}
Content-Location: filelist.xml
Content-Type: text/xml; charset="utf-8"

<xml xmlns:o="urn:schemas-microsoft-com:office:office">
    <o:MainFile HRef="../WorkBook.htm"/>
    {ListWorksheets}
    <o:File HRef="filelist.xml"/>
</xml>
------=_NextPart_dummy--
`
                    , base64 = function (s) {
                        return window.btoa(unescape(encodeURIComponent(s)))
                    }
                    , format = function (s, c) {
                        return s.replace(/{(\w+)}/g, function (m, p) {
                            return c[p];
                        })
                    }
                return function (id_tables, filename) {
                    var context_WorkBook = {
                        ExcelWorksheets: ''
                        , HTMLWorksheets: ''
                        , ListWorksheets: ''
                    };

                    var tables = jQuery(id_tables);
                    let nome_precedente = '';
                    let SheetName = '';
                    let $temp_table = '';
                    let one_table = 0;
                    let index;

                    $.each(tables, function (SheetIndex) {
                        var $table = $(this);

                        SheetName = $table.attr('data-SheetName');
                        index = SheetIndex;
                        if (nome_precedente === SheetName) {
                            one_table = 2;
                            context_WorkBook.ExcelWorksheets += format(template_ExcelWorksheet, {
                                SheetIndex: SheetIndex
                                , SheetName: SheetName
                            });

                            $temp_table[0].innerHTML += $table[0].innerHTML
                            context_WorkBook.HTMLWorksheets += format(template_HTMLWorksheet, {
                                SheetIndex: SheetIndex
                                , SheetContent: $temp_table.html()
                            });
                            console.log($table.html());
                            context_WorkBook.ListWorksheets += format(template_ListWorksheet, {
                                SheetIndex: SheetIndex
                            });
                        } else {
                            if (one_table === 1) {
                                context_WorkBook.ExcelWorksheets += format(template_ExcelWorksheet, {
                                    SheetIndex: SheetIndex
                                    , SheetName: SheetName
                                });


                                context_WorkBook.HTMLWorksheets += format(template_HTMLWorksheet, {
                                    SheetIndex: SheetIndex
                                    , SheetContent: $temp_table.html()
                                });

                                context_WorkBook.ListWorksheets += format(template_ListWorksheet, {
                                    SheetIndex: SheetIndex
                                });
                            }
                            one_table = 1;
                        }

                        nome_precedente = $table.attr('data-SheetName');
                        $temp_table = $(this);

                    });
                    if (one_table === 1) {
                        context_WorkBook.ExcelWorksheets += format(template_ExcelWorksheet, {
                            SheetIndex: index
                            , SheetName: SheetName
                        });


                        context_WorkBook.HTMLWorksheets += format(template_HTMLWorksheet, {
                            SheetIndex: index
                            , SheetContent: $temp_table.html()
                        });

                        context_WorkBook.ListWorksheets += format(template_ListWorksheet, {
                            SheetIndex: index
                        });
                    }
                    var link = document.createElement("A");
                    link.href = uri + base64(format(template_WorkBook, context_WorkBook));
                    link.download = filename || 'Workbook.xls';
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            })(jQuery);
            $(document).ready(function () {
                renderDataTable();
                renderDatiUtiliDataTable();
                renderUtilizzoDataTable();
            });

        </script>
        <?php

    }

    public static function render()
    {

        $data = new DocumentRepository();
        $tot_sezioni = $data->getSezioni($_GET['editor_name'], $_GET['version'], $_GET['city']);
        $tot_sezioni_utili = $data->getSezioniDatiUtili($_GET['editor_name'], $_GET['version'], $_GET['city']);
        $tot_sezioni_utilizzo = $data->getSezioniUtilizzo($_GET['editor_name'], $_GET['version'], $_GET['city']);

        ?>
        <div class="container pt-3" style="width: 100%">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-link active" id="costituzione-tab" href="#costituzione" role="tab"
                       aria-controls="costituzione" aria-selected="true" data-toggle="pill">Costituzione</a>
                    <a class="nav-link" id="utilizzo-tab" href="#utilizzo" role="tab" aria-controls="utilizzo"
                       aria-selected="false" data-toggle="pill">Utilizzo</a>
                    <a class="nav-link" id="dati-tab" href="#dati" role="tab" aria-controls="dati_utili"
                       aria-selected="false" data-toggle="pill">Dati utili fondo</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="costituzione" role="tabpanel"
                     aria-labelledby="costituzione-tab" aria-selected="true">
                    <div class="accordion mt-2 col" id="accordionCostituzioneDocumentTable">
                        <?php
                        $section_index = 0;
                        foreach ($tot_sezioni as $sezione) {
                            ?>
                            <div class="card" id="costituzioneDocumentCard">
                                <div class="card-header" id="headingCostituzioneDocument<?= $section_index ?>">
                                    <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                            data-target="#collapseCostituzioneDocument<?= $section_index ?>"
                                            aria-expanded="false"
                                            aria-controls="collapseCostituzioneDocument<?= $section_index ?>"
                                            data-section="<?= $sezione['sezione'] ?>">
                                        <?= $sezione['sezione'] ?>
                                    </button>
                                </div>
                                <div id="collapseCostituzioneDocument<?= $section_index ?>" class="collapse"
                                     aria-labelledby="headingCostituzioneDocument<?= $section_index ?>"
                                     data-parent="#accordionCostituzioneDocumentTable">
                                    <div class="card-body">
                                        <table class="content_table" id="contentTable" data-SheetName="Costituzione">
                                            <tr>
                                                <td>
                                                    <table class="datatable_costituzione"
                                                           id="exportableTableCostituzione<?= $section_index ?>"
                                                    >
                                                        <thead style="position:relative; min-width: 100%;">
                                                        <?php if ($sezione['sezione'] != 'Nota') { ?>
                                                            <tr style="position:relative; width: auto; padding: 10px 6px; border: 1px solid black; font-weight: 600; background-color: #427AA8; color: #FFFFFF;">
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
                                                        <?php } else { ?>
                                                            <tr style="position:relative; width: auto; padding: 10px 6px; border: 1px solid black; font-weight: 600; background-color: #427AA8; color: #FFFFFF;">
                                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                    Nota
                                                                </th>

                                                            </tr>
                                                        <?php } ?>
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
                </div>
                <div class="tab-pane fade" id="utilizzo" role="tabpanel" aria-labelledby="utilizzo-tab"
                     aria-selected="false">
                    <div class="accordion mt-2 col" id="accordionUtilizzoDocumentTable">
                        <?php
                        $section_index = 0;
                        foreach ($tot_sezioni_utilizzo as $sezione) {
                            ?>
                            <div class="card" id="utilizzoDocumentCard">
                                <div class="card-header" id="headingUtilizzoDocument<?= $section_index ?>">
                                    <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                            data-target="#collapseUtilizzoDocument<?= $section_index ?>"
                                            aria-expanded="false"
                                            aria-controls="collapseUtilizzoDocument<?= $section_index ?>"
                                            data-section="<?= $sezione['sezione'] ?>">
                                        <?= $sezione['sezione'] ?>
                                    </button>
                                </div>
                                <div id="collapseUtilizzoDocument<?= $section_index ?>" class="collapse"
                                     aria-labelledby="headingUtilizzoDocument<?= $section_index ?>"
                                     data-parent="#accordionUtilizzoDocumentTable">
                                    <div class="card-body ">
                                        <table class="content_table_utilizzo" id="contentTableUtilizzo"
                                               data-SheetName="UtilizzoFondo">
                                            <tr>
                                                <td>
                                                    <table class="table datatable_utilizzo"
                                                           id="exportableTableUtilizzo<?= $section_index ?>">
                                                        <thead style="position:relative; min-width: 100%;">
                                                        <?php if ($sezione['sezione'] != 'Nota') { ?>
                                                            <tr style="position:relative; width: auto; padding: 10px 6px; border: 1px solid black; font-weight: 600; background-color: #427AA8; color: #FFFFFF;">
                                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                    Ordinamento
                                                                </th>
                                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                    Nome Articolo
                                                                </th>
                                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                    Preventivo
                                                                </th>
                                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                    Consuntivo
                                                                </th>

                                                            </tr>
                                                        <?php } else { ?>
                                                            <tr style="position:relative; width: auto; padding: 10px 6px; border: 1px solid black; font-weight: 600; background-color: #427AA8; color: #FFFFFF;">
                                                                <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                    Nota
                                                                </th>

                                                            </tr>
                                                        <?php } ?>
                                                        </thead>
                                                        <tbody id="dataUtilizzoDocumentTableBody<?= $section_index ?>">
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
                </div>
                <div class="tab-pane fade" id="dati" role="tabpanel" aria-labelledby="dati-tab" aria-selected="false">
                    <div class="accordion mt-2 col" id="accordionDatiUtiliDocumentTable">
                        <?php
                        $section_index = 0;
                        foreach ($tot_sezioni_utili

                        as $sezione) {
                        ?>


                        <div class="card" id="datiUtiliDocumentCard">
                            <div class="card-header" id="headingDatiUtiliDocument<?= $section_index ?>">
                                <button class="btn btn-link class-accordion-button" data-toggle="collapse"
                                        data-target="#collapseDatiUtiliDocument<?= $section_index ?>"
                                        aria-expanded="false"
                                        aria-controls="collapseDatiUtiliDocument<?= $section_index ?>"
                                        data-section="<?= $sezione['sezione'] ?>">
                                    <?= $sezione['sezione'] ?>
                                </button>
                            </div>
                            <div id="collapseDatiUtiliDocument<?= $section_index ?>" class="collapse"
                                 aria-labelledby="headingDatiUtiliDocument<?= $section_index ?>"
                                 data-parent="#accordionDatiUtiliDocumentTable">
                                <div class="card-body">
                                    <table class="content_table_dati" id="contentTableDati" data-SheetName="DatiUtili">
                                        <tr>
                                            <td>
                                                <table class="content_table" id="contentTable<?= $section_index ?>">
                                                    <tr>
                                                        <td>
                                                            <table class="table datatable_dati_utili"
                                                                   id="exportableTableDatiUtili<?= $section_index ?>">
                                                                <thead style="position:relative; min-width: 100%;">
                                                                <tr style="position:relative; width: auto; padding: 10px 6px; border: 1px solid black; font-weight: 600; background-color: #427AA8; color: #FFFFFF;">
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
                                                                        formula
                                                                    </th>
                                                                    <th style="position:relative; padding: 10px 6px; border: 1px solid black; font-weight: 600;">
                                                                        nota
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="dataDatiUtiliDocumentTableBody<?= $section_index ?>">
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr></tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $section_index++;
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row d-flex flex-row-reverse ">
                <div class="p-2">
                    <a id="dlink" style="display:none;"></a>
                    <button class="btn btn-outline-primary btn-excel"
                            onclick="tablesToExcel('#contentTableDati,#contentTable,#contentTableUtilizzo', 'WorkSheet.xls');">
                        Genera
                        Foglio Excel
                    </button>
                </div>
            </div>
        </div>

        <?php
        self::render_scripts();

    }
}