<?php
defined('ABSPATH') || die;

$wpuquiz_show_splash = get_post_meta($quiz->ID, 'wpuquiz_show_splash', 1);
$wpuquiz_show_navbar = get_post_meta($quiz->ID, 'wpuquiz_show_navbar', 1);
$wpuquiz_show_title = get_post_meta($quiz->ID, 'wpuquiz_show_title', 1);
$questions = get_post_meta($quiz->ID, 'quiz_questions', 1);
if (!is_array($questions)) {
    $questions = array();
}
$scores = get_post_meta($quiz->ID, 'quiz_scores', 1);
if (!is_array($scores)) {
    $scores = array();
}

echo '<div class="wpu-quiz-front" ' . ($wpuquiz_show_splash ? 'data-quiz-has-splash="1"' : '') . ' data-quiz-id="' . $quiz->ID . '">';

if ($wpuquiz_show_splash) {
    echo '<div class="quiz-splash">';
    if ($wpuquiz_show_title) {
        echo '<h2 class="quiz-splash-title">' . esc_html($quiz->post_title) . '</h2>';
    }
    echo '<button class="quiz-action-start" data-action="start-quiz">' . __('Start Quiz', 'wpuquiz') . '</button>';
    echo '</div>';
}

/* Main title */
if ($wpuquiz_show_title && !$wpuquiz_show_splash) {
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
echo '<div class="quiz-result-message-score"></div>';
echo '</div>';

/* Values */
echo '<div><input type="hidden" name="quiz_content" value="' . esc_attr(json_encode($questions)) . '" /></div>';
echo '<div><input type="hidden" name="quiz_scores" value="' . esc_attr(json_encode($scores)) . '" /></div>';

echo '</div>';
