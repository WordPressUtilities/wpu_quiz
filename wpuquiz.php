<?php
/*
Plugin Name: WPU Quiz
Plugin URI: https://github.com/WordPressUtilities/wpuquiz
Update URI: https://github.com/WordPressUtilities/wpuquiz
Description: Simple quiz plugin for WordPress.
Version: 0.0.1
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
    private $plugin_version = '0.0.1';
    private $plugin_settings = array(
        'id' => 'wpuquiz',
        'name' => 'WPU Quiz'
    );
    private $settings;
    private $settings_obj;
    private $settings_details;
    private $plugin_description;

    public function __construct() {
        /* INIT */
        add_action('init', array(&$this, 'load_dependencies_settings'));
        add_action('init', array(&$this, 'load_translation'));
        add_action('init', array(&$this, 'register_post_type'));
        add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));

        /* ADMIN PAGE */
        add_action('add_meta_boxes', function () {
            add_meta_box('wpu-quiz-box-id', 'quiz box', array(&$this, 'edit_page_quiz'), 'quiz');
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
            'supports' => array('title', 'author')
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

    public function admin_enqueue_scripts() {
        /* Back Style */
        wp_register_style('wpuquiz_back_style', plugins_url('assets/back.css', __FILE__), array(), $this->plugin_version);
        wp_enqueue_style('wpuquiz_back_style');
        /* Back Script */
        wp_register_script('wpuquiz_back_script', plugins_url('assets/back.js', __FILE__), array(), $this->plugin_version, true);
        wp_enqueue_script('wpuquiz_back_script');
    }

    public function wp_enqueue_scripts() {
        /* Front Style */
        wp_register_style('wpuquiz_front_style', plugins_url('assets/front.css', __FILE__), array(), $this->plugin_version);
        wp_enqueue_style('wpuquiz_front_style');
        /* Front Script with localization / variables */
        wp_register_script('wpuquiz_front_script', plugins_url('assets/front.js', __FILE__), array('wp-util'), $this->plugin_version, true);
        wp_localize_script('wpuquiz_front_script', 'wpuquiz_settings', array(
            'ajax_url' => admin_url('admin-ajax.php')
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

        if (!isset($_POST['quiz_question'])) {
            return false;
        }

        /* Invalid rights */
        if (!isset($_POST['wpuquiz_post_form_nonce']) || !wp_verify_nonce($_POST['wpuquiz_post_form_nonce'], 'wpuquiz_post_form')) {
            wp_nonce_ays('');
        }

        update_post_meta($post_id, 'quiz_questions', json_encode($_POST['quiz_question']));

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

        ob_start();
        include __DIR__ . '/inc/tpl/quiz-front.php';
        return ob_get_clean();

    }
}

$WPUQuiz = new WPUQuiz();
