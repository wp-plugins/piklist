<?php
/*
Title: Checkbox Fields
Setting: piklist_demo_fields
Tab: Lists
Order: 30
*/

  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'checkbox'
    ,'label' => __('Normal', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
  ));
  
  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'checkbox_inline'
    ,'label' => __('Single Line', 'piklist-demo')
    ,'value' => 'that'
    ,'list' => false
    ,'choices' => array(
      'this' => __('This', 'piklist-demo')
      ,'that' => __('That', 'piklist-demo')
    )
  ));
 
  piklist('field', array(
    'type' => 'group'
    ,'field' => 'checkbox_list'
    ,'label' => __('Group Lists', 'piklist-demo')
    ,'list' => false
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'fields' => array(
      array(
        'type' => 'checkbox'
        ,'field' => 'checkbox_list_1'
        ,'label' => __('List #1', 'piklist-demo')
        ,'label_position' => 'before'
        ,'value' => 'third'
        ,'choices' => array(
          'first' => __('First Choice', 'piklist-demo')
          ,'third' => __('Third Choice', 'piklist-demo')
        )
        ,'columns' => 6
      )
      ,array(
        'type' => 'checkbox'
        ,'field' => 'checkbox_list_2'
        ,'label' => __('List #2', 'piklist-demo')
        ,'label_position' => 'before'
        ,'value' => 'second'
        ,'choices' => array(
          'first' => __('First Choice', 'piklist-demo')
          ,'second' => __('Second Choice', 'piklist-demo')
          ,'third' => __('Third Choice', 'piklist-demo')
        )
        ,'columns' => 6
      )
    )
  ));
  
  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'checkbox_nested'
    ,'label' => __('Nested Field', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => array(
      'first'
      ,'third'
    )
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => sprintf(__('Second Choices with a nested %s input.', 'piklist-demo'), '[field=checkbox_nested_text]')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'checkbox_nested_text'
        ,'value' => '12345'
        ,'embed' => true
        ,'attributes' => array(
          'class' => 'small-text'
        )
      )
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));
  
?>