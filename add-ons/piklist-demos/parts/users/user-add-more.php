<?php
/*
Title: Add More Fields
Capability: manage_options
Order: 110
Tab: Advanced
*/

  piklist::pre(get_user_meta($user_id->ID, 'newsletter_signup'));

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
              'first' => 'A-1'
              ,'second' => 'A-2'
              ,'third' => 'A-3'
            )
          )
          ,array(
            'type' => 'checkbox'
            ,'field' => 'newsletter_b'
            ,'columns' => 4
            ,'label' => __('Newsletter B', 'piklist-demo')
            ,'value' => 'second'
            ,'choices' => array(
              'first' => 'B-1'
              ,'second' => 'B-2'
              ,'third' => 'B-3'
            )
          )
          ,array(
            'type' => 'checkbox'
            ,'field' => 'newsletter_c'
            ,'columns' => 4
            ,'label' => __('Newsletter C', 'piklist-demo')
            ,'value' => 'third'
            ,'choices' => array(
              'first' => 'C-1'
              ,'second' => 'C-2'
              ,'third' => 'C-3'
            )
          )
        )
      )
    )
  ));

  // piklist('field', array(
  //   'type' => 'group'
  //   ,'field' => 'work_order_repair'
  //   ,'add_more' => true
  //   ,'label' => __('REPAIR', 'piklist-demo')
  //   ,'description' => __('Enter TYPE of Work, PRICE and DUE DATE', 'piklist-demo')
  //   ,'fields' => array(
  //     array(
  //       'type' => 'text'
  //       ,'field' => 'work'
  //       ,'columns' => 6
  //       ,'attributes' => array(
  //         'placeholder' => __('Type of work', 'piklist-demo')
  //       )
  //     )
  //     ,array(
  //       'type' => 'number'
  //       ,'field' => 'price'
  //       ,'columns' => 2
  //       ,'attributes' => array(
  //         'placeholder' => '$'
  //       )
  //     )
  //     ,array(
  //       'type' => 'datepicker'
  //       ,'field' => 'due'
  //       ,'columns' => 4
  //       ,'options' => array(
  //         'dateFormat' => 'M d, yy'
  //       )
  //       ,'attributes' => array(
  //         'placeholder' => __('Due date', 'piklist-demo')
  //       )
  //     )
  //   )
  // ));
  //
  //
  // piklist('field', array(
  //   'type' => 'group'
  //   ,'field' => 'demo_add_more_group_todo'
  //   ,'label' => __('Todo\'s (Grouped)', 'piklist-demo')
  //   ,'add_more' => true
  //   ,'fields' => array(
  //     array(
  //       'type' => 'select'
  //       ,'field' => 'user_todo'
  //       ,'label' => __('Assigned to', 'piklist-demo')
  //       ,'columns' => 4
  //       ,'choices' => array(
  //         'adam' => 'Adam'
  //         ,'bill' => 'Bill'
  //         ,'carol' => 'Carol'
  //       )
  //     )
  //     ,array(
  //       'type' => 'text'
  //       ,'field' => 'task'
  //       ,'label' => 'Task'
  //       ,'columns' => 8
  //     )
  //   )
  // ));
  //
  // piklist('field', array(
  //   'type' => 'group'
  //   ,'label' => __('Todo\'s (Un-Grouped)', 'piklist-demo')
  //   ,'add_more' => true
  //   ,'fields' => array(
  //     array(
  //       'type' => 'select'
  //       ,'field' => 'demo_add_more_todo_user'
  //       ,'label' => __('Assigned to', 'piklist-demo')
  //       ,'columns' => 4
  //       ,'choices' => array(
  //         'adam' => 'Adam'
  //         ,'bill' => 'Bill'
  //         ,'carol' => 'Carol'
  //       )
  //     )
  //     ,array(
  //       'type' => 'text'
  //       ,'field' => 'demo_add_more_todo_task'
  //       ,'label' => __('Task', 'piklist-demo')
  //       ,'columns' => 8
  //     )
  //   )
  // ));
  //
  //
  // piklist('field', array(
  //   'type' => 'group'
  //   ,'label' => __('Content Section (Grouped)', 'piklist-demo')
  //   ,'description' => __('When an add-more field is nested it should be grouped to maintain the data relationships.', 'piklist-demo')
  //   ,'field' => 'demo_content'
  //   ,'add_more' => true
  //   ,'fields' => array(
  //     array(
  //       'type' => 'text'
  //       ,'field' => 'csg_title'
  //       ,'label' => __('Title', 'piklist-demo')
  //       ,'columns' => 12
  //       ,'attributes' => array(
  //         'class' => 'large-text'
  //       )
  //     )
  //     ,array(
  //       'type' => 'text'
  //       ,'field' => 'csg_section'
  //       ,'label' => __('Section', 'piklist-demo')
  //       ,'columns' => 12
  //       ,'attributes' => array(
  //         'class' => 'large-text'
  //       )
  //     )
  //     ,array(
  //       'type' => 'group'
  //       ,'field' => 'content'
  //       ,'add_more' => true
  //       ,'fields' => array(
  //         array(
  //           'type' => 'select'
  //           ,'field' => 'post_id'
  //           ,'label' => __('Grade', 'piklist-demo')
  //           ,'columns' => 12
  //           ,'choices' => array (
  //             'a' => 'A'
  //             ,'b' => 'B'
  //             ,'c' => 'C'
  //           )
  //         )
  //       )
  //     )
  //   )
  // ));
  //
  // piklist('field', array(
  //   'type' => 'group'
  //   ,'label' => __('Content Section with Siblings (Grouped)', 'piklist-demo')
  //   ,'decription' => __('When an add-more field is nested it should be grouped to maintain the data relationships.', 'piklist-demo')
  //   ,'field' => 'demo_content_sibling'
  //   ,'add_more' => true
  //   ,'fields' => array(
  //     array(
  //       'type' => 'text'
  //       ,'field' => 'title'
  //       ,'label' => __('Section Title', 'piklist-demo')
  //       ,'columns' => 12
  //       ,'attributes' => array(
  //         'class' => 'large-text'
  //       )
  //     )
  //     ,array(
  //       'type' => 'text'
  //       ,'field' => 'tagline'
  //       ,'label' => __('Section Tagline', 'piklist-demo')
  //       ,'columns' => 12
  //       ,'attributes' => array(
  //         'class' => 'large-text'
  //       )
  //     )
  //     ,array(
  //       'type' => 'group'
  //       ,'field' => 'sibling_content_1'
  //       ,'add_more' => true
  //       ,'fields' => array(
  //         array(
  //           'type' => 'select'
  //           ,'field' => 'post_id_sibling_1'
  //           ,'label' => __('Content One Title', 'piklist-demo')
  //           ,'columns' => 12
  //           ,'choices' => piklist(
  //             get_posts(
  //                array(
  //                 'post_type' => 'post'
  //                 ,'orderby' => 'post_date'
  //                )
  //                ,'objects'
  //              )
  //              ,array(
  //                'ID'
  //                ,'post_title'
  //              )
  //           )
  //         )
  //       )
  //     )
  //     ,array(
  //       'type' => 'group'
  //       ,'field' => 'sibling_content_2'
  //       ,'add_more' => true
  //       ,'fields' => array(
  //         array(
  //           'type' => 'select'
  //           ,'field' => 'post_id_sibling_2'
  //           ,'label' => __('Content Two Title', 'piklist-demo')
  //           ,'columns' => 12
  //           ,'choices' => piklist(
  //             get_posts(
  //                array(
  //                 'post_type' => 'post'
  //                 ,'orderby' => 'post_date'
  //                )
  //                ,'objects'
  //              )
  //              ,array(
  //                'ID'
  //                ,'post_title'
  //              )
  //           )
  //         )
  //       )
  //     )
  //   )
  // ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));