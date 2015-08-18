<?php
  $field = piklist('field', array(
             'type' => 'select'
             ,'scope' => false
             ,'field' => '_status'
             ,'choices' => $choices
             ,'return' => true
           ));
?>

<script type="text/javascript">
  
  (function($)
  {
    $(document).ready(function()
    {
      $('select[name="_status"]').replaceWith('<?php echo $field; ?>');
    });
  })(jQuery);

</script>