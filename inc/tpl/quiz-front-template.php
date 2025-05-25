<?php
defined('ABSPATH') || die;

/* Question template */
echo '<script type="text/template" id="wpu-quiz-template"><div class="quiz-question-item">';
echo '<h3 class="quiz-question-title">##question_title##</h3>';
echo '<ul class="quiz-question-answers">##question_answers##</ul>';

/* Answer */
echo '<div class="quiz-question-answer"></div>';

/* Explanation */
echo '<div class="quiz-question-explanation"></div>';

/* - Action */
echo '<div class="quiz-question-action">';
echo '<button type="button" class="quiz-action-next" data-label-answer="' . esc_attr(__('Answer this', 'wpuquiz')) . '" data-label-next="' . esc_attr(__('Next', 'wpuquiz')) . '" data-label-submit="' . esc_attr(__('Finish', 'wpuquiz')) . '"><span></span></button>';
echo '</div>';
/* - Wrapper */
echo '</div></script>';
