(function($) {
    if ( typeof wpLink == 'undefined' )
        return;

    wpLink.htmlUpdate = function() {
        var attrs, html, htmlEnd, begin, end, cursor, selection,
        textarea = wpLink.textarea;

        if (!textarea)
            return;

        attrs = wpLink.getAttrs();
        //console.log(attrs);

        // If there's no href, return.
        if (!attrs.href || attrs.href == 'http://')
            return;

        // Build HTML
        html    = '[';
        htmlEnd = '';

        if (attrs.title)
            htmlEnd += ']('+ attrs.href;
        if (attrs.target)
            htmlEnd += ' '+attrs.target;

        htmlEnd += ')';

        // Insert HTML
        if (document.selection && wpLink.range) {
            // IE
            // Note: If no text is selected, IE will not place the cursor
            //       inside the closing tag.
            textarea.focus();
            wpLink.range.text = html + wpLink.range.text + htmlEnd;
            wpLink.range.moveToBookmark(wpLink.range.getBookmark());
            wpLink.range.select();

            wpLink.range = null;
        } else if (typeof textarea.selectionStart !== 'undefined') {
            // W3C
            begin = textarea.selectionStart;
            end = textarea.selectionEnd;
            selection = textarea.value.substring(begin, end);
            if (begin == end)
                html = html + attrs.title + htmlEnd;
            else
                html = html + selection + htmlEnd;

            cursor = begin + html.length;

            textarea.value = textarea.value.substring(0, begin) + html +
            textarea.value.substring(end, textarea.value.length);

            // Update cursor position
            textarea.selectionStart = textarea.selectionEnd = cursor;
        }

        wpLink.close();
        textarea.focus();
    };
})(jQuery);
