
<form 
  method="<?php echo strtolower($method); ?>" 
  action="<?php echo $filter ? home_url() . $action : null; ?>" 
  enctype="multipart/form-data"
  id="<?php echo $form_id; ?>"
  autocomplete="off"
>
  
  <?php

    do_action('piklist_notices', $form_id);
  
    piklist::render($form);
  
    piklist('field', array(
      'type' => 'hidden'
      ,'scope' => piklist::$prefix
      ,'field' => 'form_id'
      ,'value' => $form_id
    ));
    
    $field_ids = piklist_form::get('field_ids');
    
    if (!empty($field_ids))
    {
      foreach ($field_ids as $type => $id)
      {
        piklist('field', array(
          'type' => 'hidden'
          ,'scope' => $type
          ,'field' => (in_array($type, array('comment')) ? $type . '_' : '') . (in_array($type, array('taxonomy')) ? 'id' : 'ID')
          ,'value' => $id
        ));
      }
    }
    
    if ($filter)
    {
      piklist('field', array(
        'type' => 'hidden'
        ,'scope' => piklist::$prefix
        ,'field' => 'filter'
        ,'value' => 'true'
      ));
    }
    
    if (!empty($redirect))
    {
      piklist('field', array(
        'type' => 'hidden'
        ,'scope' => piklist::$prefix
        ,'field' => 'redirect'
        ,'value' => $redirect
      ));
    }
  ?>
  
  <?php piklist_form::save_fields(); ?>  
  
</form>