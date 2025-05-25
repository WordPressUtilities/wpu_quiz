<?php defined('ABSPATH') || die; ?>
<div data-question-id="##question_id##" class="quiz-question-answer">
    <div class="quiz-question-wrapper">
        <label for="quiz-question-##question_id##"><?php echo __('Question', 'wpuquiz') ?></label>
        <input class="quiz-question-text" type="text" id="quiz-question-##question_id##" name="quiz_question[##question_id##][question]" value="##question_text##" />
        <input type="hidden" name="quiz_question[##question_id##][id]" value="##question_id##" />
        <input type="hidden" name="quiz_question[##question_id##][order]" value="##question_order##" />
    </div>
    <?php /* Answers */?>
    <details>
        <summary><?php echo __('Answers', 'wpuquiz') ?></summary>
        <div class="quiz-answers-wrapper"></div>
        <div class="quiz-answer-add">
            <button type="button" class="button button-secondary quiz-answer-add-button"><?php echo __('Add Answer', 'wpuquiz') ?></button>
        </div>
    </details>
    <details class="quiz-question-settings">
        <summary><?php echo __('Settings', 'wpuquiz') ?></summary>
        <div class="quiz-question-show-answer">
            <input type="checkbox" ##show_answer## id="quiz-question-show-answer-##question_id##" name="quiz_question[##question_id##][show_answer]" value="1" />
            <label for="quiz-question-show-answer-##question_id##"><?php echo __('Show Answer before going to next question', 'wpuquiz') ?></label>
        </div>
        <div class="quiz-question-explanation">
            <label for="quiz-question-explanation-##question_id##"><?php echo __('Explanation of the correct answer', 'wpuquiz') ?></label>
            <textarea id="quiz-question-explanation-##question_id##" name="quiz_question[##question_id##][explanation]" rows="4">##question_explanation##</textarea>
        </div>
    </details>
    <?php /* Action */?>
    <div class="quiz-question-remove">
        <button type="button" data-quiz-action-remove=".quiz-question-answer" class="button button-secondary quiz-question-remove-button"><?php echo __('Remove', 'wpuquiz') ?></button>
    </div>
</div>
