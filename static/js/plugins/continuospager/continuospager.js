(function(jQuery){

	jQuery.fn.continuosPager = function(ajaxParams, initPage){

		if(initPage == null) initPage = 0;

		var page = initPage;

		var scrollBottom = $(this).scrollBottom();
		var isLoading = false;
		var block = $(this);
		var finished = false;


		var testScrollLoadPage = function() {
			if($(window).scrollBottom() >= scrollBottom) {
				loadPage();
			}
		};

		var scrollHandler = function() {

			if(!isLoading) {
				testScrollLoadPage();
			}
		};


		var loadPage = function() {

			if(finished) return;

			if(isLoading) return;

			isLoading = true;

			var loadingBlock = $('<div />').addClass('zwidget-continuos-pager-loading').appendTo(block);

			var loadAjaxParams = $.extend({}, ajaxParams);

			loadAjaxParams['data'] = $.extend({}, ajaxParams['data'], {'page': page});

			loadAjaxParams['dataType'] = 'json';

			loadAjaxParams['success'] = function(data) {

				var html = data['html'];
				var isEmpty = data['empty'];
				var morePages = data['more_pages'];

				loadingBlock.remove();

				if(!(isEmpty && page == initPage))
					block.append(html);

				isLoading = false;

				if(ajaxParams['success']) {
					ajaxParams['success'].apply(this, arguments);
				}
				
				if(!morePages) {

					$(window).unbind('scroll', scrollHandler);
					finished = true;

				} else {

					page = page + 1;
					scrollBottom = block.scrollBottom();
					testScrollLoadPage();

				}
				
				block.trigger('repaint');

			};


			$.ajax(loadAjaxParams);

		};


		loadPage();

		$(window).bind('scroll', scrollHandler);

		return this;

	};

})(jQuery);
