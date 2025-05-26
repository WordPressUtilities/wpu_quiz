<?php
/*
Plugin Name: WPU Quiz
Plugin URI: https://github.com/WordPressUtilities/wpuquiz
Update URI: https://github.com/WordPressUtilities/wpuquiz
Description: Simple quiz plugin for WordPress.
Version: 0.0.5
Author: darklg
Author URI: https://darklg.me/
Text Domain: wpuquiz
Domain Path: /lang
Requires at least: 6.2
Requires PHP: 8.0
License: MIT License
License URI: https://opensource.org/licenses/MIT
*/

if (!defined('ABSPATH')) {
    exit();
}

class WPUQuiz {
    private $plugin_version = '0.0.5';
    private $plugin_settings = array(
        'id' => 'wpuquiz',
        'name' => 'WPU Quiz'
    );
    private $settings;
    private $settings_obj;
    private $settings_details;
    private $basefields;
    private $plugin_description;

    public function __construct() {
        /* INIT */
        add_action('init', array(&$this, 'load_translation'));
        add_action('init', array(&$this, 'load_dependencies_settings'));
        add_action('init', array(&$this, 'load_dependencies_base_fields'));
        add_action('init', array(&$this, 'register_post_type'));

        /* ASSETS */
        add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));

        /* ADMIN PAGE */
        add_action('add_meta_boxes', function () {
            add_meta_box('wpu-quiz-box-question', __('Questions', 'wpuquiz'), array(&$this, 'edit_page_quiz'), 'quiz');
        });
        add_action('save_post', array(&$this, 'save_quiz'));

        /* SHORTCODE */
        add_shortcode('wpuquiz', array(&$this, 'render_quiz_shortcode'));
    }

    /* ----------------------------------------------------------
      Init
    ---------------------------------------------------------- */

    public function load_translation() {
        # TRANSLATION
        $lang_dir = dirname(plugin_basename(__FILE__)) . '/lang/';
        if (strpos(__DIR__, 'mu-plugins') !== false) {
            load_muplugin_textdomain('wpuquiz', $lang_dir);
        } else {
            load_plugin_textdomain('wpuquiz', false, $lang_dir);
        }
        $this->plugin_description = __('Simple quiz plugin for WordPress.', 'wpuquiz');
    }

    public function register_post_type() {
        # POST TYPE
        register_post_type('quiz', array(
            'public' => false,
            'label' => __('Quiz', 'wpuquiz'),
            'menu_icon' => 'dashicons-forms',
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'label' => __('Quiz', 'wpuquiz'),
            'supports' => array('title', 'author'),
            'labels' => array(
                'name' => __('Quizzes', 'wpuquiz'),
                'singular_name' => __('Quiz', 'wpuquiz'),
                'add_new' => __('Add New', 'wpuquiz'),
                'add_new_item' => __('Add New Quiz', 'wpuquiz'),
                'edit_item' => __('Edit Quiz', 'wpuquiz'),
                'new_item' => __('New Quiz', 'wpuquiz'),
                'view_item' => __('View Quiz', 'wpuquiz'),
                'search_items' => __('Search Quizzes', 'wpuquiz'),
                'not_found' => __('No quizzes found.', 'wpuquiz'),
                'not_found_in_trash' => __('No quizzes found in Trash.', 'wpuquiz')
            )
        ));
    }

    public function load_dependencies_settings() {

        # SETTINGS
        $this->settings_details = array(
            # Admin page
            'create_page' => true,
            'plugin_basename' => plugin_basename(__FILE__),
            # Default
            'plugin_name' => $this->plugin_settings['name'],
            'plugin_id' => $this->plugin_settings['id'],
            'option_id' => $this->plugin_settings['id'] . '_options',
            'sections' => array(
                'import' => array(
                    'name' => __('Import Settings', 'wpuquiz')
                )
            )
        );
        $this->settings = array(
            'value' => array(
                'label' => __('My Value', 'wpuquiz')
            )
        );
        require_once __DIR__ . '/inc/WPUBaseSettings/WPUBaseSettings.php';
        //$this->settings_obj = new \wpuquiz\WPUBaseSettings($this->settings_details, $this->settings);
    }

    function load_dependencies_base_fields() {
        require_once __DIR__ . '/inc/WPUBaseFields/WPUBaseFields.php';
        $fields = array(
            'wpuquiz_show_navbar' => array(
                'group' => 'wpuquiz_settings',
                'label' => __('Show nav bar', 'wpu_quiz'),
                'type' => 'checkbox',
                'required' => false
            ),
        );
        $field_groups = array(
            'wpuquiz_settings' => array(
                'label' => __('Settings', 'wpu_quiz'),
                'post_type' => 'quiz',
            )
        );
        $this->basefields = new \wpu_quiz\WPUBaseFields($fields, $field_groups);
    }

    public function admin_enqueue_scripts() {
        /* Back Style */
        wp_register_style('wpuquiz_back_style', plugins_url('assets/back.css', __FILE__), array(), $this->plugin_version);
        wp_enqueue_style('wpuquiz_back_style');
        /* Back Script */
        wp_register_script('wpuquiz_back_script', plugins_url('assets/back.js', __FILE__), array(), $this->plugin_version, true);
        wp_localize_script('wpuquiz_back_script', 'wpuquiz_settings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '__str_remove_confirm' => __('Are you sure you want to remove this?', 'wpuquiz'),
            /* Error messages */
            '__str_missing_questions' => __('You must add at least one question.', 'wpuquiz'),
            '__str_missing_question_text' => __('A question is missing text.', 'wpuquiz'),
            '__str_missing_question_answers' => __('A question is missing answers.', 'wpuquiz'),
            '__str_missing_answer_text' => __('An answer is missing text.', 'wpuquiz'),
            '__str_missing_correct_answer' => __('A question is missing a correct answer.', 'wpuquiz')

        ));
        wp_enqueue_script('wpuquiz_back_script');
    }

    public function wp_enqueue_scripts() {
        /* Front Style */
        wp_register_style('wpuquiz_front_style', plugins_url('assets/front.css', __FILE__), array(), $this->plugin_version);
        wp_enqueue_style('wpuquiz_front_style');
        /* Front Script with localization / variables */
        wp_register_script('wpuquiz_front_script', plugins_url('assets/front.js', __FILE__), array('wp-util'), $this->plugin_version, true);
        wp_localize_script('wpuquiz_front_script', 'wpuquiz_settings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '__str_good_answer' => __('Good answer', 'wpuquiz'),
            '__str_wrong_answer' => __('Wrong answer', 'wpuquiz')
        ));
        wp_enqueue_script('wpuquiz_front_script');
    }

    /* ----------------------------------------------------------
      Admin edition
    ---------------------------------------------------------- */

    function edit_page_quiz() {
        /* Template for a question */
        echo '<script type="text/template" id="quiz-question-template">';
        include __DIR__ . '/inc/tpl/quiz-question.php';
        echo '</script>';
        /* Template for an answer */
        echo '<script type="text/template" id="quiz-answer-template">';
        include __DIR__ . '/inc/tpl/quiz-answer.php';
        echo '</script>';

        $quiz_questions = get_post_meta(get_the_ID(), 'quiz_questions', true);

        if (empty($quiz_questions)) {
            $quiz_questions = json_encode(array());
        }

        echo '<script type="text/javascript">var quiz_questions = ' . $quiz_questions . ';</script>';
        echo '<div id="quiz-questions-wrapper"></div>';
        echo '<button id="wpuquiz-add-question" class="button button-primary quiz-add-question">' . __('Add a question', 'wpuquiz') . '</button>';
        wp_nonce_field('wpuquiz_post_form', 'wpuquiz_post_form_nonce');

    }

    function save_quiz($post_id) {

        /* Only once */
        if (defined('WPUQUIZ__SAVE_POST')) {
            return;
        }
        define('WPUQUIZ__SAVE_POST', 1);

        if (!current_user_can('edit_post', $post_id)) {
            return false;
        }

        if (empty($_POST)) {
            return false;
        }

        if (!isset($_POST['quiz_question']) || !is_array($_POST['quiz_question'])) {
            return false;
        }

        /* Invalid rights */
        if (!isset($_POST['wpuquiz_post_form_nonce']) || !wp_verify_nonce($_POST['wpuquiz_post_form_nonce'], 'wpuquiz_post_form')) {
            wp_nonce_ays('');
        }

        $result = array();
        foreach ($_POST['quiz_question'] as $id => $question) {
            $result_question = $this->validate_question($question, $id);
            if ($result_question) {
                $result[$id] = $result_question;
            }
        }

        update_post_meta($post_id, 'quiz_questions', json_encode($result));

    }

    function validate_question($question, $id) {
        $result_question = array(
            'explanation' => '',
            'show_answer' => 0,
            'id' => $id,
            'answers' => array()
        );

        /* Question */
        if (!isset($question['question']) || empty($question['question'])) {
            return false;
        }
        $result_question['question'] = sanitize_text_field($question['question']);

        /* Answers */
        if (!isset($question['answers']) || !is_array($question['answers']) || empty($question['answers'])) {
            return false;
        }
        foreach ($question['answers'] as $answer_id => $answer) {
            $return_answer = $this->validate_answer($answer, $answer_id);
            if ($return_answer) {
                $result_question['answers'][$answer_id] = $return_answer;
            }
        }
        if (empty($result_question['answers'])) {
            return false;
        }

        /* Order */
        if (!isset($question['order']) || !is_numeric($question['order'])) {
            $question['order'] = microtime(true);
        }
        $result_question['order'] = $question['order'];

        /* Explanation */
        if (isset($question['explanation']) && !empty($question['explanation'])) {
            $result_question['explanation'] = sanitize_text_field($question['explanation']);
        }

        /* Show answer */
        if (isset($question['show_answer'])) {
            $result_question['show_answer'] = 1;
        }

        return $result_question;

    }

    function validate_answer($answer, $answer_id) {
        $result_answer = array(
            'id' => $answer_id,
            'text' => '',
            'correct' => 0
        );

        /* Text */
        if (!isset($answer['text']) || empty($answer['text'])) {
            return false;
        }
        $result_answer['text'] = sanitize_text_field($answer['text']);

        /* Correct */
        if (isset($answer['correct'])) {
            $result_answer['correct'] = 1;
        }

        /* Order */
        if (!isset($answer['order']) || !is_numeric($answer['order'])) {
            $answer['order'] = microtime(true);
        }
        $result_answer['order'] = $answer['order'];

        return $result_answer;
    }

    /* ----------------------------------------------------------
      Front
    ---------------------------------------------------------- */

    /**
     * Render the quiz via shortcode
     */
    public function render_quiz_shortcode($atts = array()) {

        if (!isset($atts['id']) || !is_numeric($atts['id'])) {
            return '';
        }
        $quiz = get_post($atts['id']);
        if (!$quiz || $quiz->post_type !== 'quiz') {
            return '';
        }
        /* Load template */
        add_action('wp_footer', array(&$this, 'load_quiz_template'));

        ob_start();
        include __DIR__ . '/inc/tpl/quiz-front.php';
        return ob_get_clean();

    }

    function load_quiz_template() {

        /* Load once */
        if (defined('WPUQUIZ__TEMPLATE_INSERTED')) {
            return;
        }
        define('WPUQUIZ__TEMPLATE_INSERTED', 1);

        include __DIR__ . '/inc/tpl/quiz-front-template.php';
    }
}

$WPUQuiz = new WPUQuiz();
