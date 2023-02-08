<?php

namespace dateXFondoPlugin;

use DocumentRepository;

class DeterminaCostituzioneDocument
{
    private $infos = [];
    private $user_infos = [];
    private $values = array();


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
        if($value == 'titolo_ente'){
            $value = $this->user_infos['titolo_ente'];
        }
        else if($value == 'nome_soggetto_deliberante'){
            $value = $this->user_infos['nome_soggetto_deliberante'];
        }
        else if($value == 'responsabile_documento'){
            $value = $this->user_infos['responsabile'];
        }
        else if($value == 'documento_a_firma_di'){
            $value = $this->user_infos['firma'];
        }
        else if($value == 'riduzione_spesa'){
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
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/main.css">
            <link rel="stylesheet" href="<?= DateXFondoCommon::get_base_url() ?>/assets/styles/templateheader.css">

            <script>
                let data = {};

                function exportHTML() {
                    var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
                        "xmlns:w='urn:schemas-microsoft-com:office:word' " +
                        "xmlns='http://www.w3.org/TR/REC-html40'>" +
                        "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title></head><body>";
                    var footer = "</body></html>";
                    const bodyHTML = $("#determinaCostituzioneContent").clone(true);
                    bodyHTML.find('input,textarea').remove();
                    var sourceHTML = header + bodyHTML.html() + footer;
                    var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
                    var fileDownload = document.createElement("a");
                    document.body.appendChild(fileDownload);
                    fileDownload.href = source;
                    var currentdate = new Date();
                    fileDownload.download = 'determinaCostituzione' + "_" + currentdate.getDate() + "-"
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

        <div id="determinaCostituzioneContent">
            <h3><b>Determinazione del</b> <?php self::getInput('var0', 'nome_soggetto_deliberante', 'orange'); ?></h3>
            <br>
            <br>
            <h6><b>OGGETTO: COSTITUZIONE FONDO DELLE RISORSE DECENTRATE PER
                    L'ANNO</b><?php self::getInput('var1', 'anno', 'orange'); ?></h6>
            <br>
            <br>
            <b>Viste:</b>
            <br>
            <br>
            • la
            deliberazione <?php self::getInput('var2', 'della/del', 'orange'); ?> <?php self::getInput('var3', 'Consiglio Comunale/Assemblea', 'orange'); ?>
            n
            <?php self::getInput('var4', 'numero_delibera_approvazione_bilancio', 'orange'); ?>
            del <?php self::getInput('var5', ' data_delibera_approvazione_bilancio', 'orange'); ?>, esecutiva, relativa
            a:
            "<?php self::getInput('var6', '“Bilancio di previsione anno, bilancio pluriennale e DUP/PEG 2022/2024, piano di investimenti – approvazione', 'red'); ?>
            ";
            <br>
            la
            deliberazione <?php self::getInput('var7', 'della/del', 'orange'); ?> <?php self::getInput('var8', 'nome_soggetto_deliberante', 'orange'); ?>
            n. <?php self::getInput('var9', 'numero_delibera_approvazione_PEG', 'orange'); ?>
            del <?php self::getInput('var10', 'data_delibera_approvazione_PEG', 'orange'); ?> , esecutiva, relativa
            all’approvazione del Piano esecutivo di Gestione <?php self::getInput('var11', 'anno', 'orange'); ?>
            unitamente al Piano della Performance;
            <br>
            • i successivi atti di variazione del bilancio e del P.E.G.;
            • il vigente Regolamento di Organizzazione degli Uffici e dei Servizi;
            • il vigente regolamento di contabilità;
            • il T.U. sull’ordinamento degli Enti locali, approvato con D.Lgs... n. 267/2000;
            • il C.C.D.I. per la distribuzione del fondo delle risorse
            decentrate <?php self::getInput('var12', 'F13', 'orange'); ?>;
            • il nuovo CCNL siglato in data 21.5.2018;
            • il CCNL siglato in data 16.11.2022
            • la
            delibera <?php self::getInput('var13', 'della/del', 'orange'); ?> <?php self::getInput('va14', 'nome_soggetto_deliberante', 'orange'); ?>
            n. del <?php self::getInput('var15', 'data_delibera_indirizzo', 'orange'); ?>, esecutiva ai sensi di legge,
            avente per oggetto: PERSONALE NON DIRIGENTE, FONDO RISORSE DECENTRATE PER
            L’ANNO <?php self::getInput('var16', 'anno', 'orange'); ?>, INDIRIZZI PER LA
            COSTITUZIONE, DIRETTIVE PER LA CONTRATTAZIONE DECENTRATA INTEGRATIVA, con la
            quale <?php self::getInput('var17', 'nome_soggetto_deliberante', 'orange'); ?> ha
            fornito gli indirizzi per la costituzione delle risorse variabili, che si intende interamente
            richiamata;
            <br>
            <br>
            <b>Premesso che:</b>
            <br>
            <br>
            • <?php self::getInput('var18', 'il/la', 'orange'); ?> <?php self::getInput('var19', 'nome_soggetto_deliberante', 'orange'); ?>
            ha rispettato i vincoli previsti dalle regole del cosiddetto “Equilibrio di
            Bilancio” e il principio di riduzione della spesa del personale sostenuta
            rispetto <?php self::getInput('var20', 'criterio riduzione spesa', 'orange'); ?>;
            <br>
            <br>
            <b> Considerato che:</b>
            <br>
            <br>
            • ai sensi dell’art. 67 del CCNL 21.5.2018, devono essere annualmente destinate risorse per le politiche
            di sviluppo delle risorse umane e per la produttività collettiva e individuale;
            • la costituzione di tale fondo risulta di competenza
            del <?php self::getInput('var21', 'nome_soggetto_deliberante', 'orange'); ?> ;
            <br>
            <br>
            <b>Ritenuto</b>, pertanto, di procedere nella costituzione del Fondo per
            l’anno <?php self::getInput('var22', 'anno', 'orange'); ?> in adeguamento all’art. 67
            del CCNL 21.5.2018 <?php self::getInput('var23', 'e all art. 79 CCNL 16.11.2022', 'orange'); ?> ;
            <br>
            <br>

            <b>Richiamato</b> l'art. 33 comma 2, del D.L. 34/2019, convertito in Legge 58/2019 (c.d. Decreto “Crescita”)
            e
            in particolare la previsione contenuta nell'ultimo periodo di tale comma, che modifica il tetto al
            salario accessorio così come introdotto dall'articolo 23, comma 2, del D.Lgs. 75/2017, modalità
            illustrata nel DM attuativo del 17.3.2020 concordato in sede di Conferenza Unificata Stato Regioni del
            11.12.2019, e che prevede che, a partire dall’anno 2020, il limite del salario accessorio debba essere
            adeguato in aumento rispetto al valore medio pro-capite del 2018 in caso di incremento del numero di
            dipendenti presenti nel 2022 rispetto ai presenti al 31.12.2018;
            <br>
            <br>
            <b>Considerato che</b> l’incremento di cui all’art. 33 D.L. 34/2019 può essere applicato sia al fondo
            risorse
            decentrate sia ad incremento del Fondo delle Posizioni Organizzative;

            <br>
            <br>
            <b>Considerato che</b> il D.L. 6 marzo 2014, n. 16, convertito con modificazioni dalla legge n. 68/2014,
            all'art. 4 ha previsto “Misure conseguenti al mancato rispetto di vincoli finanziari posti alla
            contrattazione integrativa e all'utilizzo dei relativi fondi” e considerate la Circolare del Ministro
            per la semplificazione e la Pubblica Amministrazione del 12 maggio 2014 e il susseguente Documento della
            Conferenza delle Regioni e delle Province Autonome del 12 settembre 2014, nei quali viene precisato che
            ”Le regioni e gli enti locali che non hanno rispettato i vincoli finanziari posti alla contrattazione
            collettiva integrativa sono obbligati a recuperare integralmente, a valere sulle risorse finanziarie a
            questa destinate, rispettivamente al personale dirigenziale e non dirigenziale, le somme indebitamente
            erogate mediante il graduale riassorbimento delle stesse, con quote annuali e per un numero massimo di
            annualità corrispondente a quelle in cui si e' verificato il superamento di tali vincoli”.
            <br>
            <br>
            <b>Preso atto che</b> tali verifiche e eventuali azioni correttive sono applicabili unilateralmente dagli
            enti,
            anche in sede di autotutela, al riscontro delle condizioni previste nell’articolo 4 del D.L. 16/2014,
            convertito nella Legge di conversione n. 68/2014, nel rispetto del diritto di informazione dovuto alle
            organizzazioni sindacali;
            <br>
            <br>
            <b>Premesso che</b> in autotutela l’Amministrazione ha deciso di far effettuare un lavoro di verifica
            straordinaria dei Fondi delle risorse decentrate per gli anni precedenti, nel rispetto di quanto
            previsto dall'art. 4 del D.L. 6 marzo 2014, n. 16, convertito con modificazioni dalla legge n. 68/2014
            <br>
            <br>
            <b>Ritenuto</b>, pertanto, di procedere ad una verifica straordinaria sulla correttezza dei fondi pregressi
            ai
            sensi dell'art. 4 del D.L. 6 marzo 2014, n. 16, convertito con modificazioni dalla legge n. 68/2014 e
            alla costituzione del Fondo per l’anno;
            <br>
            <br>
            Dato atto che dalla verifica effettuata sulla correttezza della costituzione e l'utilizzo dei fondi
            pregressi ai sensi dell'art. 4 del D.L. 6 marzo 2014, n. 16, convertito con modificazioni dalla legge n.
            68/2014, <?php self::getTextArea('area0', 'SONO/NON SONO stati rilevati errori materiali nella costituzione delle risorse decentrate;', 'orange'); ?>
            <br>
            <br>
            <?php self::getTextArea('area1', 'PS: Nel caso in cui si verifichi un’anomalia circa l esatta correttezza dei fondi degli anni pregressi predisporre apposito atto dal quale emergono gli errori riscontrati e prevedere la modalità di recupero delle predette somme, in base al contenuto dell art. 4 del D.L. 16/2014. Nel caso in cui si riscontri un’anomalia circa la modalità di utilizzo dei fondi pregressi, aggiungere al testo di determina quali sono stati gli errori riscontrati e verificare di aver rispettato le condizioni poste dal comma 3 dell art. 4 del D.L. 16/2014.', 'red'); ?>
            <br>
            <br>
            <b>Considerato che:</b>
            <br>
            <br>
            • l’art. 67 comma 1 del CCNL 21.5.2018 ha definito che le risorse aventi carattere di certezza, stabilità
            e continuità determinate nell’anno 2017 secondo la previgente disciplina contrattuale, vengono definite
            in un unico importo che resta confermato, con le stesse caratteristiche, anche per gli anni successivi
            per un importo pari ad € <?php self::getInput('var25', 'S1_1', 'orange'); ?>;
            <br>
            <br>
             <?php if (self::checkOptionalValues('R124')): ?>
            • ai sensi dell’art. 67 comma 2 lett. c) CCNL 22.5.2018 che prevede che “le risorse di cui al comma 1, sono
            integrate dall’importo annuo della retribuzione individuale di anzianità e degli assegni ad personam,
            compresa la quota di tredicesima, in godimento da parte del personale cessato dal servizio nell’anno
            precedente”, è prevista una integrazione pari a € <?php self::getInput('var26', 'R124', 'orange'); ?>;
            <?php endif; ?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R128')): ?>
            • ai sensi dell’art. 67 comma 5 lett. a) CCNL 22.5.2018 che prevede “in caso di incremento delle dotazioni
            organiche, al fine di sostenere gli oneri dei maggiori trattamenti economici del personale” si inserisce
            l'importo di € <?php self::getInput('var27', 'R128', 'orange'); ?>;, in quanto l’Ente
            nell’anno <?php self::getInput('var28', 'xxxxxx', 'orange'); ?>; ha incrementato la dotazione organica e ha
            effettuato
            le conseguenti assunzioni;
             <?php endif; ?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R127')): ?>
            • ai sensi dell’art. 67 comma 2 lett. g) del CCNL 22.5.2018 si inseriscono le somme per la riduzione stabile
            del fondo dello straordinario, ad invarianza complessiva di risorse stanziate, per
            € <?php self::getInput('var29', 'R127', 'orange'); ?>;
             <?php endif; ?>
            <br>
            <br>
              <?php if (self::checkOptionalValues('R126')): ?>
            • ai sensi dell’art. 67 comma 2 lettera e) del CCNL 22.5.2018 si inseriscono gli importi necessari a
            sostenere
            a regime gli oneri del trattamento economico di personale trasferito, anche nell’ambito di processi
            associativi, di delega o trasferimento di funzioni, a fronte di corrispondente riduzione della componente
            stabile dei Fondi delle amministrazioni di provenienza, ferma restando la capacità di spesa a carico del
            bilancio dell’ente, nonché degli importi corrispondenti agli adeguamenti dei Fondi previsti dalle vigenti
            disposizioni di legge, a seguito di trasferimento di personale, come ad esempio l’art. 1, commi da 793 a
            799, della legge n. 205/2017; le Unioni di comuni tengono anche conto della speciale disciplina di cui
            all’art. 70-sexies, per € <?php self::getInput('var30', 'R126', 'orange'); ?>;
              <?php endif; ?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R125')): ?>
            • ai sensi dell’art. 67 comma 2 lettera d) del CCNL 22.5.2018 si inseriscono risorse riassorbite ai sensi
            dell’art. 2, comma 3, del D.Lgs. 30 marzo 2001, n. 165, per un importo di
            € <?php self::getInput('var31', 'R125', 'orange'); ?>;
             <?php endif; ?>
            <br>
            <br>
            • ai sensi dell’art.<?php self::getInput('var32', 'xx comma x del CCNL xx.xx.xxxx', 'orange'); ?> si
            inseriscono le somme per € <?php self::getInput('var33', '(INSERIRE IL TITOLO TESTO
            LIBERO SE SONO STATE AGGIUNTE ALTRE RISORSE NELLA PARTE STABILE)', 'orange'); ?> ;
            <br>
            <br>
               <?php if (self::checkOptionalValues('R112')): ?>
            • ai sensi dell’art. 67 comma 2 lettera b) del CCNL 22.5.2018 si inseriscono le somme di un importo pari
            alle
            differenze tra gli incrementi a regime di cui all’art. 64 CCNL 2018 riconosciuti alle posizioni economiche
            di ciascuna categoria e gli stessi incrementi riconosciuti alle posizioni iniziali; tali differenze sono
            calcolate con riferimento al personale in servizio alla data in cui decorrono gli incrementi e confluiscono
            nel fondo a decorrere dalla medesima data, per € <?php self::getInput('var34', 'R112', 'orange'); ?>. Tali
            somme, ai sensi della dichiarazione congiunta n.
            5 del CCNL 2018, non sono assoggettate ai limiti di crescita dei Fondi previsti dalle norme vigenti ed in
            particolare all’art. 23 del D.Lgs... 75/2017, così come confermato definitivamente dalla Delibera della
            Corte dei Conti Sezione delle Autonomie n. 19/2018;
               <?php endif; ?>
            <br>
            <br>
              <?php if (self::checkOptionalValues('R146')): ?>
            • ai sensi dell’art. 67 comma 2 lettera a) del CCNL 22.5.2018 si inseriscono le somme di un importo su base
            annua, pari a euro 83,20 per le unità di personale destinatarie del presente CCNL in servizio alla data del
            31.12.2015, a decorrere dal 31.12.2018 e a valere dall’anno 2019, per
            € <?php self::getInput('var35', 'R146', 'orange'); ?>. Tali somme, ai sensi della
            dichiarazione congiunta n. 5 del CCNL 2018, non sono assoggettate ai limiti di crescita dei Fondi previsti
            dalle norme vigenti ed in particolare all’art. 23 del D.Lgs... 75/2017, così come confermato definitivamente
            dalla Delibera della Corte dei Conti Sezione delle Autonomie n. 19/2018;
              <?php endif; ?>
            <br>
            <br>
            <?php if (self::checkOptionalValues('R154')): ?>
            • ai sensi dell’art. 67 comma 2 lettera e) del CCNL 22.5.2018 e art 1 c 800 L. 205/2017 relativo
            all’armonizzazione retribuzione accessoria del personale delle Città Metropolitane e Province transitato ad
            altre Amministrazioni, si inseriscono gli importi necessari a sostenere a regime gli oneri del trattamento
            economico di personale trasferito, a fronte di corrispondente riduzione della componente stabile dei Fondi
            delle amministrazioni di provenienza, che in base ai chiarimenti della Ragioneria dello Stato (nota del
            18.12.2018 di riscontro alla Regione Lombardia) sono considerate non assoggettate ai limiti di crescita dei
            Fondi previsti dalle norme vigenti ed in particolare all’art. 23 del D.Lgs... 75/2017, per un importo pari a
            € <?php self::getInput('var36', 'R154', 'orange'); ?>;
            <?php endif; ?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R148')): ?>
            • ai sensi dell'art 11 D.L. 135/2018 c. 1 lett. b) si inseriscono le somme per un importo di
            € <?php self::getInput('var37', 'R148', 'orange'); ?> a
            copertura degli oneri del trattamento economico accessorio per le assunzioni effettuate, in deroga alle
            facoltà assunzionali vigenti, successivamente all'entrata in vigore dell'articolo 23 del D.Lgs. 75/2017;
             <?php endif; ?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R25')): ?>
            • per effetto del trasferimento dell’ex personale ATA da questo Ente presso il Comparto Scuola, già a far
            data
            dall’anno 2000, sono state decurtate dal fondo risorse pari ad
            € <?php self::getInput('var37', 'R25', 'orange'); ?>;
             <?php endif; ?>
            <br>
            <br>
            <?php if (self::checkOptionalValues('R26')): ?>
            • già a partire dall’anno xxxx, a seguito dell'affidamento delle posizioni organizzative e della relativa
            retribuzione di posizione, per gli Enti senza dirigenza, il fondo di cui all'art. 15 del CCNL dell’1.4.1999
            è stato decurtato della quota delle risorse prima destinate al pagamento dei compensi per il salario
            accessorio del personale interessato <?php self::getTextArea('area2', '(OPPURE: per gli Enti con Dirigenza, a partire dall’anno 2018, in
            applicazione delle disposizioni previste dall’art. 67 c. 1 CCNL 21.5.2018, il fondo deve essere decurtato
            della quota delle risorse prima destinate al pagamento dei compensi per il salario accessorio della
            Posizione organizzativa)', 'orange'); ?>, per un valore pari ad
            € <?php self::getInput('var39', 'R26', 'orange'); ?>;
            <?php endif; ?>
            <br>
            <br>
            <?php if (self::checkOptionalValues('R27')): ?>
            • già a partire dall’anno 1999, a seguito del primo inquadramento di alcune categorie di lavoratori in
            applicazione del CCNL del 31.3.1999 (area di vigilanza e personale della prima e seconda qualifica
            funzionale) il fondo è stato decurtato della quota delle risorse destinate al pagamento degli oneri
            derivanti dalla riclassificazione del personale per un valore pari ad
            € <?php self::getInput('var40', 'R27', 'orange'); ?>;
            <?php endif; ?>
            <br>
            <br>
            • il fondo viene decurtato per
            € <?php self::getInput('var41', '(INSERIRE IL TITOLO TESTO LIBERO SE SONO STATE DECURTATE ALTRE RISORSE NELLA PARTE STABILE)', 'orange'); ?>
            ;
            <br>
            <br>
            • ai sensi dell’art. 67 comma 2 lettera e) del CCNL 22.5.2018 si procede alla decurtazione degli importi
            relativi agli oneri del trattamento economico di personale trasferito presso altri Enti, nell’ambito di
            processi associativi, di delega o trasferimento di funzioni, previsti da disposizioni di legge o altro, per
            un importo pari a € <?php self::getInput('var40', 'R151', 'orange'); ?>;
            <br>
            <br>
            <b> Tenuto conto che:</b>
            <br>
            <br>
            • il numero di dipendenti in servizio nell’anno, calcolato in base alle modalità fornite dalla Ragioneria
            dello Stato da ultimo con nota Prot. 12454 del 15.1.2021, pari
            a <?php self::getInput('var41', 'R162', 'orange'); ?> è superiore al numero dei dipendenti
            in servizio al 31.12.2018 pari a R161, pertanto, in attuazione dell’art. 33 c. 2 D.L. 34/2019 convertito
            nella L. 58/2019, il fondo risorse decentrate e il relativo limite di cui all’art. 23 c. 2 D.Lgs... 75/2017
            devono essere adeguati in aumento al fine di garantire il valore medio pro-capite riferito al 2018, per un
            importo pari ad € <?php self::getInput('var42', 'R150', 'orange'); ?>;
            <br>
            <br>
            Si precisa che, in base agli indirizzi
            della <?php self::getInput('var43', 'nome_soggetto_deliberante', 'orange'); ?>, in attuazione dell’art. 33
            c. 2 D.L.
            34/2019 convertito nella L. 58/2019, viene aumentato anche il Fondo Posizioni organizzative per un importo
            pari a € <?php self::getInput('var44', 'f374', 'orange'); ?>;
            <br>
            • il numero di dipendenti in servizio nell’anno, calcolato in base alle modalità fornite dalla Ragioneria
            dello Stato da ultimo con nota Prot. 12454 del 15.1.2021, pari
            a <?php self::getInput('var45', 'R162', 'orange'); ?> è inferiore o uguale al numero dei
            dipendenti in servizio al 31.12.2018 pari a <?php self::getInput('var46', 'R161', 'orange'); ?>, pertanto,
            in attuazione dell’art. 33 c. 2 D.L. 34/2019
            convertito nella L. 58/2019, il fondo e il limite di cui all’art. 23 c.2 D.Lgs. 75/2017 non devono essere
            adeguati in aumento al fine di garantire il valore medio pro-capite riferito al 2018;
            <br>
            • <?php self::getTextArea('area3', 'l’Ente si impegna a modificare la presente costituzione del fondo nel caso di incremento o diminuzione del
            numero di dipendenti in servizio rispetto al 31.12.2018 e comunque a rideterminare (anche in diminuzione) il
            salario accessorio complessivo in caso di sopraggiunte modifiche normative, chiarimenti ministeriali,
            interventi giurisprudenziali, sentenze o pareri di Corte dei Conti sulle modalità di calcolo di tale
            integrazione', 'red'); ?>;
            <br>
            • Le risorse aventi carattere di certezza, stabilità e continuità determinate
            nell’anno <?php self::getInput('var47', 'anno', 'orange'); ?> ai sensi
            dell’art. 67 commi 1 e 2 del CCNL 21.5.2018, e adeguate alle disposizioni del D.L. 34/2019, risultano
            pertanto essere pari ad € <?php self::getInput('var48', 'f3', 'orange'); ?>, di cui
            € <?php self::getInput('var49', 'f317', 'orange'); ?> soggette ai vincoli;
            <br>
            <br>
            <b>Preso atto che:</b>
            <br>
            è stato autorizzato l'inserimento delle voci variabili di cui all’art. 67 comma 3 CCNL 21.5.2018
            sottoposte al limite dell’anno 2016, di cui all’art. 23 del D.Lgs. 75/2017 e pertanto vengono stanziate:
            <br>
            <br>
            <?php if (self::checkOptionalValues('R33')): ?>
            • ai sensi dell’art. 67 comma 4 CCNL 21.5.2018, le risorse economiche derivanti dal calcolo fino ad un
            massimo
            dell'1,2% del monte salari anno 1997 (esclusa la quota riferita alla dirigenza), per un importo pari ad
            € <?php self::getInput('var50', 'R33', 'orange'); ?>
            ;

            <br>
            L’utilizzo è conseguente alla verifica dell’effettivo conseguimento dei risultati attesi.
            <?php endif;?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R29')): ?>
            • ai sensi dell’art. 67 comma 3 lett. a) CCNL 21.5.2018 le somme derivanti da contratti di sponsorizzazione,
            accordi di collaborazione, convenzioni con soggetti pubblici o privati e contributi dell'utenza per servizi
            pubblici non essenziali, secondo la disciplina dettata dall'art. 43 della Legge 449/1997 per
            € <?php self::getInput('var51', 'R29', 'orange'); ?> ,
            rispettivamente
            per <?php self::getTextArea('area4', '(INSERIRE IL TITOLO o allegare i file TESTO LIBERO)', 'red'); ?> ;
             <?php endif;?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R30')): ?>
            • ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018, le somme destinate alle attività di recupero ICI
            per
            € <?php self::getInput('var52', 'R30', 'orange'); ?>;
             <?php endif;?>
            <br>
            <br>
              <?php if (self::checkOptionalValues('R31')): ?>
            • ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018, le somme destinate al finanziamento delle attività
            per l’attuazione della Legge Regionale specifica (INSERIRE IL TITOLO TESTO LIBERO es. L.R. SARDEGNA n. 19
            del 1997) per €<?php self::getInput('var53', 'R31', 'orange'); ?> ;
              <?php endif;?>

            <br>
            <br>
              <?php if (self::checkOptionalValues('R32')): ?>
            • ai sensi dell’art. 67 comma 3 lett. f) CCNL 21.5.2018 una quota parte del rimborso spese per ogni
            notificazione di atti per €<?php self::getInput('var54', 'R32', 'orange'); ?> ;
              <?php endif;?>
            <br>
            <br>
             <?php if (self::checkOptionalValues('R34')): ?>
            • ai sensi dell’art. 67 comma 5 lett. b) CCNL 21.5.2018, le somme per il conseguimento di obiettivi
            dell’ente,
            anche di mantenimento, nonché obiettivi di potenziamento dei servizi di controllo finalizzati alla sicurezza
            urbana e stradale Art. 56 quater CCNL 2018, definiti nel piano della performance o in altri analoghi
            strumenti di programmazione della gestione, al fine di sostenere i correlati oneri dei trattamenti accessori
            del personale, per un importo pari a €<?php self::getInput('var55', 'R34', 'orange'); ?> ;
            <br>
            Tali risorse sono destinate al finanziamento degli obiettivi contenuti nel Piano esecutivo di Gestione anno
            unitamente al Piano della Performance e ne vengono qui di seguito elencati i titoli:
            <br>
            - <?php self::getInput('var56', '  xxxxx, (specificare almeno gli importi previsti per ogni obiettivo)', 'orange'); ?>
            ;
            <br>
            - <?php self::getInput('var57', '  xxxxx', 'orange'); ?>
            <br>
            <?php self::getTextArea('area5', '(INSERIRE IL TITOLO o allegare i file TESTO LIBERO)', 'red'); ?>
            <br>
            Si precisa che gli importi, qualora non interamente distribuiti, non daranno luogo ad economie di fondo ma
            ritorneranno nella disponibilità del bilancio dell’Ente;
             <?php endif;?>
            <br>
            • ai sensi dell’art. xx comma x del CCNL <?php self::getInput('var58', 'xx.xx.xxxx', 'orange'); ?>, le somme
            per € <?php self::getTextArea('area6', '(INSERIRE IL TITOLO TESTO LIBERO SE SONO
            STATE AGGIUNTE ALTRE RISORSE NELLA PARTE VARIABILE)', 'red'); ?>;
            <br>
            <br>
            <?php if (self::checkOptionalValues('R129')): ?>
            • ai sensi dell’art. 67 comma 3 lett. d) CCNL 21.5.2018, le somme una tantum corrispondenti alla frazione di
            RIA, calcolati in misura pari alle mensilità residue dopo la cessazione, computandosi a tal fine, oltre ai
            ratei di tredicesima mensilità, le frazioni di mese superiori a quindici giorni; l’importo confluisce nel
            Fondo dell’anno successivo alla cessazione dal servizio, per un importo pari ad
            € <?php self::getInput('var59', 'R129', 'orange'); ?>;
            <?php endif;?>
            <br>
            <br>
            <?php if (self::checkOptionalValues('R130')): ?>
            • ai sensi dell’art. 67 comma 3 lett. g CCNL 21.5.2018, le somme per gli importi delle risorse destinate ai
            trattamenti economici accessori del personale delle case da gioco secondo le previsioni della legislazione
            vigente e dei relativi decreti ministeriali attuativi, per
            € <?php self::getInput('var59', 'R130', 'orange'); ?>;
            <?php endif;?>
            <br>
            <br>
            <?php if (self::checkOptionalValues('R131')): ?>
            • ai sensi dell’art. 67 comma 3 lett. k CCNL 21.5.2018, le somme per gli importi a seguito dei trasferimenti
            di personale di cui al comma 2 lett. e) ed a fronte della corrispondente riduzione ivi prevista della
            componente variabile dei fondi - limitatamente all’anno in cui avviene il trasferimento, per
            €<?php self::getInput('var60', 'R131', 'orange'); ?> ;
            <?php endif;?>

            <br>
            <br>
             <?php if (self::checkOptionalValues('R131')): ?>
            • ai sensi dell’art. 67 c. 7 e Art.15 c. 7 CCNL 2018 le somme pari alla quota di incremento del Fondo
            trattamento accessorio per riduzione delle risorse destinate alla retribuzione di posizione e di risultato
            delle PO rispetto al tetto complessivo del salario accessorio art. 23 c. 2 D.Lgs... 75/2017, per un importo
            pari a €<?php self::getInput('var60', 'R131', 'orange'); ?> ;
             <?php endif;?>
            <br>
            <br>
            Ritenuto:
            <br>
            <br>
            di integrare le risorse variabili di cui all’art. 67 comma 3 CCNL 21.5.2018, in base alla normativa
            vigente, degli importi NON soggetti al limite del 2016, di cui all’art. 23 del D.Lgs. 75/2017 mediante:
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018, delle somme destinate alle attività
            svolte per conto dell’ISTAT per €<?php self::getInput('var60', 'R39', 'orange'); ?>;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018, delle somme destinate alla “avvocatura”
            per € <?php self::getInput('var61', 'R40', 'orange'); ?>;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018 delle somme finanziate da fondi di
            derivazione dell'Unione Europea per € <?php self::getInput('var62', 'R41', 'orange'); ?>;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. c) del CCNL 21.5.2018 delle somme destinate alle attività
            di recupero IMU e TARI in riferimento dell'art. 1 comma 1091 della L. 145 del 31.12.2018 (Legge di Bilancio
            2019) da distribuire ai sensi del regolamento vigente in materia e nel rispetto della normativa vigente in
            materia per € <?php self::getInput('var63', 'R147', 'orange'); ?>;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018, delle somme destinate alle attività
            <?php self::getTextArea('area7', '(INSERIRE IL TITOLO TESTO LIBERO)', 'red'); ?> per
            €<?php self::getInput('var64', 'R111', 'orange'); ?> ; (Opzionale)
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. c) CCNL 21.5.2018 e dell’art. 16 comma 5 della Legge
            111/2011 relativi a “Piani di razionalizzazione” dell'importo di
            € <?php self::getInput('var65', 'R37', 'orange'); ?>, che dovrà essere distribuito nel
            rigoroso rispetto dei principi introdotti dalla norma vigente e solo se, a consuntivo, verrà espresso parere
            favorevole da parte dell'Organo di Revisione;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. e) CCNL 21.5.2018, delle somme derivanti dai risparmi del
            Fondo lavoro straordinario anno precedente, pari ad €<?php self::getInput('var66', 'R45', 'orange'); ?> ;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 68 comma 1 CCNL 21.5.2018, delle risorse derivanti dai risparmi di parte
            stabile del Fondo risorse decentrate degli anni precedenti, pari ad
            € <?php self::getInput('var67', 'R44', 'orange'); ?>;
            <br>
            <br>
            • iscrizione, ai sensi dell’art. 67 comma 3 lett. a) CCNL 21.5.2018, delle somme derivanti da contratti di
            sponsorizzazione, accordi di collaborazione, convenzioni con soggetti pubblici o privati e contributi
            dell'utenza per servizi pubblici non essenziali, secondo la disciplina dettata dall'art. 43 della Legge
            449/1997 stipulati nel periodo successivo all’entrata in vigore dei limiti per il salario accessorio (2016),
            per € <?php self::getInput('var68', 'R42', 'orange'); ?>, rispettivamente
            per <?php self::getTextArea('area8', ' (INSERIRE IL TITOLO o allegare i file TESTO LIBERO)', 'red'); ?>;
            <br>
            <br>

            • iscrizione, ai sensi 67 comma 3 lett. c) CCNL 21.5.2018, delle somme destinate ai cosiddetti incentivi per
            funzioni tecniche D.Lgs... 50/2016 e ss.mm.ii. per €<?php self::getInput('var69', 'R122', 'orange'); ?> ;
            <br>
            <br>
            • iscrizione ai sensi dell’art. 67, comma 5 lett. b) del CCNL 21.5.2018, della sola quota di maggior incasso
            rispetto all’anno precedente a seguito di obiettivi di potenziamento dei servizi di controllo finalizzati
            alla sicurezza urbana e stradale Art. 56 quater CCNL 2018, così come precisato dalla Corte dei Conti Sezione
            delle Autonomie con delibera n. 5 del 2019, per un importo pari a
            € <?php self::getInput('var70', 'R152', 'orange'); ?>;
            <br>
            <br>
            • iscrizione, ai sensi
            dell’art.<?php self::getInput('var71', 'xx comma x del CCNL xx.xx.xxxx', 'orange'); ?> , delle somme per €
            <?php self::getTextArea('area9', ' (INSERIRE IL TITOLO TESTO
            LIBERO SE SONO STATE AGGIUNTE ALTRE RISORSE NELLA PARTE VARIABILE NON SOTTOPOSTA A BLOCCO)', 'red'); ?>;
            <b>Considerato che:</b>
            <br>
            <br>
            • l'importo totale del fondo delle risorse variabili per l’anno
            .<?php self::getInput('var72', 'anno', 'orange'); ?> risulta pari ad
            € <?php self::getInput('var73', 'f5', 'orange'); ?>, di cui €
            <?php self::getInput('var74', 'f4', 'orange'); ?> soggette ai vincoli;
            <br>
            <br>
            <b> Vista </b>la Legge n. 147/2013 nota Legge di Stabilità 2014, che all'art. 1, comma 456, secondo periodo,
            inserisce all'art. 9 comma 2 bis del DL 78/2010 un nuovo periodo in cui: «A decorrere dal 1º gennaio
            2015, le risorse destinate annualmente al trattamento economico accessorio sono decurtate di un importo
            pari alle riduzioni operate per effetto del precedente periodo», stabilendo così che le decurtazioni
            operate per gli anni 2011/2014 siano confermate e storicizzate nei fondi per gli anni successivi a
            partire dall'anno 2015.
            <br>
            <br>
            <b> Considerato che</b> il D.L. 78/2010, convertito con modificazioni nella legge n. 122/2010 e ssmmii, ha
            previsto per le annualità 2011/2014 limitazioni in materia di spesa per il personale e in particolare
            l'art. 9 comma 2 bis disponeva:
            <br>
            <br>
            • che l'ammontare complessivo delle risorse destinate annualmente al trattamento accessorio del personale,
            anche a livello dirigenziale, non può superare il corrispondente importo dell’anno 2010;
            <br>
            • che l'ammontare complessivo delle risorse destinate annualmente al trattamento accessorio del personale
            è, automaticamente ridotto in misura proporzionale alla riduzione del personale in servizio
            <br>
            <br>
            <b>Vista</b> la circolare n. 20 del 2015 della Ragioneria Generale dello Stato sulle modalità di calcolo
            delle
            decurtazioni per l'anno 2015;
            <br>
            <br>
            <b>Tenuto conto</b> che nel periodo 2011-2014 <?php self::getInput('var74', 'f273', 'orange'); ?>risultano
            decurtazioni rispetto ai vincoli sul fondo 2010 e
            pertanto deve essere applicata la riduzione del fondo del 2022, pari a
            € <?php self::getInput('var75', 'f263', 'red'); ?>;
            <br>
            <br>
            <b>Richiamato</b> l'art. 1 c. 236 della L. 208/2015 che aveva proposto dei nuovi limiti sui fondi delle
            risorse
            decentrate stabilendo che a decorrere dal 1° gennaio 2016 l'ammontare complessivo delle risorse
            destinate annualmente al trattamento accessorio del personale:
            <br>
            <br>
            • non poteva superare il corrispondente importo dell’anno 2015;
            <br>
            • doveva essere automaticamente ridotto in misura proporzionale alla riduzione del personale in servizio,
            tenendo conto del personale assumibile ai sensi della normativa vigente.
            <br>
            <br>
            <b>Visto</b> l'art. 23 del D.Lgs. 75/2017 il quale stabilisce che “a decorrere dal 1° gennaio 2017,
            l'ammontare
            complessivo delle risorse destinate annualmente al trattamento accessorio del personale, anche di
            livello dirigenziale, di ciascuna delle amministrazioni pubbliche di cui all'articolo 1, comma 2, del
            decreto legislativo 30 marzo 2001, n. 165, non può superare il corrispondente importo determinato per
            l'anno 2016. A decorrere dalla predetta data l'articolo 1, comma 236, della legge 28 dicembre 2015, n.
            208 e' abrogato.”
            <br>
            <br>

            <b>Tenuto conto</b> che nell'anno 2016 <?php self::getInput('var76', 'f283', 'orange'); ?>non risultano
            decurtazioni rispetto ai vincoli sul fondo 2015 e pertanto
            non deve essere applicata la riduzione del fondo di
            € <?php self::getInput('var77', 'f282', 'orange'); ?>;
            <br>
            <br>

            <b>Pertanto:</b>
            <br>
            <br>
            • l'importo del fondo complessivo anno da confrontare con il 2016 e da sottoporre alle decurtazioni di cui
            all'art. 23 del D.Lgs... 75/2017, risulta pari a € <?php self::getInput('var78', 'f33', 'orange'); ?> , di
            cui € <?php self::getInput('var79', 'f8', 'orange'); ?> soggette al limite 2016;
            <br>
            <br>
            <b>Vista</b> la costituzione del fondo per l’anno 2016, che per le risorse soggette al limite, risultava
            (con
            esclusione di: avvocatura, ISTAT, di cui art. 67 comma 3 lett. c CCNL 21.5.2018, importi di cui all’art. 67
            comma 3 lett. c CCNL 21.5.2018, importi di cui all’67 comma 3 lett. a, ove tale attività non risulti
            ordinariamente resa dall’Amministrazione precedentemente l’entrata in vigore del D.Lgs... 75/2017, economie
            del fondo dell’anno 2015 e economie del fondo straordinario anno 2015), pari a
            € <?php self::getInput('var80', 'f331', 'orange'); ?>;
            <br>
            e che lo stesso deve essere adeguato in riferimento alle disposizioni del D.L. 34/2019 e di quanto definito
            nel DM attuativo del 17.3.2020 concordato in sede di Conferenza Unificata Stato Regioni del 11.12.2019, per
            garantire l'invarianza del valore medio pro-capite riferito all'anno 2018, per un importo pari ad
            €<?php self::getInput('var81', 'R150', 'orange'); ?> ,
            per un totale del nuovo limite complessivo di cui all'art. 23 del D.Lgs... 75/2017 pari ad
            € <?php self::getInput('var82', 'f373', 'orange'); ?>;
            <br>
            <br>
            e che lo stesso non deve essere adeguato in riferimento alle disposizioni del D.L. 34/2019 e di quanto
            definito DM attuativo del 17.3.2020 concordato in sede di Conferenza Unificata Stato Regioni del 11.12.2019,
            per garantire l'invarianza del valore medio pro-capite riferito all'anno 2018 e pertanto il totale del
            limite di cui all'art. 23 del D.Lgs... 75/2017 è confermato pari ad
            € <?php self::getInput('var83', 'f1', 'orange'); ?>;
            <br><br>
            <b>Vista</b> la costituzione del fondo per l’anno <?php self::getInput('var84', 'anno', 'orange'); ?>, che
            per le risorse soggetto al limite (con esclusione di:
            avvocatura, ISTAT, di cui art. 67 comma 3 lett. c CCNL 21.5.2018, importi di cui all’art. 67 comma 3 lett. c
            CCNL 21.5.2018, importi di cui all’art. 67 comma 3 lett. a, ove tale attività non risulti ordinariamente
            resa dall’Amministrazione precedentemente l’entrata in vigore del D.Lgs. 75/2017, importi di cui all’art. 67
            comma 2 lett. b., art. 79 c. 1 lett. b CCNL 16.11.2022, art. 79 c.1 lett. c CCNL 16.11.2022, art. 79 c.3
            CCNL 16.11.2022, art. 79 c. 5 CCNL 16.11.2022,, economie del fondo dell’anno precedente e economie del fondo
            straordinario anno precedente), adeguato alle disposizioni del DL 34/2019 e di quanto definito DM attuativo
            del 17.3.2020 concordato in sede di Conferenza Unificata Stato Regioni del 11.12.2019, per garantire
            l'invarianza del valore medio pro-capite riferito all'anno 2018, per un importo pari ad € R150, per un
            totale del nuovo limite di cui all'art. 23 del D.Lgs... 75/2017 di
            € <?php self::getInput('var85', 'f8', 'orange'); ?>;
            <br>
            <br>
            <b>Vista</b> la costituzione del fondo per l’anno<?php self::getInput('var86', 'anno', 'orange'); ?>, che
            per le risorse soggetto al limite (con esclusione di:
            avvocatura, ISTAT, di cui art. 67 comma 3 lett. c CCNL 21.5.2018, importi di cui all’art. 67 comma 3 lett. c
            CCNL 21.5.2018, importi di cui all’art. 67 comma 3 lett. a, ove tale attività non risulti ordinariamente
            resa dall’Amministrazione precedentemente l’entrata in vigore del D.Lgs... 75/2017 importi di cui all’art.
            67 comma 2 lett. b., art. 79 c. 1 lett. b CCNL 16.11.2022, art. 79 c.1 lett. c CCNL 16.11.2022, art. 79 c.3
            CCNL 16.11.2022, art. 79 c. 5 CCNL 16.11.2022, economie del fondo dell’anno precedente e economie del fondo
            straordinario anno precedente), risulta pari a € <?php self::getInput('var87', 'f8', 'orange'); ?>;
            <br>
            <br>
            <b>Dato atto</b> che ai sensi dell’art. 33 del DL 34/2019 il salario accessorio complessivo è stato
            incrementato di
            un importo pari a <?php self::getInput('var88', 'R153', 'orange'); ?> di cui:
            <br>
            • Fondo risorse decentrate, come indicato nei paragrafi precedenti per
            € <?php self::getInput('var89', 'R150', 'orange'); ?>;
            <br>
            • Fondo Posizioni organizzative pari a € <?php self::getInput('var90', 'f374', 'orange'); ?>;
            <br>
            <br>
            <b> Considerato che</b>
            <br>
            • il limite di cui all’art. 23 c. 2 del D.Lgs... 75/2017 deve essere rispettato per l’amministrazione nel
            suo
            complesso, in luogo che distintamente per le diverse categorie di personale (es. dirigente e non dirigente)
            che operano nell’amministrazione, così come chiarito da diverse ma costanti indicazioni di sezioni regionali
            della Corte dei Conti e dal MEF e RGS;
            <br>
            • l'Ente si è avvalso della facoltà prevista dall'art. 11-bis comma 2 D.L. 135/2018, che prevede di
            utilizzare
            le facoltà assunzionali per incrementare il fondo delle PO;
            <br>
            <br>
            <b> Preso atto che</b> il fondo anno (per le voci soggette al blocco del D.Lgs... 75/2017) deve essere
            decurtato per
            il superamento del limite del fondo 2016 per un valore pari ad
            € <?php self::getInput('var91', 'f10', 'orange'); ?>;
            <br>
            <br>
            <b> Preso atto che</b> il fondo anno (per le voci soggette al blocco del D.Lgs... 75/2017) non deve essere
            decurtato
            poiché non supera il limite del fondo 2016;
            <br>
            <br>
            <b>Considerato che:</b>
            <br>
            • il totale del fondo (incluse le sole voci soggette al blocco dell’art. 23 del D.Lgs... 75/2017) per l’anno
            <?php self::getInput('var92', 'anno', 'orange'); ?> al netto delle decurtazioni per il superamento del
            valore del 2016 è pari ad €<?php self::getInput('var93', 'f253', 'orange'); ?> ;
            <br>
            • Il totale del fondo complessivo (incluse le voci non soggette al blocco dell’art. 23 del D.Lgs... 75/2017)
            per l’anno <?php self::getInput('var93', 'anno', 'orange'); ?> tolte le decurtazioni per il superamento del
            valore del 2016 è pari ad € <?php self::getInput('var94', 'f254', 'orange'); ?>;
            <br>
            • il tetto del salario accessorio di cui all’art. 23 c. 2 del D.Lgs... 75/2017 nel suo complesso (indennità
            di
            Posizione e Risultato, Fondo risorse decentrate e Fondo straordinario) per
            l’anno <?php self::getInput('var95', 'anno', 'orange'); ?>
            risulta <?php self::getInput('var96', 'inferiore o uguale', 'orange'); ?> al 2016 come illustrato nella
            tabella sotto:
            <br>
            • il tetto del salario accessorio di cui all’art. 23 c. 2 del D.Lgs... 75/2017 nel suo complesso (indennità
            di
            Posizione e Risultato, Fondo risorse decentrate e Fondo straordinario) per
            l’anno <?php self::getInput('var96', 'anno', 'orange'); ?> risulta superiore al
            2016 come illustrato nella tabella sotto:
            <br>
            • <p style="color: red">ATTENZIONE: In caso di superamento del limite si consiglia di intervenire su
                DATEXFONDO e di procedere alla
                verifica e alla regolarizzazione degli importi al fine di garantire il rispetto del limite di cui
                all’art.
                23 c. 2 D.Lgs. 75/2017.</p>
            <br>
            <br>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">TOTALE SALARIO ACCESSORIO per rispetto tetto art. 23 c. 2 del D.Lgs 75/2017</th>
                </tr>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">ANNO 2016</th>
                    <th scope="col">ANNO <?php self::getInput('var98', 'anno', 'orange'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>

                    <td>Fondo complessivo risorse decentrate soggette al limite</td>
                    <td><?php self::getInput('var98', 'f370', 'orange'); ?></td>
                    <td><?php self::getInput('var99', 'f253', 'orange'); ?></td
                    </td>
                </tr>
                <tr>
                    <td>Indennità di Posizione e risultato PO</td>
                    <td>  <?php self::getInput('var100', 'R138', 'orange'); ?></td>
                    <td> <?php self::getInput('var101', 'R141', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Indennità di Posizione e risultato PO anno corrente COMPRESO Quota integrazione PO finanziate
                        dalla rinuncia delle capacità assunzionali (Incremento Art. 11-bis comma 2 D.L. 135/2018) e
                        Quota art. 33 del DL 34/2019
                    </td>
                    <td> <?php self::getInput('var101', 'R138', 'orange'); ?></td>
                    <td> <?php self::getInput('var103', 'R141', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Fondo straordinario
                    </td>
                    <td> <?php self::getInput('var104', 'R157', 'orange'); ?></td>
                    <td> <?php self::getInput('var105', 'R99', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Indennità di Posizione e risultato DIRIGENTI

                    </td>
                    <td> <?php self::getInput('var106', 'R139', 'orange'); ?></td>
                    <td> <?php self::getInput('var107', 'R142', 'orange'); ?></td>
                </tr>
                <tr>
                    <td> Quota di incremento valore medio pro-capite del trattamento accessorio rispetto al 2018 - Art.
                        33 c. 2 DL 34/2019- aumento virtuale limite 2016
                    </td>
                    <td> <?php self::getInput('var108', 'R153', 'orange'); ?></td>
                    <td> <?php self::getInput('var109', '', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b> TOTALE TRATTAMENTO ACCESSORIO SOGGETTO AL LIMITE ART. 23 C. 2 D.LGS. 75/2017</b></td>
                    <td> <?php self::getInput('var110', 'f354', 'orange'); ?></td>
                    <td> <?php self::getInput('var111', 'f355', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b>TOTALE TRATTAMENTO ACCESSORIO SOGGETTO AL LIMITE ART. 23 C. 2 D.LGS. 75/2017 COMPRESO Quota
                            integrazione PO finanziate dalla rinuncia delle capacità assunzionali (Incremento Art.
                            11-bis comma 2 D.L. 135/2018) e Quota art. 33 del DL 34/2019</b></td>
                    <td> <?php self::getInput('var112', 'f354', 'orange'); ?></td>
                    <td> <?php self::getInput('var113', 'f355', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b>Quota integrazione PO finanziate dalla rinuncia delle capacità assunzionali (Incremento Art.
                            11-bis comma 2 D.L. 135/2018)</b></td>
                    <td> <?php self::getInput('var114', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var115', 'f355', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b>RISPETTO DEL LIMITE TRATTAMENTO ACCESSORIO</b></td>
                    <td> <?php self::getInput('var116', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var117', 'f358', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b>RISPETTO DEL LIMITE TRATTAMENTO ACCESSORIO COMPRESO Quota integrazione PO finanziate dalla
                            rinuncia delle capacità assunzionali (Incremento Art. 11-bis comma 2 D.L. 135/2018) e Quota
                            art. 33 del DL 34/2019</b></td>
                    <td> <?php self::getInput('var118', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var119', 'f360', 'orange'); ?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">TOTALE FONDO RISORSE DECENTRATE</th>
                </tr>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">ANNO 2016</th>
                    <th scope="col">ANNO <?php self::getInput('var120', 'anno', 'orange'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>

                    <td>Fondo stabile soggetto al limite</td>
                    <td><?php self::getInput('var121', 'R51', 'orange'); ?></td>
                    <td><?php self::getInput('var122', 'f332', 'orange'); ?></td
                    </td>
                </tr>
                <tr>
                    <td>Fondo variabile soggetta al limite</td>
                    <td> <?php self::getInput('var123', 'R52', 'orange'); ?></td>
                    <td> <?php self::getInput('var124', 'f4', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Incremento valore medio di cui all’art. 33 comma 2 D.L. 34/2019 SOLO FONDO</td>
                    <td> <?php self::getInput('var125', 'R150', 'orange'); ?></td>
                    <td> <?php self::getInput('var126', 'R150', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>RISORSE DECENTRATE
                    </td>
                    <td> <?php self::getInput('var127', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var128', '', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Risorse fondo prima delle decurtazioni</td>
                    <td> <?php self::getInput('var129', 'f371', 'orange'); ?></td>
                    <td> <?php self::getInput('var130', 'f318', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Risorse fondo prima delle decurtazioni
                    </td>
                    <td> <?php self::getInput('var131', 'f371', 'orange'); ?></td>
                    <td> <?php self::getInput('var132', 'f318', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Decurtazioni 2011/2014
                    </td>
                    <td> <?php self::getInput('var133', 'f263', 'orange'); ?></td>
                    <td> <?php self::getInput('var134', 'f263', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Decurtazioni operate nel 2016 per cessazioni e rispetto limite 2015</td>
                    <td> <?php self::getInput('var135', 'f282', 'orange'); ?></td>
                    <td> <?php self::getInput('var136', 'f282', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b>TOTALE FONDO DELL'ANNO PER RISPETTO LIMITE</b></td>
                    <td> <?php self::getInput('var137', 'var63', 'orange'); ?></td>
                    <td> <?php self::getInput('var138', 'var64', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Decurtazioni per rispetto 2016</td>
                    <td> <?php self::getInput('var139', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var140', 'f31', 'orange'); ?></td>
                </tr>
                <tr>
                    <td><b>RISORSE FONDO DOPO LE DECURTAZIONI</b></td>
                    <td> <?php self::getInput('var141', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var142', 'f253', 'orange'); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td> <?php self::getInput('var143', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var144', 'FONDO INCREMENTATO garantendo il rispetto del limite complessivo del salario accessorio come indicato nella tabella precedente
', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Risorse stabili NON sottoposte al limite</td>
                    <td> <?php self::getInput('var145', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var146', 'S1_3', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>Risorse stabili sottoposte al limite</td>
                    <td> <?php self::getInput('var147', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var148', 'S1_3', 'orange'); ?></td>
                </tr>
                <tr>
                    <td>TOTALE FONDO DECURTATO, INCLUSE LE SOMME NON SOTTOPOSTE AL LIMITE</td>
                    <td> <?php self::getInput('var149', '', 'orange'); ?></td>
                    <td> <?php self::getInput('var150', 'f254', 'orange'); ?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <br>
            <b>Preso atto che</b> risulta indisponibile alla contrattazione una quota di
            € <?php self::getInput('var151', 'f93', 'orange'); ?> in quanto relativa alla
            remunerazione di istituti erogabili in forma automatica e già precedentemente contrattati e assegnati
            (es. indennità di comparto e progressione orizzontale);
            <br>
            <br>

            <b>Visto</b> l’allegato prospetto di costituzione del fondo
            anno <?php self::getInput('var152', 'anno', 'orange'); ?>;
            <br>
            <br>
            <b>DETERMINA</b>
            <br>
            <br>
            per quanto in premessa indicato e che qui si intende integralmente richiamato:
            <br>
            <br>

            1. di costituire il fondo risorse decentrate anno <?php self::getInput('var153', 'anno', 'orange'); ?>,
            approvando l’allegato schema di costituzione;
            <br>
            1. di applicare l'art. 23 del D.Lgs. 75/2017 che prevede il “blocco” rispetto al fondo dell'anno 2016 del
            trattamento accessorio, con l’automatica riduzione delle risorse in caso di superamento rispetto all’anno
            2016;
            <br>
            2. di applicare l’art. 33 comma 2, del D.L.34/2019, convertito in Legge 58/2019 (c.d. Decreto “Crescita”)
            che modifica la modalità di calcolo del tetto al salario accessorio introdotto dall'articolo 23, comma 2,
            del D.Lgs. 75/2017, come definito DM attuativo del 17.3.2020 concordato in sede di Conferenza Unificata
            Stato Regioni del 11.12.2019, e che prevede che, a partire dall’anno 2020, il limite del salario accessorio
            debba essere adeguato in aumento rispetto al valore medio pro-capite del 2018, nel caso risulti un
            incremento del numero di dipendenti presenti al 31.12.<?php self::getInput('var154', 'anno', 'orange'); ?>
            rispetto ai presenti al 31.12.2018;
            <br>
            3. di costituire il fondo complessivo a seguito della decurtazione di cui all'art. 23 del D.Lgs. 75/2017 per
            un importo pari ad € <?php self::getInput('var155', 'f254', 'orange'); ?>;
            <br>
            4. di prendere atto che la somma totale risulta stanziata così come segue:
            <?php self::getTextArea('area10', ' per €. XXX Cap. XXX;
            per € xxx al Cap. XX “Fondo miglioramento efficienza” competenza XXXX- impegno XXX/0X;
            etc….', 'orange'); ?>
            <br>
            5. di sottrarre dalle risorse contrattabili i compensi gravanti sul fondo (indennità di comparto, incrementi
            per la progressione economica, ecc.) che, ai sensi delle vigenti disposizioni contrattuali, sono già stati
            erogati in corso d’anno per un importo pari ad € <?php self::getInput('var156', 'f93', 'orange'); ?>;
            <br>
            6. di confermare il Fondo per il Lavoro Straordinario, ai sensi dell'art. 14 CCNL 1.4.1999, per
            l’anno <?php self::getInput('var157', 'anno', 'orange'); ?>
            per un importo pari ad €<?php self::getInput('var158', 'R99', 'orange'); ?> ;
            <br>
            7. che il grado di raggiungimento del Piano delle Performance assegnato nell’anno al Dirigente/Posizioni
            Organizzative, verrà certificato dall’Organismo di Valutazione, che accerterà il raggiungimento degli
            obiettivi ed il grado di accrescimento dei servizi a favore della cittadinanza;
            <br>
            8. che il presente provvedimento diventerà esecutivo solo a seguito dell’apposizione del visto di regolarità
            contabile attestante la copertura finanziaria ai sensi del comma 4 dell'art. 151 del TUEL, D.Lgs... n.
            267/2000, da parte del servizio finanziario cui si trasmette di competenza.
            <br>
            9. <p style="color: red">di trasmettere la presente al Revisore dei Conti per la certificazione di
                competenza.</p>
            10. di trasmettere la presente alle Organizzazioni Sindacali Territoriali e alle RSU per opportuna
            conoscenza e informazione.
            <br>
            <br>


            Il responsabile;
            <br>
            <?php self::getInput('var159', '  ______________________', 'black'); ?>

            <br>
            <br>

            VISTO DI REGOLARITA’ CONTABILE
            <br>
            Si attesta la regolarità contabile e la copertura finanziaria della spesa ai sensi del comma 4 dell'art. 151
            del TUEL, approvato con D.Lgs... n. 267/2000.
            <br>
            <br>

            Il Responsabile
            <br>
            <?php self::getInput('var160', '  ______________________', 'black'); ?>

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