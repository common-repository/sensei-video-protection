<?php
/*
* Plugin Name: Sensei-video-protection free
* Plugin URI: http://senseivp.com
* Description: 
* Version: 1.0
* Author: Yonghoon Lee
* Author URI: http://senseivp.com
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0
* Slug: Sensei-video-protection free
*/
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Sensei_Security' ) ) {

    class Sensei_Security {
		public function __construct(){
            $this->slug = 'sensei-security';
			add_action( 'sensei_course_single_lessons', array($this,'course_single_lessons') );

			add_action( 'sensei_lesson_single_meta', array($this,'lesson_single_meta') );			
			add_action( 'sensei_lesson_course_signup', array( $this, 'sensei_lesson_course_signup_link' ));

			add_action('admin_init', 'plugin_admin_init');
			add_action('admin_menu', 'plugin_admin_add_page');

			add_action('init', array($this, 'remove_lessons'));
		}

		public function remove_lessons(){
			remove_action( 'sensei_course_single_lessons', 'course_single_lessons', 10 );
			add_action( 'sensei_course_single_lessons', array($this,'change_lessons'), 10 );
		}

		public function change_lessons() {			
			global $woothemes_sensei;
			ob_start();
			$woothemes_sensei->frontend->sensei_get_template( 'single-course/course-lessons.php' );
			$html = ob_get_contents();
			ob_end_clean();

			$html = preg_replace('/<header><h2><a href="([^"]*)"/i', '<header><h2><a href="#" data-href="$1"', $html);
			echo $html;
		}
		

		public function course_single_lessons(){
			$options = get_option('plugin_options');
			echo "<script type='text/javascript'>window.sensei_security_opt={firefox:'{$options['firefox']}',mobile:'{$options['mobile']}'};</script>";
	        wp_enqueue_script('sensei_security_course_single_lessons', plugins_url('/assets/js/course_single_lessons.js', __FILE__), array('jquery'));
		}
		public function lesson_single_meta(){
			$options = get_option('plugin_options');
			echo "<script type='text/javascript'>window.sensei_security_opt={firefox:'{$options['firefox']}',mobile:'{$options['mobile']}'};</script>";
	        wp_enqueue_script('sensei_security_single_lesson', plugins_url('/assets/js/single_lesson.js', __FILE__), array('jquery'));
		}
		public function sensei_lesson_course_signup_link(){
			$options = get_option('plugin_options');
			echo "<script type='text/javascript'>window.sensei_security_opt={firefox:'{$options['firefox']}',mobile:'{$options['mobile']}'};</script>";
	        wp_enqueue_script('sensei_security_single_lesson', plugins_url('/assets/js/single_lesson.js', __FILE__), array('jquery'));
		}
	}
	$sensei_security = new Sensei_Security();

	
	function plugin_admin_add_page() {
		add_options_page('Sensei security', 'Sensei security', 'manage_options', 'sensei-security-cfg', 'plugin_options_page');
	}
			
	function plugin_admin_init(){
		register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
		add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
		add_settings_field('plugin_firefox', 'Disable Firefox Access : ', 'plugin_setting_firefox', 'plugin', 'plugin_main');
		add_settings_field('plugin_mobile', 'Mobile New Window : ', 'plugin_setting_mobile', 'plugin', 'plugin_main');
	}
	function plugin_section_text() {
		echo '<p>Disable Firefox / Mobile New Lesson Window / Video Code encryption (Pro version only)</p>';
	}
	function plugin_setting_firefox() {
		$options = get_option('plugin_options');
		$checked_y = $options['firefox'] == 'Y' ? 'checked' : '';
		$checked_n = $options['firefox'] == 'Y' ? '' : 'checked';
		echo "<label>Yes <input name='plugin_options[firefox]' type='radio' value='Y' {$checked_y}/></label><label>No <input name='plugin_options[firefox]' type='radio' value='N' {$checked_n}/></label><br/>";
	}
	function plugin_setting_mobile() {
		$options = get_option('plugin_options');
		$checked_y = $options['mobile'] == 'Y' ? 'checked' : '';
		$checked_n = $options['mobile'] == 'Y' ? '' : 'checked';
		echo "<label>New Window <input name='plugin_options[mobile]' type='radio' value='Y' {$checked_y}/></label><label>Current Window<input name='plugin_options[mobile]' type='radio' value='N' {$checked_n}/></label><br/>";
	}
	function plugin_options_validate($input) {
		$options = get_option('plugin_options');
		$options['firefox'] = $input['firefox'];
		$options['mobile'] = $input['mobile'];
		return $options;
	}
	function plugin_options_page() {
	?>
		<div>
		<h2>Sensei security</h2>
		<form action="options.php" method="post">
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections('plugin'); ?>
		 
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form></div>
		<?php
	}
}
?>