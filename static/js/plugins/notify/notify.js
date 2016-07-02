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
        html('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-close fa-stack-1x fa-inverse"></i></span>').
        attr({'href': 'javascript:void(0)'}).
        bind('click', function() { block.fadeOut(); }).
        appendTo(closeButtonBlock);
    }

    block.append(messageBlock);
    block.addClass('alert');
    block.addClass('alert-'+options['type']);
    block.css(options['css']);

    block.prependTo('body');

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
    'closeTimeout': 10000,
    'css': {
        'position': 'absolute',
        'box-shadow': '3px 3px 3px rgba(0, 0, 0, 0.8)',
        'font-weight': 'bold',
        'margin': '20px 20px 0 0',
        'padding': '10px',
        'right': '0',
        'z-index': '9999999',
        'opacity': 0.9,
        'border': 'solid 1px #666'

    }

};
