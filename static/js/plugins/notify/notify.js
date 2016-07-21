(function() {

    $.notify = function(arg1, arg2)
    {
        var userOptions = {};

        if(typeof arg1 == 'string' || (typeof arg1 == 'object' && arg1.jquery))
        {
            userOptions['message'] = arg1;
        }
        else if(typeof arg1 == 'object')
        {
            userOptions = arg1;
        }

        if(arg2 && typeof arg2 == 'string')
        {
            userOptions['type'] = arg2;
        }
        else if(arg2 && typeof arg2 == 'object')
        {
            userOptions = $.extend({}, userOptions, arg2);
        }

        var options = $.extend({}, $.notify.defaultOptions, userOptions);
        options['css'] = $.extend({}, $.notify.defaultOptions['css'], userOptions['css']);

        if(typeof options['closeButton'] == 'string' && String(options['closeButton']).toLowerCase() == 'auto')
        {
            options['closeButton'] = !options['closeTimeout'] || options['closeTimeout'] > 6000;
        }

        var messageBlock = $('<div />').
        addClass('message').
        css({'padding': '20px 25px 20px 20px'});

        if(typeof options['message'] == 'object' && options['message'].jquery)
        {
            messageBlock.append(options['message'].show());
        }
        else
        {
            messageBlock.html(options['message']);
        }

        var block = $('<div />');

        if(options['closeButton'])
        {
            var closeButtonBlock = $('<div />').
            appendTo(block).
            css({'float': 'right'});

            var closeButton = $('<a />').
            addClass('close-button').
            html('<i class="fa fa-close"></i>').
            attr({'href': 'javascript:void(0)'}).
            bind('click', function() { block.fadeOut(); }).
            appendTo(closeButtonBlock);
        }

        if(options['bold'])
        {
            block.css({'font-weight': 'bold'});
        }

        block.append(messageBlock);
        block.addClass('znotify-block notify-block alert alert-'+options['type']);
        block.css(options['css']);
        block.css('opacity', 0);
        block.prependTo('body');
        block.width(options['width']);
        block.height(options['height']);

        switch(String(options['position']).toLowerCase())
        {
            case 'tl':
                block.css({'left': options['space'], 'top': options['space']});
            break;

            case 't':
                block.css({'left': 'calc(50% - ' + (block.width()/2) + 'px)', 'top': options['space']});
            break;

            case 'bl':
                block.css({'left': options['space'], 'bottom': options['space']});
                break;

            case 'b':
                block.css({'left': 'calc(50% - ' + (block.width()/2) + 'px)', 'bottom': options['space']});
                break;

            case 'br':
                block.css({'right': options['space'], 'bottom': options['space']});
            break;

            case 'ml':
                block.css({'left': options['space'], 'top': 'calc(50% - ' + (block.height()/2) + 'px)'});
            break;

            case 'm':
                block.css({'left': 'calc(50% - ' + (block.width()/2) + 'px)', 'top': 'calc(50% - ' + (block.height()/2) + 'px)'});
                break;

            case 'mr':
                block.css({'right': options['space'], 'top': 'calc(50% - ' + (block.height()/2) + 'px)'});
            break;



            case 'tr':
            default:
                block.css({'right': options['space'], 'top': options['space']});
            break;
        }

        block.animate({'opacity': 1});

        if(options['closeTimeout'])
        {
            setTimeout(function() {
                block.fadeOut();
            }, options['closeTimeout']);
        }

    };

    $.fn.notify = function(options)
    {
        $.notify(this, options);
        return this;
    };

    $.notify.defaultOptions = {

        'message': '',
        'type': 'info',
        'closeButton': 'auto',
        'closeTimeout': 6000,
        'bold': true,
        'width': 'auto',
        'height': 'auto',
        'position': 'tr', // tl: topleft, t: top, tr: topright, ml: middleleft, m: middle, mr: middleright, bl: bottomleft, b: bottom, br: bottomright
        'space': 20, // Position space
        'css': {
            'position': 'fixed',
            'box-shadow': '3px 3px 3px rgba(0, 0, 0, 0.8)',
            // 'margin': '20px 0 0 50%',
            'padding': '10px',
            // 'right': '0',
            'z-index': '9999999',
            'opacity': 0.9,
            'border': 'solid 1px #666'

        }

    };


})();