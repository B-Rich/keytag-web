function ScaleQuestion(id, title, required, variants) {
    ScaleQuestion.superClass.apply(this, [id, QuestionType.SCALE, title, required]);
    
    this.getEditHtml = function() {
        var questionTypeScaleMin = '<span class="scaleMin">'
            + '<select class="selectMin" onchange="changeMinValueInScaleType.call($(this));">';
        for (var i = 0; i<2; i++){
            questionTypeScaleMin += '<option ' + this.getMinSelectedAttr(i) + '>' + i + '</option>';
        }
        questionTypeScaleMin += '</select></span>';
        
        var questionTypeScaleMax = '<span class="scaleMax">'
            + '<select class="selectMax" onchange="changeMaxValueInScaleType.call($(this));">';    
        for (var i = 3; i<11; i++){
            questionTypeScaleMax += '<option ' + this.getMaxSelectedAttr(i) + '>' + i + '</option>';
        }
        questionTypeScaleMax += '</select></span><div class="clear"></div>';
        
        var questionTypeScaleMinLabel = '<label class="scaleMinLabelVal">' + this.variants.minValue + '</label>'
            + '<span class="scaleMinLabel"><input type="text" class="minLabel"'
            + ' value="' + this.variants.labelMin + '" ' + this.getInputMaxLength(this.labelMaxMinLength) + ' /></span><div class="clear"></div>';
        var questionTypeScaleMaxLabel = '<label class="scaleMaxLabelVal">' + this.variants.maxValue + '</label>'
            + '<span class="scaleMaxLabel"><input type="text" class="maxLabel"'
            + ' value="' + this.variants.labelMax + '" '+ this.getInputMaxLength(this.labelMaxMinLength) + ' /></span><div class="clear"></div>';
        
        var questionTypeScaleTitleRow = this.getQuestionTitle(); 
        
        var questionTypeScaleDropdownTitle = '<label>Question type</label>';
        var questionTypeScaleDropdownTitleValue = this.getQuestionTypeDropdown();
        var questionTypeScaleDropdownRow = '<div class="surveyQuestionRow surveyQuestionRowType">' 
            + questionTypeScaleDropdownTitle 
            + questionTypeScaleDropdownTitleValue 
            + '</div><div class="clear"></div>';
        
        var questionTypeScaleAnswer = '<label>Example of answer</label>';
        var questionTypeScaleAnswerValue ='<div class="scaleQuestionType">' 
            + questionTypeScaleMin 
            + questionTypeScaleMax 
            + questionTypeScaleMinLabel 
            + questionTypeScaleMaxLabel
            + '</div>';
        var questionTypeScaleAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeScaleAnswer 
            + questionTypeScaleAnswerValue 
            + '</div><div class="clear"></div>';
                
        var questionTypeScaleResult = this.beginWrapperHtml('surveyQuestionTextEditHtml')
            + this.getTopActionButtons(true)
            + questionTypeScaleTitleRow
            + questionTypeScaleDropdownRow
            + questionTypeScaleAnswerRow
            + this.getBottomActionButtons()
            + '</div>';
        
        return questionTypeScaleResult;
    };
    
    this.getPreviewEditHtml = function() {
        var questionTypeScaleTitle = '<label>Question title</label>';
        var questionTypeScaleTitleValue = '<div style="" class="surveyStaticTitleText">' + this.title + '</div>';
        var questionTypeScaleTitleRow = '<div class="surveyQuestionRow">' 
            + questionTypeScaleTitle 
            + questionTypeScaleTitleValue 
            + '</div><div class="clear" />';

        var questionTypeScaleAnswer = '<label>Example of answer</label>';
        var questionTypeScaleAnswerValue = '<div class="exampleQuestionAnswer width-400">' 
            + this.getPreviewEditHtmlScaleRadioButtons();
            + '</div>';
        var questionTypeScaleAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeScaleAnswer 
            + questionTypeScaleAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeScaleResult = this.beginWrapperHtml('surveyQuestionTextPreviewHtml')
            + this.getTopActionButtons()
            + questionTypeScaleTitleRow
            + questionTypeScaleAnswerRow
            + '</div>';
        
        return questionTypeScaleResult;
    };
    
    this.getViewHtml = function() {
        //TODO implement
    };
    
    this.getPreviewEditHtmlScaleRadioButtons = function(){
        var answerValues = this.variants;
        var minScaleVal = parseInt(answerValues.minValue);
        var maxScaleVal = parseInt(answerValues.maxValue);
        var labelScaleMin = answerValues.labelMin;
        var labelScaleMax = answerValues.labelMax;
        if (labelScaleMin == 'undefined'){
            labelScaleMin = '';
        }
        if (labelScaleMax == 'undefined'){
            labelScaleMax = '';
        }
        
        var radioStr = '';
        for( var i = minScaleVal; i <= maxScaleVal; i++){
            radioStr += '<div class="radioScaleRow">'
                + '<input class="radioScaleBttn  radiowp' + i + '" type="radio" name="radio" />'
                + '<span class="radioVal radiowp'+ i +'">' + i + '</span>'
                + '</div>';
        }
        radioStr = '<div class="wrapperRadioScale">' 
            + '<span class="firstMin">' + labelScaleMin + '</span>'
            + radioStr 
            + '<span class="firstMax">'+ labelScaleMax + '</span>'
            + '</div>';
        return  radioStr;      
    }
    
    this.baseFlush = this.flush;
    this.flush = function () {
        this.baseFlush();
        this.variants = {};
        this.variants.labelMin = this.jqObject().find('.minLabel').val();
        this.variants.labelMax = this.jqObject().find('.maxLabel').val();
        this.variants.minValue = this.jqObject().find('.selectMin').val();
        this.variants.maxValue = this.jqObject().find('.selectMax').val();
    };
    
    this.variants = variants;
    this.labelMaxMinLength = 50;
    
    this.getMinSelectedAttr = function(value) {
        return value == this.variants.minValue ? 'selected="selected"' : '';
    }
    
    this.getMaxSelectedAttr = function(value) {
        return value == this.variants.maxValue ? 'selected="selected"' : '';
    }
    
    this.baseValidate = this.validate;
    this.validate = function() {
        this.baseValidate();
        var errorObject = this.jqErrorObject();
        if (this.isValid) {
            var minLabelObj = this.jqObject().find('.minLabel');
            var maxLabelObj = this.jqObject().find('.maxLabel');
            if (minLabelObj.val().length > this.labelMaxMinLength) {
                this.isValid = false;
                errorObject.text('Minimum label is too long');
                minLabelObj.focus();
            }
            else if (maxLabelObj.val().length > this.labelMaxMinLength) {
                this.isValid = false;
                errorObject.text('Maximum label is too long');
                maxLabelObj.focus();
            }
        }
    }
}
ScaleQuestion.inheritsFrom(BaseQuestion);