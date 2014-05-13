<?php
class Link extends CValidator {
    public $type;
    
    private $google_picasa = '@^(http|https)://picasaweb.google.com@i';
    private $google_docs = '@^(http|https)://docs.google.com@i';
    private $google_plus_photos = '@^(http|https)://plus.google.com/photos@i';
    private $base_camp = '@basecamphq.com@i';
    
    protected function validateAttribute($object, $attribute) {
        $value=$object->$attribute;
        
        if ($value) {
            if ($this->type == LinkType::GOOGLE_PICASA &&
                    !($this->contains($this->google_picasa, $value)
                       || $this->contains($this->google_plus_photos, $value))) {
                $this->addError($object, $attribute, 'Invalid Picasa Web link');
            }
            else if ($this->type == LinkType::GOOGLE_DOCS && !$this->contains($this->google_docs, $value)) {
                $this->addError($object, $attribute, 'Invalid Google Docs link');
            }
            else if ($this->type == LinkType::BASE_CAMP && !$this->contains($this->base_camp, $value)) {
                $this->addError($object, $attribute, 'Invalid Base Camp link');
            }
        }
    }
    
    private function contains($pattern, $subject) {
        return preg_match($pattern, $subject);
    }
}

?>
