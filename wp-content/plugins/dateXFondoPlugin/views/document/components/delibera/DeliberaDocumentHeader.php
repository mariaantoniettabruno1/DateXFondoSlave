<?php

use dateXFondoPlugin\DateXFondoCommon;
use dateXFondoPlugin\DeliberaDocumentRepository;

class  DeliberaDocumentHeader
{
    public static function render_scripts()
    {
        ?>
        <script>
            $(document).ready(function () {
                $('#inputDocumentName').val(`${data[0].document_name}`);
                $('#inputEditorName').val(`${data[0].editor_name}`);
                $('#inputAnno').val(`${data[0].anno}`);
                let old_document_name = $('#inputDocumentName').val();
                let old_editor_name = $('#inputEditorName').val();

                $("#editInputButton").click(function () {
                    $(this).hide();
                    $('#saveInputButton').show();
                    $('#deleteEditButton').show();
                    $('#inputDocumentName').attr('readonly', false);
                    $('#inputEditorName').attr('readonly', false);
                    $('#inputAnno').attr('readonly', false);
                });
                $("#deleteEditButton").click(function () {
                    $(this).hide();
                    $('#saveInputButton').hide();
                    $('#editInputButton').show();
                });
                $('#saveInputButton').click(function () {
                    {
                        let document_name = $('#inputDocumentName').val();
                        let editor_name = $('#inputEditorName').val();
                        let anno = $('#inputAnno').val();
                        $('#inputDocumentName').attr('readonly', true);
                        $('#inputEditorName').attr('readonly', true);
                        $('#inputAnno').attr('readonly', true);

                        const payload = {
                            document_name,
                            old_document_name,
                            editor_name,
                            old_editor_name,
                            anno
                        }
                        console.log(payload)
                        $.ajax({
                            url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/documentheader',
                            data: payload,
                            type: "POST",
                            success: function (response) {
                                console.log(response);
                                $(".alert-header-success").show();
                                $(".alert-header-success").fadeTo(2000, 500).slideUp(500, function () {
                                    $(".alert-header-success").slideUp(500);
                                });
                            },
                            error: function (response) {
                                console.error(response);
                                $(".alert-header-wrong").show();
                                $(".alert-header-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                    $(".alert-header-wrong").slideUp(500);
                                });
                            }
                        });
                        $('#saveInputButton').hide();
                        $('#deleteEditButton').hide();
                        $('#editInputButton').show();

                    }
                });
            })
        </script>
        <?php
    }

    public static function render()
    {
        $data = new DeliberaDocumentRepository();
        $infos = $data->getAllValues($_GET['document_name'], $_GET['editor_name']);

        ?>
        <div class="row">
            <div class="col-4">
                <input type="text" placeholder="Titolo documento" id="inputDocumentName" readonly>

            </div>
            <div class="col-3">
                <input type="text" placeholder="Redattore del documento" id="inputEditorName" readonly>

            </div>
            <div class="col-2">
                <input type="text" placeholder="Anno" id="inputAnno" readonly>
            </div>


            <?php
            if ($infos[0]['editable'] == '1') {
                ?>
                <div class="col-3">
                    <button class="btn btn-link" id="editInputButton"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-link" id="saveInputButton" style="display: none"><i
                                class="fa-solid fa-floppy-disk"></i></button>
                    <button class="btn btn-link" id="deleteEditButton" style="display: none"> Annulla</button>
                </div>
                <?php
            } else {
                ?>
                <div class="col-3">
                    <button class="btn btn-link" id="editInputButton" disabled><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-link" id="saveInputButton" style="display: none"><i
                                class="fa-solid fa-floppy-disk"></i></button>
                    <button class="btn btn-link" id="deleteEditButton" style="display: none"> Annulla</button>
                </div>
                <?php
            }
            ?>

        </div>
        <div class="alert alert-success alert-header-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica eseguita correttamente!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-header-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Modifica non riuscita
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();

    }

}