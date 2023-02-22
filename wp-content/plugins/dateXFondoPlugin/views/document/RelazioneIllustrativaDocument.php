<?php

namespace dateXFondoPlugin;


use DocumentRepository;

class RelazioneIllustrativaDocument
{
    private $infos = [];
    private $values = array();
    private $user_infos = [];


    public function __construct()
    {
        $data = new DocumentRepository();
        $this->formule = $data->getFormulas($_GET['editor_name']);
        $this->articoli = $data->getIdsArticoli($_GET['editor_name']);
        $delibera_data = new DeliberaDocumentRepository();
        $this->infos = $delibera_data->getAllHistoryValues($_GET['document_name'], $_GET['editor_name'], $_GET['version']);
        $user_data = new UserRepository();
        $this->user_infos = $user_data->getUserInfos();


        foreach ($this->infos as $row) {
            $this->values[$row['chiave']] = $row['valore'];
        }
        foreach ($this->formule as $row) {
            $this->formulas[$row['nome']] = $row['valore'];

        }
        foreach ($this->articoli as $row) {
            $this->articles[$row['id_articolo']] = $row['valore'];

        }
    }

    private function getInput($key, $default, $color)
    {
        $value = $this->articles[$default] ?? $this->formulas[$default] ?? $this->values[$key] ?? $default;
        if ($value == 'titolo_ente') {
            $value = $this->user_infos['titolo_ente'];
        } else if ($value == 'nome_soggetto_deliberante') {
            $value = $this->user_infos['nome_soggetto_deliberante'];
        } else if ($value == 'responsabile_documento') {
            $value = $this->user_infos['responsabile'];
        } else if ($value == 'documento_a_firma_di') {
            $value = $this->user_infos['firma'];
        } else if ($value == 'riduzione_spesa') {
            $value = $this->user_infos['riduzione_spesa'];
        }

        ?>

        <span class="variable-span-text" style="color:<?= $color ?>"><?= $value ?></span>


        <?php
    }

    private function checkOptionalValues($default): bool
    {
        $bool = false;
        if (isset($this->articles[$default])) {

            $bool = true;
        } else if (isset($this->formulas[$default])) {

            $bool = true;
        }
        return $bool;
    }

    private function getTextArea($key, $default, $color)
    {
        $value = $this->values[$key] ?? $default;

        ?>

        <span class="variable-span-area" style="color:<?= $color ?>"><?= $value ?></span>

        <?php
    }


    public function render()
    {


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


            <script>
                let data = {};

                function exportHTML() {
                    const header = "<html xmlns='http://www.w3.org/TR/REC-html40'>" +
                        "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title></head><body>";
                    const footer = "</body></html>";
                    const bodyHTML = $("#id_relazioneIllustrativaDocument").clone(true);
                    bodyHTML.find('input,textarea').remove();

                    const sourceHTML = header + bodyHTML.html() + footer;

                    const source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
                    const fileDownload = document.createElement("a");
                    document.body.appendChild(fileDownload);
                    fileDownload.href = source;
                    const currentdate = new Date();
                    fileDownload.download = 'relazioneIllustrativa' + "_" + currentdate.getDate() + "-"
                        + (currentdate.getMonth() + 1) + "-"
                        + currentdate.getFullYear() + '-' + 'h' +
                        +currentdate.getHours() + '-'
                        + currentdate.getMinutes() + '-'
                        + currentdate.getSeconds() + '.doc';
                    fileDownload.click();
                    document.body.removeChild(fileDownload);
                }


                window.onbeforeunload = confirmExit;

                function confirmExit() {
                    return "You have attempted to leave this page. Are you sure?";
                }
            </script>

            <title></title>


        </head>
        <body>
        <?php if(my_get_current_user_roles()[0]=='subscriber'): ?>
            <div style="width: 20%">

                    <label>Seleziona comune per visualizzare i suoi dati:</label>
                    <select name="comune" id="idComune">
                        <option>Torino</option>
                        <option>Ivrea</option>
                    </select>

            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-end">
            <button class="btn btn-outline-secondary btn-export" onclick="exportHTML();">Esporta in word
            </button>

        </div>

        <div id="id_relazioneIllustrativaDocument">
            <style>

                #relazioneIllustrativaDocument {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma;
                    font-size: 11pt;
                    text-align: justify;
                    display: block;
                }

