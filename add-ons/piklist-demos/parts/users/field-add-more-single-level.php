<?php
/*
Title: Add More Fields: Single Level
Order: 1
Tab: Add-More's
Sub Tab: Single Level
Flow: Demo Workflow
*/

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'demo_add_more'
    ,'label' => __('Text', 'piklist-demo')
    ,'add_more' => true
    ,'value' => __('single', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'datepicker'
    ,'field' => 'demo_add_more_date'
    ,'label' => __('Date Picker', 'piklist-demo')
    ,'add_more' => true
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'demo_add_more_group_todo'
    ,'label' => __('Todo\'s (Grouped)', 'piklist-demo')
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'select'
        ,'field' => 'user'
        ,'label' => __('Assigned to', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => piklist(
          get_users(
            array(
             'orderby' => 'display_name'
             ,'order' => 'asc'
            )
            ,'objects'
          )
          ,array(
            'ID'
            ,'display_name'
          )
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'task'
        ,'label' => __('Task', 'piklist-demo')
        ,'columns' => 8
      )
    )
  ));
 
  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Todo\'s (Un-Grouped)', 'piklist-demo')
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'select'
        ,'field' => 'demo_add_more_todo_user'
        ,'label' => __('Assigned to', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => piklist(
           get_users(
             array(
              'orderby' => 'display_name'
              ,'order' => 'asc'
             )
             ,'objects'
           )
           ,array(
             'ID'
             ,'display_name'
           )
          )
        )
        ,array(
          'type' => 'text'
          ,'field' => 'demo_add_more_todo_task'
          ,'label' => __('Task', 'piklist-demo')
          ,'columns' => 8
        )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Multiple Scopes', 'piklist-demo')
    ,'description' => __('Dropdown field saves as a category, text field saves as post meta.', 'piklist-demo')
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'select'
        ,'field' => 'category'
        ,'scope' => 'taxonomy'
        ,'label' => __('Choose Categories', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => piklist(
          get_terms('category', array(
            'hide_empty' => false
          ))
          ,array(
            'term_id'
            ,'name'
          )
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'demo_add_more_taxonomy_notes'
        ,'label' => __('Notes', 'piklist-demo')
        ,'columns' => 8
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
    ,'type' => 'User Section'
  ));