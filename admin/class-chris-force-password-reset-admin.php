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


	/**
	 * Validate the password reset status.
	 * This function runs anytime the user visits the admin page
	 * 
	 * LOGIC
	 * -------------
	 * 1. get the number of days between today and when password was last changed
	 * 2. Check if the days has past 
	 * 3. Redirect the user to the appropriate page
	 * 
	 * @return void
	 */
	public function valid_password_reset() {
			//Get the number of days between now and when the password was last updated.
			$proceed = $this->get_date_diff();

			//If the days are greater than what was specified by the admin.
			if( $proceed > $this->get_password_reset_days()){
				//Redirect the user to the reset password page.
				$this->redirect_user();
			}
	}

	/**
	 * This function is used to display the notification message 
	 * on the admin page.
	 * 
	 * it is only displayed when the password is due for reset.
	 * 
	 * 
	 * LOGIC
	 * -------------
	 * 1. Check is the number of days is due for changes
	 * 2. Create a URL link to reset password.
	 * 3. Calculate the number of days past
	 * 4. Display the notification message.
	 *
	 * @return void
	 */
	function my_error_notice() {
			//Check is the number of days is due for changes
			if($this->get_date_diff() > $this->get_password_reset_days() ){
			//Create a URL link to reset password.
			$url = wp_lostpassword_url( $redirect );
			$reset_link = "<a href={$url}>  RESET PASSWORD</a>";
			//Calculate the number of days past
			$days_past  = $this->get_date_diff() - $this->get_password_reset_days();
			//Display the notification message.
		?>
			 <div class="notice error is-dismissible">
				<p><?php _e( "Your current password was due for reset {$days_past} day(s) ago.{$reset_link}", 'my_plugin_textdomain' ); ?></p>
			</div>
		<?php

		}

	}

	/**
	 * This function to calculate the date difference between today and the last time the password was updated.
	 * 
	 * LOGIC
	 * 1. Configure the option key
	 * 2. Get the current last password updated time
	 * 3. Get the current date  now
	 * 4. Convert both dates to DateTime
	 * 5. Find the diff between the two dates.
	 * 6. return the number of days
	 * 
	 * @return integer
	 */

	private function get_date_diff(){
		 
	
		//configure the option value
		$current_logged_in_user_id = get_current_user_id()."_password_update_timestamp";

		//get the current time and date the password was updated
		$last_password_update_time = get_option($current_logged_in_user_id);

 		//get the current time  
		$now = date('Y-m-d');

		//Convert to date times
		$last_updated  = new DateTime($last_password_update_time);
		$today         = new DateTime($now);

		//get the diff
		$datediff	   = $last_updated->diff($today);

		//return number of days
 		return $datediff->d;
	}

	/**
	 * Redirect the user to password reset Page.
	 * 
	 * LOGIC
	 * ---------------------------------
	 * The logic behind this function is to get the current ID of the logged in user
	 * destroy the session data based on that user and then go ahead to log the user
	 * out of the admin page]
	 * 
	 * redirect the user to the password reset page and exit the page
	 */

	private function redirect_user(){

		$user = get_current_user_id();

		$sessions = WP_Session_Tokens::get_instance($user);
		$sessions->destroy_all();

		//Log the user out
		wp_logout();

		//redirect the user to the password reset page with a message to change password.
		wp_redirect(wp_lostpassword_url($redirect));

		exit;
	}

	/**
	 * Gets the number of days setup by the admin  for the password to resetted.
	 *
	 * @return integer
	 */
	private function get_password_reset_days(){
		$options = get_option($this->plugin_name);
		$number_of_days = $options['number_of_days'];

		return $number_of_days;
	}



}

