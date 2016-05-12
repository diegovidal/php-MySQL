<?php
class Challenge{
    function __construct($c_id,$c_name,$c_detail,$c_startDate,$c_endDate,$c_dishes,$c_maxPoints,$c_media,$u_associate, $u_submissions)
    {
        $this->id = $c_id;
        $this->name = $c_name;
        $this->detail = $c_detail;
        $this->startDate = $c_startDate;
        $this->endDate = $c_endDate;
        $this->dishes = $c_dishes;
        $this->maxPoints = $c_maxPoints;
        $this->media = $c_media;
        $this->associate = $u_associate; //boolean identifica se usuário fez submissoes nesse desafio
        $this->submissions = $u_submissions;
    }
}
?>