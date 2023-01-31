<?php

use dateXFondoPlugin\DateXFondoCommon;

class AllDocumentTable
{
    private $documents;

    public function __construct($documents)
    {
        $this->documents = $documents;
    }

    public function render_scripts()
    {
        ?>
        <script>
            let documents = JSON.parse((`<?= json_encode($this->documents); ?>`));

            function renderDataTable() {
                documents.forEach(doc => {
                    $('#dataDocumentTableBody').append(`
                                 <tr>
                                       <td>${doc.document_name}</td>
                                       <td>${doc.editor_name}</td>
                                       <td>${doc.anno}</td>
                                       <td>${doc.version}</td>


                     <td><div class="row pr-3">
               <button class="btn btn-link btn-vis-templ" data-document='${doc.document_name}' data-editor='${doc.editor_name}' data-page = '${doc.page}' data-version ='${doc.version}' data-toggle="tooltip" title="Visualizza e modifica documento"><i class="fa-solid fa-eye"></i></button>
                                    </td>
                                 </tr>
                             `);
                });

            }

            $(document).ready(function () {

                renderDataTable();

                let current_url = '<?=
                   (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
                        === 'on' ? "https" : "http") .
                        "://" . $_SERVER['HTTP_HOST'] .
                        $_SERVER['REQUEST_URI'];?>';
                $('.btn-vis-templ').click(function () {
                    let document_name = $(this).attr('data-document');
                    let editor_name = $(this).attr('data-editor');
                    let page = $(this).attr('data-page');
                    let version = $(this).attr('data-version');
                        location.href = '<?= DateXFondoCommon::get_website_url()?>/' + page + '?document_name=' + document_name + '&editor_name=' + editor_name + '&version=' + version;

                });

            });
        </script>


        <?php


    }

    public function render()
    {
        ?>
        <table class="table" style="table-layout: fixed">
            <thead>
            <tr>
                <th style="width: 12.5rem">Nome Documento</th>
                <th style="width: 6.25rem">Editor</th>
                <th style="width: 6.25rem">Anno</th>
                <th style="width: 6.25rem">Versione</th>
                <th style="width:6.25rem">Azioni</th>
            </tr>

            </thead>
            <tbody id="dataDocumentTableBody">
            </tbody>
        </table>
        <?php
        self::render_scripts();

    }

}