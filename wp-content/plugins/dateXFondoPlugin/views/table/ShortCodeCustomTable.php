<?php

namespace dateXFondoPlugin;
class ShortCodeCustomTable
{
    public static function visualize_custom_table()
    {
        ?>


        <!DOCTYPE html>

        <html lang="en">

    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="https://unpkg.com/jquery-tabledit@1.0.0/jquery.tabledit.js"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
              integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU"
              crossorigin="anonymous">

    </head>
    <body>

    <h2>TABELLA DEI FONDI DELL'ANNO CORRENTE</h2>

    <h8>In questa tabella è possibile modificare i campi: valore e nota.<br>
        La modifica può essere bloccata tramite un pulsante situato al fondo della pagina. Bloccando la modifica, è
        possibile duplicare la tabella.
    </h8>
    <div class="container" style="width: 200%">
            <div class="table-responsive">

                <table id="dataTable">
                    <thead>
                    <tr>
                        <th>ID Campo</th>
                        <th>Sezione</th>
                        <th>Sottosezione</th>
                        <th>Label campo</th>
                        <th>Descrizione campo</th>
                        <th>Sottotitolo campo</th>
                        <th>Valore</th>
                        <th>Valore anno precedente</th>
                        <th>Nota</th>
                    </tr>
                    </thead>
                    <tbody id="tbl_posts_body">
                    <?php
                    $year = date("Y");

                    $years = new CustomTable();
                    $readOnly = $years->isReadOnly($year);
                    $entries = $years->getAllEntries($year);

                    foreach ($entries as $entry) {

                        ?>
                        <tr>
                            <td style="display: none"><?php echo $entry[0]; ?></td>

                            <td class="field_description">
                            <span>
                                <?php echo $entry[4]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[4]; ?>'
                                       style="display: none" data-field="id_campo" data-id="<?= $entry[0] ?>"
                                />
                            </td>
                            <td class="field_description">
                            <span >
                                <?php echo $entry[5]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[5]; ?>'
                                       style="display: none" data-field="sezione" data-id="<?= $entry[0] ?>"
                                />
                            </td>
                            <td class="field_description">
                            <span >
                                <?php echo $entry[6]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[6]; ?>'
                                       style="display: none" data-field="sottosezione" data-id="<?= $entry[0] ?>"
                                /></td>
                            <td class="field_description">
                            <span >
                                <?php echo $entry[7]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[7]; ?>'
                                       style="display: none" data-field="label_campo" data-id="<?= $entry[0] ?>"
                                /></td>
                            <td class="field_description">
                            <span >
                                <?php echo $entry[8]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[8]; ?>'
                                       style="display: none" data-field="descrizione_campo" data-id="<?= $entry[0] ?>"
                                /></td>
                            <td class="field_description">
                             <span >
                                <?php echo $entry[9]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[9]; ?>'
                                       style="display: none" data-field="sottotitolo_campo" data-id="<?= $entry[0] ?>"
                                /></td>
                            <td class="field_description">   <span class="toggleable-span">
                                <?php if($entry[10]=='') print_r('Inserisci valore'); else echo $entry[10]; ?>
                            </span>
                                <input type="text" class="toggleable-input" value='<?php echo $entry[10]; ?>'
                                       style="display: none" data-field="valore" data-id="<?= $entry[0] ?>"
                                /></td>
                            <td class="field_description">
                                <span>
                                <?php echo $entry[11]; ?>
                            </span>
                                <input type="text"  value='<?php echo $entry[11]; ?>'
                                       style="display: none" data-field="valore_anno_precedente" data-id="<?= $entry[0] ?>"
                                /></td>
                            <td class="field_description">
                              <span class="toggleable-span">
                                 <?php   if($entry[12]=='') print_r('Inserisci nota'); else echo $entry[12]; ?>
                            </span>
                                <input type="text" class="toggleable-input" value='<?php echo $entry[12]; ?>'
                                       style="display: none" data-field="nota" data-id="<?= $entry[0] ?>"
                                /></td>
                        </tr>

                        <?php

                    }
                    ?>
                    </tbody>
                </table>
            </div>


    </body>
    <form method="post">
        <input type="submit" name="button1"
               class="button" value="Blocca la Modifica"/>
        <input type="submit" name="button2"
               class="button" value="Duplica la Tabella"/>
    </form>
    <script>
            $(document).ready(function () {

                    $(".toggleable-span").click(function () {
                        $(this).hide();
                        $(this).siblings(".toggleable-input").show().focus();
                    })
                    $(".toggleable-input").blur(function () {
                        $(this).hide();
                        $(this).siblings(".toggleable-span").show();
                    })

                    $(".toggleable-input").change(changeValue)
                    $(".toggleable-select").change(changeValue)
                }
            );

            function changeValue() {
                const elem = $(this);
                var value = elem.val();
                const id = elem.data("id");
                const field = elem.data("field");
                const data = {id};
                data[field] = value;
                $.ajax({
                    type: "POST",
                    url: "https://demo.mg3.srl/date/wp-json/datexfondoplugin/v1/table/editslave",
                    data,
                    success: function () {
                        successmessage = 'Modifica eseguita correttamente';
                        console.log(successmessage);
                        elem.siblings(".toggleable-span").text(value);
                        elem.siblings(".toggleable-select").text(value);
                        elem.siblings(".toggleable-radio").val(value);
                    },
                    error: function () {
                        successmessage = 'Modifica non riuscita non riuscita';
                        console.log(successmessage);
                    }
                });
            }


    </script>
        <?php
        if (array_key_exists('button2', $_POST)) {
            $years->duplicateTable($year);
        }

    }

}