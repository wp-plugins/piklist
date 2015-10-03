<?php 

  wp_editor(
    isset($value) && !empty($value) ? stripslashes($value) : ''
    ,piklist_form::get_field_id($arguments)
    ,array_merge(
      array(
        'textarea_name' => piklist_form::get_field_name($arguments)
        ,'editor_height' => 180
        ,'quicktags' => true
        ,'textarea_rows' => 5
      )
      ,isset($options) && is_array($options) ? $options : array()
    )
  );
