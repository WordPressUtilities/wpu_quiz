<?php defined('ABSPATH') || die; ?>
<div class="quiz-score-message">
<div class="quiz-score-message-wrapper">
<div class="quiz-score-message-min-num">
    <label for="quiz-score-message-min-num-##score_id##"><?php echo __('Minimum number of correct answers', 'wpuquiz'); ?></label>
    <input type="number" id="quiz-score-message-min-num-##score_id##" name="quiz_score[##score_id##][min_number]" value="##score_min_number##" min="0" />
</div>
<div class="quiz-score-message-title">
    <label for="quiz-score-message-title-##score_id##"><?php echo __('Title', 'wpuquiz'); ?></label>
    <input type="text" id="quiz-score-message-title-##score_id##" name="quiz_score[##score_id##][title]" value="##score_title##" />
</div>
<div class="quiz-score-message-text">
    <label for="quiz-score-message-##score_id##"><?php echo __('Message', 'wpuquiz'); ?></label>
    <textarea rows="2" id="quiz-score-message-##score_id##" name="quiz_score[##score_id##][message]" class="quiz-score-message-textarea" rows="5">##score_message##</textarea>
</div>
<div class="quiz-score-message-sortable-handle"><span class="dashicons dashicons-move"></span></div>

<div class="quiz-score-message-remove">
    <button type="button" data-quiz-action-remove=".quiz-score-message" class="button button-secondary quiz-score-message-remove-button"><?php echo __('Remove', 'wpuquiz') ?></button>
</div>

</div>
</div>
