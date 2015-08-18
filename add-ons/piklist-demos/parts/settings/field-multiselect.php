<?php
/*
Title: Multiselect Fields
Setting: piklist_demo_fields
Tab: Lists
Order: 30
*/

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'multiselect'
    ,'label' => __('Multiselect', 'piklist-demo')
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
    ,'attributes' => array(
      'multiple'
    )
  ));
  
  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));
?>