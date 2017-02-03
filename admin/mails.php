<h1><?=_('Newsletter')?></h1>

<?php
$action = filter_input(INPUT_POST, 'action');
$message = '';
$editMail = false;
switch($action) {
    case 'new':
        Db::newMail(
                filter_input(INPUT_POST, 'id'),
                filter_input(INPUT_POST, 'subject'),
                filter_input(INPUT_POST, 'body'));
        $message = sprintf(_('Newsletter <u>%s</u> angelegt.'), 
                filter_input(INPUT_POST, 'subject'));
        break;
    case 'startedit':
        $editMail = Db::getMail(filter_input(INPUT_POST, 'id'));
        break;
    case 'doedit':
        Db::editMail(
                filter_input(INPUT_POST, 'id'),
                filter_input(INPUT_POST, 'subject'),
                filter_input(INPUT_POST, 'body'));
        $message = sprintf(_('Newsletter <u>%s</u> geändert.'), 
                filter_input(INPUT_POST, 'subject'));
        break;
    case 'delete':
        Db::deleteMail(filter_input(INPUT_POST, 'id'));
        $message = sprintf(_('<u>%s</u> erfolgreich gelöscht.'), 
                filter_input(INPUT_POST, 'subject'));
        break;
}
if(strlen($message)) {
    ?><div class="message"><?=$message?></div><?php
}
?>

<h2><?=$editMail == false ? _('Neuer Newsletter') : _('Bearbeite Newsletter')?></h2>
<form method="post">
    <label>
        <span><?=_('Betreff')?></span>
        <input type="text" name="subject" 
               value="<?=$editMail == false ? '' : $editMail['Subject']?>"
               <?=$editMail == false ? '' : 'autofocus'?> />
    </label>
    <label>
        <span><?=_('Text')?></span>
        <textarea name="body"><?=
            $editMail == false ? '' : $editMail['Body']
        ?></textarea>
    </label>
    
    <button type="submit"><?=
        $editMail == false ? _('Speichern') : _('Bearbeiten')?>
    </button>
    
    <input type="hidden" name="action" 
           value="<?=$editMail == false ? 'new' : 'doedit'?>" />
    <input type="hidden" name="id" 
           value="<?=$editMail == false ? 0 : $editMail['Id']?>" />
</form>

<hr />

<h2><?=_('Alle Newsletter')?></h2>
<table>
    <tr>
        <th><?=_('Datum')?></th>
        <th><?=_('Betreff')?></th>
        <th><?=_('Text')?></th>
        <th><?=_('Status')?></th>
        <th><?=_('Aktionen')?></th>
    </tr>
    
    <?php foreach(Db::getMails() as $mail) { ?>
    <tr>
        <td><?php
            $date = new DateTime($mail['Datetime']);
            print $date->format('D, j. M y');
        ?></td>
        <td><?=$mail['Subject']?></td>
        <td><?=substr(str_replace("\n", '', $mail['Body']), 0, 100)?></td>
        <td><?=$mail['Status']?></td>
        <td class="action">
            <form method="post">
                <input type="hidden" name="action" value="startedit" />
                <input type="hidden" name="id" value="<?=$mail['Id']?>" />
                <button type="submit"><?=_('Bearbeiten')?></button>
            </form>
            <form method="post">
                <input type="hidden" name="action" value="delete" />
                <input type="hidden" name="id" value="<?=$mail['Id']?>" />
                <input type="hidden" name="subject" value="<?=$mail['Subject']?>" />
                <button type="submit" class="danger"><?=_('Löschen')?></button>
            </form>
        </td></tr>
    <?php } ?>
</table>