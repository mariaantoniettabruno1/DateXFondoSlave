<?php

namespace dateXFondoPlugin;

use AllCitiesTable;

class UserManagement
{
    public static function render()
    {
        $data = new CitiesRepository();

        $results_cities = $data->getAllCities();


        foreach ($results_cities[0] as $city){
            foreach ($results_cities[1] as $entry){
                if($entry['id'] == $city['id_consulente']){
                    $city['id_consulente'] = $entry['user_login'];

                }
            }
        }

        //sistemare il cambio del consulente perchè non funziona dopo il foreach
        ?>
        <!DOCTYPE html>

        <html lang="en">

        <head>
            <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
                    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
                    crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
                    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
                    crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
                  integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        </head>
            <link rel="stylesheet"
                  href="<?= DateXFondoCommon::get_base_url() ?>/wp-content/plugins/dateXFondoPlugin/assets/styles/main.css">


        </head>
        <body>

        <script>
            let cities = JSON.parse((`<?=json_encode($results_cities[0]);?>`));
        </script>
        <div class="row">
            <?php
            AllCitiesTable::render();
            ?>
        </div>
        </body>
        </html lang="en">
        <?php
    }
}