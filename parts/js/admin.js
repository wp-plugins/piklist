/* --------------------------------------------------------------------------------
  Updates or enhancements to Piklist Functionality
--------------------------------------------------------------------------------- */

;(function($, window, document, undefined) 
{
  'use strict';

  $(document).ready(function()
  {  
    $('body')
      .wptabs();
      
    piklist_admin.init();
  });
  
  var piklist_admin = {
  
    init: function()
    {
      piklist_admin.meta_boxes();
      piklist_admin.post_name();
      piklist_admin.post_submit_meta_box();
      piklist_admin.thickbox();
      piklist_admin.user_forms();
      piklist_admin.empty_elements();
      piklist_admin.list_tables();
      piklist_admin.widgets();
      piklist_admin.shortcodes();
      piklist_admin.notices();
      piklist_admin.pointers();
    },
    
    notices: function()
    {
      $(document).on('click', '.notice.is-dismissible > .notice-dismiss', function(event)
      {
        $.ajax({
          type: 'POST',
          url: ajaxurl,
          dataType: 'json',
          data: {
            action: 'piklist_notice',
            id: $(this).parent().attr('id')
          }
        });
      });
    },
    
    pointers: function()
    {
      if (typeof $.pointers != 'undefined')
      {
        $.each(piklist.pointers, function(index) 
        {
          var pointer_id = this.pointer_id,
            options = $.extend(this.options, {
              close: function() 
              {
                $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  dataType: 'json',
                  data: {
                    action: 'dismiss-wp-pointer',
                    pointer: pointer_id
                  }
                });
              }
            });
        
          $(this.target)
            .pointer(options)
            .pointer('open');
        });
      }
    },
    
    empty_elements: function()
    {
      $('#post-body-content').each(function()
      {
        if ($.trim($(this).html()) == '')
        {
          $(this).html('');
        }
      });
    },

    user_forms: function()
    {
      if ('WebkitAppearance' in document.documentElement.style) 
      {
        setTimeout(function() 
        {
          $('input:-webkit-autofill').each(function()
          {
            var name = $(this).attr('name');

            $(this).after(this.outerHTML).remove();
            $('input[name=' + name + ']').val('');
          });
        }, 250);
      }
    },

    thickbox: function()
    {
      $('.piklist-list-table-export-button').on('click', function() 
      {
        setTimeout(function() 
        {
          var TB_WIDTH = 870,
            TB_HEIGHT = 800; 

          $('#TB_window').css({
            marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px'
            ,width: TB_WIDTH + 'px'
            ,height: TB_HEIGHT + 'px'
            ,marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px'
          });
          
          $('#TB_ajaxContent').css({
            height: TB_HEIGHT - 45 + 'px'
          })
        }, 100)
      });
    },

    meta_boxes: function()
    {
      $('.piklist-meta-box-collapse:not(.piklist-meta-box-lock)').addClass('closed');
      
      $('.piklist-meta-box-lock')
        .addClass('stuffbox')
        .css('box-shadow', 'none')
        .find('div.handlediv')
          .hide()
          .next('h3.hndle')
            .removeClass('hndle')
            .css('cursor', 'default');

      $('.piklist-meta-box-lock').show();
      
      $('.piklist-meta-box > .inside').each(function()
      {
        if ($(this).find(' > *:first-child').hasClass('piklist-field-container'))
        {
          $(this).css({
            'margin-top': '0'
          });
        }
      });
    },
    
    post_name: function()
    {
      var form = $('body.wp-admin.post-php form#post:first');
      
      if (form.length > 0)
      {
        var slug = form.find(':input#post_name');
        
        if (slug.length <= 0)
        {
          form.append($('<input type="hidden" name="post_name" id="post_name">'));
        }
      }
    },
    
    post_submit_meta_box: function()
    {
      $('.save-post-status', '#post-status-select').click(function(event) 
      {
        event.preventDefault();
        
        var status = $('#post_status').val(),
          text = $('#post_status option:selected').text();
        
        if (status != 'draft')
        {
          $('#save-post').val('Save');
          
          // TODO: Progress post status' these need to be updated to the next status!
          $('#hidden_post_status, #original_publish').val(text);
          $('#publish').val('Update');
        }
        
        $('#post-status-display').text(text);
      });
      
      $('#publish', '#major-publishing-actions').click(function()
      {
        if ($('#post-status-select').css('display') != 'none')
        {
          $('.save-post-status', '#post-status-select').trigger('click');
        }
        
        if ($('#post-visibility-select').css('display') != 'none')
        {
          $('.save-post-visibility', '#post-visibility-select').trigger('click');
        }
      });
    },
    
    shortcodes: function()
    {
      $.each(piklist.shortcodes, function(index, shortcode) 
      {
        if (wp.mce.views)
        {
          var tag = typeof shortcode.shortcode != 'undefined' ? shortcode.shortcode : shortcode;

          wp.mce.views.register(tag, piklist_admin.shortcode(tag));
        }
      });
      
      $(window).on('resize', function()
      {
        var thickbox = $('#TB_iframeContent');
    
        if (thickbox.length > 0 && thickbox.attr('src').indexOf('page=shortcode_editor'))
        {
          var width = Math.round($(window).width() - 60),
            height = Math.round($(window).height() - 60);

          $('#TB_window')
            .css({
              'width': width,
              'height': height,
              'top': '50%',
              'margin-left': -width / 2,
              'margin-top': -height / 2
            })
            .find('iframe')
              .css({
                'width': '100%',
                'height': height - $('#TB_title').height()
              });
        }
      });
      
      $(document).on('click', '.piklist-shortcode-button', function(event)
      {
        event.preventDefault(); 
        
        var title = 'Insert Shortcode',
          editor = typeof parent.tinymce.activeEditor != 'undefined' ? parent.tinymce.activeEditor : false,
          frame_in_frame = window.location != window.parent.location,
          content = editor && !frame_in_frame ? editor.selection.getContent({format : 'html'}) : null,
          url = location.href,
          editor_url = url.substr(0, url.indexOf('/wp-')) + '/wp-admin/admin.php',
          attributes = {
            page: 'shortcode_editor'
          };

        if (typeof pagenow != 'undefined' && $.inArray(pagenow, ['post', 'post-new']) > -1)
        {
          attributes[piklist.prefix + 'post[ID]'] = $('#post_ID').val();
        }
        
        attributes[piklist.prefix + '[admin_hide_ui]'] = 'true';
        
        if (content && !frame_in_frame)
        {
          attributes[piklist.prefix + 'shortcode_data[content][]'] = content;
        }
        
        attributes['TB_iframe'] = 'true';
        
        tb_show(title, editor_url + '?' + $.param(attributes));

        $(window, parent).trigger('resize');
      });
      
      if ($('form#_shortcode').length > 0)
      {
        if ($('div#piklist_form_admin_notice.update').length > 0)
        {
          var data = $(':input[name^="' + piklist.prefix + 'shortcode_data["]').serializeArray(),
            shortcode = {
              attrs: {},
              type: 'single'
            },
            attribute = null,
            attribute_length,
            attribute_string = '',
            output = '';
          
          for (var i = 0; i < data.length; i++)
          {
            attribute = data[i].name.replace(piklist.prefix + 'shortcode_data[', '').replace(/[\[\]']+/g, '');

            if (attribute == 'content')
            {
              shortcode.content = parent.switchEditors._wp_Nop(data[i].value);

              shortcode.type = 'closed';
            }
            else if (attribute == 'name')
            {
              shortcode.tag = data[i].value;
            }
          }
        
          data = $(':input[name^="' + piklist.prefix + 'shortcode["]').serializeArray();

          for (var i = 0; i < data.length; i++)
          {
            attribute_length = (piklist.prefix + 'shortcode').length + 1;
            attribute = data[i].name.substr(attribute_length, data[i].name.indexOf(']') - attribute_length); 

            if (attribute.toLowerCase() != 'id')
            {
              attribute_string += attribute + '="' + data[i].value + '" ';
            }
          }

          shortcode.attrs = wp.shortcode.attrs(attribute_string);
          
          output = wp.shortcode.string(shortcode);
          
          var _output = $(document).triggerHandler('piklist:shortcode:insert', [output, shortcode]);
          if (typeof _output != 'undefined')
          {
            output = _output;
          }
          
          parent.send_to_editor(output);

          // NOTE: In order to make sure nested shortcodes are rendered properly we have to toggle the views
          parent.switchEditors.go(parent.tinymce.activeEditor.id, 'html');
          parent.switchEditors.go(parent.tinymce.activeEditor.id, 'tmce');

          parent.tb_remove();
        }
        else
        {
          $('ul.piklist-shortcodes > .attachment').on('click', function(event)
          {
            $('input[name="' + piklist.prefix + 'shortcode_data[name][]"]').val($(this).attr('data-piklist-shortcode'));
          
            var data = $(':input[name^="' + piklist.prefix + 'shortcode_data["]').serializeArray(),
              post_id = typeof pagenow != 'undefined' && $.inArray(pagenow, ['post', 'post-new']) > -1 ? '&' + piklist.prefix + 'post[ID]=' + $('#post_ID').val() : null,
              attributes = {};
          
            $.each(data, function(key, value) 
            {
              attributes[value['name']] = value['value'];
            });
            
            location.href = location.href + (location.href.indexOf('?') > -1 ? '&' : '?') + $.param(attributes) + (post_id ? post_id : '');
          });
        }
      }
    },
    
    shortcode: function(shortcode)
    {
      return {
        template: wp.media.template('piklist-shortcode'),
        
        getContent: function()
        {
          piklist.shortcodes[this.shortcode.tag].preview = true;
          if (piklist.shortcodes[this.shortcode.tag].preview === true)
          {
            var is_IE = typeof tinymce != 'undefined' ? tinymce.Env.ie : false,
              preview = $('<iframe/>'),
              id = 'piklist-shortcode-preview-' + this.shortcode.tag + '-' + Math.random().toString(36).substr(2, 16);

            preview
              .attr('id', id)
              .attr('src', is_IE ? 'javascript:""' : '')
              .attr('frameBorder', '0')
              .attr('allowTransparency', 'true')
              .attr('scrolling', 'no')
              .addClass('piklist-shortcode-preview')
              .css({
                'width': '100%',
                'height': '1',
                'display': 'block'
              });

            $.ajax({
              type: 'POST',
              url: ajaxurl,
              dataType: 'json',
              data: {
                action: 'piklist_shortcode',
                shortcode: wp.shortcode.string(this.shortcode),
                post_id: $('#post_ID').val(),
                preview_id: id
              },
              success: function(response)
              {
                $('.wp-editor-wrap iframe').each(function()
                {
                  var preview = $(this).contents().find('iframe#' + response.data.preview_id);

                  if (preview.length > 0)
                  {
                    var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
                      head = preview.contents().find('head'),
                      body = preview.contents().find('body'),
                      resize = function()
                      {
                        preview.height(preview.contents().find('html').outerHeight());
                      };
                    
                    $(this).contents().find('head').find('link').each(function()
                    {
                      head.append($(this).prop('outerHTML'));
                    });
                    
                    body
                      .css('min-height', '100%')
                      .html(response.data.html);
                    
                    resize();

                    if (MutationObserver)
                    {
                      var observer = new MutationObserver(function()
                      {
                        resize();
                        preview.contents().find('img,link').load(resize);
                      });

                      observer.observe(preview.contents()[0], {
                        attributes: true,
                        childList: true,
                        subtree: true
                      });
                    }
                    else
                    {
                      for (var i = 1; i < 6; i++)
                      {
                        setTimeout(resize, i * 700);
                      }
                    }
                  }
                });
              }
            });

            return preview.prop('outerHTML');
          }

          return this.template({
            tag: this.shortcode.tag,
            attributes: this.shortcode.attrs.named,
            options: typeof piklist.shortcodes[this.shortcode.tag] != 'undefined' ? piklist.shortcodes[this.shortcode.tag] : {
              name: this.shortcode.tag.replace(/_/g, ' ').toUpperCase()
            }
          });
        },
        
        ui: function()
        {
          
          return this.template({
            tag: this.shortcode.tag,
            attributes: this.shortcode.attrs.named,
            options: typeof piklist.shortcodes[this.shortcode.tag] != 'undefined' ? piklist.shortcodes[this.shortcode.tag] : {
              name: this.shortcode.tag.replace(/_/g, ' ').toUpperCase()
            }
          });
        },
        
        edit: function(string) 
        {
          if (typeof string === 'object') 
          {
            string = decodeURIComponent($(string).attr('data-wpview-text'));
          }

          var shortcode = {},
            regex = wp.shortcode.regexp(this.type),
            match = regex.exec(string);
            
          regex.lastIndex = 0;
          
          if (match)
          {
            shortcode = {
              attrs: wp.shortcode.attrs(match[3]),
              tag: this.type,
              content: match[5],
              type: typeof match[6] != 'undefined' ? 'closed' : 'single'
            };

            var editor_url = location.href.substr(0, location.href.indexOf('/wp-')) + '/wp-admin/admin.php',
              attributes = {
                'page': 'shortcode_editor'
              };

            if (typeof pagenow != 'undefined' && $.inArray(pagenow, ['post', 'post-new']) > -1)
            {
              attributes[piklist.prefix + 'post[ID]'] = $('#post_ID').val();
            }

            attributes[piklist.prefix + '[admin_hide_ui]'] = 'true';
            
            $.each(shortcode.attrs.named, function(key, value)
            {
              attributes[piklist.prefix + 'shortcode[' + key + ']'] = value;
            });

            attributes[piklist.prefix + 'shortcode_data[name][]'] = shortcode.tag;
            attributes[piklist.prefix + 'shortcode_data[action][]'] = 'update';

            if (typeof shortcode.content != 'undefined')
            {
              attributes[piklist.prefix + 'shortcode_data[content][]'] = switchEditors._wp_Nop(shortcode.content);
            }

            attributes['TB_iframe'] = 'true';

            tb_show('Edit ' + (piklist.shortcodes[this.type].name ? piklist.shortcodes[this.type].name : null), editor_url + '?' + $.param(attributes));
            
            $(window).trigger('resize');
          }
        }
      };
    },
    
    widgets: function()
    {
      $(document).on('mousedown', '.widget input[name="savewidget"]', function()
      {
        var button = $(this),
          widget_container = button.parents('.widget-control-actions:first').siblings('.widget-content:first'),
          widget_title = button.parents('.widget').find('.widget-title h4'),
          title = button.parents('form').find('.piklist-universal-widget-form-container').attr('data-widget-title');

        if (typeof tinyMCE != 'undefined')
        {
          tinyMCE.triggerSave();

          widget_container.find('.wp-editor-area').each(function()
          {
            if (typeof switchEditors != 'undefined')
            {
              switchEditors.go($(this).attr('id'), 'tmce');
            }
          });
        }
        
        $('.piklist-universal-widget-form-container').on('remove', function() 
        {
          widget_container
            .css({
              'height': widget_container.outerHeight(),
              'overflow': 'hidden'
            });

          widget_container
            .removeData('wptabs')
            .removeData('piklistgroups')
            .removeData('piklistcolumns')
            .removeData('piklistmediaupload')
            .removeData('piklistaddmore')
            .removeData('piklistfields');
              
          setTimeout(function()
          {
            widget_container
              .find('.piklist-universal-widget-form-container')
              .wptabs()
              .piklistgroups()
              .piklistcolumns()
              .piklistmediaupload()
              .piklistaddmore({
                sortable: true
              })
              .piklistfields();
            
            if (typeof title != 'undefined')
            {
              widget_title
                .find('.in-widget-title')
                .text(':  ' + title);
            }
            
            widget_container
              .css({
                'height': 'auto',
                'overflow': 'visible'
              });
          }, 50);
        });
      });
              
      piklist_admin.widget_title();
      
      var current_widget = null;
      
      $(document).on('change', '.piklist-universal-widget-select', function()
      {
        var widget = $(this).val(),
          addon = $(this).attr('data-piklist-addon'),
          action = ('piklist-universal-widget-' + addon).replace(/-/g, '_'),
          widget_container = $(this).parents('.widget-content'),
          widget_classes = $(this).attr('class').split(' '),
          widget_form = widget_container.find('.piklist-universal-widget-form-container'),
          widget_number = $(this).attr('name').split('[')[1].replace(/\]/g, ''),
          widget_title = $(this).parents('.widget').find('.widget-title h4'),
          widget_description = widget_container.find('.piklist-universal-widget-select-container p'),
          wptab_active = widget_container.attr('data-piklist-wptab-active');
        
        current_widget = widget_container.parents('.widget-inside:eq(0)');
          
        if (widget)
        {
          widget_form
            .hide()
            .empty();

          $.ajax({
            type : 'POST',
            url : ajaxurl,
            async: false,
            dataType: 'json',
            data: {
              action: action,
              widget: widget,
              number: widget_number
            }
            ,success: function(response) 
            {           
              if (response.tiny_mce != '' && response.quicktags != '')
              {
                tinyMCEPreInit.mceInit = $.extend(tinyMCEPreInit.mceInit, response.tiny_mce);
                tinyMCEPreInit.qtInit = $.extend(tinyMCEPreInit.qtInit, response.quicktags);
              }

              widget_title
                .find('.in-widget-title')
                .text(':  ' + response.widget.data.title)
              
              widget_description.text(response.widget.data.description);

              widget_form
                .removeData('wptabs')
                .removeData('piklistgroups')
                .removeData('piklistcolumns')
                .removeData('piklistmediaupload')
                .removeData('piklistaddmore')
                .removeData('piklistfields');

              widget_form
                .html(response.form)
                .wptabs()
                .piklistgroups()
                .piklistcolumns()
                .piklistmediaupload()
                .piklistaddmore({
                  sortable: true
                })
                .piklistfields();
            
              widget_container
                .find('.wp-tab-bar > li')
                .removeClass('wp-tab-active');
            
              widget_container
                .find('.wp-tab-bar > li:first')
                .addClass('wp-tab-active');
                
              if (widget_container.find('.wp-tab-bar').length > 0 && typeof wptab_active != 'undefined')
              {
                widget_container
                  .find('.wp-tab-bar > li')
                  .removeClass('wp-tab-active')
                  .get(2)
                  .addClass('wp-tab-active');
              }
            
              piklist_admin.widget_dimensions(widget_container, response.widget.data.height, response.widget.data.width);
            }
          });
        }
      });
      
      $('.wp-tab-bar li a').on('click', function(event)
      {
        var widget_container = $(this).parents('.widget-content:first');
        
        if (widget_container.length > 0)
        {
          widget_container.attr('data-piklist-wptab-active', $(this).text());
        }
      });
      
      piklist_admin.widget_title();
    },
    
    widget_title: function()
    {
      setTimeout(function()
      {
        $('.piklist-universal-widget-form-container').each(function()
        {
          var widget_container = $(this).parents('.widget-content'),
            widget_title = $(this).parents('.widget').find('.widget-title h4'),
            title = $(this).attr('data-widget-title'),
            height = $(this).attr('data-widget-height'),
            width = $(this).attr('data-widget-width');
        
          if (typeof title != 'undefined')
          {
            widget_title
              .find('.in-widget-title')
              .text(':  ' + title);
          }
        
          piklist_admin.widget_dimensions(widget_container, height, width);
        });
      }, 250);
    },
    
    widget_dimensions: function(widget, height, width)
    {
      var container = widget.parents('.widget:first'),
        inside = container.find('.widget-inside'),
        toggle = container.find('.widget-action:first'),
        toggled = false;

      if (inside.is(':visible'))
      {
        toggle.trigger('click');
        
        toggled = true;
      }
      
      widget
        .siblings('input[name="widget-width"]')
        .val(width ? width : 250);

      widget
        .siblings('input[name="widget-height"]')
        .val(height ? height : 200);
      
      if ($('body.wp-customizer').length > 0)
      {
        inside
          .find('.widget-content')
          .css({
            'width': width,
            'max-width': width
          })
          .attr('style', 'max-width: ' + width + ' !important');
      }
        
      setTimeout(function()
      {
        widget
          .find('.piklist-universal-widget-form-container')
          .show();

        if (toggled)
        {
          toggle.trigger('click');
        }
      }, 250);
    },
    
    list_tables: function()
    {
      $('.piklist-list-table-export-columns')
        .sortable()
        .disableSelection();
        
      $('.piklist-list-table-export-submit').on('click', function(event)
      {
        var form_id = $(this).attr('rel');
        
        tb_remove();
        
        $('#' + form_id).submit();
      });
    }
  };

  
  
      
  /* --------------------------------------------------------------------------------
    WP Tabs - Updates or enhancements to existing WordPress Functionality
        - These should be submitted as patches to the core 
  -------------------------------------------------------------------------------- */
  
  var WPTabs = function(element, options)
  {
    this.$element = $(element);
    this._init();
  };
  
  WPTabs.prototype = {

    constructor: WPTabs,

    _init: function()
    {
      this.setup();
      
      $('.wp-tab-bar li a').on('click', function(event)
      {
        event.preventDefault(); 

        var tab = $(this).closest('li'),
          index = $(this).closest('.wp-tab-bar').children().index(tab),
          panels = $(this).closest('.wp-tab-bar').nextUntil('.wp-tab-bar', '.wp-tab-panel'); 

        tab.addClass('wp-tab-active').siblings().removeClass('wp-tab-active');
        
        for (var i = 0; i < panels.length; i++)
        {
          $(panels[i]).toggle(i == index ? true : false); 
        }
      });
    },
    
    setup: function()
    {
      $('.wp-tab-bar li a').each(function()
      {
        var tab = $(this).closest('li');

        if (!tab.hasClass('wp-tab-active'))
        {
          var index = $(this).closest('.wp-tab-bar').children().index(tab);
          
          $(this).closest('.wp-tab-bar').nextUntil('.wp-tab-bar', '.wp-tab-panel').eq(index).hide();
        }
      });
    }
  };
  
  $.fn.wptabs = function(option)
  {
    var _arguments = Array.apply(null, arguments);
    _arguments.shift();
  
    return this.each(function() 
    {
      var $this = $(this),
        data = $this.data('wptabs'),
        options = typeof option === 'object' && option;

      if (!data) 
      {
        $this.data('wptabs', (data = new WPTabs(this, $.extend({}, $.fn.wptabs.defaults, options, $(this).data()))));
      }
  
      if (typeof option === 'string') 
      {
        data[option].apply(data, _arguments);
      }
    });
  };
  
  $.fn.wptabs.defaults = {};
  
  $.fn.wptabs.Constructor = WPTabs;
  
})(jQuery, window, document);
  