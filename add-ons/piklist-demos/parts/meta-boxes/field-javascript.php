<?php
/*
Title: Color / Date Pickers
Post Type: piklist_demo
Order: 40
Tab: Common
Sub Tab: Basic
Flow: Demo Workflow
*/ 

  piklist('field', array(
    'type' => 'colorpicker'
    ,'field' => 'colorpicker'
    ,'label' => __('Color Picker', 'piklist-demo')
    ,'attributes' => array(
      'class' => 'small-text'
    )
  ));

  piklist('field', array(
    'type' => 'datepicker'
    ,'field' => 'datepicker'
    ,'label' => __('Date Picker', 'piklist-demo')
    ,'attributes' => array(
      'class' => 'text'
    )
  ));
  
  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Meta Box'
  ));