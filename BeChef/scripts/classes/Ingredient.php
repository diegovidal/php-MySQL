<?php
class Ingredient {
    function __construct($i_id, $i_name, $i_quantity, $i_unit) {
    	$this->id = $i_id;
        $this->name = $i_name;
        $this->quantity = $i_quantity;
        $this->unit = $i_unit;
    }
}
?>