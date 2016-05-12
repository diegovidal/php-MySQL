<?php
class SubmissionFull {
    function __construct($s_submission, $s_recipe, $s_ingredients, $s_steps) {
        $this->submission = $s_submission;
        $this->recipe = $s_recipe;
        $this->ingredients = $s_ingredients;
        $this->steps = $s_steps;
       
    }
}
?>