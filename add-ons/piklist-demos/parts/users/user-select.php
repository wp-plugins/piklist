<?php
/*
Title: Select Fields
Capability: manage_options
Order: 30
Tab: Lists
*/

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'select'
    ,'label' => __('Select', 'piklist-demo')
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
  ));

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'select_add_more'
    ,'label' => __('Select Add More', 'piklist-demo')
    ,'add_more' => true
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));
?>