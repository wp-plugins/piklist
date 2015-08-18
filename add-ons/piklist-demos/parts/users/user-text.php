<?php
/*
Title: Text Fields
Capability: manage_options
Order: 10
Tab: Basic
*/


  piklist('field', array(
    'type' => 'text'
    ,'field' => 'text_class_small'
    ,'label' => __('Text', 'piklist-demo')
    ,'value' => 'Lorem'
    ,'help' => __('You can easily add tooltips to your fields with the help parameter.', 'piklist-demo')
    ,'attributes' => array(
      'class' => 'regular-text'
    )
  ));


  piklist('field', array(
    'type' => 'text'
    ,'field' => 'text_add_more'
    ,'add_more' => true
    ,'label' => __('Text Add More', 'piklist-demo')
    ,'description' => 'add_more="true"'
    ,'value' => 'Lorem'
  ));

  piklist('field', array(
    'type' => 'number'
    ,'field' => 'number'
    ,'label' => __('Number', 'piklist-demo')
    ,'description' => 'ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 5
    ,'attributes' => array(
      'class' => 'small-text'
      ,'step' => 1
      ,'min' => 0
      ,'max' => 10
    )
  ));

  piklist('field', array(
    'type' => 'textarea'
    ,'field' => 'textarea_large'
    ,'label' => __('Large Code', 'piklist-demo')
    ,'description' => 'class="large-text code" rows="10" columns="50"'
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'attributes' => array(
      'rows' => 10
      ,'cols' => 50
      ,'class' => 'large-text code'
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));

?>