<?php
/*  
Title: User Profile
Method: post
Message: User Profile Saved.
Logged in: true
*/


/**
 * Piklist forms automatically generate a shortcode:
 * [piklist_form form="THE FILE NAME" add_on="PLUGIN OR THEME SLUG"]
 *
 * 
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
    ,'label' => 'User login'
    ,'attributes' => array(
      'autocomplete' => 'off'
      ,'style' => 'width: 100%'
    )
    ,'validate' => array(
      array(
        'type' => 'username_exists'
      )
    )
  ));

  piklist('field', array(
    'type' => 'password'
    ,'scope' => 'user'
    ,'field' => 'user_pass'
    ,'label' => 'New Password'
    ,'value' => false // Setting to false forces no value to show in form.
    ,'attributes' => array(
      'autocomplete' => 'off'
      ,'style' => 'width: 100%'
    )
  ));
  
  piklist('field', array(
    'type' => 'password'
    ,'scope' => 'user'
    ,'field' => 'user_pass_repeat'
    ,'label' => 'Repeat New Password'
    ,'value' => false // Setting to false forces no value to show in form.
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
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
    ,'label' => 'First name'
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta'
    ,'field' => 'last_name'
    ,'label' => 'Last name'
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta'
    ,'field' => 'nickname'
    ,'label' => 'Nickname'
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user'
    ,'field' => 'display_name'
    ,'label' => 'Display name'
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

?>

<h3><?php _e('Contact Info'); ?></h3>

<?php

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user'
    ,'field' => 'user_email'
    ,'label' => 'Email'
    ,'required' => true
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user'
    ,'field' => 'user_url'
    ,'label' => 'Website'
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

  piklist('field', array(
    'type' => 'text'
    ,'scope' => 'user_meta'
    ,'field' => 'description'
    ,'label' => 'Biographical Info'
    ,'attributes' => array(
      'style' => 'width: 100%'
    )
  ));

?>

<h3><?php _e('Personal Options'); ?></h3>

<?php

  piklist('field', array(
    'type' => 'checkbox'
    ,'scope' => 'user_meta'
    ,'field' => 'comment_shortcuts'
    ,'label' => 'Keyboard Shortcuts'
    ,'choices' => array(
      'true' => 'Enable keyboard shortcuts for comment moderation.'
    )
  ));

  piklist('field', array(
    'type' => 'checkbox'
    ,'scope' => 'user_meta'
    ,'field' => 'show_admin_bar_front'
    ,'label' => 'Toolbox'
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