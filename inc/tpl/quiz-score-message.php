<?php defined('ABSPATH') || die; ?>
<div class="quiz-score-message">
<div class="quiz-score-message-min-num">
    <label for="quiz-score-message-min-num-##score_id##"><?php echo __('Minimum number of correct answers', 'wpuquiz'); ?></label>
    <input type="number" id="quiz-score-message-min-num-##score_id##" name="quiz_score[##score_id##][min_number]" value="##score_min_number##" min="0" />
</div>
<div class="quiz-score-message-text">
    <label for="quiz-score-message-##score_id##"><?php echo __('Message', 'wpuquiz'); ?></label>
    <textarea id="quiz-score-message-##score_id##" name="quiz_score[##score_id##][message]" class="quiz-score-message-textarea" rows="5">##score_message##</textarea>
</div>
</div>
