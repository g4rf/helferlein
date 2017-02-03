<?php
    require '../settings.php';
?><!DOCTYPE html>
<html><head>
    <title>Brieftaube - Admin</title>
    
    <meta charset="UTF-8">
    <meta name="author" content="Jan Kossick, jankossick@online.de">
    
    <style><?=file_get_contents('../main.css')?></style>
</head><body><div id="wrapper">
    
    <div class="menu">
        <a href="?site=mails"><?=_('Newsletter')?></a>
        <a href="?site=users"><?=_('E-Mail-Adressen')?></a>
        <a href="?site=config"><?=_('Konfiguration')?></a>
    </div>
    
    <?php 
        $include = filter_input(INPUT_GET, 'site') . ".php";
        if(file_exists($include)) include($include);
    ?>
    
</div></body></html>
