<?php
/*
Title: Conditional Fields
Setting: piklist_demo_fields
Tab: Conditions
Tab Order: 50
*/

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'show_hide_select'
    ,'label' => __('Select: toggle a field', 'piklist-demo')
    ,'choices' => array(
      'show1' => __('Show first set', 'piklist-demo')
      ,'show2' => __('Show second set', 'piklist-demo')
      ,'hide' => __('Hide all', 'piklist-demo')
    )
    ,'value' => 'hide'
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'show_hide_field_select_1'
    ,'label' => __('Show/Hide Field (Set 1)', 'piklist-demo')
    ,'description' => __('This field is toggled by the Select field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show1'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'another_show_hide_field_select_1'
    ,'label' => __('Another Show/Hide Field (Set 1)', 'piklist-demo')
    ,'description' => __('This field is also toggled by the Select field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show1'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'show_hide_field_select_set_2'
    ,'label' => __('Show/Hide Field (Set 2)', 'piklist-demo')
    ,'description' => __('This field is toggled by the Select field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show2'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'another_show_hide_field_select_set_2'
    ,'label' => __('Another Show/Hide Field (Set 2)', 'piklist-demo')
    ,'description' => __('This field is also toggled by the Select field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show2'
      )
    )
  ));

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'select_show_hide_field_select_set_2'
    ,'label' => __('Select Show/Hide Field (Set 2)', 'piklist-demo')
    ,'description' => __('This field is also toggled by the Select field above', 'piklist-demo')
    ,'choices' => array(
      'a' => __('Choice A', 'piklist-demo')
      ,'b' => __('Choice B', 'piklist-demo')
      ,'c' => __('Choice C', 'piklist-demo')
    )
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show2'
      )
    )
  ));

  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'checkbox_show_hide_field_select_set_2'
    ,'label' => __('Checkbox Show/Hide Field (Set 2)', 'piklist-demo')
    ,'description' => __('This field is also toggled by the Select field above', 'piklist-demo')
    ,'choices' => array(
      'a' => __('Choice A', 'piklist-demo')
      ,'b' => __('Choice B', 'piklist-demo')
      ,'c' => __('Choice C', 'piklist-demo')
    )
    ,'conditions' => array(
      array(
        'field' => 'show_hide_select'
        ,'value' => 'show2'
      )
    )
  ));


  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'show_hide'
    ,'label' => __('Radio: toggle a field', 'piklist-demo')
    ,'choices' => array(
      'show' => 'Show'
      ,'hide' => 'Hide'
    )
    ,'value' => 'hide'
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'show_hide_field'
    ,'label' => __('Show/Hide Field', 'piklist-demo')
    ,'description' => __('This field is toggled by the Radio field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide'
        ,'value' => 'show'
      )
    )
  ));

  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'show_hide_checkbox'
    ,'label' => __('Checkbox: toggle a field', 'piklist-demo')
    ,'choices' => array(
      'show' => 'Show'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'show_hide_field_checkbox'
    ,'label' => __('Show/Hide Field', 'piklist-demo')
    ,'description' => __('This field is toggled by the Checkbox field above', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'show_hide_checkbox'
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
        ,'update' => 'Hello World!'
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
    ,'value' => 'Hello World!'
    ,'label' => __('Update This Field', 'piklist-demo')
    ,'description' => __('This field is updated by the field above', 'piklist-demo')
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));