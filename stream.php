<?php

// -----------------------------------------------------------------------------
// :: class that send messages using Server Side Events
// -----------------------------------------------------------------------------
class EventStream {
    function __construct($name) {
        ob_start();
        $this->name = $name;
        $this->id = 0;
        $this->setup();
        /* start fresh */
        ob_end_clean();
    }
    // -------------------------------------------------------------------------
    // :: send server side event
    // -------------------------------------------------------------------------
    function send($data) {
        $data = json_encode($data);
        echo "event: {$this->name}\r\nid: {$this->id}\r\ndata: $data\r\n\r\n";
        $this->id++;
    }
    // -------------------------------------------------------------------------
    // :: function that will make php file stream data it will disable any
    // :: buffering that may be added by apache, php or nginx proxy
    // :: ref: https://tinyurl.com/y8yyr6eq (https://www.jeffgeerling.com/blog)
    // -------------------------------------------------------------------------
    private function setup() {
        @ini_set('zlib.output_compression',0);
        @ini_set('implicit_flush',1);
        @ob_end_clean();
        set_time_limit(0);
        header('Content-type: text/event-stream; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        // Setting this header instructs Nginx to disable fastcgi_buffering
        // and disable
        // gzip for this request.
        header('X-Accel-Buffering: no');
    }
}

require_once('Messages.php');


$stream = new EventStream("chat");
$messages = new Messages();

while (true) {
    if ($messages->hasData()) {
        foreach ($messages->getData() as $message) {
            $stream->send($message);
        }
    }
    flush();
    if ( connection_aborted() ) break;
    sleep(1);
}