                #relazioneIllustrativaDocument h2 {
                    font-size: 18pt;
                    margin: 30px 0 10px;
                    font-weight: 700;
                    letter-spacing: 3px;
                }

                #relazioneIllustrativaDocument h3 {
                    font-size: 16pt;
                    margin: 30px 0 20px;
                    font-weight: 500;
                }

                #relazioneIllustrativaDocument h4 {
                    color: #457FAF;
                    font-size: 16pt;
                    margin: 30px 0 20px;
                    font-weight: 800;
                    letter-spacing: 2px;
                    text-align: center;
                }

                #relazioneIllustrativaDocument h5 {
                    font-size: 15pt;
                    background-color: #457FAF;
                    color: #FFFFFF;
                    margin: 20px 0 10px;
                    font-weight: 700;
                    letter-spacing: 2px;
                    text-align: left;
                    height: auto;
                }

                #relazioneIllustrativaDocument h6 {
                    font-size: 15pt;
                    background-color: #457FAF;
                    color: #FFFFFF;
                    margin: 30px 0 20px;
                    font-weight: 700;
                    letter-spacing: 2px;
                    line-height: 200%;
                    text-align: center;
                    height: auto;
                }

                #relazioneIllustrativaDocument ul.d {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: disc;
                }

                #relazioneIllustrativaDocument ul.d li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma;
                    font-size: 11pt;
                    text-align: justify;
                }

                #relazioneIllustrativaDocument ul.a {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: lower-alpha;
                }

                #relazioneIllustrativaDocument ul.a li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma;
                    font-size: 11pt;
                    text-align: justify;
                }

                #relazioneIllustrativaDocument ul.a li ul.c {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: circle;
                }

                #relazioneIllustrativaDocument ul.a li ul.c li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma;
                    font-size: 11pt;
                    text-align: justify;
                }

                #relazioneIllustrativaDocument ul.n {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: decimal;
                }

                #relazioneIllustrativaDocument ul.n li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma;
                    font-size: 11pt;
                    text-align: justify;
                }


                #relazioneIllustrativaDocument table
                {
                    position:relative;
                    z-index: 10;
                    top: 0px;
                    left: 0px;
                    width: 100%;
                    height: auto;
                    margin: 0px;
                    padding: 0px;
                    font-family: Tahoma;
                    font-size: 11pt;
                    text-align: justify;
                    display: block;
                    border: 0px solid black;
                }
                #relazioneIllustrativaDocument table thead
                {
                    position:relative;
                    min-width: 100%;
                }
                #relazioneIllustrativaDocument table thead tr
                {
                    position:relative;
                    padding: 20px 10px;
                    border: 1px solid black;
                    font-weight: 700;
                }
                #relazioneIllustrativaDocument table thead tr th
                {
                    position:relative;
                    padding: 20px 10px;
                    border: 1px solid black;
                    font-weight: 700;
                }
                #relazioneIllustrativaDocument table tbody tr
                {
                    width: 100%;
                    padding: 20px 10px;
                    border: 1px solid black;
                }
                #relazioneIllustrativaDocument table tbody tr th
                {
                    padding: 20px 10px;
                    border: 1px solid black;
                }
                #relazioneIllustrativaDocument table tbody tr td
                {
                    padding: 20px 10px;
                    border: 1px solid black;
                }


                #relazioneIllustrativaDocument .table_Less
                {
                    position:relative;
                    z-index: 10;
                    top: 0px;
                    left: 0px;
                    width: 100%;
                    height: auto;
                    margin: 0px;
                    padding: 0px;
                    font-family: Tahoma;
                    font-size: 10pt;
                    text-align: justify;
                    display: block;
                    border: 0px solid black;
                }
                #relazioneIllustrativaDocument .table_Less thead
                {
                    position:relative;
                    min-width: 100%;
                }
                #relazioneIllustrativaDocument .table_Less thead tr
                {
                    position:relative;
                    padding: 10px 6px;
                    border: 1px solid black;
                    font-weight: 600;
                }
                #relazioneIllustrativaDocument  .table_Less thead tr th
                {
                    position:relative;
                    padding: 10px 6px;
                    border: 1px solid black;
                    font-weight: 600;
                }
                #relazioneIllustrativaDocument .table_Less tbody tr
                {
                    width: 100%;
                    padding: 10px 6px;
                    border: 1px solid black;
                }
                #relazioneIllustrativaDocument  .table_Less tbody tr th
                {
                    padding: 10px 6px;
                    border: 1px solid black;
                }
                #relazioneIllustrativaDocument  .table_Less tbody tr td
                {
                    padding: 10px 6px;
                    border: 1px solid black;
                }

            </style>


            <div id="relazioneIllustrativaDocument">

                <h3> <?php self::getInput('var0', 'titolo_ente', 'orange'); ?></h3>
                <br />
                <br />
                <h6>Relazione illustrativa</h6>
                <br />
                <br />
                Modulo I - Illustrazione degli aspetti procedurali, sintesi del contenuto del contratto ed autodichiarazione
                relative agli adempimenti della legge
                <br />
                <br />


                <table class="table_Less">
                    <thead>
                    <tr>
                        <th><b>Data di sottoscrizione</b></th>
                        <th colspan="2"><?php self::getInput('var1', 'XX/XX/20XX (RIPORTARE LA DATA DEL PRE-ACCORDO CON LA PARTE SINDACALE)', 'orange'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th><b>Periodo temporale di vigenza</b></th>
                        <td colspan="2">
                            1° GENNAIO <?php self::getInput('var2', 'anno', 'orange'); ?> – 31
                            DICEMBRE <?php self::getInput('var3', 'anno', 'orange'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 300px"><b>Composizione della delegazione trattante</b></th>

                        <td colspan="2">
                            Parte Pubblica
                            (<?php self::getInput('var4', 'nome e cognome/ruolo/qualifiche ricoperta', 'orange'); ?>):
                            <br />
                            <?php self::getInput('var5', 'xxxxx xxxxx – xxxxx xxxxx', 'orange'); ?> – Presidente
                            <br />
                            <?php self::getInput('var5', 'xxxxx xxxxx – xxxxx xxxxx', 'orange'); ?> - Componente
                            <br />
                            <?php self::getInput('var7', 'xxxxx xxxxx – xxxxx xxxxx', 'orange'); ?> - Componente
                            <br />
                            <?php self::getInput('var8', 'xxxxx xxxxx – xxxxx xxxxx', 'orange'); ?> - Componente
                            <br />
                            Organizzazioni sindacali ammesse alla contrattazione (elenco sigle):
                            <br />
                            SIND. <b>FP CGIL</b>
                            <br />
                            SIND. <b>CISL FP</b>
                            <br />
                            SIND. <b>UIL FPL</b>
                            <br />
                            SIND. <b>CSA REGIONI AUTONOMIE LOCALI</b>
                            <br />
                            R.S.U.:
                            <br />
                            Signor <?php self::getInput('var9', 'xxxxxx', 'orange'); ?>
                            <br />
                            Signor <?php self::getInput('var10', 'xxxxxx', 'orange'); ?>
                            <br />
                            Signor <?php self::getInput('var11', 'xxxxxx', 'orange'); ?>
                            <br />
                            Signor <?php self::getInput('var12', 'xxxxxx', 'orange'); ?>
                            <br />
                            Organizzazioni sindacali firmatarie (elenco sigle):
                            <br />
                            SIND. <b>FP CGIL</b> <?php self::getInput('var13', 'signor xxxxxx', 'orange'); ?>
                            <br />
                            SIND. <b>CISL FP </b> <?php self::getInput('var14', 'signor xxxxxx', 'orange'); ?>
                            <br />
                            SIND. <b>UIL FPL </b> <?php self::getInput('var15', 'signor xxxxxx', 'orange'); ?>
                            <br />
                            SIND. <b>
                                CSA REGIONI AUTONOMIE
                                LOCALI
                            </b> <?php self::getInput('var16', 'signor xxxxxx', 'orange'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><b>Soggetti destinatari</b></th>


                        <td colspan="2">
                            Personale non dirigente del Comune
                            di <?php self::getInput('var17', 'titolo_ente', 'orange'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><b>Materie trattate dal contratto integrativo (descrizione sintetica)</b></th>

                        <td colspan="2">
                            Si rinvia per un dettaglio esaustivo al Modulo 2 Illustrazione dell’articolato del contratto
                        </td>
                    </tr>


                    <tr>
                        <th rowspan="5">
                            <b>
                                Rispetto dell’iter adempimenti procedurale e degli atti propedeutici e successivi
                                alla contrattazione
                            </b>
                        </th>

                        <th rowspan="2">
                            <b>
                                Intervento dell’Organo di controllo interno.
                                Allegazione della Certificazione dell’Organo di controllo interno alla Relazione
                                illustrativa.
                            </b>
                        </th>
                        <td>
                            Non è previsto un intervento dell’Organo di controllo interno.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            L’unica certificazione dovuta è quella del Revisore dei Conti a cui è indirizzata tale
                            relazione.
                            <br />
                            In data <?php self::getInput('var18', '____', 'orange'); ?> è stata acquisita la
                            certificazione dell’Organo di controllo
                            interno <?php self::getInput('var19', '(da aggiungere prima di inviare a ARAN E CNEL)', 'orange'); ?>
                        </td>
                    </tr>


                    <tr>
                        <th rowspan="3">
                            <b>
                                Attestazione del rispetto degli obblighi di legge che in caso di inadempimento
                                comportano la sanzione del divieto di erogazione della retribuzione accessoria
                            </b>
                        </th>
                        <td>
                            È stato adottato il Piano della performance <?php self::getInput('var20', 'anno', 'orange'); ?>
                            previsto dall’art. 10 del d.lgs. 150/2009 con
                            Delibera del <?php self::getInput('var21', 'nome_soggetto_deliberante', 'orange'); ?> n.
                            del <?php self::getInput('var22', 'numero_delibera_approvazione_PEG', 'orange'); ?>
                            <?php self::getInput('var23', 'data_delibera_approvazione_PEG', 'orange'); ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            CASO A) È stato adottato il Programma triennale per Prevenzione della Corruzione con Delibera
                            del <?php self::getInput('var24', 'xxxxx', 'orange'); ?> (GIUNTA/CDA)
                            n. <?php self::getInput('var25', 'xx', 'orange'); ?>
                            del <?php self::getInput('var26', 'xx/0x/202x', 'orange'); ?> e
                            l’Amministrazione sta procedendo alla pubblicazione
                            degli atti obbligatori previsti dalle norme sul sito internet all’interno della sezione
                            “AMMINISTRAZIONE TRASPARENTE” ai sensi del D.lgs 33/2013
                            <br />
                            CASO B) Non è stato adottato il Programma triennale per la Prevenzione della Corruzione ma
                            l’Amministrazione sta provvedendo alla pubblicazione degli atti obbligatori previsti dalle norme
                            sul sito internet all’interno della sezione “AMMINISTRAZIONE TRASPARENTE” ai sensi del D.Lgs.
                            33/2013
                            <br />
                            È stato assolto l’obbligo di pubblicazione di cui al D.Lgs. 33/2013, come da attestazioni del
                            Nucleo di Valutazione/OIV pubblicata nella sezione Amministrazione Trasparente del Sito
                            Ufficiale dell’Ente.
                            <br />
                            <?php self::getTextArea('area1', ' SELEZIONARE A SECONDA DELLA SITUAZIONE DELL ENTE ', 'red'); ?>

                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            L’organo di valutazione ha validato la relazione sulla performance relativa all’anno precedente
                            ai sensi dell’articolo 14, comma 6. del D.Lgs. n. 150/2009 di cui al Verbale
                            n. <?php self::getInput('var27', 'xx/0x/202x', 'orange'); ?>. La
                            Relazione della Performance relativa all’anno corrente verrà validata in fase di
                            consuntivazione.
                        </td>
                    </tr>
                    <tr>
                        <th colspan="3">Eventuali osservazioni: <?php self::getTextArea('area2', '', 'black'); ?></th>
                    </tr>

                    </tbody>
                </table>



                <br />
                <h6>
                    Modulo 2 Illustrazione dell’articolato del contratto<br />
                    (Attestazione della compatibilità con i vincoli derivanti da norme di legge e di contratto nazionale
                    –modalità di utilizzo delle risorse accessorie ‑ risultati attesi ‑ altre informazioni utili)
                </h6>
                <br />
                <ul class="a">
                    <li>
                        <b>
                            Illustrazione di quanto disposto dal contratto integrativo, in modo da fornire un quadro
                            esaustivo
                            della
                            regolamentazione di ogni ambito/materia e delle norme legislative e contrattuali che legittimano
                            la
                            contrattazione integrativa della specifica materia trattata;
                        </b>

                        <br />
                        <br />
                        Per l’anno ><?php self::getInput('var28', 'anno', 'orange'); ?> già con la determina di
                        costituzione del Fondo
                        n.><?php self::getInput('var29', 'numero_determina_approvazione', 'orange'); ?> del
                        <?php self::getInput('var30', 'data_determina_approvazione', 'blue'); ?>, il responsabile ha reso
                        indisponibile alla contrattazione
                        ai sensi dell’art. 68
                        comma 1 del CCNL 21.5.2018 alcuni compensi gravanti sul fondo (indennità di comparto, incrementi per
                        progressione economica, ecc) e in particolare è stato sottratto dalle risorse ancora contrattabili
                        un
                        importo complessivo pari ad € <?php self::getInput('var31', 'f93', 'orange'); ?>, destinato a
                        retribuire le indennità
                        fisse e ricorrenti già determinate
                        negli anni precedenti.
                        <br />
                        <br />
                        Per quanto riguarda il contratto decentrato per la ripartizione delle risorse
                        dell’anno <?php self::getInput('var32', 'anno', 'orange'); ?> le delegazioni
                        hanno confermato la destinazione delle risorse già in essere negli anni precedenti, destinando,
                        inoltre, per
                        l’anno:
                        <?php if (self::checkOptionalValues('F115')): ?>
                        <ol>
                            <li>
                                Progressioni economiche orizzontali specificatamente contrattate nel CCDI dell'anno (art. 68
                                comma 1 CCNL
                                21.5.2018) € <?php self::getInput('var33', 'R55', 'orange'); ?>.
                                Viene ripreso il testo del contratto siglato per l’anno 202x con il quale sono stati
                                definiti i criteri per
                                l’attribuzione delle progressioni:

                                <?php self::getTextArea('area3', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                <br />
                                RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                <br /><br />
                                Art. 68 comma 1 CCNL 21.5.2018.
                                <br /><br />
                                Gli enti rendono annualmente disponibili tutte le risorse confluite nel Fondo risorse
                                decentrate, al netto
                                delle risorse necessarie per corrispondere i differenziali di progressione economica, al
                                personale
                                beneficiario delle stesse in anni precedenti.
                                <br />
                                Art. 16 CCNL 21.5.2018.
                                <br />
                                <ol>
                                    <li>
                                        All’interno di ciascuna categoria è prevista una progressione economica che si
                                        realizza mediante
                                        l’acquisizione, in sequenza, dopo il trattamento tabellare iniziale, di successivi
                                        incrementi retributivi,
                                        corrispondenti ai valori delle diverse posizioni economiche a tal fine espressamente
                                        previste.
                                    </li>
                                    <li>
                                        La progressione economica di cui al comma 1, nel limite delle risorse effettivamente
                                        disponibili, è
                                        riconosciuta, in modo selettivo, ad una quota limitata di dipendenti, determinata
                                        tenendo conto anche degli
                                        effetti applicativi della disciplina del comma 6.
                                    </li>
                                    <li>
                                        Le progressioni economiche sono attribuite in relazione alle risultanze della
                                        valutazione della
                                        performance individuale del triennio che precede l’anno in cui è adottata la
                                        decisione di attivazione
                                        dell’istituto, tenendo conto eventualmente a tal fine anche dell’esperienza maturata
                                        negli ambiti
                                        professionali di riferimento, nonché delle competenze acquisite e certificate a
                                        seguito di processi
                                        formativi.
                                    </li>
                                    <li>
                                        Gli oneri relativi al pagamento dei maggiori compensi spettanti al personale che ha
                                        beneficiato della
                                        disciplina sulle progressioni economiche orizzontali sono interamente a carico della
                                        componente stabile del
                                        Fondo risorse decentrate di cui all’art. 67.
                                    </li>
                                    <li>
                                        Gli oneri di cui al comma 4 sono comprensivi anche della quota della tredicesima
                                        mensilità.
                                    </li>
                                    <li>
                                        Ai fini della progressione economica orizzontale, il lavoratore deve essere in
                                        possesso del requisito di
                                        un periodo minimo di permanenza nella posizione economica in godimento pari a
                                        ventiquattro mesi.
                                    </li>
                                    <li>
                                        L’attribuzione della progressione economica orizzontale non può avere decorrenza
                                        anteriore al 1° gennaio
                                        dell’anno nel quale viene sottoscritto il contratto integrativo che prevede
                                        l’attivazione dell’istituto, con
                                        la previsione delle necessarie risorse finanziarie.
                                    </li>
                                    <li>
                                        L’esito della procedura selettiva ha una vigenza limitata al solo anno per il quale
                                        è stata prevista
                                        l’attribuzione della progressione economica.
                                    </li>
                                    <li>
                                        Il personale comandato o distaccato presso enti, amministrazioni, aziende ha diritto
                                        di partecipare alle
                                        selezioni per le progressioni orizzontali previste per il restante personale
                                        dell’ente di effettiva
                                        appartenenza. A tal fine l’ente di appartenenza concorda le modalità per acquisire
                                        dall’ente di
                                        utilizzazione le informazioni e le eventuali valutazioni richieste secondo la
                                        propria disciplina.
                                    </li>
                                </ol>
                                <br />
                                Art. 23 D.Lgs. 150/2009 Progressioni economiche.
                                <br />
                                <ol>
                                    <li>
                                        Le amministrazioni pubbliche riconoscono selettivamente le progressioni economiche
                                        di cui all'articolo
                                        52, comma 1-bis, del decreto legislativo 30 marzo 2001, n.165, come introdotto
                                        dall'articolo 62 del presente
                                        decreto, sulla base di quanto previsto dai contratti collettivi nazionali e
                                        integrativi di lavoro e nei
                                        limiti delle risorse disponibili.
                                    </li>
                                    <li>
                                        Le progressioni economiche sono attribuite in modo selettivo, ad una quota limitata
                                        di dipendenti, in
                                        relazione allo sviluppo delle competenze professionali ed ai risultati individuali e
                                        collettivi rilevati dal
                                        sistema di valutazione.
                                    </li>
                                </ol>
                                <br />
                                <?php endif; ?>
                                Articolo 52 Disciplina delle mansioni D.Lgs. 165/2001.
                                <ol>
                                    <li>
                                        bis. Le progressioni all'interno della stessa area avvengono secondo principi di
                                        selettività, in funzione
                                        delle qualità culturali e professionali, dell'attività svolta e dei risultati
                                        conseguiti, attraverso
                                        l'attribuzione di fasce di merito. La valutazione positiva conseguita dal dipendente
                                        per almeno tre anni
                                        costituisce titolo rilevante ai fini della progressione economica.
                                        <br />
                                        <?php if (self::checkOptionalValues('F125')): ?>
                                        <br />
                                    </li>
                                </ol>
                            </li>
                            <li>
                                Indennità di turno (art. 68 comma 2 lett. d CCNL 21.5.2018)
                                € <?php self::getInput('var34', 'R65', 'orange'); ?>.

                                <?php self::getTextArea('area4', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>

                                <br />
                                RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                <br />
                                <br />
                                Art. 68 comma 2 lett. d CCNL 21.5.2018.
                                <br />
                                <br />
                                D) Il pagamento delle indennità di turno;
                                <br />
                                <br />

                                Art. 23 CCNL 22.5.2018.
                                <ol>
                                    <li>
                                        Gli enti, in relazione alle proprie esigenze organizzative o di servizio funzionali,
                                        possono istituire
                                        turni giornalieri di lavoro. Il turno consiste in un’effettiva rotazione del
                                        personale in prestabilite
                                        articolazioni giornaliere.
                                    </li>
                                    <li>
                                        Le prestazioni lavorative svolte in turnazione, ai fini della corresponsione della
                                        relativa indennità,
                                        devono essere distribuite nell’arco di un mese, sulla base della programmazione
                                        adottata, in modo da attuare
                                        una distribuzione equilibrata ed avvicendata dei turni effettuati in orario
                                        antimeridiano, pomeridiano e, se
                                        previsto, notturno, in relazione all’articolazione adottata dall’ente.
                                    </li>
                                    <li>
                                        Per l'adozione dell'orario di lavoro su turni devono essere osservati i seguenti
                                        criteri:
                                        <ul class="a">
                                            <li>
                                                la ripartizione del personale nei vari turni deve avvenire sulla base delle
                                                professionalità necessarie in
                                                ciascun turno;
                                            </li>
                                            <li>
                                                l'adozione dei turni può anche prevedere una parziale e limitata
                                                sovrapposizione tra il personale
                                                subentrante e quello del turno precedente, con durata limitata alle esigenze
                                                dello scambio delle consegne;
                                            </li>
                                            <li>
                                                all'interno di ogni periodo di 24 ore deve essere garantito un periodo di
                                                riposo di almeno 11 ore
                                                consecutive;
                                            </li>
                                            <li>
                                                i turni diurni, antimeridiani e pomeridiani, possono essere attuati in
                                                strutture operative che prevedano
                                                un orario di servizio giornaliero di almeno 10 ore;
                                            </li>
                                            <li>
                                                per turno notturno si intende il periodo lavorativo ricompreso dalle ore 22
                                                alle ore 6 del giorno
                                                successivo; per turno notturno-festivo si intende quello che cade nel
                                                periodo compreso tra le ore 22 del
                                                giorno prefestivo e le ore 6 del giorno festivo e dalle ore 22 del giorno
                                                festivo alle ore 6 del giorno
                                                successivo.
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        Fatte salve eventuali esigenze eccezionali o quelle dovute a eventi o calamità
                                        naturali, il numero dei
                                        turni notturni effettuabili nell'arco del mese da ciascun dipendente non può essere
                                        superiore a 10.
                                    </li>
                                    <li>
                                        Al fine di compensare interamente il disagio derivante dalla particolare
                                        articolazione dell’orario di
                                        lavoro, al personale turnista è corrisposta una indennità, i cui valori sono
                                        stabiliti come segue:
                                        <ul class="a">
                                            <li>
                                                turno diurno, antimeridiano e pomeridiano (tra le 6,00 e le 22,00):
                                                maggiorazione oraria del 10% della
                                                retribuzione di cui all’art. 10, comma 2, lett. c) del CCNL del 9.5.2006;
                                            </li>
                                            <li>
                                                turno notturno o festivo: maggiorazione oraria del 30% della retribuzione di
                                                cui all’art. 10, comma 2,
                                                lett. c) del CCNL del 9.5.2006;
                                            </li>
                                            <li>
                                                turno festivo-notturno: maggiorazione oraria del 50% della retribuzione di
                                                cui all’art. 10, comma 2,
                                                lett. c) del CCNL del 9.5.2006.
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        L’indennità di cui al comma 5, è corrisposta per i soli periodi di effettiva
                                        prestazione in turno.
                                    </li>
                                    <li>
                                        Agli oneri derivanti dal presente articolo si fa fronte, in ogni caso, con le
                                        risorse previste dall’art.
                                        67.
                                    </li>
                                </ol>
                                <ul class="a">
                                    <li>
                                        8. Il personale che si trovi in particolari situazioni personali e familiari, di cui
                                        all’art.27, comma 4
                                        può, a richiesta, essere escluso dalla effettuazione di turni notturni, anche in
                                        relazione a quanto previsto dall’art. 53,
                                        comma 2, del D.Lgs. n. 151/2001. Sono comunque escluse le donne dall'inizio dello
                                        stato di gravidanza e nel periodo
                                        di allattamento fino ad un anno di vita del bambino.
                                    </li>
                                </ul>
                                <br />
                            </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F293')): ?>
                                <li>
                                    Indennità condizioni di lavoro (Art. 68 comma 2 lett. c CCNL 2018) (Maneggio valori,
                                    attività disagiate e
                                    esposte a rischi) € <?php self::getInput('var35', 'R145', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var36', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri di
                                    attribuzione delle seguenti indennità:
                                    <br />
                                    <?php self::getTextArea('area5', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br /><br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 70 bis CCNL 21.5.2018.
                                    <br />
                                    <ol>
                                        <li>
                                            Gli enti corrispondono una unica “indennità condizioni di lavoro” destinata a
                                            remunerare lo svolgimento
                                            di attività: a) disagiate; b) esposte a rischi e, pertanto, pericolose o dannose
                                            per la salute; c)
                                            implicanti il maneggio di valori.
                                        </li>
                                        <li>
                                            L’indennità di cui al presente articolo è commisurata ai giorni di effettivo
                                            svolgimento delle attività
                                            di cui al comma 1, entro i seguenti valori minimi e massimi giornalieri: euro
                                            1,00 – euro 10,00.
                                        </li>
                                        <li>
                                            La misura di cui al comma 1 è definita in sede di contrattazione integrativa di
                                            cui all’art. 7, comma 4,
                                            sulla base dei seguenti criteri: a) valutazione dell’effettiva incidenza di
                                            ciascuna delle causali di cui al
                                            comma 1 nelle attività svolte dal dipendente; b) caratteristiche istituzionali,
                                            dimensionali, sociali e
                                            ambientali degli enti interessati e degli specifici settori di attività.
                                        </li>
                                        <li>
                                            Gli oneri per la corresponsione dell’indennità di cui al presente articolo sono
                                            a carico del Fondo
                                            risorse decentrate di cui all’art. 67.
                                        </li>
                                        <li>
                                            La presente disciplina trova applicazione a far data dal primo contratto
                                            integrativo successivo alla
                                            stipulazione del presente CCNL.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F128')): ?>
                                <li>
                                    Indennità di reperibilità (art. 68 comma 2 lett. d CCNL 21.5.2018)
                                    € <?php self::getInput('var37', 'R71', 'orange'); ?>.
                                    <br />
                                    <?php self::getTextArea('area6', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 68 comma 2 lett. d CCNL 21.5.2018.
                                    <br />
                                    <br />
                                    D) il pagamento delle indennità di reperibilità;
                                    <br />
                                    <br />
                                    Art. 24 CCNL 21.5.2018.
                                    <ol>
                                        <li>
                                            Per le aree di pronto intervento individuate dagli enti, può essere istituito il
                                            servizio di pronta
                                            reperibilità. Esso è remunerato con la somma di € 10,33 per 12 ore al giorno. Ai
                                            relativi oneri si fa fronte
                                            in ogni caso con le risorse previste dall’art. 67. Tale importo è raddoppiato in
                                            caso di reperibilità
                                            cadente in giornata festiva, anche infrasettimanale o di riposo settimanale
                                            secondo il turno assegnato.
                                        </li>
                                        <li>
                                            In caso di chiamata l’interessato dovrà raggiungere il posto di lavoro assegnato
                                            nell’arco di trenta
                                            minuti.
                                        </li>
                                        <li>
                                            Ciascun dipendente non può essere messo in reperibilità per più di 6 volte in un
                                            mese; gli enti
                                            assicurano la rotazione tra più soggetti anche volontari.
                                        </li>
                                        <li>
                                            In sede di contrattazione integrativa, secondo quanto previsto dall’art. 7,
                                            comma 4, è possibile elevare
                                            il limite di cui al comma 3 nonché la misura dell’indennità di cui al comma 1,
                                            fino ad un massimo di €
                                            13,00.
                                        </li>
                                        <li>
                                            L’indennità di reperibilità di cui ai commi 1 e 4 non compete durante l’orario
                                            di servizio a qualsiasi
                                            titolo prestato. Detta indennità è frazionabile in misura non inferiore a
                                            quattro ore ed è corrisposta in
                                            proporzione alla sua durata oraria maggiorata, in tal caso, del 10%. Qualora la
                                            pronta reperibilità cada di
                                            domenica o comunque di riposo settimanale secondo il turno assegnato, il
                                            dipendente ha diritto ad un giorno
                                            di riposo compensativo anche se non è chiamato a rendere alcuna prestazione
                                            lavorativa. Nella settimana in
                                            cui fruisce del riposo compensativo, il lavoratore è tenuto a rendere
                                            completamente l'orario ordinario di
                                            lavoro previsto. La fruizione del riposo compensativo non comporta, comunque,
                                            alcuna riduzione dell’orario
                                            di lavoro settimanale.
                                        </li>
                                        <li>
                                            In caso di chiamata, le ore di lavoro prestate vengono retribuite come lavoro
                                            straordinario o compensate,
                                            a richiesta, ai sensi dell’art.38, comma 7, e dell’art.38bis, del CCNL del
                                            14.9.2000 o con equivalente
                                            recupero orario; per le stesse ore è esclusa la percezione del compenso di cui
                                            ai commi 1 e 4.
                                        </li>
                                        <li>
                                            La disciplina del comma 6 non trova applicazione nell’ipotesi di chiamata del
                                            lavoratore in reperibilità
                                            cadente nella giornata del riposo settimanale, secondo il turno assegnato; per
                                            tale ipotesi trova
                                            applicazione, invece, la disciplina di cui all’art.24, comma 1, del CCNL del
                                            14.9.2000.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F132')): ?>
                                <li>
                                    Indennità Specifiche Responsabilità (art. 68, c. 2, lett. e. CCNL 21.5.2018 ex art. 17,
                                    c. 2, lett. f.
                                    CCNL 01/04/99) €<?php self::getInput('var38', 'R75', 'orange'); ?> .
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per l’anno 202x con il quale sono stati
                                    definiti i criteri di
                                    attribuzione dell’indennità di Specifiche responsabilità:
                                    <br />
                                    <?php self::getTextArea('area7', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 70-quinquies CCNL 21.5.2018.
                                    <ol>
                                        <li>
                                            Per compensare l’eventuale esercizio di compiti che comportano specifiche
                                            responsabilità, al personale
                                            delle categorie B, C e D, che non risulti incaricato di posizione organizzativa
                                            ai sensi dell’art.13 e
                                            seguenti, può essere riconosciuta una indennità di importo non superiore a €
                                            3.000 annui lordi.
                                        </li>
                                    </ol>
                                </li>
                            <?php endif; ?>
                            <br />
                            <?php if (self::checkOptionalValues('F295')): ?>

                                <li>
                                    Indennità di funzione (Art. 68 comma 2 lett. f CCNL 21.5.2018 e art. 56 sexies CCNL
                                    21.5.2018)
                                    (Vigilanza) € <?php self::getInput('var39', 'R135', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var40', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri di
                                    attribuzione dell’indennità di Specifiche responsabilità:
                                    <br />
                                    <?php self::getTextArea('area8', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 68 comma 2 lett. f CCNL 21.5.2018.
                                    <br />
                                    <br />
                                    f) indennità di funzione di cui all’art. 56-sexies.
                                    <br />
                                    <br />
                                    Art. 56 sexies CCNL 21.5.2018.
                                    <ol>
                                        <li>
                                            Gli enti possono erogare al personale inquadrato nelle categorie C e D, che non
                                            risulti incaricato di
                                            posizione organizzativa, una indennità di funzione per compensare l’esercizio di
                                            compiti di responsabilità
                                            connessi al grado rivestito.
                                        </li>
                                        <li>
                                            L’ammontare dell’indennità di cui al comma 1 è determinato, tenendo conto
                                            specificamente del grado
                                            rivestito e delle connesse responsabilità, nonché delle peculiarità
                                            dimensionali, istituzionali, sociali e
                                            ambientali degli enti, fino a un massimo di € 3.000 annui lordi, da
                                            corrispondere per dodici mensilità.
                                        </li>
                                        <li>
                                            Il valore dell’indennità di cui al presente articolo, nonché i criteri per la
                                            sua erogazione, nel
                                            rispetto di quanto previsto al comma 2, sono determinati in sede di
                                            contrattazione integrativa di cui
                                            all’art. 7.
                                        </li>
                                        <li>
                                            L’indennità di cui al comma 1 sostituisce per il personale di cui al presente
                                            titolo l’indennità di
                                            specifiche responsabilità, di cui all’art. 70 quinquies, comma 1.
                                        </li>
                                        <li>
                                            L’indennità di cui al presente articolo: a) è cumulabile con l’indennità di
                                            turno, di cui all’art. 23,
                                            comma 5; b) è cumulabile con l’indennità di cui all’art. 37, comma 1, lett. b),
                                            del CCNL del 6.7.1995 e
                                            successive modificazioni ed integrazioni; c) è cumulabile con l’indennità di cui
                                            all’art. 56-quinquies; d) è
                                            cumulabile con i compensi correlati alla performance individuale e collettiva;
                                            e) non è cumulabile con le
                                            indennità di cui all’art. 70-quinquies;
                                        </li>
                                        <li>
                                            Gli oneri per la corresponsione dell’indennità di cui al presente articolo sono
                                            a carico del Fondo
                                            risorse decentrate di cui all’art. 67.
                                        </li>
                                        <li>
                                            La presente disciplina trova applicazione a far data dal primo contratto
                                            integrativo successivo alla
                                            stipulazione del presente CCNL.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F133')): ?>
                                <li>
                                    Specifiche responsabilità (art. 68, c. 2, lett. e. CCNL 21.5.2018 ex art. 17, c. 2,
                                    lett. i. CCNL
                                    01/04/99) € <?php self::getInput('var41', 'R77', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var42', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri di
                                    attribuzione dell’indennità di Specifiche responsabilità:
                                    <br />
                                    <?php self::getTextArea('area9', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 70-quinquies comma 2 CCNL 21.5.2018.
                                    <ol start="2">
                                        <li>
                                            Un’indennità di importo massimo non superiore a € 350 annui lordi, può essere
                                            riconosciuta al lavoratore,
                                            che non risulti incaricato di posizione organizzativa ai sensi dell’art.13 e
                                            seguenti, per compensare: a) le
                                            specifiche responsabilità del personale delle categorie B, C e D attribuite con
                                            atto formale degli enti,
                                            derivanti dalle qualifiche di Ufficiale di stato civile ed anagrafe ed Ufficiale
                                            elettorale nonché di
                                            responsabile dei tributi stabilite dalle leggi; b) i compiti di responsabilità
                                            eventualmente affidati agli
                                            archivisti informatici nonché agli addetti agli uffici per le relazioni con il
                                            pubblico ed ai formatori
                                            professionali; c) le specifiche responsabilità affidate al personale addetto ai
                                            servizi di protezione
                                            civile; d) le funzioni di ufficiale giudiziario attribuite ai messi
                                            notificatori.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F294')): ?>

                                <li>
                                    Indennità di servizio esterno (Art. 68 comma 2 lett. f. CCNL 21.5.2018 e art. 56
                                    quinquies CCNL
                                    21.5.2018) (Vigilanza) € <?php self::getInput('var043', 'R134', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var43', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri di
                                    attribuzione dell’indennità di Specifiche responsabilità:
                                    <br />
                                    <?php self::getTextArea('area10', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. f CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    f) indennità di servizio esterno di cui all’art.56-quater;
                                    <br />
                                    <br /> Art. 56 quinquies CCNL 21.5.2018.
                                    <ol>
                                        <li>
                                            Al personale che, in via continuativa, rende la prestazione lavorativa ordinaria
                                            giornaliera in servizi
                                            esterni di vigilanza, compete una indennità giornaliera, il cui importo è
                                            determinato entro i seguenti
                                            valori minimi e massimi giornalieri: euro 1,00 - euro 10,00.
                                        </li>
                                        <li>
                                            L’indennità di cui al comma 1 è commisurata alle giornate di effettivo
                                            svolgimento del servizio esterno e
                                            compensa interamente i rischi e disagi connessi all’espletamento dello stesso in
                                            ambienti esterni.
                                        </li>
                                        <li>
                                            L’indennità di cui al presenta articolo: a) è cumulabile con l’indennità di
                                            turno, di cui all’art. 23,
                                            comma 5; b) è cumulabile con le indennità di cui all’art. 37, comma 1, lett. b),
                                            del CCNL del 6.7.1995 e
                                            successive modificazioni ed integrazioni; c) è cumulabile con i compensi
                                            connessi alla performance
                                            individuale e collettiva; d) non è cumulabile con l’indennità di cui all’art.
                                            70-bis.
                                        </li>
                                        <li>
                                            Gli oneri per la corresponsione dell’indennità di cui al presente articolo sono
                                            a carico del Fondo
                                            risorse decentrate di cui all’art. 67.
                                        </li>
                                        <li>
                                            La presente disciplina trova applicazione a far data dal primo contratto
                                            integrativo successivo alla
                                            stipulazione del presente CCNL.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F134')): ?>
                                <li>
                                    Particolare compenso incentivante personale Unioni dei comuni (art. 68, c. 1 CCNL
                                    21.5.2018)
                                    €<?php self::getInput('var44', 'R79', 'orange'); ?> .
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var45', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    attribuire il compenso incentivante:
                                    <br />
                                    <?php self::getTextArea('area11', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 13 c.5 lett. a. CCNL 22.1.2004.
                                    <ol start="5">
                                        l
                                        <li>
                                            Al fine di favorire l’utilizzazione temporanea anche parziale del personale
                                            degli enti da parte
                                            dell’unione, la contrattazione decentrata della stessa unione può disciplinare,
                                            con oneri a carico delle
                                            risorse disponibili ai sensi del comma 3:
                                        </li>
                                    </ol>
                                    <ul class="a">
                                        <li>
                                            l’attribuzione di un particolare compenso incentivante, di importo lordo
                                            variabile, in base alla
                                            categoria di appartenenza e alle mansioni affidate, non superiore a € 25, su
                                            base mensile, strettamente
                                            correlato alle effettive prestazioni lavorative;
                                        </li>
                                    </ul>
                                    <br />
                                </li>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F135')): ?>
                                <li>
                                    Centri estivi asili nido (art. 68, c. 1 CCNL 21.5.2018 e art 31 comma 5 CCNL 14/9/ 2000)
                                    € <?php self::getInput('var46', 'R81', 'orange'); ?> .
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var47', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    attribuire l’indennità prevista per il personale del nido estivo:
                                    <br />
                                    <?php self::getTextArea('area12', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art 31 comma 5 CCNL 14 -9- 2000.
                                    <ol start="5">
                                        <li>
                                            Il calendario scolastico, che non può in ogni caso superare le 42 settimane,
                                            prevede l’interruzione per
                                            Natale e Pasqua, le cui modalità attuative sono definite in sede di
                                            concertazione. In tali periodi e negli
                                            altri di chiusura delle scuole il personale è a disposizione per attività di
                                            formazione ed aggiornamento
                                            programmata dall’ente o per attività lavorative connesse al profilo di
                                            inquadramento fermo restando il
                                            limite definito nei commi precedenti. Attività ulteriori, rispetto a quelle
                                            definite nel calendario
                                            scolastico, possono essere previste a livello di ente, in sede di concertazione,
                                            per un periodo non
                                            superiore a quattro settimane, da utilizzarsi sia per le attività dei nidi che
                                            per altre attività
                                            d’aggiornamento professionale, di verifica dei risultati e del piano di lavoro,
                                            nell’ambito dei progetti di
                                            cui all’art.17, co.1, lett. a) del CCNL dell’1.4.1999; gli incentivi economici
                                            di tali attività sono
                                            definiti in sede di contrattazione integrativa decentrata utilizzando le risorse
                                            di cui all’art.15 del
                                            citato CCNL.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F136')): ?>

                                <li>
                                    Maggiorazione per il personale che presta attività lavorativa nel giorno destinato al
                                    riposo settimanale
                                    (Art. 68 comma 2 lett. d CCNL 21.5.2018 e art.24, comma 1 CCNL 14.9.2000)
                                    € <?php self::getInput('var48', 'R83', 'orange'); ?>.
                                    <br />
                                    <?php self::getTextArea('area13', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. d CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    D) compensi di cui all’art. 24, comma 1 del CCNL del 14.9.2000;
                                    Art. 24 comma 1 CCNL 14.9.2000.
                                    <ol>
                                        <li>
                                            Al dipendente che per particolari esigenze di servizio non usufruisce del giorno
                                            di riposo settimanale
                                            deve essere corrisposta la retribuzione giornaliera di cui all’art.52, comma 2,
                                            lett. b) maggiorata del 50%,
                                            con diritto al riposo compensativo da fruire di regola entro 15 giorni e
                                            comunque non oltre il bimestre
                                            successivo.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F137')): ?>

                                <li>
                                    Premi collegati alla performance organizzativa (art. 68, c. 2, lett. a. CCNL 22.5.2018)
                                    € <?php self::getInput('var49', 'R85', 'orange'); ?>.
                                    (Opzionale)
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var50', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione della performance:
                                    <br />
                                    <?php self::getTextArea('area14', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art.18 D.Lgs. 150/2009 “Criteri e modalità per la valorizzazione del merito ed
                                    incentivazione della
                                    performance”.
                                    <ol>
                                        <li>
                                            Le amministrazioni pubbliche promuovono il merito e il miglioramento della
                                            performance organizzativa e
                                            individuale, anche attraverso l'utilizzo di sistemi premianti selettivi, secondo
                                            logiche meritocratiche,
                                            nonché valorizzano i dipendenti che conseguono le migliori performance
                                            attraverso l'attribuzione selettiva
                                            di incentivi sia economici sia di carriera.
                                        </li>
                                        <li>
                                            E' vietata la distribuzione in maniera indifferenziata o sulla base di
                                            automatismi di incentivi e premi
                                            collegati alla performance in assenza delle verifiche e attestazioni sui sistemi
                                            di misurazione e
                                            valutazione adottati ai sensi del presente decreto.
                                            <br />
                                            <br />
                                            Parere Aran 499-18A8.
                                            <br />
                                            <br />
                                            Riteniamo che la produttività collettiva possa essere correlata al conseguimento
                                            di specifici risultati e/o
                                            obiettivi assegnati dall'ente ad un gruppo o a una struttura, con la
                                            individuazione anche di uno specifico
                                            finanziamento definito in sede di contrattazione decentrata. La contrattazione
                                            decentrata deve,
                                            naturalmente, stabilire anche i criteri per la valutazione, da parte dei
                                            dirigenti, dell'apporto dei singoli
                                            lavoratori al conseguimento del risultato complessivo.
                                            <br />
                                            <br />
                                            Suggeriamo, in ogni caso, di non attribuire troppo rilievo all'una o all'altra
                                            forma di incentivazione;
                                            nella sostanza occorre sempre assicurare un corretto percorso di valutazione che
                                            ogni ente è tenuto ad
                                            adottare, previa concertazione, ai sensi dell'art.6 del CCNL del 31.3.99.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F138')): ?>

                                <li>
                                    Premi collegati alla performance individuale (art. 68, c. 2, lett. b. CCNL 22.5.2018)
                                    € <?php self::getInput('var50', 'R87', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var51', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per la distribuzione della performance
                                    individuale:
                                    <br />
                                    <br />
                                    INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI
                                    DISTRIBUZIONE DELLE RISORSE E
                                    INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA
                                    DISTRIBUIRE PER QUESTA
                                    VOCE
                                    <br />
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. B CCNL 22.5.2018.
                                    <br />
                                    <br />

                                    B) premi correlati alla performance individuale.
                                    <br />
                                    <br />

                                    Art. 69 CCNL 21.5.2018.
                                    <br />
                                    <br />
                                    <ol>
                                        <li>
                                            Ai dipendenti che conseguano le valutazioni più elevate, secondo quanto previsto
                                            dal sistema di
                                            valutazione dell’ente, è attribuita una maggiorazione del premio individuale di
                                            cui all’art. 68, comma 2,
                                            lett. b.), che si aggiunge alla quota di detto premio attribuita al personale
                                            valutato positivamente sulla
                                            base dei criteri selettivi.
                                        </li>
                                        <li>
                                            La misura di detta maggiorazione, definita in sede di contrattazione
                                            integrativa, non potrà comunque
                                            essere inferiore al 30% del valore medio pro-capite dei premi attribuiti al
                                            personale valutato positivamente
                                            ai sensi del comma 1.
                                        </li>
                                        <li>
                                            La contrattazione integrativa definisce altresì, preventivamente, una limitata
                                            quota massima di personale
                                            valutato, a cui tale maggiorazione può essere attribuita.
                                        </li>
                                    </ol>
                                    <br />
                                    Art.18 D.Lgs. 150/2009 “Criteri e modalità per la valorizzazione del merito ed
                                    incentivazione della
                                    performance”
                                    <ol>
                                        <li>
                                            Le amministrazioni pubbliche promuovono il merito e il miglioramento della
                                            performance organizzativa e
                                            individuale, anche attraverso l'utilizzo di sistemi premianti selettivi, secondo
                                            logiche meritocratiche,
                                            perché valorizzano i dipendenti che conseguono le migliori performance
                                            attraverso l'attribuzione selettiva
                                            di incentivi sia economici sia di carriera.
                                        </li>
                                        <li>
                                            È vietata la distribuzione in maniera indifferenziata o sulla base di
                                            automatismi di incentivi e premi
                                            collegati alla performance in assenza delle verifiche e attestazioni sui sistemi
                                            di misurazione e
                                            valutazione adottati ai sensi del presente decreto.
                                            <br />
                                            <br />

                                            Parere Aran 499-18A8.
                                            <br />
                                            <br />

                                            La produttività individuale potrebbe essere individuata come momento di verifica
                                            e di valutazione di ogni
                                            singolo lavoratore, da parte del competente dirigente, con riferimento agli
                                            impegni di lavoro specifici
                                            derivanti dall'affidamento dei compiti da parte del competente dirigente.
                                            <br />
                                            <br />

                                            Suggeriamo, in ogni caso, di non attribuire troppo rilievo all'una o all'altra
                                            forma di incentivazione;
                                            nella sostanza occorre sempre assicurare un corretto percorso di valutazione che
                                            ogni ente è tenuto ad
                                            adottare, previa concertazione, ai sensi dell'art.6 del CCNL del 31.3.99.
                                        </li>
                                    </ol>

                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F186')): ?>

                                <li>
                                    Premi collegati alla performance organizzativa - Incentivazione legata al raggiungimento
                                    di obiettivi ai
                                    sensi dell'art. 67 c.5 lett. b parte variabile (art. 68, c. 2, lett. a. CCNL 21.5.2018)
                                    € <?php self::getInput('var52', 'R88', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var53', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione di tali risorse:
                                    <br />
                                    <?php self::getTextArea('area15', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. a CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    A) premi correlati alla performance organizzativa;
                                    <br />
                                    Art.18 D.Lgs. 150/2009 “Criteri e modalità per la valorizzazione del merito ed
                                    incentivazione della
                                    performance”.
                                    <ol>
                                        <li>
                                            Le amministrazioni pubbliche promuovono il merito e il miglioramento della
                                            performance organizzativa e
                                            individuale, anche attraverso l'utilizzo di sistemi premianti selettivi, secondo
                                            logiche meritocratiche,
                                            perché valorizzano i dipendenti che conseguono le migliori performance
                                            attraverso l'attribuzione selettiva
                                            di incentivi sia economici sia di carriera.
                                        </li>
                                        <li>
                                            È vietata la distribuzione in maniera indifferenziata o sulla base di
                                            automatismi di incentivi e premi
                                            collegati alla performance in assenza delle verifiche e attestazioni sui sistemi
                                            di misurazione e
                                            valutazione adottati ai sensi del presente decreto.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F296')): ?>
                                <li>
                                    Premi collegati alla performance organizzativa per obiettivi finanziati da risorse art
                                    67 c. 5 lett. b)
                                    di potenziamento dei servizi di controllo finalizzati alla sicurezza urbana e stradale
                                    art. 56 quater CCNL
                                    21.5.2018) € <?php self::getInput('var54', 'R136', 'orange'); ?> .
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var55', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione di tali risorse:
                                    <br />
                                    <?php self::getTextArea('area16', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 68 comma 2 lett. a CCNL 21.5.2018
                                    <br />
                                    <br />
                                    A) premi correlati alla performance organizzativa;
                                    <br />
                                    <br />
                                    Art.18 D.Lgs. 150/2009 “Criteri e modalità per la valorizzazione del merito ed
                                    incentivazione della
                                    performance”
                                    <ol>
                                        <li>
                                            < Le amministrazioni pubbliche promuovono il merito e il miglioramento della
                                            performance organizzativa e
                                            individuale, anche attraverso l'utilizzo di sistemi premianti selettivi, secondo
                                            logiche meritocratiche,
                                            perché valorizzano i dipendenti che conseguono le migliori performance
                                            attraverso l'attribuzione selettiva
                                            di incentivi sia economici sia di carriera.
                                        </li>
                                        <li>
                                            È vietata la distribuzione in maniera indifferenziata o sulla base di
                                            automatismi di incentivi e premi
                                            collegati alla performance in assenza delle verifiche e attestazioni sui sistemi
                                            di misurazione e
                                            valutazione adottati ai sensi del presente decreto.
                                        </li>
                                    </ol>
                                    <br />
                                    Art. 56 quater CCNL 21.5.2018.
                                    <ol>
                                        <li>
                                            I proventi delle sanzioni amministrative pecuniarie riscossi dagli enti, nella
                                            quota da questi
                                            determinata ai sensi dell’art. 208, commi 4 lett. c.), e 5, del D.Lgs.n.285/1992
                                            sono destinati, in coerenza
                                            con le previsioni legislative, alle seguenti finalità in favore del personale:
                                            a) contributi datoriali al
                                            Fondo di previdenza complementare Perseo-Sirio; è fatta salva la volontà del
                                            lavoratore di conservare
                                            comunque l’adesione eventualmente già intervenuta a diverse forme pensionistiche
                                            individuali; b) finalità
                                            assistenziali, nell’ambito delle misure di welfare integrativo, secondo la
                                            disciplina dell’art. 72; c)
                                            erogazione di incentivi monetari collegati a obiettivi di potenziamento dei
                                            servizi di controllo finalizzati
                                            alla sicurezza urbana e stradale.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F192')): ?>
                                <li>
                                    Altre risorse specificatamente contrattate nel CCDI dell'anno (inserire riferimento)
                                    € <?php self::getInput('var55', 'f68', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var56', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione:
                                    <br />
                                    <br />
                                    <?php self::getTextArea('area17', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F167')): ?>
                                <li>
                                    Incentivazione funzioni tecniche (art. 68, c. 2, lett. g CCNL 21.5.2018)
                                    € <?php self::getInput('var56', 'R92', 'orange'); ?>.
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var58', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />
                                    <?php self::getTextArea('area18', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. g CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    G) compensi previsti da disposizioni di legge, riconosciuti esclusivamente a valere
                                    sulle risorse di cui
                                    all’art. 67, comma 3, lett. c), ivi compresi i compensi di cui all’art.70-ter;
                                    <br />
                                    <br />
                                    Art. 67 comma 3 lett. c.
                                    <br />
                                    <br />
                                    C) delle risorse derivanti da disposizioni di legge che prevedano specifici trattamenti
                                    economici in favore
                                    del personale, da utilizzarsi secondo quanto previsto dalle medesime disposizioni di
                                    legge.
                                    <br />
                                    <br />
                                    Art. 113 comma 2 e 3 D.LGS. 18 APRILE 2016, N. 50.
                                    <ol>
                                        <li>
                                            A valere sugli stanziamenti di cui al comma 1, le amministrazioni aggiudicatrici
                                            destinano ad un apposito
                                            fondo risorse finanziarie in misura non superiore al 2 per cento modulate
                                            sull'importo dei lavori, servizi e
                                            forniture, posti a base di gara per le funzioni tecniche svolte dai dipendenti
                                            delle stesse esclusivamente
                                            per le attività di programmazione della spesa per investimenti, di valutazione
                                            preventiva dei progetti, di
                                            predisposizione e di controllo delle procedure di gara e di esecuzione dei
                                            contratti pubblici, di RUP, di
                                            direzione dei lavori ovvero direzione dell'esecuzione e di collaudo tecnico
                                            amministrativo ovvero di
                                            verifica di conformità, di collaudatore statico ove necessario per consentire
                                            l'esecuzione del contratto nel
                                            rispetto dei documenti a base di gara, del progetto, dei tempi e costi
                                            prestabiliti. Tale fondo non è
                                            previsto da parte di quelle amministrazioni aggiudicatrici per le quali sono in
                                            essere contratti o
                                            convenzioni che prevedono modalità diverse per la retribuzione delle funzioni
                                            tecniche svolte dai propri
                                            dipendenti. Gli enti che costituiscono o si avvalgono di una centrale di
                                            committenza possono destinare il
                                            fondo o parte di esso ai dipendenti di tale centrale. La disposizione di cui al
                                            presente comma si applica
                                            agli appalti relativi a servizi o forniture nel caso in cui è nominato il
                                            direttore dell'esecuzione. 3.
                                            L'ottanta per cento delle risorse finanziarie del fondo costituito ai sensi del
                                            comma 2 è ripartito, per
                                            ciascuna opera o lavoro, servizio, fornitura con le modalità e i criteri
                                            previsti in sede di contrattazione
                                            decentrata integrativa del personale, sulla base di apposito regolamento
                                            adottato dalle amministrazioni
                                            secondo i rispettivi ordinamenti, tra il responsabile unico del procedimento e i
                                            soggetti che svolgono le
                                            funzioni tecniche indicate al comma 2 nonché' tra i loro collaboratori. Gli
                                            importi sono comprensivi anche
                                            degli oneri previdenziali e assistenziali a carico dell'amministrazione.
                                            L'amministrazione aggiudicatrice o
                                            l'ente aggiudicatore stabilisce i criteri e le modalità per la riduzione delle
                                            risorse finanziarie connesse
                                            alla singola opera o lavoro a fronte di eventuali incrementi dei tempi o dei
                                            costi non conformi alle norme
                                            del presente decreto. La corresponsione dell'incentivo è disposta dal dirigente
                                            o dal responsabile di
                                            servizio preposto alla struttura competente, previo accertamento delle
                                            specifiche attività svolte dai
                                            predetti dipendenti. Gli incentivi complessivamente corrisposti nel corso
                                            dell'anno al singolo dipendente,
                                            anche da diverse amministrazioni, non possono superare l'importo del 50 per
                                            cento del trattamento economico
                                            complessivo annuo lordo. Le quote parti dell'incentivo corrispondenti a
                                            prestazioni non svolte dai medesimi
                                            dipendenti, in quanto affidate a personale esterno all'organico
                                            dell'amministrazione medesima, ovvero prive
                                            del predetto accertamento, incrementano la quota del fondo di cui al comma 2. Il
                                            presente comma non si
                                            applica al personale con qualifica dirigenziale.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F171')): ?>
                                <li>
                                    Incentivazione specifiche attività - AVVOCATURA (art. 68, c. 2, lett. g CCNL 21.5.2018)
                                    € <?php self::getInput('var59', 'R96', 'orange'); ?>.
                                    <br />
                                    <br />
                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var60', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />
                                    <?php self::getTextArea('area19', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />
                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />
                                    Art. 68 comma 2 lett. g CCNL 21.5.2018.
                                    <br />
                                    <br />
                                    G) compensi previsti da disposizioni di legge, riconosciuti esclusivamente a valere
                                    sulle risorse di cui
                                    all’art. 67, comma 3, lett. c), ivi compresi i compensi di cui all’art.70-ter;
                                    <br />
                                    <br />
                                    Art. 67 comma 3 lett. c.
                                    <br />
                                    <br />
                                    C) delle risorse derivanti da disposizioni di legge che prevedano specifici trattamenti
                                    economici in favore
                                    del personale, da utilizzarsi secondo quanto previsto dalle medesime disposizioni di
                                    legge.
                                    <br />
                                    <br />
                                    Art. 27 CCNL 14.9.2000.
                                    <ol>
                                        <li>
                                            Gli enti provvisti di Avvocatura costituita secondo i rispettivi ordinamenti
                                            disciplinano la
                                            corresponsione dei compensi professionali, dovuti a seguito di sentenza
                                            favorevole all’ente, secondo i
                                            principi di cui al regio decreto-legge 27.11.1933 n. 1578 e disciplinano,
                                            altresì, in sede di contrattazione
                                            decentrata integrativa la correlazione tra tali compensi professionali e la
                                            retribuzione di risultato di cui
                                            all’art. 10 del CCNL del 31.3.1999. Sono fatti salvi gli effetti degli atti con
                                            i quali gli stessi enti
                                            abbiano applicato la disciplina vigente per l’Avvocatura dello Stato anche prima
                                            della stipulazione del
                                            presente CCNL.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F169')): ?>
                                <li>
                                    Incentivazione specifiche attività - ISTAT (art. 68, c. 2, lett. g CCNL 21.5.2018)
                                    € <?php self::getInput('var61', 'R94', 'orange'); ?> .
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var62', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />

                                    <?php self::getTextArea('area20', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />

                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. g CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    G) compensi previsti da disposizioni di legge, riconosciuti esclusivamente a valere
                                    sulle risorse di cui
                                    all’art. 67, comma 3, lett. c), ivi compresi i compensi di cui all’art.70-ter;
                                    <br />
                                    <br />

                                    Art. 70 ter CCNL 21.5.2018.
                                    <ol>
                                        <li>
                                            Gli enti possono corrispondere specifici compensi al personale per remunerare
                                            prestazioni connesse a
                                            indagini statistiche periodiche e censimenti permanenti, rese al di fuori
                                            dell’ordinario orario di lavoro.
                                        </li>
                                        <li>
                                            Gli oneri concernenti l’erogazione dei compensi di cui al presente articolo
                                            trovano copertura
                                            esclusivamente nella quota parte del contributo onnicomprensivo e forfetario
                                            riconosciuto dall’Istat e dagli
                                            Enti e Organismi pubblici autorizzati per legge, confluita nel Fondo Risorse
                                            decentrate, ai sensi dell’art.
                                            67, comma 3, lett. c).
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F170')): ?>
                                <li>
                                    Incentivazione specifiche attività - ICI (art. 68, c. 2, lett. g CCNL 21.5.2018)
                                    € <?php self::getInput('var63', 'R95', 'orange'); ?>.
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var64', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />

                                    <?php self::getTextArea('area21', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />

                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. g CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    G) compensi previsti da disposizioni di legge, riconosciuti esclusivamente a valere
                                    sulle risorse di cui
                                    all’art. 67, comma 3, lett. c), ivi compresi i compensi di cui all’art.70-ter;
                                    <br />
                                    <br />

                                    Art. 4 CCNL del 5/10/2001 comma 3 Integrazione risorse dell'art. 15 del CCNL
                                    dell'1/4/1999.
                                    <br />
                                    <br />

                                    La disciplina dell'art. 15, comma 1, lett. k) del CCNL dell'1.4.1999, ricomprende sia le
                                    risorse derivanti
                                    dalla applicazione dell'art. 3, comma 57 della legge n. 662 del 1996 e dall'art. 59,
                                    comma 1, lett. p) del
                                    D. Lgs.n.446 del 1997 (recupero evasione ICI), sia le ulteriori risorse correlate agli
                                    effetti applicativi
                                    dell'art. 12, comma 1, lett. del D.L. n. 437 del 1996, convertito nella legge n. 556 del
                                    1996.
                                    <br />
                                    <br />

                                    Art. 3, comma 57 della legge n. 662 del 1996.
                                    <br />
                                    <br />

                                    57. Una percentuale del gettito dell'imposta comunale sugli immobili può essere
                                    destinata al potenziamento
                                    degli uffici tributari del comune. I dati fiscali a disposizione del comune sono
                                    ordinati secondo procedure
                                    informatiche, stabilite con decreto del Ministro delle finanze, allo scopo di effettuare
                                    controlli
                                    incrociati coordinati con le strutture dell'amministrazione finanziaria.
                                    <br />
                                    <br />

                                    Art. 59, comma 1, lett. p) del D. Lgs.n.446 del 1997.
                                    <br />
                                    <br />

                                    p) prevedere che ai fini del potenziamento degli uffici tributari del comune, ai sensi
                                    dell'articolo 3,
                                    comma 57, della legge 23 dicembre 1996, n. 662, possono essere attribuiti compensi
                                    incentivanti al personale
                                    addetto.
                                    <br /><br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F322')): ?>
                                <li>
                                    Incentivazione specifiche attività - Compensi IMU e TARI (art. 68 c. 2, lett. g CCNL
                                    21.5.2018)
                                    €<?php self::getInput('var65', 'R149', 'orange'); ?> .

                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var66', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />

                                    <?php self::getTextArea('area22', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />

                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. g CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    G) compensi previsti da disposizioni di legge, riconosciuti esclusivamente a valere
                                    sulle risorse di cui
                                    all’art. 67, comma 3, lett. c), ivi compresi i compensi di cui all’art.70-ter;
                                    <br />
                                    <br />

                                    Art. 1 comma 1091 della L. 145 del 31.12.2018 - Legge di Bilancio 2019 091. Ferme
                                    restando le facoltà di
                                    regolamentazione del tributo di cui all'articolo 52 del decreto legislativo 15 dicembre
                                    1997, n. 446, i
                                    comuni che hanno approvato il bilancio di previsione ed il rendiconto entro i termini
                                    stabiliti dal testo
                                    unico di cui al decreto legislativo 18 agosto 2000, n. 267, possono, con proprio
                                    regolamento, prevedere che
                                    il maggiore gettito accertato e riscosso, relativo agli accertamenti dell'imposta
                                    municipale propria e della
                                    TARI, nell'esercizio fiscale precedente a quello di riferimento risultante dal conto
                                    consuntivo approvato,
                                    nella misura massima del 5 per cento, sia destinato, limitatamente all'anno di
                                    riferimento, al potenziamento
                                    delle risorse strumentali degli uffici comunali preposti alla gestione delle entrate e
                                    al trattamento
                                    accessorio del personale dipendente, anche di qualifica dirigenziale, in deroga al
                                    limite di cui
                                    all'articolo 23, comma 2, del decreto legislativo 25 maggio 2017, n. 75. La quota
                                    destinata al trattamento
                                    economico accessorio, al lordo degli oneri riflessi e dell'IRAP a carico
                                    dell'amministrazione, è attribuita,
                                    mediante contrattazione integrativa, al personale impiegato nel raggiungimento degli
                                    obiettivi del settore
                                    entrate, anche con riferimento alle attività connesse alla partecipazione del comune
                                    all'accertamento dei
                                    tributi erariali e dei contributi sociali non corrisposti, in applicazione dell'articolo
                                    1 del decreto-legge
                                    30 settembre 2005, n. 203, convertito, con modificazioni, dalla legge 2 dicembre 2005,
                                    n. 248. Il beneficio
                                    attribuito non può superare il 15 per cento del trattamento tabellare annuo lordo
                                    individuale. La presente
                                    disposizione non si applica qualora il servizio di accertamento sia affidato in
                                    concessione.
                                    <br /><br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F168')): ?>

                                <li>
                                    Incentivazione specifiche attività – Messi Notificatori (art. 68 comma 2 lett. h CCNL
                                    21.5.2018)
                                    € <?php self::getInput('var67', 'R93', 'orange'); ?> .
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var68', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />

                                    <?php self::getTextArea('area23', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />

                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 54 CCNL del 14/9/2000.
                                    <br />
                                    <br />

                                    Gli enti possono verificare, in sede di concertazione, se esistano le condizioni
                                    finanziarie per destinare
                                    una quota parte del rimborso spese per ogni notificazione di atti dell'amministrazione
                                    finanziaria al fondo
                                    di cui all'art.15 del CCNL dell'1.4.1999 per essere finalizzata all'erogazione di
                                    incentivi di produttività
                                    a favore dei messi notificatori stessi.
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. H CCNL 21.5.2018
                                    <br />
                                    <br />

                                    h) compensi ai messi notificatori, riconosciuti esclusivamente a valere sulle risorse di
                                    all’art. 67, comma
                                    3, lett. f), secondo la disciplina di cui all’art. 54 del CCNL del 14.9.2000;
                                    <br /><br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F172')): ?>

                                <li>
                                    Incentivazione specifiche attività - Diritto soggiorno Unione Europea D.Lgs. 30/2007
                                    (art. 68 comma 2
                                    lett. h CCNL 21.5.2018) € <?php self::getInput('var69', 'R108', 'orange'); ?>.
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var70', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />

                                    <?php self::getTextArea('area23', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />

                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 68 comma 2 lett. h CCNL 21.5.2018.
                                    <br />
                                    <br />

                                    G) compensi previsti da disposizioni di legge, riconosciuti esclusivamente a valere
                                    sulle risorse di cui
                                    all’art. 67, comma 3, lett. c), ivi compresi i compensi di cui all’art.70-ter;
                                    <br />
                                    <br />

                                    LEGGE 24 dicembre 2007, n. 244 art. 2 comma 11.
                                    <br />
                                    <br />

                                    11. Per ciascuno degli anni 2008 e 2009, a valere sul fondo ordinario di cui
                                    all'articolo 34, comma 1,
                                    lettera a), del decreto legislativo 30 dicembre 1992, n. 504, è disposto un intervento
                                    fino a un importo di
                                    10 milioni di euro per la concessione di un contributo a favore dei comuni per
                                    l'attuazione della direttiva
                                    2004/38/CE del Parlamento europeo e del Consiglio, del 29 aprile 2004, relativa al
                                    diritto dei cittadini
                                    dell'Unione e dei loro familiari di circolare e di soggiornare liberamente nel
                                    territorio degli Stati
                                    membri, di cui al decreto legislativo 6 febbraio 2007, n. 30. Con decreto del Ministro
                                    dell'interno sono
                                    determinate le modalità' di riparto ed erogazione dei contributi.

                                    <br /><br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F173')): ?>
                                <li>
                                    Incentivazione specifiche attività – (art. 68 comma 2 lett. h CCNL 21.5.2018) Legge
                                    Regionale specifica
                                    <?php self::getInput('var71', 'xxxx', 'orange'); ?> xxxx (inserire riferimento)
                                    € <?php self::getInput('var72', 'R109', 'orange'); ?>.
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var73', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />
                                    <br />
                                    <?php self::getTextArea('area24', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br /><br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F190')): ?>
                                <li>
                                    Incentivazione specifiche attività – (art. 68 comma 2 lett. h CCNL 21.5.2018) (inserire
                                    riferimento) €
                                    <?php self::getInput('var74', 'f69', 'orange'); ?>.
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var75', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />
                                    <br />

                                    <?php self::getTextArea('area25', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>

                                    <br /><br />

                                </li>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F188')): ?>
                            <li>
                                Premi collegati alla performance organizzativa – Compensi per Sponsorizzazioni, convenzioni
                                e servizi
                                conto terzi (art. 67 comma 3 lett. a CCNL 21.5.2018)
                                € <?php self::getInput('var76', 'R91', 'orange'); ?> .
                                <br />
                                <br />
                                <?php endif; ?>

                                Viene ripreso il testo del contratto siglato per
                                l’anno <?php self::getInput('var77', '202x', 'orange'); ?>
                                con il quale sono stati definiti i criteri per
                                la distribuzione dello specifico incentivo:
                                <br />

                                <?php self::getTextArea('area26', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                <br />

                                RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                <br />
                                <br />

                                Art. 4 CCNL del 5/10/2001 comma 4 Integrazione risorse dell'art. 15 del CCNL dell'1/4/1999.
                                "d) La quota delle risorse che possono essere destinate al trattamento economico accessorio
                                del personale
                                nell'ambito degli introiti derivanti dalla applicazione dell'art.43 della legge n.449/1997
                                con particolare
                                riferimento alle seguenti iniziative:
                                <ul class="a">
                                    <li>
                                        contratti di sponsorizzazione ed accordi di collaborazione con soggetti privati ed
                                        associazioni senza
                                        fini di lucro, per realizzare o acquisire a titolo gratuito interventi, servizi,
                                        prestazioni, beni o
                                        attività inseriti nei programmi di spesa ordinari con il conseguimento dei
                                        corrispondenti risparmi;
                                    </li>
                                    <li>
                                        convenzioni con soggetti pubblici e privati diretti a fornire ai medesimi soggetti,
                                        a titolo oneroso,
                                        consulenze e servizi aggiuntivi rispetto a quelli ordinari;
                                    </li>
                                    <li>
                                        contributi dell'utenza per servizi pubblici non essenziali o, comunque, per
                                        prestazioni, verso terzi
                                        paganti, non connesse a garanzia di diritti fondamentali.
                                    </li>
                                </ul>
                                <br />
                            </li>


                            <?php if (self::checkOptionalValues('F187')): ?>
                                <li>
                                    Piani di razionalizzazione (Art. 67 comma 3 lett. b CCNL 21.5.2018ART. 16 C. 5 L.
                                    111/2011 e s.m.i.) €
                                    <?php self::getInput('var78', 'R110', 'orange'); ?>.
                                    <br />
                                    <br />

                                    Viene ripreso il testo del contratto siglato per
                                    l’anno <?php self::getInput('var79', '202x', 'orange'); ?>
                                    con il quale sono stati definiti i criteri per
                                    la distribuzione dello specifico incentivo:
                                    <br />

                                    <?php self::getTextArea('area27', '  INSERIRE IL TESTO DEL CONTRATTO 202x CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E
                                INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA
                                VOCE.', 'red'); ?>
                                    <br />

                                    RIFERIMENTI NORMATIVI/CONTRATTUALI:
                                    <br />
                                    <br />

                                    Art. 16, commi 4, 5 e 6 del D.L. n. 98 del 6 luglio 2011, convertito, con modificazioni,
                                    nella legge n. 111
                                    del 15 luglio 2011
                                    <ol start="4">
                                        <li>
                                            Fermo restando quanto previsto dall'articolo 11, le amministrazioni di cui
                                            all'articolo 1, comma 2, del
                                            decreto legislativo 30 marzo 2001, n. 165, possono adottare entro il 31 marzo di
                                            ogni anno piani triennali
                                            di razionalizzazione e riqualificazione della spesa, di riordino e
                                            ristrutturazione amministrativa, di
                                            semplificazione e digitalizzazione, di riduzione dei costi della politica e di
                                            funzionamento, ivi compresi
                                            gli appalti di servizio, gli affidamenti alle partecipate e il ricorso alle
                                            consulenze attraverso persone
                                            giuridiche. Detti piani indicano la spesa sostenuta a legislazione vigente per
                                            ciascuna delle voci di spesa
                                            interessate e i correlati obiettivi in termini fisici e finanziari.
                                        </li>
                                        <li>
                                            In relazione ai processi di cui al comma 4, le eventuali economie aggiuntive
                                            effettivamente realizzate
                                            rispetto a quelle già previste dalla normativa vigente, dall'articolo 12 e dal
                                            presente articolo ai fini del
                                            miglioramento dei saldi di finanza pubblica, possono essere utilizzate
                                            annualmente, nell'importo massimo del
                                            50 per cento, per la contrattazione integrativa, di cui il 50 per cento
                                            destinato alla erogazione dei premi
                                            previsti dall'articolo 19 del decreto legislativo 27 ottobre 2009, n. 150. La
                                            restante quota è versata
                                            annualmente dagli enti e dalle amministrazioni dotati di autonomia finanziaria
                                            ad apposito capitolo
                                            dell'entrata del bilancio dello Stato. La disposizione di cui al precedente
                                            periodo non si applica agli enti
                                            territoriali e agli enti, di competenza regionale o delle provincie autonome di
                                            Trento e di Bolzano, del
                                            SSN. Le risorse di cui al primo periodo sono utilizzabili solo se a consuntivo è
                                            accertato, con riferimento
                                            a ciascun esercizio, dalle amministrazioni interessate, il raggiungimento degli
                                            obiettivi fissati per
                                            ciascuna delle singole voci di spesa previste nei piani di cui al comma 4 e i
                                            conseguenti risparmi. I
                                            risparmi sono certificati, ai sensi della normativa vigente, dai competenti
                                            organi di controllo. Per la
                                            Presidenza del Consiglio dei Ministri e i Ministeri la verifica viene effettuata
                                            dal Ministero dell'economia
                                            e delle finanze - Dipartimento della Ragioneria generale dello Stato per il
                                            tramite, rispettivamente,
                                            dell'UBRRAC e degli uffici centrali di bilancio e dalla Presidenza del Consiglio
                                            - Dipartimento della
                                            funzione pubblica.
                                        </li>
                                    </ol>
                                    <br />
                                </li>
                            <?php endif; ?>

                            <?php if (self::checkOptionalValues('F272')): ?>
                                <li>
                                    Quota recupero somme (Art. 4 DL 16/2014 Salva Roma Ter)
                                    € <?php self::getInput('var80', 'R119', 'orange'); ?>.
                                    Quota annuale delle risorse decentrate finalizzata a compensare le somme indebitamente
                                    erogate negli anni
                                    precedenti.
                                    <br />
                                    <br />

                                    RIFERIMENTI NORMATIVI
                                    <br />
                                    <br />

                                    Art. 4 DL 16/2914 – Decreto Salva Roma ter
                                    <br />
                                    <br />

                                    Le regioni e gli enti locali che non hanno rispettato i vincoli finanziari posti alla
                                    contrattazione
                                    collettiva integrativa sono obbligati a recuperare integralmente, a valere sulle risorse
                                    finanziarie a
                                    questa destinate, rispettivamente al personale dirigenziale e non dirigenziale, le somme
                                    indebitamente
                                    erogate mediante il graduale riassorbimento delle stesse, con quote annuali e per un
                                    numero massimo di
                                    annualità corrispondente a quelle in cui si è verificato il superamento di tali vincoli.
                                    Nei predetti casi,
                                    le regioni ((adottano)) misure di contenimento della spesa per il personale, ulteriori
                                    rispetto a quelle già
                                    previste dalla vigente normativa, mediante l'attuazione di piani di riorganizzazione
                                    finalizzati alla
                                    razionalizzazione e allo snellimento delle strutture burocratico-amministrative, anche
                                    attraverso
                                    accorpamenti di uffici con la contestuale riduzione delle dotazioni organiche del
                                    personale dirigenziale in
                                    misura non inferiore al 20 per cento e della spesa complessiva del personale non
                                    dirigenziale In misura non
                                    inferiore al 10 per cento. Gli enti locali adottano le misure di razionalizzazione
                                    organizzativa garantendo
                                    in ogni caso la riduzione delle dotazioni organiche entro i parametri definiti dal
                                    decreto di cui
                                    all'articolo 263, comma 2, del decreto legislativo 18 agosto 2000, n. 267. Al fine di
                                    conseguire l'effettivo
                                    contenimento della spesa, alle unità di personale eventualmente risultanti in
                                    soprannumero all'esito dei
                                    predetti piani obbligatori di riorganizzazione si applicano le disposizioni previste
                                    dall'articolo 2, commi
                                    11 e 12, del decreto-legge 6 luglio 2012, n. 95, convertito, con modificazioni, dalla
                                    legge 7 agosto 2012,
                                    n. 135, nei limiti temporali della vigenza della predetta norma. Le cessazioni dal
                                    servizio conseguenti alle
                                    misure di cui al precedente periodo non possono essere calcolate come risparmio utile
                                    per definire
                                    l'ammontare delle disponibilità finanziarie da destinare alle assunzioni o il numero
                                    dell’unità sostituibili
                                    in relazione alle limitazioni del turn over. Le Regioni e gli enti locali trasmettono
                                    entro il 31 maggio di
                                    ciascun anno alla Presidenza del Consiglio dei Ministri - Dipartimento della funzione
                                    pubblica, al Ministero
                                    dell'economia e delle finanze - Dipartimento della Ragioneria generale dello Stato e al
                                    Ministero
                                    dell'interno - Dipartimento per gli affari interni e territoriali, ai fini del relativo
                                    monitoraggio, una
                                    relazione illustrativa ed una relazione tecnico-finanziaria che, con riferimento al
                                    mancato rispetto dei
                                    vincoli finanziari, dia conto dell'adozione dei piani obbligatori di riorganizzazione e
                                    delle specifiche
                                    misure previste dai medesimi per il contenimento della spesa per il personale ovvero
                                    delle misure di cui al
                                    terzo periodo.
                                    <br />
                                </li>
                            <?php endif; ?>
                        </ol>
                        <br /><br />
                    </li>

                    <li>
                        Quadro di sintesi delle modalità di utilizzo da parte della contrattazione integrativa delle risorse
                        del
                        Fondo unico di amministrazione;
                        <br />
                        <br />

                        <table>
                            <thead>
                            <tr>
                                <th colspan="2"><b>Utilizzo Fondo</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th> Totale utilizzo fondo progressioni</th>
                                <td><?php self::getInput('var81', 'F75', 'orange'); ?> </td>
                            </tr>
                            <?php if (self::checkOptionalValues('F162')): ?>
                                <tr>
                                    <th>Indennità di comparto art.33 ccnl 22.01.04, quota a carico fondo</th>
                                    <td><?php self::getInput('var82', 'R56', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F163')): ?>
                                <tr>
                                    <th>Indennità educatori asilo nido</th>
                                    <td><?php self::getInput('var83', 'R57', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F189')): ?>
                                <tr>
                                    <th>ALTRI UTILIZZI</th>
                                    <td><?php self::getInput('var84', 'f66', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th><b>TOTALE UTILIZZO RISORSE STABILI</b></th>
                                <td><?php self::getInput('var85', 's4_1', 'orange'); ?></td>
                            </tr>
                            <?php if (self::checkOptionalValues('F198')): ?>
                                <tr>
                                    <th>Indennità di turno</th>
                                    <td><?php self::getInput('var86', 'f197', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F293')): ?>
                                <tr>
                                    <th>Indennità condizioni di lavoro</th>
                                    <td><?php self::getInput('var87', 'R145', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F204')): ?>
                                <tr>
                                    <th>Reperibilità</th>
                                    <td><?php self::getInput('var88', 'f203', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F208')): ?>
                                <tr>
                                    <th>
                                        Indennità specifiche responsabilità art 70 quinquies c. 1 CCNL 2018 (ex lett. f
                                        art.
                                        17 comma 2 CCNL 1.4.1999)
                                    </th>
                                    <td><?php self::getInput('var89', 'F207', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F210')): ?>
                                <tr>
                                    <th>
                                        Indennità specifiche responsabilità art 70 quinquies c. 1 CCNL 2018 (ex lett. i
                                        art.
                                        17 comma 2 CCNL 1.4.1999)
                                    </th>
                                    <td><?php self::getInput('var90', 'F209', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F295')): ?>
                                <tr>
                                    <th>Indennità di funzione – Art. 56 sexies CCNL 2018 (Vigilanza)</th>
                                    <td><?php self::getInput('var91', 'R135', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F294')): ?>
                                <tr>
                                    <th>
                                        Indennità di servizio esterno – art. 56 quinquies CCNL 2018
                                        (Vigilanza)
                                    </th>
                                    <td><?php self::getInput('var92', 'R134', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F212')): ?>
                                <tr>
                                    <th>
                                        Indennità particolare compenso incentivante (personale unioni dei
                                        comuni)
                                    </th>
                                    <td><?php self::getInput('var93', 'F211', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F214')): ?>
                                <tr>
                                    <th>Centri estivi asili nido art 31 comma 5 CCNL 14 -9- 2000</th>
                                    <td><?php self::getInput('var94', 'F213', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F216')): ?>
                                <tr>
                                    <th>
                                        Compenso previsto dall'art.24, comma 1 CCNL 14.9.2000, per il personale che
                                        presta
                                        attività lavorativa nel giorno destinato al riposo settimanale
                                    </th>
                                    <td><?php self::getInput('var95', 'F215', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F218')): ?>
                                <tr>
                                    <th>
                                        Premi collegati alla performance organizzativa – art. 68 c. 2 lett. a) CCNL 2018
                                    </th>
                                    <td><?php self::getInput('var96', 'F217', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F220')): ?>
                                <tr>
                                    <th>
                                        Premi collegati alla performance individuale - art. 68 c. 2 lett. b) CCNL 2018
                                    </th>
                                    <td><?php self::getInput('var97', 'F219', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F186')): ?>
                                <tr>
                                    <th>
                                        Premi collegati alla performance organizzativa - Obiettivi finanziati con art.
                                        67
                                        c.5 lett. B CCNL 2018 parte variabile
                                    </th>
                                    <td><?php self::getInput('var98', 'R88', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F296')): ?>
                                <tr>
                                    <th>
                                        Premi collegati alla performance organizzativa - Obiettivi finanziati da risorse
                                        art
                                        67 c. 5 lett. b di potenziamento dei servizi di controllo finalizzati alla sicurezza
                                        urbana
                                        e
                                        stradale Art. 56 quater CCNL 2018
                                    </th>
                                    <td><?php self::getInput('var99', 'R136', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F187')): ?>
                                <tr>
                                    <th>
                                        50% ECONOMIE DA PIANI DI RAZIONALIZZAZIONE DA DESTINARE ALLA CONTRATTAZIONE DI
                                        CUI
                                        IL 50% DESTINATO ALLA PRODUTTIVITA' (escluso dal limite fondo 2010)
                                    </th>
                                    <td><?php self::getInput('var100', 'R110', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F192')): ?>
                                <tr>
                                    <th>Altro</th>
                                    <td><?php self::getInput('var101', 'F68', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F188')): ?>
                                <tr>
                                    <th>
                                        Premi collegati alla performance organizzativa - Compensi per sponsorizzazioni
                                    </th>
                                    <td><?php self::getInput('var102', 'F188', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th><b>TOTALE UTILIZZO ALTRE INDENNITA’</b></th>
                                <td><?php self::getInput('var103', 'F224', 'orange'); ?></td>
                            </tr>
                            <?php if (self::checkOptionalValues('F167')): ?>
                                <tr>
                                    <th>
                                        Art. 68 c. 2 lett. g) CCNL 2018
                                        FUNZIONI TECNICHE RIF Art. 113 comma 2 e 3 D.LGS. 18 APRILE 2016, N. 50
                                    </th>
                                    <td><?php self::getInput('var104', 'R92', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F168')): ?>
                                <tr>
                                    <th>Art. 68 c. 2 lett. h CCNL 2018 RIF Compensi per notifiche</th>
                                    <td><?php self::getInput('var105', 'R93', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F322')): ?>
                                <tr>
                                    <th>
                                        Art. 68 c. 2 lett. g) CCNL 2018 RIF Compensi IMU e TARI c. 1091 Lex 145/2018
                                    </th>
                                    <td><?php self::getInput('var106', 'R149', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F169')): ?>
                                <tr>
                                    <th>Art. 68 c. 2 lett. g) CCNL 2018 RIF - ISTAT</th>
                                    <td><?php self::getInput('var107', 'R94', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F170')): ?>
                                <tr>
                                    <th>Art. 68 c. 2 lett. g) CCNL 2018 RIF - ICI</th>
                                    <td><?php self::getInput('var108', 'R95', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F171')): ?>
                                <tr>
                                    <th>
                                        Art. 68 c. 2 lett. g) CCNL 2018 RIF - avvocatura
                                    </th>
                                    <td><?php self::getInput('var109', 'R96', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F172')): ?>
                                <tr>
                                    <th>
                                        Art. 68 c. 2 lett. g) CCNL 2018
                                        RIF - Diritto soggiorno Unione Europea D.lgs 30/2007
                                    </th>
                                    <td><?php self::getInput('var110', 'R108', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F173')): ?>
                                <tr>
                                    <th>Art. 68 c. 2 lett. g) CCNL 2018 Legge Regionale specifica</th>
                                    <td><?php self::getInput('var111', 'R109', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F190')): ?>
                                <tr>
                                    <th>Art. 68 c. 2 lett. g) CCNL 2018 RIF - Legge o ALTRO</th>
                                    <td><?php self::getInput('var112', 'F69', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F272')): ?>
                                <tr>
                                    <th>Quota recupero somme (Art. 4 DL 16/2014 Salva Roma Ter)</th>
                                    <td><?php self::getInput('var113', 'R119', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (self::checkOptionalValues('F225')): ?>
                                <tr>
                                    <th><b>TOTALE UTILIZZO RISORSE VINCOLATE</b></th>
                                    <td><?php self::getInput('var114', 'S4_4', 'orange'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th><b>TOTALE UTILIZZO FONDO</b></th>
                                <td><?php self::getInput('var115', 'F77', 'orange'); ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <br />
                        <br />
                    </li>

                    <li>
                        <b>
                            Gli effetti abrogativi impliciti, in modo da rendere chiara la successione temporale dei
                            contratti
                            integrativi e la disciplina vigente delle materie demandate alla contrattazione integrativa;
                        </b>
                        <br />
                        <br />
                        Risultano attualmente in vigore i seguenti CCDI:
                        <br />
                        <br />
                        CCDI relativo all’anno <?php self::getInput('var116', '20xx', 'orange'); ?> con il quale sono
                        state determinate le modalità di attribuzione dell’indennità
                        di <?php self::getInput('var117', 'xxxx', 'orange'); ?>
                        , <?php self::getInput('var118', 'xxxx', 'orange'); ?>
                        , <?php self::getInput('var119', 'xxxx', 'orange'); ?>
                        E <?php self::getInput('var120', 'xxxx', 'orange'); ?>
                        <br />
                        <br />
                        CCDI relativo all’anno <?php self::getInput('var121', '20xx', 'orange'); ?> con il quale sono
                        state determinate le modalità di attribuzione dell’indennità
                        di <?php self::getInput('var122', 'xxxx', 'orange'); ?>
                        , <?php self::getInput('vae123', 'xxxx', 'orange'); ?>
                        , <?php self::getInput('var124', 'xxxx', 'orange'); ?>
                        E Per l’anno anno sono state previste nuove progressioni economiche orizzontali.
                        <br />
                        <br />
                    </li>

                    <li>
                        <b>
                            Illustrazione e specifica attestazione della coerenza con le previsioni in materia di
                            meritocrazia e
                            premialità (coerenza con il Titolo III del Decreto Legislativo n.150/2009, le norme di contratto
                            nazionale
                            e la giurisprudenza contabile) ai fini della corresponsione degli incentivi per la performance
                            individuale
                            ed organizzativa;
                        </b>
                        <br />
                        <br />
                        Caso A) È stata adottata una nuova metodologia di valutazione adeguata alle disposizioni del D.Lgs.
                        150/2009.
                        <br />
                        Nel corso
                        dell’anno <?php self::getInput('var125', '20xx', 'orange'); ?>  <?php self::getInput('var126', 'il/la', 'orange'); ?>  <?php self::getInput('var127', 'nome_soggetto_deliberante', 'orange'); ?>
                        con Delibera n. <?php self::getInput('var128', 'xx', 'orange'); ?>
                        del <?php self::getInput('var129', 'xx/xx/201x', 'orange'); ?> ha approvato una
                        nuova metodologia coerente con le novità introdotte dal D.Lgs. 150/2009 e con le modifiche apportate
                        al
                        Regolamento degli Uffici e dei Servizi con Delibera
                        n. <?php self::getInput('var130', 'xxx', 'orange'); ?>
                        del <?php self::getInput('var131', 'xx/xx/201x', 'orange'); ?>.
                        <br />
                        L’organo di valutazione con verbale n.<?php self::getInput('var132', 'xx/xx/201x', 'orange'); ?> ha
                        Verificato la coerenza del “Sistema di misurazione e
                        valutazione delle performance” con i criteri espressi dall’art. 7 comma del 3 del Dlgs. 150/09. In
                        particolare, sono contenute previsioni di valutazione di merito e sono esclusi elementi automatici
                        come
                        l’anzianità di servizio.
                        <br />
                        Con il CCDI dell’anno 202x sono stati introdotti nuovi criteri di distribuzione della produttività
                        così come
                        risulta illustrato al punto a) e b) poco sopra.
                        <br />
                        <br />
                        Caso B) Non è stata adottata una nuova metodologia di valutazione adeguata alla disposizione del
                        D.Lgs.
                        150/2009.
                        <br />
                        <br />
                        Non è stata approvata una nuova metodologia di valutazione, poiché quella vigente risulta coerente
                        con le
                        novità introdotte dal D.Lgs. 150/2009 e con le modifiche apportate al Regolamento degli Uffici e dei
                        Servizi. In particolare, sono contenute previsioni di valutazione di merito e sono esclusi elementi
                        automatici come l’anzianità di servizio.
                        <br />
                        Con il CCDI dell’anno <?php self::getInput('var133', '201x', 'orange'); ?> sono stati introdotti
                        nuovi
                        criteri di distribuzione della produttività così come
                        risulta illustrato ai punti a) e b) poco sopra.
                        <br />
                        <br />
                        <p style="color: red">SELEZIONARE IL CASO a) o b) A SECONDA DELLA SITUAZIONE DELL'ENTE.</p>
                        <br />
                    </li>

                    <li>
                        <b>
                            illustrazione e specifica attestazione della coerenza con il principio di selettività delle
                            progressioni
                            economiche finanziate con il Fondo per la contrattazione integrativa - progressioni orizzontali
                            – ai
                            sensi
                            dell’articolo23 del Decreto Legislativo n.150/2009 (previsione di valutazioni di merito ed
                            esclusione di
                            elementi automatici come l’anzianità di servizio);
                        </b>

                        <br />
                        <br />

                        <?php if (self::checkOptionalValues('F115')): ?>
                            Per l’anno <?php self::getInput('var134', 'anno', 'orange'); ?> sono state previste nuove
                            progressioni economiche orizzontali.

                            <br />
                            Viene ripreso il testo del contratto siglato per
                            l’anno <?php self::getInput('var135', '20xx', 'orange'); ?> con il quale sono stati definiti i
                            criteri per
                            l’attribuzione delle progressioni:
                            <br />
                            <?php self::getTextArea('area27', 'INSERIRE IL TESTO DEL CONTRATTO 202X (Già inserito nel punto A1 poco sopra) CON IL QUALE SI È DECISA LA MODALITA’ DI DISTRIBUZIONE DELLE RISORSE E INSERIRE IL TESTO DELL’ACCORDO ANNUALE CON IL QUALE SONO STATE DEFINITE LE RISORSE DA DISTRIBUIRE PER QUESTA VOCE. illustrare e specificare la coerenza con il principio di selettività delle progressioni economiche.', 'orange'); ?>
                            <br />
                            In particolare sono contenute previsione di valutazioni di merito e sono esclusi elementi automatici come
                            l’anzianità di servizio
                        <?php endif; ?>
                        <br />
                        <br />
                        <?php if (self::checkOptionalValues('F221')): ?>
                            Per l’anno <?php self::getInput('var136', 'anno', 'orange'); ?> non sono state previste nuove
                            progressioni economiche orizzontali. Non sono stati
                            contrattati quindi nuovi criteri anche se è stato condiviso tra le parti che il sistema utilizzato per
                            valutare la performance sarà utilizzato qualora si dovessero prevedere nuove progressioni economiche.
                        <?php endif; ?>
                        <br />
                        <br />
                    </li>

                    <li>
                        <b>
                            illustrazione dei risultati attesi dalla sottoscrizione del contratto integrativo, in
                            correlazione con
                            gli strumenti di programmazione gestionale (Piano della Performance), adottati
                            dall’Amministrazione in
                            coerenza con le previsioni del Titolo II del Decreto Legislativo n.150/2009.
                        </b>
                        <br />
                        <br />
                        E’ stato approvato il Piano della Performance per
                        l’anno <?php self::getInput('var137', 'anno', 'orange'); ?>. Ai sensi dell’attuale Regolamento
                        degli
                        Uffici e dei Servizi ogni anno l’Ente è tenuto ad approvare un Piano della Performance che deve
                        contenere
                        gli obiettivi dell’Ente riferiti ai servizi gestiti.
                        <br />
                        <br />
                        Con la Delibera n. <?php self::getInput('var138', ' numero_delibera_approvazione_PEG', 'orange'); ?>
                        del <?php self::getInput('var139', 'data_delibera_approvazione_PEG', 'orange'); ?> <?php self::getInput('var140', 'il/la', 'orange'); ?>
                        <?php self::getInput('var141', 'nome_soggetto_deliberante', 'orange'); ?> ha approvato il Piano
                        della
                        Performance
                        per l’anno <?php self::getInput('var142', 'anno', 'orange'); ?>. Tale piano è stato
                        successivamente validato dall’organo di valutazione con il Verbale
                        n. <?php self::getInput('var143', '202x', 'orange'); ?>.


                        <br />
                        <br />
                        <ul class="d">
                            <?php if (self::checkOptionalValues('F44')): ?>
                                <li>
                                    Ai sensi dell’art. 67 comma 4 CCNL 21.5.2018 è stata autorizzata l’iscrizione, fra le
                                    risorse variabili,
                                    della quota fino ad un massimo dell'1,2% del monte salari (esclusa la quota riferita
                                    alla dirigenza)
                                    stabilito per l'anno 1997, nel rispetto del limite dell’anno 2016
                                    e<?php self::getInput('var144', '(viene proposto un esempio)', 'red'); ?> finalizzato
                                    al raggiungimento di specifici obiettivi di produttività e qualità espressamente
                                    definiti dall’Ente nel
                                    Piano esecutivo di Gestione <?php self::getInput('var145', 'anno', 'orange'); ?>
                                    unitamente al Piano della
                                    Performance approvato con Delibera della/del
                                    <?php self::getInput('var146', 'nome_soggetto_deliberante', 'orange'); ?>
                                    n. <?php self::getInput('var147', 'numero_delibera_approvazione_PEG', 'orange'); ?>
                                    del <?php self::getInput('var148', 'data_delibera_approvazione_PEG', 'orange'); ?> in
                                    merito a

                                    <?php self::getTextArea('area28', ' (INSERIRE IL TITOLO o allegare i file TESTO LIBERO).', 'red'); ?>
                                    <br />
                                    <br />
                                    L’importo previsto è pari a € <?php self::getInput('var149', 'R33', 'orange'); ?> che
                                    verrà erogato solo
                                    successivamente alla verifica dell’effettivo
                                    conseguimento dei risultati attesi.
                                    <br />
                                    <br />
                                    Si precisa che gli importi, qualora non dovessero essere interamente distribuiti, non
                                    daranno luogo ad
                                    economie del fondo ma ritorneranno nella disponibilità del bilancio dell’Ente.
                                    <br /><br />

                                </li>
                            <?php endif ?>
                            <?php if (self::checkOptionalValues('F45')): ?>
                                <li>
                                    Ai sensi dell’art. 67, comma 5 lett. b) del CCNL 21.5.2018 è stata autorizzata
                                    l’iscrizione, fra le risorse
                                    variabili, delle somme necessarie per il conseguimento di obiettivi dell’ente, anche di
                                    mantenimento, nonché
                                    obiettivi di potenziamento dei servizi di controllo finalizzati alla sicurezza urbana e
                                    stradale Art. 56
                                    quater CCNL 2018, per un importo pari a
                                    € <?php self::getInput('var150', 'R34', 'orange'); ?>. In
                                    particolare, tali obiettivi sono contenuti nel Piano
                                    esecutivo di Gestione anno unitamente al Piano della Performance approvato con
                                    Delibera <?php self::getInput('var151', 'della/del', 'orange'); ?>
                                    <?php self::getInput('var152', 'nome_soggetto_deliberante', 'orange'); ?>
                                    n. <?php self::getInput('var153', 'numero_delibera_approvazione_PEG', 'orange'); ?>
                                    del <?php self::getInput('var154', 'data_delibera_approvazione_PEG', 'orange'); ?> e ne
                                    vengono qui di seguito elencati i titoli:
                                    <br />
                                    <br />
                                    – <?php self::getInput('var155', '>xxxxx, (specificare almeno gli importi previsti per ogni obiettivo)', 'orange'); ?>
                                    ;
                                    <br />
                                    – <?php self::getInput('var156', '>xxxxx', 'orange'); ?>
                                    <br />
                                    <?php self::getTextArea('area29', ' (INSERIRE IL TITOLO o allegare i file TESTO LIBERO).', 'red'); ?>
                                    <br />
                                    (Consiglio: copiare quanto scritto nella Delibera di indirizzi su questo punto)
                                    <br />
                                    Si precisa che gli importi qualora non dovessero essere interamente distribuiti, non
                                    daranno luogo ad
                                    economie del fondo ma ritorneranno nella disponibilità del bilancio dell’Ente.
                                    <br /><br />
                                    Ai sensi dell’attuale Regolamento degli Uffici e dei Servizi ogni anno l’Ente è tenuto ad approvare un Piano della Performance che deve contenere le attività di processo dell’Ente riferiti ai servizi gestiti ed eventuali obiettivi strategici annuali determinati dalla  <?php self::getInput('var152', 'nome_soggetto_deliberante', 'orange'); ?>.
                                    <br /><br />
                                    Gli obiettivi contenuti nel Piano prevedono il crono programma delle attività, specifici indici/indicatori (quantità, qualità, tempo e costo) di prestazione attesa e il personale coinvolto. Si rimanda al documento per il dettaglio degli obiettivi di performance.
                                    <br /><br />
                                    <?php self::getInput('var451', 'Il/La', 'orange'); ?> <?php self::getInput('var152', 'nome_soggetto_deliberante', 'orange'); ?> in particolare, con Delibera n. <?php self::getInput('var452', 'numero_delibera_indirizzo', 'orange'); ?>  del <?php self::getInput('var453', 'data_delibera_indirizzo', 'orange'); ?>   con oggetto “PERSONALE NON DIRIGENTE. FONDO RISORSE DECENTRATE PER L’ANN0 <?php self::getInput('var454', 'anno', 'orange'); ?> . INDIRIZZI PER LA COSTITUZIONE.
                                    DIRETTIVE PER LA CONTRATTAZIONE DECENTRATA INTEGRATIVA” ha stabilito di incrementare le risorse variabili con le seguenti voci:
                                    <br /><br />
                                </li>
                            <?php endif ?>
                            <?php if (self::checkOptionalValues('F326')): ?>
                            <li>
                                ai sensi dell’art. 67 comma 5 lett. b) del CCNL 21.5.2018 è stata autorizzata l'iscrizione
                                della sola quota
                                di maggior incasso rispetto all’anno precedente a seguito di obiettivi di potenziamento dei
                                servizi di
                                controllo finalizzati alla sicurezza urbana e stradale Art. 56 quater CCNL 2018, come
                                risorsa NON soggetta
                                al limite secondo dalla Corte dei Conti Sezione delle Autonomie con delibera n. 5 del 2019,
                                per un importo
                                pari a € <?php self::getInput('var157', 'R152', 'orange'); ?>;
                                <br /><br />
                            </li>
                            <li>
                                <?php endif ?>
                                <?php if (self::checkOptionalValues('F223')): ?>
                                ai sensi dell’art. 67 comma 3 lett. a del CCNL 21.5.2018 è stata autorizzata l’iscrizione
                                fra le risorse
                                variabili delle somme derivanti da contratti di sponsorizzazione, accordi di collaborazione,
                                convenzioni con
                                soggetti pubblici o privati e contributi dell'utenza per servizi pubblici non essenziali,
                                secondo la
                                disciplina dettata dall'art. 43 della Legge 449/1997 per
                                € <?php self::getInput('var158', 'F222', 'orange'); ?>, rispettivamente per
                                <?php self::getTextArea('area30', ' (INSERIRE IL TITOLO o allegare i file TESTO LIBERO).', 'red'); ?>
                                <br /><br />
                            </li>
                        <?php endif ?>
                            <?php if (self::checkOptionalValues('F54')): ?>
                                <li>
                                    ai sensi della Legge 111/2011 e dell’art. 67 comma 3 lett. B del CCNL 21.5.2018, vista
                                    la
                                    Delibera <?php self::getInput('var159', 'della/del', 'orange'); ?>
                                    <?php self::getInput('var160', 'nome_soggetto_deliberante', 'orange'); ?>
                                    n. <?php self::getInput('var161', 'numero_delibera_approvazione_piano', 'orange'); ?>
                                    del <?php self::getInput('var162', 'data_delibera_approvazione_piano', 'orange'); ?> di
                                    approvazione del Piano di
                                    razionalizzazione <?php self::getInput('VAR163', 'are', 'orange'); ?> è stata
                                    autorizzata l’iscrizione tra le risorse variabili
                                    dell’importo pari a € <?php self::getInput('var164', 'R37', 'orange'); ?> , che dovrà
                                    essere distribuito nel
                                    rigoroso rispetto dei principi introdotti dalla
                                    norma vigente e solo in presenza, a consuntivo, del parere favorevole espresso dal
                                    Revisore dei Conti /
                                    Collegio dei Revisori;
                                    <br /><br />
                                </li>
                            <?php endif ?>
                            <?php if (self::checkOptionalValues('F339')): ?>
                            <li>
                                ai sensi dell’art. 67 c.7 e Art.15 c.7 CCNL 2018 è stata autorizzata all'iscrizione fra le
                                risorse variabili
                                la quota di incremento del Fondo trattamento accessorio per riduzione delle risorse
                                destinate alla
                                retribuzione di posizione e di risultato delle PO rispetto al tetto complessivo del salario
                                accessorio art.
                                23 c. 2 D.Lgs. 75/2017, per un importo pari a
                                € <?php self::getInput('var165', 'R155', 'orange'); ?>;
                                <?php endif ?>
                            </li>
                        </ul>
                        <br />
                    </li>
                    <li>
                        <b>
                            altre informazioni eventualmente ritenute utili per la migliore comprensione degli istituti
                            regolati
                            dal
                            contratto.
                        </b>
                        <br /><br />
                        Nessun'altra informazione
                        <br />
                        <br />
                    </li>

                </ul>


                <h6>Relazione tecnico finanziaria</h6>
                <br />
                <br />
                <b>Modulo I -La costituzione del Fondo per la contrattazione integrativa</b>
                <br />
                <br />
                Il Fondo per lo sviluppo delle risorse umane per
                l’anno <?php self::getInput('var166', 'anno', 'orange'); ?> ha seguito il seguente iter:
                <ul class="d">
                    <li>
                        Delibera n. <?php self::getInput('var167', 'numero_delibera_indirizzo', 'orange'); ?>
                        del <?php self::getInput('var168', 'data_delibera_indirizzo', 'orange'); ?> di
                        indirizzo <?php self::getInput('var169', 'della/del', 'orange'); ?> <?php self::getInput('var170', 'nome_soggetto_deliberante', 'orange'); ?>
                        alla delegazione di parte
                        pubblica e per la costituzione del Fondo <?php self::getInput('var171', 'anno', 'orange'); ?>;
                    </li>
                    <li>
                        Determina n. <?php self::getInput('vaar172', 'numero_determina_approvazione', 'orange'); ?>
                        del <?php self::getInput('var173', 'data_determina_approvazione', 'orange'); ?> di
                        costituzione del Fondo <?php self::getInput('var174', 'anno', 'orange'); ?>;
                        <br />
                    </li>
                </ul>
                <h5>Sezione I - Risorse fisse aventi carattere di certezza e stabilità</h5>
                Il fondo destinato alle politiche di sviluppo delle risorse umane ed alla produttività, in applicazione
                dell’art. 67 del CCNL del 21.05.2018, per
                l’anno <?php self::getInput('var175', 'anno', 'orange'); ?> risulta, come da allegato schema
                di costituzione del Fondo così riepilogato:
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2"><b>Risorse fisse aventi carattere di certezza e stabilità</b></th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td>
                            <b>
                                Totale Risorse storiche - Unico importo consolidato art. 67 c. 1 CCNL 21.05.2018
                                (A)
                            </b>
                        </td>
                        <td><?php self::getInput('var176', 'S1_1', 'orange'); ?></td>
                    </tr>

                    <tr>
                        <th colspan="2">Incrementi stabili</th>
                    </tr>

                    <?php if (self::checkOptionalValues('F200')): ?>
                        <tr>
                            <td>Art. 67 c. 2 lett. c) CCNL 2018 - RIA e assegni ad personam</td>
                            <td><?php self::getInput('var177', 'R124', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F300')): ?>
                        <tr>
                            <td>Art. 67 c. 2 lett. d) CCNL 2018 - eventuali risorse riassorbite</td>
                            <td><?php self::getInput('var178', 'R125', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F301')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 2 lett. e) CCNL 2018 - Oneri trattamento accessorio personale trasferito dal 2018
                            </td>
                            <td><?php self::getInput('var179', 'R126', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F302')): ?>
                        <tr>
                            <td>Art. 67 c. 2 lett. g) CCNL 2018 - Riduzione stabile Fondo Straordinario dal 2018</td>
                            <td><?php self::getInput('var180', 'R127', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F303')): ?>
                        <tr>
                            <td>Art . 67 c. 5 lett. a) CCNL 2018 - incremento dotazione organica dal 2018</td>
                            <td><?php self::getInput('var181', 'R128', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F324')): ?>
                        <tr>
                            <td>
                                Art. 33 comma 2 DL 34/2019 - Incremento valore medio procapite del fondo rispetto al 2018
                            </td>
                            <td><?php self::getInput('var182', 'R150', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td><b>Totale incrementi stabili (a)</b></td>
                        <td><?php self::getInput('var183', 'S1_2', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td><b>Totale risorse stabili SOGGETTE al limite (A+a)</b></td>
                        <td><?php self::getInput('var184', 'F335', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <th colspan="2"><b>Incrementi con carattere di certezza e stabilità NON soggetti al limite</b></th>
                    </tr>

                    <?php if (self::checkOptionalValues('F304')): ?>
                        <tr>
                            <td>Art. 67 c. 2 lett. b) CCNL 2018 - Rivalutazione delle PEO</td>
                            <td><?php self::getInput('var185', 'R112', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F320')): ?>
                        <tr>
                            <td>Art. 67 c. 2 lett. a) CCNL 2018 - Incremento 83,20 a valere dal 2019</td>
                            <td><?php self::getInput('var186', 'R146', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F379')): ?>
                        <tr>
                            <td>
                                Art. 79 c. 1 lett. b) e d) CCNL 2022 – Incremento 84,50 a valere dal 2021 e Rivalutazione
                                delle PEO
                            </td>
                            <td><?php self::getInput('var187', 'R148', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F323')): ?>
                        <tr>
                            <td>
                                Art. 79 c. 1 lett. b) e d) CCNL 2022 – Incremento 84,50 a valere dal 2021 e Rivalutazione
                                delle PEO
                            </td>
                            <td><?php self::getInput('var187', 'R148', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('f338')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 2 lett. e) CCNL 2018 – Rif Art. 1 c. 800 L. 205/2017 Armonizzazione personale
                                province transitato
                            </td>
                            <td><?php self::getInput('var188', 'R154', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F83')): ?>
                        <tr>
                            <td>Altre risorse</td>
                            <td><?php self::getInput('var189', 'F82', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>

                    <tr>
                        <td>
                            <b>Totale incrementi stabili non soggetti al limite (b)</b>
                        </td>
                        <td><?php self::getInput('var190', 'S1_3', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td><b>TOTALE RISORSE FISSE AVENTI CARATTERE DI CERTEZZA E STABILITÀ (A+a+b)</b></td>
                        <td><?php self::getInput('var191', 'F242', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <h5>Sezione II - Risorse variabili</h5>
                Quali voci variabili di cui all’art. 67 comma 3 CCNL 21.5.2018 sono state stanziate:
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2"><b>Risorse variabili</b></th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <th colspan="2"><b>Risorse variabili sottoposte al limite</b></th>
                    </tr>
                    <?php if (self::checkOptionalValues('F47')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. a) CCNL 2018- – sponsorizzazioni</td>
                            <td><?php self::getInput('var192', 'R29', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F48')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. c) CCNL 2018 ICI</td>
                            <td><?php self::getInput('var193', 'R30', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F74')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 - Legge Regionale specifica (es. SARDEGNA n. 19 del 1997)
                            </td>
                            <td><?php self::getInput('var194', 'R31', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F44')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. f) CCNL 2018 - – Compensi per Notifiche</td>
                            <td><?php self::getInput('var195', 'R32', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F45')): ?>
                        <tr>
                            <td>Art. 67 c. 4 CCNL 2018 - integrazione 1,2%</td>
                            <td><?php self::getInput('var196', 'R33', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F148')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 5 lett. b) CCNL 2018 - Obiettivi dell'Ente (anche potenziamento controllo Codice
                                Strada)
                            </td>
                            <td><?php self::getInput('var197', 'R34', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F306')): ?>
                        <tr>
                            <td> INTEGR. FONDO CCIAA IN EQ. FIN. (ART.15 C.1 L. N CCNL 98-01) R116</td>
                            <td><?php self::getInput('var198', 'R116', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F307')): ?>
                        <tr>
                            <td> Art. 67 c. 3 lett. g) CCNL 2018 - Compensi personale case da gioco R130</td>
                            <td><?php self::getInput('var199', 'R130', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F87')): ?>
                        <tr>
                            <td> Art. 67 c. 3 lett. k) CCNL 2018 - Oneri trattamento accessorio personale trasferito</td>
                            <td><?php self::getInput('var200', 'R131', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F54')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. d) CCNL 2018 - Ria e assegni ad personam personale cessato quota rateo
                                anno
                                di cessazione
                            </td>
                            <td><?php self::getInput('var201', 'R129', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F51')): ?>
                        <tr>
                            <td>
                                Art. 67 c.7 e Art.15 c.7 CCNL 2018 – Quota incremento Fondo per riduzione retribuzione di
                                PO e
                                di risultato
                            </td>
                            <td><?php self::getInput('var202', 'R155', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F52')): ?>
                        <tr>
                            <td> Altre risorse</td>
                            <td><?php self::getInput('var203', 'F86', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F88')): ?>
                        <tr>
                            <td><b>Totale voci variabili sottoposte al limite</b></td>
                            <td><?php self::getInput('var204', 'S2_1', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F285')): ?>
                        <tr>
                            <th colspan="2"><b>Risorse variabili NON sottoposte al limite</b></th>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F321')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. b) CCNL 2018- - Economie da piani di razionalizzazione</td>
                            <td><?php self::getInput('var205', 'R37', 'orange'); ?></td>

                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F53')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. c) CCNL 2018 - Compensi ISTAT</td>
                            <td><?php self::getInput('var206', 'R39', 'orange'); ?></td>

                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F49')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. c) CCNL 2018 - Avvocatura</td>
                            <td><?php self::getInput('var207', 'R40', 'orange'); ?></td>

                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F380')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 - Somme finanziate da fondi di derivazione dell'Unione
                                Europea
                            </td>
                            <td><?php self::getInput('var208', 'R41', 'orange'); ?></td>

                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F381')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 - - INCENTIVI PER FUNZIONI TECNICHE Art. 113 D.Lgs.
                                50/2016
                            </td>
                            <td><?php self::getInput('var209', 'R122', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F92')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. c) CCNL 2018 - Compensi IMU e TARI c. 1091 L. 145/2018</td>
                            <td><?php self::getInput('var210', 'R147', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F90')): ?>
                        <tr>
                            <td>Altro - Art. 67 c. 3 lett. c) CCNL 2018 (Da specificare)</td>
                            <td><?php self::getInput('var211', 'R111', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F89')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. a) CCNL 2018 - – sponsorizzazioni (per convenzioni successive al 2016)
                            </td>
                            <td><?php self::getInput('var212', 'R42', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F326')): ?>
                        <tr>
                            <td>ALTRE RISORSE (Da specificare)</td>
                            <td><?php self::getInput('var213', 'f91', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F153')): ?>
                        <tr>
                            <td>Art. 68 c. 1 CCNL 2018 - Risparmi Fondo Stabile Anno Precedente</td>
                            <td><?php self::getInput('var214', 'R44', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F326')): ?>
                        <tr>
                            <td>Art. 67 c. 3 lett. e) CCNL 2018 - Risparmi Fondo Straordinario Anno Precedente</td>
                            <td><?php self::getInput('var215', 'R45', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F153')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 5 lett. b) CCNL 2018 - Quota incremento CDS maggior incasso rispetto anno
                                precedente
                            </td>
                            <td><?php self::getInput('var216', 'R152', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td><b>Totale voci variabili NON sottoposte al limite</b></td>
                        <td><?php self::getInput('var217', 'S2_3', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td><b>TOTALE RISORSE VARIABILI</b></td>
                        <td><?php self::getInput('var218', 'f5', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <h5>Sezione III - (eventuali) Decurtazioni del Fondo</h5>
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2">
                            <b>
                                DECURTAZIONI SULLE RISORSE AVENTI CARATTERE DI CERTEZZA E STABILITA’ (a
                                detrarre)
                            </b>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (self::checkOptionalValues('F41')): ?>
                        <tr>
                            <td>Decurtazione ATA</td>
                            <td><?php self::getInput('var219', 'R25', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F72')): ?>
                        <tr>
                            <td>Decurtazione incarichi di Posizione Organizzativa (Enti con e Senza Dirigenza)</td>
                            <td><?php self::getInput('var220', 'R26', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F73')): ?>
                        <tr>
                            <td>
                                Articolo 19, comma 1 CCNL 1.4.1999
                                DECURTAZIONE primo inquadramento di alcune categorie di lavoratori in applicazione del CCNL
                                del
                                31.3.1999 (area di vigilanza e personale della prima e seconda qualifica funzionale).
                            </td>
                            <td><?php self::getInput('var221', 'R27', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F325')): ?>
                        <tr>
                            <td>
                                Decurtazione art 67 c. 2 lett. e) Ccnl 2018 - personale trasferito presso altri Enti per
                                delega
                                o trasferimento di funzioni, da disposizioni di legge o altro
                            </td>
                            <td><?php self::getInput('var222', 'R151', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F85')): ?>
                        <tr>
                            <td> ALTRE RISORSE (da specificare)</td>
                            <td><?php self::getInput('var223', 'F84', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td>
                            Decurtazione parte stabile operate nel periodo 2011/2014 ai sensi dell'art. 9 C. 2 bis
                            L.122/2010 secondo periodo
                        </td>
                        <td><?php self::getInput('var224', 'R117', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td> Decurtazioni PARTE STABILE operate nel 2016 per cessazioni e rispetto limite 2015</td>
                        <td><?php self::getInput('var225', 'R120', 'orange'); ?></td>
                    </tr>
                    <?php if (self::checkOptionalValues('F226')): ?>
                        <tr>
                            <td> Decurtazione parte stabile per rispetto limite 2016</td>
                            <td><?php self::getInput('var226', 'F14', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td>TOTALE DECURTAZIONI AVENTI CARATTERE DI CERTEZZA E STABILITA’</td>
                        <td><?php self::getInput('var227', 'F278', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="2"><b>Decurtazioni Risorse variabili</b></th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <th colspan="2"><b>Risorse variabili sottoposte al limite</b></th>
                    </tr>
                    <?php if (self::checkOptionalValues('F6')): ?>
                        <tr>
                            <td>Altre decurtazioni</td>
                            <td><?php self::getInput('var228', 'S2_2', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td>
                            Decurtazione parte variabile operate nel periodo 2011/2014 ai sensi dell'art. 9 C. 2 bis
                            L.122/2010 secondo periodo
                        </td>
                        <td><?php self::getInput('var229', 'R118', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            Decurtazioni PARTE variabile operate nel 2016 per cessazioni e rispetto limite 2015
                        </td>
                        <td><?php self::getInput('var230', 'R121', 'orange'); ?></td>
                    </tr>
                    <?php if (self::checkOptionalValues('F231')): ?>
                        <tr>
                            <td>Decurtazione parte variabile per rispetto limite 2016</td>
                            <td><?php self::getInput('var231', 'f19', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td><b>TOTALE DECURTAZIONI PARTE VARIABILE</b></td>
                        <td><?php self::getInput('var232', 'f279', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td><b>TOTALE DECURTAZIONI</b></td>
                        <td><?php self::getInput('var233', 'f280', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                Si evidenzia che il secondo periodo dell’art. 9 c. 2 bis del DL 78/2010 convertito con modificazioni nella
                legge n. 122/2010, inserito dalla Legge di Stabilità 2014 (Legge n. 147/2013) all'art. 1, comma 456,
                stabilisce “ che: «A decorrere dal 1º gennaio 2015, le risorse destinate annualmente al trattamento
                economico accessorio sono decurtate di un importo pari alle riduzioni operate per effetto del precedente
                periodo»
                <br />

                Pertanto, a partire dall'anno 2015 le risorse decentrate dovranno essere ridotte dell'importo decurtato per
                il triennio 2011/2014, mediante la conferma della quota di decurtazione operata nell'anno 2014 per
                cessazioni e rispetto del 2010 (Circolare RGS n. 20 del 8.5.20105).
                <br />
                <br />
                Nel periodo 2011-2014 <?php self::getInput('var234', 'f273', 'orange'); ?> risultano decurtazioni
                rispetto ai vincoli
                sul fondo 2010 e pertanto <?php self::getInput('var235', 'f273', 'orange'); ?> deve
                essere applicata una riduzione del fondo
                dell'anno pari a
                € <?php self::getInput('var236', 'f263', 'orange'); ?>.
                <br />
                <br />
                Si evidenzia che l’art. 1 c. 236 della L. 208/2015 prevedeva che a decorrere dal 1° gennaio 2016 (nelle more
                dell'adozione dei decreti legislativi attuativi degli articoli 11 e 17 della legge 7 agosto 2015, n. 124,
                con particolare riferimento all'omogeneizzazione del trattamento economico fondamentale e accessorio della
                dirigenza,), l'ammontare complessivo delle risorse destinate annualmente al trattamento accessorio del
                personale non può superare il corrispondente importo dell’anno 20105. Lo stesso comma disponeva la riduzione
                in misura proporzionale dello stesso in conseguenza della cessazione dal servizio di una o più unità di
                personale dipendente (tenendo conto del personale assumibile ai sensi della normativa vigente) .
                <br />
                <br />
                Si evidenzia inoltre che l'art. 23 del D.Lgs. 75/2017 ha stabilito che “a decorrere dal 1° gennaio 2017,
                l'ammontare complessivo delle risorse destinate annualmente al trattamento accessorio del personale, anche
                di livello dirigenziale, di ciascuna delle amministrazioni pubbliche di cui all'articolo 1,comma 2, del
                decreto legislativo 30 marzo 2001, n. 165, non può superare il corrispondente importo determinato per l'anno
                2016. A decorrere dalla predetta data l'articolo 1, comma 236, della legge 28 dicembre 2015, n. 208 e'
                abrogato.”
                <br />
                <br />
                In seguito all’introduzione delle disposizioni dell’art. 33 comma 2, del D.L.34/2019, convertito in Legge
                58/2019 (c.d. Decreto “Crescita”), il tetto al salario accessorio, così come introdotto dall'articolo 23,
                comma 2, del D.Lgs 75/2017, può essere modificato. La modalità di applicazione definita nel DPCM del
                17.3.2020, pubblicato in GU in data 27.4.2020, concordata in sede di Conferenza Unificata Stato Regioni del
                11.12.2019, prevede che il limite del salario accessorio, a partire dal 20 aprile 2020, debba essere
                adeguato in aumento rispetto al valore medio procapite del 2018 in caso di incremento del numero di
                dipendenti presenti nel anno , rispetto ai
                presenti al 31.12.2018, al fine di garantire l’invarianza della
                quota media procapite rispetto al 2018. Ed in particolare è fatto salvo il limite iniziale qualora il
                personale in servizio sia inferiore al numero rilevato al 31 dicembre 2018. Tale incremento va calcolato in
                base alle modalità fornite dalla Ragioneria dello Stato da ultimo con nota Prot. 12454 del 15.1.2021.
                <br />
                <br />
                Nell'anno 2016 <?php self::getInput('var237', 'f283', 'orange'); ?> risultano decurtazioni
                rispetto ai vincoli sul fondo
                2015 e pertanto <?php self::getInput('var238', 'f283', 'orange'); ?> deve essere
                applicata una riduzione del fondo pari a<?php self::getInput('var239', 'f282', 'orange'); ?>
                <br />
                <br />
                Si precisa che il totale del fondo (solo voci soggette al blocco) per l'anno 2016 era pari a
                € <?php self::getInput('var240', 'f1', 'orange'); ?> (include
                eventuale rivalutazione ai sensi dell’art. 33 comma 2, del D.L.34/2019, nel caso l'ente ne abbia facoltà)
                mentre per l’anno <?php self::getInput('var241', 'anno', 'orange'); ?> al netto delle
                decurtazioni è pari ad € <?php self::getInput('var242', 'f253', 'orange'); ?> .
                <br />
                <br />
                Pertanto si attesta che il fondo <?php self::getInput('var243', 'anno', 'orange'); ?> risulta
                non superiore al fondo anno 2016 (Tali valori non includono
                avvocatura, ISTAT, di cui art. 67 comma 3 lett. c CCNL 21.5.2018, importi di cui all’art. 67 comma 3 lett. c
                CCNL 21.5.2018, importi di cui all’67 comma 3 lett. a, ove tale attività non risulti ordinariamente resa
                dall’Amministrazione precedentemente l’entrata in vigore del D.Lgs
                75/2017, <?php self::getInput('var244', 'mporti di cui all’art. 67 comma 2 lett. b, art. 79 c. 1 lett. b CCNL 16.11.2022, art. 79 c.1 lett. c CCNL 16.11.2022, art. 79 c.3 CCNL 16.11.2022, art. 79 c. 5 CCNL 16.11.2022,', 'orange'); ?>
                , economie del fondo dell’anno precedente e economie del fondo straordinario anno precedente).
                <br />
                <br />
                <h5>Sezione IV - Sintesi della costituzione del Fondo sottoposto a certificazione</h5>
                <br />
                <table class="table">
                    <tbody>

                    <tr>
                        <td>TOTALE Risorse fisse aventi carattere di certezza e stabilità (A)</td>
                        <td><?php self::getInput('var245', 'f242', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>TOTALE decurtazioni aventi carattere di certezza e stabilita’ (B)</td>
                        <td><?php self::getInput('var246', 'f278', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                TOTALE Risorse fisse aventi carattere di certezza e stabilità DOPO LE DECURTAZIONI
                                (A-B)
                            </b>

                        </td>
                        <td><?php self::getInput('var247', 'f292', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>TOTALE Risorse variabili (C)</td>
                        <td><?php self::getInput('var248', 'f5', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>DECURTAZIONI sulle voci variabili (D)</td>
                        <td><?php self::getInput('var249', 'f279', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td><b>Totale risorse variabili dopo le decurtazioni (C-D)</b></td>
                        <td><?php self::getInput('var250', 'f255', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                TOTALE FONDO
                                (A-B)+ (C-D)
                            </b>
                        </td>
                        <td><?php self::getInput('var251', 'f254', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <h5>Sezione V – Risorse temporaneamente allocate all'esterno del fondo</h5>
                <br />
                Parte non pertinente allo specifico accordo illustrato.
                <br />
                <br />
                Si precisa che ai sensi dell'Art. 33 del CCNL 22.1.2004 l'indennità di comparto prevede una parte di risorse
                a carico del bilancio (cosiddetta quota a) e una parte a carico delle risorse decentrate (cosiddette quote b
                e c). Gli importi di cui alla lettera a) risultano pari a
                € <?php self::getInput('var252', 'xxxxx', 'orange'); ?>, gli importi di cui alle lettere b) e
                c)
                ammontano ad un totale di € <?php self::getInput('var253', 'R56', 'orange'); ?> .
                <br />
                <br />
                <?php self::getTextArea('area31', 'Per quanto riguarda le PEO in godimento, vengono inseriti a carico del fondo, gli importi “cristallizzati”, sulla base dei valori delle progressioni vigenti nell anno di decorrenza dei relativi benefici, mentre la differenza rispetto al costo erogato nella busta paga (aggiornato con l aumento del costo di dette progressioni dovuto agli incrementi stipendiali) resta a carico del bilancio (Dichiarazione congiunta n.14 CCNL 22.1.2004).', 'red'); ?>
                <br />
                <br />
                <?php self::getTextArea('area32', 'Per quanto riguarda le PEO in godimento, vengono inseriti a carico del fondo, gli importi rivalutati (aggiornati con l aumento del costo di dette progressioni dovuto agli incrementi stipendiali - Dichiarazione congiunta n. 14 CCNL 22.1.2004) e quelli derivanti dall’applicazione dell’art. 67 c. 2 lett. b) CCNL 21.5.2018 NON soggetta al limite (come indicato dalla Dichiarazione congiunta n. 5 e confermato dalla Delibera Sezione Autonomie della Corte dei Conti n. 19/2018.', 'red'); ?>
                <h4>Modulo II - Definizione delle poste di destinazione del Fondo per la contrattazione integrativa</h4>
                <br />
                <br />
                <h5>
                    Sezione I - Destinazioni non disponibili alla contrattazione integrativa o comunque non regolate
                    specificamente dal Contratto Integrativo sottoposto a certificazione
                </h5>
                Per l’anno <?php self::getInput('var253', 'anno', 'orange'); ?> con la determina di
                costituzione del Fondo n. <?php self::getInput('var254', 'numero_determina_approvazione', 'orange'); ?> del
                <?php self::getInput('var255', 'data_determina_approvazione', 'orange'); ?>
                il responsabile ha reso indisponibile alla
                contrattazione ai sensi dell’art. 68
                comma 1 del CCNL 21.5.2018 alcuni compensi gravanti sul fondo (es. indennità di comparto, progressioni
                economiche) poiché già determinate negli anni precedenti.
                <br />
                <br />
                Vanno, inoltre, sottratte alla contrattazione le risorse non regolate specificatamente dal Contratto
                Integrativo poiché regolate nelle annualità precedenti.
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th><b>UTILIZZO RISORSE NON DISPONIBILI ALLA CONTRATTAZIONE</b></th>
                        <th><?php self::getInput('var256', 'anno', 'orange'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (self::checkOptionalValues('F160')): ?>
                        <tr>
                            <td>Inquadramento ex Led</td>
                            <td><?php self::getInput('var257', 'R53', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F161')): ?>
                        <tr>
                            <td>Progressioni economiche STORICHE</td>
                            <td><?php self::getInput('var258', 'R54', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F162')): ?>
                        <tr>
                            <td>
                                Indennità di comparto art. 33 CCNL 22.01.04, quota a carico fondo

                            </td>
                            <td><?php self::getInput('var259', 'R56', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F163')): ?>
                        <tr>
                            <td>Indennità educatori asilo nido</td>
                            <td><?php self::getInput('var260', 'R57', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F189')): ?>
                        <tr>
                            <td>ALTRI UTILIZZI</td>
                            <td><?php self::getInput('var261', 'f66', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td><b>Totale utilizzo risorse stabili</b></td>
                        <td><?php self::getInput('var262', 'f93', 'orange'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <b>TOTALE RISORSE NON REGOLATE SPECIFICAMENTE DAL CONTRATTO INTEGRATIVO</b>
                        </td>
                        <td><?php self::getInput('var263', 'f93', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                CALCOLO RISORSE PER PROGRESSIONI ORIZZONTALI IN ESSERE:
                <br />
                <br />
                <?php self::getTextArea('area33', 'Illustrare qui la modalità di calcolo ed eventualmente allegare lo schema di determinazione dell’importo.', 'orange'); ?>
                <br />
                <br />
                COSTO PER INDENNITA’ DI COMPARTO
                <br />
                <?php self::getTextArea('area34', 'Illustrare qui la modalità di calcolo ed eventualmente allegare lo schema di determinazione dell’importo.', 'orange'); ?>
                <br />
                <br />
                <h5>Sezione II - Destinazioni specificamente regolate dal Contratto Integrativo</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">
                            <b>DESTINAZIONI REGOLATE SPECIFICAMENTE DAL CONTRATTO INTEGRATIVO</b>
                        </th>
                        <th><?php self::getInput('var264', 'anno', 'orange'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (self::checkOptionalValues('F115')): ?>
                        <tr>
                            <td>Progressioni economiche specificatamente contratte nel CCDI dell'anno</td>
                            <td><?php self::getInput('var265', 'R55', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F125')): ?>
                        <tr>
                            <td>Turno</td>
                            <td><?php self::getInput('var266', 'R65', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F293')): ?>
                        <tr>
                            <td>
                                Indennità condizioni di lavoro Art. 70 bis CCNL 2018 (Maneggio valori, attività disagiate e
                                esposte a rischi)
                            </td>
                            <td><?php self::getInput('var267', 'R145', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F128')): ?>
                        <tr>
                            <td>
                                Reperibilità
                            </td>
                            <td><?php self::getInput('var268', 'R71', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F132')): ?>
                        <tr>
                            <td>
                                Indennità specifiche Responsabilità art. 70 quinquies c. 1 CCNL 2018 (ex art. 17 lett. f)
                            </td>
                            <td><?php self::getInput('var269', 'R75', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F133')): ?>
                        <tr>
                            <td>
                                Indennità specifiche Responsabilità art. 70 quinquies c. 1 CCNL 2018 (ex art. 17 lett. i)
                            </td>
                            <td><?php self::getInput('var270', 'R77', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F134')): ?>
                        <tr>
                            <td>
                                Particolare compenso incentivante personale Unioni dei comuni (art. 13 c. 5 CCNL
                                22.1.2004)
                            </td>
                            <td><?php self::getInput('var271', 'R79', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F135')): ?>
                        <tr>
                            <td> Centri estivi asili nido (art 31 c. 5CCNL 14 .9.2000 Code)</td>
                            <td><?php self::getInput('var272', 'R81', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F136')): ?>
                        <tr>
                            <td>
                                Compenso previsto dall'art.24, comma 1 CCNL 14.9.2000, per il personale che presta attività
                                lavorativa nel giorno destinato al riposo settimanale
                            </td>
                            <td><?php self::getInput('var273', 'R83', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F137')): ?>
                        <tr>
                            <td>Premi collegati alla performance organizzativa – art. 68 c. 2 lett. a) CCNL 2018</td>
                            <td><?php self::getInput('var274', 'R85', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F138')): ?>
                        <tr>
                            <td>Premi collegati alla performance individuale - art. 68 c. 2 lett. b) CCNL 2018</td>
                            <td><?php self::getInput('var275', 'R87', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F186')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance organizzativa - Obiettivi finanziati con risorse Art. 67 c.
                                5 lett. b) CCNL 2018
                            </td>
                            <td><?php self::getInput('var276', 'R88', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F296')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance organizzativa - Obiettivi collegati a risorse art 67 c. 5
                                lett. b di potenziamento dei servizi di controllo finalizzati alla sicurezza urbana e
                                stradale Art. 56 quater CCNL 2018
                            </td>
                            <td><?php self::getInput('var277', 'R136', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F294')): ?>
                        <tr>
                            <td>Indennità di servizio esterno – art. 56 quinquies CCNL 2018 (Vigilanza)</td>
                            <td><?php self::getInput('var278', 'R134', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F295')): ?>
                        <tr>
                            <td>Indennità di funzione – Art. 56 sexies CCNL 2018 (Vigilanza)</td>
                            <td><?php self::getInput('var279', 'R135', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F167')): ?>
                        <tr>
                            <td>
                                Compensi 50% economie da Piani di Razionalizzazione - Art. 67 c. 3 lett. b) CCNL 2018-Art.
                                16 C. 5 L. 111/2011
                            </td>
                            <td><?php self::getInput('var280', 'R110', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F322')): ?>
                        <tr>
                            <td>
                                ALTRI UTILIZZI (contrattati nel CCDI dell'anno)
                            </td>
                            <td><?php self::getInput('var281', 'F68', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F168')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance organizzativa - Compensi per SPONSORIZZAZIONI Art. 67 c. 3
                                lett. a) CCNL 2018
                            </td>
                            <td><?php self::getInput('var282', 'R91', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F169')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 FUNZIONI TECNICHE RIF Art. 113 comma 2 e 3 D.LGS. 18 APRILE
                                2016, N. 50
                            </td>
                            <td><?php self::getInput('var283', 'R92', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F170')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 COMPENSI IMU e TARI c. 1091 L. 145/2018
                            </td>
                            <td><?php self::getInput('var284', 'R149', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F171')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. h CCNL 2018 - Compensi per notifiche
                            </td>
                            <td><?php self::getInput('var285', 'R93', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F172')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 RIF – ISTAT
                            </td>
                            <td><?php self::getInput('var286', 'R94', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F173')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 RIF - ICI
                            </td>
                            <td><?php self::getInput('var287', 'R95', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F190')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 RIF – avvocatura
                            </td>
                            <td><?php self::getInput('var288', 'R96', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F272')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 RIF - Diritto soggiorno Unione Europea D.lgs 30/2007
                            </td>
                            <td><?php self::getInput('var289', 'R108', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F173')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 Legge Regionale specifica
                            </td>
                            <td><?php self::getInput('var290', 'R109', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F190')): ?>
                        <tr>
                            <td>
                                Altri utilizzi Art. 68 c. 2 lett. g) CCNL 2018
                            </td>
                            <td><?php self::getInput('var291', 'f69', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F272')): ?>
                        <tr>
                            <td>
                                Quota recupero somme (Art. 4 DL 16/2014 Salva Roma Ter)
                            </td>
                            <td><?php self::getInput('var292', 'R119', 'orange'); ?></td>
                        </tr>
                    <?php endif ?>

                    <tr>
                        <td>
                            <b>
                                TOTALE RISORSE REGOLATE SPECIFICAMENTE DAL CONTRATTO INTEGRATIVO
                            </b>
                        </td>
                        <td><?php self::getInput('var293', 'f243', 'orange'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <h5>Sezione III - (eventuali) Destinazioni ancora da regolare</h5>
                <br />
                Parte non pertinente allo specifico accordo illustrato.
                <br />
                <br />
                Le risorse ancora da contrattare ammontano ad
                €<?php self::getInput('var294', 'f78', 'orange'); ?>
                <br />

                <br />
                <table class="table">
                    <tbody>
                    <tr>
                        <td>
                            <b>TOTALE RISORSE non regolate specificamente dal Contratto Integrativo (A)</b>
                        </td>
                        <td><?php self::getInput('var295', 'f93', 'orange'); ?></td>
                        <td>+</td>

                    </tr>
                    <tr>
                        <td>
                            <b>TOTALE RISORSE regolate specificamente dal Contratto Integrativo (B)</b>
                        </td>
                        <td><?php self::getInput('var296', 'f243', 'orange'); ?></td>
                        <td>=</td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                TOTALE UTILIZZO
                                (A+B)
                            </b>

                        </td>
                        <td><?php self::getInput('var297', 'f77', 'orange'); ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>TOTALE DESTINAZIONI ANCORA DA REGOLARE [TOTALE FONDO – (A+B)]</b>
                        </td>
                        <td><?php self::getInput('var298', 'f78', 'orange'); ?></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <h5>Sezione V Destinazioni temporaneamente allocate all'esterno del fondo</h5>
                <br />
                <?php if (self::checkOptionalValues('F237')): ?>
                    Parte non pertinente allo specifico accordo illustrato.
                <?php endif ?>
                <br />
                <br />
                Si precisa che ai sensi dell'Art. 33 del CCNL 22.1.2004 l'indennità di comparto prevede una parte di
                risorse a carico del bilancio (cosiddetta quota a) e una parte a carico delle risorse decentrate
                (cosiddette quote b e c). Gli importi di cui alla lettera a) risultano pari a
                € <?php self::getInput('var299', 'xxxx,xx', 'orange'); ?>, gli importi di cui
                alle lettere b) e c) ammontano ad un totale di
                €<?php self::getInput('var300', 'R56', 'orange'); ?>.
                <br />
                <br />
                <?php self::getTextArea('area35', 'Per quanto riguarda le PEO in godimento, vengono inseriti a carico del fondo, gli importi “cristallizzati”, sulla base dei valori delle progressioni vigenti nell anno di decorrenza dei relativi benefici, mentre la differenza rispetto al costo erogato nella busta paga (aggiornato con l aumento del costo di dette progressioni dovuto agli incrementi stipendiali) resta a carico del bilancio (Dichiarazione congiunta n.14 CCNL 22.1.2004).', 'red'); ?>
                <br />
                <br />
                <?php self::getTextArea('area36', 'Per quanto riguarda le PEO in godimento, vengono inseriti a carico del fondo, gli importi rivalutati (aggiornati con l aumento del costo di dette progressioni dovuto agli incrementi stipendiali - Dichiarazione congiunta n.14 CCNL 22.1.2004) e quelli derivanti dall’applicazione dell’art. 67 c. 2 lett. b) CCNL 21.5.2018 NON soggetta al limite (come indicato dalla Dichiarazione congiunta n. 5 e confermato dalla Delibera Sezione Autonomie della Corte dei Conti n. 19/2018.
                ATTENZIONE: deve corrispondere con la sezione V del Modulo I', 'red'); ?>
                <br />
                <br />
                <h5>
                    Sezione VI - Attestazione motivata, dal punto di vista tecnico-finanziario, del rispetto di vincoli di
                    carattere generale
                </h5>
                La presente relazione, in ossequio a quanto disposto dall’art. 40 c. 3 sexies del D.Lgs 165/2001, così
                come modificato dal D. Lgs 150/2009 persegue l’obiettivo di fornire una puntuale e dettagliata
                relazione, dal punto di vista finanziario, circa le risorse economiche costituenti il fondo per le
                risorse decentrate e, dal punto di vista tecnico, per illustrare le scelte effettuate e la coerenza di
                queste con le direttive dell’Amministrazione.
                <br />
                <br />
                Con la presente si attesta:
                <ul class="a">
                    <li>
                        <b>
                            Il rispetto della copertura delle risorse destinate a finanziare indennità di carattere certo e
                            continuativo con risorse stabili e consolidate.
                        </b>
                        <br />
                        <br />
                        Come evidenziato dalle precedenti sezioni, le indennità fisse di carattere certo e continuativo
                        (PEO,
                        Indennità di comparto) pari a € <?php self::getInput('var301', 'S4_1', 'orange'); ?> sono
                        completamente finanziate
                        dalle risorse stabili pari ad € <?php self::getInput('var302', 'f192', 'orange'); ?>.
                        <br /><br />
                    </li>
                    <li>
                        <b>Il rispetto del principio di attribuzione selettiva degli incentivi economici.</b>
                        <br />
                        <br />
                        Le previsioni sono coerenti con le disposizioni in materia di meritocrazia e premialità in quanto
                        viene
                        applicato il Sistema di Valutazione e Misurazione della Performance, adeguato al D.lgs 150/2009 e
                        all’art. 68 comma lett. a-b del CCNL 21.5.2018.
                        <br />
                        <br />
                        Le risorse destinate alla performance saranno riconosciute attraverso la predisposizione di
                        obiettivi
                        strategici ed operativi dell’Amministrazione (contenuti nel Piano Performance), al fine di
                        contribuire
                        al raggiungimento dei risultati previsti negli strumenti di pianificazione e gestione.
                        <br />
                        Sinteticamente viene riportata la modalità di ripartizione delle risorse destinate alla performance.
                        <br />
                        <br />
                        <?php self::getTextArea('area37', '(viene fornito un esempio, da completare a cura dell’Ente)
                        Valutazione superiore a xx% - erogazione premio xx%
                        Valutazione compresa tra xx% e xx% - erogazione premio xx%
                        Valutazione inferiore a xx% - nessuna erogazione di premio', 'orange'); ?>
                    </li>
                    <li>
                        <b> Il rispetto del principio di selettività delle progressioni di carriera.</b>
                        <br />
                        <br />
                        In particolare, si evidenzia che
                        <br />
                        <br />
                        <?php if (self::checkOptionalValues('F221')): ?>
                            per l’anno in corso non è previsto il riconoscimento di progressioni orizzontali
                        <?php endif ?>
                        <br />
                        <br />
                        <?php if (self::checkOptionalValues('F115')): ?>
                            per l’anno in corso è previsto il riconoscimento di progressioni orizzontali che saranno attribuite con
                            la seguente modalità

                            <?php self::getTextArea('area38', '(descrivere sinteticamente la modalità e cosa valuta)', 'red'); ?>
                        <?php endif ?>
                    </li>
                </ul>
                <br />
                <br />
                <h4>
                    Modulo III - Schema generale riassuntivo del Fondo per la contrattazione integrativa e confronto
                    con il
                    corrispondente Fondo certificato dell’anno precedente
                </h4>
                <br />
                <br />
                In dettaglio:
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="4">Tabella 1</th>

                    </tr>
                    <tr>
                        <th scope="col"><b>COSTITUZIONE DEL FONDO</b></th>
                        <th scope="col">
                            <b>
                                Fondo <?php self::getInput('var303', 'anno', 'orange'); ?>(A)
                            </b>
                        </th>
                        <th scope="col">
                            <b>
                                Fondo <?php self::getInput('var304', 'F13', 'orange'); ?>
                                (B)
                            </b>
                        </th>
                        <th scope="col"><b>Diff A-B</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th colspan="4"><b>Risorse fisse aventi carattere di certezza e stabilità</b></th>
                    </tr>
                    <tr>
                        <th colspan="4"><b>Risorse storiche (A)</b></th>
                    </tr>
                    <?php if (self::checkOptionalValues('F140')): ?>
                        <tr>

                            <td>Unico importo consolidato anno 2017 (art. 67 c. 1 Ccnl EELL 2018)</td>
                            <td><?php self::getInput('var305', 'S1_1', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <th colspan="4"><b>Incrementi stabili (A)</b></th>
                    </tr>
                    <?php if (self::checkOptionalValues('F299')): ?>
                        <tr>

                            <td>Art. 67 c. 2 lett. c) CCNL 2018 - RIA e assegni ad personam</td>
                            <td><?php self::getInput('var306', 'R124', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F300')): ?>
                        <tr>

                            <td>Art. 67 c. 2 lett. d) CCNL 2018 - eventuali risorse riassorbite</td>
                            <td><?php self::getInput('var307', 'R125', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F301')): ?>
                        <tr>

                            <td>
                                Art. 67 c. 2 lett. e) CCNL 2018 - Oneri trattamento accessorio personale trasferito dal
                                2018
                            </td>
                            <td><?php self::getInput('var308', 'R126', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F302')): ?>
                        <tr>

                            <td>Art. 67 c. 2 lett. g) CCNL 2018 - Riduzione stabile Fondo Straordinario dal 2018</td>
                            <td><?php self::getInput('var309', 'R127', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F303')): ?>
                        <tr>

                            <td>Art . 67 c. 5 lett. a) CCNL 2018 - incremento dotazione organica dal 2018</td>
                            <td><?php self::getInput('var310', 'R128', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F324')): ?>
                        <tr>

                            <td>
                                Art. 33 comma 2 DL 34/2019 - Incremento valore medio procapite del fondo rispetto al 2018
                            </td>
                            <td><?php self::getInput('var311', 'R150', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>

                    <tr>
                        <th colspan="4">
                            <b>Incrementi con carattere di certezza e stabilità NON soggetti al limite (b)</b>
                        </th>
                    </tr>
                    <?php if (self::checkOptionalValues('F304')): ?>
                        <tr>

                            <td>Art. 67 c. 2 lett. b) CCNL 2018 - Rivalutazione delle PEO</td>
                            <td><?php self::getInput('var312', 'R112', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F320')): ?>
                        <tr>

                            <td>Art. 67 c. 2 lett. a) CCNL 2018 Incremento € 83,20 a valere dal 2019</td>
                            <td><?php self::getInput('var313', 'R146', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F379')): ?>
                        <tr>

                            <td> Art. 11 c.1 lett. b) D.L.135/2018</td>
                            <td><?php self::getInput('var314', 'R148', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>

                    <?php if (self::checkOptionalValues('F323')): ?>
                        <tr>

                            <td>ncremento € 84,50 a valere dal 2021 e Rivalutazione delle PEO</td>
                            <td><?php self::getInput('var500', 'R163', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F338')): ?>
                        <tr>

                            <td>
                                Art. 67 c. 2 lett. e) CCNL 2018 – Rif Art. 1 c. 800 L. 205/2017 Armonizzazione personale
                                province transitato
                            </td>
                            <td><?php self::getInput('var315', 'R154', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F83')): ?>
                        <tr>

                            <td> Altre risorse stabili</td>
                            <td><?php self::getInput('var316', 'f82', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>

                    <tr>

                        <td> Totale risorse fisse aventi carattere di certezza e stabilità SOGGETTE al limite (A+a)</td>
                        <td><?php self::getInput('var317', 'f335', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>

                        <td>
                            <b>
                                Totale risorse fisse con carattere di certezza
                                e stabilità
                            </b>
                        </td>
                        <td><?php self::getInput('var318', 'f242', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <b>Risorse variabili</b>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <b>Risorse variabili sottoposte al limite</b>
                        </th>
                    </tr>
                    <?php if (self::checkOptionalValues('F46')): ?>
                        <tr>

                            <td>
                                Art. 67 c. 3 lett. a) CCNL 2018 – sponsorizzazioni
                            </td>
                            <td><?php self::getInput('var319', 'R29', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F47')): ?>
                        <tr>

                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 ICI
                            </td>
                            <td><?php self::getInput('var320', 'R30', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F48')): ?>
                        <tr>

                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 Legge Regionale specifica (es. SARDEGNA n. 19 del 1997)
                            </td>
                            <td><?php self::getInput('var321', 'R31', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F74')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. f) CCNL 2018 – Compensi per Notifiche
                            </td>
                            <td><?php self::getInput('var322', 'R32', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F44')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 4 CCNL 2018 (1,2% m salari 1997)
                            </td>
                            <td><?php self::getInput('var323', 'R33', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F45')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 5 lett. b) CCNL 2018 - Obiettivi dell'Ente (anche potenziamento controllo Codice
                                Strada)
                            </td>
                            <td><?php self::getInput('var324', 'R34', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <tr>
                        <?php endif ?>
                        <?php if (self::checkOptionalValues('F148')): ?>
                        <td>
                            INTEGR. FONDO CCIAA IN EQ. FIN. (ART.15 C.1 L. N CCNL 98-01) R116
                        </td>
                        <td><?php self::getInput('var325', 'R116', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F305')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. d) CCNL 2018 - Ria e assegni ad personam personale cessato quota rateo
                                anno di cessazione
                            </td>
                            <td><?php self::getInput('var326', 'R129', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F306')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. g) CCNL 2018 - Compensi personale case da gioco
                            </td>
                            <td><?php self::getInput('var327', 'R131', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F307')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. k) CCNL 2018 - Oneri trattamento accessorio personale trasferito
                            </td>
                            <td><?php self::getInput('var328', 'R155', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F339')): ?>
                        <tr>
                            <td>
                                Art. 67 c.7 e Art.15 c.7 CCNL 2018 – Quota incremento Fondo per riduzione retribuzione di
                                PO e di risultato
                            </td>
                            <td><?php self::getInput('var329', 'f86', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F87')): ?>
                        <tr>
                            <td>
                                Altre risorse
                            </td>
                            <td><?php self::getInput('var330', 'f86', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <th colspan="4">
                            <b>Poste variabili non sottoposte al limite</b>
                        </th>
                    </tr>
                    <?php if (self::checkOptionalValues('F54')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. b) CCNL 2018 (Piani di razionalizzazione)
                            </td>
                            <td><?php self::getInput('var331', 'R37', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F51')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 ISTAT
                            </td>
                            <td><?php self::getInput('var332', 'R39', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F52')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 AVVOCATURA
                            </td>
                            <td><?php self::getInput('var333', 'R40', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F285')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 FUNZIONI TECNICHE
                            </td>
                            <td><?php self::getInput('var334', 'R122', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F321')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 Compensi IMU e TARI
                            </td>
                            <td><?php self::getInput('var335', 'R147', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F88')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. c) CCNL 2018 Somme finanziate da fondi di derivazione dell'Unione Europea
                            </td>
                            <td><?php self::getInput('var336', 'R41', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F53')): ?>
                        <tr>
                            <td>
                                Altro - Art. 67 c. 3 lett. c) CCNL 2018
                            </td>
                            <td><?php self::getInput('var337', 'R111', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F380')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. a) CCNL 2018 – sponsorizzazioni
                            </td>
                            <td><?php self::getInput('var338', 'R42', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F381')): ?>
                        <tr>
                            <td>
                                Altre risorse
                            </td>
                            <td><?php self::getInput('var339', 'f91', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F49')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 1 CCNL 2018 - Risparmi Fondo Stabile Anno Precedente
                            </td>
                            <td><?php self::getInput('var340', 'R44', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F92')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 3 lett. e) CCNL 2018 - Risparmi Fondo Straordinario Anno Precedente
                            </td>
                            <td><?php self::getInput('var341', 'R45', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F90')): ?>
                        <tr>
                            <td>
                                Art. 67 c. 5 lett. b) CCNL 2018 - Quota incremento CDS maggior incasso rispetto anno
                                precedente
                            </td>
                            <td><?php self::getInput('var342', 'R152', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>

                    <tr>
                        <td>
                            <b>Totale risorse variabili</b>
                        </td>
                        <td><?php self::getInput('var343', 'f239', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <b>Decurtazioni del fondo</b>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            Decurtazione operate nel periodo 2011/2014 ai sensi dell'art. 9 C. 2 bis L.122/2010 secondo
                            periodo
                        </td>
                        <td><?php self::getInput('var344', 'F263', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Decurtazioni operate nel 2016 per cessazioni e rispetto limite 2015
                        </td>
                        <td><?php self::getInput('var345', 'F282', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Decurtazione per rispetto limite 2016
                        </td>
                        <td><?php self::getInput('var346', 'F284', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php if (self::checkOptionalValues('F241')): ?>
                        <tr>
                            <td>
                                Altre decurtazioni del fondo
                            </td>
                            <td><?php self::getInput('var347', 'F240', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td>
                            <b>Totale decurtazioni del fondo</b>
                        </td>
                        <td><?php self::getInput('var348', 'F280', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <b>Risorse del Fondo sottoposte a certificazione</b>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            Risorse fisse aventi carattere di certezza e stabilità
                        </td>
                        <td><?php self::getInput('var349', 'F242', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Risorse variabili
                        </td>
                        <td><?php self::getInput('var350', 'F239', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Altre decurtazioni
                        </td>
                        <td><?php self::getInput('var351', 'F280', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>Totale risorse Fondo sottoposte a certificazione</b>
                        </td>
                        <td><?php self::getInput('var352', 'F254', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="4">Tabella 2</th>

                    </tr>
                    <tr>
                        <th scope="col"><b>COSTITUZIONE DEL FONDO</b></th>
                        <th scope="col">
                            <b>Fondo <?php self::getInput('var353', 'anno', 'orange'); ?>(A)</b>
                        </th>
                        <th scope="col">
                            <b>
                                Fondo <?php self::getInput('var354', 'F13', 'orange'); ?>
                                (B)
                            </b>
                        </th>
                        <th scope="col"><b>Diff A-B</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th colspan="4"><b>Destinazioni non regolate in sede di contrattazione integrativa</b></th>
                    </tr>
                    <?php if (self::checkOptionalValues('F160')): ?>
                        <tr>

                            <td>Inquadramento ex Led</td>
                            <td><?php self::getInput('var355', 'R53', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F161')): ?>
                        <tr>

                            <td>Progressioni economiche STORICHE</td>
                            <td><?php self::getInput('var356', 'R54', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F162')): ?>
                        <tr>

                            <td>Indennità di comparto art.33 ccnl 22.01.04, quota a carico fondo</td>
                            <td><?php self::getInput('var357', 'R56', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F163')): ?>
                        <tr>

                            <td>
                                Indennità educatori asilo nido
                            </td>
                            <td><?php self::getInput('var358', 'R57', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F189')): ?>
                        <tr>

                            <td>ALTRI UTILIZZI</td>
                            <td><?php self::getInput('var359', 'F66', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <tr>

                        <td><b>Totale destinazioni non regolate in sede di contrattazione integrativa</b></td>
                        <td><?php self::getInput('var360', 'f93', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <th colspan="4">
                            <b>Destinazioni regolate in sede di contrattazione integrativa</b>
                        </th>
                    </tr>
                    <?php if (self::checkOptionalValues('F115')): ?>
                        <tr>

                            <td>Progressioni economiche specificatamente contratte nel CCDI dell'anno</td>
                            <td><?php self::getInput('var361', 'R55', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F125')): ?>
                        <tr>

                            <td>Indennità di turno</td>
                            <td><?php self::getInput('var362', 'R65', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F293')): ?>
                        <tr>

                            <td>
                                Indennità condizioni di lavoro Art. 70 bis CCNL 2018 (Maneggio valori, attività disagiate e
                                esposte a rischi)
                            </td>
                            <td><?php self::getInput('var363', 'R145', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F128')): ?>
                        <tr>

                            <td>
                                Reperibilità
                            </td>
                            <td><?php self::getInput('var364', 'R71', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F132')): ?>
                        <tr>

                            <td>
                                Indennità Specifiche Responsabilità art. 70 quinquies c. 1 CCNL 2018 (ex art. 17 lett. f)
                            </td>
                            <td><?php self::getInput('var365', 'R75', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F133')): ?>
                        <tr>

                            <td>
                                Indennità Specifiche Responsabilità art. 70 quinquies c. 1 CCNL 2018 (ex art. 17 lett. i)

                            </td>
                            <td><?php self::getInput('var366', 'R77', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F134')): ?>
                        <tr>

                            <td>
                                Indennità particolare compenso incentivante (personale Unioni dei comuni)
                            </td>
                            <td><?php self::getInput('var367', 'R79', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F135')): ?>
                        <tr>

                            <td>
                                Indennità centri estivi asili nido art 31 comma 5 CCNL 14 -9- 2000 code
                            </td>
                            <td><?php self::getInput('var368', 'R81', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F136')): ?>
                        <tr>

                            <td>
                                Compenso previsto dall'art.24, comma 1 CCNL 14.9.2000, per il personale che presta attività
                                lavorativa nel giorno destinato al riposo settimanale
                            </td>
                            <td><?php self::getInput('var369', 'R83', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F137')): ?>
                        <tr>

                            <td>
                                Premi collegati alla performance organizzativa – art. 68 c. 2 lett. a) CCNL 2018
                            </td>
                            <td><?php self::getInput('var370', 'R85', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F138')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance individuale - art. 68 c. 2 lett. b) CCNL 2018 contrattate
                                nel CCDI dell'anno
                            </td>
                            <td><?php self::getInput('var371', 'R87', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F186')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance organizzativa - Obiettivi finanziati con risorse Art. 67
                                c. 5 lett. b) CCNL 2018
                            </td>
                            <td><?php self::getInput('var372', 'R88', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F296')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance organizzativa -Obiettivi finanziati da risorse art 67 c. 5
                                lett. b) per potenziamento dei servizi di controllo finalizzati alla sicurezza urbana e
                                stradale Art. 56 QUATER CCNL 2018
                            </td>
                            <td><?php self::getInput('var373', 'R136', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F187')): ?>
                        <tr>
                            <td>
                                Compensi 50% economie da Piani di Razionalizzazione - Art. 67 c. 3 lett. b) CCNL 2018-Art.
                                16 C. 5 L. 111/2011
                            </td>
                            <td><?php self::getInput('var374', 'R110', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F294')): ?>
                        <tr>
                            <td>
                                Indennità di servizio esterno – art. 56 quinquies CCNL 2018 (Vigilanza)
                            </td>
                            <td><?php self::getInput('var375', 'R124', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F295')): ?>
                        <tr>
                            <td>
                                Indennità di funzione – Art. 56 sexies CCNL 2018 (Vigilanza)
                            </td>
                            <td><?php self::getInput('var376', 'R125', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F192')): ?>
                        <tr>
                            <td>
                                ALTRE indennità contrattate nel CCDI dell'anno trasferito
                            </td>
                            <td><?php self::getInput('var377', 'f68', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F188')): ?>
                        <tr>
                            <td>
                                Premi collegati alla performance organizzativa – Compensi per sponsorizzazioni Art. 67 c. 3
                                lett. a) CCNL 2018
                            </td>
                            <td><?php self::getInput('var378', 'R91', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F167')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                FUNZIONI TECNICHE

                            </td>
                            <td><?php self::getInput('var379', 'R92', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F322')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018 - Compensi IMU e TARI
                            </td>
                            <td><?php self::getInput('var380', 'R149', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F168')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. h CCNL 2018 - Compensi per notifiche
                            </td>
                            <td><?php self::getInput('var381', 'R93', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F169')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                RIF – ISTAT

                            </td>
                            <td><?php self::getInput('var382', 'R94', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F170')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                RIF - ICI

                            </td>
                            <td><?php self::getInput('var383', 'R95', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F171')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                RIF - avvocatura

                            </td>
                            <td><?php self::getInput('var384', 'R96', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F172')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                RIF - Diritto soggiorno Unione Europea D.lgs 30/2007

                            </td>
                            <td><?php self::getInput('var385', 'R108', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F173')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                Legge Regionale specifica

                            </td>
                            <td><?php self::getInput('var386', 'R199', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F190')): ?>
                        <tr>
                            <td>
                                Art. 68 c. 2 lett. g) CCNL 2018
                                RIF - Legge o ALTRO

                            </td>
                            <td><?php self::getInput('var387', 'f60', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <?php if (self::checkOptionalValues('F272')): ?>
                        <tr>
                            <td>
                                Quota recupero somme (Art. 4 DL 16/2014 Salva Roma Ter)
                            </td>
                            <td><?php self::getInput('var388', 'R119', 'orange'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td>
                            <b>Totale destinazioni regolate in sede di contrattazione integrativa</b>
                        </td>
                        <td><?php self::getInput('var389', 'f243', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <b>(eventuali) destinazioni da regolare</b>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            Risorse ancora da contrattare
                        </td>
                        <td><?php self::getInput('var390', 'f78', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>Totale (eventuali) destinazioni ancora da regolare</b>
                        </td>
                        <td><?php self::getInput('var391', 'f78', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="4">
                            <b>Destinazioni fondno sottoposte a certificazione</b>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            Destinazioni non regolate in sede di contrattazione integrativa
                        </td>
                        <td><?php self::getInput('var392', 'f93', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>
                            Destinazioni regolate in sede di contrattazione integrativa
                        </td>
                        <td><?php self::getInput('var393', 'f243', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            (eventuali) destinazioni ancora da regolare
                        </td>
                        <td><?php self::getInput('var394', 'f78', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>Totale destinazioni Fondo sottoposte a certificazione</b>
                        </td>
                        <td><?php self::getInput('var395', 'f254', 'orange'); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <br />
                <br />
                <h4>
                    Modulo IV - Compatibilità economico-finanziaria e modalità di copertura degli oneri del Fondo con
                    riferimento agli strumenti annuali e pluriennali di bilancio
                </h4>
                <h5>
                    Sezione I - Esposizione finalizzata alla verifica che gli strumenti della contabilità
                    economico-finanziaria dell’Amministrazione presidiano correttamente i limiti di spesa del Fondo nella
                    fase programmatoria della gestione
                </h5>
                <br />
                Per ciascun argomento si evidenzia quanto segue:
                <ul class="a">
                    <li>
                        <b>Rispetto dei vincoli di bilancio: </b>’ammontare delle risorse per le quali si contratta la
                        destinazione
                        trovano copertura negli stanziamenti del bilancio
                        anno <?php self::getInput('var396', 'anno', 'orange'); ?>;
                        <br />
                    </li>
                    <li>
                        <b>Rispetto dei vincoli derivanti dalla legge e dal contratto nazionale.</b> Le fonti di
                        alimentazione del
                        fondo sono previste dal contratto nazionale e la loro quantificazione è elaborata sulla base delle
                        disposizioni stesse (Vedi Modulo I). La destinazione comprende esclusivamente istituti espressamente
                        devoluti dalla contrattazione nazionale a quella decentrata (Vedi Modulo II)
                        <br />
                    </li>
                    <li>
                        <b>Imputazione nel Bilancio:</b> La destinazione del fondo disciplinata dall’ipotesi di accordo in
                        oggetto
                        trova finanziamento nel bilancio di
                        previsione <?php self::getInput('var397', 'anno', 'orange'); ?> come segue:
                        <ul class="c">
                            <li>
                                le voci di utilizzo fisse (Indennità di comparto e progressioni orizzontali già in atto)
                                saranno
                                imputate ai capitoli/interventi di spesa previsti in bilancio per ciascun dipendente;
                            </li>
                            <li>
                                la restante parte di utilizzo oggetto di contrattazione (fondo generale e indennità
                                individuali) sarà
                                imputata all’intervento <?php self::getInput('var398', '____', 'orange'); ?> del
                                bilancio <?php self::getInput('var399', 'anno', 'orange'); ?> gestione competenza.
                            </li>
                            <li>
                                le voci relative agli incentivi di cui all’art. 113 del D. Lgs 50/2016 saranno iscritte
                                negli
                                stanziamenti dei diversi interventi a cui si riferiscono;
                                <br />
                                <br />
                                <?php if (self::checkOptionalValues('F102')): ?>
                                    Si attesta che la spesa del personale per l'anno 2008 era pari ad
                                    € <?php self::getInput('var400', 'xxxx', 'orange'); ?>
                                <?php endif; ?>
                                <br />
                                <br />
                                <?php if (self::checkOptionalValues('F101')): ?>
                                    Si attesta che la spesa del personale per la media del triennio 2011-2013 era pari ad
                                    € <?php self::getInput('var401', 'xxx', 'orange'); ?>
                                <?php endif; ?>
                                <br />
                                Si attesta che la spesa del personale per
                                l'anno <?php self::getInput('var402', 'anno', 'orange'); ?> è pari ad
                                € <?php self::getInput('var403', 'xxx', 'orange'); ?>
                                <br />
                                Si attesta, pertanto, che sono stati rispettati i limiti dei parametri di virtuosità fissati
                                per la
                                spesa di personale dalle attuali norme vigenti.
                            </li>
                        </ul>
                        <br />
                    </li>
                </ul>
                <h5>
                    Sezione II -Esposizione finalizzata alla verifica a consuntivo che il limite di spesa del Fondo
                    dell'anno precedente risulta rispettato
                </h5>
                La costituzione del fondo per l'anno <?php self::getInput('var404', 'anno', 'orange'); ?>
                , così come previsto dal D.Lgs. 75/2017 non risulta superare
                l'importo determinato per l'anno 2016.
                <br />
                <br />
                Si precisa, inoltre, che il fondo dell'anno precedente risultava pari a
                € <?php self::getInput('var405', '____', 'orange'); ?> mentre per
                l'anno <?php self::getInput('var406', 'anno', 'orange'); ?>
                è pari ad € <?php self::getInput('var407', 'f253', 'orange'); ?>.
                <br />
                <br />
                In seguito all’introduzione delle disposizioni dell’art. 33 comma 2, del D.L.34/2019, convertito in
                Legge 58/2019 (c.d. Decreto “Crescita”), il tetto al salario accessorio, così come introdotto
                dall'articolo 23, comma 2, del D.Lgs 75/2017, può essere modificato. La modalità di applicazione
                definita nel DPCM del 17.3.2020, pubblicato in GU in data 27.4.2020, concordata in sede di Conferenza
                Unificata Stato Regioni del 11.12.2019, prevede che il limite del salario accessorio, a partire dal 20
                aprile 2020, debba essere adeguato in aumento rispetto al valore medio procapite del 2018 in caso di
                incremento del numero di dipendenti presenti
                nel <?php self::getInput('var408', 'anno', 'orange'); ?>, rispetto ai presenti al
                31.12.2018, al fine di
                garantire l’invarianza della quota media procapite rispetto al 2018. Tale incremento va calcolato in
                base alle modalità fornite dalla Ragioneria dello Stato da ultimo con nota Prot. 12454 del 15.1.2021.
                <br />
                <br />
                Si precisa che in questo Ente:
                <ul class="d">
                    <li>
                        il numero di dipendenti in servizio
                        nel <?php self::getInput('var409', 'anno', 'orange'); ?> calcolato in base alle modalità
                        fornite dalla Ragioneria
                        dello Stato da ultimo con nota Prot. 12454 del 15.1.2021, pari
                        a <?php self::getInput('var410', 'R162', 'orange'); ?> è inferiore o uguale al numero dei
                        dipendenti in servizio al 31.12.2018 pari
                        a <?php self::getInput('var411', 'R161', 'orange'); ?>, pertanto, in
                        attuazione dell’art. 33 c. 2 D.L. 34/2019
                        convertito nella L. 58/2019, il fondo e il limite di cui all’art. 23 c.2 D.Lgs. 75/2017 non deve
                        essere
                        adeguato in aumento al fine di garantire il valore medio pro-capite riferito al 2018
                    </li>
                    <li>
                        il numero di dipendenti in servizio
                        nel <?php self::getInput('var412', 'anno', 'orange'); ?> calcolato in base alle modalità
                        fornite dalla Ragioneria
                        dello Stato da ultimo con nota Prot. 12454 del 15.1.2021, pari
                        a <?php self::getInput('var413', 'R162', 'orange'); ?> è superiore al numero dei
                        dipendenti in servizio al 31.12.2018 pari
                        a <?php self::getInput('var414', 'R161', 'orange'); ?>, pertanto, in
                        attuazione dell’art. 33 c. 2 D.L. 34/2019
                        convertito nella L. 58/2019, il fondo risorse decentrate e il relativo limite di cui all’art. 23 c.
                        2
                        D.Lgs. 75/2017 deve essere adeguato in aumento al fine di garantire il valore medio pro-capite
                        riferito
                        al 2018, per un importo pari ad € <?php self::getInput('var415', 'R150', 'orange'); ?>;
                    </li>
                    <li>
                        <?php self::getTextArea('area39', '
                        l’Ente si impegna a modificare la costituzione del fondo nel caso di incremento o diminuzione del
                        numero di dipendenti in servizio rispetto al 31.12.2018 e comunque a rideterminare (anche in
                        diminuzione) il salario accessorio complessivo in caso di sopraggiunte modifiche normative, chiarimenti
                        ministeriali, interventi giurisprudenziali, sentenze o pareri di Corte dei Conti sulle modalità di
                        calcolo di tale integrazione;', 'red'); ?>

                    </li>
                </ul>
                Si precisa che i valori esposti equivalgono al totale del fondo dell’anno al netto della eventuale
                decurtazione del limite dell’anno 2016. Tali valori non includono avvocatura, ISTAT, di cui art. 67
                comma 3 lett. c CCNL 21.5.2018, importi di cui all’art. 67 comma 3 lett. c CCNL
                21.5.2018, <?php self::getTextArea('area40', ' importi di
                cui all’67 comma 3 lett. a, ove tale attività non risulti ordinariamente resa dall’Amministrazione
                precedentemente l’entrata in vigore del D.Lgs 75/2017, importi di cui all’art. 67 comma 2 lett.b,', 'orange'); ?>
                economie del fondo dell’anno precedente e economie del fondo straordinario anno precedente.
                <br />
                <br />

                Viene ulteriormente specificato che il limite di cui all’art. 23 c. 2 del Dl. Lgs 75/2017 deve essere
                rispettato per l’Amministrazione nel suo complesso, in luogo che distintamente per le diverse categorie
                di personale (es. dirigente e non dirigente) che operano nell’amministrazione, così come chiarito da
                diverse ma costanti indicazioni di sezioni regionali della Corte dei Conti e dal MEF e RGS;
                <br />
                <br />
                <?php if (self::checkOptionalValues('F344')): ?>
                    <ul class="d">
                        <li>
                            l'Ente si è avvalso della facoltà prevista dall'art. 11-bis comma 2 D.L. 135/2018, che prevede
                            di
                            utilizzare le facoltà assunzionali per incrementare il fondo delle PO;
                        </li>
                    </ul>
                <?php endif; ?>
                <br />
                <br />
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"><b>Anno 2016</b></th>
                        <th scope="col"><b>Anno <?php self::getInput('var416', 'anno', 'orange'); ?></b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Fondo complessivo risorse decentrate soggette al limite</td>
                        <td><?php self::getInput('var417', 'f370', 'orange'); ?></td>
                        <td><?php self::getInput('var418', 'f253', 'orange'); ?></td>
                    </tr>
                    <?php if (self::checkOptionalValues('F376')): ?>
                        <tr>
                            <td>Indennità di Posizione e risultato PO</td>
                            <td><?php self::getInput('var418', 'R138', 'orange'); ?></td>
                            <td><?php self::getInput('var419', 'R141', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (self::checkOptionalValues('F375')): ?>
                        <tr>
                            <td>
                                Indennità di Posizione e risultato PO anno corrente COMPRESO Quota integrazione PO
                                finanziate dalla rinuncia delle capacità assunzionali (Incremento Art. 11-bis comma 2 D.L.
                                135/2018) e Quota art. 33 del DL 34/2019
                            </td>
                            <td><?php self::getInput('var420', 'R157', 'orange'); ?></td>
                            <td><?php self::getInput('var421', 'R99', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Fondo Straordinario 2016</td>
                        <td><?php self::getInput('var422', 'R139', 'orange'); ?></td>
                        <td><?php self::getInput('var423', 'R142', 'orange'); ?></td>
                    </tr>
                    <?php if (self::checkOptionalValues('F310')): ?>
                        <tr>
                            <td>Indennità di Posizione e risultato DIRIGENTI</td>
                            <td><?php self::getInput('var424', 'R153', 'orange'); ?></td>
                            <td><?php self::getInput('var425', '', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>
                            Quota di incremento valore medio procapite del trattamento accessorio rispetto al 2018 -
                            Art. 33 c. 2 DL 34/2019- aumento virtuale limite 2016
                        </td>
                        <td><?php self::getInput('var426', 'F354', 'orange'); ?></td>
                        <td><?php self::getInput('var427', 'F355', 'orange'); ?></td>
                    </tr>
                    <?php if (self::checkOptionalValues('F375')): ?>
                        <tr>
                            <td>
                                <b>TOTALE TRATTAMENTO ACCESSORIO SOGGETTO AL LIMITE ART. 23 C. 2 D.LGS 75/2017</b>
                            </td>
                            <td><?php self::getInput('var428', 'F354', 'orange'); ?></td>
                            <td><?php self::getInput('var429', 'F355', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (self::checkOptionalValues('F375')): ?>
                        <tr>
                            <td>
                                <b>
                                    TOTALE TRATTAMENTO ACCESSORIO SOGGETTO AL LIMITE ART. 23 C. 2 D.LGS 75/2017 COMPRESO
                                    Quota
                                    integrazione PO finanziate dalla rinuncia delle capacità assunzionali (Incremento Art.
                                    11-bis comma 2 D.L. 135/2018) e Quota art. 33 del DL 34/2019
                                </b>
                            </td>
                            <td><?php self::getInput('var430', 'F354', 'orange'); ?></td>
                            <td><?php self::getInput('var431', 'F355', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (self::checkOptionalValues('F344')): ?>
                        <tr>
                            <td>
                                <b>
                                    Quota integrazione PO finanziate dalla rinuncia delle capacità assunzionali (Incremento
                                    Art.
                                    11-bis comma 2 D.L. 135/2018)
                                </b>
                            </td>
                            <td><?php self::getInput('var432', '', 'orange'); ?></td>
                            <td><?php self::getInput('var433', 'F358', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (self::checkOptionalValues('F376')): ?>
                        <tr>
                            <td>RISPETTO DEL LIMITE TRATTAMENTO ACCESSORIO</td>
                            <td><?php self::getInput('var434', '', 'orange'); ?></td>
                            <td><?php self::getInput('var435', 'F360', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (self::checkOptionalValues('F375')): ?>
                        <tr>
                            <td>
                                <b>
                                    RISPETTO DEL LIMITE TRATTAMENTO ACCESSORIO COMPRESO Quota integrazione PO finanziate
                                    dalla
                                    rinuncia delle capacità assunzionali (Incremento Art. 11-bis comma 2 D.L. 135/2018) e
                                    Quota
                                    art. 33 del DL 34/2019
                                </b>
                            </td>
                            <td><?php self::getInput('var436', '', 'orange'); ?></td>
                            <td><?php self::getInput('var437', '', 'orange'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <br />
                Per quanto riguarda la spesa, esaminata la parte di utilizzo oggetto della contrattazione, si evidenzia
                che a consuntivo risulta rispettato il limite di spesa del Fondo, pertanto l’ente risulta nella presente
                condizione:
                <b>Scegliere tra caso A e caso B</b>
                <br />
                <?php self::getTextArea('area41', 'CASO A
                Dal prospetto relativo alla spesa determinata a consuntivo, le risorse risultano utilizzate integralmente, pertanto non si sono realizzate economie.
                Tali risorse sono al netto delle voci esterne al Fondo (Incentivo per Funzioni Tecniche Art. 113 D.Lgs. 50/2016 e compresi ISTAT e altro), poiché gli eventuali residui che si dovessero creare, relativi a tali incrementi, non costituiscono economie da rinviare all anno successivo, bensì economia di bilancio.', 'orange'); ?>
                <br />
                <br />
                <?php self::getTextArea('area42', 'Oppure CASO B
                Dal prospetto relativo alla spesa, a consuntivo, le risorse non risultano utilizzate integralmente, realizzando delle economie da destinare ad incremento, ai sensi dell art . 68 c.1 del CCNL 21.5.2018, del fondo dell anno successivo a titolo di risorsa variabile. Tali risorse sono al netto delle voci variabili e delle risorse esterne al Fondo (Incentivo per Funzioni tecniche, Art. 113 D.Lgs. 50 2016 e compresi ISTAT e altro), poiché gli eventuali residui che si dovessero creare, relativi a tali incrementi, non costituiscono economie da rinviare all anno successivo, bensì economia di bilancio.', 'orange'); ?>
                <br />
                <h5>
                    Sezione III – Verifica delle disponibilità finanziarie dell'Amministrazione ai fini della copertura
                    delle diverse voci di destinazione del Fondo
                </h5>
                <br />
                Si rappresenta che, in ossequio ai disposti di cui all'art. 48, comma 4, ultimo periodo, del D.Lgs.
                n.165/2001, l'Ente ha autorizzato, con distinta indicazione dei mezzi di copertura, le spese relative al
                contratto collettivo decentrato integrativo – parte economica
                anno <?php self::getInput('var438', 'anno', 'orange'); ?>, attraverso le procedure di
                approvazione del bilancio di previsione
                dell'esercizio <?php self::getInput('var439', 'anno', 'orange'); ?>. La spesa derivante
                dalla contrattazione
                decentrata trova copertura sulla disponibilità delle pertinenti risorse previste nel bilancio di
                previsione <?php self::getInput('var440', 'anno', 'orange'); ?>, approvato con
                deliberazione consiliare
                n. <?php self::getInput('var441', 'numero_delibera_approvazione_bilancio', 'orange'); ?> del
                <?php self::getInput('var442', 'data_delibera_approvazione_bilancio', 'orange'); ?> esecutiva.
                <br />
                <br />
                L’Ente non versa in condizioni deficitarie.
                <br />
                <br />
                La costituzione del fondo per le risorse decentrate risulta compatibile con i vincoli in tema di
                contenimento della spesa del personale.
                <br />
                <br />
                Il totale del fondo come da determinazione
                n. <?php self::getInput('var443', 'numero_determina_apporvazione', 'orange'); ?> del
                <?php self::getInput('var444', 'data_determina_apporvazione', 'orange'); ?> è impegnato al
                capitolo <?php self::getInput('var445', 'xx/xx/xx', 'orange'); ?> del
                bilancio <?php self::getInput('var446', 'anno', 'orange'); ?> e precisamente agli
                impegni n. <?php self::getInput('var447', 'xxx-xx-xx', 'orange'); ?>.
                <br />
                <br />
                Con riferimento al fondo per il lavoro straordinario di cui all’art. 14 comma 1 CCNL 1/4/1999, si dà
                atto che la somma stanziata rimane fissata, come dall’anno 2000, nell’importo di
                €<?php self::getInput('var448', 'R99', 'orange'); ?>.
                <br />
                <br />
                Specificare inoltre:
                <br />
                <br />
                - <?php self::getTextArea('area43', 'nel caso (prevalente) in cui il fondo ed il relativo capitolo di spesa (o voce di costo del conto economico) siano stati costituiti al netto degli oneri riflessi, la relazione tecnica, dovrà dare conto della capienza delle voci di bilancio che finanziano detti oneri (contributi previdenziali ed assistenziali ed IRAP);', 'red'); ?>
                <br />
                - <?php self::getTextArea('area44', 'nel caso di utilizzo di personale con tipologia di lavoro flessibile e/o di personale comandato, le risorse da destinare a tale personale, a titolo di retribuzioni accessorie finanziate dalla contrattazione integrativa, debbono trovare capienza nel medesimo fondo unico; SPECIFICARE SE CI SONO O MENO ALTRE TIPOLOGIE DI PERSONALE E DOVE ATTINGONO EVENTUALMENTE LE RISORSE DESTINATE AL TRATTAMENTO ACCESSORIO, SE NON VI SONO RISORSE O PERSONALE CANCELLARE O SPECIFICARE CHE NON SONO DISTRIBUITE ALTRE RISORSE A TITOLO ACCESSORIO;', 'red'); ?>
                <br />
                <br />
                - <?php self::getTextArea('area45', 'Determinare una media di risorse pro-capite. Es. Nell’ente sono presenti n. xx dipendenti. Una media pro-capite di risorse è pari ad € xxxx, come determinato in sede di verifica dell’applicazione dell’art. 33 del DL 34/2019.', 'red'); ?>
                <br />
                <br />
                - <?php self::getTextArea('area46', 'la relazione tecnico-finanziaria dovrà infine dimostrare la copertura di tutti i costi diretti ed attestare l’inesistenza di costi indiretti; in presenza di costi indiretti (es. quando la contrattazione integrativa incida anche su altre categorie di personale che, seppur non ricomprese tra i diretti destinatari dei fondi, possono risultare beneficiarie in tutto o in parte del contratto integrativo), occorrerà provvedere alla quantificazione di tali costi ed alla dimostrazione della relativa copertura con risorse già allocate in bilancio ancorché diverse da quelle che finanziano i fondi. In sostanza occorre dimostrare che dall’accordo integrativo non derivino nuovi o maggiori oneri privi della prescritta copertura', 'red'); ?>

                <br />
                <br />
                Il Presidente della Delegazione trattante di parte
                pubblica <?php self::getInput('var449', '____________', 'black'); ?>
                <br />
                <br />
                Per la parte relativa allo schema di relazione tecnico – finanziaria
                <br />
                <br />
                Il responsabile <?php self::getInput('var450', '______________________________________', 'black'); ?>
            </div>
        </div>
        </body>


        <div class="d-flex justify-content-end">
            <button class="btn btn-outline-secondary btn-export" onclick="exportHTML();">Esporta in word
            </button>

        </div>

        </html lang="en">


        <?php
    }

}