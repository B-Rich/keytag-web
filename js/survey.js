var QuestionType = {TEXT_INPUT: 1, TEXT_AREA: 2, SINGLE_CHOICE: 3, MULTIPLE_CHOICE: 4, DROPDOWN: 5, SCALE: 6, GRID: 7, IMAGE: 8, VIDEO: 9, SMILES : 10};

function removeSingleLine (){
    var lineLength= $(this).parents('.multipleChoiceUl').children('li.multipleChoiceLi').length;
    if(lineLength > 1){
        $(this).parent().remove();
            if($(this).parent().find('.another-answer:visible').length < 1){
                $(this).parents('.surveyQuestion').find('.multipleChoiceAddOther').show();
                $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').show();
            }
        return false;
    } /*else if (lineLength == 1) {
        if($(this).parent().find('.another-answer:visible').length < 1){
            $(this).parents('.surveyQuestion').find('.multipleChoiceAddOther').show();
            $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').show();
        }
        return false;
    } else {
        return false;
    }*/
    return false;
}

function addNewLine (){
    var thisExampleQuestionAnswer = $(this).parents(".surveyQuestion").find('.exampleQuestionAnswer');
    var questionTypeMultipleChoiceLi = '<li class="multipleChoiceLi">'
        + '<input class="short-input-multiple-checkbox" type="radio" name="TypeSingleChoice" />'
        + '<span class="short-input SingleChoiceInputEdit"><input maxlength="50" type="text" name="Variants" /></span>'
        + '<a href="#" class="removeMultipleChoiceLine"></a>'
        + '</li>';
    $(thisExampleQuestionAnswer).find('li:last-child').after(questionTypeMultipleChoiceLi);
    $('.multipleChoiceLi .removeMultipleChoiceLine').click(removeSingleLine);
    $('.multipleChoiceLi').find('input').focus();
    return false;
}

function addNewCheckboxLine (){
    var thisExampleQuestionAnswer = $(this).parents(".surveyQuestion").find('.exampleQuestionAnswer');
    var questionTypeCheckboxLi = '<li class="multipleChoiceLi">'
        + '<input class="short-input-multiple-checkbox" type="checkbox" name="TypeSingleChoice" />'
        + '<span class="short-input SingleChoiceInputEdit"><input type="text" maxlength="50" name="Variants" /></span>'
        + '<a href="#" class="removeMultipleChoiceLine"></a>'
        + '</li>';
    $(thisExampleQuestionAnswer).find('li:last-child').after(questionTypeCheckboxLi);
    $('.multipleChoiceLi .removeMultipleChoiceLine').click(removeSingleLine);
    $('.multipleChoiceLi').find('input').focus();
    return false;
}

function addNewSelectLine (){
    var thisExampleQuestionAnswer = $(this).parents(".surveyQuestion").find('.exampleQuestionAnswer');
    var questionTypeSelectLi = '<li class="multipleChoiceLi">'
        + '<span class="short-input selectListInputEdit"><input maxlength="50" type="text" name="Variants" /></span>'
        + '<a href="#" class="removeMultipleChoiceLine"></a>'
        + '</li>';
    $(thisExampleQuestionAnswer).find('li:last-child').after(questionTypeSelectLi);
    $('.multipleChoiceLi .removeMultipleChoiceLine').click(removeSingleLine);
    $('.multipleChoiceLi').find('input').focus();
    return false;
}

function addAnotherLine (){
    var thisExampleQuestionAnswer = $(this).parents(".surveyQuestion").find('.exampleQuestionAnswer');
    var questionTypeMultipleChoiceAnotherLi = '<li class="multipleChoiceLi">'
        + '<input class="short-input-multiple-checkbox" type="radio" name="TypeMultipleChoice" />'
        + '<span class="short-input"><input type="text" class="another-answer" value="Your own answer" readonly="readonly" /></span>'
        + '<a href="#" class="removeMultipleChoiceLine"></a>'
        + '</li>';
    $(thisExampleQuestionAnswer).find('li:last-child').after(questionTypeMultipleChoiceAnotherLi);
    $(this).hide();
    $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').hide();
    $('.multipleChoiceLi .removeMultipleChoiceLine').click(function(){
        if($(this).parent().find('.another-answer:visible').length > 0){
            $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').show();
            removeSingleLine();
        }
        return false;
    });
    return false;
}

function addAnotherCheckboxLine (){
    var thisExampleQuestionAnswer = $(this).parents(".surveyQuestion").find('.exampleQuestionAnswer');
    var questionTypeCheckboxAnotherLi = '<li class="multipleChoiceLi">'
        + '<input class="short-input-multiple-checkbox" type="checkbox" name="TypeMultipleChoice" />'
        + '<span class="short-input"><input type="text" class="another-answer" value="Your own answer" readonly="readonly" /></span>'
        + '<a href="#" class="removeMultipleChoiceLine"></a>'
        + '</li>';
    $(thisExampleQuestionAnswer).find('li:last-child').after(questionTypeCheckboxAnotherLi);
    $(this).hide();
    $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').hide();
    $('.multipleChoiceLi .removeMultipleChoiceLine').click(function(){
        if($(this).parent().find('.another-answer:visible').length > 0){
            $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').show();
            removeSingleLine();
        }
        return false;
    });
    return false;
}

