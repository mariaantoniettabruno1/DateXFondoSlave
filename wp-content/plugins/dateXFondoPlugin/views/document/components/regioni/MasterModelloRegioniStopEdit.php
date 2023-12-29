<?php

use dateXFondoPlugin\DateXFondoCommon;

class MasterModelloRegioniStopEdit
{
public static function render_scripts(){
?>       <script>
        $(document).ready(function (){
            $('#stopEditRegioniDocumentButton').click(function () {

                $("#idAddRegioniRow").attr("disabled", true);
                let document_name = $('#inputDocumentName').val();

                const payload = {
                    document_name
                }
                console.log(payload)
                $.ajax({
                    url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/disabledeeditregioni',
                    data: payload,
                    type: "POST",
                    success: function (response) {
                        console.log(response);
                        location.href = '<?= DateXFondoCommon::get_website_url() ?>/regioni-ed-autonomie-locali-documento/';
                    },
                    error: function (response) {
                        console.error(response);
                        $(".alert-block-wrong").show();
                        $(".alert-block-wrong").fadeTo(2000, 500).slideUp(500, function(){
                            $(".alert-block-wrong").slideUp(500);
                        });
                    }
                });
            });
        });
    </script>
    <?php
}

    public static function render()
    {
        ?>
        <button class="btn btn-link" id="stopEditRegioniButton"  data-toggle="modal" data-target="#stopEditRegionModal"><i class="fa-solid fa-ban stopIcon"></i> Blocca la modifica</button>
        <div class="modal fade" id="stopEditRegionModal" tabindex="-1" role="dialog" aria-labelledby="stopEditRegionModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="stopEditRegionModalLabel">Blocca modifica sul template </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Vuoi bloccare definitivamente la modifica su questo template?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="stopEditRegioniDocumentButton">Blocca</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-success alert-block-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Blocco modifica applicato correttamente!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-block-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Blocco sulla modifica dei campi non riuscito
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();
    }
}