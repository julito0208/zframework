var GMapWrapper = function(node, options) {
	
	var $this = this;
	var node = $(node);
	var canvas = node[0];
	var initPosition;
	var initZoom;
	
	var map;
	var markers = {};
	var defaultMarkerKey = null;
	
	var polygons = [];
	

	/*--------------------------------------------------------------*/
	
	var parsePositionZoom = function(arg1, arg2, arg3) {
		
		var latLng;
		var zoom;
		
		if(arguments.length == 3) {
			
			latLng = new google.maps.LatLng(arg1, arg2);
			zoom = arg3;
			
		} else {
			
			if($.isPlainObject(arg1)) {
				
				latLng = new google.maps.LatLng(arg1['lat'], arg1['lng']);
				
				if(arg1['zoom']) zoom = arg1['zoom'];
				else if(arguments.length == 2) zoom = arg2;
				
			} else if(google.maps.LatLng.prototype.isPrototypeOf(arg1)) {
				
				latLng = arg1;
				
				if(arguments.length == 2) zoom = arg2;
			
			} else if($.isArray(arg1)) {
				
				return parsePositionZoom.apply(this, arg1);
			
			} else {
				
				latLng = new google.maps.LatLng(arg1, arg2);
				
			}
			
		}
		
		return {latLng: latLng, zoom: parseInt(zoom)};
		
	};
	
	/*--------------------------------------------------------------*/
	
	$this.getCenter = function() {
		return map.getCenter();
	};
	
	$this.getZoom = function() {
		return map.getZoom();
	};
	
	$this.setZoom = function(zoom) {
		map.setZoom(zoom);
		return $this;		
	};
	
	$this.setInitialZoom = function(zoom) {
		initialZoom = zoom;
		map.setZoom(zoom);
		return $this;		
	};
	
	$this.getInitialZoom = function() {
		return initialZoom;
	};
	
	$this.getInitialPosition = function() {
		return initialZoom;
	};
	
	$this.setInitialPosition = function(arg1, arg2, arg3) {
		
		var positionZoom = parsePositionZoom.apply(this, arguments);
		
		initialPosition = positionZoom['latLng'];
		map.setCenter(initialPosition);
		
		if(positionZoom['zoom'] != null) {
			
			initialZoom = positionZoom['zoom'];
			map.setZoom(parseInt(initialZoom));
		}
		
		return $this;
	};
	
	
	$this.setInitialCenter = function(arg1, arg2, arg3) {
		return $this.setInitialPosition.apply(this, arguments);		
	};
	
	
	$this.setPosition = function(arg1, arg2, arg3) {
		
		var positionZoom = parsePositionZoom.apply(this, arguments);
		map.setCenter(positionZoom['latLng']);
		
		if(positionZoom['zoom'] != null) {
			map.setZoom(parseInt(positionZoom['zoom']));
		}
		
		return $this;
	};
	
	
	$this.setCenter = function(arg1, arg2, arg3) {
		return $this.setPosition.apply(this, arguments);		
	};
	
	
	$this.setOptions = function(options) {
		map.setOptions(options);
		return $this;
	};
	
	$this.setMapType = function(mapType) {
		map.setMapTypeId(google.maps.MapTypeId[mapType]);
		return $this;
	};
	
	$this.getMapType = function() {
		return map.getMapTypeId().toUpperCase();
	};
	
	
	$this.setDefaultMarkerKey = function(markerKey) {
		defaultMarkerKey = markerKey;
		return $this;
	}
	
	$this.getDefaultMarkerKey = function() {
		return defaultMarkerKey;
	}
	
	
	$this.getMarker = function(markerKey) {

		
		if(markerKey != null && Object.isArray(markerKey)) {
			
			var markersArray = [];
			$.each(markerKey, function(index, key) { markersArray.push($this.getMarker(key)); });
			return markersArray;

		} else {
		
			if(markerKey == null || typeof markerKey == 'string' || typeof markerKey == 'number') {

				if(markerKey == null || markers[markerKey] == null || (typeof markerKey == 'string' && markerKey.toUpperCase() == 'DEFAULT')) {
					markerKey = defaultMarkerKey;
				}

				return markers[markerKey];

			} else if(markerKey != null && typeof markerKey == 'object' && markerKey['rootMarkerObject']) {

				return markerKey['rootMarkerObject'];

			} else if(Object.isPlainObject(markerKey) && Object.hasKey(markerKey, 'position') && Object.hasKey(markerKey, 'zoom') && Object.hasKey(markerKey, 'markerOptions') && Object.hasKey(markerKey, 'marker')) {

				return markerKey;

			} else {

				return null;

			}

		}
		
	};
	
	
	$this.getMarkersKeys = function() {
		
		var keys = [];
		$.each(markers, function(key, marker) { keys.push(key); });
		return keys;
		
	};
	
	
	$this.getMarkersArray = function() {
		
		var markers = [];
		$.each(markers, function(key, marker) { markers.push(marker); });
		return markers;
		
	};
	
	
	$this.getMarkersCount = function() {
		
		var count = 0;
		$.each(markers, function(key, marker) { count+=1; });
		return count;
		
	};
	
//	$this.centerMarker = function(markerKey) {
//		
//		var marker = $this.getMarker(markerKey);
//
//		if(marker != null && Object.isArray(marker)) marker = marker[0];
//		
//		if(marker) {
//			
//			map.setCenter(marker['position']);
//			map.setZoom(marker['zoom']);
//			
//		}
//		
//		return $this;
//	};
	
	
	$this.getMarkerPosition = function(markerKey) {

		var marker = $this.getMarker(markerKey);

		if(marker != null && Object.isArray(marker)) marker = marker[0];
		
		if(marker) {
			
			return marker['marker'].getPosition();
			
		}
		
	};
	
	$this.bindMarkerEventHandler = function(markerKey, eventName, listeners) {
		
		var markersArray = $this.getMarker(markerKey);

		if(!Object.isArray(markersArray)) markersArray = [markersArray];
		if(!Object.isArray(listeners)) listeners = [listeners];
		if(Object.isArray(eventName)) eventName = eventName.join(' ');
		
		var eventsNames = eventName.split(' ');
		
		$.each(markersArray, function(indexMarker, marker) {
			$.each(listeners, function(indexListener, listener) {
				$.each(eventsNames, function(indexEvent, eventN) {
					var gmapMarker = marker['marker'];
					var gmapMarkerListenerDict = gmapMarker['listeners'];
					if(gmapMarkerListenerDict[eventN] == null) gmapMarkerListenerDict[eventN] = [];
					gmapMarkerListenerDict[eventN].push(google.maps.event.addListener(gmapMarker, eventN, listener));
					
				});
			});
		});
		
	};
	
	
	$this.unbindMarkerEventHandler = function(markerKey, eventName, listeners) {
		
		var markersArray = $this.getMarker(markerKey);

		if(!Object.isArray(markersArray)) markersArray = [markersArray];
		if(!Object.isArray(listeners)) listeners = [listeners];
		if(Object.isArray(eventName)) eventName = eventName.join(' ');
		
		var eventsNames = eventName.split(' ');
		
		$.each(markersArray, function(indexMarker, marker) {
			$.each(listeners, function(indexListener, listener) {
				$.each(eventsNames, function(indexEvent, eventN) {
					
					var gmapMarker = marker['marker'];
					var gmapMarkerListenerDict = gmapMarker['listeners'];
					if(gmapMarkerListenerDict[eventN] == null) gmapMarkerListenerDict[eventN] = [];
					
					var listenerRemoveIndex = false;
					
					$.each(gmapMarkerListenerDict[eventN], function(indexSearchListener, listenerObject) {
						if(listenerObject['e'] == listener) {
							listenerRemoveIndex = indexSearchListener;			
							return false;
						}
					});
					
					if(listenerRemoveIndex !== false) {
						var listenerObject = gmapMarkerListenerDict[eventN].pop(listenerRemoveIndex);
						listenerObject.remove();
					}
					
				});
			});
		});
		return $this;
	};
	
	
	
	$this.setMarkerFloatHTML = function(markerKey, html, widgetOptions) {
		
		var markersArray = $this.getMarker(markerKey);

		if(!Object.isArray(markersArray)) markersArray = [markersArray];
		
		$.each(markersArray, function(index, marker) {
			
			var contentBlock = $.followMouseTitle($('<div />').html(html), widgetOptions);
			
			var mouseOverListener = function() {
				contentBlock.show();
			};
			
			var mouseOutListener = function() {
				contentBlock.hide();
			};
			
			$this.bindMarkerEventHandler(marker, 'mouseover', mouseOverListener);
			$this.bindMarkerEventHandler(marker, 'mouseout', mouseOutListener);
			
		});
		
		return $this;
	};




	$this.bindMapEventHandler = function(eventName, listeners) {
		
		if(!Object.isArray(listeners)) listeners = [listeners];
		if(Object.isArray(eventName)) eventName = eventName.join(' ');
		
		var eventsNames = eventName.split(' ');
		
		$.each(listeners, function(indexListener, listener) {
			$.each(eventsNames, function(indexEvent, eventN) {
				var mapListenerDict = map['listeners'];
				if(mapListenerDict[eventN] == null) mapListenerDict[eventN] = [];
				mapListenerDict[eventN].push(google.maps.event.addListener(map, eventN, listener));

			});
		});
		return $this;
	};
	
	
	$this.unbindMapEventHandler = function(eventName, listeners) {
		
		if(!Object.isArray(listeners)) listeners = [listeners];
		if(Object.isArray(eventName)) eventName = eventName.join(' ');
		
		var eventsNames = eventName.split(' ');
		
		$.each(listeners, function(indexListener, listener) {
			$.each(eventsNames, function(indexEvent, eventN) {

				var mapListenerDict = map['listeners'];
				if(mapListenerDict[eventN] == null) mapListenerDict[eventN] = [];

				var listenerRemoveIndex = false;

				$.each(mapListenerDict[eventN], function(indexSearchListener, listenerObject) {
					if(listenerObject['e'] == listener) {
						listenerRemoveIndex = indexSearchListener;			
						return false;
					}
				});

				if(listenerRemoveIndex !== false) {
					var listenerObject = mapListenerDict[eventN].pop(listenerRemoveIndex);
					listenerObject.remove();
				}

			});
		});
		
		return $this;
		
	};
	
	$this.addMarker = function(arg1, arg2, arg3) {
		
		if(arg1 && !(typeof arg1 == 'string' || typeof arg1 == 'number')) {
			
			var callArguments = [];
			callArguments.push($.uniqID());
			callArguments.push(arg1);
			
			if(arguments.length > 1) callArguments.push(arg2);
			
			return $this.addMarker.apply($this, callArguments);
		}
		
		var markerKey = arg1 != null ? arg1 : $.uniqID();
		var positionZoom;
		var zoom;
		var options;
		
		if(arguments.length == 3) {
			
			positionZoom = parsePositionZoom.call(this, arg2);
			options = $.extend({}, GMapWrapper.DefaultMarkerOptions, arg3);
			
		} else {
			
			options = $.extend({}, GMapWrapper.DefaultMarkerOptions, arg2);
			
		}
		
		if(options['center']) {
			
			positionZoom = parsePositionZoom.call(this, options['center']);
			delete options['center'];
			
		}
		
		if(options['position']) {
			
			positionZoom = parsePositionZoom.call(this, options['position']);
			delete options['position'];
			
		}
		
		if(options['zoom']) {
			
			zoom = options['zoom'];
			delete options['zoom'];
			
		}
		
		
		
		
		var marker = {};
		
		markers[markerKey] = marker;
		
		marker['position'] = positionZoom['latLng'];
		marker['zoom'] = zoom != null ? zoom : positionZoom['zoom'];
		marker['markerKey'] = markerKey;
		
		marker['markerOptions'] = {};
		marker['markerOptions']['position'] = marker['position'];
		marker['markerOptions']['draggable'] = options['draggable'];
		marker['markerOptions']['flat'] = options['flat'];
		marker['markerOptions']['map'] = map;
		marker['markerOptions']['cursor'] = options['cursor'];
		
		if(options['icon']) marker['markerOptions']['icon'] = options['icon'];
		
		marker['marker'] = new google.maps.Marker(marker['markerOptions']);
		marker['marker']['rootMarkerObject'] = markers[markerKey];
		marker['marker']['listeners'] = {};
		
		
		if($this.getMarkersCount() == 1 && defaultMarkerKey == null) {
			$this.setDefaultMarkerKey(markerKey);
		}
		
		if(options['float_html']) {
			$this.setMarkerFloatHTML(markerKey, options['float_html'], {classname: options['float_html_classname']});
		}
		
		
		
		if(options['onclick']) {
			
			var handler = function(value) {
				eval('var func = ' + options['onclick'] + ';');
				func.call(this, markerKey);
			}
			
			$this.bindMarkerEventHandler(markerKey, 'click', handler);
		}
		
		
		return marker;
		
	};
	
	$this.resetPosition = function() {
		$this.setCenter(initialPosition);
		$this.setZoom(initialZoom);
		return $this;
	};
	
	$this.removeMarker = function(markerKey) {
		
		var marker = $this.getMarker(markerKey);

		if(marker != null && Object.isArray(marker)) marker = marker[0];
		
		if(marker) {
			
			marker['marker'].setMap(null);
			marker['marker'] = null;
			
			var markerKey = marker['markerKey'];
			delete markers[markerKey];
			
			if(defaultMarkerKey && markerKey == defaultMarkerKey) {
				
				var keys = $this.getMarkersKeys();
				
				if(keys.length > 0) defaultMarkerKey = keys[0];
				else defaultMarkerKey = null;
				
			}
		}

		return $this;
	};
	

	$this.centerMarker = function(markerKey) {
			
		var marker = $this.getMarker(markerKey);

		if(marker != null && Object.isArray(marker)) marker = marker[0];
		
		if(marker) {
			
			if(marker['zoom']) $this.setZoom(marker['zoom']);
			$this.setCenter(marker['marker'].getPosition());
			
		}

		return $this;
		
	};
	
	
	$this.addPolygon = function(points, options) {
		
		var polygon = new google.maps.Polygon($.extend({}, GMapWrapper.DefaultPolygonOptions, options));

		if(points.length > 2) {

			$.each(points, function(index, point) {
				var positionZoom = parsePositionZoom.call(this, point);
				polygon.getPath().push(positionZoom['latLng']);
			});

		}

		polygon.setMap(map);
		
		$.each(['click', 'mousemove', 'mouseout', 'mouseover'], function(index, eventName) {

			google.maps.event.addListener(polygon, eventName, function(evt) {
			
				if(map['listeners'][eventName]) {
					
					$.each(map['listeners'][eventName], function(index1, listener) {
						
						listener['e'].call(map, evt);
						
					});
					
				}
				
			});
		});
		
		polygons.push(polygon);
		
		return polygon;
		
	};
	
	
	$this.getPolygons = function() {
		return polygons;
		
	};
	
	
	$this.removePolygon = function(polygon) {
	
		$.each(arguments, function(index, arg) {
			
			$.each($.isArray(arg) ? arg : [arg], function(index1, polygon) {
		
				var pindex = polygons.indexOf(polygon);
				
				if(pindex >= 0) {
					
					polygons.pop(pindex);
					polygon.setMap(null);
					
				}
				
			});
			
		});
		
	};
	
	
	$this.removeAllPolygons = function() {
		return $this.removePolygon(polygons);
	};
	
	
	$this.containsPoint = function(point) {
		
		var positionZoom = parsePositionZoom.call(this, point);
		return map.getBounds().contains(positionZoom['latLng']);
	};
	
	
	
	$this.getBounds = function() {
		
		var bounds = [];
		
		var mapBounds = map.getBounds();
		var northeast = mapBounds.getNorthEast();
		var southwest = mapBounds.getSouthWest();
		
		bounds.push(new google.maps.LatLng(northeast.lat(), southwest.lng()));
		bounds.push(new google.maps.LatLng(northeast.lat(), northeast.lng()));
		bounds.push(new google.maps.LatLng(southwest.lat(), northeast.lng()));
		bounds.push(new google.maps.LatLng(southwest.lat(), southwest.lng()));
		
		return bounds;
	};
	
	/*--------------------------------------------------------------*/

	var optionsWrapper = $.extend({}, GMapWrapper.DefaultOptions, options);
	
	var optionsWrapperInitialPosition;
	var optionsWrapperInitialZoom;
	var optionsWrapperMapType;
	
	if(optionsWrapper['initialPosition']) {
		optionsWrapperInitialPosition = optionsWrapper['initialPosition'];
		delete optionsWrapper['initialPosition'];
	}
	
	if(optionsWrapper['center']) {
		optionsWrapperInitialPosition = optionsWrapper['center'];
		delete optionsWrapper['center'];
	}
	
	if(optionsWrapper['initialZoom']) {
		optionsWrapperInitialZoom = optionsWrapper['initialZoom'];
		delete optionsWrapper['initialZoom'];
	}
	
	if(optionsWrapper['zoom']) {
		optionsWrapperInitialZoom = optionsWrapper['zoom'];
		delete optionsWrapper['zoom'];
	}

	if(optionsWrapper['mapType']) {
		optionsWrapperMapType = optionsWrapper['mapType'];
		delete optionsWrapper['mapType'];
	}
	
	if(optionsWrapper['mapTypeId']) {
		optionsWrapperMapType = optionsWrapper['mapTypeId'];
		delete optionsWrapper['mapTypeId'];
	}
	
	map = new google.maps.Map(canvas, optionsWrapper);
	map['listeners'] = {};
	
	if(optionsWrapperInitialPosition) {
		$this.setInitialPosition(optionsWrapperInitialPosition);
	}
	
	if(optionsWrapperInitialZoom) {
		$this.setInitialZoom(optionsWrapperInitialZoom);
	}
	
	if(optionsWrapperMapType) {
		$this.setMapType(optionsWrapperMapType);
	}
	
	/*--------------------------------------------------------------*/

	$(canvas).data('__gmap_wrapper__', this);

	return $this;
	
};

/*-------------------------------------------------------------*/


GMapWrapper.DefaultOptions = {
	'mapTypeControlOptions': {mapTypeIds: []}
};

GMapWrapper.DefaultMarkerOptions = {
	'draggable': false,
	'flat': true,
	'cursor': 'default'
};

GMapWrapper.DefaultPolygonOptions = {
	'strokeColor': '#1B3340',
	'strokeOpacity': 0.6,
	'strokeWeight': 2,
	'fillColor': '#4F97BA',
	'fillOpacity': 0.2	
};

/*-------------------------------------------------------------*/

$.fn.GMapWrapper = function(options) {
	
	var obj = $(this).data('__gmap_wrapper__');

	if(!obj) {

		obj = new GMapWrapper(this, options);
		$(this).data('__gmap_wrapper__', obj);

	} else {

		obj.setOptions(options);

	}

	return obj;
};