<?php defined('ABSPATH') || die; ?>
<div data-question-id="##question_id##" class="quiz-question-answer">
    <div class="quiz-question-wrapper">
        <label for="quiz-question-##question_id##"><?php echo __('Question', 'wpuquiz') ?></label>
        <input class="quiz-question-text" type="text" id="quiz-question-##question_id##" name="quiz_question[##question_id##][question]" value="##question_text##" />
        <input type="hidden" name="quiz_question[##question_id##][id]" value="##question_id##" />
        <input type="hidden" name="quiz_question[##question_id##][order]" value="##question_order##" />
    </div>
    <details>
        <summary><?php echo __('Answers', 'wpuquiz') ?></summary>
        <div class="quiz-answers-wrapper"></div>
        <div class="quiz-answer-add">
            <button type="button" class="button button-secondary quiz-answer-add-button"><?php echo __('Add Answer', 'wpuquiz') ?></button>
        </div>
    </details>
    <div class="quiz-question-remove">
        <button type="button" data-quiz-action-remove=".quiz-question-answer" class="button button-secondary quiz-question-remove-button"><?php echo __('Remove', 'wpuquiz') ?></button>
    </div>
</div>
