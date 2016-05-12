<?php
class Recipe {
    function __construct($r_id, $r_dishes, $r_time, $r_title, $r_origin) {
        $this->id = $r_id;
        $this->dishes = $r_dishes;
        $this->time = $r_time;
        $this->title = $r_title;
        $this->origin = $r_origin;
    }
}
?>