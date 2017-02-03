<?php
require_once 'Mail.php';

class Email {
    public static function send($to, $subject, $body, &$error = '') {
        $replyTo = Config::get()['smtpFrom'];
        $from = Config::get()['smtpUser']; // could be recognised as spam: Config::get()['smtpFrom'];
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