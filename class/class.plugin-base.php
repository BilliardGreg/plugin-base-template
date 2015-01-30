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
			
			add_action('init', array(&$this, 'init'));
			
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));

		}
		
		public static function activate() {
			
		}
		
		public static function deactivate() {
			
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
			
			$this->add_error_msg("Test Error Message");
			echo "<h1>Plugin Base Settings Page</h2>";
			$this->disp_errors();
			
		}
		
		public function sample_page() {
			$this->check_user();
			//if passed then display following code to user

			echo "<h1>Plugin Base 2nd Page</h2>";
			$this->disp_errors();
			
		}
		

		
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
					  `ip` varchar(64) NOT NULL,
					  `phpsessid` varchar(64) NOT NULL,
					  `entireurl` text NOT NULL,
					  `querystring` text NOT NULL,
					  `q` text NOT NULL,
					  `referrer` text NOT NULL,
					  `landingpage` text NOT NULL,
					  `utmcmd` text NOT NULL,
					  `utmctr` text NOT NULL,
					  `dataarray` text NOT NULL,
					  `httpcookie` text NOT NULL,
					  `pagecount` int(11) NOT NULL DEFAULT '1',
					  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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