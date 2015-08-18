<?php
/*
Title: Party Invite
Setting: piklist_demo_fields
Order: 100
Collapse: false
Tab: Conditions
*/
  
  piklist('field', array(
    'type' => 'html'
    ,'field' => '_message_meal'
    ,'template' => 'admin_notice_error'
    ,'value' => __('We only serve steaks rare.', 'piklist-demo')
    ,'conditions' => array(
      'relation' => 'or'
      ,array(
        'field' => 'guest_meal'
        ,'value' => 'steak'
      )
      ,array(
        'field' => 'guest_one:guest_one_meal'
        ,'value' => 'steak'
      )
      ,array(
        'field' => 'guest_two:guest_two_meal'
        ,'value' => 'steak'
      )
    )
  ));

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'attending'
    ,'label' => __('Are you coming to the party?', 'piklist-demo')
    ,'choices' => array(
      '' => ''
      ,'yes' => 'Yes'
      ,'no' => 'No'
      ,'maybe' => 'Maybe'
    )
    ,'conditions' => array(
      array(
        'field' => 'guests'
        ,'value' => array('yes', 'maybe')
        ,'update' => 'yes'
        ,'type' => 'update'
      )
    )
  ));

  piklist('field', array(
    'type' => 'radio'
    ,'field' => 'guest_meal'
    ,'label' => __('Choose meal type', 'piklist-demo')
    ,'choices' => array(
      'chicken' => __('Chicken', 'piklist-demo')
      ,'steak' => __('Steak', 'piklist-demo')
      ,'vegetarian' => __('Vegetarian', 'piklist-demo')
    )
    ,'conditions' => array(
      array(
        'field' => 'attending'
        ,'value' => array('', 'no')
        ,'compare' => '!='
      )
    )
  ));

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'guests'
    ,'label' => __('Are you bringing guests', 'piklist-demo')
    ,'description' => __('Coming to party != (No or empty)', 'piklist-demo')
    ,'choices' => array(
      'yes' => __('Yes', 'piklist-demo')
      ,'no' => __('No', 'piklist-demo')
    )
    ,'conditions' => array(
      array(
        'field' => 'attending'
        ,'value' => array('', 'no')
        ,'compare' => '!='
      )
    )
  ));

  piklist('field', array(
    'type' => 'html'
    ,'field' => '_message_guests'
    ,'template' => 'admin_notice'
    ,'value' => __('Sorry, only two guests are allowed.', 'piklist-demo')
    ,'conditions' => array(
      array(
        'field' => 'guests_number'
        ,'value' => '3'
      )
    )
  ));

  piklist('field', array(
    'type' => 'number'
    ,'field' => 'guests_number'
    ,'label' => __('How many guests?', 'piklist-demo')
    ,'description' => __('Coming to party != (No or empty) AND Guests = Yes', 'piklist-demo')
    ,'value' => 1
    ,'attributes' => array(
      'class' => 'small-text'
      ,'step' => 1
      ,'min' => 1
      ,'max' => 3
    )
    ,'conditions' => array(
      array(
        'field' => 'attending'
        ,'value' => array('', 'no')
        ,'compare' => '!='
      )
      ,array(
        'field' => 'guests'
        ,'value' => 'yes'
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Guest One', 'piklist-demo')
    ,'field' => 'guest_one'
    ,'description' => __('Number of guests != empty', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'guest_one_name'
        ,'label' => __('Name', 'piklist-demo')
      )
      ,array(
        'type' => 'radio'
        ,'field' => 'guest_one_meal'
        ,'label' => __('Meal choice', 'piklist-demo')
        ,'choices' => array(
          'chicken' => __('Chicken', 'piklist-demo')
          ,'steak' => __('Steak', 'piklist-demo')
          ,'vegetarian' => __('Vegetarian', 'piklist-demo')
        )
      )
    )
    ,'conditions' => array(
      array(
        'field' => 'guests_number'
        ,'value' => array('', '0')
        ,'compare' => '!='
      )
      ,array(
        'field' => 'guests'
        ,'value' => 'yes'
      )
      ,array(
        'field' => 'attending'
        ,'value' => array('', 'no')
        ,'compare' => '!='
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Guest Two', 'piklist-demo')
    ,'field' => 'guest_two'
    ,'description' => __('Number of guests != (empty or 1)', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'guest_two_name'
        ,'label' => __('Name', 'piklist-demo')
      )
      ,array(
        'type' => 'radio'
        ,'field' => 'guest_two_meal'
        ,'label' => __('Meal choice', 'piklist-demo')
        ,'choices' => array(
          'chicken' => 'Chicken'
          ,'steak' => 'Steak'
          ,'vegetarian' => 'Vegetarian'
        )
      )
    )
    ,'conditions' => array(
      array(
        'field' => 'guests_number'
        ,'value' => array('', '0', '1')
        ,'compare' => '!='
      )
      ,array(
        'field' => 'attending'
        ,'value' => array('', 'no')
        ,'compare' => '!='
      )
    )
  ));   

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));   

?>