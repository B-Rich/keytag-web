function SmileQuestion(id, title, required, variants) {
    SmileQuestion.superClass.apply(this, [id, QuestionType.SMILES, title, required]);
    
    this.getEditHtml = function() {
         
        var questionTypeSmileMinLabel = '<label class="smileMinLabelVal">' +'<img src="images/smiles/strongly-disagree.png" height="45">'+ '</label>'
            + '<span class="smileMinLabel"><input type="text" class="minLabel"'
            + ' value="' + this.variants.labelMin + '" ' + this.getInputMaxLength(this.labelMaxMinLength) + ' /></span><div class="clear"></div>';
        var questionTypeSmileMaxLabel = '<label class="smileMaxLabelVal">' + '<img src="images/smiles/strongly-agree.png" height="45">' + '</label>'
            + '<span class="smileMaxLabel"><input type="text" class="maxLabel"'
            + ' value="' + this.variants.labelMax + '" ' + this.getInputMaxLength(this.labelMaxMinLength) + ' /></span><div class="clear"></div>';
        
        var questionTypeSmileTitleRow = this.getQuestionTitle(); 
        
        var questionTypeSmileDropdownTitle = '<label>Question type</label>';
        var questionTypeSmileDropdownTitleValue = this.getQuestionTypeDropdown();
        var questionTypeSmileDropdownRow = '<div class="surveyQuestionRow surveyQuestionRowType">' 
            + questionTypeSmileDropdownTitle 
            + questionTypeSmileDropdownTitleValue 
            + '</div><div class="clear"></div>';
        
        var questionTypeSmileAnswer = '<label>Example of answer</label>';
        var questionTypeSmileAnswerValue ='<div class="smileQuestionType">' 
            + questionTypeSmileMinLabel 
            + questionTypeSmileMaxLabel
            + '</div>';
        var questionTypeSmileAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeSmileAnswer 
            + questionTypeSmileAnswerValue 
            + '</div><div class="clear"></div>';
                
        var questionTypeSmileResult = this.beginWrapperHtml('surveyQuestionTextEditHtml')
            + this.getTopActionButtons(true)
            + questionTypeSmileTitleRow
            + questionTypeSmileDropdownRow
            + questionTypeSmileAnswerRow
            + this.getBottomActionButtons()
            + '</div>';
        
        return questionTypeSmileResult;
    };
    
    this.getPreviewEditHtml = function() {
        var questionTypeSmileTitle = '<label>Question title</label>';
        var questionTypeSmileTitleValue = '<div style="" class="surveyStaticTitleText">' + this.title + '</div>';
        var questionTypeSmileTitleRow = '<div class="surveyQuestionRow">' 
            + questionTypeSmileTitle 
            + questionTypeSmileTitleValue 
            + '</div><div class="clear" />';

        var questionTypeSmileAnswer = '<label>Example of answer</label>';
        var questionTypeSmileAnswerValue = '<div class="exampleQuestionAnswer width-400">' 
            + this.getPreviewEditHtmlSmileRadioButtons();
            + '</div>';
        var questionTypeSmileAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeSmileAnswer 
            + questionTypeSmileAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeSmileResult = this.beginWrapperHtml('surveyQuestionTextPreviewHtml')
            + this.getTopActionButtons()
            + questionTypeSmileTitleRow
            + questionTypeSmileAnswerRow
            + '</div>';
        
        return questionTypeSmileResult;
    };
    
    this.getViewHtml = function() {
        //TODO implement
    };
    
    this.getPreviewEditHtmlSmileRadioButtons = function(){
        var answerValues = this.variants;
        var minSmileVal = parseInt(answerValues.minValue);
        var maxSmileVal = parseInt(answerValues.maxValue);
        var labelSmileMin = answerValues.labelMin;
        var labelSmileMax = answerValues.labelMax;
        if (labelSmileMin == 'undefined'){
            labelSmileMin = '';
        }
        if (labelSmileMax == 'undefined'){
            labelSmileMax = '';
        }
        
        var radioStr = '';
        var imagesSrc = ['images/smiles/strongly-disagree.png',
            'images/smiles/disagree.png',
            'images/smiles/not-sure.png',
            'images/smiles/agree.png',
            'images/smiles/strongly-agree.png'];
        for( var i = minSmileVal; i <= maxSmileVal; i++){
            radioStr += '<div class="radioSmileRow">'
                + '<input class="radioSmileBttn  radiowp' + i + '" type="radio" name="radio" />'
                + '<span class="radioVal radiowp'+ i +'">' + '<img src="'+ imagesSrc[i-1] +'" height="45">' + '</span>'
                + '</div>';
        }
        radioStr = '<div class="wrapperRadioSmile">' 
            + '<span class="firstMin smileFirstMin">' + labelSmileMin + '</span>'
            + radioStr 
            + '<span class="firstMax smileFirstMax">'+ labelSmileMax + '</span>'
            + '</div>';
        return  radioStr;      
    }
    
    this.baseFlush = this.flush;
    this.flush = function () {
        this.baseFlush();
        this.variants = {};
        this.variants.labelMin = this.jqObject().find('.minLabel').val();
        this.variants.labelMax = this.jqObject().find('.maxLabel').val();
        this.variants.minValue = 1;
        this.variants.maxValue = 5;
    };
    
    this.variants = variants;
    this.labelMaxMinLength = 50;
    
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
SmileQuestion.inheritsFrom(BaseQuestion);