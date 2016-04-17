(function(jQuery){
	
	jQuery.writeChange = function(node, changeCallback, timeout) {
		
		if(this == jQuery) return new jQuery.writeChange(node, changeCallback, timeout);
				
		/*------------------------------------------------------------------*/

		var $this = this;
		var timeout = timeout ? timeout : jQuery.writeChange.defaultTimeout;
		var textNode = $(node);
		var lastVal = textNode.val();
		var changeTimeout = null;
		
		textNode.data('__textInputKeyDownChange__', $this);
		
		/*------------------------------------------------------------------*/

		textNode.bind('keyup', function(evt) {
			
			var newVal = textNode.val();
			
			if(newVal != lastVal) {
				
				if(changeTimeout) {
					clearTimeout(changeTimeout);
					changeTimeout = null;
				}
				
				changeTimeout = setTimeout(function() {
					
					var textVal = textNode.val();
					
					if(textVal == newVal) {
						
						lastVal = textVal;
						changeTimeout = null;
						textNode.trigger('change');
						
					}
					
				}, timeout);
				
			}
			
		});
		
		
		textNode.bind('change', function(evt) {
			
			if(changeTimeout) {
				clearTimeout(changeTimeout);
				changeTimeout = null;
			}
			
			var newVal = textNode.val();
			
			if(newVal != lastVal) {
				
				lastVal = newVal;
				changeTimeout = null;
				
			} else {
				
				return false;
				
			}
			
		});

		/*------------------------------------------------------------------*/
		
		if(changeCallback) textNode.bind('change', changeCallback);
		
		return $this;
	};
	
	
	
	jQuery.writeChange.defaultTimeout = 600;
	
	
	jQuery.fn.writeChange = function(changeCallback, timeout){

		$(this).each(function() {

			var obj = $(this).data('__textInputKeyDownChange__');

			if(!obj) {
				
				obj = new jQuery.writeChange(this, changeCallback, timeout);
				$(this).data('__textInputKeyDownChange__', obj);
								
			} 
			
		});
		
		return $(this);
	};

})(jQuery);	
