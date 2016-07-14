

jQuery.fn.listMenu = function() {

    var $this = $(this);

    $this.triggerHandler('listMenu.init');

    $this.bind('listMenu.clickItem', function() {

        var closed = true;

        $(this).find('.menu-submenu-link > a').each(function(index, item) {

            var link = $(item);
            var ul = link.next();

            if(ul.hasClass('visible'))
            {
                closed = false;
                return false;
            }
        });

        $this.triggerHandler('listMenu.closeAll', closed);
    });

    $this.bind('listMenu.hideAll', function() {

        $this.find('.menu-submenu-link > a').each(function(index, item) {

            var link = $(item);
            var ul = link.next();

            if(ul.is(':visible'))
            {
                ul.removeClass('visible').slideUp();
                link.find('.icon').removeClass('fa-minus-square').addClass('fa-plus-square');
                $this.triggerHandler('listMenu.hideItem', ul);
            }
        });

        $this.triggerHandler('listMenu.clickItem');
    });

    $this.find('.menu-submenu-link ul').hide();

    $this.find('.menu-submenu-link > a').bind('click', function() {

        var link = $(this);
        var ul = link.next();

        if(ul.is(':visible'))
        {
            ul.removeClass('visible').slideUp();
            link.find('.icon').removeClass('fa-minus-square').addClass('fa-plus-square');
            $this.triggerHandler('listMenu.hideItem', ul);
        }
        else
        {
            ul.addClass('visible').slideDown();
            link.find('.icon').removeClass('fa-plus-square').addClass('fa-minus-square');
            $this.triggerHandler('listMenu.showItem', ul);
        }

        $this.triggerHandler('listMenu.clickItem');
    });

    if($this.hasClass('horizontal') || $this.hasClass('menu-horizontal'))
    {
        $this.find('.menu-submenu-link ul').css({
            //'display': 'block',
            'position': 'absolute'
        });

        $this.find('.menu-submenu-link ul li').css({
            'display': 'block'
        });
    }

    if($this.hasClass('vertical') || $this.hasClass('menu-vertical'))
    {
        $this.find('.menu-submenu-link > a').prepend("<span class='icon fa fa-plus-square'></span>&nbsp;&nbsp;");

        $this.find('.menu-submenu-link li.active').getParent('li.menu-item').find('a .icon').removeClass('fa-plus-square').addClass('fa-minus-square');
    }

    $this.find('.menu-sublink.active').parents().not($this).show();

};
