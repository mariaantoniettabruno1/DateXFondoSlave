<?php

namespace dateXFondoPlugin;

use DocumentTable;

//use Dompdf\Dompdf;

//require '../dompdf/autoload.inc.php';
header('Content-Type: text/javascript');

class ShortCodeDocumentTable
{
    public static function visualize_document_template()
    {

        // per filtrare il contenuto delle pagine tramite gli utenti
        global $current_user;
        get_currentuserinfo();
        if ($current_user->user_login == 'admin') {
            //do something
        } else {
            //do something else
        }
        $document = new DocumentTable();
        $entries = $document->getEditedDocument(30);
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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
            <script type="text/javascript">
                $(document).ready(function () {
                    let document_text = `<?php echo json_encode($entries['testo']);?>`;
                    document.getElementById("paragraphDocumentID").innerHTML = document_text;
                });

                function makeInputReadOnly() {

                    document.getElementById("inputId").setAttribute("readonly", "readonly");
                    document.getElementById("inputId2").setAttribute("readonly", "readonly");
                    document.getElementById("inputId3").setAttribute("readonly", "readonly");
                    document.getElementById("inputId4").setAttribute("readonly", "readonly");
                    document.getElementById("inputId5").setAttribute("readonly", "readonly");
                    document.getElementById("inputId6").setAttribute("readonly", "readonly");
                    document.getElementById("inputId7").setAttribute("readonly", "readonly");
                    document.getElementById("inputId8").setAttribute("readonly", "readonly");
                    document.getElementById("inputId9").setAttribute("readonly", "readonly");
                    document.getElementById("inputId10").setAttribute("readonly", "readonly");
                    document.getElementById("inputId11").setAttribute("readonly", "readonly");
                    let inputValue = document.getElementById('inputId').value;
                    let inputValue2 = document.getElementById('inputId2').value;
                    let inputValue3 = document.getElementById('inputId3').value;
                    console.log(inputValue);
                    let inputValue4 = document.getElementById('inputId4').value;
                    let inputValue5 = document.getElementById('inputId5').value;
                    let inputValue6 = document.getElementById('inputId6').value;
                    let inputValue7 = document.getElementById('inputId7').value;
                    let inputValue8 = document.getElementById('inputId8').value;
                    let inputValue9 = document.getElementById('inputId9').value;
                    let inputValue10 = document.getElementById('inputId10').value;
                    let inputValue11 = document.getElementById('inputId11').value;
                    document.getElementById('inputId').setAttribute('value', inputValue);
                    document.getElementById('inputId2').setAttribute('value', inputValue2);
                    document.getElementById('inputId3').setAttribute('value', inputValue3);
                    document.getElementById('inputId4').setAttribute('value', inputValue4);
                    document.getElementById('inputId5').setAttribute('value', inputValue5);
                    document.getElementById('inputId6').setAttribute('value', inputValue6);
                    document.getElementById('inputId7').setAttribute('value', inputValue7);
                    document.getElementById('inputId8').setAttribute('value', inputValue8);
                    document.getElementById('inputId9').setAttribute('value', inputValue9);
                    document.getElementById('inputId10').setAttribute('value', inputValue10);
                    document.getElementById('inputId11').setAttribute('value', inputValue11);

                    let val = document.getElementById("paragraphDocumentID").innerHTML;
                    document.getElementById("hiddenParagraphId").value = val;
                    document.getElementById('editableButton').hidden = false;
                    document.getElementById('saveEditButton').hidden = true;
                    <?php
                    $documentText = $_POST['hiddenParagraphId'];
                    $goodDocumentText = str_replace('\"', '', $documentText);
                    if (isset($goodDocumentText) && $goodDocumentText !== '') {
                        $document->updateDocument('', $goodDocumentText, '', '', 0);
                    }
                    ?>


                }


                function makeInputEditable() {
                    document.getElementById('inputId').removeAttribute("readonly");
                    document.getElementById('inputId2').removeAttribute("readonly");
                    document.getElementById('inputId3').removeAttribute("readonly");
                    document.getElementById('inputId4').removeAttribute("readonly");
                    document.getElementById('inputId5').removeAttribute("readonly");
                    document.getElementById('inputId6').removeAttribute("readonly");
                    document.getElementById('inputId7').removeAttribute("readonly");
                    document.getElementById('inputId8').removeAttribute("readonly");
                    document.getElementById('inputId9').removeAttribute("readonly");
                    document.getElementById('inputId10').removeAttribute("readonly");
                    document.getElementById('inputId11').removeAttribute("readonly");
                    document.getElementById('editableButton').hidden = true;
                    document.getElementById('saveEditButton').hidden = false;
                }

                function createPDF() {

                    var element = document.getElementById('paragraphDocumentID');
                    var opt = {
                        margin: [0.5, 1, 0.5, 1],
                        filename: 'Document.pdf',
                        //image: {type: 'jpeg', quality: 0.98},
                        html2canvas: {
                            scale: 2,
                            allowTaint: true,
                            useCORS: true
                        },
                        jsPDF: {unit: 'in', format: 'Legal', orientation: 'p'},
                        pagebreak: {mode: ['avoid-all', 'css', 'legacy']}
                    };

                    html2pdf().set(opt).from(element).save();
                }


            </script>
        </head>
        <body>
        <div class="container_content" id="container_content">
            <p id="paragraphDocumentID">
        </div>
        <form method="POST">
            <input type='hidden' id='hiddenParagraphId' name='hiddenParagraphId'>
            <input type="submit" class="btn btn-info" style="float: right" onclick="makeInputReadOnly()"
                   value="Salva modifica" id="saveEditButton" hidden>
        </form>
        <div>
            <button class="btn btn-primary" onclick="makeInputEditable()" id="editableButton">Modifica pdf</button>
            <button class="btn btn-primary" onclick="createPDF()">Crea pdf</button>
            <div>


            </div>
        </div>
        </body>
        </html>


        <?php

    }
}