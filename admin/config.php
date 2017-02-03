<h1><?=_('Konfiguration')?></h1>

<p>
    <?=_('Zur Zeit wird nur SMTP über <b>SSL</b> unterstützt.')?>
</p>
<?php
$divider = '@---@';
$configKeys = array(
    'webTitle' => _('Titel für die Webseite'),
    'webText' => _('Text für die Webseite'),
    $divider,
    'mailSignature' => _('E-Mail-Signatur'),
    $divider,
    'webUrl' => _('URL zur Seite'),
    $divider,
    'dbServer' => _('Datenbankserver'), 
    'dbPort' => _('Datenbankport'), 
    'dbUser' => _('Benutzername'), 
    'dbPassword' => _('Passwort'),
    $divider, 
    'smtpServer' => _('SMTP-Server'), 
    'smtpPort' => _('SMTP-Port'), 
    'smtpUser' => _('Benutzername'), 
    'smtpPassword' => _('Passwort'),
    'smtpFrom' => _('Antwortadresse')
);
$message = '';
switch(filter_input(INPUT_POST, 'action')) {
    case 'save':
        foreach($configKeys as $k => $v) {
            if($v == $divider) continue; // it's a divider
            Config::set($k, filter_input(INPUT_POST, $k));
        }
        
        $message = _('Daten gespeichert.');
        break;
    case 'testSmtp':
        foreach($configKeys as $k => $v) {
            if($v == $divider) continue; // it's a divider
            Config::set($k, filter_input(INPUT_POST, $k));
        }
        if(Brieftaube::send(Config::get()['smtpFrom'],
                _('Brieftaube: Test der SMTP-Verbindung'),
                _('Wenn diese E-Mail angekommen ist, ist die SMTP-Verbindung'
                        . ' korrekt eingerichtet. \o/'),
                $error)) {
            $message = sprintf(_('Der Server meldete keine Fehlermeldung. Bitte'
                    . ' prüfe das Postfach <u>%s</u> auf den Eingang der'
                    . ' Testmail.'), Config::get()['smtpFrom']);
        } else {
            $message = _('Ein Fehler ist aufgetreten: ') . $error;
        }
        break;
}
if(strlen($message)) {
    ?><div class="message"><?=$message?></div><?php
}
?>

<hr />

<form method="post">
    <?php foreach($configKeys as $k => $v) {
        if($v == $divider) {
            ?><hr /><?php
        } else { ?>
            <label>
                <span><?=$v?></span>
                <?php if (preg_match('/text|signature/Ui', $k)) { ?>
                    <textarea name="<?=$k?>"><?=Config::get()[$k]?></textarea>
                <?php } elseif (preg_match('/password/Ui', $k)) { ?>
                    <input type="password" name="<?=$k?>" 
                           value="<?=Config::get()[$k]?>" />
                <?php } else { ?>
                    <input type="text" name="<?=$k?>"
                           value="<?=Config::get()[$k]?>" />
                <?php } ?>
            </label><?php
        }
    } ?>

    <hr />

    <button type="submit"><?=_('Speichern')?></button>
    <input type="hidden" name="action" value="save" />
</form>

<form method="post">
    <button type="submit"><?=_('SMTP testen')?></button>
    <span><?=_('Dies sendet eine Test-Nachricht an die im Feld <i>Antwortadresse</i> angegebene E-Mail-Adresse.')?></span>
    <input type="hidden" name="action" value="testSmtp" />
</form>