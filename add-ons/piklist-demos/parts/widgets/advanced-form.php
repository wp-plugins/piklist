<?php

  piklist('field', array(
    'type' => 'colorpicker'
    ,'field' => 'color'
    ,'label' => __('Color Picker', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'colorpicker'
    ,'add_more' => true
    ,'field' => 'color_add_more'
    ,'label' => __('Color Picker Add More', 'piklist-demo')
  ));


  piklist('field', array(
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
 
  ));

  piklist('field', array(
    'type' => 'datepicker'
    ,'field' => 'date_add_more'
    ,'add_more' => true
    ,'label' => __('Date Add More', 'piklist-demo')
    ,'description' => __('Choose a date', 'piklist-demo')
    ,'options' => array(
      'dateFormat' => 'M d, yy'
    )
    ,'attributes' => array(
      'size' => 12
    )
    ,'value' => date('M d, Y', time() + 604800)
 
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Newsletter Signup (Grouped)', 'piklist-demo')
    ,'description' => __('Add email addresses with topic selectivity', 'piklist-demo')
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

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'work_order_repair'
    ,'add_more' => true
    ,'label' => __('REPAIR', 'piklist-demo')
    ,'description' => __('Enter TYPE of Work, PRICE and DUE DATE', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'work'
        ,'columns' => 6
        ,'attributes' => array(
          'placeholder' => __('Type of work', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'number'
        ,'field' => 'price'
        ,'columns' => 2
        ,'attributes' => array(
          'placeholder' => __('$', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'datepicker'
        ,'field' => 'due'
        ,'columns' => 4
        ,'options' => array(
          'dateFormat' => 'M d, yy'
        )
        ,'attributes' => array(
          'placeholder' => __('Due date', 'piklist-demo')
        )
      )
    )
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
        ,'choices' => array (
          'adam' => __('Adam', 'piklist-demo')
          ,'bill' => __('Bill', 'piklist-demo')
          ,'carol' => __('Carol', 'piklist-demo')
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
        ,'choices' => array (
          'adam' => __('Adam', 'piklist-demo')
          ,'bill' => __('Bill', 'piklist-demo')
          ,'carol' => __('Carol', 'piklist-demo')
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
    ,'label' => __('Content Section (Grouped)', 'piklist-demo')
    ,'description' => __('When an add-more field is nested it should be grouped to maintain the data relationships.', 'piklist-demo')
    ,'field' => 'demo_content'
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'csg_title'
        ,'label' => __('Title', 'piklist-demo')
        ,'columns' => 12
        ,'attributes' => array(
          'class' => 'large-text'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'csg_section'
        ,'label' => __('Section', 'piklist-demo')
        ,'columns' => 12
        ,'attributes' => array(
          'class' => 'large-text'
        )
      )
      ,array(
        'type' => 'group'
        ,'field' => 'content'
        ,'add_more' => true
        ,'fields' => array(
          array(
            'type' => 'select'
            ,'field' => 'post_id'
            ,'label' => __('Grade', 'piklist-demo')
            ,'columns' => 12
            ,'choices' => array (
              'a' => 'A'
              ,'b' => 'B'
              ,'c' => 'C'
            )
          )
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Content Section with Siblings (Grouped)', 'piklist-demo')
    ,'decription' => __('When an add-more field is nested it should be grouped to maintain the data relationships.', 'piklist-demo')
    ,'field' => 'demo_content_sibling'
    ,'add_more' => true
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'title'
        ,'label' => __('Section Title', 'piklist-demo')
        ,'columns' => 12
        ,'attributes' => array(
          'class' => 'large-text'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'tagline'
        ,'label' => __('Section Tagline', 'piklist-demo')
        ,'columns' => 12
        ,'attributes' => array(
          'class' => 'large-text'
        )
      )
      ,array(
        'type' => 'group'
        ,'field' => 'sibling_content_1'
        ,'add_more' => true
        ,'fields' => array(
          array(
            'type' => 'select'
            ,'field' => 'post_id_sibling_1'
            ,'label' => __('Content One Title', 'piklist-demo')
            ,'columns' => 12
            ,'choices' => piklist(
              get_posts(
                 array(
                  'post_type' => 'post'
                  ,'orderby' => 'post_date'
                 )
                 ,'objects'
               )
               ,array(
                 'ID'
                 ,'post_title'
               )
            )
          )
        )
      )
      ,array(
        'type' => 'group'
        ,'field' => 'sibling_content_2'
        ,'add_more' => true
        ,'fields' => array(
          array(
            'type' => 'select'
            ,'field' => 'post_id_sibling_2'
            ,'label' => __('Content Two Title', 'piklist-demo')
            ,'columns' => 12
            ,'choices' => piklist(
              get_posts(
                 array(
                  'post_type' => 'post'
                  ,'orderby' => 'post_date'
                 )
                 ,'objects'
               )
               ,array(
                 'ID'
                 ,'post_title'
               )
            )
          )
        )
      )
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Widget'
  ));