<?php
/*
Title: Text Fields
Setting: piklist_demo_fields
Tab Order: 10
*/

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'text_class_regular'
    ,'label' => __('Text', 'piklist-demo')
    ,'description' => 'class="regular-text"'
    ,'help' => __('You can easily add tooltips to your fields with the help parameter.', 'piklist-demo')
    ,'attributes' => array(
      'class' => 'regular-text'
      ,'placeholder' => __('Enter some text', 'piklist-demo')
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'text_required'
    ,'label' => __('Text Required', 'piklist-demo')
    ,'description' => "required => true"
    ,'attributes' => array(
      'class' => 'regular-text'
      ,'placeholder' => __('Enter text or this page won\'t save.', 'piklist-demo')
    )
    ,'required' => true
  ));
  
  piklist('field', array(
    'type' => 'text'
    ,'field' => 'text_add_more'
    ,'add_more' => true
    ,'label' => __('Add More', 'piklist-demo')
    ,'description' => 'add_more="true" columns="8"'
    ,'attributes' => array(
      'columns' => 8
      ,'placeholder' => __('Enter some text', 'piklist-demo')
    )
  ));
  
  piklist('field', array(
    'type' => 'textarea'
    ,'field' => 'demo_textarea_large'
    ,'label' => __('Large Code', 'piklist-demo')
    ,'description' => 'class="large-text code" rows="10" columns="50"'
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'attributes' => array(
      'rows' => 10
      ,'cols' => 50
      ,'class' => 'large-text code'
    )
  ));

  piklist('field', array(
    'type' => 'html'
    ,'label' => __('HTML Field', 'piklist-demo')
    ,'description' => __('Allows you to output any HTML in the proper format.', 'piklist-demo')
    ,'value' => sprintf(__('%1$s %2$sFirst Item%3$s %2$sSecond Item%3$s %4$s', 'piklist-demo'), '<ul>', '<li>', '</li>', '</ul>')
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));