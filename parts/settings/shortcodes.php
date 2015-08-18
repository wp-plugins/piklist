<?php
/*
Title: Shortcodes
Setting: piklist_core
Tab Order: 10
*/
  
  $choices = piklist_shortcode::get_shortcodes();

  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'shortocde_ui'
    ,'label' => __('Allow Shortcode UI', 'piklist')
    ,'list' => count($choices) < 5 ? true : false
    ,'columns' => count($choices) < 5 ? '12' : '4'
    ,'help' => __('Enable the Shortcode UI for these non-Piklist shortcodes.', 'piklist')
		,'choices' => $choices
  ));