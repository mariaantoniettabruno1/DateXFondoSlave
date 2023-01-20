<?php

namespace dateXFondoPlugin;

use SlaveFormulaTable;
use FormulaRepository;
use GFAPI;
use Mpdf\Form;

header('Content-Type: text/javascript');

class SlaveShortCodeFormulaTable
{
    public static function visualize_slave_formula_template()
    {

        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>
            <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
                  integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
            <style type="text/css">

                .field_description > span {
                    overflow: hidden;
                    max-height: 200px;
                    min-width: 40px;
                    min-height: 40px;
                    display: inline-block;
                }

                .field_section > select {
                    width: 150px;
                }


            </style>
        </head>

        <body>
        <div>
            <form method='POST'>

                <h4>Seleziona Sezione</h4>

                <?php

                $sections = new FormulaRepository();
                $results_sections = $sections->getAllSections();
                arsort($results_sections);

                ?>

                <select id='section' name='select_section' onchange='this.form.submit()'>
                    <option disabled selected> Seleziona Sezione</option>

                    <?php foreach ($results_sections as $res_section): ?>

                        <option <?= isset($_POST['select_section']) && $_POST['select_section'] === $res_section[0] ? 'selected' : '' ?>

                                value='<?= $res_section[0] ?>'><?= $res_section[0] ?></option>

                    <?php endforeach; ?>
                </select>
                <h4>Seleziona Sottosezione </h4>

                <?php
                $results_subsections = $sections->getAllSubsections($_POST['select_section']);
                arsort($results_subsections);

                ?>

                <select id='subsection' name='select_subsection' onchange='this.form.submit()'>
                    <option disabled selected> Seleziona sottosezione</option>

                    <?php foreach ($results_subsections as $res_subsection): ?>

                        <option <?= isset($_POST['select_subsection']) && $_POST['select_subsection'] === $res_subsection[0] ? 'selected' : '' ?>

                                value='<?= $res_subsection[0] ?>'><?= $res_subsection[0] ?></option>

                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <br>
        <div>
            <h2>TABELLA FORMULE DELLE SOTTOSEZIONI CREATE</h2>
            <br>
            <div class="table table-responsive">
                <table id="data_table" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Sezione</th>

                        <th>Sottosezione</th>

                        <th>label descrittiva</th>

                        <th>Condizione</th>

                        <th>Formula</th>

                        <th>Totale</th>
                    </tr>
                    </thead>
                    <tbody id="tbl_posts_body">
                    <?php
                    $formulaData = new SlaveFormulaTable();


                    if (isset($_POST['select_section'])) {
                    $selected_section = $_POST['select_section'];
                    $selected_subsection = $_POST['select_subsection'];
                    $formulaEntries = $formulaData->getFormulaBySelectedSection($selected_subsection);

                    foreach ($formulaEntries

                             as $formula) {
                    ?>
                    <tr>
                        <td><?php echo $formula[1]; ?></td>
                        <td><?php echo $formula[2]; ?></td>
                        <td><?php echo $formula[3]; ?></td>
                        <td><?php echo $formula[4]; ?></td>
                        <td><?php echo $formula[5]; ?></td>
                        <?php
                        $array_formula_character = str_split($formula[5]);
                        $array_formula_condition = str_split($formula[4]);

                        $counter = 0;
                        $id_campo = '';
                        $temp_value = '';
                        $firstValueCondition = '';
                        $condition = '';
                        $secondValueCondition = '';

                        foreach ($array_formula_character as $character) {
                            if ($character == '+' || $character == '-' || $character == '*' || $character == '/' || $character == '(' || $character == ')') {
                                $temp_value .= $formulaData->getValueFromIdCampo($id_campo)['valore'];
                                $temp_value .= $character;
                                $id_campo = '';
                                $counter++;
                            } else {
                                $id_campo .= $character;
                                $counter++;
                                if ($counter == sizeof($array_formula_character)) {
                                    $temp_value .= $formulaData->getValueFromIdCampo($id_campo)['valore'];
                                    $id_campo = '';
                                }
                            }

                        }

                        $total = "print (" . $temp_value . ");";
                        $counterCondition = 0;
                        foreach ($array_formula_condition as $character) {
                            if ($character == '>' || $character == '<' || $character == '=' || $character == '>=' || $character == '<=') {
                                $firstValueCondition = (int)$formulaData->getValueFromIdCampo($id_campo)['valore'];
                                $condition = $character;
                                $id_campo = '';
                                $counterCondition++;
                            } else {
                                $id_campo .= $character;
                                $counterCondition++;

                                if ($counterCondition == sizeof($array_formula_condition)) {
                                    $secondValueCondition = (int)$formulaData->getValueFromIdCampo($id_campo)['valore'];
                                    $id_campo = '';
                                }
                            }


                        }
                        if (sizeof($formula[4]) != 0) {
                            switch ($condition) {
                                case '>':
                                    if ($firstValueCondition > $secondValueCondition) {

                                        echo '<td>';
                                        print_r(number_format(eval($total), 2, ',', '.'));
                                        echo '</td>';
                                    } else {
                                        echo '<td>';
                                        echo 'Condizione non soddisfatta';
                                        echo '</td>';
                                    }
                                    break;
                                case '<':
                                    if ($firstValueCondition < $secondValueCondition) {

                                        echo '<td>';
                                        print_r(number_format(eval($total), 2, ',', '.'));
                                        echo '</td>';
                                    } else {
                                        echo '<td>';
                                        echo 'Condizione non soddisfatta';
                                        echo '</td>';
                                    }

                                    break;
                                case '=':
                                    if ($firstValueCondition == $secondValueCondition) {

                                        echo '<td>';
                                        print_r(number_format(eval($total), 2, ',', '.'));
                                        echo '</td>';
                                    } else {
                                        echo '<td>';
                                        echo 'Condizione non soddisfatta';
                                        echo '</td>';
                                    }
                                    break;
                                case '>=':

                                    if ($firstValueCondition >= $secondValueCondition) {

                                        echo '<td>';
                                        print_r(number_format(eval($total), 2, ',', '.'));
                                        echo '</td>';
                                    } else {
                                        echo '<td>';
                                        echo 'Condizione non soddisfatta';
                                        echo '</td>';
                                    }

                                    break;
                                case '<=':
                                    if ($firstValueCondition <= $secondValueCondition) {

                                        echo '<td>';
                                        print_r(number_format(eval($total), 2, ',', '.'));
                                        echo '</td>';
                                    } else {
                                        echo '<td>';
                                        echo 'Condizione non soddisfatta';
                                        echo '</td>';
                                    }

                                    break;
                            }
                        } else {
                            echo '<td>';
                            print_r(number_format(eval($total), 2, ',', '.'));
                            echo '</td>';
                        }
                        // $formulaData->saveTotal($total, $formula[0][2], $selected_section, $fondo, $ente, $anno);
                        }
                        }
                        ?>
                    </tr>
                    <tr class="table-active">
                        <td><p><b>Totale sezione:</b></p></td>
                        <td></td>
                        <?php
                        $array_formula_character = str_split($formula[0][5]);
                        $counter = 0;
                        $id_campo = '';
                        $temp_value = '';

                        foreach ($array_formula_character as $character) {
                            if ($character == '+' || $character == '-' || $character == '*' || $character == '/' || $character == '(' || $character == ')') {
                                $temp_value .= $formulaData->getValueFromIdCampo($id_campo)['valore'];
                                $temp_value .= $character;
                                $id_campo = '';
                                $counter++;
                            } else {
                                $id_campo .= $character;
                                $counter++;
                                if ($counter == sizeof($array_formula_character)) {
                                    $temp_value .= $formulaData->getValueFromIdCampo($id_campo)['valore'];
                                    $id_campo = '';
                                }
                            }

                        }

                        $total = "print (" . $temp_value . ");";
                        //$totalConverted = number_format(eval($total), 2, ',','.'); non me lo stampa con il valore corretto se ci metto la variabile
                        // $formulaData->saveTotal($total, $formula[0][2], $selected_section, $fondo, $ente, $anno);

                        ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?php //print_r(number_format(eval($total), 2, ',','.'));
                            ?></td>
                    </tr>
                    <?php

                    ?>
                    </tbody>
                </table>

                <!--                <nav aria-label="Page navigation example">-->
                <!--                    <ul class="pagination justify-content-end">-->
                <!--                        <li class="page-item --><?php //if ($page == 1) echo 'disabled';
                ?><!--">-->
                <!--                            <a class="page-link" href="?index=1" tabindex="-1" aria-disabled="true">1</a>-->
                <!--                        <li class="page-item --><?php //if ($page <= 1) {
                //                            echo 'disabled';
                //                        }
                ?><!--"><a class="page-link" href="?index=--><?php //echo $previous;
                ?><!--"">Precedente</a></li>-->
                <!--                        <li class="page-item"><input id="currentPageInput" type="number" min="1"-->
                <!--                                                     max="--><?php //echo $totalPages
                ?><!--"-->
                <!--                                                     placeholder="--><?php //echo $page;
                ?><!--" required></li>-->
                <!--                        <li class="page-item --><?php //if ($page >= $totalPages) {
                //                            echo 'disabled';
                //                        }
                ?><!--"><a class="page-link" href="?index=--><?php //echo $next;
                ?><!--">Successivo</a></li>-->
                <!--                        <li class="page-item --><?php //if ($page >= $totalPages) {
                //                            echo 'disabled';
                //                        }
                ?><!--"><a class="page-link"-->
                <!--                                 href="?index=--><?php //echo $totalPages;
                ?><!--">--><?php //echo $totalPages;
                ?><!--</a>-->
                <!--                        </li>-->
                <!--                    </ul>-->
                <!--                </nav>  <nav aria-label="Page navigation example">-->
                <!--                    <ul class="pagination justify-content-end">-->
                <!--                        <li class="page-item --><?php //if ($page == 1) echo 'disabled';
                ?><!--">-->
                <!--                            <a class="page-link" href="?index=1" tabindex="-1" aria-disabled="true">1</a>-->
                <!--                        <li class="page-item --><?php //if ($page <= 1) {
                //                            echo 'disabled';
                //                        }
                ?><!--"><a class="page-link" href="?index=--><?php //echo $previous;
                ?><!--"">Precedente</a></li>-->
                <!--                        <li class="page-item"><input id="currentPageInput" type="number" min="1"-->
                <!--                                                     max="--><?php //echo $totalPages
                ?><!--"-->
                <!--                                                     placeholder="--><?php //echo $page;
                ?><!--" required></li>-->
                <!--                        <li class="page-item --><?php //if ($page >= $totalPages) {
                //                            echo 'disabled';
                //                        }
                ?><!--"><a class="page-link" href="?index=--><?php //echo $next;
                ?><!--">Successivo</a></li>-->
                <!--                        <li class="page-item --><?php //if ($page >= $totalPages) {
                //                            echo 'disabled';
                //                        }
                ?><!--"><a class="page-link"-->
                <!--                                 href="?index=--><?php //echo $totalPages;
                ?><!--">--><?php //echo $totalPages;
                ?><!--</a>-->
                <!--                        </li>-->
                <!--                    </ul>-->
                <!--                </nav>-->


            </div>
        </body>
        </div>

        </html lang="en">

        <?php


    }
}