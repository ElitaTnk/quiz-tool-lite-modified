<?php
/*
Plugin Name: Quiz Tool Lite
Plugin URI: https://wordpress.org/plugins/quiz-tool-lite/
Description: Create questions and quizzes, embed individual questions for formative assessment or deploy entire an quiz
Version: 2.3.12
Author: Alex Furr, Lisha Chen Wilson and Simon Ward
Author URI: https://wordpress.org/plugins/quiz-tool-lite/
License: GPL
*/
date_default_timezone_set('UTC');
define('AIQUIZ_PATH', plugin_dir_path(__FILE__)); # inc /
define('AIQUIZ_DIR', plugin_dir_path(__FILE__)); # inc /
define ('AI_Plugin_Path', plugin_basename(__FILE__));
define('AIQUIZ_ABS_PATH', plugin_dir_url(__FILE__));
define( 'QTL_PLUGIN_URL', plugins_url('quiz-tool-lite' , dirname( __FILE__ )) );

require_once AIQUIZ_PATH.'functions.php'; # All the php functions etc...
require_once AIQUIZ_PATH.'scripts/qry-functions.php'; # All the DB queries
require_once AIQUIZ_PATH.'admin/index.php'; # Load admin pages
require_once AIQUIZ_PATH.'scripts/database.php'; # All the create database actions
require_once AIQUIZ_PATH.'scripts/utils.php'; # All the useful utils
require_once AIQUIZ_PATH.'scripts/ajax.php'; #Code for all the ajax calls
require_once AIQUIZ_PATH.'scripts/actions.php'; # All the actinos on the DB
require_once AIQUIZ_PATH.'scripts/export-functions.php'; # All the export function
require_once AIQUIZ_PATH.'quizFrontEnd/draw.php'; #Code that shows the quiz on the front page
require_once AIQUIZ_PATH.'scripts/draw.php'; #Drawing functions for backend


$QTL_frontendHandler = new qtl_quiz_draw(); // Initalise the menu and shortcodes
$initialiseQTL = new qtl_initialise($QTL_frontendHandler);  // Inistalise the front end shortcode drawing functions

class qtl_initialise
{
	public function __construct($Object)
	{	
		add_action( 'admin_menu', array( $this, 'QTL_createAdminMenu' ));
		add_action( 'admin_head', array( $this, 'QTL_loadMyPluginScripts' ));
		
		// Add the shortcodes
		add_shortcode('QTL-Quiz', array( $Object, 'startQuiz'));
		add_shortcode('QTL-Question', array( $Object, 'drawExampleQuestion') );
		add_shortcode('QTL-Response', array( $Object, 'drawUserResponse'));
		
		// Shortcode to show results to student
		add_shortcode('QTL-Score', array( $Object, 'drawUserScore'));
		
		// Shortcode to show results to student
		add_shortcode('QTL-Leaderboard', array( $Object, 'drawLeaderboard'));		
		
	}	
		
	// Add the Admin Menu Items
	public static function QTL_createAdminMenu() 
	{
		// Get the wordpress minimum level If it doesn't exist then create it as admin for default
		if(!get_option('qtl-minimum-editor'))
		{
			add_option('qtl-minimum-editor', 'administrator');	
		}
		$minimumAccessLevel = get_option('qtl-minimum-editor');
		
		switch ($minimumAccessLevel)
		{
			case "administrator":
			{
				$myCapability = 'manage_options';
				//echo 'User must be able to manage_options';
				break;
			}
			
			case "editor":
			{
				$myCapability = 'delete_others_pages';
				//echo 'User must be able to delete_others_pages';
				break;
			}	
			
			case "author":
			{
				$myCapability = 'delete_published_posts';
				//echo 'User must be able to delete_published_posts';
				break;
			}	
			
			case "contributor":
			{
				$myCapability = 'delete_posts';				
				//echo 'User must be able to delete_posts';
				break;
			}									
			
		}
		
		
		$myIcon = plugins_url();
		$myIcon.='/quiz-tool-lite/images/quiz_icon.png';
		
		// Create main menu item
		$page_title="Quiz Questions";
		$menu_title="Quiz Questions";
		$menu_slug="ai-quiz-home";
		$function="drawAIquiz_home";
		$iconURL=$myIcon;	
		add_menu_page( $page_title, $menu_title, $myCapability, $menu_slug, $function, $iconURL);
		
		$parentSlug = "ai-quiz-home";
		$page_title="Quizzes";
		$menu_title="Quizzes";
		$menu_slug="ai-quiz-quiz-list";
		$function="drawAIquiz_QuizList";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);
		
