

jQuery.fn.listMenu = function() {

    var $this = $(this);

    $this.find('.menu-submenu-link ul').hide();

    $this.find('.menu-submenu-link > a').bind('click', function() {

        var link = $(this);
        var ul = link.next();

        if(ul.is(':visible'))
        {
            ul.slideUp();
            link.find('.icon').removeClass('fa-minus-square').addClass('fa-plus-square');
        }
        else
        {
            ul.slideDown();
            link.find('.icon').removeClass('fa-plus-square').addClass('fa-minus-square');
        }

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
