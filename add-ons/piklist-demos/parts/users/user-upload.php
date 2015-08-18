<?php
/*
Title: Upload Fields
Capability: manage_options
Order: 10
Tab: Basic
*/

  // Any field with the scope set to the field name of the upload field will be treated as related
  // data to the upload. Below we see we are setting the post_status and post_title, where the
  // post_status is pulled dynamically on page load, hence the current status of the content is
  // applied. Have fun! ;)
  //
  // NOTE: If the post_status of an attachment is anything but inherit or private it will NOT be
  // shown on the Media page in the admin, but it is in the database and can be found using query_posts
  // or get_posts or get_post etc....

  piklist('field', array(
    'type' => 'file'
    ,'field' => 'upload_basic'
    ,'label' => __('Add File', 'piklist-demo')
    ,'description' => __('This is the basic upload field.', 'piklist-demo')
    ,'options' => array(
      'basic' => true
    )
  ));

  piklist('field', array(
    'type' => 'file'
    ,'field' => 'upload_media'
    ,'label' => __('Add File(s)','piklist-demo')
    ,'description' => __('This is the uploader seen in the admin by default.', 'piklist-demo')
    ,'options' => array(
      'modal_title' => __('Add File(s)', 'piklist-demo')
      ,'button' => __('Add', 'piklist-demo')
    )
    ,'validate' => array(
      array(
        'type' => 'limit'
        ,'options' => array(
          'min' => 0
          ,'max' => 2
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
  
  // piklist::pre($profileuser);
  // piklist::pre(get_user_meta($profileuser->ID, 'slides_basic', true));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'slides_basic'
    ,'add_more' => true
    ,'label' => __('Slide Images', 'piklist-demo')
    ,'description' => __('Add the slides for the slideshow.  You can add as many slides as you want, and they can be drag-and-dropped into the order that you would like them to appear.', 'piklist-demo')
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

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'User Section'
  ));