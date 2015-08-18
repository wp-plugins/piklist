<?php
/*
Title: Date / Time Fields
Setting: piklist_demo_fields
Tab: Advanced
Order: 30
*/
  
  piklist('field', array(
    'type' => 'datepicker'
    ,'field' => 'date'
    ,'label' => __('Date', 'piklist-demo')
    ,'description' => __('Choose a date', 'piklist-demo')
    ,'options' => array(
      'dateFormat' => 'M d, yy'
    )
    ,'attributes' => array(
      'size' => 12
    )
    ,'value' => date('M d, Y', time() + 604800)
  ));
  
  piklist('field', array(
    'type' => 'datepicker'
    ,'field' => 'date_add_more'
    ,'add_more' => true
    ,'label' => __('Add More', 'piklist-demo')
    ,'description' => __('Choose a date', 'piklist-demo')
    ,'options' => array(
      'dateFormat' => 'M d, yy'
    )
    ,'attributes' => array(
      'size' => 12
    )
    ,'value' => date('M d, Y', time() + 604800)
  ));
    
  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));