function addAnotherSelectLine (){
    var thisExampleQuestionAnswer = $(this).parents(".surveyQuestion").find('.exampleQuestionAnswer');
    var questionTypeSelectAnotherLi = '<li class="multipleChoiceLi">'
        + '<span class="short-input"><input type="text" class="another-answer" value="Your own answer" readonly="readonly" /></span>'
        + '<a href="#" class="removeMultipleChoiceLine"></a>'
        + '</li>';
    $(thisExampleQuestionAnswer).find('li:last-child').after(questionTypeSelectAnotherLi);
    $(this).hide();
    $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').hide();
    $('.multipleChoiceLi .removeMultipleChoiceLine').click(function(){
        if($(this).parent().find('.another-answer:visible').length > 0){
            $(this).parents('.surveyQuestion').find('.multipleChoiceAddNewLine').show();
            removeSingleLine();
        }
        return false;
    });
    return false;
}

function changeMinValueInScaleType(){
    var tmpText =  $(this).parents(".surveyQuestion").find('.selectMin').val();
    $(this).parents(".surveyQuestion").find('.scaleMinLabelVal').text(tmpText);
}

function changeMaxValueInScaleType(){
    var tmpText =  $(this).parents(".surveyQuestion").find('.selectMax').val();
    $(this).parents(".surveyQuestion").find('.scaleMaxLabelVal').text(tmpText);
}

