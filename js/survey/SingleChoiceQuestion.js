function SingleChoiceQuestion(id, title, required, variants) {
    SingleChoiceQuestion.superClass.apply(this, [id, QuestionType.SINGLE_CHOICE, title, required, variants]);
    
    this.getEditHtml = function() {
        var questionTypeSingleChoiceTitleRow = this.getQuestionTitle(); 
        
        var questionTypeSingleChoiceDropdownTitle = '<label>Question type</label>';
        var questionTypeSingleChoiceDropdownTitleValue = this.getQuestionTypeDropdown();
        var questionTypeSingleChoicetDropdownRow = '<div class="surveyQuestionRow surveyQuestionRowType">' 
            + questionTypeSingleChoiceDropdownTitle 
            + questionTypeSingleChoiceDropdownTitleValue 
            + '</div><div class="clear"></div>';
        
        var questionTypeSingleChoiceAnswer = '<label>Example of answer</label>';
        var addNewLine = '<a href="#" onclick="addNewLine.call($(this)); return false;" class="multipleChoiceAddNewLine">Add new line</a>';
        var addOtherLine = '';// temporary commented '<a href="#" onclick="addAnotherLine.call($(this)); return false;" class="multipleChoiceAddOther">Add other</a>';
        var questionTypeSingleChoiceAnswerValue = '<div class="exampleQuestionAnswer">' 
            + this.questionTypeSingleChoiceAnswerValueEdit() 
            + addNewLine 
            + addOtherLine 
            + '</div>';
        var questionTypeSingleChoiceAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeSingleChoiceAnswer 
            + questionTypeSingleChoiceAnswerValue 
            + '</div><div class="clear"></div>';
        
        var questionTypeSingleChoiceResult = this.beginWrapperHtml('surveyQuestionSingleChoiceEditHtml')
            + this.getTopActionButtons(true)
            + questionTypeSingleChoiceTitleRow
            + questionTypeSingleChoicetDropdownRow
            + questionTypeSingleChoiceAnswerRow
            + this.getBottomActionButtons()
            + '</div>';
        
        return questionTypeSingleChoiceResult;
    };
    
    this.getPreviewEditHtml = function() {
        var questionTypeSingleChoiceTitle = '<label>Question title</label>';
        var questionTypeSingleChoiceTitleValue = '<div style="" class="surveyStaticTitleText">' 
            + this.title 
            + '</div>';
        var questionTypeSingleChoiceTitleRow = '<div class="surveyQuestionRow">' 
            + questionTypeSingleChoiceTitle 
            + questionTypeSingleChoiceTitleValue 
            + '</div><div class="clear" />';

        var questionTypeSingleChoiceAnswer = '<label>Example of answer</label>';
        var questionTypeSingleChoiceAnswerValue = '<div class="exampleQuestionAnswer">' 
            + this.questionTypeSingleChoiceAnswerValuePreview()  
            + '</div>';
        var questionTypeSingleChoiceAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeSingleChoiceAnswer 
            + questionTypeSingleChoiceAnswerValue
            + '</div><div class="clear"></div>';
        
        var questionTypeSingleChoiceResult = this.beginWrapperHtml('surveyQuestionSingleChoicePreviewHtml')
            + this.getTopActionButtons()
            + questionTypeSingleChoiceTitleRow
            + questionTypeSingleChoiceAnswerRow
            + '</div>';
        return questionTypeSingleChoiceResult;
    };
    
    this.questionTypeSingleChoiceAnswerValuePreview = function (){
        var answerValues = this.variants;
        var answerLenfth = answerValues.length;
        var answerItems = '';
        for (var i=0; i<answerLenfth; i++){
            var answerItem = '<li>'
            + '<input class="short-input-multiple-checkbox" type="radio" name="TypeSingleChoice" />'
            + '<span class="short-input short-input-multiple SingleChoiceInput"><input type="text" readonly="readonly" value="'+ answerValues[i].title +'" /></span>'
            + '</li>';
            answerItems += answerItem;
        }
        var answerResult = '<ul class="multipleChoiceUl">' + answerItems + '</ul><div class="clear"></div>';
        return answerResult;
    };
    
    this.questionTypeSingleChoiceAnswerValueEdit = function (){
        var answerValues = this.variants;
        var answerLenfth = answerValues.length;
        var answerItems = '';
        for (var i=0; i<answerLenfth; i++){
            var answerItem = '<li class="multipleChoiceLi">'
            + '<input class="short-input-multiple-checkbox" type="radio" name="TypeSingleChoice" />'
            + '<span class="short-input short-input-multiple SingleChoiceInputEdit"><input type="text"'
            + ' value="'+ answerValues[i].title +'" name="Variants" ' + this.getOptionMaxLength() + ' /></span>'
            + '<a href="#" onclick="removeSingleLine.call($(this)); return false;" class="removeMultipleChoiceLine"></a>'
            + '</li>';
            answerItems += answerItem;
        }
        var answerResult = '<ul class="multipleChoiceUl">' + answerItems + '</ul><div class="clear"></div>';
        return answerResult;
    };
    
    this.getViewHtml = function() {
        //TODO implement
    };
}
SingleChoiceQuestion.inheritsFrom(BaseVariantQuestion);