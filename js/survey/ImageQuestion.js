function ImageQuestion(id, title, required) {
    ImageQuestion.superClass.apply(this, [id, QuestionType.IMAGE, title, required]);
    
    this.getEditHtml = function() {
        var questionTypeImageTitleRow = this.getQuestionTitle();

        var questionTypeImageDropdownTitle = '<label>Question type</label>';
        var questionTypeImageDropdownTitleValue = this.getQuestionTypeDropdown();
        var questionTypeImageDropdownRow = '<div class="surveyQuestionRow surveyQuestionRowType">' 
            + questionTypeImageDropdownTitle 
            + questionTypeImageDropdownTitleValue 
            + '</div><div class="clear"></div>';

        var questionTypeImageAnswer = '<label>Example of answer</label>';
        var questionTypeImageAnswerValue = '<span class="short-input short-input-image">' 
            + '<input type="text" readonly="readonly" /></span><a href="" onclick="return false;" class="bttn-browse">Browse</a>';
        var questionTypeImageAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeImageAnswer 
            + questionTypeImageAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeImageResult = this.beginWrapperHtml('surveyQuestionTextEditHtml')
            + this.getTopActionButtons(true)
            + questionTypeImageTitleRow
            + questionTypeImageDropdownRow
            + questionTypeImageAnswerRow
            + this.getBottomActionButtons()
            + '</div>';
        return questionTypeImageResult;
    };
    
    this.getPreviewEditHtml = function() {
        var questionTypeImageTitle = '<label>Question title</label>';
        var questionTypeImageTitleValue = '<div style="" class="surveyStaticTitleText">' + this.title + '</div>';
        var questionTypeImageTitleRow = '<div class="surveyQuestionRow">' 
            + questionTypeImageTitle 
            + questionTypeImageTitleValue 
            + '</div><div class="clear" />';

        var questionTypeImageAnswer = '<label>Example of answer</label>';
        var questionTypeImageAnswerValue = '<div class="exampleQuestionAnswer width-400">' 
            + '<span class="short-input short-input-image">'
            + '<input type="text" readonly="readonly" /></span><span><a href="" onclick="return false;" class="bttn-browse">Browse</a>'
            + '</div>';
        var questionTypeImageAnswerRow = '<div class="row surveyQuestionRow">' 
            + questionTypeImageAnswer 
            + questionTypeImageAnswerValue 
            + '</div><div class="clear"></div>';

        var questionTypeImageResult = this.beginWrapperHtml('surveyQuestionTextPreviewHtml')
            + this.getTopActionButtons()
            + questionTypeImageTitleRow
            + questionTypeImageAnswerRow
            + '</div>';
        return questionTypeImageResult;
    };
    this.getViewHtml = function() {};
}
ImageQuestion.inheritsFrom(BaseQuestion);

