<?php

class SerializedArrayNotEmpty extends CValidator {
    
    protected function validateAttribute($object, $attribute) {
        $value=$object->$attribute;
        if(!$value) {
            $this->setError($object, $attribute);
            return;
        }
        $result = unserialize($value);
        if(count($result) < 1) {
            $this->setError($object, $attribute);
        }
    }
    
    private function setError($object, $attribute) {
        $this->addError($object, $attribute, $attribute . ' cannot be empty');
    }
}

?>