		$parentSlug = "ai-quiz-question-list";
		$page_title="Question List";
		$menu_title="Question List";
		$menu_slug="ai-quiz-question-list";
		$function="drawAIquiz_questionList";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		
		$parentSlug = "ai-quiz-quiz-list";
		$page_title="Edit Quiz";
		$menu_title="Edit Quiz";
		$menu_slug="ai-quiz-quiz-edit";
		$function="drawAIquiz_quizEdit";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);			
	
		$parentSlug = "ai-quiz-question-list";
		$page_title="Edit Question";
		$menu_title="Edit Question";
		$menu_slug="ai-quiz-question-edit";
		$function="drawAIquiz_questionEdit";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);
		
		$parentSlug="ai-quiz-quiz-list";
		$page_title="Results";
		$menu_title="Results";
		$menu_slug="ai-quiz-results";
		$function="drawAIquiz_results";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		$parentSlug="ai-quiz-results";
		$page_title="User Results";
		$menu_title="User Results";
		$menu_slug="ai-user-results";
		$function="drawAIquiz_user_results";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-user-results";
		$page_title="Quiz Breakdown";
		$menu_title="Quiz Breakdown";
		$menu_slug="ai-quiz_breakdown";
		$function="drawAIquiz_user_breakdown";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		
		
		$parentSlug="ai-quiz-questionType";
		$page_title="Pick Question Type";
		$menu_title="Pick Question Type";
		$menu_slug="ai-quiz-questionType";
		$function="drawAIquiz_questionType";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		
		$parentSlug="ai-quiz-home";
		$page_title="Export / Import";
		$menu_title="Export / Import";
		$menu_slug="ai-quiz-export";
		$function="drawAIquiz_export";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);		
		
		$parentSlug="ai-quiz-home";
		$page_title="Settings";
		$menu_title="Settings";
		$menu_slug="ai-quiz-settings";
		$function="drawAIquiz_settings";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		
		$parentSlug="ai-quiz-home";
		$page_title="Help";
		$menu_title="Help";
		$menu_slug="ai-quiz-help";
		$function="drawAIquiz_help";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		
		$parentSlug="ai-quiz-quiz-list";
		$page_title="Grade Boundaries";
		$menu_title="Grade Boundaries";
		$menu_slug="ai-quiz-boundaries";
		$function="drawAIquiz_gradeBoundaries";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	
		
		$parentSlug="ai-quiz-boundaries";
		$page_title="Edit Grade Boundary";
		$menu_title="Edit Grade Boundary";
		$menu_slug="ai-quiz-boundaryEdit";
		$function="drawAIquiz_boundaryEdit";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);			
		
		
		
		
	}
	
	// Check if we're on our options page   and only load plugin scripts if so
	public static function QTL_isMyPluginScreen()
	{
		global $overrideAdminCheck; //override for loading up gplugin scripts from front end i.e. doesn't need to check if on admin page
		
		if($overrideAdminCheck==true)
		{
			return true;
		}
		else
		{
			$myPluginPages = array(
			"toplevel_page_ai-quiz-home",
			"admin_page_ai-quiz-question-list",
			"quiz-questions_page_ai-quiz-quiz-list",
			"admin_page_ai-quiz-quiz-edit",
			"quiz-questions_page_ai-quiz-results",
			"admin_page_ai-quiz-question-edit",
			"admin_page_ai-quiz-questionType",
			"quiz-questions_page_ai-quiz-export",
			"quiz-questions_page_ai-quiz-settings",
			"quiz-questions_page_ai-quiz-help",
			"ai-quiz-results_uos",
			"admin_page_ai-quiz-results",
			"admin_page_ai-user-results",
			"admin_page_ai-quiz_breakdown",
			"admin_page_ai-quiz-boundaries",
			"admin_page_ai-quiz-boundaryEdit"
			
			);
			
			// Get the screen name
			global $current_screen;
			$screen = get_current_screen();
			$thisPage = $screen->id;
			
			if (in_array($thisPage, $myPluginPages))
			{
				$isMyPluginPage = true;
			}
			else
			{
				$isMyPluginPage = false;
			}
			
			
			if (is_object($screen) && $isMyPluginPage==true)
			{
				return true;  
				
			}else{  
				return false;  
			}  
		}
	} 
	
  
	public static function QTL_loadMyPluginScripts()
	{  
	
		// Include JS/CSS only if we're on our options page  
		if (qtl_initialise::QTL_isMyPluginScreen())
		{  
			wp_enqueue_script('js_custom', plugins_url('/scripts/js-functions.js',__FILE__) ); #Custom JS functions
	
			wp_register_style( 'QTL_css_custom',  plugins_url('/css/styles.css',__FILE__) );
			wp_enqueue_style( 'QTL_css_custom' );
			
			wp_register_style( 'QTL_css_icons',  plugins_url('/css/icons.css',__FILE__) );
			wp_enqueue_style( 'QTL_css_icons' );
			
			
			global $wp_scripts;	
			
			// Allow the poopup thickbox to appear all pages
			add_thickbox();
		
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-position'); 	//drag/drop dependency
			
			
			wp_enqueue_script('jquery-ui-widget'); 		//drag/drop dependency
			wp_enqueue_script('jquery-ui-mouse');  		//drag/drop dependency
			wp_enqueue_script('jquery-ui-draggable');  	//drag/drop dependency
			wp_enqueue_script('jquery-ui-droppable');  	//drag/drop dependency
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-tabs'); 
			
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('jquery-touch-punch');
			
				
			// get the jquery ui object
			$queryui = $wp_scripts->query('jquery-ui-core');
		 
			// load the jquery ui theme
			$url = "https://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";	
			wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
				
			//spectrum colour picker scripts
			wp_enqueue_style( 'spectrum_css', plugins_url('', __FILE__) . '/css/spectrum.css' );
			wp_enqueue_script( 'spectrum_js', plugins_url('', __FILE__) . '/scripts/spectrum.js' );			
			
		
		} 
	} 		
}
// Southampton Uni Specific stuff. If its eFolio then load the local files as well.
if (strpos($_SERVER['SERVER_NAME'],'efolio.soton.ac.uk') !== false)
{
	require_once($_SERVER['DOCUMENT_ROOT']."/scripts/quiz_tool_lite_UoS/functions_uos.php");
}

?>