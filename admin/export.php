<?php
// set content type and trigger download
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=newsletter.csv");

// access to database
require_once '../settings.php';

// get mail addresses and convert to csv
$data = Db::getConfirmedUsers();

/* next lines credit to http://stackoverflow.com/a/16508155/1724808 */

// Create a stream opening it with read / write mode
$stream = fopen('data://text/plain,' . "", 'w+');

// Iterate over the data, writting each line to the text stream
foreach ($data as $val) {
    fputcsv($stream, $val);
}

// Rewind the stream
rewind($stream);

// You can now echo it's content
print stream_get_contents($stream);

// Close the stream 
fclose($stream);

