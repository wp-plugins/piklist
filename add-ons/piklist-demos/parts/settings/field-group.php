<?php
/*
Title: Groups
Order: 80
Tab: Layout
Sub Tab: Field Groups
Setting: piklist_demo_fields
Flow: Demo Workflow
*/

  $states = array(
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
  );

  piklist('field', array(
    'type' => 'group'
    ,'field' => 'address_group'
    ,'label' => __('Address (Grouped)', 'piklist-demo')
    ,'list' => false
    ,'description' => __('A grouped field with a key set. Data is not searchable, since it is saved in an array.', 'piklist-demo')
    ,'fields' => array(
      array(
        'type' => 'text'
        ,'field' => 'address_1'
        ,'label' => __('Street Address', 'piklist-demo')
        ,'required' => true
        ,'columns' => 12
        ,'attributes' => array(
          'placeholder' => 'Street Address'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'address_2'
        ,'label' => __('PO Box, Suite, etc.', 'piklist-demo')
        ,'columns' => 12
        ,'attributes' => array(
          'placeholder' => 'PO Box, Suite, etc.'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'city'
        ,'label' => __('City', 'piklist-demo')
        ,'columns' => 5
        ,'attributes' => array(
          'placeholder' => 'City'
        )
      )
      ,array(
        'type' => 'select'
        ,'field' => 'state'
        ,'label' => __('State', 'piklist-demo')
        ,'columns' => 4
        ,'choices' => $states
      )
      ,array(
        'type' => 'text'
        ,'field' => 'postal_code'
        ,'label' => __('Postal Code', 'piklist-demo')
        ,'columns' => 3
        ,'attributes' => array(
          'placeholder' => 'Postal Code'
        )
      )
      ,array(
        'type' => 'text'
        ,'field' => 'phone'
        ,'label' => __('Phone', 'piklist-demo')
        ,'template' => 'post_meta'
        ,'columns' => 12
      )
    )
  ));
  
  piklist('field', array(
    'type' => 'group'
    ,'field' => 'address_group_add_more'
    ,'add_more' => true
    ,'label' => __('Address (Grouped/Add-More)', 'piklist-demo')
    ,'description' => __('A grouped field using Add-More.', 'piklist-demo')
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
        ,'choices' => $states
      )
      ,array(
        'type' => 'text'
        ,'field' => 'postal_code'
        ,'label' => __('Postal Code', 'piklist-demo')
        ,'columns' => 3
      )
      ,array(
        'type' => 'text'
        ,'field' => 'phone'
        ,'label' => __('Phone', 'piklist-demo')
        ,'template' => 'post_meta'
        ,'columns' => 12
      )
    )
  ));
  
  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Settings Section'
  ));