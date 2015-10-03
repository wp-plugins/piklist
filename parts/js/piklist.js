/* --------------------------------------------------------------------------------
  Piklist Libraries
--------------------------------------------------------------------------------- */

;(function($, window, document, undefined) 
{
  'use strict';
  
  $(document).ready(function()
  {  
    $('body')
      .piklistgroups()
      .piklistcolumns()
      .piklistaddmore({
        sortable: true
      })
      .piklistmediaupload()
      .piklistfields();
  });
  

  
  /* --------------------------------------------------------------------------------
    Piklist Fields - Sets up Field rules and handles dynamic fields
  -------------------------------------------------------------------------------- */
  
  var PiklistFields = function(element, options)
  {
    this.$element = $(element);

    var _fields_ids = this.$element.find('[name="' + piklist.prefix + '[fields]"]'),
      fields_ids = _fields_ids.length > 0 ? _fields_ids : this.$element.parents('form:first').find('[name="' + piklist.prefix + '[fields]"]');
    
    this.ids = fields_ids.map(function()
               { 
                 return $(this).val(); 
               })
               .get();
                
    this._init();
  };
  
  PiklistFields.prototype = {

    constructor: PiklistFields,
    
    processed_conditions: [],
    
    events: [],
    
    templates: [],

    _init: function()
    {
      var fields;
      
      for (var i in this.ids)
      {
        fields = $(':input[name="' + piklist.prefix + '[fields]"][value="' + this.ids[i] + '"]').data('piklist-fields');
        
        if (typeof fields != 'undefined')
        {
          this.process_fields(this.ids[i], fields);
        }
      }
      
      this.process_events();
    },
    
    process_fields: function(id, fields)
    {
      for (var i in fields)
      {
        for (var j in fields[i])
        {
          if (!fields[i][j].display)
          {
            if (fields[i][j].index == null || fields[i][j].index == 0)
            {
              this.process_field(fields[i][j], id);
            }
          }
        }
      }
    },
    
    process_events: function()
    {
      for (var selector in this.processed_conditions)
      {
        $(document).off('change', selector, this.conditions_handler);

        $(document).on('change', selector, {
          piklistfields: this,
          list: this.processed_conditions[selector]
        }, this.conditions_handler);

        $(selector).trigger('change');
      }
    },
    
    process_field: function(field, fields_id)
    {
      if (field.id && field.id.indexOf('__i__') > -1)
      {
        var widget = $('input[value="' + fields_id + '"]:last').parents('.widget').attr('id'),
          n = widget.charAt(widget.length - 1);
        
        if (!isNaN(parseFloat(n)) && isFinite(n))
        {
          field.id = field.id.toString().replace('__i__', n);
          field.name = field.name.toString().replace('__i__', n);
        }
        else
        {
          return false;
        }
      }

      if (field.multiple && field.name)
      {
        if (field.name.match(/\[[0-9]\]/))
        {
          var i = 0,
            fields = $(':input[name^="' + field.name.replace(/\[[0-9]\]/, '[' + i + ']') + '"]:not(:hidden)');

          while (fields.length > 0)
          {
            fields
              .on('click', this.multiple_handler)
              .each(this.multiple_handler);

            fields = $(':input[name^="' + field.name.replace(/\[[0-9]\]/, '[' + i++ + ']') + '"]:not(:hidden)')
          }
        }
        else
        {
          $(':input[name="' + field.name + '"]:not(:hidden)')
            .on('click', this.multiple_handler)
            .each(this.multiple_handler);
        }
      }
      
      if (field.conditions)
      {
        var field_id,
          field_selector,
          field_event;
                    
        for (var i in field.conditions)
        {
          if (i != 'relation' && typeof field.name != 'undefined' && !field.display)
          {
            switch (field.conditions[i].type)
            {
              case 'update':
          
                field_selector = '[name="' + field.conditions[i].name + '"]';
                field_event = ':input[name="' + field.name + '"]';

              break;
        
              default:

                field_selector = '[name="' + field.name + '"]';
                field_event = '.' + field.conditions[i].id;
          
              break;
            }
            
            if (typeof this.processed_conditions[field_event] === 'undefined')
            { 
              this.processed_conditions[field_event] = [];
            }
            
            this.processed_conditions[field_event].push({
              selector: field_selector,
              conditions: field.conditions
            });
          }
        }
      }
       
      $('.piklist-field-condition-toggle').each(function()
      {
        var hide = {
          'position': 'absolute',
          'left': '-9999999px',
          'visibility': 'hidden',
          'opacity': 0
        };

        if ($(this).parents('table.form-table').length > 0)
        {
          $(this).parents('tr:eq(0)').css(hide);
        }
      });

      var options = typeof field.options === 'object' ? field.options : null;

      switch (field.type)
      {
        case 'editor':
          
          if (typeof this.templates['piklist-editor-proxy'] == 'undefined')
          {
            this.templates['piklist-editor-proxy'] = $('#wp-piklist-editor-proxy-wrap').html().trim();

            $('#wp-piklist-editor-proxy-wrap').remove();
          }

          var $this = this,
            is_widget = $('body').hasClass('widgets-php') || $('body').hasClass('wp-customizer'),
            editor_ids = [];
            

          $('textarea[name*="' + field.name.split(/(?!^)\[[\d]\]/g).join('"][name*="') + '"]').each(function()
          {
            var element = $(this),
              wrapper = element.parents('.wp-editor-wrap:eq(0)'),
              id = element.attr('id'),
              original_id = id.replace(/\d+$/, 0),
              index = id.match(/\d+$/)[0],
            template;

            if (index == 0 && typeof $this.templates[id] == 'undefined')
            {
              $this.templates[id] = wrapper.prop('outerHTML');
            }

            if (element.parents('#wp-' + id + '-editor-container').length == 0)
            {
              if (typeof $this.templates[original_id] == 'undefined')
              {
                original_id = element.attr('name').replace(/\[[0-9]\]/, '[0]').replace(/\]/g, '').replace(/\[/g, '_');
                original_id += (original_id.indexOf('_', original_id.length - 1) !== -1 ? null : '_') + 0;
              }

              template = $($this.templates[original_id].replace(new RegExp(original_id, 'g'), id));
              
              template
                .find('textarea')
                .val(element.val());

              $(template).insertAfter(wrapper);
            
              wrapper.remove();
              
              if (typeof tinyMCEPreInit.qtInit[id] == 'undefined')
              {
                tinyMCEPreInit.qtInit[id] = JSON.parse(JSON.stringify(tinyMCEPreInit.qtInit[original_id]));
                tinyMCEPreInit.qtInit[id].id = id;
              }

              if (typeof tinyMCEPreInit.mceInit[id] == 'undefined')
              {
                tinyMCEPreInit.mceInit[id] = JSON.parse(JSON.stringify(tinyMCEPreInit.mceInit[original_id]));
                tinyMCEPreInit.mceInit[id].elements = id;
                tinyMCEPreInit.mceInit[id].selector = '#' + id;
                tinyMCEPreInit.mceInit[id].body_class = tinyMCEPreInit.mceInit[id].body_class.replace(original_id, id);
              }
              else
              {
                tinyMCE.execCommand(tinymce.majorVersion == 3 ? 'mceRemoveControl' : 'mceRemoveEditor', true, id);                
              }

              editor_ids.push(id);
            }
            else if (is_widget)
            {
              tinyMCE.execCommand(tinymce.majorVersion == 3 ? 'mceRemoveControl' : 'mceRemoveEditor', true, id);                
          
              editor_ids.push(id);
            }
          });
          

          for (var i = 0; i < editor_ids.length; i++)
          {
            quicktags(tinyMCEPreInit.qtInit[editor_ids[i]]);

            QTags._buttonsInit();
            
            tinyMCE.init(editor_ids[i]);
          
            if (typeof switchEditors != 'undefined')
            {
              if (is_widget)
              {
                setTimeout(function(id)
                {
                  switchEditors.go(id, 'html');
                  switchEditors.go(id, 'tmce');
                }, 300, editor_ids[i]);
              }
              else
              {
                switchEditors.go(editor_ids[i], 'html');
                switchEditors.go(editor_ids[i], 'tmce');
              }
            }
          }
          
        break;

        case 'datepicker':
          
          $(':input[name*="' + field.name.split(/(?!^)\[[\d]\]/g).join('"][name*="') + '"]:not(.hasDatepicker)').each(function()
          {
            $(this)
              .attr('autocomplete', 'off')
              .datepicker(options);
          });

        break;

        case 'colorpicker':

          $(':input[name*="' + field.name.split(/(?!^)\[[\d]\]/g).join('"][name*="') + '"]').wpColorPicker(options);

        break;
        
        case 'file':

          $(':input.' + field.attributes.class)
            .data('multiple', typeof field.options.multiple != 'undefined' ? field.options.multiple : 'true')
            .data('save', typeof field.options.save != 'undefined' ? field.options.save : 'id');

        break;
      }

      // Legacy/Inline field configuration option
      if (field.js_callback)
      {
        window[field.js_callback](field);
      }
      
      $(document).trigger('piklist:field:render', [field]);
    },
    
    multiple_handler: function(event)
    {
      var value = $(':input[name="' + $(this).attr('name') + '"]:not(:hidden)' + ($(this).is(':checkbox') || $(this).is(':radio') ? ':checked' : null)).val(),
        hidden = $(':input[name="' + $(this).attr('name') + '"][type="hidden"]');

      if (typeof value != 'undefined')
      {
        hidden.attr('disabled', 'disabled');
      }
      else
      {
        hidden.removeAttr('disabled', 'disabled');
      }
    },
    
    to_array: function(object)
    {
      return $.map(object, function(o) 
      {
        return [$.map(o, function(v) 
        {
          return v;
        })];
      });
    },
    
    conditions_handler: function(event) 
    { 
      var $this = event.data.piklistfields;

      for (var i in event.data.list)
      {
        $this.conditions($(this), event.data.list[i].selector, event.data.list[i].conditions);
      }
    },
    
    conditions: function(condition_field, selector, list) 
    { 
      var field, element, parent, context, i, widget, widget_id, widget_id_base,
        conditions = [],
        condition_value,
        condition_selector,
        add_more = condition_field.parents('div[data-piklist-field-addmore]:eq(0)').length > 0 ? condition_field.parents('div[data-piklist-field-addmore]:eq(0)') : null,
        relation = 'and',
        form = condition_field.parents('form:first'),
        index = condition_field.index('*[name="' + condition_field.attr('name') + '"]:not(:input[type="hidden"])'),
        reset_selector = selector.replace(/\[[0-9]+(?!.*[0-9])\]/, '[' + index + ']'),
        update,
        result,
        outcomes = [],
        overall_outcome = true,
        value,
        values = [],
        show = {
          'position': 'relative',
          'left': 'auto',
          'visibility': 'visible'
        },
        hide = {
          'position': 'absolute',
          'left': '-9999999px',
          'visibility': 'hidden',
          'opacity': 0
        };

      // Get the field in question
      field = $('*[name*="' + (selector == reset_selector ? selector : reset_selector).replace('[name="', '').replace('"]', '').split(/(?!^)\[[\d]\]/g).join('"][name*="') + '"]', add_more);
      
      // Determine the conditions and relation
      for (i in list)
      {
        if (i == 'relation')
        {
          relation = list[i];
        }
        else
        {
          conditions.push(list[i]);
        }
      }
      
      context = add_more ? add_more : form;
      widget = $(':input[type="hidden"][name="widget_number"]', form);

      // Check the conditions and their outcomes
      for (i in conditions)
      {
        if (typeof conditions[i].name != 'undefined')
        {
          result = false;
          values = $.isArray(conditions[i].value) ? conditions[i].value : [conditions[i].value];

          if (widget.length > 0)
          {
            widget_id = $(':input[type="hidden"][name="multi_number"]', form).val();
            widget_id = !widget_id ? widget.val() : widget_id;
            widget_id_base = $(':input[type="hidden"][name="id_base"]', form).val();

            element = '[name^="widget-' + widget_id_base + '[' + widget_id + ']' + '[' + conditions[i].field + ']"]';
          }
          else
          {
            element = '';
            $(conditions[i].field.split(':')).each(function(index, part)
            {
              element += '[name*="[' + part + ']"]';
            });
          }

          element = $(':input' + element, context); // + ':eq(' + index + ')'

          if (element.is('select'))
          {
            value = element.children('option:selected').val();
          }
          else if (element.is(':radio, :checkbox'))
          {
            var _values = [];

            element.each(function()
            {
              if ($(this).is(':checked'))
              {
                _values.push($(this).val());
              }
            });
            
            value = _values.length > 0 ? _values : value;
          }
          else
          {
            value = element.val();
          }
          
          if ($.isArray(value))
          {
            value = $.map(value, function(v, i)
            {
              return isNaN(v) ? v : parseFloat(v);
            });

            result = $.intersect(value, values).length > 0;
          }
          else
          {
            result = $.inArray(value, values) != -1;
          }

          if (typeof conditions[i].compare != 'undefined' || conditions[i].compare == '!=')
          {
            result = !result;
          }

          outcomes.push({
            condition: conditions[i],
            result: result
          });
        }
      }
      
      // Dertermine overall condition based on outcomes
      for (i = 0; i < outcomes.length; i++)
      {
        if (outcomes[i].condition.type != 'update')
        {
          if (relation == 'and')  
          {
            overall_outcome = overall_outcome && outcomes[i].result;
          }
          else if (relation == 'or')  
          {
            overall_outcome = !overall_outcome && !outcomes[i].result ? outcomes[i].result : overall_outcome || outcomes[i].result;
          }
        }
      }

      if (relation == 'or' && overall_outcome && outcomes.length > 0)
      {
        overall_outcome = false;
    
        for (i = 0; i < outcomes.length; i++)
        {
          if (outcomes[i].result)
          {  
            overall_outcome = true;
    
            break;
          }
        }
      }

      // Find context
      if (field.parents('table.form-table').length > 0)
      {
        context = field.parents('tr:eq(0)');
      }
      else if (field.parents('.piklist-field-condition-toggle').length > 0) 
      {
        context = field.parents('.piklist-field-condition-toggle');
      }
      else if (field.parents('div[data-piklist-field-group="' + field.attr('data-piklist-field-group') + '"]').length)
      {
        context = field.parents('div[data-piklist-field-group="' + field.attr('data-piklist-field-group') + '"]');
      }
      else
      {
        context = null;
      }
    
      // Check if we are in an add-more
      if (context && context.parent('.piklist-field-addmore-wrapper').length > 0 && field.parents('.piklist-field-addmore-wrapper').length > condition_field.parents('.piklist-field-addmore-wrapper').length)
      {
        context = context.parent('.piklist-field-addmore-wrapper');
      }
      
      for (i in outcomes)
      {
        switch (outcomes[i].condition.type)
        {
          case 'update':
            
            condition_selector = '[name="' + outcomes[i].condition.name + '"]';
            
          break;
            
          default:

            condition_selector = '[name="' + field.attr('name') + '"]';

          break;
        }

        $(condition_selector, context).each(function()
        {
          field = $(this);
      
          switch (outcomes[i].condition.type)
          {
            case 'update':
        
              update = false;
      
              if (condition_field.is(':radio') || condition_field.is(':checkbox'))
              {
                condition_value = condition_field.is(':checked') ? condition_field.val() : '';
              }
              else
              {
                condition_value = condition_field.val();
              }
      
              if ($.isArray(outcomes[i].condition.value) && $.inArray(condition_value, outcomes[i].condition.value) > -1)
              {
                update = true;
              }
              else
              {
                if (condition_value == outcomes[i].condition.value)
                {
                  if (condition_field.is(':radio') || condition_field.is(':checkbox'))
                  {
                    update = condition_field.is(':checked');
                  }
                  else
                  {
                    update = true;
                  }
                }
              }
    
              if (update)
              {
                if (field.is('select'))
                {
                  if (typeof outcomes[i].condition.choices != 'undefined')
                  {
                    field.empty();
                
                    for (var key in outcomes[i].condition.choices)
                    {
                      field
                        .append($('<option></option>')
                        .attr('value', key).text(outcomes[i].condition.choices[key]));
                    }
                  }
        
                  if (field.children('option[value="' + outcomes[i].condition.update + '"]').length > 0)
                  {
                    field.children('option').removeAttr('selected');
                    field.children('option[value="' + outcomes[i].condition.update + '"]').attr('selected', 'selected');
                  }
                }
                else
                {
                  field.val(outcomes[i].condition.update);
                }
    
                field.trigger('change');
              }
        
            break;

            default:
        
              if (context != null)
              {
                if (context.css('visibility') == 'hidden' && overall_outcome)
                {
                  context
                    .css(show)
                    .animate({
                      'opacity': 1
                    });
                }
                else if (!overall_outcome)
                {
                  if (outcomes[i].condition.reset)
                  {
                    if (field.is(':radio') || field.is(':checkbox'))
                    {
                      field = $(selector == reset_selector ? selector : reset_selector);

                      if (field.is(':checked'))
                      {
                        field
                          .attr('checked', false)
                          .trigger('change');
                      }
                    }
                    else
                    {
                      if (field.is('select'))
                      {
                        if (field.children('option[selected="selected"]').length > 0)
                        {
                          field
                            .children('option')
                            .removeAttr('selected');

                          field.trigger('change');
                        }
                      }
                      else
                      {
                        if (field.val() != '')
                        {
                          field
                            .val('')
                            .trigger('change');
                        }
                      }
                    }
                  }

                  context.css(hide);
                }
              }

            break;
          }
        });
      }
      
      return false;
    }
  };
  
  $.fn.piklistfields = function(option)
  {
    var _arguments = Array.apply(null, arguments);
    _arguments.shift();
  
    return this.each(function() 
    {
      var $this = $(this),
        data = $this.data('piklistfields'),
        options = typeof option === 'object' && option;
  
      if (!data) 
      {
        $this.data('piklistfields', (data = new PiklistFields(this, $.extend({}, $.fn.piklistfields.defaults, options, $(this).data()))));
      }
  
      if (typeof option === 'string') 
      {
        data[option].apply(data, _arguments);
      }
    });
  };
  
  $.fn.piklistfields.defaults = {};
  
  $.fn.piklistfields.Constructor = PiklistFields;
  
  
  
  /* --------------------------------------------------------------------------------
    Piklist Groups - Creates Group containers for Grouped Fields
  -------------------------------------------------------------------------------- */
  
  var PiklistGroups = function(element, options)
  {
    this.$element = $(element);

    this._init();
  };
  
  PiklistGroups.prototype = {

    constructor: PiklistGroups,

    _init: function() 
    {
      this.$element
        .find('[data-piklist-field-group]:not(:radio, :checkbox, :file)')
        .each(function()
        {
          var $element = $(this),
            group = $element.attr('data-piklist-field-group'),
            sub_group = $element.attr('data-piklist-field-sub-group');
            
          if ($element.is('textarea') && $element.hasClass('wp-editor-area'))
          {
            $element = $(this).parents('.wp-editor-wrap:first');
          }
            
          if ($element.parents('.piklist-field-part').length > 0)
          {
            $element = $element.parents('.piklist-field-part:eq(0)');
          }
          
          if ($element.prev().hasClass('piklist-label-position-before'))
          {
            $element = $element
              .prevUntil('.piklist-label-position-before', '.piklist-field-part:not(div)')
              .addBack()
              .prev('.piklist-label-position-before');
          }
          else if ($element.next().hasClass('piklist-label-position-after'))
          {            
            $element = $element
              .nextUntil('.piklist-label-position-after', '.piklist-field-part:not(div)')
              .addBack()
              .next('.piklist-label-position-after');
          }

          if (!$element.hasClass('wp-editor-wrap'))
          {
            $element = $element.addBack();
          }
                    
          $element.wrapAll('<div data-piklist-field-group="' + group + '" ' + (sub_group ? 'data-piklist-field-sub-group="' + sub_group + '"' : '') + ' />');
        });

     this.$element
       .find('[data-piklist-field-group]')
       .filter(':radio, :checkbox')
       .each(function()
       {
         var $element = $(this),
           group = $element.attr('data-piklist-field-group'),
           sub_group = $element.attr('data-piklist-field-sub-group'),
           list = $element.parents('.piklist-field-list').length > 0,
           parent_selector = list ? '.piklist-field-list' : '.piklist-field-list-item',
           parent = $element.parents('div[data-piklist-field-group]:eq(0)'),
           wrap = $('<div data-piklist-field-group="' + group + '" ' + (sub_group ? 'data-piklist-field-sub-group="' + sub_group + '"' : '') + ' />');

         if ($element.parents('.piklist-field-part').length > 0)
         {
           parent_selector = '.piklist-field-part';
         }

         var index = $($element.parents(parent_selector)).index();

         if (parent.length > 0)
         {
           parent.attr('data-piklist-field-group', group);

           if (sub_group)
           {
             parent.attr('data-piklist-field-sub-group', sub_group);
           }
         }
         else
         {
           if (list)
           {
             $element = $element
               .parents(parent_selector)
               .prev('.piklist-field-part:eq(0):not(.piklist-label-position-before)')
               .addBack()
               .prev('.piklist-field-part:eq(0):not(.piklist-label-position-after)')
           }
           else
           {
             $element = $element.parents(parent_selector);

             if ($element.prev().hasClass('piklist-label-position-before') || $element.next().length == 0)
             {
               $element = $element
                 .prev('.piklist-label-position-before')
                 .addBack()
                 .nextUntil('.piklist-field-part', parent_selector);
             }
             else if ($element.next().hasClass('piklist-label-position-after') || $element.prev().length == 0)
             {
               $element = $element
                 .next('.piklist-label-position-after')
                 .addBack()
                 .prevUntil('.piklist-field-part', parent_selector);
             }
             else
             {
               $element = $element
                 .nextUntil(':not(.piklist-field-list-item)', parent_selector)
                 .addBack();
             }
           }

           $element
             .addBack()
             .wrapAll(wrap);
         }
       });
    }
  };
  
  $.fn.piklistgroups = function(option)
  {
    var _arguments = Array.apply(null, arguments);
    _arguments.shift();
  
    return this.each(function() 
    {
      var $this = $(this),
        data = $this.data('piklistgroups'),
        options = typeof option === 'object' && option;
  
      if (!data) 
      {
        $this.data('piklistgroups', (data = new PiklistGroups(this, $.extend({}, $.fn.piklistgroups.defaults, options, $(this).data()))));
      }
  
      if (typeof option === 'string') 
      {
        data[option].apply(data, _arguments);
      }
    });
  };
  
  $.fn.piklistgroups.defaults = {};
  
  $.fn.piklistgroups.Constructor = PiklistGroups;



  /* --------------------------------------------------------------------------------
    Piklist Add More - Creates Add More fields for Piklist
  -------------------------------------------------------------------------------- */
  
  var PiklistAddMore = function(element, options)
  {
    this.$element = $(element);
    
    this.add = options.add;
    this.remove = options.remove;
    this.move = options.move;
    this.sortable = options.sortable;
    
    this._init();
  };
  
  PiklistAddMore.prototype = {

    constructor: PiklistAddMore,
    
    templates: [],

    _init: function() 
    {
      var $this = this;
      
      // NOTE: This fixes most layouts that will break jQuery UI Sortables.
      $('html, body').css('overflow-x', 'initial');

      $(document).on('click', '[data-piklist-field-addmore-action]', { piklistaddmore: $this }, $this.action_handler);
      
      this.$element
        .find('*[data-piklist-field-addmore]')
        .each(function()
        {
          var $element = $(this),
            group = $element.attr('data-piklist-field-group'),
            set = $element.attr('name'),
            addmore = $element.attr('data-piklist-field-addmore'),
            addmore_actions = $element.attr('data-piklist-field-addmore-actions'),
            $wrapper = $('<div />')
                         .attr('data-piklist-field-addmore', set)
                         .addClass('piklist-field-addmore-wrapper'),
            $wrapper_actions = $('<div />')
                                 .addClass('piklist-field-addmore-wrapper-actions')
                                 .css('display', 'inline');
                 
          $this.sortable = $element.attr('data-piklist-field-sortable');
          
          if ($element.parents('div[data-piklist-field-addmore="' + $element.attr('name') + '"]').length == 0)
          {
            if ($element.is('[data-piklist-field-columns]'))
            {
              $wrapper.css({
                'float': 'none'
              });
            }

            if (group)
            {
              $wrapper.addClass('piklist-field-addmore-wrapper-full');
            }
          
            if ($element.is('textarea') && $element.hasClass('wp-editor-area'))
            {
              $element = $element.parents('.wp-editor-wrap:first');
            }
            
            if ($element.is(':checkbox, :radio, :input[type="hidden"]'))
            {
              var $parent = $(':input[name="' + $element.attr('name') + '"]').commonAncestor();

              if ($parent.parents('div[data-piklist-field-group="' + group + '"], div[data-piklist-field-sub-group="' + group + '"]').length > 0)
              {
                $parent = $parent.parents('div[data-piklist-field-group="' + group + '"], div[data-piklist-field-sub-group="' + group + '"]');
              }
            
              if ($parent.parents('.piklist-field-part').length > 0)
              {
                $parent = $parent.parents('.piklist-field-part');
              }

              if ($element.is(':input[type="hidden"]'))
              {
                $wrapper.addClass('piklist-field-addmore-wrapper-full');
              }
            
              if ($parent.parent('div[data-piklist-field-addmore="' + $element.attr('name') + '"]').length == 0)
              {
                $element = $parent
                  .siblings('div[data-piklist-field-group="' + group + '"], div[data-piklist-field-sub-group="' + group + '"], .piklist-field-part:first')
                  .addBack()
                  .wrapAll($wrapper);
              }
            }
            else
            {              
              if (typeof group === 'undefined')
              {
                if ($element.parent('.piklist-field-column').length > 0)
                {
                  $element = $element
                    .parent('.piklist-field-column')
                    .wrapAll($wrapper);
                }
                else
                {
                  $element = $element
                    .siblings('.piklist-field-part')
                    .addBack()
                    .wrapAll($wrapper);
                }
              }
              else
              {
                if ($element.attr('data-piklist-field-addmore-single'))
                {
                  $element
                    .parents('div[data-piklist-field-group="' + group + '"], div[data-piklist-field-sub-group="' + group + '"]')
                    .wrapAll($wrapper);
                }
                else
                {
                  var set = $('div[data-piklist-field-group="' + group + '"]');
                  
                  set = $this.get_groups(set, group);
                  
                  set.wrapAll($wrapper);
                }
              }
            }
          
            var $container = $element.parents('div[data-piklist-field-addmore' + (typeof set == 'string' ? '="' + set + '"' : '') + ']:first'),
              $parent = $container.parent();
            
            if (addmore_actions)
            { 
              if (($('body').hasClass('widgets-php') || $('body').hasClass('wp-customizer') ? $container.actual('height') : $container.height()) >= 60)
              {
                $wrapper_actions.addClass('piklist-field-addmore-wrapper-actions-vertical');
                $container.addClass('piklist-field-addmore-wrapper-vertical');
              }

              $wrapper_actions.prepend($($this.add).attr('data-piklist-field-addmore-action', 'add'));
              $wrapper_actions.prepend($($this.remove).attr('data-piklist-field-addmore-action', 'remove'));
            }
            else
            {
              $container.addClass('piklist-field-sortable');
            }
            
            if ($this.sortable)
            {
              $container.addClass('piklist-field-sortable-active');
            }

            $parent
              .sortable({
                items: '> div[data-piklist-field-addmore]:not([name])',
                cursor: 'move',
                placeholder: 'piklist-addmore-placeholder',
                disabled: $this.sortable ? false : true,
                start: function(event, ui)
                {
                  ui.placeholder.height(ui.item.innerHeight());
                  ui.placeholder.width(ui.item.outerWidth());
                },
                update: function(event, ui) 
                {
                  $this.re_index($(this), true);
                }
              });

            if ($element.siblings('.piklist-field-addmore-wrapper-actions').length == 0)
            {
              $element
                .parents('div.piklist-field-addmore-wrapper:eq(0)')
                .append($wrapper_actions);
            }
          }
        });
        
      this.$element
        .find('*[data-piklist-field-addmore]')
        .each(function()
        {
          var $element = $(this),
            $html = $element.parents('div[data-piklist-field-addmore]:first'),
            name = $html.attr('data-piklist-field-addmore'),
            names = [],
            excludes = '[data-piklist-field-addmore], [data-piklist-field-group], .piklist-field-addmore-wrapper-actions, .piklist-field-addmore-wrapper, .piklist-field-column';
          
          if (typeof name != 'undefined')
          {
            var template_name = name.replace(/(?!^)\[[\d]\]/g, '[0]');

            if (typeof $this.templates[template_name] == 'undefined')
            {
              $html = $('<div/>').append($html.parent().html());

              $html.find('div[data-piklist-field-addmore]').each(function()
              {
                var data = $(this).attr('data-piklist-field-addmore').replace(/(?!^)\[[\d]\]/g, '[0]');

                if ($.inArray(data, names) == -1)
                {
                  $(this).find(':input:not([data-piklist-field-addmore-clear="0"])').each(function()
                  {
                    $(this)
                      .attr('data-piklist-original-id', $(this).attr('id'))
                      .removeAttr('id')
                      .off()
                      .find('option')
                      .removeAttr('selected');

                    if ($(this).is(':checkbox'))
                    {
                      $(this).removeAttr('checked');
                    }

                    if (!$(this).is(':checkbox, :radio'))
                    {
                      $(this).removeAttr('value');
                    }
                  
                    if ($(this).is('textarea'))
                    {
                      $(this).empty();
                    }
                  });
                
                  if (!$(this).prev().is(excludes))
                  {
                    $(this).prev().remove();
                  }

                  if (!$(this).next().is(excludes))
                  {
                    $(this).next().remove();
                  }

                  $(this)
                    .find('.piklist-field-preview *:not(ul.attachments, div.piklist-field-addmore-wrapper-actions, div.piklist-field-addmore-wrapper-actions *, :input[type="hidden"])')
                    .remove();

                  names.push(data);
                }
                else
                {
                  $(this).remove();
                }
              });

              $html.children().each(function()
              {
                if (!$(this).is('.piklist-field-addmore-wrapper-actions, [data-piklist-field-addmore="' + name + '"]'))
                {
                  $(this).remove();
                }
              });

              $this.templates[template_name] = $html.html().trim();
            }
          }
        });

      $('.wp-editor-area, .wp-editor-area-proxy').parents('.piklist-field-addmore-wrapper').addClass('piklist-field-addmore-wrapper-full');
    },
    
    get_groups: function(set, group)
    {
      var $this = this,
        groups_collected = false,
        _group = group; 
    
      do {
        $('div[data-piklist-field-sub-group="' + _group + '"]').each(function()
        {
          _group = $(this).attr('data-piklist-field-group');

          set.push(this);
          
          set = $this.get_groups(set, _group);
        });
    
        groups_collected = $('div[data-piklist-field-sub-group="' + _group + '"]').length == 0;
      
      } while(!groups_collected);
      
      return set;
    },
    
    action_handler: function(event)
    {
      event.preventDefault();

      if (event.isPropagationStopped())
      {
        return; 
      }

      event.stopPropagation();
      
      var $element = $(this),
        $wrapper = $element.parents('div.piklist-field-addmore-wrapper:first'),
        count = $wrapper.siblings('div.piklist-field-addmore-wrapper').length,
        element = $wrapper.attr('data-piklist-field-addmore'),
        element_indexes = element ? element.replace(/\]/g, '').split('[') : [],
        groups = 0,
        $this = event.data.piklistaddmore;

      for (var j = element_indexes.length - 1; j >= 0; j--)
      {
        if ($.isNumeric(element_indexes[j]))
        {
          groups = groups + 1;
        }
      }

      $wrapper.parent('.ui-sortable').css('height', 'auto');
      
      switch ($element.attr('data-piklist-field-addmore-action'))
      {
        case 'add':

          var name = $wrapper.attr('data-piklist-field-addmore').replace(/(?!^)\[[\d]\]/g, '[0]'),
            template = $this.templates[name],
            sub_group = $(template).find('div[data-piklist-field-addmore="' + name + '"]:first');          
          
          if (sub_group.length > 0)
          {
            template = $(sub_group).clone().wrap('<div>').parent().html();
          }
          
          $wrapper.parent().find(':radio').each(function()
          {
            if ($(this).is(':checked'))
            {
              $(this).data('piklist-field-checked', 'true');
            }
          });
          
          $(template).insertAfter($wrapper);

          $wrapper
            .parent()
            .children('div.piklist-field-addmore-wrapper')
            .each(function(i)
            {
              $(this)
                .sortable({
                  items: '> div[data-piklist-field-addmore]:not([name])',
                  cursor: 'move',
                  placeholder: 'piklist-addmore-placeholder',
                  start: function(event, ui)
                  {
                    ui.placeholder.height(ui.item.innerHeight());
                    ui.placeholder.width(ui.item.outerWidth());
                  },
                  update: function(event, ui) 
                  {
                    $this.re_index($(this), true);
                  }
                });
            });

          $this.re_index($wrapper.parent(), false);
            
          $wrapper = $wrapper.next();
          
          $wrapper.trigger('piklistaddmore', [$wrapper, 'add']);

          if ($wrapper.find('.wp-editor-wrap').length > 0)
          {
            $wrapper.addClass('piklist-field-addmore-wrapper-full');
          }
          
        break;
        
        case 'remove':
          
          if (count > 0)
          {
            var $containers = $wrapper
                            .parent()
                            .children('div.piklist-field-addmore-wrapper');

            $this.re_index($wrapper.parent(), true);

            $wrapper
              .trigger('piklistaddmore', [$wrapper, 'remove'])
              .remove();
          }
          
        break;
      }
    },
      
    re_index: function(wrapper, sort)
    {
      if (wrapper.length == 0)
      {
        return;
      }

      wrapper.find('> div[data-piklist-field-addmore]').each(function()
      {
        var element = $(this);
        
        if (sort)
        {
          element.find(':radio').each(function()
          {
            if ($(this).is(':checked'))
            {
              $(this).data('piklist-field-checked', 'true');
            }
          });
        }

        element.find(':input').each(function()
        {
          var id,
            name = $(this).attr('name'),
            is_widget = $('body').hasClass('widgets-php') || $('body').hasClass('wp-customizer');

          if (name)
          {
            var level = 0,
              index,
              _indexes = [],
              indexes = name.replace(/\]/g, '').split('['),
              levels = $(this).parents('div[data-piklist-field-addmore]').length - 1,
              scope = indexes[0],
              parent = $(this).parents('div[data-piklist-field-addmore]:eq(0)'),
              value = $(this).val();
          
            for (var i = 0; i <= levels; i++)
            {
              _indexes.push($(parent.parents('.ui-sortable:eq(' + i + ')').children('div[data-piklist-field-addmore]')).index(i == 0 ? parent : parent.parents('.ui-sortable:eq(' + (i - 1) + ')')));
            }

            for (var j = 0; j < indexes.length; j++)
            { 
              if ($.isNumeric(indexes[j]))
              {
                if (!is_widget || (is_widget && level > 0))
                {
                  if ($.isNumeric(_indexes[_indexes.length - (is_widget ? level : level + 1)]))
                  {
                    indexes[j] = _indexes[_indexes.length - (is_widget ? level : level + 1)];
                  }
                }
        
                level = level + 1;
              }
    
              indexes[j] = indexes[j] + (scope !== indexes[j] ? ']' : '');
            }
          
            index = _indexes.slice(-1).pop();

            name = indexes.join('[');

            id = (is_widget ? indexes.splice(0, indexes.length - 2).join('-').replace(/]/g, '') + '-' + indexes.splice(-2).join('_').replace(/]/g, '') : indexes.join('_').replace(/]/g, ''));
            id += (id.indexOf('_', id.length - 1) !== -1 ? null : '_') + index;
        
            $(this)
              .attr('name', name)
              .attr('id', id);

            if (!$(this).is(':file'))
            {
              $(this).val(value);
            }
            
            parent.find('[for="' + name + '"]').attr('for', name);
          }
        });
      
        var radios = [];

        element.find(':radio').each(function()
        {
          $(this).removeAttr('checked');
        
          if ($.inArray($(this).attr('name'), radios) == -1)
          {
            radios.push($(this).attr('name'));
          }
        
          if (typeof $(this).data('piklist-field-checked') != 'undefined')
          {
            $(this)
              .attr('checked', 'checked')
              .removeData('piklist-field-checked');          
          }
        });
      
        for (var i in radios)
        {
          if ($(':radio[name="' + radios[i] + '"]:checked').length == 0)
          {
            $(':radio[name="' + radios[i] + '"]:eq(0)').attr('checked', 'checked');
          }
        }
      });
      
      wrapper
        .removeData('piklistmediaupload')
        .removeData('piklistfields')
        .piklistmediaupload()
        .piklistfields();
    }
  };
  
  $.fn.piklistaddmore = function(option)
  {
    var _arguments = Array.apply(null, arguments);
    _arguments.shift();

    return this.each(function() 
    {
      var $this = $(this),
        data = $this.data('piklistaddmore'),
        options = typeof option === 'object' && option;
  
      if (!data) 
      {
        $this.data('piklistaddmore', (data = new PiklistAddMore(this, $.extend({}, $.fn.piklistaddmore.defaults, options, $(this).data()))));
      }
  
      if (typeof option === 'string') 
      {
        data[option].apply(data, _arguments);
      }
    });
  };
  
  $.fn.piklistaddmore.defaults = {
    add: '<a href="#" class="' + ($('body').hasClass('wp-admin') ? 'button-secondary' : null) + ' piklist-addmore-button piklist-addmore-add"><span>&#43;</span></a>',
    remove: '<a href="#" class="' + ($('body').hasClass('wp-admin') ? 'button-secondary' : null) + ' piklist-addmore-button piklist-addmore-remove"><span>&ndash;</span></a>',
    sortable: true
  };
  
  $.fn.piklistaddmore.Constructor = PiklistAddMore;



  /* --------------------------------------------------------------------------------
    Piklist Columns - Creates fluid column based layout
  -------------------------------------------------------------------------------- */
  
  var PiklistColumns = function(element, options)
  {
    this.$element = $(element);
    this.total_columns = options.total_columns;
    this.column_width = options.column_width;
    this.gutter_width = options.gutter_width;
    this.gutter_height = options.gutter_height;
    this.minimum_height = options.minimum_height;
    
    this._init();
  };
  
  PiklistColumns.prototype = {

    constructor: PiklistColumns,

    _init: function() 
    {
      var total_columns = this.total_columns,
        column_width = this.column_width,
        gutter_width = this.gutter_width,
        gutter_height = this.gutter_height,
        minimum_height = this.minimum_height,
        track = {
          columns: 0,
          gutters: 0,
          group: false
        };

      this.$element
        .find('[data-piklist-field-columns]:not(:radio, :checkbox, :input[type="hidden"])')
        .each(function()
        {
          var $element = $(this),
            columns = $element.attr('data-piklist-field-columns');
          
          if ($element.is('textarea') && $element.hasClass('wp-editor-area'))
          {
            $element = $(this).parents('.wp-editor-wrap:first');
          }
              
          var $parent = $element.parent('div[data-piklist-field-group]:eq(0)');
              
          if ($parent.length > 0)
          {
            $parent.attr('data-piklist-field-columns', columns);
          } 
          else
          {
            $element
              .siblings('.piklist-field-part:eq(0)')
              .addBack()
              .wrapAll('<div data-piklist-field-columns="' + columns + '" />');
          }
        
          $element
            .css({
              'width': $element.attr('size') || $element.is(':button, :submit') ? 'auto' : '100%',
            })
            .parent('div[data-piklist-field-columns]')
            .css({
              'display': 'block',
              'float': 'left',
              'width': (columns * column_width + (columns - 1) * gutter_width) + '%',
              'margin-right': gutter_width + ($.isNumeric(gutter_width) ? '%' : null),
              'margin-bottom': gutter_height + ($.isNumeric(gutter_height) ? '%' : null)
            });   
        });

      this.$element
        .find('[data-piklist-field-columns]')
        .filter(':radio, :checkbox, :input[type="hidden"]')
        .each(function()
        {
          var $element = $(this),
            columns = $element.attr('data-piklist-field-columns'),
            group = $element.attr('data-piklist-field-group'),
            sub_group = $element.attr('data-piklist-field-sub-group'),
            parent_selector;
            
          if ($element.is(':radio, :checkbox'))
          {
            parent_selector = $element.parents('.piklist-field-list').length > 0 ? '.piklist-field-list' : '.piklist-field-list-item';
          }
          else
          {
            parent_selector = '.piklist-field-part';
          }
            
          $element
            .parents(parent_selector)
            .each(function()
            {
              if ($(this).parent('div[data-piklist-field-columns]').length == 0)
              {
                var $parent = $(this).parent('div[data-piklist-field-group]:eq(0)');
                
                if ($parent.length > 0)
                {
                  $parent.attr('data-piklist-field-columns', columns);
                } 
                else
                {
                  $(this)
                    .siblings('.piklist-field-part')
                    .addBack()
                    .wrapAll('<div data-piklist-field-columns="' + columns + '" data-piklist-field-group="' + group + '" ' + (sub_group ? 'data-piklist-field-sub-group="' + sub_group + '"' : '') + ' />');
                }
                
                $(this)
                  .parent('div[data-piklist-field-columns]')
                  .css({
                    'display': 'block',
                    'float': 'left',
                    'width': (columns * column_width + (columns - 1) * gutter_width) + '%',
                    'margin-right': gutter_width + ($.isNumeric(gutter_width) ? '%' : null),
                    'margin-bottom': gutter_height + ($.isNumeric(gutter_height) ? '%' : null)
                  });
              }
            });  
        });
        
        this.$element
          .find('div[data-piklist-field-columns]')
          .each(function(i)
          {
            var $element = $(this),
              columns = $element.attr('data-piklist-field-columns'),
              group = $element.attr('data-piklist-field-group');

            $element.addClass('piklist-field-column');
            
            if (typeof track.group == 'undefined' || track.group != group)
            {
              track = {
                columns: 0,
                gutters: 0,
                group: group
              };
            }
            
            track = {
              columns: track.columns + columns,
              gutters: track.gutters + 1,
              group: group
            };
            
            if (track.columns >= total_columns)
            {
              $element
                .addClass('piklist-field-column-last')
                .css({
                  'margin-right': '0'
                });

              track = {
                columns: 0,
                gutters: 0,
                group: false
              };
            }
          });
    }
  };
  
  $.fn.piklistcolumns = function(option)
  {
    var _arguments = Array.apply(null, arguments);
    _arguments.shift();
  
    return this.each(function() 
    {
      var $this = $(this),
        data = $this.data('piklistcolumns'),
        options = typeof option === 'object' && option;
  
      if (!data) 
      {
        $this.data('piklistcolumns', (data = new PiklistColumns(this, $.extend({}, $.fn.piklistcolumns.defaults, options, $(this).data()))));
      }
  
      if (typeof option === 'string') 
      {
        data[option].apply(data, _arguments);
      }
    });
  };
  
  $.fn.piklistcolumns.defaults = {
    total_columns: 12,
    column_width: 7,
    gutter_width: 1.45,
    gutter_height: '7px',
  };
  
  $.fn.piklistcolumns.Constructor = PiklistColumns;
  
  
  
  
  
  
  
  
  
  
  
  /* --------------------------------------------------------------------------------
    Piklist Media Upload - Handles the File Upload Field
  -------------------------------------------------------------------------------- */
  
  var PiklistMediaUpload = function(element, options)
  {
    this.$element = $(element);
    this._init();
  };
  
  PiklistMediaUpload.prototype = {

    constructor: PiklistMediaUpload,

    _init: function() 
    {
      $('.piklist-upload-file-preview .attachments')
        .sortable({
          items: 'li.attachment',
          cursor: 'move',
          placeholder: 'piklist-addmore-placeholder attachment',
          start: function(event, ui)
          {
            ui.placeholder.height(ui.item.height() - 2);
            ui.placeholder.width(ui.item.width());
          },
          update: function(event, ui) 
          {
            var attachments = $(this).find('[data-attachment-id]'),
              input_name = $(attachments[0]).data('attachments'),
              input = $(':input[name="' + input_name + '"][type="hidden"]'),
              updates = [];
          
            attachments.each(function(i)
            {
              updates.push($(this).data('attachment-id'));
            });
            
            $(':input[name="' + input_name + '"][type="hidden"]:not(:first)').remove();

            input.val(updates.shift());

            for (var i = 0; i < updates.length; i++)
            {
              $(input
                  .first()
                  .clone()
                  .removeAttr('id')
                  .val(updates[i])
                ).insertAfter($(':input[name="' + input_name + '"][type="hidden"]:last'));
            }
          }
        });
          
      $(document).on('click', '.piklist-upload-file-preview .attachment', function(event)
      {
        event.preventDefault();
      
        $(this)
          .parents('.piklist-upload-file-preview:first')
          .prev('.piklist-upload-file-button')
          .trigger('click');
      });
      
      $(document).on('click', '.piklist-upload-file-preview .attachment .check', function(event)
      {
        event.preventDefault();
        
        var index = $($(this).parents('.attachments:eq(0)').children()).index($(this).parents('.attachment:eq(0)')),
          save = $(this).data('attachment-save'),
          name = $(this).data('attachments'),
          value = $(this).data('attachment-' + save);
        
        if ($(':input[name="' + name + '"]').length > 1)
        {
          $(':input[name="' + name + '"][type="hidden"]:eq(' + index + ')').remove();
        }
        else
        {
          $(':input[name="' + name + '"][type="hidden"]:eq(' + index + ')').val('')
        }
        
        $(this)
          .parents('.attachment:first')
          .remove();
      });
      
      $(document).on('click', '.piklist-upload-file-button', function(event)
      {
        event.preventDefault();
      
        var button = $(this);

        if ($('*[id^="__wp-uploader-"]').length > 0)
        {
          $('*[id^="__wp-uploader-"]').remove();
        }
        
        var field = button.next('.piklist-upload-file-preview').children(':input[type="hidden"]:eq(0)'),
          media_frame = wp.media.frames.file_frame = wp.media({
            title: button.attr('title'),
            button: {
              text: button.text(),
            },
            multiple: field.data('multiple')
          });
          
        media_frame.on('select', function()
        {
          var attachments = media_frame.state().get('selection'),
            preview_container = button.next('.piklist-upload-file-preview'),
            input = preview_container.children(':input[type="hidden"]'),
            input_name = input.attr('name'),
            preview = preview_container.children('ul.attachments'),
            updates = [];

          attachments.map(function(attachment) 
          {
            attachment = attachment.toJSON();
        
            if (attachment.sizes)
            {
              var display = attachment.sizes.full;
      
              if (attachment.sizes.thumbnail)
              {
                display = attachment.sizes.thumbnail;
              }
              else if (attachment.sizes.medium)
              {
                display = attachment.sizes.medium;
              }
              else if (attachment.sizes.large)
              {
                display = attachment.sizes.large;
              }
              
              preview.append(
                $('<li class="attachment selected">\
                      <div class="attachment-preview ' + (display.width > display.height ? 'landscape' : 'portrait') + '">\
                        <div class="thumbnail">\
                          <div class="centered">\
                            <a href="#">\
                              <img src="' + display.url + '" />\
                            </a>\
                          </div>\
                        </div>\
                        <button type="button" class="button-link check" data-attachment-id="' + attachment.id + '" data-attachment-url="' + attachment.url + '" data-attachments="' + input.attr('name') + '"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>\
                      </div>\
                   </li>\
                 ')
              );
            }
            else
            {
              var display = attachment;

              preview.append(
                $('<li class="attachment selected">\
                      <div class="attachment-preview attachment-preview-document type-' + display.type + ' subtype-' + display.subtype + ' landscape">\
                         <div class="thumbnail">\
                           <div class="centered">\
                            <img src="' + display.icon + '" class="icon" />\
                          </div>\
                          <div class="filename">\
                             <div>' + display.filename + '</div>\
                          </div>\
                        </div>\
                        <button type="button" class="button-link check" data-attachment-id="' + attachment.id + '" data-attachments="' + input.attr('name') + '"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>\
                      </div>\
                   </li>\
                 ')
              );
            }
            
            updates.push(field.data('save') == 'url' ? attachment.url : attachment.id);
          });
          
          for (var i = 0; i < updates.length; i++)
          {
            if (input.first().val() == '')
            {
              input.first().val(updates[i]);
            }
            else
            {
              $(input
                  .first()
                  .clone()
                  .removeAttr('id')
                  .val(updates[i])
                ).insertAfter($(':input[name="' + input_name + '"]:last'));
            }
          }
        });
      
        media_frame.open();
      });          
    }
  };
  
  $.fn.piklistmediaupload = function(option)
  {
    var _arguments = Array.apply(null, arguments);
    _arguments.shift();
  
    return this.each(function() 
    {
      var $this = $(this),
        data = $this.data('piklistmediaupload'),
        options = typeof option === 'object' && option;
  
      if (!data) 
      {
        $this.data('piklistmediaupload', (data = new PiklistMediaUpload(this, $.extend({}, $.fn.piklistmediaupload.defaults, options, $(this).data()))));
      }
  
      if (typeof option === 'string') 
      {
        data[option].apply(data, _arguments);
      }
    });
  };
  
  $.fn.piklistmediaupload.defaults = {
    multiple: true,
    save: 'id'
  };
  
  $.fn.piklistmediaupload.Constructor = PiklistMediaUpload;
  
  
  
  /* --------------------------------------------------------------------------------
    WordPress Updates
  -------------------------------------------------------------------------------- */
  
  // NOTE: Allow dynamically added editors to work properly with added buttons
  $(document).on('click', '.insert-media.add_media', function(event)
  {
    tinyMCE.get($(this).data('editor')).focus();
  });
  

  // NOTE: WordPress Updates to allow meta boxes and widgets to have tinymce
  $(document)
    .on('sortstart', '.ui-sortable', function(event, ui)
    {
      if ($(this).is('.ui-sortable') && (ui.item.hasClass('postbox') || ui.item.hasClass('piklist-field-addmore-wrapper')))
      {
        $(this).find('.wp-editor-area').each(function()
        {
          if (typeof switchEditors != 'undefined' && typeof tinyMCE != 'undefined')
          {
            var id = $(this).attr('id'),
              command = tinymce.majorVersion == 3 ? 'mceRemoveControl' : 'mceRemoveEditor';
            
            switchEditors.go(id, 'tmce');
            
            tinyMCE.execCommand(command, false, id);
          }
        });
      }
    })
    .on('sortstop sortreceive', '.ui-sortable', function(event, ui)
    {
      if ($(this).is('.ui-sortable') && (ui.item.hasClass('postbox') || ui.item.hasClass('piklist-field-addmore-wrapper')))
      {
        $(this).find('.wp-editor-area').each(function()
        {
          if (typeof switchEditors != 'undefined' && typeof tinyMCE != 'undefined')
          {
            var id = $(this).attr('id'),
              command = tinymce.majorVersion == 3 ? 'mceAddControl' : 'mceAddEditor';

            tinyMCE.execCommand(command, false, id);
          }
        });
      }
    });
  
  
  
  /* --------------------------------------------------------------------------------
    Additional Methods
  -------------------------------------------------------------------------------- */
  
  $.fn.reverse = function() 
  {
    return Array.prototype.reverse.call(this);
  };
  
  $.intersect = function(a, b)
  {
    return $.grep(a, function(i)
    {
      return $.inArray(i, b) > -1;
    });
  };

  $.fn.commonAncestor = function() 
  {
    var current = null,
      compare = this.eq(0).parents().reverse(),
      position = compare.length - 1;

    for (var i = 1, j = this.length; i < j && position > 0; i += 1) 
    {
      current = this.eq(i).parents().reverse();
      position = Math.min(position, current.length - 1);

      while (compare[position] !== current[position]) 
      {
        position -= 1;
      }
    }

    return compare.eq(position);
  };
  
  $.fn.actual = function(dimension) 
  {    
    var $wrap = $('<div />').appendTo($('body')),
      $clone, dimension;
    
    $wrap.css({
      'position': 'absolute',
      'left': '-9999999px',
      'visibility': 'hidden',
      'display': 'block'
    });

    $clone = $(this).clone().appendTo($wrap);

    dimension = typeof dimension != 'undefined' && dimension == 'width' ? $clone.width() : $clone.height();

    $wrap.remove();

    return dimension;
  };
  
})(jQuery, window, document);