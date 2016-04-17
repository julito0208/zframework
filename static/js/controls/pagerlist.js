$(document).ready(function() {
	
	var pagerChecksBlockTop = $('.pagerlist-check-block');
	
	if(pagerChecksBlockTop.length > 0) {
		
		pagerChecksBlockTop.addClass('pagerlist-check-block-top');
		
		var pagerChecksBlockBottom = pagerChecksBlockTop.clone();
		pagerChecksBlockBottom.removeClass('pagerlist-check-block-top').addClass('pagerlist-check-block-bottom');
		
		if($('.pagerlist-check-block-bottom').length == 0) {
			
			if($('.pagerlist-bottom')) {

				pagerChecksBlockBottom.prependTo('.pagerlist-bottom');

			} else {

				pagerChecksBlockBottom.insertAfter('#pager-list');
			}

		}
	}
	
});