<?php
class User {
    function __construct($uId, $uIdFacebook, $uEmail, $uName, $uMedia, $uTitle, $uTotalPoints) {
        $this->userId = $uId;
        $this->userIdFacebook =  $uIdFacebook;
        $this->userEmail = $uEmail;
        $this->userName = $uName;
        $this->userMedia = $uMedia;
        $this->userTitle = $uTitle;
        $this->userTotalPoints = $uTotalPoints;
    }
}
?>