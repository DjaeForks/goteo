/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

$(function(){

    $(".auto-update-projects").on('change', ".interest", function (e) {
        var value = $(this).is(":checked") ? 1 : 0;
        var id = $(this).attr('id');
        var $parent = $(this).closest('.auto-update-projects');
        var $button = $parent.find('.more-projects-button');
        var url = $parent.data('url');
        var limit = $parent.data('limit') || 6;

        $.post(url + '?' + $.param({ limit: limit }), { 'id' : id, 'value' : value  }, function(result) {
            if((result.offset + result.limit) >= result.total) {
                $button.addClass('hidden');
            } else {
                $button.removeClass('hidden');
            }
            $parent.contents('.elements-container').html(result.html);
        });
    });

    $(".auto-update-projects").on('click', ".more-projects-button", function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.auto-update-projects');
        var $button = $parent.find('.more-projects-button');
        var total_elements = $parent.find('.widget-element').length;
        var url = $parent.data('url');
        var total = $parent.data('total');
        var limit = $parent.data('limit') || 6;

        $.get(url, {offset:total_elements, limit: limit}, function(result) {
            if((result.offset + result.limit) >= result.total) {
                $button.addClass('hidden');
            } else {
                $button.removeClass('hidden');
            }
            $parent.contents('.elements-container').append(result.html);
        });
    });


    $(".ajax-comments").on('click', ".send-comment", function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.ajax-comments');
        var $list = $($parent.data('list'));
        var url = $parent.data('url');
        var $error = $parent.find('.error-message');
        var $textarea = $parent.find('[name="message"]');
        var data = {
            message: $textarea.val(),
            thread: $parent.data('thread'),
            project: $parent.data('project')
        }
        $error.addClass('hidden').html('');
        $.post(url, data, function(data) {
            // console.log('ok!', data);
            $list.append(data.html);
            $textarea.val('');
          }).fail(function(data) {
            var error = JSON.parse(data.responseText);
            console.log('error', data, error)
            $error.removeClass('hidden').html(error.error);
          })
    });

});
