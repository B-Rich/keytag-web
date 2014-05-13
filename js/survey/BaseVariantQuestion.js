function BaseVariantQuestion(id, type, title, required, variants) {
    BaseVariantQuestion.superClass.apply(this, [id, type, title, required]);
    
    this.baseFlush = this.flush;
    this.flush = function () {
        this.baseFlush();
        this.variants = [];
        
        var inputs = this.jqObject().find('input[name="Variants"]');
        for (var i=0; i < inputs.length; i++) {
            this.variants.push({ title: $(inputs[i]).val(), weight: i+1 });
        }
    };
    
    this.baseValidate = this.validate;
    this.validate = function() {
        this.baseValidate();
        
        var errorObject = this.jqErrorObject();
        if (this.isValid) {
            var inputs = this.jqObject().find('input[name="Variants"]');
            if (inputs.length < 1) {
                this.isValid = false;
                errorObject.text('Enter at least one option');
            }
            else {
                for (var i=0; i < inputs.length; i++) {
                    var inputVal = $(inputs[i]).val();
                    if (!inputVal) {
                        this.isValid = false;
                        errorObject.text('Each option must have title');
                    }
                    else if (inputVal.length > this.optionLength) {
                        this.isValid = false;
                        errorObject.text('Option name is too long');
                    }
                    if (!this.isValid) {
                        $(inputs[i]).focus();
                        break;
                    }
                }
            }
        }
    }
    
    this.variants = variants;
    this.optionLength = 50;
    
    this.getOptionMaxLength = function() {
        return this.getInputMaxLength(this.optionLength);
    }
    
}
BaseVariantQuestion.inheritsFrom(BaseQuestion);

