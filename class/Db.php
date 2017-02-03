<?php
/**
 * This class talks to the DBMS.
 */
class Db {
    private static $mysqli;
    
    public static function initialize() {
        self::$mysqli = new mysqli(
                Config::get()['dbServer'],
                Config::get()['dbUser'],
                Config::get()['dbPassword'],
                'brieftaube',
                Config::get()['dbPort']);
        self::$mysqli->query('SET NAMES "utf8"');
    }  
   
    /*** Public Functions ***/
    
    /**
     * Escapes a string for use in an MySQL query.
     * @param string $v String to escape.
     * @return string Escaped string.
     */
    public static function sl($v) {
        return self::$mysqli->real_escape_string($v);
    }

    /**
     * Gets the auto increment id from the last operation, if any.
     * @return mixed The id.
     */
    public static function getLastId() {
        return self::$mysqli->insert_id;
    }

    /**
     * Returns the last error.
     * @return string The error.
     */
    public static function getLastError() {
        return self::$mysqli->error;
    }

    /**
     * Returns the last error number.
     * @return integer The error number.
     */
    public static function getLastErrorNo() {
        return self::$mysqli->errno;
    }
    
    public static function log($m) {
        self::res("INSERT INTO log SET Message='" . self::sl($m) . "'");
    }
    
    public static function getUsers() {
        return self::table('SELECT * FROM users');
    }
    
    public static function getConfirmedUsers() {
        return self::table('SELECT Email FROM users WHERE Unconfirmed="0"');
    }

    public static function newUser($mail, $key) {
        return self::res("INSERT INTO users SET"
                . " Email='" . self::sl($mail) . "',"
                . " Unconfirmed='" . self::sl($key) . "'");
    }
    
    public static function editUser($mail, $key, $value) {
        self::res("UPDATE users SET"
                . " `" . self::sl($key) . "`='" . self::sl($value) . "'"
                . " WHERE Email='" . self::sl($mail) . "'");
    }
    
    public static function confirmUser($mail, $key) {
        $user = self::single("SELECT Email FROM users"
                . " WHERE Email='" . self::sl($mail) . "'"
                . "   AND Unconfirmed='" . self::sl($key) . "'");
        if($user == false) return false;
        self::editUser($user, 'Unconfirmed', '0');
        return $user;
    }
    
    public static function confirmUnsubscribe($mail, $key) {
        $user = self::single("SELECT Email FROM users"
                . " WHERE Email='" . self::sl($mail) . "'"
                . "   AND Unsubscribe='" . self::sl($key) . "'");
        if($user == false) return false;
        self::deleteUser($user);
        return $user;
    }
    
    public static function deleteUser($mail) {
        self::res("DELETE FROM users WHERE Email='" . self::sl($mail) . "'");
    }
    
    public static function muteUser($mail) {
        self::res("UPDATE users SET Mute=1 "
                . "WHERE Email='" . self::sl($mail) . "'");
    }
    
    public static function unmuteUser($mail) {
        self::res("UPDATE users SET Mute=0 WHERE Email='" . self::sl($mail) . "'");
    }
    
    public static function getMails() {
        return self::table('SELECT * FROM mails ORDER BY Datetime DESC');
    }
    
    public static function getMail($id) {
        return self::row("SELECT * FROM mails "
                . "WHERE id='" . self::sl($id) . "'");
    }
    
    public static function deleteMail($id) {
        self::res("DELETE FROM mails WHERE id='" . self::sl($id) . "'");
    }
    
    public static function newMail($id, $subject, $body) {
        self::res("INSERT INTO mails SET"
                . " Id='" . self::sl($id) . "',"
                . " Subject='" . self::sl($subject) . "',"
                . " Body='" . self::sl($body) . "'");
    }
    
    public static function editMail($id, $subject, $body) {
        self::res("UPDATE mails SET"
                . " Subject='" . self::sl($subject) . "',"
                . " Body='" . self::sl($body) . "'"
                . " WHERE Id='" . self::sl($id) . "'");
    }
    
    public static function getUnsentUsersForMail($mailId) {
        return self::table("SELECT * FROM users WHERE Id NOT IN " . 
                "(SELECT UserId FROM sent"
                . " WHERE MailId='" . self::sl($mailId) . "')"
                . " AND Status='" . SendStatus::NONE . "'");
    }
    
    /*** private helper functions ***/
    
    /**
     * Does a query and returns the ressource.
     * @param string $query A valid MySQL query.
     * @return mixed FALSE on failure, otherwise mysqli_result object
     */
    private static function res($query) {
        return self::$mysqli->query($query);
    }
    
    /**
     * Does a query and returns the data as an array of associative arrays.
     * @param string $query A valid MySQL query.
     * @return mixed Returns an array of associative arrays.
     */
    private static function table($query) {
        $result = self::$mysqli->query($query);
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        return $array;
    }

    /**
     * Does a query and returns the data as an associative array.
     * @param string $query A valid MySQL query.
     * @return mixed Returns an associative array of strings representing the 
     *      fetched row in the result set, where each key in the array 
     *      represents the name of one of the result set's columns or NULL if 
     *      there is no row.
     */
    private static function row($query) {
        $result = self::$mysqli->query($query);
        if(! $result) return false;
        return $result->fetch_assoc();
    }
    
    /**
     * Does a query and returns the first data of every row as an array.
     * @param string $query A valid MySQL query.
     * @return array Returns an array of values.
     */
    private static function column($query) {
        $result = self::$mysqli->query($query);
        $array = array();
        while ($row = $result->fetch_row()) {
            $array[] = $row[0];
        }
        return $array;
    }

    /**
     * Does a query and returns the first value of the first row.
     * @param string $query A valid MySQL query.
     * @return mixed A single value or false
     */
    private static function single($query) {
        $result = self::$mysqli->query($query);
        return $result->fetch_row()[0];
    }
}