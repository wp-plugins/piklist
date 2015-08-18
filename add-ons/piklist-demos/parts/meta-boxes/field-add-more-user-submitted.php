<?php
/*
Title: User Submitted
Post Type: piklist_demo
Order: 1
Collapse: false
Tab: Add-More's
Sub Tab: User Submitted
Flow: Edit Demo
*/

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'ingredient_section'
    ,'label' => __('Ingredients', 'piklist-demo')
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'ingredients_component_title'
        ,'label' => __('Section Title', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'group'
        ,'field' => 'ingredient'
        ,'add_more' => true
        ,'fields' => array(
          array(
            'type' => 'text'
            ,'field' => 'ingredient_qty'
            ,'label' => __('Qty', 'piklist-demo')
            ,'columns' => 2
          )
          ,array(
            'type' => 'textarea'
            ,'field' => 'ingredient_description'
            ,'label' => __('Description', 'piklist-demo')
            ,'columns' => 10
          )
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'module_group'
    ,'label' => __('Page Modules', 'piklist-demo')
    ,'description' => __('Add-more\'s within a hide/show condition', 'piklist-demo')
    ,'value' => 'none'
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'select'
        ,'field' => 'module_select'
        ,'label' => __('Select a Module', 'piklist-demo')
        ,'columns' => 12
        ,'choices' => array(
          'none' => __('Select a Module to add', 'piklist-demo')
          ,'module' => __('Editor', 'piklist-demo')
          ,'repeating_module' => __('Repeating Textarea', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'editor'
        ,'field' => 'module_editor'
        ,'columns' => 12
        ,'options' => array(
          'wpautop' => true
          ,'media_buttons' => false
          ,'tabindex' => ''
          ,'editor_css' => ''
          ,'editor_class' => true
          ,'teeny' => false
          ,'dfw' => false
          ,'tinymce' => true
          ,'quicktags' => true
        )
        ,'conditions' => array(
          array(
            'field' => 'module_group:module_select'
            ,'value' => 'module'
          )
        )
      )
      ,array(
        'type' => 'textarea'
        ,'field' => 'module_title'
        ,'label' => __('Module title:', 'piklist-demo')
        ,'columns' => 12
        ,'add_more' => true
        ,'attributes' => array(
          'class' => 'large-text'
          , 'rows' => 2
        )
        ,'conditions' => array(
          array(
            'field' => 'module_group:module_select'
            ,'value' => 'repeating_module'
          )
        )
      )
      ,array(
        'type' => 'textarea'
        ,'field' => 'module_text'
        ,'label' => __('Module text:', 'piklist-demo')
        ,'columns' => 12
        ,'add_more' => true
        ,'attributes' => array(
          'class' => 'large-text'
          , 'rows' => 3
        )
        ,'conditions' => array(
          array(
            'field' => 'module_group:module_select'
            ,'value' => 'repeating_module'
          )
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'slides'
    ,'add_more' => true
    ,'label' => __('Slide Images', 'piklist-demo')
    ,'description' => __('Add the slides for the slideshow.  You can add as many slides as you want, and they can be drag-and-dropped into the order that you would like them to appear.', 'piklist-demo')
    ,'fields'  => array(
      array(
        'type' => 'file'
        ,'field' => 'image'
        ,'label' => __('Slides', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'url'
        ,'label' => __('URL', 'piklist-demo')
        ,'columns' => 12
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'slides_basic'
    ,'add_more' => true
    ,'label' => __('Slide Images with Basic uploader', 'piklist-demo')
    ,'description' => __('This is the same field as above, except it is using the Basic uploader.', 'piklist-demo')
    ,'fields'  => array(
      array(
        'type' => 'file'
        ,'field' => 'image'
        ,'label' => __('Slides', 'piklist-demo')
        ,'columns' => 12
        ,'options' => array(
          'basic' => true
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'url'
        ,'label' => __('URL', 'piklist-demo')
        ,'columns' => 12
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'guide_section'
    ,'label' => __('Upload Repeater', 'piklist-demo')
    ,'add_more' => true
      ,'fields' => array(
        array(
          'type' => 'file'
          ,'field' => 'image'
          ,'label'=> __('Image', 'piklist-demo')
          ,'description' => ''
          ,'columns' => 4
        )
        ,array(
          'type' => 'textarea'
          ,'field' => 'description'
          ,'label' => __('Information Section', 'piklist-demo')
          ,'description' => ''
          ,'add_more' => true
          ,'columns' => 12
        )
      )
  ));

piklist('field', array(
    'type' => 'group'
    ,'label' => __('Newsletter Signup', 'piklist-demo')
    ,'field' => 'newsletter_signup'
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'first_name'
        ,'label' => __('First Name', 'piklist-demo')
        ,'columns' => 4
      )
      ,array(
        'type' => 'text'
        ,'field' => 'last_name'
        ,'label' => __('Last Name', 'piklist-demo')
        ,'columns' => 4
      )
      ,array(
        'type' => 'text'
        ,'field' => 'email'
        ,'label' => __('Email Address', 'piklist-demo')
        ,'columns' => 4
      )
      ,array(
        'type' => 'group'
        ,'field' => 'newsletters'
        ,'fields' => array(
          array(
            'type' => 'checkbox'
            ,'field' => 'newsletter_a'
            ,'label' => __('Newsletter A', 'piklist-demo')
            ,'columns' => 4
            ,'value' => 'first'
            ,'choices' => array(
              'first' => __('A-1', 'piklist-demo')
              ,'second' => __('A-2', 'piklist-demo')
              ,'third' => __('A-3', 'piklist-demo')
            )
          )
          ,array(
            'type' => 'checkbox'
            ,'field' => 'newsletter_b'
            ,'columns' => 4
            ,'label' => __('Newsletter B', 'piklist-demo')
            ,'value' => 'second'
            ,'choices' => array(
              'first' => __('B-1', 'piklist-demo')
              ,'second' => __('B-2', 'piklist-demo')
              ,'third' => __('B-3', 'piklist-demo')
            )
          )
          ,array(
            'type' => 'checkbox'
            ,'field' => 'newsletter_c'
            ,'columns' => 4
            ,'label' => __('Newsletter C', 'piklist-demo')
            ,'value' => 'third'
            ,'choices' => array(
              'first' => __('C-1', 'piklist-demo')
              ,'second' => __('C-2', 'piklist-demo')
              ,'third' => __('C-3', 'piklist-demo')
            )
          )
        )
      )
    )
  ));


  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Meta Box'
  ));