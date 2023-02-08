<?php

namespace dateXFondoPlugin;

use DocumentRepository;

class DeliberaIndirizziDocument
{
    private $infos = [];
    private $user_infos = [];
    private $formule = [];
    private $articoli = [];
    private $values = array();
    private $formulas = array();
    private $articles = array();

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

    private function checkOptionalValues($default)
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
        $value = isset($this->values[$key]) ? $this->values[$key] : $default;

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
            <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/main.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/templateheader.css">
            <script>
                let data = {};


                function exportHTML() {
                    const header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
                        "xmlns:w='urn:schemas-microsoft-com:office:word' " +
                        "xmlns='http://www.w3.org/TR/REC-html40'>" +
                        "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title><style type='text/css'>.div_Delibera ul.n { position:relative; z-index: 10; top: 0; left: 0; margin-top: 3px; list-style-type: decimal; } .div_Delibera ul.n li { position:relative; z-index: 10; top: 0; left: 0; width: 100%; height: auto; margin: 0;padding: 0;font-family: 'sans-serif';font-size: 11pt;text-align: justify;} </style></head><body>";
                    const footer = "</body></html>";
                    const bodyHTML = $("#DeliberaIndirizziDocument").clone(true);
                    bodyHTML.find('input,textarea').remove();
                    let formula_value = bodyHTML.find('select').val();
                    bodyHTML.find('select').replaceWith(formula_value);

                    const sourceHTML = header + bodyHTML.html() + footer;
                    const source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
                    const fileDownload = document.createElement("a");
                    document.body.appendChild(fileDownload);
                    fileDownload.href = source;
                    const currentdate = new Date();
                    fileDownload.download = 'deliberaIndirizzi' + "_" + currentdate.getDate() + "-"
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

        </head>

        <body>
        <div class="d-flex justify-content-end">
            <button class="btn btn-outline-secondary btn-export" onclick="exportHTML();">Esporta in word
            </button>

        </div>




        <div id="DeliberaIndirizziDocument" class="div_Delibera">
            <style id="styleDoc">

                .div_Delibera {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: serif;
                    font-size: 11pt;
                    text-align: justify;
                    display: block;
                }

                .div_Delibera h2 {
                    font-size: 15pt;
                    margin: 30px 0 10px;
                    font-weight: 700;
                    letter-spacing: 3px;
                }

                .div_Delibera h3 {
                    font-size: 13pt;
                    margin: 30px 0 20px;
                    font-weight: 500;
                }

                .div_Delibera h4 {
                    font-size: 15pt;
                    margin: 30px 0 20px;
                    font-weight: 700;
                    letter-spacing: 2px;
                    text-align: center;
                }


                .div_Delibera ul.d {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: disc;
                }

                .div_Delibera ul.d li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: sans-serif;
                    font-size: 11pt;
                    text-align: justify;
                }

                .div_Delibera ul.a {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: lower-alpha;
                }

