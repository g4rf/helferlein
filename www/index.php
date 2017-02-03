<?php
    require '../settings.php';
?><!DOCTYPE html>
<html><head>
    <title>Brieftaube</title>
    
    <meta charset="UTF-8">
    <meta name="author" content="Jan Kossick, jankossick@online.de">
    
    <style><?=file_get_contents('../main.css')?></style>
</head><body><div id="wrapper">
    <h1><?=Config::get()['webTitle']?></h1>
    <p><?=nl2br(Config::get()['webText'])?></p>
    
    <?php
        $message = '';
        switch (filter_input(INPUT_GET, 'action')) {
            case 'subscribe':
                $key = substr(sha1(mt_rand()), 0, 15);
                $mail = filter_input(INPUT_GET, 'email');
                if(Db::newUser($mail, $key)) {
                    if(Brieftaube::subscribe($mail, $key)) {
                        $message = sprintf(_('Es wurde eine Bestätigungs-E-Mail an'
                            . ' <u>%s</u> geschickt. Bitte folge dem Link'
                            . ' in der E-Mail um den Newsletter zu'
                            . ' abonnieren.'), $mail);
                    } else {
                        $message = _('Es trat ein interner Fehler auf.');
                    }
                } else {
                    $message = _('Die E-Mail-Adresse ist bereits angemeldet.');
                }
                break;
            case 'confirm':
                $mail = filter_input(INPUT_GET, 'email');
                $key = filter_input(INPUT_GET, 'key');
                if(Db::confirmUser($mail, $key)) {
                    Brieftaube::welcome($mail);
                    $message = sprintf(_('Der Newsletter wurde erfolgreich mit'
                            . ' der Adresse <u>%s</u> abonniert'), $mail);                    
                } else {
                    $message = _('Die Bestätigung ist fehlgeschlagen. Entweder'
                            . ' wurde die Adresse bereits bestätigt oder die'
                            . ' Adresse wurde nicht eingetragen.');
                }
                break;
            case 'unsubscribe':
                $mail = filter_input(INPUT_GET, 'email');
                $key = substr(sha1(mt_rand()), 0, 15);
                Db::editUser($mail, 'unsubscribe', $key);
                if(Brieftaube::unsubscribe($mail, $key)) {
                    $message = sprintf(_('Es wurde eine Bestätigungs-E-Mail an'
                        . ' <u>%s</u> geschickt. Bitte folge dem Link'
                        . ' in der E-Mail um den Newsletter abzubestellen.'),
                        $mail);
                } else {
                    $message = _('Es trat ein interner Fehler auf.');
                }                
                break;
            case 'confirmunsubscribe':
                $mail = filter_input(INPUT_GET, 'email');
                $key = filter_input(INPUT_GET, 'key');
                if(Db::confirmUnsubscribe($mail, $key)) {
                    Brieftaube::bye($mail);
                    $message = sprintf(_('Der Newsletter wurde erfolgreich'
                            . ' abbestellt'), $mail);                    
                } else {
                    $message = _('Die Abbestellung ist fehlgeschlagen.'
                            . ' Vermutlich wurde der Newsletter bereits'
                            . ' abbestellt.');
                }
                break;
        }
        
        if(strlen($message)) {
            ?><div class="message"><?=$message?></div><?php
        }
    ?>
    
    <?php if(filter_input(INPUT_GET, 'action') == false) { ?>
        <h3><?=_('Newsletter bestellen')?></h3>
        <form method="get">
            <label>
                <span><?=_('E-Mail')?></span>
                <input type="text" name="email" value="" />
            </label>
            <button type="submit"><?=_('Newsletter bestellen')?></button>
            <input type="hidden" name="action" value="subscribe" />
        </form>
    <?php } ?>
        
    <?php include '../footer.php'; ?>
</div></body></html>
