
<div class="wrap piklist-workflow <?php echo $position == 'header' ? 'piklist-workflow-position-header' : null; ?>">

  <?php 
    foreach ($workflows as $tab):
      if ($tab['data']['header']):
        
        piklist::render($tab['part'], array(
          'data' => $tab
        ));
    
      endif;
    endforeach;
    
    $tabs_to_render = 0;
    foreach ($workflows as $workflow):
      $tabs_to_render = !$workflow['data']['header'] ? $tabs_to_render + 1 : $tabs_to_render;
    endforeach;
    
    if ($tabs_to_render > 1 || ($tabs_to_render == 1 && isset($active_tab['parts']) && count($active_tab['parts']) > 1)):
  ?>
    
    <?php if ($layout == 'bar'): ?>
      
      <div class="wp-filter">
        
        <ul class="filter-links">
  
    <?php else: ?>
    
      <h2 class="nav-tab-wrapper piklist-workflow-tab-wrapper">

    <?php endif; ?>
    
        <?php 

          foreach ($workflows as $tab):
            if (!$tab['data']['header']):
              
              if ($layout == 'bar'):
                ?><li><a class="<?php echo $tab['data']['active'] ? 'current' : null; ?>" <?php echo $tab['url'] ? 'href="' . esc_url($tab['url']) . '"' : null; ?>><?php _e($tab['data']['title']); ?></a></li><?php
              else:
                ?><a class="nav-tab <?php echo $tab['data']['active'] ? 'nav-tab-active' : null; ?>" <?php echo $tab['url'] ? 'href="' . esc_url($tab['url']) . '"' : null; ?>><?php _e($tab['data']['title']); ?></a><?php
              endif;
              
              if (isset($tab['data']['active']) && $tab['data']['active']):
                $active_tab = $tab;
              endif;
          
            endif;
          endforeach;
        ?>

    <?php if ($layout == 'bar'): ?>
      
      	</ul>
    
        <?php do_action('piklist_workflow_flow_append', $tab['data']['flow_slug']); ?>
        
      </div>
  
    <?php else: ?>
      
        <?php do_action('piklist_workflow_flow_append', $tab['data']['flow_slug']); ?>
      
      </h2>

    <?php endif; ?>
    
    <?php if (isset($active_tab['parts'])): ?>

      <ul class="subsubsub piklist-workflow-subsubsub">
      
        <?php 
           $parts = $active_tab['parts']; 

           foreach ($parts as $order => $part):
          
            if ($part['data']['active']):
              $active_tab = $part;
            endif;
          ?>
        
          <li class="nav-tab-sub"><a <?php echo $part['url'] ? 'href="' . esc_url($part['url']) . '"' : null; ?> class="<?php echo $part['data']['active'] ? 'current' : null; ?>"><?php _e($part['data']['title']); ?></a> <?php echo $part === end($parts) ? null : '|'; ?></li>

        <?php endforeach; ?>
  
      </ul>
    
      <div class="clear"></div>
  
    <?php endif; ?>
  
  <?php endif; ?>
  
  <?php
    if (isset($active_tab)):
      do_action('piklist_pre_render_workflow', $active_tab);
      
      if ($active_tab['part']):
        piklist::render($active_tab['part'], array(
          'data' => $active_tab
        ));
      endif;
      
      do_action('piklist_post_render_workflow', $active_tab);
    endif;
  ?>

</div>