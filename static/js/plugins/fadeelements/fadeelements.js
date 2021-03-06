(function(jQuery){

	jQuery.fadeElements = function(node, imageDuration, fadeSpeed, initialIndex, selector) {

		if(this == jQuery) return new jQuery.fadeElements(node, imageDuration, fadeSpeed, initialIndex);
				
		var $this = $(node);
		
		if($this.data('__fadeElements__')) return $this;
		$this.data('__fadeElements__', true);
		
		/*------------------------------------------------------------------*/
		
		if(!imageDuration) imageDuration = jQuery.fadeElements.DefaultImageDuration;
		if(!fadeSpeed) fadeSpeed = jQuery.fadeElements.DefaultFadeSpeed;
		if(!initialIndex) initialIndex = jQuery.fadeElements.DefaultInitialIndex;
		if(!selector) selector = jQuery.fadeElements.DefaultSelector;
		
		var elements = $this.children(selector);
		
		if(elements.length == 0)
		{
			return $this;
		}
		else if(elements.length == 1)
		{
			elements.addClass('fade-element fade-visible');
		}
		
		if(initialIndex < 0 || initialIndex >= elements.length)
		{
			initialIndex = 0;
		}
		
		elements.addClass('fade-element fade-hidden');
		elements.eq(initialIndex).removeClass('fade-hidden').addClass('fade-visible');
		
		var actualIndex = initialIndex;
		
		var showNextImage = function()
		{
			var nextIndex = actualIndex+1;
			
			if(nextIndex >= elements.length)
			{
				nextIndex = 0;
			}
			
			elements.eq(actualIndex).fadeOut(fadeSpeed, function() { $(this).addClass('fade-hidden').removeClass('fade-visible'); });
			elements.eq(nextIndex).fadeIn(fadeSpeed, function() { $(this).addClass('fade-visible').removeClass('fade-hidden'); });
			
			actualIndex = nextIndex;
		};
		
		setInterval(showNextImage, imageDuration);
		
		/*------------------------------------------------------------------*/
		
		return $this;
	};
	
	jQuery.fadeElements.DefaultImageDuration = 3000;
	jQuery.fadeElements.DefaultFadeSpeed = 400;
	jQuery.fadeElements.DefaultInitialIndex = 0;
	jQuery.fadeElements.DefaultSelector = '*';
	
	jQuery.fn.fadeElements = function(imageDuration, fadeSpeed, initialIndex) {
		new jQuery.fadeElements(this, imageDuration, fadeSpeed, initialIndex);
		return this;
	};

	
})(jQuery);	
