<?php
/*
Title: ColorPicker Fields
Capability: manage_options
Order: 60
Tab: Advanced
*/

  piklist('field', array(
    'type' => 'colorpicker'
    ,'field' => 'color'
    ,'label' => __('Color Picker', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'colorpicker'
    ,'add_more' => true
    ,'field' => 'color_add_more'
    ,'label' => __('Color Picker Add More', 'piklist-demo')
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));