<?php
/*
Title: Add More Fields
Setting: piklist_demo_fields
Tab: Advanced
Tab Order: 20
*/

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'demo_add_more'
    ,'label' => __('Text', 'piklist-demo')
    ,'add_more' => true
    ,'value' => 'single'
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
    ,'label' => __('Content Section (Grouped)', 'piklist-demo')
    ,'description' => __('When an add-more field is nested it should be grouped to maintain the data relationships.', 'piklist-demo')
    ,'field' => 'demo_content'
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
        ,'field' => 'content'
        ,'add_more' => true
        ,'fields' => array(
          array(
            'type' => 'select'
            ,'field' => 'post_id'
            ,'label' => __('Content Title', 'piklist-demo')
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

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Content Section with Siblings (Grouped)', 'piklist-demo')
    ,'description' => __('When an add-more field is nested it should be grouped to maintain the data relationships.', 'piklist-demo')
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
    ,'type' => 'Settings Section'
  ));


?>