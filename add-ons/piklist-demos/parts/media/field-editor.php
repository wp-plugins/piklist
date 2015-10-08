<?php
/*
Title: Editor Examples
Order: 100
Lock: true
Flow: Demo Workflow
Tab: Common
Sub Tab: Editor
*/

  piklist('field', array(
    'type' => 'editor'
    ,'field' => 'post_content_editor'
    ,'label' => __('Post Content', 'piklist-demo')
    ,'description' => __('This is the standard WordPress Editor, placed in a Metabox, which is placed in a Piklist WorkFlow tab. By default, Piklist formats the editor like any other field with a label to the left.', 'piklist-demo')
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'options' => array(
      'wpautop' => true
      ,'media_buttons' => true
      ,'tabindex' => ''
      ,'editor_css' => ''
      ,'editor_class' => ''
      ,'teeny' => false
      ,'dfw' => false
      ,'tinymce' => array(
        'resize' => false
        ,'wp_autoresize_on' => true
      )
      ,'quicktags' => true
      ,'drag_drop_upload' => true
    )
  ));
  
  piklist('field', array(
    'type' => 'editor'
    ,'field' => 'post_content_add_more'
    ,'label' => __('Post Content Add More', 'piklist-demo')
    ,'add_more' => true
    ,'description' => __('This is the teeny editor used in an add-more repeater field.', 'piklist-demo')
    ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'options' => array (
      'media_buttons' => true
      ,'teeny' => true
      ,'textarea_rows' => 5
      ,'drag_drop_upload' => true
      ,'tinymce' => array(
        'resize' => false
        ,'wp_autoresize_on' => true
      )
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Media Section'
  ));