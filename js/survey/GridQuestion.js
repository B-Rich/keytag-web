function GridQuestion(id, title, required) {
    GridQuestion.superClass.apply(this, [id, QuestionType.GRID, title, required]);
    
    this.getEditHtml = function() {
        //TODO implement
    };
    
    this.getPreviewEditHtml = function() {
        //TODO implement
    };
    
    this.getViewHtml = function() {
        //TODO implement
    };
    
    this.baseFlush = this.flush;
    this.flush = function () {
        this.baseFlush();
    };
    
    this.variants = [];
}
GridQuestion.inheritsFrom(BaseQuestion);


