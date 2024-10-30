<?php
/*
Plugin Name: #blogg100 Badge
Plugin URI: http://erikfalk.com
Description: Lägger till en badge med ordningsnummer för alla dina inlägg taggade med #blogg100.
Version: 0.1.1
Author: Erik Falk
Author URI: http://erikfalk.com
License: GPL2
*/

/*  Copyright 2013  Erik Falk  (email : mr@erikfalk.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?><?php

// some definition we will use
define( 'B100EFB_PLUGIN_NAME', '#blogg100 Badge');
define( 'B100EFB_PLUGIN_DIRECTORY', 'blogg100-badge');
define( 'B100EFB_CURRENT_VERSION', '0.1.0' );

// create custom plugin settings menu
add_action( 'admin_menu', 'b100efb_create_menu' );

//call register settings function
add_action( 'admin_init', 'b100efb_register_settings' );


register_activation_hook(__FILE__, 'b100efb_activate');
register_deactivation_hook(__FILE__, 'b100efb_deactivate');
register_uninstall_hook(__FILE__, 'b100efb_uninstall');

// activating the default values
function b100efb_activate() {
	$style = '
.b100efb {
   background:#21759b;
   color:#fff;
   border-radius:35px;
   padding:10px;
   width:50px;
   height:50px;
   box-shadow: 0 0 3px rgba(0,0,0,0.5);
}
.b100efb span {
   display:block;
   text-align:center;
   font-size: 20px;
}
.b100efb span:first-child {
   font-size: 10px;
}';
	add_option('b100efb_style', $style);
	add_option('b100efb_xOffset', '-100');
	add_option('b100efb_yOffset', '0');
}

// deactivating
function b100efb_deactivate() {
	// needed for proper deletion of every option
	delete_option('b100efb_style');
	delete_option('b100efb_xOffset');
	delete_option('b100efb_yOffset');
}

// uninstalling
function b100efb_uninstall() {
	// delete all data stored
	delete_option('b100efb_style');
	delete_option('b100efb_xOffset');
	delete_option('b100efb_yOffset');
}

function b100efb_create_menu() {
	//create new top-level menu
	add_menu_page('#blogg100 Badge', '#blogg100', 'administrator', B100EFB_PLUGIN_DIRECTORY.'/b100efb_settings_page.php');
}

function b100efb_register_settings() {
	//register settings
	register_setting('b100efb-settings-group', 'b100efb_style');
	register_setting('b100efb-settings-group', 'b100efb_xOffset');
	register_setting('b100efb-settings-group', 'b100efb_yOffset');
}

function b100efb_add_style() {
	$style = get_option('b100efb_style');
	
	$buffer = '
	<style>
		.b100efb {
			position:absolute;
			z-index:10;
		}
		'.$style;
	
	if (current_user_can('manage_options')){
		$buffer .= '
		.b100efb {
			cursor:hand;
			cursor:pointer;
		}
		.b100efb_h {box-shadow:0 0 3px #000}
		.b100efb_d {box-shadow:0 2px 5px #000}
		';
	}
		
	$buffer .= '
	</style>';
	
	echo $buffer;
}

function b100efb_add_js(){
	$xOffset = get_option('b100efb_xOffset');
	$yOffset = get_option('b100efb_yOffset');
	
	$buffer = '
	<script type="text/javascript">
	jQuery(document).ready(function($){
		var b100efb_xOffset = '.$xOffset.';
		var b100efb_yOffset = '.$yOffset.';
		var b100efb_position = "main";
		
		$("img, iframe, document").each(function(){
			$(this).load(function(){
				b100efb_updatePos();
			});
		});
		
		$(window).resize(function(){
			b100efb_updatePos();
		});
		
		function b100ef_invertInt(int){
			if(int>0)
				int = 0-int;
			else if(int<0)
				int = int*-1
			return int;
		}
		
		function b100efb_updatePos(){
			if(b100efb_position == "body"){
				$(".b100ef").each(function(){
					var hPos = $(this).offset();
					
					var b100 = $(this).find(".b100efb");
					
					b100.css("left", hPos.left-b100ef_invertInt(b100efb_xOffset)+"px");
					b100.css("top", hPos.top-b100ef_invertInt(b100efb_yOffset)+"px");
				});
			} else if(b100efb_position == "post") {
				$(".b100ef").each(function(){
					var b100 = $(this).find(".b100efb");
					
					b100.css("left", b100efb_xOffset+"px");
					b100.css("top", b100efb_yOffset+"px");
				});
			} else if(b100efb_position == "main") {
				$(".b100ef").each(function(){
					var hPos = $(this).offset();
					var hPosParent = $(this).offsetParent().offset();
					
					var b100 = $(this).find(".b100efb");
					
					b100.css("left", hPos.left-hPosParent.left-b100ef_invertInt(b100efb_xOffset)+"px");
					b100.css("top", hPos.top-hPosParent.top-b100ef_invertInt(b100efb_yOffset)+"px");
				});
			}
		}
		
		function b100ef_moveNode(){
			$(".b100efb").each(function(){
				$(this).parent().prepend($(this));
			});
		}
		
		function b100ef_setPositionType(){
			var lastPos = 0;
			var loopCount = 0;
			
			$(".b100efb").each(function(){
				var parentPos = $(this).offsetParent().offset();
				
				if(loopCount > 0 && lastPos != parentPos.top){
					b100efb_position = "post";
					return;
				}
				
				lastPos = parentPos.top;
				loopCount++;
			});
		}
		
		function b100ef_init(){
			$(".b100efb").css("display","block");
			b100ef_moveNode();
			b100ef_setPositionType();
			b100efb_updatePos();
		}
		
		b100ef_init();
		';
		
	if (current_user_can('manage_options')){
		
		$buffer .= '
		var ajaxurl = "'.admin_url('admin-ajax.php').'";
		$(".b100efb").draggable({
			start: function(){
				$(this).addClass("b100efb_d");
			},
			drag: function(){},
			stop: function(){
				$(this).removeClass("b100efb_d");
				
				var thisPos = $(this).offset();
				var postPos = $(this).closest(".b100ef").offset();
				
				if(!b100efb_position) {
					thisPos = $(this).offsetParent().offset();
					postPos = $(this).closest(".b100ef").offsetParent().offset();
				}
				
				b100efb_xOffset = b100ef_invertInt(postPos.left - thisPos.left);
				b100efb_yOffset = b100ef_invertInt(postPos.top - thisPos.top);
				
				b100efb_updatePos();
				
				$.ajax({
					type: "GET",
					url: ajaxurl,
					data: {action: "b100efb_update_position", xOffset: b100efb_xOffset, yOffset: b100efb_yOffset}
				}).done(function(msg) {
					if(msg == 2)
						alert("Positionen uppdaterades");
					else
						alert("Lyckades inte uppdatera positionen");
				});
			}
		});
		
		$(".b100efb").hover(
			function(){
				$(this).addClass("b100efb_h");
			},
			function(){
				$(this).removeClass("b100efb_h");
			})
		';
	}
		
		$buffer .='
	});
	</script>
	
	';
	echo $buffer;
}
function b100efb_post_class($c) {
	$c[] = 'b100ef';
	return $c;
}
function b100efb_post_content($content) {
	if(!is_feed()) {
		global $post;
		if (has_tag('#blogg100', $post) || has_tag('blogg100', $post)){
			$start_date = '2013-01-23';
			$post_date = get_the_date('Y-m-d', $post);
			
			$day = (strtotime($post_date) - strtotime($start_date)) / (60 * 60 * 24)+1;
			
			$day = (strlen($day) < 3 ? '0'.$day : $day);
			$day = (strlen($day) < 3 ? '0'.$day : $day);
			
			$content .= '<div class="b100efb" style="display:none"><span>#blogg100</span> <span>#'.$day.'</span></div>';
		}
	}
	return $content;
}

function b100efb_update_position(){
	if(current_user_can('manage_options')){
		$msg = 0;
		if($_GET['xOffset'] != null){
			$xOffset = $_GET['xOffset'];
			update_option('b100efb_xOffset', $xOffset);
			$msg++;
		}
		
		if($_GET['yOffset'] != null){
			$yOffset = $_GET['yOffset'];
			update_option('b100efb_yOffset', $yOffset);
			$msg++;
		}
		echo $msg;
		die();
	}
}

function b100efb_init(){
	if(true) {
	add_action('wp_ajax_b100efb_update_position', 'b100efb_update_position');
	/*
	if ($_POST['b100ef']){
		b100efb_update_position();
	}
	*/
	// Add scripts, actions and filters
	wp_enqueue_script('jquery');
	//if (current_user_can('manage_options')){
		wp_enqueue_script('jquery-ui-draggable');
	//}
	
	add_action('wp_head', 'b100efb_add_style');
	add_action('wp_footer', 'b100efb_add_js');
	add_filter('the_content', 'b100efb_post_content');
	add_filter('post_class', 'b100efb_post_class');
	}
}

// Run the plugin
b100efb_init();
?>
