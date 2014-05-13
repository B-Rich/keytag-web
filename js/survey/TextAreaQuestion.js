function TextAreaQuestion(id, title, required) {
    TextAreaQuestion.superClass.apply(this, [id, QuestionType.TEXT_AREA, title, required]);

    this.getEditHtml = function() {
        var questionTypeTextTitleRow = this.getQuestionTitle(); 

        var questionTypeTextDropdownTitle = '<label>Question type</label>';
        var questionTypeTextDropdownTitleValue = this.getQuestionTypeDropdown();
        var questionTypeTextDropdownRow = '<div class="surveyQuestionRow surveyQuestionRowType">' 
            + questionTypeTextDropdownTitle 
            + questionTypeTextDropdownTitleValue 
            + '</div><div class="clear"></div>';

        var questionTypeTextAnswer = '<label>Example of answer</label>';
        var questionTypeTextAnswerValue = '<span class="short-textarea short-textarea-question-type paragraphedText">' 
            + '<textarea readonly="readonly">Answered text</textarea></span>';
        var questionTypeTextAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeTextAnswer 
            + questionTypeTextAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeTextResult = this.beginWrapperHtml('surveyQuestionTextEditHtml')
            + this.getTopActionButtons(true)
            + questionTypeTextTitleRow
            + questionTypeTextDropdownRow
            + questionTypeTextAnswerRow
            + this.getBottomActionButtons()
            + '</div>';
        return questionTypeTextResult;
    };
    
    this.getPreviewEditHtml = function() {
        var questionTypeTextTitle = '<label>Question title</label>';
        var questionTypeTextTitleValue = '<div style="" class="surveyStaticTitleText">' + this.title + '</div>';
        var questionTypeTextTitleRow = '<div class="surveyQuestionRow">' 
            + questionTypeTextTitle 
            + questionTypeTextTitleValue 
            + '</div><div class="clear" />';

        var questionTypeTextAnswer = '<label>Example of answer</label>';
        var questionTypeTextAnswerValue = '<div class="exampleQuestionAnswer width-400">' 
            + '<span class="short-textarea short-textarea-question-type paragraphedText">'
            + '<textarea readonly="readonly">Answered text</textarea>'
            + '</span></div>';
        var questionTypeTextAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeTextAnswer 
            + questionTypeTextAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeTextResult = this.beginWrapperHtml('surveyQuestionTextPreviewHtml')
            + this.getTopActionButtons()
            + questionTypeTextTitleRow
            + questionTypeTextAnswerRow
            + '</div>';
        return questionTypeTextResult;
    };
    
    this.getViewHtml = function() {
        //TODO implement
    };
    
    this.baseFlush = this.flush;
    this.flush = function () {
        this.baseFlush();
    };
}
TextAreaQuestion.inheritsFrom(BaseQuestion);