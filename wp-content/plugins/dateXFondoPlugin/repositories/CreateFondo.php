<?php

namespace dateXFondoPlugin;

use mysqli;

class CreateFondo

{
    private $titolo_fondo;
    private $anno;
    private $ente;
    private $descrizione;
    private $fondo_di_riferimento;
    private $data_libera_approvazione_bilancio;
    private $numero_delibera_approvazione_PEG;
    private $numero_delibera_approvazione_PEG_e_performance;
    private $numero_delibera_approvazione_razionalizzazione;
    private $numero_delibera_costituzione_fondo;
    private $numero_delibera_indirizzo_costituzione_contrattazione;
    private $principio_riduzione_spesa_personale;
    private $modello_di_riferimento;
    private $numero_delibera_approvazione_bilancio;
    private $responsabile;
    private $data_delibera_di_approvazione;
    private $data_delibera_di_nomina;
    private $data_delibera;
    private $data_delibera_di_costituzione;
    private $data_delibera_indirizzo_anno_corrente;
    private $ufficiale;
    private $nome_soggetto_deliberante;

    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getNomeSoggettoDeliberante()
    {
        return $this->nome_soggetto_deliberante;
    }

    /**
     * @param mixed $nome_soggetto_deliberante
     */
    public function setNomeSoggettoDeliberante($nome_soggetto_deliberante): void
    {
        $this->nome_soggetto_deliberante = $nome_soggetto_deliberante;
    }

    /**
     * @return mixed
     */
    public function getTitoloFondo()
    {
        return $this->titolo_fondo;
    }

    /**
     * @param mixed $titolo_fondo
     */
    public function setTitoloFondo($titolo_fondo): void
    {
        $this->titolo_fondo = $titolo_fondo;
    }

    /**
     * @return mixed
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * @param mixed $anno
     */
    public function setAnno($anno): void
    {
        $this->anno = $anno;
    }

    /**
     * @return mixed
     */
    public function getEnte()
    {
        return $this->ente;
    }

    /**
     * @param mixed $ente
     */
    public function setEnte($ente): void
    {
        $this->ente = $ente;
    }

    /**
     * @return mixed
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * @param mixed $descrizione
     */
    public function setDescrizione($descrizione): void
    {
        $this->descrizione = $descrizione;
    }

    /**
     * @return mixed
     */
    public function getFondoDiRiferimento()
    {
        return $this->fondo_di_riferimento;
    }

    /**
     * @param mixed $fondo_di_riferimento
     */
    public function setFondoDiRiferimento($fondo_di_riferimento): void
    {
        $this->fondo_di_riferimento = $fondo_di_riferimento;
    }

    /**
     * @return mixed
     */
    public function getDataLiberaApprovazioneBilancio()
    {
        return $this->data_libera_approvazione_bilancio;
    }

    /**
     * @param mixed $data_libera_approvazione_bilancio
     */
    public function setDataLiberaApprovazioneBilancio($data_libera_approvazione_bilancio): void
    {
        $this->data_libera_approvazione_bilancio = $data_libera_approvazione_bilancio;
    }

    /**
     * @return mixed
     */
    public function getNumeroDeliberaApprovazionePEG()
    {
        return $this->numero_delibera_approvazione_PEG;
    }

    /**
     * @param mixed $numero_delibera_approvazione_PEG
     */
    public function setNumeroDeliberaApprovazionePEG($numero_delibera_approvazione_PEG): void
    {
        $this->numero_delibera_approvazione_PEG = $numero_delibera_approvazione_PEG;
    }

    /**
     * @return mixed
     */
    public function getNumeroDeliberaApprovazionePEGEPerformance()
    {
        return $this->numero_delibera_approvazione_PEG_e_performance;
    }

    /**
     * @param mixed $numero_delibera_approvazione_PEG_e_performance
     */
    public function setNumeroDeliberaApprovazionePEGEPerformance($numero_delibera_approvazione_PEG_e_performance): void
    {
        $this->numero_delibera_approvazione_PEG_e_performance = $numero_delibera_approvazione_PEG_e_performance;
    }

    /**
     * @return mixed
     */
    public function getNumeroDeliberaApprovazioneRazionalizzazione()
    {
        return $this->numero_delibera_approvazione_razionalizzazione;
    }

