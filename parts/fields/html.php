<div class="piklist-field-part">

  <span
    <?php echo piklist_form::attributes_to_string($attributes); ?>
    id="<?php echo piklist_form::get_field_id($field, $scope, $index, $prefix); ?>" 
    name="<?php echo piklist_form::get_field_name($field, $scope, $index, $prefix); ?>" 
  ><?php echo is_array($value) ? implode($value, ' ') : $value; ?></span>

</div>