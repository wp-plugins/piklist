<?php
/*  
Title: Post Submit
Method: post
Message: Data saved in Piklist Demos, under the Validation tab.
Logged in: true
*/

/**
 * Piklist forms automatically generate a shortcode:
 *
 * If your form is in a PLUGIN (i.e. wp-content/plugins/my-plugin/parts/forms/my-form.php)
 * Use [piklist_form form="my-form" add_on="my-plugin"]
 *
 * If your form is in a THEME (i.e. wp-content/themes/my-theme/piklist/parts/forms/my-form.php)
 * Use [piklist_form form="my-form" add_on="theme"]
 *
 */

/** 
 * The shortcode for this form is:
 * [piklist_form form="new-post-with-validation" add_on="piklist-demos"]
 */

/**
 * The fields in this form are exactly like the fields in piklist-demos/parts/meta-boxes/field-validate.php
 * Only the 'scope' paramater needed to be added.
 */

  // Where to save this form
  piklist('field', array(
    'type' => 'hidden'
    ,'scope' => 'post'
    ,'field' => 'post_type'
    ,'value' => 'piklist_demo'
  ));


  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'post' // post_title is in the wp_posts table, so scope is: post
    ,'field' => 'post_title'
    ,'label' => __('Title', 'piklist-demo')
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  // Allows user to choose their own post status.
  $statuses = piklist_cpt::get_post_statuses_for_type('piklist_demo', false);

  piklist('field', array(
    'type' => 'select'
    ,'scope' => 'post'
    ,'field' => 'post_status'
    ,'label' => __('Post Status', 'piklist-demo')
    ,'choices' => $statuses
  ));

  /**
   * To automatically set the post status:
   *** Remove the field above since it's letting the user choose their status
   *** Uncomment this field
   *** Set your default post status by changing the "value" parameter.
   */
  // piklist('field', array(
  //   'type' => 'hidden'
  //   ,'scope' => 'post'
  //   ,'field' => 'post_status'
  //   ,'value' => 'pending'
  // ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'validate_text_required'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('Text Required', 'piklist-demo')
    ,'description' => "required => true"
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
    ,'required' => true
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'validate_group_required'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('Group Required', 'piklist-demo')
    ,'add_more'=> true
    ,'fields'  => array(
      array(
        'type' => 'text'
        ,'field' => 'name'
        ,'label' => 'Name'
        ,'columns' => 7
        ,'attributes' => array(
          'placeholder' => 'Name'
        )
      )
      ,array(
        'type' => 'checkbox'
        ,'field' => 'hierarchical'
        ,'label' => 'Type'
        ,'required' => true
        ,'columns' => 5
        ,'choices' => array(
          'true' => 'Hierarchical'
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'label' => __('File Name', 'piklist-demo')
    ,'field' => 'sanitize_file_name'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'description' => 'Converts multiple words to a valid file name'
    ,'sanitize' => array(
      array(
        'type' => 'file_name'
      )
    )
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'validate_emaildomain'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('Email address', 'piklist-demo')
    ,'description' => __('Validate Email and Email Domain')
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
    ,'validate' => array(
      array(
        'type' => 'email'
      )
      ,array(
        'type' => 'email_domain'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'validate_file_exists'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('File exists?', 'piklist-demo')
    ,'description' => sprintf(__('Test with: %s', 'piklist-demo'), 'http://wordpress.org/plugins/about/readme.txt')
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
    ,'validate' => array(
      array(
        'type' => 'file_exists'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'field' => 'validate_image'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('Image', 'piklist-demo')
    ,'description' => sprintf(__('Test with: %s', 'piklist-demo'), 'http://piklist.com/wp-content/themes/piklistcom-base/images/piklist-logo@2x.png')
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
    ,'validate' => array(
      array(
        'type' => 'image'
      )
    )
  ));

  piklist('field', array(
    'type' => 'checkbox'
    ,'field' => 'validate_checkbox_limit'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('Checkbox', 'piklist-demo')
    ,'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ,'value' => 'third'
    ,'choices' => array(
      'first' => 'First Choice'
      ,'second' => 'Second Choice'
      ,'third' => 'Third Choice'
    )
    ,'validate' => array(
      array(
        'type' => 'limit'
        ,'options' => array(
          'min' => 2
          ,'max' => 2
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'file'
    ,'field' => 'validate_upload_media_limit'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'label' => __('Add File(s)','piklist-demo')
    ,'description' => 'No more than one file is allowed'
    ,'options' => array(
      'modal_title' => __('Add File(s)','piklist-demo')
      ,'button' => __('Add','piklist-demo')
    )
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
    ,'validate' => array(
      array(
        'type' => 'limit'
        ,'options' => array(
          'min' => 0
          ,'max' => 1
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'validate_group_add_more_limit'
    ,'scope' => 'post_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'add_more' => true
    ,'label' => __('Grouped/Add-More with Limit', 'piklist-demo')
    ,'description' => 'No more than two add-mores are allowed'
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'group_field_1'
        ,'label' => __('Field 1', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'group_field_2'
        ,'label' => __('Field 2', 'piklist-demo')
        ,'columns' => 12
      )
    )
    ,'validate' => array(
      array(
        'type' => 'limit'
        ,'options' => array(
          'min' => 1
          ,'max' => 2
        )
      )
    )
  ));

  // Submit button
  piklist('field', array(
    'type' => 'submit'
    ,'field' => 'submit'
    ,'value' => 'Submit'
  ));