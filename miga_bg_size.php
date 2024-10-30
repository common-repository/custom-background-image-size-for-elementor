<?php

 /**
  * Plugin Name
  *
  * @package           PluginPackage
  * @author            Michael Gangolf
  * @copyright         2022 Michael Gangolf
  * @license           GPL-2.0-or-later
  *
  * @wordpress-plugin
  * Plugin Name:       Custom background-image size for Elementor
  * Description:       Change the background-image size in an Elementor Container widget
  * Version:           1.0.1
  * Requires at least: 5.2
  * Requires PHP:      7.2
  * Author:            Michael Gangolf
  * Author URI:        https://www.migaweb.de/
  * License:           GPL v2 or later
  * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
  */

 if (! defined('ABSPATH')) {
     exit; // Exit if accessed directly.
 }


 add_action('init', static function () {
     if (! did_action('elementor/loaded')) {
         return false;
     }
 });


 function miga_bg_size_before_section_end($element, $section_id, $args)
 {
     if (
         "container" === $element->get_name() && "section_background" === $section_id
     ) {

       $opts = ["full" => "full"];
       $imgSizes = wp_get_registered_image_subsizes();
       foreach( $imgSizes as $key=>$value){
         $opts[$key] = array($key);
       }

         $element->add_control(
             'background_size_cont',
             [
                 'label' => esc_html__('Background image size', 'el_bg'),
                 'type' => \Elementor\Controls_Manager::SELECT,
                 'default' => 'full',
                 'options' => $opts,
             ]
         );
     }
 }


 function miga_bg_size_before_render($element)
 {
     if ("container" === $element->get_name()) {
         $id = $element->get_settings("background_image")["id"];
         if (!empty($id)) {
           if ($element->get_settings("background_size_cont") != "full") {
             $newImage = wp_get_attachment_image_src($id, $element->get_settings("background_size_cont"))[0];
             $element->add_render_attribute("_wrapper", ["style" => "background-image:url('".esc_url($newImage)."');"]);
           }
         }
     }
 }
 add_action("elementor/element/before_section_end", "miga_bg_size_before_section_end", 10, 3);
 add_action("elementor/frontend/before_render", "miga_bg_size_before_render");
