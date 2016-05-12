<?php

define ('SITE_ROOT', realpath(dirname(__FILE__)));

function savePhoto() {
    if(isset($_FILES['photo'])) { 
        $target_dir = getenv('OPENSHIFT_DATA_DIR');//SITE_ROOT.'/../photos/';
        if ($_FILES["photo"]["size"] <= 1000000) {   
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            $fileinfo = explode("/", $check["mime"]);
            $fileType = $fileinfo[1];
            if($check !== false && ($fileType == "jpg" || $fileType != "png" || $fileType != "jpeg")) {
                $target_file = '';
                do {
                    $length = 20;
                    $target_file = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
                    $target_file .= '.' . $fileType;
                } while(file_exists($target_dir . "." . $target_file));

                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $target_file)) {
                    return $target_file;
                } 
            } 
        } 
    }

    return '';
}

function saveMultiplePhotos($steps) {

    if (isset($steps)) {

        $arrayPhotos = array();
        
        for ($i=0; $i < count($steps); $i++) { 

            $stepPhoto = $steps[$i]->stepPhoto;

            if(isset($_FILES[$stepPhoto])) { 
                $target_dir = getenv('OPENSHIFT_DATA_DIR');//SITE_ROOT.'/../photos/';
                if ($_FILES[$stepPhoto]["size"] <= 1000000) {   
                    $check = getimagesize($_FILES[$stepPhoto]["tmp_name"]);
                    $fileinfo = explode("/", $check["mime"]);
                    $fileType = $fileinfo[1];
                    if($check !== false && ($fileType == "jpg" || $fileType != "png" || $fileType != "jpeg")) {
                        $target_file = '';
                        do {
                            $length = 20;
                            $target_file = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
                            $target_file .= '.' . $fileType;
                        } while(file_exists($target_dir . "." . $target_file));

                        if (move_uploaded_file($_FILES[$stepPhoto]["tmp_name"], $target_dir . $target_file)) {
                            $arrayPhotos[] = $target_file;
                        } 
                    }
                }
            }//if isset(photo)
            else{
                $arrayPhotos[] = "";
            }
        }//for
    }//if isset(numberOfPhotos)

    return $arrayPhotos;
       
}

?>