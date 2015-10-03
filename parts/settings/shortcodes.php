<?php
/*
Title: Shortcodes
Setting: piklist_core
Order: 30
*/
  
  piklist('field', array(
    'type' => 'group'
    ,'field' => 'shortocde_ui'
    ,'label' => __('Allow Shortcode UI', 'piklist')
    ,'add_more' => true
    ,'sortable' => false
    ,'fields' => array(
      array(
        'type' => 'select'
        ,'label' => 'Shortcode'
        ,'field' => 'tag'
        ,'columns' => 4
        ,'choices' => piklist_shortcode::get_shortcodes()
        ,'value' => 'piklist_form'
      )
      ,array(
        'type' => 'checkbox'
        ,'label' => 'Options'
        ,'field' => 'options'
        ,'list' => false
        ,'columns' => 8
        ,'choices' => array(
          'preview' => 'Preview'
        )
        ,'value' => 'preview'
      )
    )
  ));