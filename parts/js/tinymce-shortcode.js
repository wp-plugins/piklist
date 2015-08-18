(function($) 
{
  tinymce.create('tinymce.plugins.PiklistShortcodePlugin', {
    init: function(editor, url) 
    {
      // NOTE: This allows nested shortcodes with UI to be handled properly.
      editor.on('BeforeSetContent', function( event )
      {
        if (!event.content)
        {
          return;
        }

        event.content = typeof wp.mce.views.toViews != 'undefined' ? wp.mce.views.toViews(event.content) : wp.mce.views.setMarkers(event.content);
      });
    },
    
    getInfo: function() 
    {
      return {
        longname: 'Piklist Shortcode Plugin',
        author: 'Piklist',
        authorurl: 'http://piklist.com',
        infourl: 'http://piklist.com',
        version: tinymce.majorVersion + '.' + tinymce.minorVersion
      };
    }
  });
 
  tinymce.PluginManager.add('piklist_shortcode', tinymce.plugins.PiklistShortcodePlugin);
  
  if (typeof wp.shortcode != 'undefined')
  {
    wp.shortcode.next = function(tag, text, index) 
    {
      var re = wp.shortcode.regexp(tag),
        match, result;

      re.lastIndex = index || 0;
      match = re.exec(text);
    
      if (!match) 
      {
        return;
      }
    
      // NOTE: Added to allow proper parsing of nest shortcodes that have UI
      var _text = text.substr(text.indexOf('[' + match[2] + match[3])),
        _match = _text.match(/\[\/([^\]]+)]/g),
        _open_tag, _open_match, _close_tag, _close_match;

      if (_match)
      {
        for (var i = 0; i < _match.length; i++)
        {
          _open_tag = new RegExp('\\[(\\[?)(' + _match[i].replace(/[\[\/\]']+/g, '') + ')', 'g');
          _open_match = _text.match(_open_tag);
        
          _close_tag = new RegExp('\\' + _match[i], 'g');
          _close_match = _text.match(_close_tag);
        
          if ((_open_match && !_close_match)
              || (!_open_match && _close_match)
              || (_open_match && _close_match && _open_match.length < _close_match.length)
             )
          {
            return;
          }
        }
      }
      // END
    
      // If we matched an escaped shortcode, try again.
      if ('[' === match[1] && ']' === match[7]) 
      {
        return wp.shortcode.next(tag, text, re.lastIndex);
      }

      result = {
        index: match.index,
        content: match[0],
        shortcode: wp.shortcode.fromMatch(match)
      };

      // If we matched a leading `[`, strip it from the match and increment the index accordingly.
      if (match[1]) 
      {
        result.content = result.content.slice(1);
        result.index++;
      }

      // If we matched a trailing `]`, strip it from the match.
      if (match[7]) 
      {
        result.content = result.content.slice(0, -1);
      }

      return result;
    };
  }
  
})(jQuery);