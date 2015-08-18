<?php
/*
Title: Multiselect Fields
Capability: manage_options
Order: 20
Tab: Lists
*/

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'multiselect'
    ,'label' => __('Multiselect', 'piklist-demo')
    ,'value' => 'third'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
    ,'attributes' => array(
      'multiple' => 'multiple'
    )
  ));

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'multiselect_add_more'
    ,'label' => __('Multiselect Add More', 'piklist-demo')
    ,'add_more' => true
    ,'description' => __('A grouped field. Data is not searchable, since it is saved in an array.', 'piklist-demo')
    ,'field' => 'multiselect_add_more_field'
    ,'choices' => array(
      'first' => __('First Choice', 'piklist-demo')
      ,'second' => __('Second Choice', 'piklist-demo')
      ,'third' => __('Third Choice', 'piklist-demo')
    )
    ,'attributes' => array(
      'multiple' => 'multiple'
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));
?>