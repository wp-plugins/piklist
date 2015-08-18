<?php
/*
Title: Piklist Demo Fields
Description: This is an example of some fields built with Piklist
Taxonomy: piklist_demo_type
Order: 0
*/

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'text_class_regular'
    ,'label' => __('Text', 'piklist-demo')
    ,'description' => 'class="regular-text"'
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
    ,'label' => __('Add More', 'piklist-demo')
    ,'description' => 'add_more="true" columns="8"'
    ,'value' => 'Lorem'
    ,'attributes' => array(
      'columns' => 8
    )
  ));
  
  piklist('field', array(
    'type' => 'number'
    ,'field' => 'number'
    ,'label' => __('Number', 'piklist-demo')
    ,'description' => 'ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 5
    ,'attributes' => array(
      'class' => 'small-text'
      ,'step' => 5
      ,'min' => 5
    )
  ));

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
        ,'value' => 'third'
        ,'choices' => array(
          'first' => __('First Choice', 'piklist-demo')
          ,'second' => __('Second Choice', 'piklist-demo')
          ,'third' => __('Third Choice', 'piklist-demo')
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
          'forth' => __('Forth Choice', 'piklist-demo')
          ,'fifth' => __('Fifth Choice', 'piklist-demo')
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
      ,'second' => sprintf(__('Second Choice with a nested %s input.', 'piklist-demo'), '[field=checkbox_nested_text]')
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

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'date_time'
    ,'label' => __('Date / Time', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'fields' => array(
      array(
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
        ,'columns' => 3
      )
    )
  ));


  
  piklist('field', array(
    'type' => 'colorpicker'
    ,'field' => 'color'
    ,'label' => __('Color Picker', 'piklist-demo')
    ,'value' => '#03ADEF'
  ));


  piklist('field', array(
    'type' => 'select'
    ,'field' => 'show_hide_select'
    ,'label' => __('Toggle a field', 'piklist-demo')
    ,'choices' => array(
      'show' => 'Show'
      ,'hide' => 'Hide'
    )
    ,'value' => 'hide'
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'show_hide_field_select'
    ,'label' => __('Show/Hide Field', 'piklist-demo')
    ,'description' => __('This field is toggled by the Select field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show'
      )
    )
  ));
  
  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'change'
    ,'label' => __('Update a field', 'piklist-demo')
    ,'choices' => array(
      'hello-world' => __('Hello World', 'piklist-demo')
      ,'clear' => __('Clear', 'piklist-demo')
    )
    ,'value' => 'hello-world'
    ,'conditions' => array(
      array(
        'field' => 'update_field'
        ,'value' => 'hello-world' 
        ,'update' => __('Hello World!', 'piklist-demo')
        ,'type' => 'update'
      )
      ,array(
        'field' => 'update_field'
        ,'value' => 'clear' 
        ,'update' => '' 
        ,'type' => 'update'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'update_field'
    ,'value' => __('Hello World!' , 'piklist-demo')
    ,'label' => __('Update This Field', 'piklist-demo')
    ,'description' => __('This field is updated by the field above', 'piklist-demo')
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Term Meta Section'
  ));
  
?>