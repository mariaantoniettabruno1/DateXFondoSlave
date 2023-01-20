<?php

namespace dateXFondoPlugin;

use GFAPI;

class ShortCodeCreateFondo
{
    public static function create_fondo()
    {
        $lastEntry = GFAPI::get_entries(7);

        if (!empty($lastEntry[0])) {
            $lastEntry = GFAPI::get_entries(7)[0];
            $new_fondo = new CreateFondo();
            $new_fondo->setTitoloFondo($lastEntry[1]);
            $new_fondo->setAnno($lastEntry[25]);
            $new_fondo->setEnte($lastEntry[26]);
            $new_fondo->setDescrizione($lastEntry[2]);
            $new_fondo->setFondoDiRiferimento($lastEntry[6]);
            $new_fondo->setModelloDiRiferimento($lastEntry[7]);
            $new_fondo->setNomeSoggettoDeliberante($lastEntry[8]);
            $new_fondo->setNumeroDeliberaApprovazioneBilancio($lastEntry[9]);
            $new_fondo->setDataLiberaApprovazioneBilancio($lastEntry[10]);
            $new_fondo->setResponsabile($lastEntry[11]);
            $new_fondo->setNumeroDeliberaApprovazionePEG($lastEntry[12]);
            $new_fondo->setDataDeliberaDiApprovazione($lastEntry[13]);
            $new_fondo->setNumeroDeliberaApprovazionePEG($lastEntry[14]);
            $new_fondo->setDataDeliberaDiNomina($lastEntry[15]);
            $new_fondo->setNumeroDeliberaApprovazioneRazionalizzazione($lastEntry[16]);
            $new_fondo->setDataDelibera($lastEntry[17]);
            $new_fondo->setNumeroDeliberaCostituzioneFondo($lastEntry[18]);
            $new_fondo->setDataDeliberaDiCostituzione($lastEntry[19]);
            $new_fondo->setNumeroDeliberaIndirizzoCostituzioneContrattazione($lastEntry[20]);
            $new_fondo->setDataDeliberaIndirizzoAnnoCorrente($lastEntry[21]);
            $new_fondo->setPrincipioRiduzioneSpesaPersonale($lastEntry[22]);
            $new_fondo->setUfficiale($lastEntry[22]);
            $tablename_fondo = 'DATE_entry_new_fondo_' . $new_fondo->getTitoloFondo();
            $tablename_fondo = str_replace(' ', '_', $tablename_fondo);

            $temp = $new_fondo->checkIfTableExist($tablename_fondo);
            if ($temp) {
                $new_fondo->insertDataFondo($tablename_fondo);
            } else {
                $new_fondo->createNewTableFondo($tablename_fondo);
                $new_fondo->insertDataFondo($tablename_fondo);
            }


        }
        ?>
        <!DOCTYPE html>

        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <!-- Bootstrap CSS -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
                  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
                  crossorigin="anonymous">
            <style>
                .buttonsDiv{
                    display: flex;
                    justify-content: center;
                }

            </style>
        </head>
        <body>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
                integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
                crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
                integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
                crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
                integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
                crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <div class="buttonsDiv">
            <button id="button_id" type="button" data-toggle="modal" data-target=".modal" class="btn btn-primary mr-1">Duplica Template Precedente</button>
            <a id="btn_newTemplate" type="button" class="btn btn-primary ml-1" href="crea-nuovo-template/">Crea nuovo template</a>
        </div>

        <div class="modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Seleziona il campo che vuoi ereditare dall'anno precedente:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="radio" class="campo_ereditato" name="campo_ereditato" value="Valore"> Valore<br>
                        <input type="radio" class="campo_ereditato" name="campo_ereditato" value="Nota e Valore"> Nota e Valore<br>
                        <input type="radio" class="campo_ereditato" name="campo_ereditato" value="Nessuno"> Nessuno<br>
                    </div>
                    <div class="modal-footer">
                        <button id="submit_button" type="button" class="btn btn-primary">Duplica Template Precedente</button>
                    </div>
                </div>
            </div>
        </div>

        </body>
        <script>

            $('#submit_button').click(function(){
                var campo_ereditato = $('.campo_ereditato:checked').val();
                $.ajax({
                    type: "POST",
                    url: "https://demo.mg3.srl/date/wp-json/datexfondoplugin/v1/table/new",
                    data: {
                        <?php
                        $myObj = ["fondo" => $new_fondo->getTitoloFondo(), "ente" => $new_fondo->getEnte(), "anno" => $new_fondo->getAnno()];
                        ?>
                        "JSONIn":<?php echo json_encode($myObj);?>,
                        campo_ereditato

                    },
                    success: function () {
                        successmessage = 'I dati sono stati caricati correttamente';
                        alert(successmessage);
                        location.href = "https://demo.mg3.srl/date/duplicazione-template-anno-precedente/";
                    },
                    error: function () {
                        successmessage = 'Errore: caricamento dati non riuscito';
                        alert(successmessage);
                    },

                });
            });
        </script>
        </html>

        <?php
        return '';

    }


}