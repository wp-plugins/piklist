<?php
/*
Notice Type: updated
Capability: manage_options
Dismiss: true
Page: piklist_demo
Flow: All
Tab: All
*/
?>

	<p>

		<?php _e('Piklist makes it super simple to add admin notices that are dismissable.', 'piklist-demo'); ?>
	</p>

<?php

  piklist('shared/code-locater', array(
    'location' => __FILE__
    ,'type' => 'Admin notice'
  ));