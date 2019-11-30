<?php

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
        $this->db = new PDO('sqlite:messages.sqlite');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$this->table_exists('chat')) {
            $this->query("CREATE TABLE chat(username VARCHAR(300), message " .
                         "TEXT, timestamp INTEGER)");
        }
    }
    // -------------------------------------------------------------------------
    // :: function check if table exists in SQLite databse file
    // -------------------------------------------------------------------------
    private function table_exists($table) {
        $data = $this->query("SELECT name FROM sqlite_master WHERE type=" .
                             "'table' AND name = ?", array($table));
        return count($data) > 0;
    }
    // -------------------------------------------------------------------------
    // :: universal query database function that return data or
    // :: numer of rows affected
    // -------------------------------------------------------------------------
    function query($query, $data = null) {
        if ($data == null) {
            $res = $this->db->query($query);
        } else {
            $res = $this->db->prepare($query);
            if ($res) {
                if (!$res->execute($data)) {
                    throw Exception("execute query failed");
                }
            } else {
                throw Exception("wrong query");
            }
        }
        if ($res) {
            $re = "/^\s*INSERT|UPDATE|DELETE|ALTER|CREATE|DROP/i";
            if (preg_match($re, $query)) {
                return $res->rowCount();
            } else {
                return $res->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            throw new Exception("Coudn't open file");
        }
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
        return $this->query("INSERT INTO chat(username, message, timestamp) " .
                            "VALUES (?, ?, strftime('%s','now'))",
                            array($user, $message));
    }
}
