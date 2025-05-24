<?php
defined('ABSPATH') || die;
echo '<div class="wpu-quiz-front" data-quiz-id="' . $quiz->ID . '">';

/* Main title */
echo '<h2>' . esc_html($quiz->post_title) . '</h2>'

/* Questions */;
echo '<div class="quiz-questions">';
echo '<ul class="quiz-questions-list" data-visible="1"></ul>';
echo '</div>';

/* Result */
echo '<div class="quiz-result" data-visible="0">';
echo '<h3>'.__('Thank you!', 'wpuquiz').'</h3>';
echo '<div class="quiz-result-good-answers"></div>';
echo '</div>';

/* Values */
echo '<div><input type="hidden" name="quiz_content" value="' . esc_attr(get_post_meta($quiz->ID, 'quiz_questions', 1)) . '" /></div>';

echo '</div>';
