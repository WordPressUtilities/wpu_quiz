<?php defined('ABSPATH') || die; ?><div class="quiz-answer">
    <div class="quiz-answer-wrapper">
        <label for="quiz-answer-##answer_id##"><?php echo __('Answer', 'wpuquiz') ?></label>
        <input class="quiz-answer-text" type="text" id="quiz-answer-##answer_id##" name="quiz_question[##question_id##][answers][##answer_id##][text]" value="##answer_text##" />
        <input type="hidden" name="quiz_question[##question_id##][answers][##answer_id##][id]" value="##answer_id##" />
        <input class="quiz-answer-input-order" type="hidden" name="quiz_question[##question_id##][answers][##answer_id##][order]" value="##answer_order##" />
        <div class="quiz-answer-sortable-handle"><span class="dashicons dashicons-move"></span></div>
    </div>
    <div class="quiz-answer-options">
        <div>
            <input class="quiz-answer-correct-checkbox" type="checkbox" id="quiz-answer-correct-##answer_id##" name="quiz_question[##question_id##][answers][##answer_id##][correct]" ##correct_answer## value="1" />
            <label for="quiz-answer-correct-##answer_id##"><?php echo __('Correct answer', 'wpuquiz'); ?></label>
        </div>
    </div>
    <div class="quiz-answer-remove">
        <button type="button" data-quiz-action-remove=".quiz-answer" class="button button-secondary quiz-answer-remove-button"><?php echo __('Remove', 'wpuquiz') ?></button>
    </div>
</div>