function Survey() {
    
    this.questionsContainer = {};
    
    this.init = function(json) {
        this.questionsContainer = $('div#syrveyCustomForm .surveyBodyQuestions');
        this.Questions = [];
        
        var questions = JSON.parse(json);
        if (questions) {
            for (var i=0; i<questions.length; i++) {
                var question = questions[i];
                this.Questions.push(this.getNewQuestionObject(question.id, question.type,
                                         question.title, question.required, question.variants));
            }
        }
    }
    
    this.draw = function() {
        for (var i=0; i < this.Questions.length; i++) {
            this.questionsContainer.append(this.Questions[i].getPreviewEditHtml());
        }
    }

    this.editQuestion = function (id) {
        var proceed = true;
        var question = this.getQuestion(id);
        if(question) {
            var editedQuestion = this.getEditedQuestion();

            if (editedQuestion) {
                editedQuestion.validate();
                proceed = editedQuestion.isValid;
                if (editedQuestion.isValid) {
                    editedQuestion.flush();
                    editedQuestion.jqObject().replaceWith(editedQuestion.getPreviewEditHtml());
                    editedQuestion.isInEdit = false;
                }
            }

            if (!editedQuestion || (proceed && question.id != editedQuestion.id)) {
                if(question.isInEdit){
                    question.jqObject().replaceWith(question.getPreviewEditHtml());
                } else {
                    question.jqObject().replaceWith(question.getEditHtml());
                }
                question.isInEdit = !question.isInEdit;
            }
        }
    }
    
    this.addQuestion = function() {
        var proceed = true;
        var editedQuestion = this.getEditedQuestion();
        if (editedQuestion) {
            editedQuestion.validate();
            proceed = editedQuestion.isValid;
            if (editedQuestion.isValid) {
                editedQuestion.flush();
                editedQuestion.jqObject().replaceWith(editedQuestion.getPreviewEditHtml());
                editedQuestion.isInEdit = false;
            }
        }

        if (proceed) {
            var newId = 0;
            if (this.Questions.length > 0) {
                newId = this.Questions[this.Questions.length - 1].id;
            }
            var newQuestion = new TextInputQuestion(newId + 1, "Question Title", false);
            this.Questions.push(newQuestion);
            this.questionsContainer.append(newQuestion.getEditHtml());
            newQuestion.isInEdit = true;
        }
    }
    
    this.removeQuestion = function(id) {
        var proceed = true;
        var editedQuestion = this.getEditedQuestion();
        if (editedQuestion && editedQuestion.id != id)
        {
            editedQuestion.validate();
            proceed = editedQuestion.isValid;
            if (editedQuestion.isValid) {
                editedQuestion.flush();
                editedQuestion.jqObject().replaceWith(editedQuestion.getPreviewEditHtml());
                editedQuestion.isInEdit = false;
            }
        }
        
        if (proceed) {
            if (confirm("Are you really want to delete the question?")) {
                var idx = this.indexOf(id);
                if (idx > -1) {
                    this.Questions[idx].jqObject().remove();
                    this.Questions.splice(idx, 1);
                    this.reorderQuestions();
                }
            }
        }
    }
    
    this.saveQuestion = function (id) {
        var question = this.getQuestion(id);
        if (question) {
            question.validate();
            if (question.isValid) {
                question.flush();
                question.jqObject().replaceWith(question.getPreviewEditHtml());
                question.isInEdit = false;
            }
        }
    }
    
    this.onTypeChanged = function (id) {
        var idx = this.indexOf(id)
        if (idx > -1) {
            var oldQuestion = this.Questions[idx];
            var selectedType = oldQuestion.getSelectedTypeValue();
            var newQuestion = this.getNewQuestionObject(oldQuestion.id, selectedType);
            oldQuestion.validate();
            if (oldQuestion.isValid) {
                oldQuestion.flush();
            }
            
            this.convertQuestion(newQuestion, oldQuestion);
            newQuestion.isInEdit = true;
            this.Questions[idx] = newQuestion;
            oldQuestion.jqObject().replaceWith(newQuestion.getEditHtml());
        }
    }

    this.reorderQuestions = function () {
        for (var i=0; i<this.Questions.length; i++) {
            this.Questions[i].setId(i+1);
        }
    }

    this.indexOf = function(id) {
        var result = -1;
        for (var i=0; i<this.Questions.length; i++) {
            if (this.Questions[i].id == id) {
                result = i;
                break;
            }
        }
        return result;
    }
    
    this.getQuestion = function(id) {
        var result;
        var idx = this.indexOf(id)
        if (idx > -1) {
            result = this.Questions[idx];
        }
        return result;
    }
    
    this.getJSON = function() {
        return JSON.stringify(this.Questions);
    }
    
    this.validate = function() {
        var result = true;
        var editedQuestion = this.getEditedQuestion();
        if (editedQuestion) {
            editedQuestion.validate();
            result = editedQuestion.isValid;
            if (editedQuestion.isValid) {
                editedQuestion.flush();
                editedQuestion.jqObject().replaceWith(editedQuestion.getPreviewEditHtml());
                editedQuestion.isInEdit = false;
            }
        }
        return result;
    }
    
    this.getNewQuestionObject = function(id, type, title, required, variants) {
        
        title = title ? title : 'Question Title';
        required = required ? required : false;
        
        if (type == QuestionType.SINGLE_CHOICE || type == QuestionType.MULTIPLE_CHOICE || type == QuestionType.DROPDOWN) {
            variants = variants ? variants : [{weight: 1, title: "Option 1"}, {weight: 2, title: "Option 2"}];
        }
        
        switch (type) {
            case QuestionType.TEXT_INPUT: {
                 return new TextInputQuestion(id, title, required);
            }
            case QuestionType.TEXT_AREA: {
                 return new TextAreaQuestion(id, title, required);
            }
            case QuestionType.SINGLE_CHOICE: {
                 return new SingleChoiceQuestion(id, title, required, variants);
            }
            case QuestionType.MULTIPLE_CHOICE: {
                 return new MultipleChoiceQuestion(id, title, required, variants);
            }
            case QuestionType.DROPDOWN: {
                 return new DropdownQuestion(id, title, required, variants);
            }
            case QuestionType.SCALE: {
                 variants = variants ? variants : {'labelMin': '', 'labelMax': '', minValue: 0, maxValue: 3};
                 return new ScaleQuestion(id, title, required, variants);
            }
            case QuestionType.GRID: {
                 return new GridQuestion(id, title, required);
            }
            case QuestionType.IMAGE: {
                 return new ImageQuestion(id, title, required);
            }
            case QuestionType.VIDEO: {
                 return new VideoQuestion(id, title, required);
            }
            case QuestionType.SMILES: {
                 variants = variants ? variants : {'labelMin': '', 'labelMax': ''};
                 return new SmileQuestion(id, title, required, variants);
            }
        }
        return null;
    }
    
    this.convertQuestion = function(newQuestion, oldQuestion) {
        newQuestion.title = oldQuestion.title;
        newQuestion.required = oldQuestion.required;
        
        if ((oldQuestion.type == QuestionType.DROPDOWN
            || oldQuestion.type == QuestionType.SINGLE_CHOICE
            || oldQuestion.type == QuestionType.MULTIPLE_CHOICE) &&
            (newQuestion.type == QuestionType.DROPDOWN
            || newQuestion.type == QuestionType.SINGLE_CHOICE
            || newQuestion.type == QuestionType.MULTIPLE_CHOICE)) {
            
            newQuestion.variants = oldQuestion.variants;
        }
    }
    
    this.getEditedQuestion = function () {
        var result = null;
        
        for (var i=0; i < this.Questions.length; i++) {
            var tmpQuestion = this.Questions[i];
            if (tmpQuestion.isInEdit) {
                result = tmpQuestion;
                break;
            }
        }
        
        return result;
    }
}

window.survey = new Survey();
