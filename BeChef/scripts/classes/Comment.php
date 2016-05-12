<?php
class Comment {
    function __construct($uId, $uName, $uMedia, $cId, $cText, $cDate) {
        $this->userId = $uId;
        $this->userName = $uName;
        $this->userMedia = $uMedia;
        $this->commentId = $cId;
        $this->commentText = $cText;
        $this->commentDate = $cDate;
    }
}
?>