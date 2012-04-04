<?php
/*
Plugin Name: Advanced Code Editor
Plugin URI: http://en.bainternet.info
Description: Enables syntax highlighting in the integrated themes and plugins source code editors with line numbers, AutoComplete and much more. Supports PHP, HTML, CSS and JS.
Version: 1.9
Author: BaInternet
Author URI: http://en.bainternet.info
*/
/*
		* 	Copyright (C) 2011-2012  Ohad Raz
		*	http://en.bainternet.info
		*	admin@bainternet.info

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Disallow direct access to the plugin file */
if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
	die('Sorry, but you cannot access this page directly.');
}


/**
 * advanced_code_editor is the main class...
 * @author Bainternet
 *
 */
class advanced_code_editor{

	// Class Variables
	/**
	 * used as localiztion domain name
	 * @var string
	 */
	var $localization_domain = "baace";
	
	
	/**
	 * Class constarctor
	 */
	function advanced_code_editor(){
		if( is_admin()){
			//create new file admin ajax
			add_action('wp_ajax_create_file', array($this,'ajax_create_file'));
			//delete file admin ajax
			add_action('wp_ajax_delete_file', array($this,'ajax_delete_file'));
			//create new directory admin ajax
			add_action('wp_ajax_create_directory', array($this,'ajax_create_directory'));
			if( strpos( strtolower( $_SERVER[ 'REQUEST_URI' ] ), 'plugin-editor.php' ) !== false || strpos( strtolower( $_SERVER[ 'REQUEST_URI' ] ), 'theme-editor.php' ) !== false ){
				add_filter( 'admin_footer', array($this,'do_edit' ));
				add_filter('admin_enqueue_scripts',array($this,'add_scripts'));
				//Language Setup
				$locale = get_locale();
				load_plugin_textdomain( $this->localization_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
				if (strpos( strtolower( $_SERVER[ 'REQUEST_URI' ] ), 'plugin-editor.php' ) !== false)
					add_action('admin_footer',array($this,'plugin_file_tree'));
				else
					add_action('admin_footer',array($this,'theme_file_tree'));
			}
			//downloads
			
	    }
	    add_filter('init', array($this,'add_query_var_vars'));
		add_action('template_redirect', array($this,'admin_redirect_download_files'));
	}

	/**
	 * add plugins entry points to query vars
	 * @author Ohad Raz
	 * @since 1.9
	 * @access public
	 */
	public function add_query_var_vars() {
	    global $wp;
	    $wp->add_query_var('theme_download'); 			//download theme
		$wp->add_query_var('dn_file'); 		 			//download file name
		$wp->add_query_var('plugin_download'); 			//download plugin
		$wp->add_query_var('dnf'); 						//download plugin
	}

	/**
	 * admin_redirect_download_files handler
	 * @author Ohad   Raz
	 * @since 1.9
	 * @access public
	 * 
	 * @return void
	 */
	public function admin_redirect_download_files(){
		global $wp;
	    global $wp_query;
		//download theme
		 if (array_key_exists('theme_download', $wp->query_vars) && $wp->query_vars['theme_download'] == 'theme_download'){
	        $this->download_theme();
			die();
		}
		if (array_key_exists('plugin_download', $wp->query_vars) && $wp->query_vars['plugin_download'] != ''){
	        $this->download_plugin();
			die();
		}
		if (array_key_exists('dn_file', $wp->query_vars) && $wp->query_vars['dn_file'] != ''){
	        $this->download_file();
			die();
		}
		
	}

	/**
	 * zip and download plugin
	 * 
	 * @author Ohad Raz
	 * @since 1.9
	 * @access public
	 * 
	 * @return zip file
	 */
	public function download_plugin(){
		header('HTTP/1.1 200 OK');
		if ( !current_user_can('edit_plugins') )
				wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this site.').'</p>');

		$plugin = get_query_var('plugin_download');
		if(isset($plugin) && $plugin != ''){
			
			//Get the directory to zip
			$directory = WP_PLUGIN_DIR .'/'.$plugin;
			$zipname = date('Ymdhis') . '.zip';
			// create object
			$zip = $this->Zip($directory,$zipname,strtolower($plugin).'/');
			if ($zip === false){
				wp_die('<p>'.__('error ziping files.').'</p>');
			}
		
			$file = $zipname;
			if(file_exists($file)){
				$content = file_get_contents($file);
			}else{
				$content = 'File does not exist...';
			}

			$fsize = filesize($file);
			header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
    	    header("Content-Type: application/zip");
        	header("Content-Transfer-Encoding: Binary");
        	header("Content-Length: ".filesize($file));
        	header("Content-Disposition: attachment; filename=\"".$plugin . '.zip'."\"");
        	readfile($file);
			unlink($file);
			exit;
		}
	}

	/**
	 * download_file current edited file
	 * 
	 * @author ohad raz
	 * @since 1.9
	 * @access public
	 * 
	 * @return file
	 */
	public function download_file(){
		header('HTTP/1.1 200 OK');
		$from = get_query_var('dnf');
		if (!isset ($from))
			wp_die('<p>'.__('You do not have sufficient permissions to Download this file.').'</p>');
		
		if ($from == 'theme'){
			if ( !current_user_can('edit_themes') )
				wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this site.').'</p>');
		}elseif ($from == 'plugin') {
			if ( !current_user_can('edit_plugins') )
				wp_die('<p>'.__('You do not have sufficient permissions to edit Plugins for this site.').'</p>');
		}else{
			wp_die('<p>'.__('You do not have sufficient permissions to edit files.').'</p>');
		}
		$file = get_query_var('dn_file');
		if (!isset($file)){
			wp_die('<p>'.__('Error Downloading file.').'</p>');	
		}
		if ($from == 'plugin'){
			$file = WP_PLUGIN_DIR .'/'.$file;
		}
		if(file_exists($file)){
			$content = file_get_contents($file);
			$filename = explode("/","/" . $file);
			$fsize = strlen($content);

			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header('Content-Description: File Transfer');
			header("Content-Disposition: attachment; filename=" . $filename[count($filename)-1]);
			header("Content-Length: ".$fsize);
			header("Expires: 0");
			header("Pragma: public");
			echo $content;
			exit;
		}
			wp_die('<p>'.__('Error Downloading file.').'</p>');	

	}

	/**
	 * Zip file maker
	 * 
	 * @author Ohad Raz
	 * @since 1.9
	 * @access public
	 * 
	 * @param string $source           file or directory to zip
	 * @param string $destination      zip file to create
	 * @param string $container_folder if you want to put the files inside a directory in the zip then pass it here
	 */
	public function Zip($source, $destination,$container_folder = ''){
	    if (!extension_loaded('zip') || !file_exists($source)) 
	        return false;

	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE))
	        return false;

