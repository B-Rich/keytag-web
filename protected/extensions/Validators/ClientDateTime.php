<?php

class ClientDateTime extends CValidator {
    
    protected function validateAttribute($object, $attribute){
        $value=$object->$attribute;
        if(!$value) {
            return;
        }
        if(!Localization::IsClientDateValid($value)) {
            $this->addError($object, $attribute, 'Incorrect date format, use: mm/dd/yyyy');
        }
    }
}

?>