    /**
     * @param mixed $numero_delibera_approvazione_razionalizzazione
     */
    public function setNumeroDeliberaApprovazioneRazionalizzazione($numero_delibera_approvazione_razionalizzazione): void
    {
        $this->numero_delibera_approvazione_razionalizzazione = $numero_delibera_approvazione_razionalizzazione;
    }

    /**
     * @return mixed
     */
    public function getNumeroDeliberaCostituzioneFondo()
    {
        return $this->numero_delibera_costituzione_fondo;
    }

    /**
     * @param mixed $numero_delibera_costituzione_fondo
     */
    public function setNumeroDeliberaCostituzioneFondo($numero_delibera_costituzione_fondo): void
    {
        $this->numero_delibera_costituzione_fondo = $numero_delibera_costituzione_fondo;
    }

    /**
     * @return mixed
     */
    public function getNumeroDeliberaIndirizzoCostituzioneContrattazione()
    {
        return $this->numero_delibera_indirizzo_costituzione_contrattazione;
    }

    /**
     * @param mixed $numero_delibera_indirizzo_costituzione_contrattazione
     */
    public function setNumeroDeliberaIndirizzoCostituzioneContrattazione($numero_delibera_indirizzo_costituzione_contrattazione): void
    {
        $this->numero_delibera_indirizzo_costituzione_contrattazione = $numero_delibera_indirizzo_costituzione_contrattazione;
    }

    /**
     * @return mixed
     */
    public function getPrincipioRiduzioneSpesaPersonale()
    {
        return $this->principio_riduzione_spesa_personale;
    }

    /**
     * @param mixed $principio_riduzione_spesa_personale
     */
    public function setPrincipioRiduzioneSpesaPersonale($principio_riduzione_spesa_personale): void
    {
        $this->principio_riduzione_spesa_personale = $principio_riduzione_spesa_personale;
    }

    /**
     * @return mixed
     */
    public function getModelloDiRiferimento()
    {
        return $this->modello_di_riferimento;
    }

    /**
     * @param mixed $modello_di_riferimento
     */
    public function setModelloDiRiferimento($modello_di_riferimento): void
    {
        $this->modello_di_riferimento = $modello_di_riferimento;
    }

    /**
     * @return mixed
     */
    public function getNumeroDeliberaApprovazioneBilancio()
    {
        return $this->numero_delibera_approvazione_bilancio;
    }

    /**
     * @param mixed $numero_delibera_approvazione_bilancio
     */
    public function setNumeroDeliberaApprovazioneBilancio($numero_delibera_approvazione_bilancio): void
    {
        $this->numero_delibera_approvazione_bilancio = $numero_delibera_approvazione_bilancio;
    }

    /**
     * @return mixed
     */
    public function getResponsabile()
    {
        return $this->responsabile;
    }

    /**
     * @param mixed $responsabile
     */
    public function setResponsabile($responsabile): void
    {
        $this->responsabile = $responsabile;
    }

    /**
     * @return mixed
     */
    public function getDataDeliberaDiApprovazione()
    {
        return $this->data_delibera_di_approvazione;
    }

    /**
     * @param mixed $data_delibera_di_approvazione
     */
    public function setDataDeliberaDiApprovazione($data_delibera_di_approvazione): void
    {
        $this->data_delibera_di_approvazione = $data_delibera_di_approvazione;
    }

    /**
     * @return mixed
     */
    public function getDataDeliberaDiNomina()
    {
        return $this->data_delibera_di_nomina;
    }

    /**
     * @param mixed $data_delibera_di_nomina
     */
    public function setDataDeliberaDiNomina($data_delibera_di_nomina): void
    {
        $this->data_delibera_di_nomina = $data_delibera_di_nomina;
    }

    /**
     * @return mixed
     */
    public function getDataDelibera()
    {
        return $this->data_delibera;
    }

    /**
     * @param mixed $data_delibera
     */
    public function setDataDelibera($data_delibera): void
    {
        $this->data_delibera = $data_delibera;
    }

    /**
     * @return mixed
     */
    public function getDataDeliberaDiCostituzione()
    {
        return $this->data_delibera_di_costituzione;
    }

    /**
     * @param mixed $data_delibera_di_costituzione
     */
    public function setDataDeliberaDiCostituzione($data_delibera_di_costituzione): void
    {
        $this->data_delibera_di_costituzione = $data_delibera_di_costituzione;
    }

    /**
     * @return mixed
     */
    public function getDataDeliberaIndirizzoAnnoCorrente()
    {
        return $this->data_delibera_indirizzo_anno_corrente;
    }

