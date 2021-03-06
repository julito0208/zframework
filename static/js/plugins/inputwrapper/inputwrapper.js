

(function(jQuery){

	
	jQuery.inputWrapper = function() {
					
		var params = {};
		var presets = [];
		
		$.each(arguments, function(index, argument) {
			
			params = $.extend(params, ($.isArray(argument) || (argument && typeof argument == 'string')) ? {preset: argument} : $.extend({}, argument));
			if(params.preset) presets.appendArray($.isArray(params.preset) ? params.preset : String(params.preset).trim().split(' '));
		});
		
	
		$.each(presets.reverse(), function(index, name) {
			params = $.extend({}, jQuery.inputWrapper.presets[String(name).toLowerCase()], params); 
		});
		
		
		if(this == jQuery) return new jQuery.inputWrapper(params);
		
		/*--------------------------------------------------------------------------------------------------------------*/ 
	
		
		var valueInput = $(params.valueInput);
		var textInput = $(params.textInput);
		
		if(textInput.length == 0 && valueInput.length > 0) {
			
			textInput = $("<input type='text' />").attr('id', $.uniqID('text')).insertBefore(valueInput);
			
			if(valueInput.attr('id')) $('label[for='+valueInput.attr('id')+']').attr('for', textInput.attr('id'));
		
			
		} else if(valueInput.length == 0 && textInput.length > 0) {
			
			valueInput = $("<input type='hidden' />").insertAfter(textInput);
		
			if(textInput.attr('name')) {
				valueInput.attr('name', textInput.attr('name'));
				textInput.removeAttr('name');
			}
			
		} else if(valueInput.length == 0 && textInput.length == 0) {
			
			valueInput = $("<input type='hidden' />");
			textInput = $("<input type='text' />").insertAfter(valueInput);
		}
		
		
		/*--------------------------------------------------------------------------------------------------------------*/ 
		
		var $this = this;
		
		
		textInput.addClass('input-border text');

		if(params.value != null) textInput.val(params.value);
		
		if(params.textClass) textInput.addClass(params.textClass);
		
		if(params.textStyle) textInput.css(params.textStyle);
		
			
		var parser = params.parser ? params.parser : function(value) { return value; };
		var formatter = params.formatter ? params.formatter : function(text, value) { return text; };
		
		var defaultText = params.defaultText == null ? '' : String(params.defaultText);
		var defaultTextClass = params.defaultTextClass ? params.defaultTextClass : '';
		
		var defaultValueClass = params.defaultValueClass ? params.defaultValueClass : '';
		
		var disabledClass = params.disabledClass ? params.disabledClass : '';
		
		var disabledText = params.disabledText != null ? params.disabledText : '';
		
		var readOnlyClass = params.readOnlyClass ? params.readOnlyClass : 'read-only';
		
		var prevDisabledText = textInput.val();
		
		var text, value, textEmpty, textFormatted;
		
		
		/*--------------------------------------------------------------------------------------------------------------*/ 
		
		
		this.textInput = textInput;
		
		
		this.valueInput = valueInput;
		
		
		this.getTextInput = function() {
			return textInput;
		};
		
		
		this.getValueInput = function() {
			return valueInput;
		};
		
		
		this.disabled = function(value) {
			
			if(arguments.length > 0) {
				textInput.disabled(value);
				return this;
			
			} else return textInput.disabled();
		};
		
		
		
		this.textEmpty = function() {
			return textEmpty;
		};
		
		
		this.text = function(text) {
			
			if(arguments.length > 0) {
				textInput.changeValue(text);
				textInput.triggerHandler('blur');
				return this;
				
			} else return text;		
		};
		
		
		this.clearText = function() {
			return this.text('');
		};
		
		
		this.reset = this.clearText;
		
		
		this.value = function(newValue) {
			
			if(arguments.length > 0) {
				
				var text = String(newValue);
				this.text(formatter.call(this, text, newValue));
				return this;
				
			} else return value;
		};
		
		
		//this.val = this.value;
		
		
		this.readOnly = function(value) {
			
			if(arguments.length > 0) {
				
				value = Boolean(value);
				textInput.attr('readonly', value);
				
				if(value) textInput.addClass(readOnlyClass);
				else textInput.removeClass(readOnlyClass);
				
				return this;
				
			} else return Boolean(textInput.attr('readonly'));
		}
		
			
		/*--------------------------------------------------------------------------------------------------------------*/ 
		
		var changed = false;
		
		
		
		textInput.bindAll({
			
			'disabled': function() {
				
				valueInput.disabled(textInput.disabled());
				
				if(textInput.disabled()) {

					prevDisabledText = textInput.val();
					textInput.val('').addClass(disabledClass).addClass('text-disabled').val(disabledText);
				
				} else textInput.val('').removeClass(disabledClass).removeClass('text-disabled').val(prevDisabledText);
				
			},
	
			
			'change': function(event) {
				
				var textInputText = textInput.val();
				
				if(textInputText != text) {
					
					text = textInputText;
					value = parser.call(this, text);
					
					textFormatted = formatter.call(this, text, value);
					textFormatted = textFormatted == null ? '' : String(textFormatted);
					
					textEmpty = !textFormatted;
										
					if(!textEmpty) textInput.removeClass(defaultTextClass).removeClass('text-default');
					
					if(value) textInput.removeClass(defaultValueClass).removeClass('value-default');
					
					valueInput.changeValue(value);
				
				} else {
					
					event.stopImmediatePropagation();
				}
				
				
				changed = false;
			},
			
			
			
			'focus focusout': function() {

										
				if(textEmpty || (defaultText != null && textInput.val() == defaultText)) textInput.val('');
				
				textInput.removeClass(defaultTextClass).removeClass(defaultValueClass).removeClass('text-default value-default');
				

				changed = true;	
			},
			
			
			'blur': function() {
							
				if(changed) textInput.trigger('change');
				
				textInput.removeClass('input-border-focused');
							
				if(textEmpty) 
					textInput.val('').
						removeClass(defaultValueClass).
						removeClass('value-default').
						addClass(defaultTextClass).
						addClass('text-default').
						val($this.disabled() ? disabledText : defaultText);
				
				else {
					
					textInput.val('').removeClass(defaultTextClass).removeClass('text-default');
				
					if(!valueInput.val()) textInput.addClass(defaultValueClass).addClass('value-default');
						
					textInput.val(textFormatted);
				}
				
				
			},
			
			
			'keydown': function(evt) {
				
				if(evt.which == $.KEY_TAB) textInput.trigger('change');
			}
		});
		
		
		
		valueInput.bind('disabled', function() { 
			textInput.disabled(valueInput.disabled()); 
		});
			
		
		/*--------------------------------------------------------------------------------------------------------------*/ 
			
		if(this instanceof jQuery.inputWrapper) {
		
			textInput.trigger('change');
			textInput.triggerHandler('blur');			
			
			if(textInput.disabled() || valueInput.disabled()) {
				
				valueInput.attr('disabled',true);
				textInput.attr('disabled',true);
				textInput.trigger('disabled');
				
			}
		}
				
		/*--------------------------------------------------------------------------------------------------------------*/ 
		
		return this;	
	};	
	
	
	jQuery.inputWrapper.presets = {
		
		'optional': {
			
			defaultText: 'No especificar',
			defaultTextClass: 'text-optional',
			formatter: function(text, value) { return value ? String(value) : ''; }
		},
		
		
		'obligatory': {
			
			defaultText: 'Escriba el nombre',
			defaultValueClass: 'error'
			
		},
		
		'number': {
		
			textClass: 'text-numeric numeric',
			parser: Number.parseInt,
			defaultText: '0',
			formatter: function(text, value) { return String(value); }
		},
		
		
		'currency': {
		
			textClass: 'text-numeric numeric',
			parser: function(text) { return Number.parseFloat(String(text).replace(/\./g, ','), ',', ''); },
			formatter: function(text, value) { return Number.format(value, 2, '', ',', false); }
		},
		
		
		'trim': {
			
			parser: function(s) { return String(s).trim(); }
		}
		
	};
	
	
	
	jQuery.fn.inputWrapper = function() {
		
		var params = {};
		
		if(this.is("input[type=hidden]")) params.valueInput = this;
		else if(this.is("input[type=text]")) params.textInput = this;
		else if(this.is('textarea')) params.textInput = this;
		
		return jQuery.inputWrapper.apply(jQuery, $.merge($.makeArray(arguments), [params]));
		
	};
	
	
})(jQuery);