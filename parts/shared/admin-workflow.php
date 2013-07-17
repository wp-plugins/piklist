<?php if ($position == 'header'): ?> 
  
  <div class="wrap">

<?php endif; ?>

    <?php 
      foreach ($path as $data)
      {
        if (strtolower($data['config']['header']) == 'true')
        {
          piklist::render($data['part'], array(
            'data' => $data
          ));
        }
      }
    ?>

    <h2 class="nav-tab-wrapper">

      <?php foreach ($path as $data): ?>
    
        <?php if (strtolower($data['config']['header']) != 'true'): ?>
      
          <?php
            $saved = isset($post->ID);
            $data['config'] = array_filter($data['config']);
            $url_arguments = array(
              'flow' => $data['config']['flow_slug']
              ,'flow_page' => $data['config']['page_slug']
            );
        
            $url_arguments['post'] = isset($post->ID) ? $post->ID : (isset($_REQUEST['post']) ? (int) $_REQUEST['post'] : null);
        
            parse_str($_SERVER['QUERY_STRING'], $url_defaults);
        
            foreach (array('message', 'paged', 'updated') as $variable)
            {
              unset($url_defaults[$variable]);
            }
        
            $url = array_merge($url_defaults, $url_arguments);

            if (isset($data['config']['redirect']))
            {
              $data['config']['redirect'] = apply_filters('piklist_workflow_redirect_url', $data['config']['redirect'], $workflow, $data);
              $url = admin_url($data['config']['redirect'] . (strstr($data['config']['redirect'], '?') ? '&' : '?') . http_build_query(array_filter($url)));
            }
            else if (!isset($data['config']['disable']))
            {
              if ($url_arguments['post'])
              {
                unset($url['page']);
                $url['action'] = 'edit';
              }
          
              $url = admin_url($pagenow . '?' . http_build_query(array_filter($url)));
            }
            else
            {
              $url = false;
            }

          ?>
    
          <a class="nav-tab <?php echo isset($data['config']['active']) ? 'nav-tab-active' : null; ?>" <?php echo $url ? 'href="' . $url . '"' : null; ?>>
            <?php echo $data['config']['name']; ?>
          </a>
    
          <?php
            if (isset($data['config']['active']))
            {
              $active_data = $data;
            }
          ?>
      
        <?php endif; ?>
    
      <?php endforeach;?>
  
      <?php do_action('piklist_workflow_flow_append', $data['config']['flow_slug']); ?>
  
    </h2>

    <?php
      if ($active_data)
      {
        piklist::render($active_data['part'], array(
          'data' => $active_data
        ));
      }
    ?>

<?php if ($position == 'header'): ?> 
    
    <br />
    
  </div>

<?php endif; ?>