	    $source = str_replace('\\', '/', realpath($source));

	    if (is_dir($source) === true){
	        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
	        foreach ($files as $file){
	            $file = str_replace('\\', '/', realpath($file));
	            if (is_dir($file) === true){
	                $zip->addEmptyDir(str_replace($source . '/', $container_folder, $file . '/'));
	            }else if (is_file($file) === true){
	                $zip->addFromString(str_replace($source . '/', $container_folder, $file), file_get_contents($file));
	            }
	          //  echo $file .'<br />';
	        }
	        //die();
	    }else if (is_file($source) === true){
	        $zip->addFromString(basename($source), file_get_contents($source));
	    }

	    return $zip->close();
	}


	/**
	 * zip and download theme
	 * @author Ohad   Raz
	 * @since 1.9
	 * @access public
	 * 
	 * @return zip file on success and string on faliure 
	 */
	public function download_theme(){
		header('HTTP/1.1 200 OK');
		if ( !current_user_can('edit_themes') )
				wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this site.').'</p>');

		$themes = get_themes();
		$theme = '';

		if(!isset($_GET['theme'])){
			$theme = get_current_theme();
		}else{
			$theme = $_GET['theme'];
		}

		//Get the directory to zip
		$directory = $themes[$theme]['Template Dir'] . '/';
		$zipname = date('Ymdhis') . '.zip';
		// create object
		$zip = $this->Zip($directory,$zipname,strtolower($theme).'/');
		if ($zip === false){
			wp_die('<p>'.__('error ziping files.').'</p>');
		}
		
		

		$file = $zipname;
		if(file_exists($file)){
			$content = file_get_contents($file);
		}else{
			$content = 'File does not exist...';
		}

		$fsize = filesize($file);
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: ".filesize($file));
        header("Content-Disposition: attachment; filename=\"".$theme . '.zip'."\"");
        readfile($file);
		unlink($file);
		exit;
	}
	
	//ajax create directory
	/**
	 * function to handle ajax new directory creation
	 */
	function ajax_create_directory(){
		check_ajax_referer('create_directory');
		global $current_user;
		get_currentuserinfo();
		if (isset($_POST['di_name']) && isset($_POST['dir'])){
			if (current_user_can('manage_options')){
				$dir_name = '';
				$new_dir_name = strtolower( str_replace(' ', '-', $_POST['di_name']));
				if (isset($_POST['f_type'])){
					if ($_POST['f_type'] == "plugin" ){
						$dir_name = WP_PLUGIN_DIR . '/' . $_POST['dir'] . '/' . $new_dir_name;
					}
					if ($_POST['f_type'] == "theme" ){
						$dir_name = $_POST['dir'] . '/' . $new_dir_name;
					}
					
					//if(!is_dir($dir_name)){
						//echo __("Cannot create directory  Error code 9<br />".$dir_name,"baace");
					//}else{
						$umask = umask(0);
						if (@mkdir($dir_name, 0777)){
							echo __("New directory Created!!!","baace");
						}else{
							echo __("Cannot create directory Error code 8<br />".$dir_name,"baace");
						}
						umask($umask);
					//}
				}else{
					echo __('Error Code 7','baace');
				}
			}else{
				echo __('Error Code 5','baace');
			}
		}else{
			echo __('Error Code 6','baace');
		}
		die();
	}
	
	//ajax delete file
	/**
	 * function to handle ajax delete file
	 */
	function ajax_delete_file(){
		check_ajax_referer('delete_file');
		global $current_user;
		get_currentuserinfo();
		if(isset($_POST['F_T_D']) && $_POST['F_T_D'] != '' && isset($_POST['f_type'])){
			$f_name = '';
			if($_POST['f_type'] == "plugin" ){
				$f_name = WP_PLUGIN_DIR . '/' .$_POST['F_T_D'];
			}else{
				$f_name = $_POST['F_T_D'];
			}
				@unlink($f_name);
				echo __('File Deleted!!!','baace');
				die();
		}else{
			echo __('Error Code 4','baace');
			die();
		}
	}
	
	//ajax create file
	/**
	 * function to handle ajax file creation
	 */
	function ajax_create_file(){
		check_ajax_referer('create_new_file');
		global $current_user;
		get_currentuserinfo();
		if(isset($_POST)){
		$checks = false;
		$file_name = '';
			if (isset($_POST['file_name']) && $_POST['file_name'] != ''){
				if (isset($_POST['f_type']) && isset($_POST['dir'])){
					$f_name = strtolower( str_replace(' ', '-', $_POST['file_name']));
					if($_POST['f_type'] == "plugin" ){
						if (current_user_can( 'edit_plugins' )){
							$checks = true;
							$file_name = WP_PLUGIN_DIR . '/' . $_POST['dir'] . '/' . $f_name;
						}
					}elseif( $_POST['f_type'] == "theme" ){
						if (current_user_can( 'edit_themes' )){
							$checks = true;
							$file_name = $_POST['dir'] . '/' . $f_name;

						}
					}else{
						echo __('Error Code 3','baace');
						die();
					}
				}else{
					echo __('Error Code 2','baace');
					die();
				}
				if ($checks){
					
					if(file_exists( $file_name)){
						echo __("File already exists","baace");
						die();
					}else{
						$handle = fopen($file_name, 'w') or wp_die('Cannot open file for editing');
						
						$file_contents = '';
						fwrite($handle, $file_contents);
						fclose($handle);
						echo __('New File Created!','baace');
						die();
					}
				}
			}else{
				echo __('you must set a file name','baace');
			}
		}else{
			echo __('Error Code 1','baace');
			die();
		}
		die();
	}
	
	/**
	 * function to include jQuery form plugin for ajax save ...
	 */
	function add_scripts(){
		$url = plugins_url()."/advanced-code-editor/";
		wp_enqueue_script( 'jquery' );
	    wp_enqueue_script( 'jquery-form' );
	}

	function do_edit(){
		$url = plugins_url()."/advanced-code-editor/"; 
		/**/
		?>
		<script type="text/javascript" src="<?php echo $url; ?>js/codemirror.js"></script>
		<link rel="stylesheet" href="<?php echo $url; ?>css/codemirror.css">
		<script type="text/javascript" src="<?php echo $url; ?>js/xml.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>js/javascript.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>js/css.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>js/clike.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>js/php.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>js/complete.js"></script>
		<link rel="stylesheet" href="<?php echo $url; ?>themes/default.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/night.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/elegant.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/neat.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/raverStudio.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/cobalt.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/eclipse.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/monokai.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/rubyblue.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/solarizedDark.css">
		<link rel="stylesheet" href="<?php echo $url; ?>themes/solarizedLight.css">		
		<?php
		   /*jq todo use enquire_script*/
		   ?>
		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
		<?php
		   /*style todo move to external file*/
		   ?>
		<style>
		.ace_tool_bar{list-style: none;}
		.ace_tool_bar li{cursor: pointer;}
		.completions {position: absolute;z-index: 10;overflow: hidden;-webkit-box-shadow: 2px 3px 5px rgba(0,0,0,.2);-moz-box-shadow: 2px 3px 5px rgba(0,0,0,.2);box-shadow: 2px 3px 5px rgba(0,0,0,.2);}
		.completions select {background: #fafafa;outline: none;border: none;padding: 0;margin: 0;font-family: monospace;}
		   <?php if (!is_rtl()){?>
		.CodeMirror-scroll {height: 600px;overflow: auto; margin-right: 0 !important;}
		.CodeMirror-gutter{ width: 50px !important;}
		.fullscreen{background-color: #FFFFFF;height: 89%;left: 0;position: fixed;top: 80px;width: 100%;z-index: 100;}
		.ace_ToolBar{background-color: #FFFFFF;left: 0;min-height: 85px;position: fixed;top: 0;width: 100%;z-index: 100;}
		
		.CodeMirror {border: 1px solid #eee;} 
		/*toolbar*/
		#template div {margin-right: 105px;}
		.ace_tool_bar li{float: left; }
	    .clean_ace{clear:left;}

		<?php }else{ ?>
		 
		.CodeMirror {border: 1px solid #eee; margin-left: 190px !important;} 
		.CodeMirror-scroll {height: 600px;overflow: auto; margin-left: 0 !important;}
		.CodeMirror-gutter{ width: 50px !important;}
		 #template div {margin-left: 0px;}
		.fullscreen{background-color: #FFFFFF;height: 89%;right: 0;position: fixed;top: 80px;width: 100%;z-index: 100;}
		.ace_ToolBar{background-color: #FFFFFF;right: 0;min-height: 85px;position: fixed;top: 0;width: 100%;z-index: 100;}
		.ace_tool_bar li{float: right; }
		.clean_ace{clear:right;}
		.CodeMirror-lines{direction: ltr;}
		.completions{direction: ltr;}
		  <?php } ?>
		</style>
		<?php /*scripts todo move to external file*/ ?>
		<script>
			var lastPos = null, lastQuery = null, marked = [];
			jQuery(document).ready(function($) {
				//ajax save
			   // attach handler to form's submit event 
				$('#template').submit(function(){
					// submit the form 
					// prepare Options Object 
					  var options = { 
						  beforeSubmit:  BeforeSave,
						  success:    showResponse 
					  };
					  $(this).ajaxSubmit(options); 
					  // return false to prevent normal browser submit and page navigation 
					  return false; 
				});

				//add toolbar
				   jQuery("#newcontent").after("<div class=\"ace\"><h3><?php _e('Advanced Code Editor','baace');?></h3><div class=\"s_r\"></div></div><div class=\"clean_ace\"></div>");
				   jQuery('.s_r').append('<ul class=\"ace_tool_bar\"><li><a class=\"tb_se\" id=\"ace_tool_s\" title=\"<?php _e('Search','baace');?>\"><img src=\"<?php echo $url; ?>images/z4Ulb.png\" alt=\"Search\"></a></li></ul>');
				   var toolbar = jQuery('.ace_tool_bar');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_sr\" title=\"<?php _e('Replace','baace');?>\"><img src=\"<?php echo $url; ?>images/1smMk.png\" alt=\"Replace\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_jmp\" title=\"<?php _e('Jump To Line','baace');?>\"><img src=\"<?php echo $url; ?>images/rmic5.png\" alt=\"Jump To Line\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_full\" title=\"<?php _e('Full Screen Editor','baace');?>\"><img src=\"<?php echo $url; ?>images/6NDPx.png\" alt=\"Full Screen Editor\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_save\" title=\"<?php _e('Save Changes','baace');?>\"><img src=\"<?php echo $url; ?>images/suvnt.png\" alt=\"Save Changes\"></a><li>');
				   toolbar.append('<li><?php _e('Change editor theme:','baace');?><select id=\"editortheme\" onchange=\"selectTheme(this.value)\"></select></li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_new_file\" title=\"<?php _e('Create New File','baace');?>\"><img src=\"<?php echo $url; ?>images/ZjkC3.png" alt=\"Create New File\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_delete\" title=\"<?php _e('Delete Current File','baace');?>\"><img src=\"<?php echo $url; ?>images/3b5nW.png" alt=\"Delete Current File\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_new_d\" title=\"<?php _e('Create New Directory','baace');?>\"><img src=\"<?php echo $url; ?>images/iAW16.png" alt=\"Create New Directory\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_help\" title=\"<?php _e('Help','baace');?>\"><img src=\"<?php echo $url; ?>images/Y1xXZ.png\" alt=\"Help\"></a><li>');
				   toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_about\" title=\"<?php _e('About','baace');?>\"><img src=\"<?php echo $url; ?>images/Wwa3Z.png\" alt=\"About\"></a><li>');
				   toolbar.append('<iframe id="dframe" width="0" height="0" src=""></iframe>');

				 //set theme changer
					var theme_coo = readCookie('adce_theme');
					var tedi = jQuery('#editortheme');
					if (theme_coo) {
					   var theme_names = ["default", "night", "neat", "elegant", "raverStudio", "cobalt", "eclipse", "monokai", "rubyblue","solarizedLight", "solarizedDark"];
					   for(var i in theme_names){
						  if (theme_names[i] == theme_coo){
						 tedi.append('<option selected=\"selected\">'+theme_names[i]+'</option>');
						  }else{
						 tedi.append('<option>'+theme_names[i]+'</option>');	       
						  }
					   }
					}else{
					   tedi.append('<option selected=\"selected\">default</option>');
					   tedi.append('<option>night</option>');
					   tedi.append('<option>neat</option>');
					   tedi.append('<option>elegant</option>');
					   tedi.append('<option>raverStudio</option>');
					   tedi.append('<option>cobalt</option>');
					   tedi.append('<option>eclipse</option>');
					   tedi.append('<option>monokai</option>');
					   tedi.append('<option>rubyblue</option>');
					   tedi.append('<option>solarizedDark</option>');
					   tedi.append('<option>solarizedLight</option>');
					}

					//tool Bar
					//jump to lines
					jQuery('#ace_tool_jmp').bind('click', function() {
						jQuery("#jump_tbox").dialog({ focus: function(event, ui){jQuery('#jump_line_number').focus(); }, hide: 'slide',title: '<?php _e('Jump to Line','baace');?>', buttons: [
							{
								text: "<?php _e("Jump","baace");?>",
								click: function() { jQuery(this).dialog("close"); Jump_to_Line(); },
							}] 
							});
					});
				
					// search toolbar
					jQuery('#ace_tool_s').bind('click', function() {
					   jQuery("#search").dialog({ focus: function(event, ui){jQuery('#query').focus(); }, hide: 'slide',title: '<?php _e('Search Box','baace');?>' });
					   // document.getElementById("query").focus();
					   
					});
					jQuery( "#search" ).bind( "dialogopen", function(event, ui) {
					     jQuery('#query').focus(); 
					 });
					 
					 jQuery('#query').live('keydown',function(e) {
					    
					    if(e.keyCode == 13) {
					       e.preventDefault();
						jQuery("#ace_se").click();
					    }
					  });
					//new directory
					jQuery('#ace_tool_new_d').bind('click', function() {
						jQuery("#add_new_file").html('<form action="" method="POST" id="new_d_create"><p>Directory Name: <input type="text" id="di_name" name="di_name" value=""><br /></p></form>');
						jQuery("#add_new_file").dialog({ show: 'slide',hide: 'slide',title: '<?php _e('Create Directory','baace');?>', buttons: [
							{
								text: "<?php _e("Cancel","baace");?>",
								click: function() { jQuery(this).dialog("close"); },
							},
							{
								text: "<?php _e("Create","baace");?>",
								click: function() { ajax_create_directory(jQuery('#di_name').val()); }
							}
							] });
					});
					//new file toolbar
					jQuery('#ace_tool_new_file').bind('click', function() {
						jQuery("#add_new_file").html('<form action="" method="POST" id="new_F_create"><p> File Name: <input type="text" id="fi_name" name="fi_name" value=""></p></form>');
						jQuery("#add_new_file").dialog({ show: 'slide',hide: 'slide',title: '<?php _e('Create A new File','baace');?>' , buttons: [
							{
								text: "<?php _e("Cancel","baace");?>",
								click: function() { jQuery(this).dialog("close"); },
							},
							{
								text: "<?php _e("Create","baace");?>",
								click: function() { create_new_file_callback(); }
							}
							] });
					});
					//delete file toolbar
					jQuery('#ace_tool_delete').bind('click', function() {
						var f_type1 = '';
						if (jQuery('input[name="plugin"]').length){
							file_to_delete = jQuery('input[name="plugin"]').val();
							f_type1 = 'plugin';
						}else{
						//theme file
							file_to_delete = jQuery('input[name="file"]').val();
							f_type1 = 'theme';
						}
						jQuery("#add_new_file").html('<p>are you sure you want to delete this file: ' + file_to_delete);
						jQuery("#add_new_file").dialog({ show: 'slide',hide: 'slide',title: '<?php _e('Delete File','baace');?>', buttons: [
							{
								text: "<?php _e("No","baace");?>",
								click: function() { jQuery(this).dialog("close"); },
							},
							{
								text: "<?php _e("YES I am Sure","baace");?>",
								click: function() { ajax_delete_file(file_to_delete,f_type1); }
							}
							] }); 
					});
					//replace toolbar
					jQuery('#ace_tool_sr').bind('click', function() {
					   jQuery("#searchR").dialog({ show: 'slide',hide: 'slide',title: '<?php _e('Search And Replace Box','baace');?>' });
					});
					//fullscreen toolbar button
					jQuery('#ace_tool_full').bind('click', function() {
					   toggleFullscreenEditing();
					});
					//save toolbar button
					jQuery('#ace_tool_save').live('click', function() {
					   jQuery('#submit').click();
					});

					//help toolbar
					jQuery('#ace_tool_help').bind('click', function() {
					   jQuery("#ace_help").dialog({show: 'slide',hide: 'slide', title: '<?php _e('Help','baace');?>' });
					});

					//about toolbar
					jQuery('#ace_tool_about').bind('click', function() {
					   jQuery("#ace_about").dialog({show: 'slide',hide: 'slide', title: '<?php _e('About WordPress Advanced Code Editor','baace');?>',width: 380 });
					});				
			});
	
			//action buttons
			//delete file
			function ajax_delete_file(file_to_delete,f_type1){
				jQuery('#add_new_file').html('<p style="text-align:center;">Deleting File ...<br/><img src="<?php echo $url; ?>images/GRZ9W.gif"></p>');
				var data = {
					action: 'delete_file',
					f_type: f_type1,
					F_T_D: file_to_delete,
					_ajax_nonce: '<?php echo wp_create_nonce( 'delete_file' ); ?>'
				};
				jQuery.post(ajaxurl, data, function(response) {
					//alert('Got this from the server: ' + response);
					jQuery(".ui-dialog-content").dialog("close");
					jQuery('#add_new_file').dialog( "destroy" );
					jQuery('#update_Box').html('<div>' + response + '</div>');
					jQuery("#update_Box").dialog({ show: 'slide',hide: 'slide', title: '<?php _e('Create A new File','baace');?>', buttons: [
						{
							text: "Ok",
							click: function() { jQuery(this).dialog("close"); }
						}
					] }); 
				});
			}
			//create new directory
			function ajax_create_directory(di_name){
				jQuery('#add_new_file').html('<p style="text-align:center;">Creating New Directory ...<br/><img src="<?php echo $url; ?>images/GRZ9W.gif"></p>');
				var plugin_meta = new Array();
				var f_type2 = '';
				//plugin file
				if (jQuery('input[name="plugin"]').length){
					plugin_meta = jQuery('input[name="plugin"]').val().split('/');
					var plugin_dir = plugin_meta[0];
					var dirs = plugin_meta.length - 1;
					for(i=1; i < dirs; i++) { 
						plugin_dir = plugin_dir + '/' + plugin_meta[i];
					}
					f_type2 = 'plugin';
				}else{
					//theme file
					plugin_meta = jQuery('input[name="file"]').val().split('/');
					var plugin_dir = plugin_meta[0];
					var dirs = plugin_meta.length - 1;
					for(i=1; i < dirs; i++) { 
						plugin_dir = plugin_dir + '/' + plugin_meta[i];
					}
					f_type2 = 'theme';
				}

				var data = {
					action: 'create_directory',
					dir: plugin_dir,
					f_type: f_type2,
					di_name: di_name,
					_ajax_nonce: '<?php echo wp_create_nonce( 'create_directory' ); ?>'
				};
				jQuery.post(ajaxurl, data, function(response) {
					//alert('Got this from the server: ' + response);
					jQuery(".ui-dialog-content").dialog("close");
					jQuery('#add_new_file').dialog( "destroy" );
					jQuery('#update_Box').html('<div>' + response + '</div>');
					jQuery("#update_Box").dialog({ show: 'slide',hide: 'slide', title: '<?php _e('Create A new Directory','baace');?>', buttons: [
						{
							text: "Ok",
							click: function() { jQuery(this).dialog("close"); }
						}
					] }); 
				});
			}
			
			//create new file
		function create_new_file_callback(){
			var file_name = jQuery("#fi_name").val();
			jQuery('#add_new_file').html('<p style="text-align:center;">Creating New File ...<br/><img src="<?php echo $url; ?>images/GRZ9W.gif"></p>');
			var plugin_meta = new Array();
			//plugin file
			var f_type = '';
			if (jQuery('input[name="plugin"]').length){
				plugin_meta = jQuery('input[name="plugin"]').val().split('/');
				var plugin_dir = plugin_meta[0];
				var dirs = plugin_meta.length - 1;
				for(i=1; i < dirs; i++) { 
					plugin_dir = plugin_dir + '/' + plugin_meta[i];
				}
				f_type = 'plugin';
			}else{
			//theme file
				plugin_meta = jQuery('input[name="file"]').val().split('/');
				var plugin_dir = plugin_meta[0];
				var dirs = plugin_meta.length - 1;
				for(i=1; i < dirs; i++) { 
					plugin_dir = plugin_dir + '/' + plugin_meta[i];
				}
				f_type = 'theme';
			}

			var data = {
				action: 'create_file',
				dir: plugin_dir,
				f_type: f_type,
				file_name: file_name,
				_ajax_nonce: '<?php echo wp_create_nonce( 'create_new_file' ); ?>'
			};
			jQuery.post(ajaxurl, data, function(response) {
				//alert('Got this from the server: ' + response);
				jQuery(".ui-dialog-content").dialog("close");
				jQuery('#add_new_file').dialog( "destroy" );
				jQuery('#update_Box').html('<div>' + response + '</div>');
				jQuery("#update_Box").dialog({ show: 'slide',hide: 'slide', title: '<?php _e('Create A new File','baace');?>', buttons: [
					{
						text: "Ok",
						click: function() { jQuery(this).dialog("close"); }
					}
				] }); 
			});
		}
		//replace
		jQuery('#ace_re').live('click', function(event) {
		   event.preventDefault();
		   replace();
		});
		//search
		jQuery('#ace_se').live('click', function(event) {
		   event.preventDefault();
		  search();
		});
		//replace all
		jQuery('#ace_res').live('click', function(event) {
		   event.preventDefault();
		   replaceall();
		});
		//jump to line
		jQuery('#ace_jamp').live('click', function(event) {
		   event.preventDefault();
		   Jump_to_Line();
		});
		
		jQuery('#jump_line_number').live('keydown',function(e) {
		    if(e.keyCode == 13) {
		       e.preventDefault();
		       jQuery("#jump_tbox").dialog("close");
 		       Jump_to_Line();
		    }
		});
		
		//functions
		//jump to line
		function Jump_to_Line(){
			var line = document.getElementById("jump_line_number").value -1;
			if (line && !isNaN(Number(line))) {
				editor.setCursor(Number(line),0);
				editor.setSelection({line:Number(line),ch:0},{line:Number(line)+1,ch:0});
				editor.focus();
			}
		}
		//search unmark
		function unmark() {
			for (var i = 0; i < marked.length; ++i) marked[i]();
				marked.length = 0;
		}
	   //change theme
		function selectTheme(theme) {
			var editorDiv = jQuery('.CodeMirror-scroll');
			if (editorDiv.hasClass('fullscreen')) {
				toggleFullscreenEditing();
				editor.setOption("theme", theme);
				createCookie('adce_theme',theme,365);
				toggleFullscreenEditing();
			}else{
				editor.setOption("theme", theme);
				createCookie('adce_theme',theme,365);
			}
		}
		//search
		function search() {
			unmark();
			var text = document.getElementById("query").value;
			if (!text) return;
			for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
			marked.push(editor.markText(cursor.from(), cursor.to(), "searched"));
			if (lastQuery != text) lastPos = null;
			var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
			if (!cursor.findNext()) {
				 cursor = editor.getSearchCursor(text);
			   if (!cursor.findNext()) return;
			}
			editor.setSelection(cursor.from(), cursor.to());
			lastQuery = text; lastPos = cursor.to();
		}

		//replace
		function replace() {
			unmark();
			var text = document.getElementById("query1").value,
			replace = document.getElementById("replace").value;
			if (!text) return;
			var cursor = editor.getSearchCursor(text);
			cursor.findNext();
			if (!cursor) return;
			editor.replaceRange(replace, cursor.from(), cursor.to());
			
		}
		//replaceall
		function replaceall() {
			unmark();
			var text = document.getElementById("query1").value,
			replace = document.getElementById("replace").value;
			if (!text) return;
			for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
			   editor.replaceRange(replace, cursor.from(), cursor.to());
		}

		//before save
		function BeforeSave() {
		      jQuery("#SaveBox").html('<p style="text-align:center;">saving changes ...<br/><img src="<?php echo $url; ?>images/GRZ9W.gif"></p>');
		      jQuery("#SaveBox").dialog({ show: 'slide',hide: 'slide',title: '<?php _e('Save Box','baace');?>' }); 
		      return true; 
		}
		 
		//save response
		function showResponse(responseText)  { 
			var htmlCode = jQuery('#message',jQuery(responseText)).html();
			jQuery(".ui-dialog-content").dialog("close");
			jQuery('#saveBox').dialog( "destroy" );
			jQuery('#update_Box').html('<div>' + htmlCode + '</div><div><small>this Box will auto close in <span class="closein">5</span> seconds</small></div>');
			jQuery("#update_Box").dialog({ show: 'slide',hide: 'slide', title: '<?php _e('Save Box','baace');?>', buttons: [
				{
					text: "Ok",
					click: function() { jQuery(this).dialog("close"); }
				}]
			}); 
			setTimeout("autoclose_dialog(5)",1000);
		}
		
		//autoclose save dialog
		function autoclose_dialog(t){
			if (t == 1){
				jQuery('#update_Box').dialog('close');
				editor.focus();
			}else{
				jQuery(".closein").html(t-1);
				setTimeout("autoclose_dialog("+(t-1)+")",1000);
			}
		}

		//fullscreen edit
		function toggleFullscreenEditing(){
			var editorDiv = jQuery('.CodeMirror-scroll');
			var toolbarDiv = jQuery('.ace');
			if (!editorDiv.hasClass('fullscreen')) {
				var bgcolor = editorDiv.css("background-color");
				toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width(),bg: editorDiv.css("background-color") }
				editorDiv.addClass('fullscreen');
				jQuery(".fullscreen").css('background-color',bgcolor);
				editorDiv.height('89%');
				editorDiv.width('100%');
				toolbarDiv.addClass('ace_ToolBar');
				editor.refresh();
			}else {
				editorDiv.removeClass('fullscreen');
				toolbarDiv.removeClass('ace_ToolBar');
				editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
				editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
				editorDiv.css('background-color','');
				editor.refresh();
			}
		}
		
		//refresh editor
		 editor.refresh();
		</script>
		<div id="add_new_file" style="display:none;"></div>
		<div id="SaveBox" style="display:none;"></div>
		<div id="update_Box" style="display:none;"></div>
		<div id="search" style="display:none;"><?php _e('Search For: ','baace');?><input type="text" value="" id="query" style="width: 5em"><button class="button"  id="ace_se" type="button"><?php _e('Search','baace');?></button> </div> 
		<div id="jump_tbox" style="display:none;"><?php _e('Jump to Line: ','baace');?><input type="text" value="" id="jump_line_number" style="width: 5em"></div> 
		<div id="searchR" style="display:none;"><?php _e('Search For: ','baace');?><input type="text" value="" id="query1" style="width: 5em"><br/><?php _e('And Replace with:','baace');?><input type="text" id="replace" value="" style="width: 5em"><br /><button class="button"  id="ace_re" type="button"><?php _e('Replace','baace');?></button><?php _e('OR','baace');?> <button class="button"  id="ace_res" type="button"><?php _e('Replace all','baace');?></button> </div> 
		<div id="ace_help" style="display:none;"><h4><?php _e('Hot Keys:','baace');?></h4>
		   <ul>
			  <li><strong>CRTL + Space</strong> -  <?php _e('Triggers AutoComplete.','baace');?></li>
			  <li><strong>CRTL + Z</strong> -  <?php _e('Undo (remembers all changes, so you can use more then one)','baace');?></li>
			  <li><strong>CRTL + Y</strong> -  <?php _e('Redo (remembers all changes, so you can use more then one)','baace');?></li>
			  <li><strong>CRTL + F</strong> -  <?php _e('Search','baace');?></li>
			  <li><strong>CRTL + H</strong> -  <?php _e('Search and Replace','baace');?></li>
			  <li><strong>CRTL + G</strong> -  <?php _e('Jump to Line','baace');?></li>
			  <li><strong>CRTL + S</strong> -  <?php _e('Save Changes (When cruser is inside editor)','baace');?></li>			  
  			  <li><strong>F11</strong> -  <?php _e('FullScreen Editor (When cruser is inside editor)','baace');?></li>	
		   </ul>
		   <h4></h4>
		</div>
		<div id="ace_about" style="display:none;text-align:center;">
		   <h4><?php _e('WordPress Advanced Code Editor','baace');?></h4>
		   <ul style="list-style: square inside none; width: 300px; font-weight: bolder; padding: 20px; border: 2px solid; background-color: #FFFFE0; border-color: #E6DB55;">
			<li> Any feedback or suggestions are welcome at <a href="http://en.bainternet.info/">plugin homepage</a></li>
			<li> <a href="http://wordpress.org/tags/advanced-code-editor/?forum_id=10">Support forum</a> for help and bug submittion</li>
			<li> Also check out <a href="http://en.bainternet.info/category/plugins">my other plugins</a></li>
			<li> And if you like my work <span style="font-weight:bolder;color: #FF0000;">make a donation</span><br/>
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PPCPQV8KA3UQA"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"></a>
			 or at least <a href="http://wordpress.org/extend/plugins/bainternet-user-ranks/">rank the plugin</a></li>
		   </ul>
		   <p><?php _e('WordPress Advanced Code Editor was uses:','baace');?> </p>
			  <ul>
			 <li><a href="http://codemirror.net" traget="_blank">CodeMirror2</a> by Marijn Haverbeke.</li>
			 <li>icons By:
				<ul>
				   <li><a href="http://www.icons-land.com" traget="_blank">Icons Land</a></li>
				   <li><a href="http://www.oxygen-icons.org/" traget="_blank">Oxygen Team</a></li>
					   <li><a href="http://www.oxygen-icons.org/" traget="_blank">Oliver Scholtz</a></li>
					   <li>Marco Martin</li>
					   <li><a href="http://sa-ki.deviantart.com/" traget="_blank">Alexandre Moore</a></li>
				</ul>
			  </li>
			  </ul>
		</div>
		<?php
	}
	
	public function plugin_file_tree(){
		$url = plugins_url()."/advanced-code-editor/";
		?>
		<script>
		jQuery(document).ready(function(){

			//add downloads
			var toolbar = jQuery('.ace_tool_bar');
			//current file
			toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_download_file\" title=\"<?php _e('Download file','baace');?>\"><img src=\"<?php echo $url; ?>images/download_file.png\" alt=\"Download File\"></a><li>');
			get_file_name = jQuery('input[name="plugin"]').val();
			download_from = 'plugin';
			jQuery("#ace_tool_download_file").live('click',function(){
				jQuery("#dframe").attr("src",'<?php bloginfo('url'); ?>?dn_file=' + get_file_name + '&dnf=plugin');
			});			
			//zip plugin
			plugin_to_d = get_file_name.split("/")[0]; 
			toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_download_zip\" title=\"<?php _e('Download Plugin','baace');?>\"><img src=\"<?php echo $url; ?>images/download_zip.png\" alt=\"Download Plugin\"></a><li>');
			jQuery("#ace_tool_download_zip").live('click',function(){
				jQuery("#dframe").attr("src",'<?php bloginfo('url'); ?>?plugin_download=' + plugin_to_d);
			});

			var files_count = 0;
			var files = Array;
			jQuery("#templateside").find("ul").children().each(function() {
				files[files_count] = {name: jQuery(this).find('a').text(), link: jQuery(this).find('a').attr('href')};
				files_count++;
			});
			var main = files[0].name;
			main = main.split('/');
			var tree = jQuery('<ul>');
			tree.attr('id', main[0]);
			tree.addClass('class', 'root');
			var cur = jQuery(".highlight").find("a");
			var c_l = jQuery(cur).attr("href");
			for (var i = 0; i < files_count; i++){
				tempName = files[i].name;
				templink = files[i].link;
				if (templink == c_l)
					templink = templink + '" class="cur';
				st = tempName.split("/");
				switch (st.length){
					case 2:
						tree.append('<li><a href="' +templink + '">' + st[1] + '</a></li>');
						break;
					case 3:
						if (tree.find('#'+ st[1]).length > 0){
							tree.find('#'+ st[1]).append('<li><a href="' +templink + '">' + st[2] + '</a></li>');
						}else{
							tree.append('<li class="folder">'+ st[1] +'<ul id="'+ st[1] +'"><li><a href="' +templink + '">' + st[2] + '</a></li></ul></li>');
						}
						break;
					case 4:
						if (tree.find('#'+ st[1]).length > 0){
							if (tree.find('#'+ st[1]).find('#'+st[2]).length > 0){
								tree.find('#'+ st[1]).find('#'+st[2]).append('<li><a href="' +templink + '">' + st[3] + '</a></li>');
							}else{
								tree.find('#' + st[1]).append('<li class="folder">'+ st[2] +'<ul id="'+ st[2] +'"><li><a href="' +templink + '">' + st[3] + '</a></li></ul></li>');
							}
							
						}else{
							tree.append('<li class="folder">'+ st[1] +'<ul id="'+ st[1] +'"><li  class="folder">'+ st[2] +'<ul id="'+st[2]+'"><li><a href="' +templink + '">' + st[3] + '</a></li></ul></li></ul></li>');
						}
						break;
				}
			}
			var root = jQuery('<li>');
			jQuery(root).html(main[0]);
			jQuery(root).addClass('root');
			jQuery(root).append(tree);
			jQuery("#templateside").find("ul").html(root);
			jQuery(".folder").each(function(){
				jQuery(this).find('ul').hide();
			});
			//close - open folders
			jQuery(".folder").live("click",function(){
				var child = jQuery(this).children();
				if (jQuery(child).css('display') == 'none'){
					jQuery(child).show();
				}else{
					jQuery(child).hide();
				}
			});
			//folders
			var fol = jQuery('.folder');
			fol.css('padding-left','26px');
			fol.css('background-image','url(<?php echo $url; ?>images/wPPkk.png)');
			fol.css('background-repeat','no-repeat');
			fol.css('cursor','pointer');
			var jroot = jQuery(".root");
			jroot.css('padding-left','26px');
			jroot.css('background-image','url(<?php echo $url; ?>images/wPPkk.png)');
			jroot.css('background-repeat','no-repeat');
			jroot.find('li').css("min-height","26px");
			jroot.find('li').css('padding-left','26px');
			jroot.find('li').css('background-repeat','no-repeat');
			function icon(ext) {
				return {
					'php': 'images/O57GR.png',
					'css':'images/NbJXD.png',
					'txt':'images/tBtiP.png',
					'js':'images/MjEOb.png',
					'htm':'images/GZVfa.png',
					'html':'images/GZVfa.png'
				}[ext];
			}
			var re = new RegExp('file=[^\.]*.([^&]+).*');
			jroot.find('a').each(function(){				
				jQuery(this).parent().css('background-image','url(<?php echo $url; ?>' + icon(re.exec(jQuery(this).attr('href'))[1]) + ')');
			});
			jQuery(".cur").parent().addClass("highlight");
		});
		</script>
		<?php
	}
	
	//theme tree
	public function theme_file_tree(){
		$url = plugins_url()."/advanced-code-editor/";
		?>
		<script>
		jQuery(document).ready(function(){
			//add downloads
			var toolbar = jQuery('.ace_tool_bar');
			//current file
			//
			toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_download_file\" title=\"<?php _e('Download file','baace');?>\"><img src=\"<?php echo $url; ?>images/download_file.png\" alt=\"Download File\"></a><li>');
			get_file_name = jQuery('input[name="file"]').val();
			download_from = 'theme';
			jQuery("#ace_tool_download_file").live('click',function(){
				jQuery("#dframe").attr("src",'<?php bloginfo('url'); ?>?dn_file=' + get_file_name + '&dnf=theme');
			});			
			//zip theme
			toolbar.append('<li><a class=\"tb_re\"  id=\"ace_tool_download_zip\" title=\"<?php _e('Download theme','baace');?>\"><img src=\"<?php echo $url; ?>images/download_zip.png\" alt=\"Download theme\"></a><li>');
			jQuery("#ace_tool_download_zip").live('click',function(){
				jQuery("#dframe").attr("src",'<?php bloginfo('url'); ?>?theme_download=theme_download');
			});
			
			var files_count = 0;
			var files = Array;
			jQuery("#templateside").find("ul").children().each(function() {
				files[files_count] = {name: jQuery(this).find('a').text(), link: jQuery(this).find('a').attr('href')};
				files_count++;
			});
			var main = jQuery(".fileedit-sub").find("h3").text();
			main = main.split(':');
			var tree = jQuery('<ul>');
			tree.attr('id', main[0]);
			tree.addClass('class', 'root');
			var cur = '';
			cur = jQuery(".highlight").parent().attr("href");
			for (var i = 0; i < files_count; i++){
				tempName = files[i].name;
				templink = files[i].link;

				if (templink == cur){
					templink = templink + '" class="cur';
					
				}
				st = tempName.split("/");
				switch (st.length){
					case 1:
						tempName = tempName.split("(");
						var tmp = tempName[1].split(")");
						tempName[1] = tmp[0];
						tree.append('<li><a href="' +templink + '" title="'+tempName[0]+'">' + tempName[1] + '</a></li>');
						break;
					case 2:
						if (st[1] != "undefined"){
							tree.append('<li><a href="' +templink + '">' + st[1] + '</a></li>');
						}
						break;
					case 3:
						if (tree.find('#'+ st[1]).length > 0){
							tree.find('#'+ st[1]).append('<li><a href="' +templink + '">' + st[2] + '</a></li>');
						}else{
							tree.append('<li class="folder">'+ st[1] +'<ul id="'+ st[1] +'"><li><a href="' +templink + '">' + st[2] + '</a></li></ul></li>');
						}
						break;
					case 4:
						if (tree.find('#'+ st[1]).length > 0){
							if (tree.find('#'+ st[1]).find('#'+st[2]).length > 0){
								tree.find('#'+ st[1]).find('#'+st[2]).append('<li><a href="' +templink + '">' + st[3] + '</a></li>');
							}else{
								tree.find('#' + st[1]).append('<li class="folder">'+ st[2] +'<ul id="'+ st[2] +'"><li><a href="' +templink + '">' + st[3] + '</a></li></ul></li>');
							}
							
						}else{
							tree.append('<li class="folder">'+ st[1] +'<ul id="'+ st[1] +'"><li  class="folder">'+ st[2] +'<ul id="'+st[2]+'"><li><a href="' +templink + '">' + st[3] + '</a></li></ul></li></ul></li>');
						}
						break;
				}
			}
			var root = jQuery('<div>');
			root.html(main[0]);
			root.addClass('root');
			root.append(tree);
			jQuery("#templateside").html('<h3><?php _e("Templates"); ?></h3>');
			jQuery("#templateside").append(root);
			jQuery(".folder").each(function(){
				jQuery(this).find('ul').hide();
			});
			//close - open folders
			jQuery(".folder").live("click",function(){
				var child = jQuery(this).children();
				if (jQuery(child).css('display') == 'none'){
					jQuery(child).show();
				}else{
					jQuery(child).hide();
				}
			});
			jQuery(".cur").parent().addClass("highlight");
			//folders
			var fol = jQuery('.folder');
			fol.css('padding-left','26px');
			fol.css('background-image','url(<?php echo $url; ?>images/wPPkk.png)');
			fol.css('background-repeat','no-repeat');
			fol.css('cursor','pointer');
			var jroot = jQuery(".root");
			jroot.css('padding-left','26px');
			jroot.css('background-image','url(<?php echo $url; ?>images/wPPkk.png)');
			jroot.css('background-repeat','no-repeat');
			jroot.find('li').css("min-height","26px");
			jroot.find('li').css('padding-left','26px');
			jroot.find('li').css('background-repeat','no-repeat');
			function icon(ext) {
				return {
					'php': 'images/O57GR.png',
					'css':'images/NbJXD.png',
					'txt':'images/tBtiP.png',
					'js':'images/MjEOb.png',
					'htm':'images/GZVfa.png',
					'html':'images/GZVfa.png'
				}[ext];
			}
			var re = new RegExp('file=[^\.]*.([^&]+).*');
			jroot.find('a').each(function(){				
				jQuery(this).parent().css('background-image','url(<?php echo $url; ?>' + icon(re.exec(jQuery(this).attr('href'))[1]) + ')');
			});
		});
		</script>
		<?php
	}
	
}//END Class

$ace = new advanced_code_editor();