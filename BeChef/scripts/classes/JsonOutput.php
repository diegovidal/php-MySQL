<?php
class JsonOutput {
    function __construct($succ, $msg, $items) {
        $this->success = $succ;
        $this->message = $msg;
        $this->data = $items;
    }
}
?>