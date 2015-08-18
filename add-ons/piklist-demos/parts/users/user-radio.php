<?php
/*
Title: Radio Fields
Capability: manage_options
Order: 50
Tab: Lists
*/

  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'radio'
    ,'label' => __('Radio', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
  ));

  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'radio_add_more'
    ,'label' => __('Radio Add More', 'piklist-demo')
    ,'add_more' => true
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 'second'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
  ));

  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'radio_inline'
    ,'label' => __('Single Line', 'piklist-demo')
    ,'value' => 'no'
    ,'list' => false
    ,'choices' => array(
      'yes' => __('Yes', 'piklist-demo')
      ,'no' => __('No', 'piklist-demo')
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'radio_list'
    ,'label' => __('Group Lists', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'fields' => array(
      array(
        'type' => 'radio'
        ,'field' => 'radio_list_1'
        ,'label' => __('List #1', 'piklist-demo')
        ,'label_position' => 'before'
        ,'value' => 'second'
        ,'choices' => array(
          'first' => __('1-1 Choice', 'piklist-demo')
          ,'second' => __('1-2 Choice', 'piklist-demo')
        )
        ,'columns' => 6
      )
      ,array(
        'type' => 'radio'
        ,'field' => 'radio_list_2'
        ,'label' => __('List #2', 'piklist-demo')
        ,'label_position' => 'before'
        ,'value' => 'second'
        ,'choices' => array(
          'first' => __('2-1 Choice', 'piklist-demo')
          ,'second' => __('2-2 Choice', 'piklist-demo')
          ,'third' => __('2-3 Choice', 'piklist-demo')
        )
        ,'columns' => 6
      )
    )
  ));

  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'radio_nested'
    ,'label' => __('Nested Field', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => sprintf(__('Second Choice with a nested %s input.', 'piklist-demo'), '[field=radio_nested_text]')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'radio_nested_text'
        ,'value' => '123'
        ,'embed' => true
        ,'attributes' => array(
          'class' => 'small-text'
        )
      )
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));

?>