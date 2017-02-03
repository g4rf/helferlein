<h1><?=_('E-Mail-Adressen')?></h1>

<?php
$action = filter_input(INPUT_POST, 'action');
$message = '';
switch($action) {
    case 'delete':
        Db::deleteUser(filter_input(INPUT_POST, 'email'));
        $message = sprintf(_('<u>%s</u> erfolgreich gelöscht.'), 
                filter_input(INPUT_POST, 'email'));
        break;
    case 'mute':
        Db::muteUser(filter_input(INPUT_POST, 'email'));
        $message = sprintf(_('<u>%s</u> erhält nun keine Newsletter mehr.'), 
                filter_input(INPUT_POST, 'email'));
        break;
    case 'unmute':
        Db::unmuteUser(filter_input(INPUT_POST, 'email'));
        $message = sprintf(_('<u>%s</u> erhält nun wieder die Newsletter.'), 
                filter_input(INPUT_POST, 'email'));
        break;
}
if(strlen($message)) {
    ?><div class="message"><?=$message?></div><?php
}
?>
    
<form method="post" action="export.php">
    <button type="submit">
        <?=_('Exportiere bestätigte E-Mail-Adressen als CSV')?>
    </button>
</form>

<table>
    <tr>
        <th><?=_('E-Mail-Adresse')?></th>
        <th><?=_('Bestätigt?')?></th>
        <th><?=_('Pausiert?')?></th>
        <th><?=_('Aktionen')?></th>
    </tr>
    
    <?php
    $countUsers = 0;
    $countConfirmed = 0;
    $countMuted = 0;
    
    foreach(Db::getUsers() as $user) {
        $confirmed = $user['Unconfirmed'] == "0";
        $muted = $user['Mute'] == "1";
        
        $countUsers++;
        if($confirmed) $countConfirmed++;
        if($muted) $countMuted++; ?>
    <tr class="<?=$confirmed ? '' : 'unconfirmed'?> <?=$muted ? 'muted' : ''?>">
        <td><?=$user['Email']?></td>
        <td><?=$confirmed ? 'ja' : 'nein'?></td>
        <td><?=$muted ? 'ja' : 'nein'?></td>
        <td class="action">
            <form method="post">
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="email" value="<?=$user['Email']?>" />
                <button type="submit" class="danger"><?=_('Löschen')?></button>
            </form>
            <?php if($confirmed) { ?>
                <form method="post">
                    <input type="hidden" name="action" 
                           value="<?=$muted ? 'unmute' : 'mute'?>" />
                    <input type="hidden" name="email" value="<?=$user['Email']?>" />
                    <button type="submit"><?=
                        $muted ? _('Aktivieren') : _('Pausieren')
                    ?></button>
                </form>
            <?php } ?>
        </td></tr>
    <?php } ?>
</table>

<h3><?=_('Statistik')?>:</h3>
<ul>
    <li><?=_('Insgesamt')?>: <?=$countUsers?></li>
    <li><?=_('Bestätigt')?>: <?=$countConfirmed?></li>
    <li><?=_('Pausiert')?>: <?=$countMuted?></li>
    <li><?=_('Empfangsbereit')?>: <?=$countConfirmed - $countMuted?></li>
</ul>