                .div_Delibera ul.a li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: sans-serif;
                    font-size: 11pt;
                    text-align: justify;
                }

                .div_Delibera ul.a li ul.c {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: circle;
                }

                .div_Delibera ul.a li ul.c li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: sans-serif;
                    font-size: 11pt;
                    text-align: justify;
                }


                .div_Delibera ul.n {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    margin-top: 3px;
                    list-style-type: decimal;
                }

                .div_Delibera ul.n li {
                    position: relative;
                    z-index: 10;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    font-family: sans-serif;
                    font-size: 11pt;
                    text-align: justify;
                }
            </style>

            <h2><?php self::getInput('var1', 'Il/La', 'orange'); ?><?php self::getInput('var2', 'Il/La nome_soggetto_deliberante', 'orange'); ?>
                </h2>
            <h3><b>OGGETTO: PERSONALE NON DIRIGENTE. FONDO RISORSE DECENTRATE PER
                    L’ANNO <?php self::getInput('var3', 'anno', 'orange'); ?>. INDIRIZZI PER LA COSTITUZIONE PARTE
                    VARIABILE.
                    DIRETTIVE PER LA CONTRATTAZIONE DECENTRATA INTEGRATIVA.</b></h3>
            <b> Visti:</b>
            <br/>
            <ul class="d">
                <li>la deliberazione
                    di <?php self::getInput('var4', 'della/del', 'orange'); ?>   <?php self::getInput('var5', 'Consiglio Comunale/Assemblea', 'orange'); ?>
                    n. <?php self::getInput('var6', 'numero_delibera_approvazione_bilancio', 'orange'); ?> del
                    <?php self::getInput('var7', 'data_delibera_approvazione_bilancio', 'orange'); ?>, esecutiva,
                    relativa a:
                    "<?php self::getInput('var8', 'Bilancio di previsione anno, bilancio pluriennale e DUP/PEG anno, piano di investimenti – approvazione”', 'orange'); ?>
                    "
                    ;
                </li>
                <li>la
                    deliberazione <?php self::getInput('var11', ' della/del', 'orange'); ?>  <?php self::getInput('var12', 'nome_soggetto_deliberante', 'orange'); ?>
                    n.<?php self::getInput('var13', 'numero_delibera_approvazione_PEG', 'orange'); ?>
                    del <?php self::getInput('var13', 'data_delibera_approvazione_PEG', 'orange'); ?>
                    , esecutiva, relativa all’approvazione del Piano esecutivo di
                    Gestione <?php self::getInput('var14', 'anno', 'orange'); ?>
                    unitamente al Piano della Performance;
                </li>
                <li>i successivi atti di variazione del bilancio del comune e del P.E.G./Piano Performance;</li>
                <li>il vigente Regolamento di Organizzazione degli Uffici e dei Servizi;</li>
                <li>la
                    deliberazione <?php self::getInput('var15', 'della/del', 'orange'); ?> <?php self::getInput('var16', 'nome_soggetto_deliberante', 'orange'); ?>
                    n.<?php self::getInput('var17', 'numero_delibera_nomina', 'orange'); ?>
                    del <?php self::getInput('var18', 'data_delibera_nomina', 'orange'); ?> di
                    nomina della delegazione trattante di parte pubblica abilitata alla contrattazione collettiva
                    decentrata
                    integrativa per il personale dipendente;
                </li>
            </ul>

            <b>Richiamati:</b>
            <br/>
            <ul class="d">
                <li>l’art. 48, comma 2 del D.Lgs. n. 267/2000;</li>
                <li>l’art. 59, comma 1, lettera p del D.Lgs. n. 446/1997;</li>
                <li>gli artt. 40, comma 3 e 40-bis del D. Lgs. n. 165/2001;</li>
                <li>gli artt. 18, 19 e 31 del D.Lgs. 150/2009;</li>
                <li> il CCNL siglato in data 21.5.2018, in particolare gli artt. 67, 68, 70, 56 quinquies e 56 sexies
                    del
                    C.C.N.L. 21.5.2018 e successive modifiche ed integrazioni;
                </li>
                <li>i CCNL 31.3.1999, 1.4.1999, 14.9.2000, 5.10.2001, 22.1.2004, 9.5.2006, 11.4.2008 e 31.07.2009;</li>
            </ul>


            <b>Premesso che </b>in data 21.5.2018 è stato sottoscritto il Contratto Collettivo Nazionale di Lavoro del
            personale del comparto Regioni-Autonomie Locali per il triennio 2016-2018 e che il suddetto CCNL stabilisce
            all'art. 67, che le risorse finanziarie destinate alla incentivazione delle politiche di sviluppo delle
            risorse umane e della produttività vengano determinate annualmente dagli Enti, secondo le modalità definite
            da tale articolo e individua le risorse aventi carattere di certezza, stabilità e continuità nonché le
            risorse aventi caratteristiche di eventualità e di variabilità, individuando le disposizioni contrattuali
            previgenti dalla cui applicazione deriva la corretta costituzione del fondo per il salario accessorio;
            <br/>
            <br/>
            <b>Visto</b> l’art. 67 comma 8 e seguenti della legge n. 133/2008 per il quale gli Enti Locali sono tenuti a
            inviare entro il 31 maggio di ogni anno alla Corte dei Conti le informazioni relative alla contrattazione
            decentrata integrativa, certificati dagli organi di controllo interno;
            <br/>
            <br/>
            <b>Dato atto che:</b>
            <br/>
            <ul class="d">
                <li>la dichiarazione congiunta n. 2 del C.C.N.L. del 22.1.2004 prevede che tutti gli adempimenti
                    attuativi della
                    disciplina dei contratti di lavoro sono riconducibili alla più ampia nozione di attività di gestione
                    delle
                    risorse umane, affidate alla competenza dei dirigenti e dei responsabili dei servizi che vi
                    provvedono
                    mediante l’adozione di atti di diritto comune, con la capacità ed i poteri del privato datore di
                    lavoro e
                    individua il responsabile del settore personale quale soggetto competente a costituire con propria
                    determinazione il fondo di alimentazione del salario accessorio secondo i principi indicati dal
                    contratto di
                    lavoro;
                </li>
            </ul>

            <b>Vista</b> la Legge n. 15/2009 e il D.Lgs. n. 150/2009 “Attuazione della legge n. 15/2009, in materia di
            ottimizzazione della produttività del lavoro pubblico e di efficienza e trasparenza delle pubbliche
            amministrazioni”;
            <br/>
            <br/>
            <b>Visto</b> il D.Lgs. n. 165/2001 “Norme generali sull’ordinamento del lavoro alle dipendenze delle
            Amministrazioni pubbliche”, con particolare riferimento alle modifiche apportate dal sopracitato D.Lgs. n.
            150/2009, e art. 40 “Contratti collettivi nazionali ed integrativi” e art. 40bis “Controlli in materia di
            contrattazione integrativa”;

            <br/>
            <br/>
            <b>Considerato che</b> il D.L. 78/2010, convertito con modificazioni nella legge n. 122/2010 e ssmmii, ha
            previsto
            per le annualità 2011/2014 limitazioni in materia di spesa per il personale e in particolare l'art. 9 comma
            2 bis disponeva:
            <ul class="d">
                <li>che l'ammontare complessivo delle risorse destinate annualmente al trattamento accessorio del
                    personale,
                    anche a livello dirigenziale, non può superare il corrispondente importo dell’anno 2010;
                </li>
                <li>che l'ammontare complessivo delle risorse destinate annualmente al trattamento accessorio del
                    personale è,
                    automaticamente ridotto in misura proporzionale alla riduzione del personale in servizio;
                </li>
            </ul>

            <b>Vista</b> la Legge n. 147/2013 nota Legge di Stabilità 2014, che all'art. 1, comma 456, secondo periodo,
            inserisce all'art. 9 comma 2 bis del DL 78/2010 un nuovo periodo in cui: <i>«A decorrere dal 1º gennaio
                2015,
                le risorse destinate annualmente al trattamento economico accessorio sono decurtate di un importo pari
                alle
                riduzioni operate per effetto del precedente periodo»</i>, stabilendo così che le decurtazioni operate
            per gli
            anni 2011/2014 siano confermate e storicizzate nei fondi per gli anni successivi a partire dall'anno 2015.
            <br/>
            <br/>
            <b>Visto</b> l'art. 1 c. 236 della L. 208/2015 (Legge di stabilità 2016) che stabiliva <i>“Nelle more
                dell’adozione
                dei decreti legislativi attuativi degli articoli 11 e 17 della legge 7 agosto 2015, n. 124, con
                particolare
                riferimento all’omogeneizzazione del trattamento economico fondamentale e accessorio della dirigenza,
                tenuto
                conto delle esigenze di finanza pubblica, a decorrere dal 1° gennaio 2016 l’ammontare complessivo delle
                risorse destinate annualmente al trattamento accessorio del personale, anche di livello dirigenziale,
                [...],
                non può superare il corrispondente importo determinato per l’anno 2015 ed è, comunque, automaticamente
                ridotto in misura proporzionale alla riduzione del personale in servizio, tenendo conto del personale
                assumibile ai sensi della normativa vigente.</i>
            <br/>
            <br/>
            <b>Visto</b> l'art. 23 del D.Lgs. 75/2017 il quale stabilisce che <i>“a decorrere dal 1° gennaio 2017,
                l'ammontare
                complessivo delle risorse destinate annualmente al trattamento accessorio del personale, anche di
                livello
                dirigenziale, di ciascuna delle amministrazioni pubbliche di cui all'articolo 1,comma 2, del decreto
                legislativo 30 marzo 2001, n. 165, non può superare il corrispondente importo determinato per l'anno
                2016. A
                decorrere dalla predetta data l'articolo 1, comma 236, della legge 28 dicembre 2015, n. 208 e'
                abrogato.”</i>
            <br/>
            <br/>
            <b>Richiamato</b> l'art. 33 comma 2, del D.L. n. 34/2019, convertito in Legge 58/2019<i> (c.d. Decreto
                “Crescita”)</i> e
            in particolare la previsione contenuta nell'ultimo periodo di tale comma, che modifica la modalità di
            calcolo del tetto al salario accessorio introdotto dall'articolo 23, comma 2, del D.Lgs 75/2017, modalità
            illustrata nel DM attuativo del 17.3.2020 concordato in sede di Conferenza Unificata Stato Regioni del
            11.12.2019, e che prevede che a partire dall’anno 2020 il limite del salario accessorio debba essere
            adeguato in aumento rispetto al valore medio pro-capite del 2018,
            <br/>
            <br/>
            <b>Vista</b> la Determinazione dell’Area <?php self::getInput('var19', '____', 'red'); ?> di
            costituzione della
            parte stabile del Fondo risorse decentrate per
            l'anno <?php self::getInput('var20', 'anno', 'orange'); ?>
            <br/>
            <?php self::getTextArea('area1', 'SCRIVERE QUALCOSA SE PER CASO È STATA ADOTTATA PRECEDENTEMENTE LA DETERMINA DI PARTE STABILE', 'red'); ?>
            <br/>
            <b>Tenuto conto</b> che nel periodo 2011-2014 <?php self::getInput('var44', 'f273', 'orange'); ?> risultano
            decurtazioni
            rispetto ai vincoli sul fondo 2010 e
            pertanto <?php self::getInput('var45', 'f273', 'orange'); ?> deve essere applicata la riduzione del fondo
            pari
            a
            <b>€</b><?php self::getInput('var46', 'f263', 'orange'); ?>;
            <br/>
            <br/>
            <b>Richiamato</b> l’importo totale del fondo anno 2016, per le risorse soggette al limite (con esclusione
            dei
            compensi destinati all'avvocatura, ISTAT, art. 15 comma 1 lett. k CCNL 1.4.1999, gli importi di cui alla
            lettera d) dell’art. 15 ove tale attività non risulti ordinariamente resa dall’Amministrazione
            precedentemente l’entrata in vigore del D. Lgs. 75/2017, le economie del fondo dell’anno 2015 e delle
            economie del fondo straordinari anno 2015), pari ad
            <b>€</b> <?php self::getInput('var47', 'f331', 'orange'); ?>.
            <br/>
            <br/>
            <b>Dato atto che </b>le ultime disposizioni individuano controlli più puntuali e stringenti sulla
            contrattazione
            integrativa;
            <br/>
            <br/>
            <b>Considerato</b> che il D.L. 6 marzo 2014, n. 16, convertito con modificazioni dalla legge n. 68/2014,
            all'art. 4
            ha previsto <i>“Misure conseguenti al mancato rispetto di vincoli finanziari posti alla contrattazione
                integrativa e all'utilizzo dei relativi fondi” e considerate la Circolare del Ministro per la
                semplificazione e la Pubblica Amministrazione del 12 maggio 2014 e il susseguente Documento della
                Conferenza
                delle Regioni e delle Province Autonome del 12 settembre 2014, nei quali viene precisato che ”Le regioni
                e
                gli enti locali che non hanno rispettato i vincoli finanziari posti alla contrattazione collettiva
                integrativa sono obbligati a recuperare integralmente, a valere sulle risorse finanziarie a questa
                destinate, rispettivamente al personale dirigenziale e non dirigenziale, le somme indebitamente erogate
                mediante il graduale riassorbimento delle stesse, con quote annuali e per un numero massimo di
                annualita'
                corrispondente a quelle in cui si e' verificato il superamento di tali vincoli”</i>.
            <br/>
            <br/>
            <?php self::getTextArea('area2', 'Preso atto che tali verifiche e eventuali azioni correttive sono applicabili unilateralmente dagli enti, anche in sede di autotutela, al riscontro delle condizioni previste nell’articolo 4 del D.L. 16/2014, convertito nella legge di conversione n. 68/2014, nel rispetto del diritto di informazione dovuto alle organizzazioni sindacali;', 'red'); ?>

            <br/>
            <br/>
            <?php self::getTextArea('area3', 'Dato atto che in autotutela l’Amministrazione intende far effettuare un lavoro di verifica straordinaria dei Fondi delle risorse decentrate per gli anni precedenti, nel rispetto di quanto previsto dall art. 4 del D.L. 6 marzo 2014, n. 16, convertito con modificazioni dalla legge n. 68/2014;', 'red'); ?>
            <br/>
            <br/>
            <b>Premesso che:</b>
            <ul class="d">
                <li><?php self::getInput('var22', ' il/la', 'orange'); ?> <?php self::getInput('var23', 'titolo_ente', 'orange'); ?>
                    ha rispettato i vincoli previsti
                    dalle
                    regole del cosiddetto “Equilibrio di Bilancio” e il
                    principio del tetto della spesa del personale sostenuta
                    rispetto <?php self::getInput('var24', 'criterio_riduzione_spesa', 'orange'); ?>;
                </li>
                <li>il/la <?php self::getInput('var24', 'var23', 'orange'); ?> ha rispettato i vincoli previsti
                    dalle
                    regole del cosiddetto “Equilibrio di Bilancio” e il
                    principio del tetto della spesa del personale sostenuta rispetto all'anno 2008;
                </li>
                <li>il numero di dipendenti in servizio nell'anno,
                    calcolato in
                    base alle modalità fornite dalla Ragioneria dello
                    Stato da ultimo con nota Prot. 12454 del 15.1.2021, pari
                    a <?php self::getInput('var48', 'R162', 'orange'); ?>
                    è<?php self::getInput('var25', 'superiore /inferiore o uguale', 'orange'); ?> al numero dei
                    dipendenti in
                    servizio al 31.12.2018 pari a <?php self::getInput('var49', 'R161', 'orange'); ?>, pertanto, in
                    attuazione
                    dell’art. 33
                    c. 2 D.L. 34/2019 convertito nella
                    L. 58/2019, il fondo e il limite di cui all’art. 23 c. 2 bis D.Lgs. 75/2017 devono essere adeguati
                    in
                    aumento al fine di garantire il valore medio pro-capite riferito al 2018;
                </li>
                <li>ai sensi delle vigenti disposizioni contrattuali sono già stati erogati in corso d’anno alcuni
                    compensi
                    gravanti sul fondo (indennità di comparto, incrementi economici, ecc), frutto di precedenti accordi
                    decentrati;
                </li>
                <li>il grado di raggiungimento del Piano delle Performance assegnato nell’anno verrà certificato
                    dall’Organismo
                    di Valutazione, che accerterà il raggiungimento degli stessi ed il grado di accrescimento dei
                    servizi a
                    favore della cittadinanza;
                </li>
            </ul>

            <b>Considerato che:</b>
            <ul class="d">
                <li>è quindi necessario fornire gli indirizzi per la costituzione, del suddetto fondo relativamente
                    all’anno
                    corrente;
                </li>
                <li> è inoltre urgente, una volta costituito il fondo suddetto, sulla base degli indirizzi di cui al
                    presente
                    atto, provvedere alla conseguente contrattazione decentrata per la distribuzione del fondo stesso;
                </li>
                <li>a tal fine è necessario esprimere fin d’ora le direttive a cui dovrà attenersi la Delegazione di
                    Parte
                    Pubblica durante la trattativa per il suddetto contratto decentrato;
                </li>
            </ul>

            <b>Ritenuto di:</b>
            <ul class="a">
                <li>esprimere i seguenti indirizzi per la costituzione del fondo delle risorse decentrate di parte
                    variabile
                    del Comparto Regioni ed Autonomie Locali relativo all’anno corrente:
                    <ul class="c">
                        <?php if (self::checkOptionalValues('R33')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 c. 4 CCNL
                            2018, delle
                            risorse
                            economiche complessive derivanti dal calcolo fino ad un massimo dell'1,2% del monte salari
                            (esclusa la quota
                            riferita alla dirigenza) stabilito per l'anno 1997, sempre rispettando il limite dell’anno
                            2016,
                            destinandoli
                            a <?php self::getTextArea('area4', ' (INSERIRE IL TITOLO o allegare i file TESTO LIBERO)', 'red') ?>
                            .
                            L’importo previsto è pari ad € <?php self::getInput('var51', 'R33', 'orange') ?>
                            <br/>
                            Si precisa che gli importi, qualora non interamente distribuiti, non daranno luogo ad
                            economie di fondo ma
                            ritorneranno nella disponibilità del bilancio dell’Ente.
                            </li><?php endif; ?>
                        <?php if (self::checkOptionalValues('R34')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67, comma 5
                            lett. b) del CCNL
                            21.5.2018, delle somme necessarie per il conseguimento di obiettivi dell’ente, anche di
                            mantenimento, nonché
                            obiettivi di potenziamento dei servizi di controllo finalizzati alla sicurezza urbana e
                            stradale Art. 56
                            quater CCNL 2018, definiti nel piano della performance o in altri analoghi strumenti di
                            programmazione della
                            gestione, al fine di sostenere i correlati oneri dei trattamenti accessori del personale,
                            per un importo
                            pari a € <?php self::getInput('var52', 'R34', 'orange'); ?>;
                            <br/>
                            In particolare tali obiettivi sono contenuti nel Piano esecutivo di
                            Gestione anno unitamente al Piano della Performance approvata con Delibera
                            <?php self::getInput('var26', ' della/del', 'orange'); ?><?php self::getInput('var27', 'nome_soggetto_deliberante', 'orange'); ?>
                            n. <?php self::getInput('var28', 'numero_delibera_approvazione_PEG', 'orange'); ?>
                            del <?php self::getInput('var29', 'data_delibera_approvazione_PEG', 'orange'); ?> e ne
                            vengono qui di
                            seguito elencati i titoli:
                            <br/>
                            – <?php self::getInput('var30', 'xxxxx, (specificare almeno gli importi previsti per ogni obiettivo);', 'red'); ?>
                            ;
                            <br/>
                            <?php self::getTextArea('area5', 'INSERIRE TESTO LIBERO', 'red'); ?>.
                            <br/>
                            Si precisa che i suddetti importi, qualora non interamente distribuiti, non daranno luogo ad
                            economie di
                            fondo ma ritorneranno nella disponibilità del bilancio dell’Ente;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R29')): ?>
                            <li>
                            autorizzazione all’iscrizione fra le risorse variabili, ai sensi dell’art. 67 comma 3 lett.
                            a) del CCNL
                            21.5.2018 delle somme derivanti da contratti di sponsorizzazione, accordi di collaborazione,
                            convenzioni con
                            soggetti pubblici o privati e contributi dell'utenza per servizi pubblici non essenziali,
                            secondo la
                            disciplina dettata dall'art. 43 della Legge 449/1997, e soggette al limite 2015, per
                            €<?php self::getInput('var53', 'R29', 'orange'); ?>, rispettivamente
                            per <?php self::getTextArea('area6', '(INSERIRE IL TITOLO o allegare i file TESTO LIBERO);', 'red'); ?>
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R30')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 lett.
                            c) del CCNL
                            21.5.2018 delle somme destinate alle attività di recupero ICI da distribuire ai sensi del
                            regolamento
                            vigente in materia e nel rispetto della normativa vigente in materia per
                            €<?php self::getInput('var54', 'R30', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R30')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 lett.
                            c) del CCNL
                            21.5.2018 delle somme destinate all’attuazione della specifica Legge
                            Regionale <?php self::getTextArea('area7', 'INSERIRE IL TITOLO TESTO LIBERO es. L.R. SARDEGNA n. 19 del 1997)', 'red'); ?>
                            da distribuire ai sensi del regolamento vigente in materia e nel rispetto della normativa
                            vigente in materia per
                            €<?php self::getInput('var55', 'R30', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R31')): ?>
                            <li>
                            autorizzazione all'iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 lett.
                            f) CCNL
                            21.5.2018 della quota parte del rimborso spese per ogni notificazione di atti per
                            € <?php self::getInput('var56', 'R31', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R32')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 lett.
                            e) CCNL
                            21.5.2018, delle somme derivanti dai risparmi del Fondo lavoro straordinario anno
                            precedente, pari ad
                            € <?php self::getInput('var57', 'R32', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R45')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 68 comma 1 CCNL
                            21.5.2018,
                            delle
                            risorse derivanti dai risparmi di parte stabile del Fondo risorse decentrate degli anni
                            precedenti, pari ad
                            €<?php self::getInput('var58', 'R45', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R42')): ?>
                            <li>
                            autorizzazione all’iscrizione fra le risorse variabili, ai sensi dell’art. 67 comma 3 lett.
                            a) del CCNL
                            21.5.2018 delle somme derivanti da contratti di sponsorizzazione, accordi di collaborazione,
                            convenzioni con
                            soggetti pubblici o privati e contributi dell'utenza per servizi pubblici non essenziali,
                            secondo la
                            disciplina dettata dall'art. 43 della Legge 449/1997 per
                            € <?php self::getInput('var59', 'R42', 'orange'); ?>,
                            rispettivamente
                            per<?php self::getTextArea('area7', 'INSERIRE IL TITOLO o allegare i file TESTO LIBERO)', 'red'); ?>
                            ;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R122')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 let.
                            c) del CCNL
                            21.5.2018 delle somme destinate agli incentivi per funzioni tecniche art. 113 comma 2 e 3
                            D.Lgs. n. 50/2016
                            e ss.mm.ii da distribuire ai sensi del regolamento vigente in materia e nel rispetto della
                            normativa vigente
                            in materia per € <?php self::getInput('var60', 'R122', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R39')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 let.
                            c) del CCNL
                            21.5.2018 delle somme destinate alle attività svolte per conto dell’ISTAT da distribuire ai
                            sensi dei
                            regolamenti vigenti in materia e nel rispetto della normativa vigente in materia per
                            € <?php self::getInput('var61', 'R39', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R40')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’ 67 comma 3 let. c)
                            del CCNL
                            21.5.2018 delle somme destinate alla “avvocatura” da distribuire ai sensi del regolamento
                            vigente in materia
                            e nel rispetto della normativa vigente in materia per
                            €<?php self::getInput('var62', 'R40', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R41')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 let.
                            c) del CCNL
                            21.5.2018 delle somme finanziate da fondi di derivazione dell'Unione Europea da distribuire
                            ai sensi dei
                            regolamenti vigenti in materia e nel rispetto della normativa vigente in materia per
                            €<?php self::getInput('var63', 'R41', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R147')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 let.
                            c) del CCNL
                            21.5.2018 delle somme destinate alle attività di recupero IMU e TARI in riferimento all'art.
                            1 comma 1091
                            della L. 145 del 31.12.2018 (Legge di Bilancio 2019) da distribuire ai sensi del regolamento
                            vigente in
                            materia e nel rispetto della normativa vigente in materia per
                            €<?php self::getInput('var64', 'R147', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R111')): ?>
                            <li>
                            autorizzazione all’iscrizione, fra le risorse variabili, ai sensi dell’art. 67 comma 3 let.
                            c) del CCNL
                            21.5.2018 delle somme destinate alle
                            attività <?php self::getTextArea('area8', '(INSERIRE IL TITOLO TESTO LIBERO)', 'red'); ?> da
                            distribuire
                            ai sensi del regolamento vigente in materia e nel rispetto della normativa vigente in
                            materia per
                            € <?php self::getInput('var65', 'R111', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R37')): ?>
                            <li>
                            vista la Delibera <?php self::getInput('var31', 'della/del', 'orange'); ?>
                            <?php self::getInput('var32', 'nome_soggetto_deliberante', 'orange'); ?>
                            n.<?php self::getInput('var33', 'numero_delibera_approvazione_piano', 'orange'); ?>
                            del
                            <?php self::getInput('var34', 'data_delibera_approvazione_piano', 'orange'); ?>di
                            approvazione del Piano di
                            razionalizzazione anno ai sensi dell’art. 16
                            comma 5 della Legge 111/2011 e dell’art. 67 comma 3 lett. B del CCNL 21.5.2018,
                            autorizzazione
                            all’iscrizione tra le risorse variabili di
                            €<?php self::getInput('var66', 'R37', 'orange'); ?>, che
                            dovranno
                            essere
                            distribuite nel rigoroso rispetto dei
                            principi introdotti dalla norma vigente e solo se a consuntivo verrà espresso parere
                            favorevole da parte
                            dell'Organo di Revisione;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R152')): ?>
                            <li>
                            autorizzazione all’iscrizione, ai sensi dell’art. 67 comma 5 lett. b) del CCNL 21.5.2018
                            della sola quota
                            di
                            maggior incasso rispetto all’anno precedente a seguito di obiettivi di potenziamento dei
                            servizi di
                            controllo finalizzati alla sicurezza urbana e stradale Art. 56 quater CCNL 2018, come
                            risorsa NON soggetta
                            al limite secondo dalla Corte dei Conti Sezione delle Autonomie con delibera n. 5 del 2019,
                            per un importo
                            pari a € <?php self::getInput('var67', 'R152', 'orange'); ?>;
                            </li>
                        <?php endif; ?>
                        <?php if (self::checkOptionalValues('R155')): ?>
                            <li>
                            autorizzazione all’iscrizione, ai sensi dell’art. 67 c.7 e Art.15 c.7 CCNL 2018 della quota
                            di incremento
                            del Fondo trattamento accessorio per riduzione delle risorse destinate alla retribuzione di
                            posizione e di
                            risultato delle PO rispetto al tetto complessivo del salario accessorio art. 23 c. 2 D.Lgs
                            75/2017, per un
                            importo pari a € <?php self::getInput('var68', 'R155', 'orange'); ?>.
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li> In merito all’utilizzo del fondo, fornisce i seguenti indirizzi alla delegazione trattante di parte
                    pubblica
                    <ul class="c">
                        <li>Dare attuazione al contratto decentrato normativo vigente nell’Ente per il
                            triennio <?php self::getInput('var35', 'xxxx/xxxx', 'orange'); ?> siglato in
                            data <?php self::getInput('var36', 'xxxx/xxxx', 'orange'); ?> per la ripartizione economica
                            dell’anno
                            e
                            riconoscere le indennità previste, nel rispetto
                            delle condizioni previste dai CCNL e
                            CDIA <?php self::getTextArea('area9', '(Compilazione a cura dell’Ente TESTO LIBERO)', 'red'); ?>
                        </li>
                        <li>Gli importi destinati alla performance dovranno essere distribuiti in relazione agli
                            obiettivi coerenti
                            col
                            DUP e contenuti all’interno del Piano della
                            Performance <?php self::getInput('var37', 'anno', 'orange'); ?>.
                            Tali obiettivi dovranno avere i requisiti di
                            misurabilità ed essere incrementali rispetto all’ordinaria attività lavorativa. Inoltre, le
                            risorse
                            destinate a finanziare le performance dovranno essere distribuite sulla base della
                            valutazione da effettuare
                            a consuntivo ai sensi del sistema di valutazione vigente nell’Ente e adeguato al D.Lgs.
                            150/2009;
                        </li>
                    </ul>
                </li>
            </ul>

            sono fatte salve, in ogni caso, tutte le piccole modifiche non sostanziali che la delegazione ritenga
            opportune;
            <br/>
            <br/>
            <b>Appurato che:</b>
            <ul class="d">
                <li>le spese di cui al presente provvedimento non alterano il rispetto del limite delle spese di
                    personale
                    rispetto alla media del triennio 2011-2013; e ribadito che le risorse variabili verranno distribuite
                    solo se
                    sarà rispettato l’“Equilibrio di Bilancio” dell’anno corrente e solo se non saranno superati i
                    limiti in
                    materia di spesa di personale;
                </li>
                <li>le spese di cui al presente provvedimento non alterano il rispetto del limite delle spese di
                    personale
                    rispetto all'anno 2008 e ribadito che le risorse variabili verranno distribuite solo se sarà
                    rispettato l’
                    “Equilibrio di Bilancio” dell’anno corrente e solo se non saranno superati i limiti in materia di
                    spesa di
                    personale;
                </li>
                <li>le spese di cui al presente provvedimento non alterano il rispetto del limite delle spese di
                    personale
                    rispetto <?php self::getInput('var38', 'criterio riduzione spesa', 'orange'); ?> e ribadito che le
                    risorse variabili verranno distribuite solo se
                    sarà rispettato l’“Equilibrio di Bilancio” dell’anno corrente e solo se non saranno superati i
                    limiti in
                    materia di spesa di personale;
                </li>
            </ul>

            <b>Acquisiti sulla proposta di deliberazione:</b>
            <ul class="d">
                <li>i pareri favorevoli, espressi sulla presente deliberazione ai sensi e per gli effetti di cui
                    all’articolo
                    49, comma 1 del D.Lgs. n. 267/2000, allegati quale parte integrante e sostanziale del presente atto;
                </li>
            </ul>

            a voti unanimi resi nei modi di legge
            <br/>
            <br/>

            <h4>DELIBERA</h4>
            <ul class="n">
                <li>di esprimere gli indirizzi per la costituzione variabile del fondo delle risorse decentrate di cui
                    all’art. 67 del CCNL 21.5.2018 del Comparto Regioni ed Autonomie Locali relativi
                    all’anno <?php self::getInput('var39', 'anno', 'orange'); ?> e di
                    autorizzare l'inserimento delle risorse variabili nei modi e nei termini riportati in premessa;
                </li>
                <li>di esprimere le direttive alle quali dovrà attenersi la Delegazione Trattante di Parte Pubblica, nel
                    contrattare con la Delegazione Sindacale un’ipotesi di contratto collettivo decentrato integrativo
                    per il
                    personale non dirigente, che dovrà essere sottoposta a
                    questa <?php self::getInput('var40', 'nome_soggetto_deliberante', 'orange'); ?> e all’organo di
                    revisione contabile per l’autorizzazione e la definitiva stipula, unitamente alla relazione
                    illustrativa e
                    tecnico-finanziaria prevista ai sensi del D.Lgs. 150/2009 nei termini riportati in premessa;
                </li>
                <li>di inviare il presente provvedimento al responsabile
                    per
                    l’adozione degli atti di competenza e per
                    l’assunzione dei conseguenti impegni di spesa, dando atto che gli stanziamenti della spesa del
                    personale
                    attualmente previsti nel bilancio <?php self::getInput('var41', 'anno', 'orange'); ?>
                    presentano la
                    necessaria disponibilità.
                </li>
                <li><span style="color: red">Di inviare il presente provvedimento al Revisore dei Conti per la certificazione di
                competenza</span></li>
            </ul>

            <br/>
            Successivamente,
            <br/>
            <br/>
            <?php self::getInput('var42', 'Il/La', 'orange'); ?>
            <?php self::getInput('var43', 'nome_soggetto_deliberante', 'orange'); ?>

            <br/>
            <br/>
            Stante l’urgenza di provvedere
            <br/>
            <br/>
            Visto l’art. 134 – IV comma – del D. Lgs. 267/2000;
            <br/>
            <br/>
            Con voti favorevoli unanimi resi in forma palese
            <br/>
            <br/>
            <h4> DELIBERA</h4>
            <br/>
            <br/>
            Di rendere il presente atto immediatamente eseguibile.
            <br/>
            <br/>
            <br/>
            <br/>

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