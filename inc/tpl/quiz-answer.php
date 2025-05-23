<?php defined('ABSPATH') || die; ?><div class="quiz-answer">
    <div class="quiz-answer-text">
        <label for="quiz-answer-##answer_id##"><?php echo __('Answer', 'wpuquiz') ?></label>
        <input type="text" id="quiz-answer-##answer_id##" name="quiz_question[##question_id##][answers][##answer_id##][text]" value="##answer_text##" />
        <input type="hidden" name="quiz_question[##question_id##][answers][##answer_id##][id]" value="##answer_id##" />
        <input type="hidden" name="quiz_question[##question_id##][answers][##answer_id##][order]" value="##answer_order##" />
    </div>
    <div class="quiz-answer-options">
        <label for="quiz-answer-correct-##answer_id##"><?php echo __('Correct answer', 'wpuquiz'); ?></label>
        <input type="checkbox" id="quiz-answer-correct-##answer_id##" name="quiz_question[##question_id##][answers][##answer_id##][correct]" ##correct_answer## value="1" />
    </div>
</div>
