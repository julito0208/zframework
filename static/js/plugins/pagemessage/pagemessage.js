(function(jQuery){

    jQuery.pageMessage = function(text, classname)
    {
        var html = $('<div />').addClass('page-message-block ' + (classname ? classname : '')).css({'opacity': 0.9});
        var textNode = $('<div />').addClass('text').appendTo(html);

        if($.isNode(text))
        {
            $(text).remove().appendTo(textNode);
        }
        else
        {
            textNode.html(text);
        }

        jQuery.fixedStaticBlock(html, jQuery.pageMessage.defaultOptions);
    };


    jQuery.fn.pageMessage = function(classname)
    {
        return jQuery.pageMessage(this, classname);
    };

    jQuery.savePageMessage = function(text, classname)
    {
        var data = {};
        data[jQuery.pageMessage.cookieName] = {};
        data[jQuery.pageMessage.cookieName+'[text]'] = text;
        data[jQuery.pageMessage.cookieName+'[classname]'] = classname;
        Navigation.addCookie(data);
    };

    jQuery.fn.savePageMessage = function(classname)
    {
        return jQuery.savePageMessage($(this).html(), classname);
    };

    jQuery.pageMessage.cookieName = '__pageMessage__';

    jQuery.pageMessage.defaultOptions = {
        closeTimeout: 10,
        animation: 500,
        autoShow: true,
        position: jQuery.fixedStaticBlock.POSITION.RIGHT_TOP,
        closeButton: true
    };

    Navigation.savePageMessage = jQuery.savePageMessage;
    var cookie = Navigation.getCookie(jQuery.pageMessage.cookieName);
    $(document).ready(function() {

        var cookie = Navigation.getCookie(jQuery.pageMessage.cookieName);

        if(cookie)
        {
            jQuery.pageMessage(cookie);
            Navigation.removeCookie(jQuery.pageMessage.cookieName);
            //Navigation.removeCookie(jQuery.pageMessage.cookieName+'[classname]');
        }
    });


})(jQuery);
