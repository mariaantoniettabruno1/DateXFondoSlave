<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloRegioniHeader
{
    public static function render_scripts(){
?>
        <script>
        $(document).ready(function (){
            $('#inputDocumentName').val(`${articoli_costituzione[0].document_name}`);
            $('#inputYear').val(`${articoli_costituzione[0].anno}`);
            $('#inputEditorName').val(`${articoli_costituzione[0].editor_name}`);
            let old_document_name = $('#inputDocumentName').val();

            $("#editInputButton").click(function () {
                $(this).hide();
                $('#saveInputButton').show();
                $('#deleteEditButton').show();
                $('#inputDocumentName').attr('readonly', false);
                $('#inputEditorName').attr('readonly', false);
                $('#inputYear').attr('readonly', false);
            });
            $("#deleteEditButton").click(function (){
                $(this).hide();
                $('#saveInputButton').hide();
                $('#editInputButton').show();
            });
            $('#saveInputButton').click(function () {
                {
                    let document_name = $('#inputDocumentName').val();
                    let editor_name = $('#inputEditorName').val();
                    let anno = $('#inputYear').val();

                    $('#inputDocumentName').attr('readonly', true);
                    $('#inputEditorName').attr('readonly', true);
                    $('#inputYear').attr('readonly', true);

                    const payload = {
                        document_name,
                        old_document_name,
                        editor_name,
                        anno
                    }
                    console.log(payload)
                    $.ajax({
                        url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/regionidocumentheader',
                        data: payload,
                        type: "POST",
                        success: function (response) {
                    console.log(response);
                    $(".alert-header-success").show();
                    $(".alert-header-success").fadeTo(2000, 500).slideUp(500, function(){
                        $(".alert-header-success").slideUp(500);
                    });
                },
                        error: function (response) {
                    console.error(response);
                    $(".alert-header-wrong").show();
                    $(".alert-header-wrong").fadeTo(2000, 500).slideUp(500, function(){
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
    public static function render(){
        $data = new \dateXFondoPlugin\RegioniDocumentRepository();
        $articoli = $data->getCostituzioneArticoli($_GET['editor_name']);
        ?>
        <div class="col-2">
            <input type="text" placeholder="Redattore del documento" id="inputEditorName" readonly>

        </div>
        <div class="col-2">
            <input type="text" placeholder="Titolo documento" id="inputDocumentName" readonly>

        </div>
        <div class="col-2">
            <input type="text" placeholder="Anno" id="inputYear" readonly>
        </div>
        <?php
        if ($articoli[0]['editable'] == '1') {
            ?>
            <button class="btn btn-link" id="editInputButton"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-link" id="saveInputButton" style="display: none"><i
                    class="fa-solid fa-floppy-disk"></i></button>
            <button class="btn btn-link" id="deleteEditButton" style="display: none"> Annulla </button>

            <?php
        } else {
            ?>
            <button class="btn btn-link" id="editInputButton" disabled><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-link" id="saveInputButton" style="display: none"><i
                    class="fa-solid fa-floppy-disk"></i></button>
            <button class="btn btn-link" id="deleteEditButton" style="display: none"> Annulla </button>
            <?php
        }
        ?>
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