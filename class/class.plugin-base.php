<?php

if (!class_exists('plugin_base')) :

	class plugin_base {
		
		//declaring different variables needed for class
		public $sessions_needed;
		private $db;
		private $definitions = array();
		private $error = array();
		private $access_level;
		
		public function __construct() {
			global $wpdb;
			
			$this->db = $wpdb;
			
			//place to set initial settings for plugin
			$this->sessions_needed = true;	
			$this->access_level = "manage_options";
			
			register_activation_hook(__FILE__, array(&$this,'activate'));
			register_deactivation_hook(__FILE__,array(&$this,'deactivate'));

			add_action('init', array(&$this, 'init'));
			
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));

		}
		
		public static function activate() {
			/*
			This is so you can then access variables if needed in activation of plugin.
			Like if you need to access the variable $sessions_needed you can then call $plugin_base->sessions_needed
			*/
			$plugin_base = new plugin_base(); 

		}
		
		public static function deactivate() {
			/*
			This is so you can then access variables if needed in deactivation of plugin.
			Like if you need to access the variable $sessions_needed you can then call $plugin_base->sessions_needed
			*/
			$plugin_base = new plugin_base();
			
		}
		public function init() {
			if ($this->sessions_needed) :
				if (!session_id()) :
					session_start();
				endif;
			endif;
			
			$this->init_settings();				
			
		}
		
		public function define_variable($variablename, $variabledata) {
			define($variablename, $variabledata);
			$this->definitions[$variablename] = $variabledata;
			return true;
		}
		
		public function admin_init() {
			//customized initilization for the admin area only
			$this->init_settings();
			
		}
		
		public function init_settings() {
			//customized init settings for entire program backend and front end
		}
		
		public function update_options() {
			
		}
		
		public function add_menu() {
			/* This menu choice adds the menu choice under the main menu settings */
			//add_options_page('Plugin Ba Settings', 'Incon Tracking', 'manage_options', 'incon_tracking_settings', array(&$this, 'incon_tracking_settings_page'));
			
			/* This menu choice adds it as a main menu allowing for sub pages */
			// Add the top-level admin menu to backend of WordPress
			$page_title = 'My Custom Plugin Settings';
			$menu_title = 'My Custom Plugin';
			$capability = $this->access_level; //'manage_options';
			$menu_slug = 'pluginbase-settings';
			$menu_function = array(&$this, 'settings_page');
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, $menu_function);
		 
			// Add submenu page with same slug as parent to ensure no duplicates
			$sub_menu_title = 'Settings';
			add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $menu_function);
		 
			// Now add the submenu page for Help
			$submenu_page_title = 'My Plugin 2nd Page';
			$submenu_title = '2nd Page';
			$submenu_slug = 'pluginbase-second';
			$submenu_function = array(&$this, 'sample_page');
			add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
			
		}

		public function settings_page() {
			$this->check_user();
			//if passed then display following code to user
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
<?php			
			//$this->add_error_msg("Test Error Message");
			echo "<h1>Plugin Base Settings Page</h2>";
			$this->disp_errors();
?>
</div>
<?php
			
		}
		
		public function sample_page() {
			$this->check_user();
			//if passed then display following code to user
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
<?php			

			echo "<h1>Plugin Base 2nd Page</h2>";
			$this->disp_errors();
?>
</div>
<?php
			
		}
		
		public function initialize_settings_options() {
			
			// First, we register a section. This is necessary since all future options must belong to a 
				add_settings_section(
					'general_settings_section',         // ID used to identify this section and with which to register options
					'Sandbox Options',                  // Title to be displayed on the administration page
					array(&$this, 'general_options_callback'), // Callback used to render the description of the section
					'pluginbase-settings'                           // Page on which to add this section of options
				);
				 
				// Next, we'll introduce the fields for toggling the visibility of content elements.
				add_settings_field( 
					'show_header',                      // ID used to identify the field throughout the theme
					'Header',                           // The label to the left of the option interface element
					array(&$this, 'sandbox_toggle_header_callback'),   // The name of the function responsible for rendering the option interface
					'pluginbase-settings',                          // The page on which this option will be displayed
					'general_settings_section',         // The name of the section to which this field belongs
					array(                              // The array of arguments to pass to the callback. In this case, just a description.
						'Activate this setting to display the header.'
					)
				);
				 
				register_setting(
					'pluginbase-settings',
					'show_header'
				);
				 
				/*register_setting(
					'pluginbase-settings',
					'pluginbase-settings'
				);	*/			
							
		}
		
		function sandbox_toggle_header_callback($args) {
		 
			$options = get_option('sandbox_theme_display_options');
		 
			$html = '<input type="checkbox" id="show_header" name="sandbox_theme_display_options[show_header]" value="1" ' . checked(1, $options['show_header'], false) . '/>'; 
			$html .= '<label for="show_header"> '  . $args[0] . '</label>'; 
		 
			echo $html;
		 
		} // end sandbox_toggle_header_callback
		 
		function general_options_callback() {
			echo '<p>Select which areas of content you wish to display.</p>';
		} // end sandbox_general_options_callback
		 
		/* ------------------------------------------------------------------------ *
		 * Field Callbacks
		 * ------------------------------------------------------------------------ */
		 
		function toggle_header_callback($args) {
			 
			// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
			$html = '<input type="checkbox" id="show_header" name="show_header" value="1" ' . checked(1, get_option('show_header'), false) . '/>'; 
			 
			// Here, we'll take the first argument of the array and add it to a label next to the checkbox
			$html .= '<label for="show_header"> '  . $args[0] . '</label>'; 
			 
			echo $html;
			 
		} // end sandbox_toggle_header_callback
		 

		/* 
		 * The check_user function checks to see if they have sufficient permissions
		 * and if not then displays error message and does a wp_die.
		 */
		private function check_user($user_can) {
			if ($user_can == '') $user_can = $this->access_level;
			if (!current_user_can($user_can)) {
				wp_die(__('You do not have sufficient permissions to access this page.'));	
			}
			return true;
		}
		
		private function create_table($tablename, $variableSql) {
			/*		Sample $variableSql data
			
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `varcharexample` varchar(64) NOT NULL,
					  `textexample` text NOT NULL,
					  `intexample` int(11) NOT NULL DEFAULT '',
					  `timestampexample` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  UNIQUE ( `id` )
			*/
			if ($variableSql != '') {
				$sql = "CREATE TABLE IF NOT EXISTS `".$tablename."` ( ".$variableSql.");";
				$this->db->query($sql);
				return true;
			}
			
			return false;
		}
		
		private function drop_table($tablename) {
			$sql = 	"DROP TABLE IF EXISTS " . $tablename;
			if ($this->db->query($sql)) 
				return true;			
			return false;	
		}

		public function disp_errors() {
			$displayText = '';
			if (count($this->error) > 0 ) {
				foreach ($this->error as $text) {
					$displayText .= $text . "<br>";
				}
				echo "<div style='color:#ff0000;font-weight:bold;'>".$displayText."</div>";
				return true;
			}
			return false;
		}
		
		private function add_error_msg($msg) {
			$this->error[] = $msg;
		}
		
	}


endif;