<?php

namespace dateXFondoPlugin;

use DateTime;

class EnteMailSender
{
    public static function render()
    {
        $data = new MailSenderRepository();
        $results_infos = $data->getExpireDate();

        date_default_timezone_set("Europe/Rome");
        $data_odiernea = date("Y-m-d");
        $data2 = new DateTime($data_odiernea);
        $array_enti = [];
        $array_mail_consulenti = [];
        $array_mail_utenti = [];
        $array_enti_scadenza = [];
        $array_enti_scaduti = [];

        for ($i = 0; $i <= sizeof($results_infos); $i++) {
            if ($results_infos[$i]['data_scadenza'] != '') {
                $data1 = new DateTime($results_infos[$i]['data_scadenza']);
                $differenza = $data1->diff($data2);
                $giorni_di_differenza = $differenza->days;
                if ($giorni_di_differenza === 0) {
                    array_push($array_enti_scaduti, $results_infos[$i]['id']);
                }
                if ($giorni_di_differenza <= 60 && $giorni_di_differenza > 30 && $results_infos[$i]['giorni_scadenza'] == 0) {
                    $utente_info = get_userdata($results_infos[$i]['id_consulente']);
                    $email_utente = $utente_info->get('user_email');
                    array_push($array_mail_consulenti, $email_utente);
                    array_push($array_enti_scadenza, $results_infos[$i]['id']);
                } else if ($giorni_di_differenza <= 30 && $results_infos[$i]['giorni_scadenza'] == 0) {
                    array_push($array_enti, $results_infos[$i]['id']);
                    array_push($array_enti_scadenza, $results_infos[$i]['id']);
                }
            }
        }
        for ($i = 0; $i <= sizeof($array_mail_consulenti); $i++) {
            if ($array_mail_consulenti[$i] != '')
                inviaEmail($array_mail_consulenti[$i]);
        }

        $array_ids = $data->getUserID($array_enti);

        for ($i = 0; $i <= sizeof($array_ids); $i++) {
            if ($array_ids[$i]['id_user'] != '') {
                $utente_info = get_userdata($array_ids[$i]['id_user']);
                $ruoli_utente = $utente_info->roles;

                if ($ruoli_utente[0] === 'contributor') {
                    $email_utente = $utente_info->get('user_email');
                    array_push($array_mail_utenti, $email_utente);
                }
            }

        }

        for ($i = 0; $i <= sizeof($array_mail_utenti); $i++) {
            if ($array_mail_utenti[$i] != '')
                inviaEmail($array_mail_utenti[$i]);
        }

        $data->updateExpireDays($array_enti_scadenza);
        $data->updateCitiesStatus($array_enti_scaduti);

        //per disabilitare gli utenti "ente" contributori e sottoscrittori quando la data di scadenza è superata
        $array_user_scaduti = $data->getUserID($array_enti_scaduti);

        for ($i = 0; $i <= sizeof($array_user_scaduti); $i++) {
            $utente_info = get_userdata($array_user_scaduti[$i]);
            $ruoli_utente = $utente_info->roles;

            if ($ruoli_utente[0] === 'contributor' || $ruoli_utente[0] === 'subscriber') {
                wp_update_user(array('ID' => $array_user_scaduti[$i], 'user_status' => 0));
            }
        }


    }
}