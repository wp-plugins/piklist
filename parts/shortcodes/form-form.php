<?php
/*
Name: Piklist Form
Description: Embed a Piklist form
Shortcode: piklist_form
Icon: dashicons-forms
*/

  piklist('field', array(
    'type' => 'textarea'
    ,'field' => 'form'
    ,'label' => __('Form')
    ,'attributes' => array(
      'class' => 'large-text'
      ,'rows' => 5
    )
    ,'required' => true
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'add_on'
    ,'label' => __('Add on')
    ,'attributes' => array(
      'class' => 'large-text'
    )
    ,'required' => true
  ));