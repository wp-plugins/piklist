<?php
/*  
Title: User Profile
Method: post
Message: User Profile Saved.
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
 * [piklist_form form="user-profile" add_on="piklist-demos"]
 */

?>


<h3><?php _e('Name'); ?></h3>

<?php
 
  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user' // user_login is in the wp_users table, so scope is: user
    ,'field' => 'user_login'
    ,'label' => __('User login', 'piklist-demo')
    ,'attributes' => array(
      'autocomplete' => 'off'
    )
  ));

  piklist('field', array(
    'type' => 'password'
    ,'scope' => 'user'
    ,'field' => 'user_pass'
    ,'label' => __('New Password', 'piklist-demo')
    ,'value' => false // Setting to false forces no value to show in form.
    ,'attributes' => array(
      'autocomplete' => 'off'
    )
  ));
  
  piklist('field', array(
    'type' => 'password'
    ,'scope' => 'user'
    ,'field' => 'user_pass_repeat'
    ,'label' => __('Repeat New Password', 'piklist-demo')
    ,'value' => false // Setting to false forces no value to show in form.
    ,'validate' => array(
      array(
        'type' => 'match'
        ,'options' => array(
          'field' => 'user_pass'
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'first_name'
    ,'label' => __('First name', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta' // scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'last_name'
    ,'label' => __('Last name', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'nickname'
    ,'label' => __('Nickname', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'display_name'
    ,'label' => __('Display name', 'piklist-demo')
  ));

?>

<h3><?php _e('Contact Info'); ?></h3>

<?php

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'user_email'
    ,'label' => __('Email', 'piklist-demo')
    ,'required' => true
    ,'validate' => array(
      array(
        'type' => 'email_exists'
      )
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'user_url'
    ,'label' => __('Website', 'piklist-demo')
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'description'
    ,'label' => __('Biographical Info', 'piklist-demo')
  ));

?>

<h3><?php _e('Personal Options'); ?></h3>

<?php

  piklist('field', array(
    'type' => 'checkbox'
    ,'scope' => 'user_meta'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'comment_shortcuts'
    ,'label' => __('Keyboard Shortcuts', 'piklist-demo')
    ,'choices' => array(
      'true' => 'Enable keyboard shortcuts for comment moderation.'
    )
  ));

  piklist('field', array(
    'type' => 'checkbox'
    ,'scope' => 'user_meta'// scope needs to be set on EVERY field for front-end forms.
    ,'field' => 'show_admin_bar_front'
    ,'label' => __('Toolbox', 'piklist-demo')
    ,'choices' => array(
      'true' => 'Show Toolbar when viewing site'
    )
  ));

  // Submit button
  piklist('field', array(
    'type' => 'submit'
    ,'field' => 'submit'
    ,'value' => 'Submit'
  ));