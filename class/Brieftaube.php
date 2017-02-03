<?php
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
        return Email::send($mail,
                _('Anmeldung: ') . Config::get()['webTitle'], $body);
    }
    
    public static function unsubscribe($mail, $key) {
        $body = sprintf(_("Hallo,\n\nbitte bestätige die Abmeldung mit folgendem Link:\n\n%s\n\n%s"), 
                Config::get()['webUrl'] . "?action=confirmunsubscribe&email=$mail&key=$key",
                Config::get()['mailSignature']);
        return Email::send($mail, 
                _('Abmeldung: ') . Config::get()['webTitle'], $body);
    }
    
    public static function welcome($mail) {
        $body = sprintf(_("Hallo,\n\nwillkommen beim Newsletter: %s. Du erhälst nun regelmäßig E-Mails.\n\n%s"),
                Config::get()['webTitle'],
                Config::get()['mailSignature'])
                . self::createFooter($mail);
        return Email::send($mail, Config::get()['webTitle'], $body);
    }
    
    public static function bye($mail) {
        $body = sprintf(_("Hallo,\n\ndie Abmeldung vom Newsletter war erfolgreich.\n\n%s"),
                Config::get()['mailSignature']);
        return Email::send($mail, 
                _('Abgemeldet: ') . Config::get()['webTitle'], $body);
    }
}
