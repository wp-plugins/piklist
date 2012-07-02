<?php
/*
Title: Radio, Checkbox and Nested Fields
Setting: piklist-demo-fields
Order: 30
*/

  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'radio'
    ,'label' => __('Radio')
    ,'description' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
    ,'value' => 'third'
    ,'choices' => array(
      'first' => 'First Choice'
      ,'second' => 'Second Choice with a nested [field=radio_text_small] input.'
      ,'third' => 'Third Choice'
    )
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'radio_text_small'
        ,'value' => '12345'
        ,'embed' => true
        ,'attributes' => array(
          'class' => 'small-text'
        )
      )
    )
  ));
  
  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'checkbox'
    ,'label' => __('Checkbox')
    ,'description' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
    ,'value' => 'second'
    ,'choices' => array(
      'first' => 'First Choice'
      ,'second' => 'Second Choice'
      ,'third' => 'Third Choice with a nested [field=checkbox_select] input.'
    )
    ,'fields' => array(
      array(
        'type' => 'select'
        ,'field' => 'checkbox_select'
        ,'value' => 'third'
        ,'embed' => true
        ,'choices' => array(
          'first' => 'First Choice'
          ,'second' => 'Second Choice'
          ,'third' => 'Third Choice'
        )
      )
    )
  ));
  