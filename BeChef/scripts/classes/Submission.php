<?php
class Submission {
    function __construct($s_id, $s_detail, $s_media, $s_points, $s_userId, $s_challengeId, $s_recipeId, $s_date, $s_liked, $s_likes, $u_name, $u_media) {
        $this->id = $s_id;
        $this->detail = $s_detail;
        $this->media = $s_media;
        $this->points = $s_points;
        $this->userId = $s_userId;
        $this->challengeId = $s_challengeId;
        $this->recipeId = $s_recipeId;
        $this->date = $s_date;
        $this->liked = $s_liked;
        $this->likes = $s_likes;
        $this->userName = $u_name;
        $this->userMedia = $u_media;
    }
}
?>