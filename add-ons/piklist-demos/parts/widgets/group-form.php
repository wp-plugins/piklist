<?php

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'address_group'
    ,'label' => __('Address (Grouped)', 'piklist-demo')
    ,'list' => false
    ,'description' => __('A grouped field. Data is not searchable, since it is saved in an array.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'address_1'
        ,'columns' => 12
        ,'attributes' => array(
          'placeholder' => __('Street Address', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'address_2'
        ,'columns' => 12
        ,'attributes' => array(
          'placeholder' => __('PO Box, Suite, etc.', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'city'
        ,'columns' => 5
        ,'attributes' => array(
          'placeholder' => __('City', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'select'
        ,'field' => 'state'
        ,'columns' => 4
        ,'choices' => array(
          'AL' => 'AL'
          ,'AK' => 'AK'  
          ,'AZ' => 'AZ'  
          ,'AR' => 'AR'  
          ,'CA' => 'CA'  
          ,'CO' => 'CO'  
          ,'CT' => 'CT'  
          ,'DE' => 'DE'  
          ,'DC' => 'DC'  
          ,'FL' => 'FL'  
          ,'GA' => 'GA'  
          ,'HI' => 'HI'  
          ,'ID' => 'ID'  
          ,'IL' => 'IL'  
          ,'IN' => 'IN'  
          ,'IA' => 'IA'  
          ,'KS' => 'KS'  
          ,'KY' => 'KY'  
          ,'LA' => 'LA'  
          ,'ME' => 'ME'  
          ,'MD' => 'MD'  
          ,'MA' => 'MA'  
          ,'MI' => 'MI'
          ,'MN' => 'MN'  
          ,'MS' => 'MS'  
          ,'MO' => 'MO'  
          ,'MT' => 'MT'
          ,'NE' => 'NE'
          ,'NV' => 'NV'
          ,'NH' => 'NH'
          ,'NJ' => 'NJ'
          ,'NM' => 'NM'
          ,'NY' => 'NY'
          ,'NC' => 'NC'
          ,'ND' => 'ND'
          ,'OH' => 'OH'  
          ,'OK' => 'OK'  
          ,'OR' => 'OR'  
          ,'PA' => 'PA'  
          ,'RI' => 'RI'  
          ,'SC' => 'SC'  
          ,'SD' => 'SD'
          ,'TN' => 'TN'  
          ,'TX' => 'TX'  
          ,'UT' => 'UT'  
          ,'VT' => 'VT'  
          ,'VA' => 'VA'  
          ,'WA' => 'WA'  
          ,'WV' => 'WV'  
          ,'WI' => 'WI'  
          ,'WY' => 'WY'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'postal_code'
        ,'columns' => 3
        ,'attributes' => array(
          'placeholder' => __('Postal Code', 'piklist-demo')
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'address_group_add_more'
    ,'add_more' => true
    ,'label' => __('Address (Grouped/Add-More)', 'piklist-demo')
    ,'description' => __('A grouped field using Add-More. No fields labels.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'address_1'
        ,'label' => __('Street Address', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'address_2'
        ,'label' => __('PO Box, Suite, etc.', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'city'
        ,'label' => __('City', 'piklist-demo')
        ,'columns' => 5
      )
      ,array(
        'type' => 'select'
        ,'field' => 'state'
        ,'label' => __('State', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => array(
          'AL' => 'AL'
          ,'AK' => 'AK'  
          ,'AZ' => 'AZ'  
          ,'AR' => 'AR'  
          ,'CA' => 'CA'  
          ,'CO' => 'CO'  
          ,'CT' => 'CT'  
          ,'DE' => 'DE'  
          ,'DC' => 'DC'  
          ,'FL' => 'FL'  
          ,'GA' => 'GA'  
          ,'HI' => 'HI'  
          ,'ID' => 'ID'  
          ,'IL' => 'IL'  
          ,'IN' => 'IN'  
          ,'IA' => 'IA'  
          ,'KS' => 'KS'  
          ,'KY' => 'KY'  
          ,'LA' => 'LA'  
          ,'ME' => 'ME'  
          ,'MD' => 'MD'  
          ,'MA' => 'MA'  
          ,'MI' => 'MI'
          ,'MN' => 'MN'  
          ,'MS' => 'MS'  
          ,'MO' => 'MO'  
          ,'MT' => 'MT'
          ,'NE' => 'NE'
          ,'NV' => 'NV'
          ,'NH' => 'NH'
          ,'NJ' => 'NJ'
          ,'NM' => 'NM'
          ,'NY' => 'NY'
          ,'NC' => 'NC'
          ,'ND' => 'ND'
          ,'OH' => 'OH'  
          ,'OK' => 'OK'  
          ,'OR' => 'OR'  
          ,'PA' => 'PA'  
          ,'RI' => 'RI'  
          ,'SC' => 'SC'  
          ,'SD' => 'SD'
          ,'TN' => 'TN'  
          ,'TX' => 'TX'  
          ,'UT' => 'UT'  
          ,'VT' => 'VT'  
          ,'VA' => 'VA'  
          ,'WA' => 'WA'  
          ,'WV' => 'WV'  
          ,'WI' => 'WI'  
          ,'WY' => 'WY'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'postal_code'
        ,'label' => __('Postal Code', 'piklist-demo')
        ,'columns' => 3
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Address (Un-Grouped)', 'piklist-demo')
    ,'description' => __('An Un-grouped field. Data is saved as individual meta and is searchable.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'ungrouped_address_1'
        ,'label' => __('Street Address', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'ungrouped_address_2'
        ,'label' => __('PO Box, Suite, etc.', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'ungrouped_city'
        ,'label' => __('City', 'piklist-demo')
        ,'columns' => 5
      )
      ,array(
        'type' => 'select'
        ,'field' => 'ungrouped_state'
        ,'label' => __('State', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => array(
           'AL' => 'AL'
          ,'AK' => 'AK'  
          ,'AZ' => 'AZ'  
          ,'AR' => 'AR'  
          ,'CA' => 'CA'  
          ,'CO' => 'CO'  
          ,'CT' => 'CT'  
          ,'DE' => 'DE'  
          ,'DC' => 'DC'  
          ,'FL' => 'FL'  
          ,'GA' => 'GA'  
          ,'HI' => 'HI'  
          ,'ID' => 'ID'  
          ,'IL' => 'IL'  
          ,'IN' => 'IN'  
          ,'IA' => 'IA'  
          ,'KS' => 'KS'  
          ,'KY' => 'KY'  
          ,'LA' => 'LA'  
          ,'ME' => 'ME'  
          ,'MD' => 'MD'  
          ,'MA' => 'MA'  
          ,'MI' => 'MI'
          ,'MN' => 'MN'  
          ,'MS' => 'MS'  
          ,'MO' => 'MO'  
          ,'MT' => 'MT'
          ,'NE' => 'NE'
          ,'NV' => 'NV'
          ,'NH' => 'NH'
          ,'NJ' => 'NJ'
          ,'NM' => 'NM'
          ,'NY' => 'NY'
          ,'NC' => 'NC'
          ,'ND' => 'ND'
          ,'OH' => 'OH'  
          ,'OK' => 'OK'  
          ,'OR' => 'OR'  
          ,'PA' => 'PA'  
          ,'RI' => 'RI'  
          ,'SC' => 'SC'  
          ,'SD' => 'SD'
          ,'TN' => 'TN'  
          ,'TX' => 'TX'  
          ,'UT' => 'UT'  
          ,'VT' => 'VT'  
          ,'VA' => 'VA'  
          ,'WA' => 'WA'  
          ,'WV' => 'WV'  
          ,'WI' => 'WI'  
          ,'WY' => 'WY'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'ungrouped_postal_code'
        ,'label' => __('Postal Code', 'piklist-demo')
        ,'columns' => 3
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'label' => __('Address (Un-Grouped/Add-More)', 'piklist-demo')
    ,'add_more' => true
    ,'description' => __('An Un-grouped field. Data is saved as individual meta and is searchable.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'ungrouped_address_1_addmore'
        ,'label' => __('Street Address', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'ungrouped_address_2_addmore'
        ,'label' => __('PO Box, Suite, etc.', 'piklist-demo')
        ,'columns' => 12
      )
      ,array(
        'type' => 'text'
        ,'field' => 'ungrouped_city_addmore'
        ,'label' => __('City', 'piklist-demo')
        ,'columns' => 5
      )
      ,array(
        'type' => 'select'
        ,'field' => 'ungrouped_state_addmore'
        ,'label' => __('State', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => array(
          'AL' => 'AL'
          ,'AK' => 'AK'  
          ,'AZ' => 'AZ'  
          ,'AR' => 'AR'  
          ,'CA' => 'CA'  
          ,'CO' => 'CO'  
          ,'CT' => 'CT'  
          ,'DE' => 'DE'  
          ,'DC' => 'DC'  
          ,'FL' => 'FL'  
          ,'GA' => 'GA'  
          ,'HI' => 'HI'  
          ,'ID' => 'ID'  
          ,'IL' => 'IL'  
          ,'IN' => 'IN'  
          ,'IA' => 'IA'  
          ,'KS' => 'KS'  
          ,'KY' => 'KY'  
          ,'LA' => 'LA'  
          ,'ME' => 'ME'  
          ,'MD' => 'MD'  
          ,'MA' => 'MA'  
          ,'MI' => 'MI'
          ,'MN' => 'MN'  
          ,'MS' => 'MS'  
          ,'MO' => 'MO'  
          ,'MT' => 'MT'
          ,'NE' => 'NE'
          ,'NV' => 'NV'
          ,'NH' => 'NH'
          ,'NJ' => 'NJ'
          ,'NM' => 'NM'
          ,'NY' => 'NY'
          ,'NC' => 'NC'
          ,'ND' => 'ND'
          ,'OH' => 'OH'  
          ,'OK' => 'OK'  
          ,'OR' => 'OR'  
          ,'PA' => 'PA'  
          ,'RI' => 'RI'  
          ,'SC' => 'SC'  
          ,'SD' => 'SD'
          ,'TN' => 'TN'  
          ,'TX' => 'TX'  
          ,'UT' => 'UT'  
          ,'VT' => 'VT'  
          ,'VA' => 'VA'  
          ,'WA' => 'WA'  
          ,'WV' => 'WV'  
          ,'WI' => 'WI'  
          ,'WY' => 'WY'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'ungrouped_postal_code_addmore'
        ,'label' => __('Postal Code', 'piklist-demo')
        ,'columns' => 3
      )
    )

   ));

   piklist('field', array(
    'type' => 'group'
    ,'field' => 'editor_test_one'
    ,'label' => __('Editor test 1 with Addmore', 'piklist-demo')
    ,'add_more' => true
    ,'description' => __('A grouped/addmore field test with Editor.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'checkbox'
        ,'field' => 'editor_test_one_checkbox'
        ,'label' => __('Checkbox', 'piklist-demo')
        ,'columns' => 12
        ,'choices' => array(
          'first' => __('First Choice', 'piklist-demo')
          ,'second' => __('Second Choice', 'piklist-demo')
          ,'third' => __('Third Choice', 'piklist-demo')
        )
      )
      ,array(
        'type' => 'editor'
        ,'field' => 'editor_test_one_editor'
        ,'columns' => 12
        ,'label' => __('Post Content', 'piklist-demo')
        ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        ,'options' => array (
          'wpautop' => true
          ,'media_buttons' => true
          ,'tabindex' => ''
          ,'editor_css' => ''
          ,'editor_class' => ''
          ,'teeny' => false
          ,'dfw' => false
          ,'tinymce' => true
          ,'quicktags' => true
        )
      )
    )
  ));

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'editor_test_two'
    ,'label' => __('Editor test 2 with Addmore', 'piklist-demo')
    ,'add_more' => true
    ,'description' => __('A grouped/addmore field test with Editor.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'editor'
        ,'field' => 'editor_test_two_editor'
        ,'columns' => 12
        ,'label' => __('Post Content', 'piklist-demo')
        ,'description' => __('This is the standard post box, now placed in a Piklist WorkFlow.', 'piklist-demo')
        ,'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        ,'options' => array (
          'wpautop' => true
          ,'media_buttons' => true
          ,'tabindex' => ''
          ,'editor_css' => ''
          ,'editor_class' => ''
          ,'teeny' => false
          ,'dfw' => false
          ,'tinymce' => true
          ,'quicktags' => true
        )
      )
      ,array(
        'type' => 'checkbox'
        ,'field' => 'editor_test_two_checkbox'
        ,'label' => __('Checkbox', 'piklist-demo')
        ,'columns' => 12
        ,'choices' => array(
          'first' => __('First Choice', 'piklist-demo')
          ,'second' => __('Second Choice', 'piklist-demo')
          ,'third' => __('Third Choice', 'piklist-demo')
        )
      )
    )
  ));

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Widget'
  ));