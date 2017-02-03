<?php
require_once 'Mail.php';

class Brieftaube {
    private static function createFooter($mail) {
        return "\n\n\n---\n" . _("Abmeldung") . ": " . Config::get()['webUrl']
                . "?action=unsubscribe&email=$mail";
    }
    
    public static function subscribe($mail, $key) {
        $body = sprintf(_("Hallo,\n\ndiese E-Mail wurde für folgenden Newsletter eingetragen:\n\n%s\n\nBitte rufe den folgenden Link auf, um das Abonemment zu bestätigen:\n\n%s\n\n%s"), 
                Config::get()['webTitle'],
                Config::get()['webUrl'] . "?action=confirm&email=$mail&key=$key",
                Config::get()['mailSignature']);
        return self::send($mail,
                _('Anmeldung: ') . Config::get()['webTitle'], $body);
    }
    
    public static function unsubscribe($mail, $key) {
        $body = sprintf(_("Hallo,\n\nbitte bestätige die Abmeldung mit folgendem Link:\n\n%s\n\n%s"), 
                Config::get()['webUrl'] . "?action=confirmunsubscribe&email=$mail&key=$key",
                Config::get()['mailSignature']);
        return self::send($mail, 
                _('Abmeldung: ') . Config::get()['webTitle'], $body);
    }
    
    public static function welcome($mail) {
        $body = sprintf(_("Hallo,\n\nwillkommen beim Newsletter: %s. Du erhälst nun regelmäßig E-Mails.\n\n%s"),
                Config::get()['webTitle'],
                Config::get()['mailSignature'])
                . self::createFooter($mail);
        return self::send($mail, Config::get()['webTitle'], $body);
    }
    
    public static function bye($mail) {
        $body = sprintf(_("Hallo,\n\ndie Abmeldung vom Newsletter war erfolgreich.\n\n%s"),
                Config::get()['mailSignature']);
        return self::send($mail, 
                _('Abgemeldet: ') . Config::get()['webTitle'], $body);
    }
    
    public static function send($to, $subject, $body, &$error = '') {
        $replyTo = Config::get()['smtpFrom'];
        $from = Config::get()['smtpUser'];; // could be recognised as spam: Config::get()['smtpFrom'];
        $host = 'ssl://' . Config::get()['smtpServer'];
        $port = Config::get()['smtpPort'];
        $username = Config::get()['smtpUser'];
        $password = Config::get()['smtpPassword'];

        $headers = array(
            'From' => $from, 
            'To' => $to, 
            'Subject' => $subject,
            'Reply-To' => $replyTo,
            'charset' => 'UTF-8');
        $smtp = Mail::factory('smtp',
            array ('host' => $host, 'port' => $port, 'auth' => true,
                'username' => $username, 'password' => $password)
        );
        $mail = $smtp->send($to, $headers, $body);

        if (PEAR::isError($mail)) {
            $error = $mail->getMessage();
            return false;
         } else {
            return true;
         }
    }
}
