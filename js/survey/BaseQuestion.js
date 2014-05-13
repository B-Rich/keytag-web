function BaseQuestion(id, type, title, required) {
    this.id = id;
    this.type = type;
    this.title = title;
    this.required = required;
    
    this.getEditHtml = function() {}
    this.getPreviewEditHtml = function() {}
    this.getViewHtml = function() {}
    
    this.isInEdit = false;
    
    this.flush = function() {
        this.title = this.jqObject().find(".surveyStaticTitle").val();
        this.required = this.getCheckboxValue(this.jqObject().find(".surveyQuestionRequiredCheckbox"));
    }
    
    this.DOMId = function() {
        return "question_" + this.id;
    }
    
    this.jqObject = function() {
        return $('#'+this.DOMId());
    }
    
    this.jqErrorObject = function() {
        return this.jqObject().find('#' + this.errorLabelId(this.id));
    }
    
    this.setId = function(id) {
        if (this.id != id) {
            var select = this.jqObject().find('.questionTypeSelect');
            var editButton = this.jqObject().find('.editSurvey');
            var deleteButton = this.jqObject().find('.deleteSurvey');
            var doneButton = this.jqObject().find('.surveyQuestionDone');
            var errorLabel = this.jqObject().find('.surveyError');

            select.attr('onchange', this.changeTypeHandler(id));
            editButton.attr('href', this.wrapHref(this.editHandler(id)));
            deleteButton.attr('href', this.wrapHref(this.removeHandler(id)));
            doneButton.attr('href', this.wrapHref(this.saveHandler(id)));
            errorLabel.attr('id', this.errorLabelId(id));
            
            this.jqObject().attr('id', 'question_' + id);
            this.id = id;
        }
    }
    
    this.beginWrapperHtml = function (classes) {
        return '<div id="' + this.DOMId() + '" class="surveyQuestion ' + classes + '">';
    }
    
    this.getQuestionTypeDropdown = function() {
        var result = '<span class="surveyQuestionType"><select class="questionTypeSelect" onchange="' + this.changeTypeHandler(this.id) + '">'
            + '<option ' + this.getSelectedAttr(QuestionType.TEXT_INPUT) 
                + ' value="' + QuestionType.TEXT_INPUT + '" class="typeText">Text</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.TEXT_AREA) 
                + ' value="' + QuestionType.TEXT_AREA + '" class="typeTextArea">Paragraphed Text</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.SINGLE_CHOICE) 
                + ' value="' + QuestionType.SINGLE_CHOICE + '" class="multipleChoice">Single Choice</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.MULTIPLE_CHOICE) 
                + ' value="' + QuestionType.MULTIPLE_CHOICE + '" class="checkBoxes">Multiple Choice</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.DROPDOWN)
                + ' value="'+ QuestionType.DROPDOWN + '" class="selectList">Select List</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.SCALE)
                + ' value="' + QuestionType.SCALE + '" class="scale">Scale</option>'
            //+ '<option value="' + QuestionType.GRID + '" class="typeText">Grid</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.IMAGE)
                + ' value="'+ QuestionType.IMAGE + '" class="imageUpload">Image Upload</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.VIDEO)
                + ' value="'+ QuestionType.VIDEO + '" class="videoUpload">Video Upload</option>'
            + '<option ' + this.getSelectedAttr(QuestionType.SMILES)
                + ' value="'+ QuestionType.SMILES + '" class="smileSelect">Smiles</option>'
            + '</select></span><div class="clear"></div>';
        return result;
    }
    
    this.getTopActionButtons = function(direction) {
        var result = '<div class="surveyControlButtons">'
            + '<a title="Edit this question" href="javascript:' + this.editHandler(this.id) + ';"'
                + ' class="editSurvey' + (direction ? ' activeBttnSurvey' : '') + '"></a>'
            + '<a title="Delete this question" href="javascript:' + this.removeHandler(this.id) + ';" class="deleteSurvey"></a>'
            + '<span class="sortSurveyQuestion"></span>'
            + '</div>';
        return result;
    }
    
    this.getBottomActionButtons = function() {
        var result = '<div class="row surveyQuestionRow">'
            + '<label id="' + this.errorLabelId(id) + '" class="surveyError" style="width: 200px; color: red; font-weight: bold;">&nbsp;</label>'
            + '<a href="javascript:' + this.saveHandler(this.id) + ';" class="surveyQuestionDone">Done</a>'
            + '<input type="checkbox" class="surveyQuestionRequiredCheckbox"' + this.getCheckedAttr() + ' />'
            + '<label class="label-for-checkbox surveyQuestionRequiredLabel">Make this question required</label>'
            + '</div><div class="clear"></div>';
        return result;
    }
    
    this.getQuestionTitle = function() {
        return '<div class="surveyQuestionRow"><label>Question title</label><span class="short-input input-width-350"><input type="text"'
                + ' class="surveyStaticTitle" value="' + this.title + '" ' + this.getInputMaxLength(this.titleLength)
                + ' /></span></div><div class="clear" />';
    }

    this.getSelectedAttr = function(type) {
        return type == this.type ? 'selected="selected"' : '';
    }
    
    this.getCheckedAttr = function() {
        return this.required ? ' checked' : '';
    }
    
    this.getCheckboxValue = function(checkbox) {
        return checkbox.attr('checked') == 'checked';
    }
    
    this.getSelectedTypeValue = function() {
        return parseInt(this.jqObject().find(".questionTypeSelect").val());
    }
    
    this.isValid = true;
    
    this.validate = function() {
        var errorObject = this.jqErrorObject();
        var titleObject = this.jqObject().find(".surveyStaticTitle");
        var title = titleObject.val();
        this.isValid = true;
        errorObject.text('');

        if (!title || title == '') {
            this.isValid = false;
            errorObject.text('Please enter Question title');
        }
        else if (title.length > this.titleLength) {
            this.isValid = false;
            errorObject.text('Question title is too long');
        }
        if (!this.isValid) {
            titleObject.focus();
        }
    }
    
    //misc
    this.changeTypeHandler = function (id) {
        return 'window.survey.onTypeChanged(' + id + ')';
    }
    
    this.editHandler = function (id) {
        return 'window.survey.editQuestion(' + id + ')';
    }
    
    this.saveHandler = function (id) {
        return 'window.survey.saveQuestion(' + id + ')';
    }
    
    this.removeHandler = function (id) {
        return 'window.survey.removeQuestion(' + id + ')';
    }
    
    this.errorLabelId = function(id) {
        return 'error_' + id;
    }
    
    this.wrapHref = function(str) {
        return 'javascript:' + str + ';';
    }
    
    this.getInputMaxLength = function(length) {
        if (length) {
            return 'maxlength="' + length + '"';
        }
        return "";
    }
    
    this.titleLength = 200;
}