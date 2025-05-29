document.addEventListener("DOMContentLoaded", function() {
    'use strict';

    /* Init quiz */
    Array.prototype.forEach.call(document.querySelectorAll('.wpu-quiz-front'), wpuquiz_setup_quiz);
});


function wpuquiz_setup_quiz($quiz) {
    var $list = $quiz.querySelector('.quiz-questions-list'),
        $result = $quiz.querySelector('.quiz-result'),
        _original_question_template = document.getElementById('wpu-quiz-template').innerHTML,
        _nb_good_answers = 0,
        _question_template = '';

    function callback_next_button() {
        /* Check if valid answer */
        var $question = this.closest('[data-question-id]'),
            _id = $question.getAttribute('data-question-id'),
            $button = $question.querySelector('.quiz-action-next'),
            $input = $question.querySelectorAll('input[type="radio"]:checked'),
            _is_correct = false,
            _prevent_next = false;

        if (!$input.length) {
            return false;
        }

        /* Check answer */
        (function() {
            if (_questions[_id].is_answered) {
                return;
            }
            _questions[_id].is_answered = true;

            /* Mark all inputs as disabled */
            Array.prototype.forEach.call($question.querySelectorAll('input[type="radio"]'), function(el) {
                el.disabled = true;
            });

            for (var i = 0, len = _questions[_id].answers.length; i < len; i++) {
                if (_questions[_id].answers[i].id != $input[0].value) {
                    continue;
                }
                if (_questions[_id].answers[i].correct) {
                    _is_correct = true;
                    _nb_good_answers++;
                }
            }
        }());

        /* Show answer */
        if (_questions[_id].show_answer && !$question.hasAttribute('data-has-visible-answer')) {
            _prevent_next = true;
            $question.setAttribute('data-has-visible-answer', '1');
            $question.querySelector('.quiz-question-answer').innerHTML = _is_correct ?
                '<span class="quiz-answer-correct">' + wpuquiz_settings.__str_good_answer + '</span>' :
                '<span class="quiz-answer-incorrect">' + wpuquiz_settings.__str_wrong_answer + '</span>';

            /* Display correct answer */
            (function() {
                /* Mark all answers as wrong by default */
                Array.prototype.forEach.call($question.querySelectorAll('.quiz-question-answer-item'), function(el) {
                    el.setAttribute('data-has-correct-answer', '0');
                });

                /* Show correct answer */
                for (var i = 0, len = _questions[_id].answers.length; i < len; i++) {
                    if (!_questions[_id].answers[i].correct) {
                        continue;
                    }
                    var $correct_answer = $question.querySelector('input[value="' + _questions[_id].answers[i].id + '"]');
                    if (!$correct_answer) {
                        continue;
                    }
                    $correct_answer.closest('.quiz-question-answer-item').setAttribute('data-has-correct-answer', '1');
                }

            }());
        }

        /* Show explanation */
        if (_questions[_id].explanation && !$question.hasAttribute('data-has-visible-explanation')) {
            _prevent_next = true;
            $question.setAttribute('data-has-visible-explanation', '1');
            $question.querySelector('.quiz-question-explanation-wrapper').innerHTML = '<div class="quiz-question-explanation">' + _questions[_id].explanation + '</div>';
        }

        /* Change button label and prevent going to next */
        if (_prevent_next) {
            $button.querySelector('span').innerHTML = $button.getAttribute('data-label');
            return false;
        }

        /* Go to next */
        var $next_question = $question.nextElementSibling;
        if (!$next_question) {
            /* Show result */
            $list.setAttribute('data-visible', '0');
            $result.setAttribute('data-visible', '1');
            $result.querySelector('.quiz-result-good-answers').innerHTML = _nb_good_answers + '/' + questions.length;
        } else {
            $question.setAttribute('data-visible', '0');
            $next_question.setAttribute('data-visible', '1');
        }

        var current_page = parseInt($question.getAttribute('data-i'), 10);
        $quiz.dispatchEvent(new CustomEvent('wpuquiz:pagechange', {
            detail: {
                current_page: current_page + 1
            }
        }));
    }

    /* Build questions */
    var _questions = JSON.parse($quiz.querySelector('input[name="quiz_content"]').value),
        questions = wpuquiz_convert_and_sort(_questions),
        _last_question = questions.length - 1;

    for (var i = 0, len = questions.length; i < len; i++) {

        /* Replace variables */
        _question_template = _original_question_template;
        _question_template = _question_template.replace(/##question_title##/g, questions[i].question);

        /* Build answers */
        (function() {
            questions[i].answers = wpuquiz_convert_and_sort(questions[i].answers);
            var _answers_html = '';
            for (var j = 0, jlen = questions[i].answers.length; j < jlen; j++) {
                _answers_html += '<li class="quiz-question-answer-item">';
                _answers_html += '<input type="radio" name="question' + questions[i].id + '" id="a' + questions[i].answers[j].id + '" value="' + questions[i].answers[j].id + '" />';
                _answers_html += '<label for="a' + questions[i].answers[j].id + '">' + questions[i].answers[j].text + '</label>';
                _answers_html += '</li>';
            }
            _question_template = _question_template.replace(/##question_answers##/g, _answers_html);
        }());

        (function() {
            /* Create element */
            var $question = document.createElement('li');
            $question.insertAdjacentHTML('beforeend', _question_template);

            /* Setup button */
            var $button = $question.querySelector('.quiz-action-next');
            $question.setAttribute('data-visible', i === 0 ? '1' : '0');
            $question.setAttribute('data-question-id', questions[i].id);
            $question.setAttribute('data-i', i);
            var _button_label = $button.getAttribute(i == _last_question ? 'data-label-submit' : 'data-label-next');
            $button.setAttribute('data-label', _button_label);
            if (questions[i].explanation || questions[i].show_answer) {
                _button_label = $button.getAttribute('data-label-answer');
            }
            $button.querySelector('span').innerHTML = _button_label;
            $button.addEventListener('click', callback_next_button);

            /* Mark */
            $question.addEventListener('change', function(e) {
                if (e.target.checked) {
                    $question.setAttribute('data-has-value', '1');
                }
            });

            /* Append */
            $list.appendChild($question);
        }());
    }

    /* Build navbar */
    (function() {
        /* Elements */
        var $navbar = $quiz.querySelector('.quiz-navbar');
        if (!$navbar) {
            return;
        }
        var $navbar_progress = $navbar.querySelector('.quiz-navbar-progress .bar'),
            $navbar_count = $navbar.querySelector('.quiz-navbar-count');

        /* Watch page change */
        $quiz.addEventListener('wpuquiz:pagechange', function(e) {
            var currentPage = e.detail.current_page,
                progress = Math.round((currentPage + 1 / questions.length) * 100);

            if (currentPage + 1 > questions.length) {
                $navbar.setAttribute('data-visible', '0');
            }

            wpuquiz_set_scalex($navbar_progress, progress / 100);
            $navbar_count.innerHTML = (currentPage + 1) + '/' + questions.length;
        });

        /* Trigger initial */
        $quiz.dispatchEvent(new CustomEvent('wpuquiz:pagechange', {
            detail: {
                current_page: 0
            }
        }));

    }());
}


/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

/* Set transform3d style
-------------------------- */

function wpuquiz_set_scalex(el, x) {
    if (!el) {
        return;
    }
    x = typeof x === 'undefined' ? 0 : x;
    el.style.transform = 'scaleX(' + x + ')';
    el.style.webkitTransform = 'scaleX(' + x + ')';
    el.style.mozTransform = 'scaleX(' + x + ')';
}

/* Sort an object by its "order" property
-------------------------- */

function wpuquiz_convert_and_sort(_obj) {
    var obj = [];
    for (var i in _obj) {
        obj.push(_obj[i]);
    }
    obj.sort(function(a, b) {
        return parseInt(a.order, 10) - parseInt(b.order, 10);
    });
    return obj;
}
