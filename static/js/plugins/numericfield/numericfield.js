(function(jQuery){
	
	jQuery.numericFieldControl = function(node, params) {
		return $(node).numericFieldControl(params);
	};

	jQuery.numericFieldControl.DefaultParams = {
		'signed': false,
		'zero': true,
		'float': false,
		'defaultValue': ''
	};
	
	
	jQuery.fn.numericFieldControl = function(params) {
	
		var params = $.extend({}, jQuery.numericFieldControl.DefaultParams, params);
		
		return $(this).each(function(item) {
			
			var $this = $(this);
			
			var parseText = function(text) {

				if(arguments.length == 0) {
					text = String($this.val()).replace(/^\s*/g, '').replace(/\s*$/g, '');
				} else {
					text = String(text).replace(/^\s*/g, '').replace(/\s*$/g, '');
				}
				
				var value = 0;

				var negative = params['signed'] ? text.substr(0, '-'.length) == '-' : false;
				
				if(params['float']) value = parseFloat(text.replace(/[^0-9\.\,]/g,''));
				else value = parseInt(text.replace(/[^0-9]/g,''));

				if(isNaN(value)) value = '';

				if(negative) value = -value;
				
				if(params['zero'] && (value == '' || value == '0')) {
					value = params['defaultValue'];
				} else if(value == '' || value == '0') {
					value = 0;
				}

				return value;
			};

			$this.bind('change', function() {
				
				var value = parseText();
				
				if(params['zero'] && value == '0') 	value = '';

				$this.val(value);
				
			});
			
			$this.bind('keypress', function(evt) {

				if(!evt.ctrlKey && !evt.shiftKey && !evt.altKey) {
					
					if(!((evt.which >= 48 && evt.which <= 57) || evt.which == 9 || evt.which == 13 || evt.which == 36 || evt.which == 35 || evt.which == 36 || (evt.key && evt.key == 'Tab') || (params['float'] && (evt.which == 46 || evt.which == 44)) || (params['signed'] && (evt.which == 45)) || evt.which == 8 )) {
						evt.preventDefault();
						return false;						
					}
				}

				
			});
			
		});

	};
	
})(jQuery);	
