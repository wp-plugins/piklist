<?php

  piklist('field', array(
    'type' => 'editor'
    ,'field' => 'post_content'
    ,'scope' => 'post'
    ,'label' => __('Post Content', 'piklist-demo')
    ,'description' => __('This is the standard post box, now placed in a Piklist WorkFlow.', 'piklist-demo')
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'options' => array (
      'wpautop' => true
      ,'media_buttons' => true
      ,'tabindex' => ''
      ,'editor_css' => ''
      ,'editor_class' => ''
      ,'teeny' => false
      ,'dfw' => false
      ,'tinymce' => true
      ,'quicktags' => true
    )
  ));

  piklist('field', array(
    'type' => 'editor'
    ,'field' => 'post_content'
    ,'label' => __('Post Content Add More', 'piklist-demo')
    ,'add_more' => true
    ,'description' => __('This is the teeny editor with an add more.', 'piklist-demo')
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'options' => array (
      'media_buttons' => true
      ,'teeny' => true
      ,'textarea_rows' => 5
    )
  ));

  piklist('field', array(
    'type' => 'editor'
    ,'field' => 'post_content_draggable'
    ,'scope' => 'post_meta'
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'options' => array (
      'wpautop' => true
      ,'media_buttons' => true
      ,'tabindex' => ''
      ,'editor_css' => ''
      ,'editor_class' => ''
      ,'teeny' => false
      ,'dfw' => false
      ,'tinymce' => true
      ,'quicktags' => true
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Widget'
  ));