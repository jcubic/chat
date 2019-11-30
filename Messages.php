<?php

require_once('Database.php');
require_once('Notifications.php');

// -----------------------------------------------------------------------------
// :: class that represent model of messages using SQLite database
// -----------------------------------------------------------------------------
class Messages {
    private $limit = 400;
    function __construct($time = null) {
        // if $time is null it will return all messages we will use this when
        // showing message when app starts
        if (is_null($time)) {
            $this->time = time();
        } else {
            $this->time = $time;
        }
        $this->db = new Database();
        $this->notification = new Notification();
        if (!$this->table_exists('chat')) {
            $this->query("CREATE TABLE chat(username VARCHAR(300), message " .
                         "TEXT, timestamp INTEGER)");
        }
    }
    // -------------------------------------------------------------------------
    // :: forward every missing method to database object
    // -------------------------------------------------------------------------
    public function __call($name, $args) {
        return call_user_func_array(array($this->db, $name), $args);
    }
    // -------------------------------------------------------------------------
    // :: function used to fetch data, you're passing what should be returned
    // -------------------------------------------------------------------------
    function fetch($values) {
        return $this->query("SELECT $values FROM chat WHERE timestamp > " .
                            "{$this->time} ORDER BY timestamp");
    }
    // -------------------------------------------------------------------------
    // :: function check if there any new data in database from after timestamp
    // -------------------------------------------------------------------------
    function hasData() {
        $data = $this->fetch("count(*)");
        return $data[0]['count(*)'] > 0;
    }
    // -------------------------------------------------------------------------
    // :: function return data from last time and reset timer - each time
    // :: it's called inside single instance, it returns different data
    // :: only lastest ones
    // -------------------------------------------------------------------------
    function getData() {
        $time = time();
        $data = $this->fetch("username, message");
        $this->time = $time;
        return $data;
    }
    // -------------------------------------------------------------------------
    // :: function add new message to databse with current time
    // -------------------------------------------------------------------------
    function newMessage($user, $message) {
        if (strlen($message) > $this->limit) {
            $message = substr($message, 0, $this->limit) . "...";
        }
        $this->notification->send($user, $message);
        return $this->query("INSERT INTO chat(username, message, timestamp) " .
                            "VALUES (?, ?, strftime('%s','now'))",
                            array($user, $message));
    }
}
