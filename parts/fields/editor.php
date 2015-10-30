<?php 

  $options = array_merge(
               array(
                 'textarea_name' => piklist_form::get_field_name($arguments)
                 ,'editor_height' => 180
                 ,'quicktags' => false
                 ,'teeny' => true
                 ,'media_buttons' => false
                 ,'textarea_rows' => 5
                 ,'editor_class' => implode(' ', $attributes['class'])
               )
               ,isset($options) && is_array($options) ? $options : array()
             );

  if (!empty($attributes['class'])):
    
    $options['editor_class'] .= ' ' . implode(' ', $attributes['class']);
  
  endif;
  
  wp_editor(
    isset($value) && !empty($value) ? stripslashes(is_array($value) ? end($value) : $value) : ''
    ,piklist_form::get_field_id($arguments)
    ,$options
  );