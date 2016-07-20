$.notify = function(options)
{
    options = $.extend({}, $.notify.defaultOptions, options);
    options['css'] = $.extend({}, $.notify.defaultOptions['css'], options['css']);

    var messageBlock = $('<div />').
    addClass('message').
    css({'padding': '20px 25px 20px 20px'}).
    html(options['message']);

    var block = $('<div />');

    if(options['closeButton'])
    {
        var closeButtonBlock = $('<div />').
        appendTo(block).
        css({'float': 'right'});

        var closeButton = $('<a />').
        addClass('close-button').
        html('<span class=""><i class="fa fa-close"></i></span>').
        attr({'href': 'javascript:void(0)'}).
        bind('click', function() { block.fadeOut(); }).
        appendTo(closeButtonBlock);
    }

    block.append(messageBlock);
    block.addClass('notify-block');
    block.addClass('alert');
    block.addClass('alert-'+options['type']);
    block.css(options['css']);
    block.css('opacity', 0);
    block.prependTo('body');
    block.animate({'opacity': 1});

    if(options['closeTimeout'])
    {
        setTimeout(function() {
            block.fadeOut();
        }, options['closeTimeout']);
    }

};

$.notify.defaultOptions = {

    'message': '',
    'type': 'info',
    'closeButton': true,
    'closeTimeout': 5000,
    'css': {
        'position': 'fixed',
        'box-shadow': '3px 3px 3px rgba(0, 0, 0, 0.8)',
        'font-weight': 'bold',
        'font-weight': 'bold',
        'margin': '20px 20px 0 0',
        'padding': '10px',
        'right': '0',
        'z-index': '9999999',
        'opacity': 0.9,
        'border': 'solid 1px #666'

    }

};
