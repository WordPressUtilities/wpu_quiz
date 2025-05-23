<?php defined('ABSPATH') || die; ?><div data-question-id="##question_id##" class="quiz-question-answer">
    <div class="quiz-question-wrapper">
        <label for="quiz-question-##question_id##"><?php echo __('Question', 'wpuquiz') ?></label>
        <input type="text" id="quiz-question-##question_id##" name="quiz_question[##question_id##][question]" value="##question_text##" />
        <input type="hidden" name="quiz_question[##question_id##][id]" value="##question_id##" />
        <input type="hidden" name="quiz_question[##question_id##][order]" value="##question_order##" />
    </div>

    <div class="quiz-answers-wrapper"></div>

    <div class="quiz-answer-add">
        <button type="button" class="button button-secondary quiz-answer-add-button"><?php echo __('Add Answer', 'wpuquiz') ?></button>
    </div>
</div>
