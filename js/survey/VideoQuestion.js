function VideoQuestion(id, title, required) {
    VideoQuestion.superClass.apply(this, [id, QuestionType.VIDEO, title, required]);
    
    this.getEditHtml = function() {
        var questionTypeVideoTitleRow = this.getQuestionTitle(); 

        var questionTypeVideoDropdownTitle = '<label>Question type</label>';
        var questionTypeVideoDropdownTitleValue = this.getQuestionTypeDropdown();
        var questionTypeVideoDropdownRow = '<div class="surveyQuestionRow surveyQuestionRowType">' 
            + questionTypeVideoDropdownTitle 
            + questionTypeVideoDropdownTitleValue 
            + '</div><div class="clear"></div>';

        var questionTypeVideoAnswer = '<label>Example of answer</label>';
        var questionTypeVideoAnswerValue = '<span class="short-input short-input-image">' 
            + '<input type="text" readonly="readonly" /></span><a href="" onclick="return false;" class="bttn-browse">Browse</a>';
        var questionTypeVideoAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeVideoAnswer 
            + questionTypeVideoAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeVideoResult = this.beginWrapperHtml('surveyQuestionTextEditHtml')
            + this.getTopActionButtons(true)
            + questionTypeVideoTitleRow
            + questionTypeVideoDropdownRow
            + questionTypeVideoAnswerRow
            + this.getBottomActionButtons()
            + '</div>';
        return questionTypeVideoResult;
    };
    
    this.getPreviewEditHtml = function() {
        var questionTypeVideoTitle = '<label>Question title</label>';
        var questionTypeVideoTitleValue = '<div style="" class="surveyStaticTitleText">' + this.title + '</div>';
        var questionTypeVideoTitleRow = '<div class="surveyQuestionRow">' 
            + questionTypeVideoTitle 
            + questionTypeVideoTitleValue 
            + '</div><div class="clear" />';

        var questionTypeVideoAnswer = '<label>Example of answer</label>';
        var questionTypeVideoAnswerValue = '<div class="exampleQuestionAnswer width-400">' 
            + '<span class="short-input short-input-image">'
            + '<input type="text" readonly="readonly" /></span><a href="" onclick="return false;" class="bttn-browse">Browse</a>'
            + '</div>';
        var questionTypeVideoAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeVideoAnswer 
            + questionTypeVideoAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeVideoResult = this.beginWrapperHtml('surveyQuestionTextPreviewHtml')
            + this.getTopActionButtons()
            + questionTypeVideoTitleRow
            + questionTypeVideoAnswerRow
            + '</div>';
        return questionTypeVideoResult;
    };
    
    this.getViewHtml = function() {};
}
VideoQuestion.inheritsFrom(BaseQuestion);