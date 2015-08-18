<?php if ($position == 'header'): ?> 
  
  <div class="piklist-workflow">

<?php endif; ?>

    <?php 
      foreach ($workflows as $tab):
        if ($tab['data']['header']):
          
          piklist::render($tab['part'], array(
            'data' => $tab
          ));
      
        endif;
      endforeach;
    ?>

    <h2 class="nav-tab-wrapper">

      <?php 

        foreach ($workflows as $tab):
          if (!$tab['data']['header']):
            
            ?><a class="nav-tab <?php echo $tab['data']['active'] ? 'nav-tab-active' : null; ?>" <?php echo $tab['url'] ? 'href="' . esc_url($tab['url']) . '"' : null; ?>><?php _e($tab['data']['title']); ?></a><?php
        
            if (isset($tab['data']['active']) && $tab['data']['active']):
              $active_tab = $tab;
            endif;
            
          endif;
        endforeach;
      ?>
  
      <?php do_action('piklist_workflow_flow_append', $tab['data']['flow_slug']); ?>
  
    </h2>
    
    <?php if (isset($active_tab['parts'])): ?>

      <ul class="subsubsub">
        
        <?php 
           $parts = $active_tab['parts']; 

           foreach ($parts as $order => $part):
            
            if ($part['data']['active']):
              $active_tab = $part;
            endif;
          ?>
          
          <li class="nav-tab-sub-<?php echo piklist::dashes($part['data']['page_slug']); ?>"><a <?php echo $part['url'] ? 'href="' . esc_url($part['url']) . '"' : null; ?> class="<?php echo $part['data']['active'] ? 'current' : null; ?>"><?php _e($part['data']['title']); ?></a> <?php echo $part === end($parts) ? null : '|'; ?></li>

        <?php endforeach; ?>
    
      </ul>
      
      <br class="clear"/>
    
    <?php endif; ?>
    
    <?php
      if (isset($active_tab)):
        do_action('piklist_pre_render_workflow', $active_tab);
        
        piklist::render($active_tab['part'], array(
          'data' => $active_tab
        ));

        do_action('piklist_post_render_workflow', $active_tab);
      endif;
    ?>

<?php if ($position == 'header'): ?> 
    
  </div>

<?php endif; ?>




