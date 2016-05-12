<?php
class Achievement{
    function __construct($a_id,$c_id,$a_title,$a_media)
    {
        $this->id = $a_id;
        $this->challengeId = $c_id;
        $this->title = $a_title;
        $this->media = $a_media;
    }
}
?>