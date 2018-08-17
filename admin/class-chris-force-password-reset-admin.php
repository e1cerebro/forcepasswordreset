<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Chris_Force_Password_Reset
 * @subpackage Chris_Force_Password_Reset/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chris_Force_Password_Reset
 * @subpackage Chris_Force_Password_Reset/admin
 * @author     ChristianNwachukwu <nwachukwu16@gmail.com>
 */
class Chris_Force_Password_Reset_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chris_Force_Password_Reset_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chris_Force_Password_Reset_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chris-force-password-reset-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chris_Force_Password_Reset_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chris_Force_Password_Reset_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chris-force-password-reset-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
		 * This function runs when the user updates the password in their profile
		 * The function only updates the database if the password is updated
		 * 
		 * LOGIC BEHIND THE FUNCTION
		 * ----------------------------------------------------------
		 * 1. At first I checked the capability of the current logged in user. 
		 * 2. Then I checked to see if the POST REQUEST has the password field filled and the length greater than one
		 * 3. To properly create a well formatted and unique options name for all the users, I concatenated the user Id with the string.
		 * 4. Created the current date the password was changed 
		 * 5. Saved the data in the database options table. 
		 *
		 * @return void
	 */
	public function fpr_profile_update( $user_id ) {
		
		if( current_user_can('editor') || current_user_can('administrator') ) { 
				if(isset($_POST['pass1']) && '' != $_POST['pass1'] && strlen($_POST['pass1']) > 1 ){
					
					//configure the option value
					$current_logged_in_user_id = get_current_user_id()."_password_update_timestamp";

					//get the current time and date the password was updated
					$value = date('Y-m-d');

					//update the options table
					$result = update_option( $current_logged_in_user_id , $value);
				}
 		 } 

	}

	/**
	 * Creates a settings page under the admin settings menu
	 *
	 * @return void
	 */ 
 	public function fpr_settings_page() {

				add_options_page(
								'Force WP Password Reset', //Page Title
								'FWP Password Reset ',     //Menu Title
								'manage_options',		//Capabilities
								'fpr-settings-page-page-slug',  //Page slug
								array($this, 'fpr_settings_page_callback_function') //Callback function
							);
	}


	/**
	 * Callback function for handling the view of the force password reset settings page.
	 *
	 * @return void
	 */
	public function fpr_settings_page_callback_function() {
		 //Include the path to the page template.
 		 include_once( 'partials/chris-force-password-reset-admin-display.php' );
	}


	/**
	 * Validate the user input before saving that to the options table in the database.
	 * 
	 * @return array; 
	 */
	public function validate($input) {
		// create an array to save the inputs.      
		$valid = array();
	
		//Validate the user inputs and add them to the array.
		$valid['administrator'] = (isset($input['administrator']) && !empty($input['administrator'])) ? 1 : 0;
		$valid['editor'] = (isset($input['editor']) && !empty($input['editor'])) ? 1: 0;
		$valid['author'] = (isset($input['author']) && !empty($input['author'])) ? 1 : 0;
		$valid['contributor'] = (isset($input['contributor']) && !empty($input['contributor'])) ? 1 : 0;
		$valid['subscriber'] = (isset($input['subscriber']) && !empty($input['subscriber'])) ? 1 : 0;
		$valid['number_of_days'] = sanitize_text_field($input['number_of_days']);
	
		return $valid;
	 }

	/**
	 * 
	 * Register the new settings in the database.
	 * 
	 * @return void
	 * 
	 */
	public function options_update() {
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}


	public function valid_password_reset() {

		$proceed = $this->get_date_diff();

		if( $proceed > $number_of_days){
			  $this->redirect_user();
			 //die("Point User to your custom password reset page");
		 }
	}


	function my_error_notice() {
			
		  $options = get_option($this->plugin_name);
		
			$number_of_days = $options['number_of_days'];
			

			if($this->get_date_diff() > $number_of_days){

			$url = wp_lostpassword_url( $redirect );
			$reset_link = "<a href={$url}>  RESET PASSWORD</a>";

		?>
			 <div class="notice error my-acf-notice is-dismissible">
				<p><?php _e( "Your current password was due for reset {$this->get_date_diff()} day(s) ago.{$reset_link}", 'my_plugin_textdomain' ); ?></p>
			</div>
		<?php
			// wp_logout();
			// $this->redirect_user();
		
		}

	}

	private function get_date_diff(){
		 
		$options = get_option($this->plugin_name);
		
		$number_of_days = $options['number_of_days'];

		//configure the option value
		$current_logged_in_user_id = get_current_user_id()."_password_update_timestamp";

		//get the current time and date the password was updated
		$last_password_update_time = get_option($current_logged_in_user_id);

 		//get the current time  
		$now = date('Y-m-d');

		$last_updated  = new DateTime($last_password_update_time);
		$today         = new DateTime($now);

		$datediff	   = $last_updated->diff($today);

 		return $datediff->d;
	}

	private function redirect_user(){

		$user = get_current_user_id();

		$sessions = WP_Session_Tokens::get_instance($user);
		$sessions->destroy_all();

		//Log the user out
		wp_logout();

		//redirect the user to the password reset page with a message to change password.
		wp_redirect($request = $_SERVER["HTTP_REFERER"]);

		exit;
	}

	public static function modifyLostPasswordURL($lostpwUrl, $redirect = '') {
        $lostpwUrl = wp_login_url() . '#lostpassword'; // Link to lostpassword URL

        if(!empty($redirect)) {
            $lostpwUrl = add_query_arg('redirect_to', urlencode($redirect), $lostpwUrl);
        }

        return $lostpwUrl;
    }

}

