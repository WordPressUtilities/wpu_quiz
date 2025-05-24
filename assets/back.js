document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    var $wrapper = document.getElementById('quiz-questions-wrapper');
    if (!$wrapper) {
        return;
    }

    /* ----------------------------------------------------------
      Helpers
    ---------------------------------------------------------- */

    function get_question(_question) {
        if (!_question || typeof _question !== 'object') {
            _question = {};
        }
        if (!_question.question) {
            _question.question = 'Example question';
        }
        if (!_question.hasOwnProperty('id')) {
            _question.id = 'q' + (Math.random()).toString(36).substring(2);
        }
        if (!_question.hasOwnProperty('order') || !_question.order) {
            _question.order = $wrapper.querySelectorAll('.quiz-question-answer').length + 1;
        }
        if (!_question.answers) {
            _question.answers = [get_answer(false, _question)];
        }

        return _question;
    }

    function get_answer(_answer, _question) {
        if (!_answer || typeof _answer !== 'object') {
            _answer = {};
        }
        if (!_answer.hasOwnProperty('text')) {
            _answer.text = 'Example answer';
        }
        if (!_answer.hasOwnProperty('id')) {
            _answer.id = 'a' + _question.id + (Math.random() + 1).toString(36);
        }
        if (!_answer.hasOwnProperty('order') || !_answer.order) {
            _answer.order = document.querySelectorAll('.quiz-answer-wrapper').length + 1;
        }
        if (_answer.correct === undefined) {
            _answer.correct = 0;
        }

        return _answer;
    }

    function add_question_to_form(_question) {

        /* Build question */
        var question_html = _question_template;
        _question = get_question(_question);
        question_html = question_html.replace(/##question_id##/g, _question.id);
        question_html = question_html.replace(/##question_text##/g, _question.question);
        question_html = question_html.replace(/##question_order##/g, _question.order);

        var $question = document.createElement('div');
        $question.innerHTML = question_html;

        /* Build answers */
        var $answers_wrapper = $question.querySelector('.quiz-answers-wrapper');

        for (var j in _question.answers) {
            add_answer_to_question($answers_wrapper, _question, _question.answers[j]);
        }


        $wrapper.appendChild($question);
    }

    function add_answer_to_question($answers_wrapper, _question, _answer) {
        var _answer = get_answer(_answer, get_question(_question));
        var answer_html = _answer_template;
        answer_html = answer_html.replace(/##question_id##/g, _question.id);
        answer_html = answer_html.replace(/##answer_id##/g, _answer.id);
        answer_html = answer_html.replace(/##answer_order##/g, _answer.order);
        answer_html = answer_html.replace(/##answer_text##/g, _answer.text);
        answer_html = answer_html.replace(/##correct_answer##/g, (_answer.correct ? 'checked' : ''));

        var $answer = document.createElement('div');
        $answer.innerHTML = answer_html;

        $answers_wrapper.appendChild($answer);
    }


    /* ----------------------------------------------------------
      Build vars
    ---------------------------------------------------------- */

    var _question_template = document.getElementById('quiz-question-template').innerHTML;
    var _answer_template = document.getElementById('quiz-answer-template').innerHTML;

    if (!quiz_questions || quiz_questions.length === 0) {
        quiz_questions = [get_question()];
    }

    /* ----------------------------------------------------------
      Build form
    ---------------------------------------------------------- */

    for (var i in quiz_questions) {
        add_question_to_form(quiz_questions[i]);
    }

    /* ----------------------------------------------------------
      Events
    ---------------------------------------------------------- */

    /* Add a new question */
    document.getElementById('wpuquiz-add-question').addEventListener('click', function(e) {
        e.preventDefault();
        add_question_to_form();
    });

    /* Add a new answer */
    document.body.addEventListener('click', function(e) {
        if (!e.target || !e.target.classList.contains('quiz-answer-add-button')) {
            return;
        }
        e.preventDefault();
        var $questionDiv = e.target.closest('.quiz-question-answer');
        var $answers_wrapper = $questionDiv.querySelector('.quiz-answers-wrapper');
        var _question = {
            id: $questionDiv.getAttribute('data-question-id')
        };
        add_answer_to_question($answers_wrapper, _question);
    });

    /* Remove an answer or a question */
    document.body.addEventListener('click', function(e) {
        if (!e.target || !e.target.getAttribute('data-quiz-action-remove')) {
            return;
        }
        var $target = e.target.closest(e.target.getAttribute('data-quiz-action-remove'));
        if (!$target) {
            return;
        }
        e.preventDefault();
        if (!confirm(wpuquiz_settings.__str_remove_confirm)) {
            return;
        }
        $target.parentNode.removeChild($target);
    });

    /* Form validation */
    function check_quiz(e) {
        var allowSubmit = true;

        /* A question has no answers or an answer is empty */
        var $questions = document.querySelectorAll('.quiz-question-answer');
        for (var i = 0; i < $questions.length; i++) {
            if (!check_question($questions[i])) {
                allowSubmit = false;
                break;
            }
        }
        if (!$questions || $questions.length === 0) {
            alert(wpuquiz_settings.__str_missing_questions);
            allowSubmit = false;
        }

        if (!allowSubmit) {
            e.preventDefault();
        }
    }

    function check_question($question) {

        /* Missing text */
        var $questionText = $question.querySelector('.quiz-question-text');
        if (!$questionText || !$questionText.value) {
            alert(wpuquiz_settings.__str_missing_question_text);
            return false;
        }

        var questionText = $questionText.value.trim();

        /* No answers */
        var $answers = $question.querySelectorAll('.quiz-answer-text');
        if ($answers.length === 0) {
            alert(wpuquiz_settings.__str_missing_question_answers + "\n" + '-> "' + questionText + '"');
            return false;
        }

        /* Empty answers */
        for (var j = 0; j < $answers.length; j++) {
            if ($answers[j].value.trim() === '') {
                alert(wpuquiz_settings.__str_missing_answer_text + "\n" + '-> "' + questionText + '"');
                return false;
            }
        }

        /* No correct answer */
        if (!$question.querySelector('.quiz-answer-correct-checkbox:checked')) {
            alert(wpuquiz_settings.__str_missing_correct_answer + "\n" + '-> "' + questionText + '"');
            return false;
        }

        return true;

    }

    var $form = document.querySelector('form#post');
    if ($form) {
        $form.addEventListener('submit', check_quiz);
    }

    /* Save button event */
    var $saveBtn = document.getElementById('wpuquiz-save-quiz');
    if ($saveBtn) {
        $saveBtn.addEventListener('click', check_quiz);
    }

});
