<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



function inviaEmail($destinatario)
{
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Configura il server SMTP
        //le variabili sono nel file wp-config
        $mail->isSMTP();
        $mail->Host = WPMS_SMTP_HOST;
        $mail->SMTPAuth = WPMS_SMTP_AUTH;
        $mail->Username = WPMS_SMTP_USER;
        $mail->Password = WPMS_SMTP_PASS;
        $mail->SMTPSecure = WPMS_SSL;
        $mail->Port = WPMS_SMTP_PORT;

        // Configura il mittente e il destinatario
        $mail->setFrom(WPMS_SMTP_USER, 'DemoDateXFondo');
        $mail->addAddress($destinatario, 'Maria Antonietta Bruno');

        // Aggiungi il corpo del messaggio
        $mail->isHTML(true);
        $mail->Subject = 'Email di prova dateXFondo';
        $mail->Body = "Gentile Cliente,
<br>
Ti stiamo contattando per informarti che il tuo account sta per scadere. Per evitare interruzioni di servizio e garantire la continuità del tuo accesso, ti invitiamo ad agire tempestivamente.
<br>
<br>
Dettagli dell'account:
<br>
- Nome utente: [Tuo Nome Utente]
<br>
- Data di scadenza dell'account: [Data di Scadenza]
<br>
<br>
Per rinnovare il tuo account o estendere la data di scadenza, ti preghiamo di contattare:
<br>
Se hai domande o hai bisogno di assistenza, non esitare a contattare il nostro servizio clienti all'indirizzo [indirizzo_email@tuo_sito.com] o al numero di telefono [tuo_numero_di_telefono].
<br><br>

Grazie per la tua collaborazione.
<br>
<br>
Cordiali saluti,
<br>
Il Team di FondoWeb";

        // Invia l'email
        $mail->send();
       // echo 'Email inviata con successo';
    } catch (Exception $e) {
        //echo "Errore nell'invio dell'email: {$mail->ErrorInfo}";
    }
}