<?php
defined('ABSPATH') || die;

$wpuquiz_show_navbar = get_post_meta($quiz->ID, 'wpuquiz_show_navbar', 1);
$wpuquiz_show_title = get_post_meta($quiz->ID, 'wpuquiz_show_title', 1);
$_questions = get_post_meta($quiz->ID, 'quiz_questions', 1);
$questions = json_decode($_questions, true);

echo '<div class="wpu-quiz-front" data-quiz-id="' . $quiz->ID . '">';

/* Main title */
if ($wpuquiz_show_title) {
    echo '<h2 class="quiz-title">' . esc_html($quiz->post_title) . '</h2>';
}

/* Nav bar */
if ($wpuquiz_show_navbar) {
    echo '<div class="quiz-navbar">';
    echo '<div class="quiz-navbar-progress"><div class="bar"></div></div>';
    echo '<div class="quiz-navbar-count">1/' . count($questions) . '</div>';
    echo '</div>';
}

/* Questions */;
echo '<div class="quiz-questions">';
echo '<ul class="quiz-questions-list" data-visible="1"></ul>';
echo '</div>';

/* Result */
echo '<div class="quiz-result" data-visible="0">';
echo '<h3>' . __('Thank you!', 'wpuquiz') . '</h3>';
echo '<div class="quiz-result-good-answers"></div>';
echo '</div>';

/* Values */
echo '<div><input type="hidden" name="quiz_content" value="' . esc_attr($_questions) . '" /></div>';

echo '</div>';