    /**
     * @param mixed $data_delibera_indirizzo_anno_corrente
     */
    public function setDataDeliberaIndirizzoAnnoCorrente($data_delibera_indirizzo_anno_corrente): void
    {
        $this->data_delibera_indirizzo_anno_corrente = $data_delibera_indirizzo_anno_corrente;
    }

    /**
     * @return mixed
     */
    public function getUfficiale()
    {
        return $this->ufficiale;
    }

    /**
     * @param mixed $ufficiale
     */
    public function setUfficiale($ufficiale): void
    {
        $this->ufficiale = $ufficiale;
    }

    public function checkIfTableExist($tablename_fondo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $val = mysqli_query("select 1 from $tablename_fondo LIMIT 1");
        mysqli_close($mysqli);
        return $val;

    }

    public function createNewTableFondo($tablename_fondo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "CREATE TABLE $tablename_fondo(
        id INT(2)  PRIMARY KEY, 
        titolo_fondo VARCHAR(255) NOT NULL,
        anno VARCHAR(255) NOT NULL,
        ente VARCHAR(255) NOT NULL,
        descrizione VARCHAR(255),
        fondo_di_riferimento VARCHAR(255),
        data_libera_approvazione_bilancio VARCHAR(255),
        numero_delibera_approvazione_PEG VARCHAR(255),
        numero_delibera_approvazione_PEG_e_performance VARCHAR(255),
        numero_delibera_approvazione_razionalizzazione VARCHAR(255),
        numero_delibera_costituzione_fondo VARCHAR(255),
        numero_delibera_indirizzo_costituzione_contrattazione VARCHAR(255),
        principio_riduzione_spesa_personale VARCHAR(255),
        modello_di_riferimento VARCHAR(255),
        numero_delibera_approvazione_bilancio VARCHAR(255),
        responsabile VARCHAR(255),
        data_delibera_di_approvazione VARCHAR(255),
        data_delibera_di_nomina VARCHAR(255),
        data_delibera VARCHAR(255),
        data_delibera_di_costituzione VARCHAR(255),
        data_delibera_di_indirizzo_costituzione_anno_corrente VARCHAR(255),
        ufficiale VARCHAR(255)
        )";
        if ($mysqli->query($sql) === TRUE) {

        } else {
            $mysqli->error;
        }
        $sql = "ALTER TABLE $tablename_fondo
MODIFY id INT NOT NULL AUTO_INCREMENT";
        $mysqli->query($sql);


        mysqli_close($mysqli);
    }

    public function insertDataFondo($tablename_fondo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $stmt = "INSERT INTO $tablename_fondo (titolo_fondo, anno, ente,descrizione,fondo_di_riferimento,
                   data_libera_approvazione_bilancio,numero_delibera_approvazione_PEG,numero_delibera_approvazione_PEG_e_performance,
                   numero_delibera_approvazione_razionalizzazione,numero_delibera_costituzione_fondo,
                   numero_delibera_indirizzo_costituzione_contrattazione,principio_riduzione_spesa_personale,modello_di_riferimento,
                   numero_delibera_approvazione_bilancio,responsabile, data_delibera_di_approvazione,data_delibera_di_nomina,
                   data_delibera,data_delibera_di_costituzione,data_delibera_di_indirizzo_costituzione_anno_corrente,ufficiale) VALUES (?, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($stmt);
        $stmt->bind_param("sisssssssssssssssssss", $this->titolo_fondo, $this->anno, $this->ente, $this->descrizione, $this->fondo_di_riferimento,
            $this->data_libera_approvazione_bilancio, $this->numero_delibera_approvazione_PEG, $this->numero_delibera_approvazione_PEG_e_performance, $this->numero_delibera_approvazione_razionalizzazione,
            $this->numero_delibera_costituzione_fondo, $this->numero_delibera_indirizzo_costituzione_contrattazione, $this->principio_riduzione_spesa_personale, $this->modello_di_riferimento,
            $this->numero_delibera_approvazione_bilancio, $this->responsabile, $this->data_delibera_di_approvazione, $this->data_delibera_di_nomina, $this->data_delibera,
            $this->data_delibera_di_costituzione, $this->data_delibera_indirizzo_anno_corrente, $this->ufficiale);
        $res = $stmt->execute();

        mysqli_close($mysqli);

    }


}