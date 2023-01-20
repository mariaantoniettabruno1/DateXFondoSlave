<?php

namespace dateXFondoPlugin;

class ShortCodeTable
{
    public static function visualize_table()
    {
        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
            <script type="text/javascript" src="https://unpkg.com/jquery-tabledit@1.0.0/jquery.tabledit.js"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

        </head>

        <body>


        <div>

            <form method='POST' action=''>

                <h4>Seleziona Anno</h4>

                <?php

                $years = new CustomTable();
                $results_years = $years->getAllYears();
                arsort($results_years);

                ?>

                <select id='year' name='select_year' onchange='this.form.submit()'>
                    <option disabled selected> Seleziona Anno</option>

                    <?php foreach ($results_years as $res_year): ?>

                        <option <?= isset($_POST['select_year']) && $_POST['select_year'] === $res_year[0] ? 'selected' : '' ?>

                                value='<?= $res_year[0] ?>'><?= $res_year[0] ?></option>

                    <?php endforeach; ?>
                </select>


                <h4>Seleziona Fondo</h4>

                <?php

                $fondi = new CustomTable();
                $results_fondi = $fondi->getAllFondi();
                arsort($results_fondi);

                ?>

                <select id='fondo' name='select_fondo' onchange='this.form.submit()'>
                    <option disabled selected> Seleziona Fondo</option>

                    <?php foreach ($results_fondi as $res_fondo): ?>

                        <option <?= isset($_POST['select_fondo']) && $_POST['select_fondo'] === $res_fondo[0] ? 'selected' : '' ?>

                                value='<?= $res_fondo[0] ?>'><?= $res_fondo[0] ?></option>

                    <?php endforeach; ?>
                </select>
            </form>

        </div>

        <h2>Tabella per la visualizzazione dei dati di Chivasso.</h2>
        <h8>I dati possono essere filtrati scegliendo l'anno o scegliendo l'anno e il fondo.</h8>

        <table id="data_table" class="table table-striped">
            <thead>
            <tr>

                <th>Fondo</th>

                <th>Ente</th>

                <th>Anno</th>

                <th>ID Campo</th>

                <th>Label Campo</th>

                <th>Descrizione Campo</th>

                <th>Sottotitolo Campo</th>

                <th>Valore</th>

                <th>Valore Anno Precedente</th>

                <th>Nota</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if (isset($_POST['select_year']) && isset($_POST['select_fondo'])) {
                $data = new CustomTable();
                $selected_year = $_POST['select_year'];
                $selected_fondo = $_POST['select_fondo'];

                $entries = $data->getAllEntriesFromYearsFondo($selected_year, $selected_fondo);
                foreach ($entries as $entry) {
                    echo '<tr>';
                    unset($entry[0]);
                    foreach ($entry as $anno) {
                        echo '<td>' . $anno . '</td>';
                    }

                    echo '</tr>';


                }

            } elseif (isset($_POST['select_year'])) {
                $years = new CustomTable();
                $selected_year = $_POST['select_year'];

                $entries = $years->getAllEntries($selected_year);

                foreach ($entries as $entry) {
                    echo '<tr>';
                    unset($entry[0]);
                    foreach ($entry as $anno) {
                        echo '<td>' . $anno . '</td>';
                    }

                    echo '</tr>';


                }
            }
            ?>
            </tbody>
        </table>
        </body>

        <script>
        <?php
    }

}