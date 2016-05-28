(function(jQuery){

	jQuery.fn.resolveAlignJustify = function(offset){

		this.each(function(ei, e) {

			$(e).css({'text-align': 'justify'});
			
			var $this = $(this);

			if(!$this.data('resolve-align-justify-repaint-handler')) {
				$this.data('resolve-align-justify-repaint-handler', true);
				$this.bind('repaint', function() { $this.compactElementsHeight(); });
			}

			var resolveJustify = function(offset, setMarginLeft, defaultColumnsLefts) {

				var elements = $this.children('div');

				if(elements.length <= 1) return;

				if(setMarginLeft == null) {
					setMarginLeft = true;
				}
						
				if(offset == null) {
					offset = 0;
					elements.css({'vertical-align': 'top', 'display': 'inline-block', 'position': 'relative'});
					if(setMarginLeft) elements.css({'margin-left': 0});
				} else {
					elements = elements.slice(offset-1);
				}


				var columnsLefts;
				var textAlign;
				textAlign = $this.css('text-align');
				$this.css('text-align', 'left');
				
				if(defaultColumnsLefts != null) {
					
					columnsLefts = defaultColumnsLefts;
					
					
					elements.each(function(index, element) {

						var $element = $(element);
						$element.data('compactElementsHeight_offset', $element.offset());

					});
					
				} else {
				
					columnsLefts = [];
					
					elements.each(function(index, element) {

						var $element = $(element);
						$element.data('compactElementsHeight_offset', $element.offset());

						if(!columnsLefts.contains($element.data('compactElementsHeight_offset')['left'])) {
							columnsLefts.push($element.data('compactElementsHeight_offset')['left']);
						}

					});

					columnsLefts.sort(function(item1,item2) { return item1 > item2;});

				}
				
				$this.css('text-align', textAlign);

				var columnsTopOffset = [];
				var firstColumnLeft = columnsLefts[0];

				$.each(columnsLefts, function(index, value) {
					columnsTopOffset.push(0);
				});

				var rows = [];
				var row = null;
				var lastElement = null;
				var columnsCount = null;

				elements.each(function(index, element) {

					var $element = $(element);
					var offset = $element.data('compactElementsHeight_offset');

					var columnIndex = columnsLefts.indexOf(offset['left']);

					$element.data('compactElementsHeight_columnIndex', columnIndex);
					
					var lastElementColSpan = null;

					if(columnIndex == 0) {

						if(row == null) {

							row = [];
							row.push($element);

						} else {
							
							rows.push(row);
							row = [];
							row.push($element);
						}

					} else {

						if(row == null) {
							row = [];
						}

						row.push($element);

					}


					lastElement = $element;
				});
				
				if(row) {

					rows.push(row);

					if(lastElement) {

						var columnIndexLastElement = lastElement.data('compactElementsHeight_columnIndex');
						var lastElementRight = lastElement.data('compactElementsHeight_offset')['left'] + lastElement.outerWidth();
						var lastElementColSpan = 0;

						for(var i=columnIndexLastElement; i<columnsLefts.length;i++) {

							if(lastElementRight > columnsLefts[i]) {
								lastElementColSpan++;
							} else {
								break;
							}

						}

					}


				}


				elements.each(function(index, element) {

					var $element = $(element);
					var offset = $element.data('compactElementsHeight_offset');
					var columnIndex = $element.data('compactElementsHeight_columnIndex');

					var colSpan = 1;
					var elementWidth = $element.innerWidth();
					var elementLeft = columnsLefts[columnIndex];
					var elementRight = elementWidth + elementLeft;

					for(var i=columnIndex+1; i<columnsLefts.length; i++) {

						if(columnsLefts[i] > elementRight) {
							break;
						} else {
							colSpan++;
						}

					}

					$element.data('compactElementsHeight_colSpan', colSpan);
				});

				if(rows.length > 1)
				{
					elements.css({'margin-right': '0'});
				}
				else
				{
					elements.css({'margin-right': ''}).filter(':last-child').css({'margin-right': '0'});
				}

				var lastRowHeight = 0;

				$.each(rows, function(rowIndex, row) {

					var rowHeight = 0;
					var lastColumnIndex = null;

					$.each(row, function(colIndex, element) {

						var height = element.outerHeight();

						if(height > rowHeight) {
							rowHeight = height;
						}

					});

					if(rowIndex == 0) {

						$.each(row, function(colIndex, element) {

							var columnIndex = element.data('compactElementsHeight_columnIndex');
							var colSpan = parseInt(element.data('compactElementsHeight_colSpan'));

							for(var i=0; i<colSpan; i++) {
								columnsTopOffset[i+columnIndex]+= rowHeight-element.outerHeight();
							}

							lastColumnIndex = columnIndex+colSpan-1;

						});

					} else {
						
						
						$.each(row, function(colIndex, element) {


							var columnIndex = element.data('compactElementsHeight_columnIndex');
							var colSpan = element.data('compactElementsHeight_colSpan');

							var columnsIndexs = [];

							var maxBottom = null;

							for(var i=0; i<colSpan; i++) {

								var index = i+columnIndex;

								columnsIndexs.push(index);

								if(maxBottom == null || columnsTopOffset[index] < maxBottom) {
									maxBottom = columnsTopOffset[index];
								}
							}

							var elementBottom = maxBottom;

							for(var i=0; i<colSpan; i++) {

								columnsTopOffset[i+columnIndex] = maxBottom + rowHeight-element.outerHeight();
							}

							lastColumnIndex = columnIndex+colSpan-1;

						});

						if(setMarginLeft && rowIndex > 0 && rowIndex == rows.length-1) {
							
							var justifyOffset = Math.round((((rows[0][rows[0].length-1].outerWidth() + rows[0][rows[0].length-1].position().left) - (row[0].position().left)) / (rows[0].length)) - row[0].outerWidth());
							var columnsLeftMargins = [];

							$.each(rows[0], function(colIndex, element) {

								if(colIndex == 0) {

									columnsLeftMargins.push(0);

								} else {

									var prevElement = rows[0][colIndex-1];
									var actualElement = rows[0][colIndex];

									columnsLeftMargins.push(actualElement.position().left - (prevElement.position().left + prevElement.outerWidth()));

								}

							});
							
							
							$.each(row, function(colIndex, element) {

								if(colIndex > 0) {
									
									element.css('margin-left', columnsLeftMargins[colIndex]-4);
									
								}

							});
						} 

					}

					if(lastColumnIndex < columnsLefts.length-1 && rowIndex < rows.length-1) {
						for(var i=lastColumnIndex+1; i<columnsLefts.length; i++) {
							columnsTopOffset[i]+=rowHeight;

						}
					}

				});

				if(setMarginLeft) {
					resolveJustify(offset, false, columnsLefts);
				}

			};


			resolveJustify(null);

			
			$this.find('img').each(function(index, img) {

				var $img = $(img);

				if(!$img.data('resolve-align-justify-img-onload')) {

					$img.data('resolve-align-justify-img-onload', true);
					$img.bind('load', function () { resolveJustify(null, true);	});
				}

			});


			return $this;


		});
		
		return this;
		
	};

	jQuery.resolveAlignJustify = function(node, offset) {
		return $(node).resolveAlignJustify(offset);
	};
	
	jQuery.resolveAlignJustify.Classname = 'resolve-align-justify';

	jQuery.resolveAlignJustify.Load = function() {
		
		var blocks = $('.' + jQuery.resolveAlignJustify.Classname);
		blocks.addClass('repaint');
		blocks.each(function(index, element) {
			var $element = $(element);
			if(!$element.data('resolve-align-justify-repaint-handler')) {
				$element.data('resolve-align-justify-repaint-handler', true);
				$element.bind('repaint', function() { $element.resolveAlignJustify(null); });
			}			
			$element.resolveAlignJustify();
		});
		
		
	};

})(jQuery);

$(document).ready(function() {
	jQuery.resolveAlignJustify.Load();
	jQuery.resolveAlignJustify.Load();
});