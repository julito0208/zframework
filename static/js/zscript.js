
// Constantes de Teclas

jQuery.KEY_ESC = 27;
jQuery.KEY_ENTER = 13;
jQuery.KEY_SPACE = 32;
jQuery.KEY_UP = 38;
jQuery.KEY_DOWN = 40;
jQuery.KEY_LEFT = 37;
jQuery.KEY_RIGHT = 39;
jQuery.KEY_PGDOWN = 34;
jQuery.KEY_PGUP = 33;
jQuery.KEY_HOME = 36;
jQuery.KEY_END = 35;
jQuery.KEY_SUPR = 46;
jQuery.KEY_BACKSPACE = 8;
jQuery.KEY_SHIFT = 16;
jQuery.KEY_CTRL = 17;
jQuery.KEY_ALT = 18;
jQuery.KEY_TAB = 9;
jQuery.KEY_INSERT = 45;
jQuery.KEY_BLOQMAYUS = 20,
jQuery.KEY_PLUS = 107;
jQuery.KEY_MINUS = 109;

jQuery.KEY_F1 = 112;
jQuery.KEY_F2 = 113;
jQuery.KEY_F3 = 114;
jQuery.KEY_F4 = 115;

jQuery.KEY_F5 = 116;
jQuery.KEY_F6 = 117;
jQuery.KEY_F7 = 118;
jQuery.KEY_F8 = 119;

jQuery.KEY_F9 = 120;
jQuery.KEY_F10 = 121;
jQuery.KEY_F11 = 122;
jQuery.KEY_F12 = 123;

// Otras configuraciones -----------------------------------------------------------------------------------------------------------------------------

jQuery.MOBILE_MAX_WIDTH = 768;

//  Configuraciï¿½n de Ajax -----------------------------------------------------------------------------------------------------------------------------

$.ajaxSetup({
    'type': 'post'
});

//  Funcionalidades Adicionales -----------------------------------------------------------------------------------------------------------------------------

(function(jQuery){

    // Agregar contenido html directamente ---------------------------------------------------------------------------------

    jQuery.fn.addHtml = function(html) {

        html = String(html);

        this.each(function(){
            var $this = jQuery(this);
            $this.html($this.html() + html);
        });

        return this;
    };


    jQuery.isNode = function(arg, nodeType) {

        if(arg && (typeof arg == 'string' || (typeof arg == 'object' && (arg instanceof jQuery || arg.nodeType)))) {
            arg = jQuery(arg);
            if(arg.length > 0 && (!nodeType || (nodeType && arg.is(nodeType)))) return true;
        }

        return false;

    };

    // Comprobar si un elemento contiene a otro elemento -------------------------------------------------------------------

    jQuery.fn.contains = function(element) {
        return this.has(element).length > 0;
    };



    jQuery.fn.exists = function() {
        return $(document.body).contains(this);
    };


    // Obtiene o establece el nombre de la clase del objeto ----------------------------------------------------------------------------

    jQuery.fn.classname = function(newClassname) {

        if(newClassname != null) {

            if(jQuery.isArray(newClassname)) return this.classes(newClassname);

            newClassname = String(newClassname).replace(/ +/g, ' ').trim();

            this.removeClass(this.classname());
            this.addClass(newClassname);

            return this;

        } else if(this.length > 0) return String(this.get(0).className).replace(/ +/g, ' ').trim();

        else return '';
    };


    // Obtiene o establece las clases del elemento en arrays --------------------------------------------------------------------------

    jQuery.fn.classes = function(newClasses) {

        if(newClasses != null) return this.classname( jQuery.isArray(newClasses) ? newClasses.join(' ') : newClasses );
        else return this.classname().split(' ');
    };



    jQuery.fn.removeAttr = function(name) {

        var _this = this;
        jQuery.each(arguments, function(index, names){
            jQuery.each(jQuery.isArray(names) ? names : [names], function(index2, name){
                _this.each(function() {
                    try { this.removeAttribute(name); }
                    catch(e) {}
                })
            })
        });

        return this;
    };

//
//	// Obtiene o establece el tamaï¿½o de un elemento -----------------------------------------------------------------------------------

    jQuery.fn.dimension = function(arg1, arg2) {

        if(arguments.length > 0) {

            var height, width;

            if(jQuery.isPlainObject(arg1)) {
                width = arg1['width'];
                height = arg1['height'];

            } else if(arguments.length > 1) {
                width = arg1;
                height = arg2;

            } else {
                width = arg1;
                height = arg2;
            }

            if(width == null) width = '';
            if(height == null) height = '';

            return this.css({width: width, height: height});

        } else {

            if(this.get(0) == document && document.body)
                return {
                    width: document.body.clientWidth,
                    height: document.body.scrollHeight};

            else if(this.get(0) == window && document.documentElement)
                return {
                    width: document.documentElement.clientWidth ? document.documentElement.clientWidth : document.documentElement.scrollWidth,
                    height: document.documentElement.clientHeight ? document.documentElement.clientHeight : document.documentElement.scrollHeight};

            else return {
                    width: this.width(),
                    height: this.height()};
        }

    };


//
//	// Obtiene el tamaï¿½o interior de un elemento --------------------------------------------------------------------------------------

    jQuery.fn.innerDimension = function() {
        return {width: this.innerWidth(), height: this.innerHeight()};
    };


//
//	// Obtiene el tamaï¿½o exterior de un elemento --------------------------------------------------------------------------------------

    jQuery.fn.outerDimension = function() {
        return {width: this.outerWidth(), height: this.outerHeight()};
    };




    /* Espacio total que queda a lo alto entre el margen, el border y el padding */
    jQuery.fn.outerHeightSpace = function() {
        return this.outerHeight() - this.height();
    };


    /* Espacio total que queda a lo ancho entre el margen, el border y el padding */
    jQuery.fn.outerWidthSpace = function() {
        return this.outerWidth() - this.width();
    };



    /* Espacio total que queda entre el margen, el border y el padding */
    jQuery.fn.outerSpace = function() {
        return {width: this.outerWidthSpace(), height: this.outerHeightSpace()};
    };







    /* Espacio total de padding a lo alto */
    jQuery.fn.innerHeightSpace = function() {
        return this.innerHeight() - this.height();
    };


    /* Espacio total de padding a lo ancho */
    jQuery.fn.innerWidthSpace = function() {
        return this.innerWidth() - this.width();
    };



    /* Espacio total de padding */
    jQuery.fn.innerSpace = function() {
        return {width: this.innerWidthSpace(), height: this.innerHeightSpace()};
    };




    /* Alto total del contenido (incluye padding) (no se toma en cuenta si hay scroll) */
    jQuery.fn.innerContentHeight = function() {
        return this.length > 0 ? this[0].scrollHeight : null;
    };


    /* Ancho total del contenido (incluye padding) (no se toma en cuenta si hay scroll) */
    jQuery.fn.innerContentWidth = function() {
        return this.length > 0 ? this[0].scrollWidth : null;
    };

    /* Dimensiï¿½n total del contenido (incluye padding) */
    jQuery.fn.innerContentDimension = function() {
        return {width: this.innerContentWidth(), height: this.innerContentHeight()};
    };


    /* Alto total del contenido (no incluye padding) (no se toma en cuenta si hay scroll) */
    jQuery.fn.contentHeight = function() {
        return this.innerContentHeight() - this.innerHeightSpace();
    };


    /* Ancho total del contenido (no incluye padding) (no se toma en cuenta si hay scroll) */
    jQuery.fn.contentWidth = function() {
        return this.innerContentWidth() - this.innerWidthSpace();
    };


    /* Dimensiï¿½n total del contenido (no incluye padding) (no se toma en cuenta si hay scroll) */
    jQuery.fn.contentDimension = function() {
        return {width: this.contentWidth(), height: this.contentHeight()};
    };
//
//
//	/*----------------------------------------------------------------------------------------------------------*/
//
    var parseSizeValue = function(value, coordenate, element, parent) {

        if(value != null) {

            if(typeof value == 'number') return value;

            else {

                var value = String(value).replace(/\s/g, '').toLowerCase();

                if(value == '')	return 0;

                match = value.match(/^(\d+(?:\.\d+)?)(?:px|(\%))?$/);

                if(match) {

                    var number = parseInt(match[1]);
                    if(match[2]) {

                        parent = parent ? jQuery(parent) : jQuery(element).getParent();

                        number = Math.round((parent[coordenate]() * number) / 100);
                    }

                    return number;

                } else return null;
            }

        } else return null;
    };

    var controlMaxMinSize = function(elements, coordenate, type) {


        var coordenate = String(coordenate).toLowerCase();
        var type = String(type).toLowerCase();
        var capitalizedCoordenate = coordenate == 'width' ? 'Width' : 'Height';

        jQuery(elements).each(function() {

            var element = jQuery(this);
            var value = parseSizeValue(this.style[type+capitalizedCoordenate] ? this.style[type+capitalizedCoordenate] : element.css(type+'-'+coordenate), coordenate, this);

            if(value != null) {

                var contentSize = element[coordenate]('')['innerContent'+capitalizedCoordenate]();
                if((type == 'min' && contentSize < value) || (type == 'max' && contentSize > value)) element[coordenate](value);
            }
        });


    };



    jQuery.fn.controlMaxWidth = function() { controlMaxMinSize(this, 'width', 'max'); return this; };

    jQuery.fn.controlMaxHeight = function() { controlMaxMinSize(this, 'height', 'max'); return this; };

    jQuery.fn.controlMinWidth = function() { controlMaxMinSize(this, 'width', 'min'); return this; };

    jQuery.fn.controlMinHeight = function() { controlMaxMinSize(this, 'height', 'min'); return this; };


    jQuery.fn.controlMaxMinSize = function() {
        controlMaxMinSize(this, 'width', 'min');
        controlMaxMinSize(this, 'height', 'min');
        controlMaxMinSize(this, 'width', 'max');
        controlMaxMinSize(this, 'height', 'max');
        return this;
    };

    jQuery(window).bind('repaint', function() { jQuery('.control-size').controlMaxMinSize(); });

    /*----------------------------------------------------------------------------------------- */


    jQuery.fn.parseWidth = function(width) {
        return parseSizeValue(width, 'width', this);
    };


    jQuery.fn.parseHeight = function(height) {
        return parseSizeValue(height, 'height', this);
    };


    jQuery.fn.parseStyleValue = function(name, parent) {

        if(this.length == 0) return null;

        var name = String(name).toLowerCase().replace(/[^a-z]+/g, '');
        var javascriptName, cssName, coordenate;

        if(name == 'maxwidth') { javascriptName = 'maxWidth'; cssName = 'max-width'; coordenate = 'width'; }
        else if(name == 'minwidth') { javascriptName = 'minWidth'; cssName = 'min-width'; coordenate = 'width'; }
        else if(name == 'maxheight') { javascriptName = 'maxHeight'; cssName = 'max-height'; coordenate = 'height'; }
        else if(name == 'minheight') { javascriptName = 'minHeight'; cssName = 'min-height'; coordenate = 'height'; }
        else if(name == 'width') { javascriptName = 'width'; cssName = 'width'; coordenate = 'width'; }
        else if(name == 'height') { javascriptName = 'height'; cssName = 'height'; coordenate = 'height'; }
        else if(name == 'top') { javascriptName = 'top'; cssName = 'top'; coordenate = 'height'; }
        else if(name == 'bottom') { javascriptName = 'bottom'; cssName = 'bottom'; coordenate = 'height'; }
        else if(name == 'left') { javascriptName = 'left'; cssName = 'left'; coordenate = 'width'; }
        else if(name == 'right') { javascriptName = 'right'; cssName = 'right'; coordenate = 'width'; }
        else return null;

        return parseSizeValue(this[0].style.javascriptName ? this[0].style.javascriptName : this.css(cssName), coordenate, this, parent);
    };



    // Obtiene la posiciï¿½n del nodo relativa a su nodo padre -----------------------------------------------------------------

    jQuery.fn.positionRelative = function(ignoreScroll, includeBorder) {

        var position = {top: 0, left: 0};

        try{
            var parent = this.parents().eq(0);
            var childOffset = this.offset();
            var parentOffset = parent.offset();

            position.top = childOffset.top-parentOffset.top;
            position.left = childOffset.left-parentOffset.left;

            if(ignoreScroll) {
                position.top += parent.scrollTop();
                position.left += parent.scrollLeft();
            }

            if(!includeBorder) {

                var borderDimension = {};

                try{ borderDimension.top = parseInt(parent.css('border-top-width')); } catch(e) {}
                try{ borderDimension.left = parseInt(parent.css('border-left-width')); } catch(e) {}

                position.top -= (borderDimension.top != null && !isNaN(borderDimension.top) ? borderDimension.top : 0);
                position.left -= (borderDimension.left != null && !isNaN(borderDimension.left) ? borderDimension.left : 0);
            }

        } catch(e) {}


        position.bottom = position.top + this.outerHeight();
        position.right = position.left + this.outerWidth();

        return position;
    };


    // Obtiene la posiciï¿½n de un nodo descendiente relativa a un nodo ancestro ---------------------------------------------

    jQuery.fn.positionRelativeTo = function(ancestor, ignoreScroll, includeBorder) {

        var $this = this.eq(0);
        ancestor = jQuery(ancestor).eq(0);

        if(ancestor.get(0) == window) {

            return $this.offset();

        } else {

            if(this.length == 0 || ancestor.length == 0 || !ancestor.contains($this)) return {};

            ancestor = ancestor.get(0);
            var node = $this;
            var position = {top: 0, left: 0};

            while(true) {

                var parent = node.parents().eq(0);
                var lastParent = parent.get(0) == ancestor;
                var nodePosition = node.positionRelative(ignoreScroll, lastParent ? includeBorder : true);

                position.top += nodePosition.top;
                position.left += nodePosition.left;

                if(lastParent) break;

                node = parent;
            }

            position.top = parseInt(position.top);
            position.left = parseInt(position.left);
            position.bottom = position.top + this.outerHeight();
            position.right = position.left + this.outerWidth();

            return position;

        }
    };



    jQuery.fn.positionAbsolute = function(ignoreScroll) {
        return this.positionRelativeTo(document.body, ignoreScroll, true);
    };


    jQuery.fn.getMousePosition = function(evt) {

        var absPos = this.positionAbsolute();
        return {left: evt.pageX - absPos.left, top: evt.pageY - absPos.top};

    };


    // Desliza (scroll) un elemento padre del nodo para asegurar que que sea visible ----------------------------------------

    jQuery.fn.scrollView = function(relativeParent, scrollType) {

        this.each(function(){

            var $this = jQuery(this);
            var parent = relativeParent ? jQuery(relativeParent) : $this.parents().eq(0);

            var parentSize = {width: parent.width(), height: parent.height()};
            var parentScroll = {top: parent.scrollTop(), left: parent.scrollLeft()};
            parentScroll.bottom = parentScroll.top + parentSize.height;
            parentScroll.right = parentScroll.left + parentSize.width;

            var childSize = {width: $this.outerWidth(), height: $this.outerHeight()};
            var childPosition = $this.positionRelativeTo(parent, true, false);
            childPosition.bottom = childPosition.top + childSize.height;
            childPosition.right = childPosition.left + childSize.width;

            if(scrollType != 'vertical') {
                if(childPosition.right > parentScroll.right) parent.scrollLeft(childPosition.right-parentSize.width);
                else if(childPosition.left < parentScroll.left) parent.scrollLeft(childPosition.left);
            }

            if(scrollType != 'horizontal') {
                if(childPosition.bottom > parentScroll.bottom) parent.scrollTop(childPosition.bottom-parentSize.height);
                else if(childPosition.top < parentScroll.top) parent.scrollTop(childPosition.top);
            }

        });

        return this;
    };


    jQuery.fn.scrollBottom = function() {
        return $(this).scrollTop() + $(this).height();
    };


    jQuery.fn.scrollRight = function() {
        return $(this).scrollLeft() + $(this).width();
    };


    // Desliza (scroll) sï¿½lo verticalmente un elemento padre del nodo para asegurar que que sea visible --------------------

    jQuery.fn.scrollViewHeight = function(relativeParent) {
        return this.scrollView(relativeParent, 'vertical');
    };


    // Desliza (scroll) sï¿½lo horizontalmente un elemento padre del nodo para asegurar que que sea visible ------------------

    jQuery.fn.scrollViewWidth = function(relativeParent) {
        return this.scrollView(relativeParent, 'horizontal');
    };


    // Desliza (scroll) sï¿½lo verticalmente la ventana para asegurar que que sea visible --------------------

    jQuery.fn.scrollWindowViewHeight = function() {
        return this.scrollView(window, 'vertical');
    };


    // Desliza (scroll) sï¿½lo horizontalmente la ventana para asegurar que que sea visible ------------------

    jQuery.fn.scrollWindowViewWidth = function() {
        return this.scrollView(window, 'horizontal');
    };


    jQuery.fn.scrollViewTop = function(relativeParent) {


        this.each(function(){

            var $this = jQuery(this);
            var parent = relativeParent ? jQuery(relativeParent) : $this.parents().eq(0);

            var totalParentHeight = parent.innerContentHeight();
            var parentHeight = parent.height();
            var maxParentScrollTop = totalParentHeight - parentHeight;

            if(maxParentScrollTop <= 0) return;

            parent.scrollTop(Math.min($this.positionRelativeTo(parent, true, false).top, maxParentScrollTop));

        });

        return this;
    };


    jQuery.fn.scrollWindowTop = function() {
        $(window).scrollTop($(this).positionAbsolute().top);
    };

    jQuery.fn.scrollWindowLeft = function() {
        $(window).scrollLeft($(this).positionAbsolute().left);
    };

    // Obtiene el nombre de la etiqueta (tagname) de los elementos ---------------------------------------------------------

    jQuery.fn.tagname = function() {

        if(this.length > 0) return this.get(0).tagName;
        else return null;

    };

    jQuery.fn.href = function(href) {

        if(arguments.length > 0) return href == null ? this.removeAttr('href') : this.attr('href', href);
        else return this.attr('href');
    };


    jQuery.fn.id = function(id) {

        if(arguments.length > 0) return id == null ? this.removeAttr('id') : this.attr('id', id);
        else return this.attr('id');
    };



    jQuery.fn.changeValue = function(value, fireChangeEvent) {

        var valueFunction = jQuery.isFunction(value) ? value : function(index, currentValue) { return value; };
        var fireChangeEvent = fireChangeEvent == null ? true : Boolean(fireChangeEvent);

        return this.each( function(index, currentValue) {

            var currentValue = (this.value == null || typeof this.value == 'undefined') ? '' : String(this.value);
            var newValue = valueFunction.call(this, index, currentValue);

            this.value = (newValue == null || typeof newValue == 'undefined') ? '' : String(newValue);

            if(fireChangeEvent && this.value != currentValue) jQuery(this).trigger('change');

        });
    };

    /*----------------------------------------------------------------------------------------- */



    jQuery.uniqID = function(prefix,iterations){

        if(iterations == null) iterations=2;

        var units_base = Math.pow(10,iterations);
        var randomNumber = Number.random(units_base-1);
        var date = new Date();

        return (prefix ? prefix : 'id') + Number.decimalToBase( (date.getTime()*units_base) + randomNumber, 16);
    };


    /*----------------------------------------------------------------------------------------- */

    jQuery.urlencode = function(s) {
        return escape(String(s));
    }

    jQuery.urldecode = function(s) {
        return unescape(String(s));
    }


    /*----------------------------------------------------------------------------------------- */

    jQuery.varDump = function(value,forExport,deep){



        if(value == null){ return 'null'; }
        else if(typeof value == 'string') return (forExport ? ('"' + value.replace(/(\"|\\)/g, '\\$1') + '"') : ("'" + value.replace(/(\')/g, '\\$1') + "'"));
        else if(typeof value == 'number') return String(value);
        else if(typeof value == 'boolean') return (value ? 'true' : 'false');
        else if(typeof value == 'function') return (forExport ? String(value) : 'function');
        else {

            var item_prefix = (arguments.length > 2 ? arguments[2] : '');
            var items = [];
            var obj_delimiters;

            if(jQuery.isArray(value)) {

                obj_delimiters = ['[', ']'];

                jQuery.each(value, function(key,value) {
                    items.push(jQuery.varDump(value, forExport, item_prefix + '   '));
                });


            } else {

                obj_delimiters = ['{', '}'];
                var max_key_length = 0;

                jQuery.each(value, function(key,value){

                    key = (forExport ? jQuery.varDump(String(key),true) : String(key)) + ': ';
                    if(key.length > max_key_length) max_key_length = key.length;

                    items.push([key,value]);
                });

                var blank_key_prefix = ' '.repeat(max_key_length);

                jQuery.each(items, function(key, value) {
                    items[key] = value[0].paddRight(max_key_length, ' ') +  jQuery.varDump(value[1], forExport, ' ' + blank_key_prefix);
                });


            }

            return '\n' + item_prefix + obj_delimiters[0] + items.join(',\n ' + item_prefix) + obj_delimiters[1];
        }
    };

    jQuery.varExport = function(value){
        return jQuery.varDump(value,true);
    };



    jQuery.varKeys = function(value) {

        var keys = [];
        jQuery.each(value, function(key, value){ keys.push(key); });
        return keys;
    };


    /*----------------------------------------------------------------------------------------- */


    jQuery.mergeFunctions = function(values){

        var functions = [], arrayValue;
        for(var i=0; i<arguments.length; i++) {
            arrayValue = jQuery.isArray(arguments[i]) ? arguments[i] : [arguments[i]];
            for(var j=0; j<arrayValue.length; j++)
                if(jQuery.isFunction(arrayValue[j])) functions.push(arrayValue[j]);
        }

        if(functions.length == 1) return functions[0];
        else return function(values) {
            var value;
            for(var i=0; i<functions.length; i++) value = functions[i].apply(this, arguments);
            return value;
        };
    };




    jQuery.concatFunctions = function(values){

        var functions = [], arrayValue;
        for(var i=0; i<arguments.length; i++) {
            arrayValue = jQuery.isArray(arguments[i]) ? arguments[i] : [arguments[i]];
            for(var j=0; j<arrayValue.length; j++)
                if(jQuery.isFunction(arrayValue[j])) functions.push(arrayValue[j]);
        }

        return function(values) {
            for(var i=0; i<functions.length; i++)
                if(functions[i].apply(this, arguments) === false) break;

        };
    };



    /*----------------------------------------------------------------------------------------- */

    jQuery.compactParamMapKeys = function(obj){

        var key_func, value;
        var compacted = arguments.length>2 ? arguments[2] : {};
        var prefix = arguments.length>1 ? arguments[1] : null;

        if(prefix) key_func = function(key){ return prefix + '[' + key + ']'; }
        else key_func = function(key){ return key; }

        for(var key in obj) try{
            value = obj[key]; key = key_func(key);
            if(typeof value == 'object' && Object.prototype.toString.call(value) != '[object Array]') jQuery.compactParamMapKeys(value, key, compacted);
            else compacted[key] = value;

        } catch(e) {}
        return compacted;
    };


    jQuery.extendParamMapKeys = function(obj){
        var extended = {}, key_obj;

        for(var key in obj) try{
            var value = obj[key]; key_obj = extended;
            var keys = String(key).trim().replace(/\]/g, '').split('[');
            var parent_keys = keys.slice(0,-1);
            var value_key = keys[keys.length-1];

            if(value_key){

                for(var j=0; j<parent_keys.length;j++){
                    var key_part = parent_keys[j];
                    if(key_obj[key_part] == null) key_obj[key_part] = {};
                    key_obj = key_obj[key_part];

                }

                key_obj[value_key] = value;
            }


        } catch(e) {}

        return extended;

    };

    /*---------------------------------------------------------------------------------------------------------- */

    jQuery.paramArray = function(obj,opts){

        opts=jQuery.extend({},opts);
        var data_array = [];


        if(typeof obj == 'string'){

            opts=jQuery.extend({}, {avoidDecode:false, decodeSeparator: '&', decodeAssign: '='},opts);

            var strfunc = opts.avoidDecode ? function(v){return v;} : $.urldecode;
            var strparts = obj.trim().trimLeft('?').split(opts.decodeSeparator);
            for(var i=0; i<strparts.length; i++){
                var strpart = strparts[i].split(opts.decodeAssign,2);
                if(strpart.length>1) data_array.push([strfunc(strpart[0]), strfunc(strpart[1])]);

            }

        } else if(obj && Object.prototype.toString.call(obj) === '[object Array]'){

            for(var i=0; i<obj.length; i++)
                if(obj[i] && Object.prototype.toString.call(obj[i]) === '[object Array]' && obj[i].length > 1) data_array.push([obj[i][0], obj[i][1]]);
                else if(opts.name) data_array.push(['',obj[i]]);

        } else if(obj && typeof obj == 'object' && (obj instanceof jQuery || obj.nodeType)) {

            opts=jQuery.extend({}, {allowDisabled:false}, opts);
            var node = jQuery(obj), inputselector='input,select,textarea,submit,button';

            node.add(node.find(inputselector)).each(function(){
                var $this=jQuery(this);
                var name=$this.attr('name'), value=$this.val();

                if(	name &&
                    (opts.allowDisabled || !Boolean($this.attr('disabled'))) &&
                    ( $this.is(':not(:checkbox,:radio)') || $this.is(':checked') ) &&
                    ( ( $this.is('select') && Boolean($this.attr('multiple')) ) || value !== null ) )

                    data_array.push([name,value==null?[]:value]);

            });


        } else if(obj && typeof obj == 'object') {

            jQuery.each(jQuery.compactParamMapKeys(obj), function(key,value){

                if(Object.prototype.toString.call(value) === '[object Array]') jQuery.each(value, function(index,value) { data_array.push([key+'[]',value]); });
                else data_array.push([key,value]);

            });
        }

        if(opts.name)
            for(var i=0; i<data_array.length; i++)
                data_array[i][0] = opts.name + '[' + data_array[i][0].replace(/\]/g, '').split('[').join('][') + ']';

        return data_array;
    };

    jQuery.paramMap=function(obj,opts){

        var map={};
        jQuery.each(jQuery.paramArray(obj,opts), function(index,data){
            var key=data[0];
            var key_array_pos = key.lastIndexOf('[]');
            if(key_array_pos>0 && key_array_pos == key.length-2) {
                key=key.substr(0,key.length-2);
                if(map[key]==null) map[key]=[];
                map[key].push(data[1]);
            } else map[key]=data[1];
        });

        return map;
    };


    jQuery.paramMapExtended=function(obj,opts){
        return jQuery.extendParamMapKeys(jQuery.paramMap(obj, opts));
    }

    jQuery.paramQuery=function(obj,opts){

        opts=jQuery.extend({}, {avoidEncode: false, separator: '&', assign: '=' },opts);

        var array=[], strfunc=opts.avoidEncode?function(v){return v;} : $.urlencode;
        jQuery.each(jQuery.paramArray(obj,opts), function(index,data){

            var str_value = typeof data[1] == 'bool' ? ( data[1] ? '1' : '0') : String(data[1]);
            if(str_value.length > 0) array.push(strfunc(data[0])+opts.assign+strfunc(data[1]));
        });

        return array.join(opts.separator);
    };


    jQuery.paramDecode=function(obj,opts){

        opts=jQuery.extend({}, {avoidEncode:true, separator: '&', assign: '=' },opts);

        return jQuery.extendParamMapKeys(jQuery.paramMap(obj,opts));
    };


    jQuery.fn.paramArray=function(opts){ return jQuery.paramArray(this,opts); };

    jQuery.fn.paramMap=function(opts){ return jQuery.paramMap(this,opts); };

    jQuery.fn.paramMapExtended=function(opts){ return jQuery.paramMapExtended(this,opts); };

    jQuery.fn.readForm=function(opts){ return jQuery.paramMapExtended(this,opts); };

    jQuery.fn.paramQuery=function(opts){ return jQuery.paramQuery(this,opts); };

    jQuery.fn.paramDecode=function(opts){ return jQuery.paramDecode(this,opts); };


    jQuery.JSON = {};

    jQuery.JSON.parse = function(s) {

        s = String(s);
        s = s.replace(/\'([\w+]+)\'/g, '"$1"');
        return JSON.parse(s);

    };

    /*----------------------------------------------------------------------------------------- */


    var paintingNode = null;


    jQuery.fn.repaint = function() {

        if(this.length > 1) {

            for(var i=0; i<this.length; i++){

                this.eq(i).repaint();

            }

            return this;


        } else if(this.length == 1) {


            if(!this.data('__repaint__')) {

                this.data('__repaint__', true);

                this.one('repaint', function() {

                    $(this).data('__repaint__', null);

                });


                this.controlMaxMinSize();
                this.triggerHandler('repaint');


            } else {

                return this;

            }

        } else {

            return this;

        }

        return this;
    };


    jQuery.fn.repaintAll = function() {

        this.repaint().find('.repaint,.control-size').repaint();
        return this;
    };


    jQuery(window).bind('repaint', function() { jQuery('.repaint,.control-size').triggerHandler('repaint'); });

    jQuery(window).bind('resize', function() { $(window).triggerHandler('repaint'); });


    jQuery.repaint = function() { return jQuery(window).repaint(); };


    jQuery.repaintAll = jQuery.repaint;



    jQuery.fn.bindAll = function() {

        if(arguments.length > 0 && typeof arguments[0] == 'string') {

            var handlers = {};
            handlers[arguments[0]] = (arguments.length > 1 && jQuery.isArray(arguments[1])) ? arguments[1] : jQuery.makeArray(arguments).slice(1);
            return this.bindAll(handlers);

        } else {

            var $this = this;

            jQuery.each(arguments, function(index, argument) {
                jQuery.each(argument, function(event, handlers) {
                    jQuery.each(jQuery.isArray(handlers) ? handlers : [handlers], function(index, handler) {
                        $this.bind(event, handler);
                    });
                });
            });

            return this;

        }
    };



    jQuery.fn.unbindAll = function() {

        if(arguments.length > 0 && typeof arguments[0] == 'string') {

            var handlers = {};
            handlers[arguments[0]] = (arguments.length > 1 && jQuery.isArray(arguments[1])) ? arguments[1] : jQuery.makeArray(arguments).slice(1);
            return this.unbindAll(handlers);

        } else {

            var $this = this;

            jQuery.each(arguments, function(index, argument) {
                jQuery.each(argument, function(event, handlers) {
                    jQuery.each(jQuery.isArray(handlers) ? handlers : [handlers], function(index, handler) {
                        $this.unbind(event, handler);
                    });
                });
            });

            return this;

        }
    };


    jQuery.fn.hasHandler = function(eventType, eventHandler) {

        var namespaces;
        var handlers;
        var hasHandler = false;

        var eventsData = this.data('events');
        if(!eventsData) return hasHandler;

        if (eventType.indexOf(".") > -1) {
            namespaces = eventType.split(".");
            eventType = namespaces.shift();
            namespaces = namespaces.slice(0).sort().join(".");

        } else namespaces = '';

        handlers = eventsData[eventType];
        if(!handlers) return hasHandler;


        jQuery.each(handlers, function(index, handlerObject){

            if((namespaces == '' || (namespaces && namespaces == handlerObject.namespaces)) && handlerObject.handler === eventHandler){
                hasHandler = true;
                return false;
            }

        });

        return hasHandler;

    };


    jQuery.fn.bindOnce = function() {

        if(arguments.length > 0 && typeof arguments[0] == 'string') {

            var handlers = {};
            handlers[arguments[0]] = (arguments.length > 1 && jQuery.isArray(arguments[1])) ? arguments[1] : jQuery.makeArray(arguments).slice(1);
            return this.bindOnce(handlers);

        } else {

            var $this = this;

            jQuery.each(arguments, function(index, argument) {

                jQuery.each(argument, function(events, handlers) {

                    var events = jQuery.isArray(events) ? events : String(events).replace(/\s+/g, ' ').split(' ');

                    jQuery.each(jQuery.isArray(handlers) ? handlers : [handlers], function(index, handler) {

                        jQuery.each(events, function(index, event) {

                            $this.each(function() {

                                var _this = jQuery(this);
                                if(!_this.hasHandler(event, handler)) _this.bind(event, handler);

                            });

                        });

                    });
                });

            });

            return this;

        }
    };

    jQuery.fn.disabled=function(disabled){

        if(arguments.length == 0) return Boolean(this.attr('disabled'));

        else {

            disabled = Boolean(disabled);

            return this.each(function() {

                var $this = $(this);

                try {

                    if($this.attr('disabled') != disabled) {

                        $this.attr('disabled', disabled);
                        $this.triggerHandler('disabled');
                    }

                } catch(ex) {}

            });



        }
    };



    jQuery.fn.checked=function(checked){
        if(arguments.length == 0) return Boolean(this.is(':checked'));
        else return this.attr('checked', Boolean(checked));
    };


    jQuery.fn.hidden = function(hidden){
        if(arguments.length > 0) {

            var visibilityValue = (hidden == '' || hidden == 'auto' || hidden == 'inherit') ? '' : (hidden ? 'hidden' : 'visible');
            this.css('visibility', visibilityValue);
            this.triggerHandler('css.visibility');
            return this;

        } else {

            return this.css('visibility') == 'hidden';

        }
    };




    jQuery.fn.display = function(display){
        if(arguments.length > 0) return this.css('display', (display == '' || display == 'auto' || display == 'inherit') ? '' : (display ? '' : 'none'));
        else return this.css('display') != 'none';
    };

    /*----------------------------------------------------------------------*/


    jQuery.fn.loadImage = function(src, callback) {
        return this.one('load', callback ? callback : function() {}).attr('src', src);
    };


    jQuery.fn.adjustMaxWidth = function(findChildren) {

        if(findChildren)
        {
            var nodes = this.find('[data-max-width]').add(this.filter('[data-max-width]'));
            var maxWidthIds = [];

            nodes.each(function() {

                var maxWidthId = $(this).attr('data-max-width');

                if(maxWidthIds.indexOf(maxWidthId) == -1)
                {
                    maxWidthIds.push(maxWidthId);
                }

            });

            $.each(maxWidthIds, function(index, maxWidthId) {
                nodes.filter('[data-max-width=' + maxWidthId + ']').adjustMaxWidth();
            });
        }
        else
        {

            var maxWidth = 0;

            this.each(function () {

                var $this = $(this);

                var width = $this.css({'width': ''}).width();

                if (width > maxWidth) maxWidth = width;

            });


            this.width(maxWidth);
        }

        return this;

    };

    jQuery.fn.adjustChildrenMaxWidth = function()
    {
        return this.adjustMaxWidth(true);
    };

    jQuery.adjustChildrenMaxWidth = function()
    {
        return $('body').adjustChildrenMaxWidth();
    };

    jQuery(document).ready(function() {
        jQuery.adjustChildrenMaxWidth();
    });

    jQuery.fn.hoverClass = function(classname) {

        return this.each(function() {
            $(this).
            bind('mouseenter', function() { jQuery(this).addClass(classname); }).
            bind('mouseleave', function() { jQuery(this).removeClass(classname); });
        });
    };


    jQuery.fn.liveHoverClass = function(classname) {

        return this.
        live('mouseenter', function() { jQuery(this).addClass(classname); }).
        live('mouseleave', function() { jQuery(this).removeClass(classname); });
    };



    jQuery.fn.focusClass = function(classname) {

        return this.
        bind('focusin', function() { jQuery(this).addClass(classname); }).
        bind('focusout', function() { jQuery(this).removeClass(classname); });
    };

    jQuery.fn.getParent = function(selector) {
        return $(this).parents(selector).eq(0);
    };

    jQuery.fn.getMatchNode = function(selector) {

        if(this.is(selector))
        {
            return this;
        }
        else
        {
            return this.getParent(selector);
        }
    };


    jQuery.fn.fillWindowHeight = function(fillHeight) {

        var $this = $(this);
        var $all = $(this).add('.fill-window-height').add('.fill-parent-height');

        if($this.length == 0) return this;

        if(!$this.data('__fill_height_fn__') || ($this.is('[data-fill-height-offset]') && parseInt($this.attr('data-fill-height-offset')) != $this.data('__fill_height_data_fill_height__'))) {

            $this.data('__fill_height_fn__', parseInt($this.attr('data-fill-height-offset')));

            var fillHeightFn = function() {

                this.each(function() {

                    var $this = $(this);
                    var offsetHeight = 0;

                    if(!fillHeight && $this.is('[data-fill-height-offset]'))
                    {
                        offsetHeight = parseInt($this.attr('data-fill-height-offset'));
                    }

                    $all.css('display', 'none');
                    var documentHeight = $(document.body).outerHeight();

                    $all.css('display', '');

                    var avalHeight = $(window).outerHeight();

                    $this.css({'min-height': avalHeight - documentHeight + (offsetHeight ? offsetHeight : 0)});

                    $this.trigger('fillwindowheight');

                });

            };

            $this.data('__fill_height_fn__', fillHeightFn);

            $this.data('__fill_height_fn__').call($this);

            $(window).bind('resize', function() {
                $this.data('__fill_height_fn__').call($this);
            });

        } else {

            $this.data('__fill_height_fn__').call($this);

        }

        return $this;

    };


    jQuery.fn.fillParentHeight = function(fillHeight) {

        var $this = $(this);

        if($this.length == 0) return this;

        if(!$this.data('__fill_pheight_fn__')) {

            var fillHeightFn = function() {

                this.each(function() {

                    var $this = $(this);
                    var offsetHeight = 0;

                    if (!fillHeight && $this.is('[data-fill-height-offset]')) {
                        offsetHeight = parseInt($this.attr('data-fill-height-offset'));
                    }

                    $this.css('display', 'none');
                    var avalHeight = $this.getParent().outerHeight();
                    $this.css('display', '');
                    $this.css({'min-height': avalHeight + (offsetHeight ? offsetHeight : 0)});

                });

            };

            $this.data('__fill_pheight_fn__', fillHeightFn);

            $this.data('__fill_pheight_fn__').call($this);

            $(window).bind('resize', function() {
                $this.data('__fill_pheight_fn__').call($this);
            });

            $this.getParent().bind('resize repaint', function() {
                $this.data('__fill_pheight_fn__').call($this);
            });

        } else {

            $this.data('__fill_pheight_fn__').call($this);

        }

        return $this;

    };

    /*----------------------------------------------------------------------------------------------------------------------------------------------- */

    jQuery.fn.getZIndex = function()
    {
        var $this = $(this);
        var zIndex = $this.css('z-index');

        if(zIndex)
        {
            return parseInt(zIndex);
        }
        else
        {
            var parent = $this.getParent();

            if(parent.length > 0)
            {
                return parent.getZIndex();
            }
            else
            {
                return 0;
            }
        }


    };

    jQuery.escapeSelector = function(selector)
    {
        return String(selector).replace( /(:|\.|\[|\]|,)/g, "\\$1" );
    }

    /*----------------------------------------------------------------------------------------------------------------------------------------------- */

    /* For comp */
    jQuery.fn.live = function(event, handler)
    {
        $(document).on(event, this, handler);
        return this;
    };

    /*----------------------------------------------------------------------------------------------------------------------------------------------- */
    /* Atajos para ajax */

    $.ajaxPost = function(url, data, success, error, complete)
    {
        return $.ajax({url:url, data:data, type: 'post', success: success, error: error, complete: complete});
    };

    /*----------------------------------------------------------------------------------------------------------------------------------------------- */

    $(document).on('resize', $('.fixed-height'), function(evt)
    {
        var $this = $(evt.target);
        var $height = $this.outerHeight();
        var $parent = $this.getParent('.fixed-height-container');
        $parent.height($height);
        evt.stopPropagation();
        return false;
    });

    $(window).bind('resize', function()
    {
        $('.fixed-height').trigger('resize');
    })


    $(document).ready(function() {
        $(window).triggerHandler('resize');
        $('.first-focus').focus();
    });

    /*-------------------------------------------------------------*/



})(jQuery);

$(document).ready(function() {
    $('.fill-window-height').fillWindowHeight();
    $('.fill-parent-height').fillParentHeight();
});

/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones adicionales de objetos ------------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */


/* Controla si un objeto es un objeto plano */
Object.isPlainObject = function() {
    return jQuery.isPlainObject.apply(jQuery, arguments);
};


/* Controla si un objeto es un array */
Object.isArray = function() {
    return jQuery.isArray.apply(jQuery, arguments);
};


/* Aplica una funciï¿½n a cada miembro de un objeto */
Object.each = function(obj, handler) {
    return jQuery.each((jQuery.isPlainObject(obj) || jQuery.isArray(obj)) ? obj : [obj], handler);
};


/* Crea un objeto plano desde los argumentos. Se pueden pasar arrays de dos elementos de forma: [key, value] */
Object.create = function() {

    var object = {};

    $.each(arguments, function(index, argument) {

        if($.isArray(argument)) object[argument[0]] = argument[1];
        else object = $.extend(object, argument);

    });

    return object;
};


/* Obtiene los nombres de los atributos de un objeto */
Object.getKeys = function(obj) {

    var keys = [];

    try{ for(var key in obj) keys.push(key); }
    catch(e) {}

    return keys;
};


/* Crea un objeto a partir de nombres de claves y un valor por defecto */
Object.fromKeys = function(keys, defaultValue){

    if(Object.isPlainObject(keys)) keys = Object.getKeys(keys);
    else if(!Object.isArray(keys)) keys = [keys];

    var obj = {};
    for(var i=0; i<keys.length; i++) obj[keys[i]] = defaultValue;

    return obj;
};


/* Controla si un objeto tiene un miembro */
Object.hasKey = function(obj, searchKey) {

    if(searchKey != null)
        try{ for(key in obj) if(key == searchKey) return true; }
        catch(e) {}

    return false;
};


/* Obtiene el valor de un miembro de un objeto (se devuelve defaultValue si no existe) */
Object.getValue = function(obj, key, defaultValue) {

    if(Object.hasKey(obj, key)) return obj[key];
    else return defaultValue;

};


/* Obtiene el valor de un miembro de un objeto y se elimina (se devuelve defaultValue si no existe) */
Object.pop = function(obj, key, defaultValue) {

    if(Object.hasKey(obj, key)) {

        var value = obj[key];
        delete obj[key];
        return value;

    } else return defaultValue;
};


/* Obtiene la lista de los valores de los miembros de un objeto */
Object.getValues = function(obj) {

    var values = [];
    Object.each(obj, function(key, value) { values.push(value); });
    return values;
};


/* Copia un objeto (array o plano) */
Object.copy = function(obj) {

    if(jQuery.isPlainObject(obj) || jQuery.isArray(obj)) {

        var copy = jQuery.isArray(obj) ? [] : {};
        jQuery.each(obj, function(key, value) { copy[key] = value; } );
        return copy;

    } else return obj;

};


/* Copia recursivamente un objeto */
Object.deepcopy = function(obj) {

    if(jQuery.isPlainObject(obj) || jQuery.isArray(obj)) {

        var copy = jQuery.isArray(obj) ? [] : {};
        jQuery.each(obj, function(key, value) { copy[key] = Object.deepcopy(value); } );
        return copy;

    } else return obj;

};



Object.filter = function(obj, callback, recursive) {

    if(!callback) callback = $.isNotNull;

    var filtered;

    if($.isPlainObject(obj)) {

        filtered = {};

        $.each(obj, function(key, value) {
            if(callback.call(obj, value, key))
                filtered[key] = (recursive && ($.isPlainObject(value) || $.isArray(value))) ? Object.filter(value, callback, recursive) : value;

        });

    } else {

        filtered = [];

        $.each(obj, function(key, value) {
            if(callback.call(obj, value, key))
                filtered.push((recursive && ($.isPlainObject(value) || $.isArray(value))) ? Object.filter(value, callback, recursive) : value);

        });
    }

    return filtered;
};


/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones de Cadenas ------------------------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */


/* Formatea una cadena de acuerdo a argumentos */
String.format = function(format, args) {

    var format = String(format).replaceCallback(/([^\%]|^)\%(s|d)/, function(match, index) { return match[1]+'%$'+String(index+1)+match[2]; });

    var params = {};
    var paramsArray = [];

    for(var i=1; i<arguments.length; i++)
        if(jQuery.isArray(arguments[i])) paramsArray.appendArray(arguments[i]);
        else if(jQuery.isPlainObject(arguments[i])) params = jQuery.extend({}, params, arguments[i]);
        else paramsArray.push(arguments[i]);

    for(var i=0; i<paramsArray.length; i++) params[i+1] = paramsArray[i];

    var formatted = format.replaceCallback(/([^\%]|^)\%(?:(?:\$(\d+))|(?:\((\w+)\)))(s|d)/, function(match, index){

        var paramName = match[2] ? match[2] : match[3];
        var paramType = match[4];

        var paramValue = params[paramName];

        return match[1] + (paramValue == null ? '' : String(paramValue));
    });

    return formatted.replace(/\%\%/g, '%');
};


/*----------------------------------------------------------------------------------------------------------------------------------------------- */

String.charCode = function(s) {
    return String(s).charCodeAt(0);
};


/* Elimina espacios (u otros caracteres) al principio de la cadena */
String.prototype.trimLeft = function(tchar) {
    var string = String(this); if(tchar == null) tchar = ' ';
    if(tchar==' ') return string.replace(new RegExp('^(\\s|\n)+', 'g'), '');
    while(string.charAt(0) == tchar) string = string.substring( 1 );
    return string;
};


/* Elimina espacios (u otros caracteres) al final de la cadena */
String.prototype.trimRight = function(tchar) {
    var string = String(this); if(tchar == null) tchar = ' ';
    if(tchar==' ') return string.replace(new RegExp('(\\s|\n)+$', 'g'), '');
    while(string.charAt(string.length-1) == tchar) string = string.substring( 0, string.length - 1 );
    return string;
};


/* Elimina espacios (u otros caracteres) al principio y al final de la cadena */
String.prototype.trim = function(tchar) {
    return this.trimLeft(tchar).trimRight(tchar);
};


/* Versiï¿½n estï¿½tica de trim */
String.trim = function(s, tchar) {
    return String(s).trim(tchar);
};


/* Controla si la cadena empieza con otra subcadena (opcionalmente si la comparaciï¿½n debe omitir mayï¿½sculas y minï¿½sculas) */
String.prototype.startsWith = function ( prefix, caseInsensitive ) {
    if(!prefix && prefix != '') return false;
    var cadena = String(this);
    if(caseInsensitive) { cadena = cadena.toUpperCase(); prefix = prefix.toUpperCase(); }
    return cadena.substr(0, prefix.length) == prefix;
};


/* Controla si la cadena termina con otra subcadena (opcionalmente si la comparaciï¿½n debe omitir mayï¿½sculas y minï¿½sculas) */
String.prototype.endsWith = function ( sufix, caseInsensitive ) {
    if(!sufix && sufix != '') return false;
    var cadena = String(this);
    if(caseInsensitive) { cadena = cadena.toUpperCase(); sufix = sufix.toUpperCase(); }
    return cadena.substr(cadena.length - sufix.length) == sufix;
};


/* Elimina una subcadena al final de la cadena (opcionalmente si la comparaciï¿½n debe omitir mayï¿½sculas y minï¿½sculas) */
String.prototype.removeSufix = function ( sufix, caseInsensitive ) {
    var string = String(this);
    if(string.endsWith(sufix, caseInsensitive)) return string.substring( 0, string.length - sufix.length );
    else return string;
};


/* Elimina una subcadena al principio de la cadena (opcionalmente si la comparaciï¿½n debe omitir mayï¿½sculas y minï¿½sculas) */
String.prototype.removePrefix = function (prefix,caseInsensitive ) {
    var string = String(this);
    if(string.startsWith(prefix, caseInsensitive)) return string.substring(prefix.length);
    else return string;
};



/* Agrega una subcadena al final de la cadena (si todavï¿½a no la tiene) */
String.prototype.putSufix = function ( sufix, caseInsensitive ) {
    var string = String(this);
    if(!string.endsWith(sufix, caseInsensitive)) return string + sufix;
    else return string;
};


/* Agrega una subcadena al principio de la cadena (si todavï¿½a no la tiene) */
String.prototype.putPrefix = function (prefix,caseInsensitive ) {
    var string = String(this);
    if(!string.startsWith(prefix, caseInsensitive)) return prefix + string;
    else return string;
};


/* Repite una cadena */
String.prototype.repeat=function(times){
    var repeat_str = String(this); var output = '';
    for(var i=0;i<Number(times);i++) output+=repeat_str;
    return output;
};


/* Invierte una cadena */
String.prototype.reverse=function(){
    var output = '';
    for(var i=this.length-1;i>=0;i--) output += this.charAt(i);
    return output;
};


/* Rellena la cadena hacia la izquierda */
String.prototype.paddLeft = function(paddlen,paddstr) {
    if(paddstr==null || paddstr.length==0) paddstr=' ';
    paddlen = Number(paddlen); var output = String(this);

    if(output.length < paddlen) {
        while(output.length < paddlen) 	output = paddstr + output;
        if(output.length > paddlen) output = output.substring(output.length - paddlen);
    }
    return output;
};

/* Rellena la cadena hacia la derecha */
String.prototype.paddRight = function(paddlen,paddstr) {
    if(paddstr==null || paddstr.length==0) paddstr=' ';
    paddlen = Number(paddlen); var output = String(this);

    if(output.length < paddlen) {
        while(output.length < paddlen) output += paddstr;
        if(output.length > paddlen) output = output.substring(0, paddlen);
    }
    return output;
};


/* Reemplaza todas las ocurrencias de una subcadena (puede ser un array para especificar distintas subcadenas) por otra (tambiï¿½n puede ser un array) */
String.prototype.replaceAll = function(search, replace){

    var search_array, replace_array;

    if(Object.prototype.toString.call(search) === '[object Array]') {
        search_array = search;

        if(Object.prototype.toString.call(replace) === '[object Array]' && replace.length == search_array.length)
            replace_array = replace;

        else {
            replace_array = [];
            while(replace_array.length < search_array.length) replace_array.push(replace);
        }

    } else { search_array = [search]; replace_array = [replace]; }

    var return_str = this;
    for(var i=0; i < search_array.length; i++) return_str = return_str.split(search_array[i]).join(replace_array[i]);
    return return_str;
};



/* Reemplaza una expresiï¿½n regular de acuerdo a una funcion callback */
String.prototype.replaceCallback = function(search, callback) {
    return RegExp.create(search).replaceCallback(this, callback);
};


/* Formatea una cadena de acuerdo a argumentos */
String.prototype.format = function(args) {
    return String.format.apply(this, jQuery.merge([this], arguments));
};


/* Objeto con distintas funciones de tipos de escapado ----------------------------------------------------------------------------------------------------- */

String.escape = {

    'default': function(s) {
        var escaped = String(s).replace(/\|/g,'\\|');
        escaped = escaped.replace(/\\/g,'\\\\');
        escaped = escaped.replace(/\[/g,'\\[');
        escaped = escaped.replace(/\]/g,'\\]');
        escaped = escaped.replace(/\'/g,'\\\'');
        escaped = escaped.replace(/\"/g,'\\\"');
        escaped = escaped.replace(/\,/g,'\\,');
        escaped = escaped.replace(/\=/g,'\\=');

        return escaped;
    },


    'regexp': function(s) {
        var escaped = String(s).replace(/\|/g,'\\|');
        escaped = escaped.replace(/\\/g,'\\\\');
        escaped = escaped.replace(/\[/g,'\\[');
        escaped = escaped.replace(/\]/g,'\\]');
        escaped = escaped.replace(/\'/g,'\\\'');
        escaped = escaped.replace(/\"/g,'\\\"');
        escaped = escaped.replace(/\,/g,'\\,');
        escaped = escaped.replace(/\=/g,'\\=');
        escaped = escaped.replace(/\(/g,'\\(');
        escaped = escaped.replace(/\)/g,'\\)');
        escaped = escaped.replace(/\./g,'\\.');

        return escaped;
    },


    'url': escape,

    'html': function(s) {

        var escaped = String(s);
        escaped = escaped.replace(/\&/g,'&amp;');
        escaped = escaped.replace(/\</g,'&lt;');
        escaped = escaped.replace(/\>/g,'&gt;');
        escaped = escaped.replace(/\"/g,'&quot;');
        escaped = escaped.replace(/\'/g,'&#039;');
        escaped = escaped.replace(/\\/g,'&#092;');
        return escaped;
    },


    'string': function(s) {

        var escaped = String(s).replace(/\\/g,'\\');
        escaped = escaped.replace(/\'/g,'\\\'');
        escaped = escaped.replace(/\"/g,'\\"');
        escaped = escaped.replace(/\n/g,'\\n');

        return escaped;
    }
};

/* Escapa una cadena de acuerdo a un tipo */
String.prototype.escape = function(type) {
    return Object.getValue(String.escape, type, String.escape['default'] )(this);
};


String.prototype.quote = function(quote) {
    if(quote==null) quote = '"';
    return quote+this.escape('string')+quote;
};


String.quote = function(s,quote) {
    return String(s).quote(quote);
};


String.prototype.quoteHtml = function(quote) {
    if(quote==null) quote = '"';
    return quote+this.escape('html')+quote;
};


String.quoteHtml = function(s,quote) {
    return String(s ? s : '').quoteHtml(quote);
};


String.prototype.escapeHtml = function() {
    return this.escape('html');
};


String.escapeHtml = function(s) {
    return String(s ? s : '').escapeHtml();
};

String.quoteHtmlAttrs = function(attrs, quote) {

    var attrsString = '';

    $.each(attrs, function(key, value){

        if(typeof value == 'boolean') {
            if(value) attrsString += ' ' + key + '=' + String.quoteHtml(key, quote);

        } else if(value !== null)
            attrsString += ' ' + key + '=' + String.quoteHtml(value, quote);

    });

    return attrsString;

};


String.quoteShortTag = function(tagname, attrs, quote) {
    return '<' + tagname + String.quoteHtmlAttrs(attrs, quote) + ' />';
};



String.quoteWrapTag = function(tagname, content, attrs, quote) {
    return '<' + tagname + String.quoteHtmlAttrs(attrs, quote) + '>' + String(content) + '</' + tagname + '>';
};


String.quoteLongTag = function(tagname, content, attrs, quote) {
    return String.quoteWrapTag(tagname, String.escapeHtml(content), attrs, quote);
};




String.prototype.validateEmail = function() {
    return Boolean(this.match(/^(?:[a-zA-Z0-9_\.\-]+)\@(?:(?:(?:[a-zA-Z0-9\-])+\.)+(?:[a-zA-Z0-9]{2,4})+)$/));
};


String.validateEmail = function(s) {
    return String(s ? s : '').validateEmail();
};


/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones de Numeros ------------------------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */


Number.isNumeric = function(n)
{
    return !isNaN(parseFloat(n)) && isFinite(n);
}

/* Convierte un nï¿½mero decimal a otra base */
Number.decimalToBase=function(dec, base) {

    if(dec < 0) return '-' + Number.decimalToBase(Math.abs(dec), base);
    else{

        var dividendo = Math.round(dec);
        var divisor = Math.round(base);

        var digits = [];

        while(dividendo >= divisor){

            digits.push(dividendo % divisor);
            dividendo = Math.floor(dividendo/divisor);
        }

        if(dividendo > 0) digits.push(dividendo);
        if(digits.length == 0) digits.push(0);

        var base_str = '';

        for(var i=digits.length-1; i>=0; i--){
            var digit = digits[i];
            base_str += digit < 10 ? String(digit) : String.fromCharCode( digit - 10 + 97 );

        }


        return base_str;
    }
};


/* Abreviaciones para convertir un decimal a bases conocidas */
Number.decimalToHex=function(dec) { return Number.decimalToBase(dec,16); };

Number.decimalToOct=function(dec) { return Number.decimalToBase(dec,2); };

Number.decimalToBin=function(dec) { return Number.decimalToBase(dec,8); };


/*-------------------------------------------------------------------------------------------*/


/* Convertir un nï¿½mero de otra base a decimal */
Number.baseToDecimal=function(base_str, base){

    base_str = String(base_str).toLowerCase();

    if(base_str.charAt(0) == '-') {
        base_str = base_str.substring(1);
        var dec_sign = -1;
    } else var dec_sign = 1;

    var dec = 0;
    var exp = 0;

    for(var i=base_str.length-1; i>=0; i--){

        var digit_str = base_str.charAt(i);
        var digit_code = digit_str.charCodeAt(0);

        if(digit_code >= 48 && digit_code <= 57) var dec_number = Number(digit_str);
        else var dec_number = digit_code - 97 + 10;

        dec += dec_number * Math.pow(base,exp);
        exp++;

    }


    return dec * dec_sign;

};



/* Abreviaciones para convertir de otras bases conocidas a decimal */
Number.hexToDecimal=function(base_str) { return Number.baseToDecimal(base_str,16); };

Number.octToDecimal=function(base_str) { return Number.baseToDecimal(base_str,8); };

Number.binToDecimal=function(base_str) { return Number.baseToDecimal(base_str,2); };


/*-------------------------------------------------------------------------------------------*/


/* Separadores por defecto para el formateo de numeros */
Number.thousandsSeparator = '.';
Number.decimalSeparator = ',';


/* Formateo de nï¿½meros */
Number.format = function( number, decimalPlaces, thousandsSeparator, decimalSeparator, autoAddDecimalPlaces ) {

    var numberString = String( number );

    thousandsSeparator = thousandsSeparator == null ? Number.thousandsSeparator : thousandsSeparator;
    decimalSeparator = decimalSeparator == null ? Number.decimalSeparator : decimalSeparator;

    if(autoAddDecimalPlaces == null) autoAddDecimalPlaces = true;


    var posDecimalSeparator = numberString.indexOf('.');

    var intPart;
    var decimalPart;

    if(posDecimalSeparator >= 0 && posDecimalSeparator < numberString.length) {

        var parts = numberString.split('.', 2);

        intPart = String( parts[0] );
        decimalPart = String( parts[1] );

    } else {

        intPart = String( numberString );
        decimalPart = '0';
    }

    decimalPlaces = Number(decimalPlaces == null ? (decimalPart != '0' ? decimalPart.length : 0) : decimalPlaces);


    var intArray = new Array();
    var numberThousands = Math.floor( intPart.length / 3 );
    var thousandsOffset = intPart.length - ( numberThousands * 3 );

    if(thousandsOffset > 0)
        intArray.push( intPart.substr( 0, thousandsOffset ) );

    for(i=0; i < numberThousands; i++)
        intArray.push( intPart.substr( thousandsOffset + ( i * 3 ), 3 ) );


    var finalString = intArray.join( thousandsSeparator );

    if(decimalPlaces > 0) {

        finalString += decimalSeparator;

        if( decimalPlaces <= decimalPart.length && !autoAddDecimalPlaces ) finalString += decimalPart.substr( 0, decimalPlaces );
        else if( decimalPlaces <= decimalPart.length && autoAddDecimalPlaces ) finalString += decimalPart;
        else {

            while( decimalPlaces > decimalPart.length )
                decimalPart += '0';

            finalString += decimalPart;

        }
    }

    return finalString;
};



/* Parseo de un nï¿½mero */
Number.parse = function(s, decimalSeparator, thousandsSeparator) {

    if(s != null && typeof s == 'number') return s;

    thousandsSeparator = thousandsSeparator == null ? Number.thousandsSeparator : thousandsSeparator;
    decimalSeparator = decimalSeparator == null ? Number.decimalSeparator : decimalSeparator;

    var numberString = String(s).replace(/\s+/g, '');

    if(thousandsSeparator) numberString = numberString.replace(new RegExp('\\'+thousandsSeparator, 'g'), '');
    if(decimalSeparator) numberString = numberString.replace(new RegExp('\\'+decimalSeparator, 'g'), '.');


    var decimalSymbolPos = numberString.lastIndexOf('.');

    var intPart = '0';
    var decimalPart = '0';

    if(decimalSymbolPos >= 0 && decimalSymbolPos < numberString.length) {

        intPart = numberString.substring( 0, decimalSymbolPos ).replace(/([^0-9])+/g, '');
        decimalPart = numberString.substring( decimalSymbolPos ).replace(/([^0-9])+/g, '');

    } else intPart =  numberString.replace(/([^0-9])+/g, '');


    var finalString = Number( intPart ) + '.' + decimalPart;

    if( numberString.charAt(0) == '-' ) finalString = '-' + finalString;

    return Number( finalString );

};



/* Parseo de nï¿½meros sï¿½lamente enteros */
Number.parseInt = function(s, decimalSeparator, thousandsSeparator) {
    return window.parseInt(Number.parse(s, decimalSeparator, thousandsSeparator));
};


/* Parseo de nï¿½mero sï¿½lamente decimales */
Number.parseFloat = function(s, decimalSeparator, thousandsSeparator) {
    return Number.parse(s, decimalSeparator, thousandsSeparator);
};


/*-------------------------------------------------------------------------------------------*/


/* Redondeo de nï¿½meros */
Number.round = function(num,num_decimals){
    var move_factor = Math.pow(10, num_decimals == null ? 0 : num_decimals);
    return Math.round(Number(num) * move_factor ) / move_factor;

};


/* Obtiene un nï¿½mero aleatorio */
Number.random = function ( max, min ) {

    if(max == null) max = 100000;
    if(min == null) min = 0;

    if(max <= min) return max;

    var numPosibilidades = max - min;
    var aleatorio = Math.round( Math.random() * numPosibilidades );
    return min + aleatorio;
};


/*-------------------------------------------------------------------------------------------*/

Number.sizeUnits = ['bytes','Kb','Mb','Gb','Tb','Pb'];

Number.SIZE_UNIT_BYTES = 0;
Number.SIZE_UNIT_KB = 1;
Number.SIZE_UNIT_MB = 2;
Number.SIZE_UNIT_GB = 3;
Number.SIZE_UNIT_TB = 4;
Number.SIZE_UNIT_PB = 5;

Number.getOptimalSizeUnit = function(bytes) {

    var sizeUnit = 0;

    while(sizeUnit <= Number.SIZE_UNIT_PB && (bytes / Math.pow(1024,sizeUnit)) > 1024)
        sizeUnit++;

    return sizeUnit;
};


Number.formatSize = function(bytes, numDecimals, sizeUnit) {

    if(sizeUnit == null) sizeUnit = Number.getOptimalSizeUnit(bytes);
    return Number.format(Number.round(bytes/Math.pow(1024, sizeUnit), numDecimals), numDecimals == null ? 0 : numDecimals) + ' ' + Number.sizeUnits[sizeUnit];
};



/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones de Arrays -------------------------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */


/* Controla si un objeto es un array */
Array.isArray = function(v) {

    return jQuery.isArray(v)
};


/* Aplica una funcion a cada elemento de un array */
Array.each = function(obj, handler) {
    return jQuery.each(jQuery.isArray(obj) ? obj : [obj], handler);
};


/* Controla si un array contiene un elemento */
Array.contains = function(array, element) {
    return array.contains(element);
};


/* Obtiene un array a partir de un rango */
Array.range = function(start,end,pass){

    if(start == null || end == null || end == start || (typeof start != 'string' && typeof start != 'number') || typeof start != typeof end || pass == 0 ) return [];

    if(typeof start == 'string') {
        start = start.charCodeAt(0);
        end = end.charCodeAt(0);

        elementFunction = function(i) { return String.fromCharCode(i); };

    } else elementFunction = function(i) { return i; };

    if(pass == 0) return [];
    else if(pass == null) pass = 1;

    if(end < start) pass = -pass;

    var range=[];
    for(var i=start; i<=end; i=i+pass) range.push(elementFunction(i));

    return range;
};


/*----------------------------------------------------------------------------------------------------------------------------------------------- */

/* Obtiene el indice de ocurrencia de un elemento */
Array.prototype.indexOf = function(element, offset, forceType){

    var compFunction = forceType ? function(value) { return value === element; } : function(value) { return value == element; };
    var start = offset != null ? (offset >= 0 ? offset : this.length-(offset+2)) : 0;

    for(var i=start; i<this.length; i++)
        if(compFunction(this[i])) return i;

    return -1;
};


/* Obtiene el ultimo indice de ocurrencia de un elemento */
Array.prototype.lastIndexOf = function(element, offset, forceType){

    var compFunction = forceType ? function(value) { return value === element; } : function(value) { return value == element; };
    var start = offset != null ? (offset >= 0 ? offset : this.length-(offset+2)) : 0;

    for(var i=this.length-1; i>=start; i--)
        if(compFunction(this[i])) return i;

    return -1;
};


/* Obtiene los indices de ocurrencia de los elementos */
Array.prototype.findAll = function(elements, offset, forceType){

    if(!jQuery.isArray(elements)) return this.findAll([elements], offset, forceType);
    if(!jQuery.isArray(elements)) return [];

    var compFunction = forceType ? function(value1, value2) { return value1 === value2; } : function(value1, value2) { return value1 == value2; };
    var start = offset != null ? (offset >= 0 ? offset : this.length-(offset+2)) : 0;
    var indexs = [];

    for(var i=0; i<elements.length; i++)
        for(var j=start; j<this.length; j++)
            if(compFunction(this[j], elements[i]))
                indexs.push(j);

    return indexs;
};


/* Obtiene los indices de ocurrencia de un elemento */
Array.prototype.find = function(element, offset, forceType) {

    if(!Array.isArray(this)) return $.find.call(this, element);
    else return this.findAll([element], offset, forceType);
};



/* Controla si el array contiene un elemento */
Array.prototype.contains = function(element, forceType){

    if(!Array.isArray(this)) return $.contains.call(this, element);
    else return this.indexOf(element, 0, forceType) >= 0;
};


/* Aplica una funciï¿½n a cada elemento del array */
Array.prototype.each = function(callback) {
    Array.each(this, callback);
    return this;
};


/* Une el array con otro array (se puede utilizar index para especificar la posicion) */
Array.prototype.merge = function(array, index){

    if(!array) return this;

    if(index == null) index = this.length;
    else index = index<0 ? 0: (index>this.length ? this.length : index);

    var tail=[];

    while(this.length>index) tail.push(this.pop());

    for(var i=0;i<array.length;i++) this.push(array[i]);
    for(var i=tail.length-1;i>=0;i--) this.push(tail[i]);

    return this;
};



/* Inserta elementos en una posicion */
Array.prototype.insert = function(index, element) {
    return this.merge($.makeArray(arguments).slice(1), index);
};


/* Inserta elementos al final de un array */
Array.prototype.append = function(element) {
    return this.merge(arguments);
};


/* Inserta elementos al principio de un array */
Array.prototype.prepend = function(element) {
    return this.merge(arguments, 0);
};



/* Copia el array */
Array.prototype.copy = function(deepcopy) {
    return deepcopy ? Object.deepcopy(this) : Object.copy(this);
};


/* Elimina elementos de acuerdo a su indice (por defecto el ultimo) */
Array.prototype.pop = function(indexs) {

    if(arguments.length == 0) return this.pop(this.length-1);
    else if(arguments.length == 1 && !jQuery.isArray(arguments[0])) return this.splice(arguments[0], 1)[0];

    var removeIndexs = [], returnValues = [], index, indexs;

    for(var i=0; i<arguments.length; i++) {
        indexs = jQuery.isArray(arguments[i]) ? arguments[i] : [arguments[i]];

        for(var j=0; j<indexs.length; j++) {

            if(!removeIndexs.contains(indexs[j])) removeIndexs.push(indexs[j]);
            returnValues.push(this[indexs[j]]);
        }
    }

    removeIndexs.sort();

    for(var i=removeIndexs.length-1; i>=0; i--) this.splice(removeIndexs[i], 1);
    return returnValues;
};


/* Elimina todas las ocurrencias de un elemento del array */
Array.prototype.removeAll = function(element, forceType, limit) {

    var compFunction = forceType ? function(value1, value2) { return value1 === value2; } : function(value1, value2) { return value1 == value2; };
    var removeIndexs = [];
    limit = limit == null ? this.length : Number(limit);

    for(var i=0; i<this.length && removeIndexs.length < limit; i++)
        if(compFunction(this[i], element))
            removeIndexs.push(i);


    for(var i=removeIndexs.length-1; i>=0; i--) this.splice(removeIndexs[i], 1);
    return this;
};


/* Elimina la primer ocurrencia (se puede utilizar limit) de un elemento */
Array.prototype.remove = function(element, limit, forceType) {
    return this.removeAll(element, forceType, limit == null ? 1 : (limit == true ? this.length : Number(limit)));
};



/* Abreviaciï¿½n para la inserciï¿½n de un array */
Array.prototype.insertArray=function(index,array){
    return this.merge(array, index);
};


/* Inserta un array al final */
Array.prototype.appendArray=function(array){
    return this.insertArray(this.length,array);
};

/* Inserta un array al inicio */
Array.prototype.prependArray=function(array){
    return this.insertArray(0,array);
};



Array.prototype.filter = function(callback, recursive) {
    return Object.filter(this, callback, recursive);
};



/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones y funciones adicionales de expresiones regulares ----------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */


/* Crea una expresiï¿½n regular (puede aceptar otra expresiï¿½n regular)*/
RegExp.create = function(value, flags) {
    if(flags == null) flags = '';

    var patternMatch = String(value).match(/\/(.*)\/([a-z]*)/);

    if(!patternMatch) return new RegExp(String(value), flags);
    else return new RegExp(patternMatch[1], patternMatch[2] + flags);

};

/* Escapa una cadena de expresiï¿½n regular */
RegExp.escape = function(s) {
    return String.escape.regexp(s);
};


/* Abreviacion del metodo replace de cadenas para expresiones regulares */
RegExp.prototype.replace = function(string, replace) {
    return String(string).replace(this, replace);
};


/* Reemplaza una expresiï¿½n regular de acuerdo a una funcion callback */
RegExp.prototype.replaceCallback = function(string, callback) {

    var replacedString = '';
    var remainString = String(string);

    var match = remainString.match(this);
    var index = 0;

    while(match) {

        replacedString += remainString.substr(0, match.index);
        replacedString += String(callback.call(this, match, index));

        remainString = remainString.substr(match.index + match[0].length);
        match = remainString.match(this);
        index++;
    }

    replacedString += remainString;
    return replacedString;

};




/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones y funciones de Navegaciï¿½n ---------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */



var Navigation = {};

Navigation.host = location.host;					/* Host */

Navigation.hostname = location.hostname;			/* Nombre del host */

Navigation.pathname = location.pathname;			/* Path */

Navigation.port = location.port;					/* Puerto */

Navigation.protocol = location.protocol;			/* Protocolo */

Navigation.document = location.href.split(location.href.indexOf('?') == -1 ? '#' : '?').shift();			/* Direccion url (sin las variables GET) */

Navigation.url = location.href.split('#').shift();

Navigation.baseURL = String($('base').attr('href') ? $('base').attr('href') : Navigation.document).replace(new RegExp('/+$','g'),'')+'/';		/* Base url de la web */

Navigation.serverURL = location.href.replace(new RegExp('^([A-Za-z]+\://(?:\w|\.|\-|\_|\:|\@|\=)+?)(?:/.*)?$'), '$1');

Navigation.hashParamsSeparator = '!';

Navigation.hashParamsEncodeString = function(s) {
    return $.urlencode(s);
};

Navigation.hashParamsDecodeString = function(s) {
    return $.urldecode(s);
};

/*--------------------------------------------------------------------------------------------------------------*/

/* Prepara una url relativa a la direccion de la web (baseURL) */
Navigation.relativeURL = function(href) {
    href = String(href).trim();
    if(new RegExp('^[A-Za-z]+\://').test(href)) return href;
    else if(href.startsWith('/')) return Navigation.serverURL + href;
    else return Navigation.baseURL + href.replace(new RegExp('$\/+', 'g'), '');
}

Navigation.openBlank = function(href) {

    var form =

        $('<form />').attr({
            'target': '_blank',
            'method': 'get',
            'action': href
        }).bind('submit', function() {
            setTimeout(function() { $(this).remove(); }, 500);
        }).hide();

    $(document.body).append(form);
    form.submit();

};


/* Abre una ventana popup */
Navigation.openPopup = function( href, width, height, wait ) {


    if(width == null) width = screen.availWidth / 2;
    else if( width > screen.availWidth ) width = screen.availWidth;

    if( height == null ) height = screen.availHeight / 2;
    else if( height > screen.availHeight ) height = screen.availHeight;

    if(wait == null || wait <= 0) wait = 0;

    var top = Math.ceil( ( screen.availHeight - height ) / 2 );
    var left = Math.ceil( ( screen.availWidth - width ) / 2 );

    width += 10;
    height += 20;

    var propiedades= 'toolbars=false,width='+width+',height='+height+',top='+top+',left=' + left;

    var functionString = "window.open( '"+href+"', null,'" + propiedades + "' );";

    try { setTimeout(functionString, wait * 1000); }
    catch(e) {}
};


/* Conduce a una url, pero como si fuera por medio de un formulario (se pueden especificar variables y el tipo GET o POST, o el target) */
Navigation.go = function(href, opts){

    opts=jQuery.extend({}, {data:{}, method:'get', target:'_self', wait:0}, opts);

    var hrefParts = String(href).split('?');

    var form=jQuery('<form />').
    hide().
    attr('method', opts.method).
    attr('target', opts.target).
    attr('action', hrefParts[0]).
    appendTo(document.body);

    jQuery.each($.merge($.paramArray(hrefParts[1]), jQuery.paramArray(opts.data)), function(index,param){

        $('<input />').
        attr('type','hidden').
        attr('name', param[0]).
        val(param[1]).
        appendTo(form);
    });

    $("<button type='submit' />").appendTo(form);

    setTimeout(function(){form.find('button').click();}, opts.wait);

};


/* Abreviaciï¿½n del mï¿½todo "go" para peticiones POST */
Navigation.post = function(href, vars){
    return Navigation.go(href, {data:vars, method:'post'});
};

/* Abreviaciï¿½n del mï¿½todo "go" para peticiones POST */
Navigation.postBlank = function(href, vars){
    return Navigation.go(href, {data:vars, method:'post', target: '_blank'});
};


/* Abreviaciï¿½n del mï¿½todo "go" para peticiones GET */
Navigation.get = function(href, vars, merge){

    if(merge) {

        vars = $.extend({}, $.paramDecode(Navigation.query()), $.paramDecode(vars));

    }



    return Navigation.go(href, {data:vars, method:'get'});

};


/* Abreviaciï¿½n del mï¿½todo "go" para peticiones que abren ventanas nuevas */
Navigation.openWindow = function(href, opts){
    return Navigation.go(href, jQuery.extend({}, {target:'_blank'}, opts));
};


Navigation.href = function(href) {

    if(arguments.length > 0) location.replace(href);
    else return location.href;
};


Navigation.hash = function(hash) {

    if(arguments.length > 0) {

        var href = Navigation.document;

        var query = Navigation.query();
        if(query) href += '?' + query;

        href += '#';

        if(hash) hash += String(hash);

        return Navigation.href(href);

    } else return location.hash.replace(/^\#/, '');
};


Navigation.hashString = function(hash) {

    if(arguments.length > 0) {

        var href = Navigation.document;

        var query = Navigation.query();
        if(query) href += '?' + query;

        var hashParts = Navigation.hash().split(Navigation.hashParamsSeparator, 2);

        if(hashParts.length > 1) {

            if(hash) href += '#' + String(hash) + Navigation.hashParamsSeparator + hashParts[1];
            else href += '#' + Navigation.hashParamsSeparator + hashParts[1];

        } else if(hashParts.length == 1) {

            if(hash) href += '#' + String(hash);

        } else {
            href += '#';
        }

        return Navigation.href(href);

    } else {

        var hashParts = Navigation.hash().split(Navigation.hashParamsSeparator);

        if(hashParts.length > 0) return hashParts[0];
        else return '';

    }
};



Navigation.hashParams = function(params, replace) {

    if(arguments.length > 0) {

        var href = Navigation.document;

        var query = Navigation.query();
        if(query) href += '?' + query;

        var hashParts = Navigation.hash().split(Navigation.hashParamsSeparator, 2);

        var hashString = '';
        var hashParams = {};

        if(hashParts.length > 1) {

            hashParams = $.paramDecode(Navigation.hashParamsDecodeString(hashParts[1]));
            hashString = hashParts[0];

        } else if(hashParts.length == 1) {

            hashString = hashParts[0];

        }

        params = $.paramDecode(params);

        if(replace) hashParams = params;
        else hashParams = $.extend({}, hashParams, params);

        var hasParams = $.isPlainObject(hashParams) && Object.getKeys(hashParams).length > 0;
        var hasString = hashString.length > 0;

        href += '#';

        if(hasParams || hasString) {

            if(hasString) href += hashString;

            if(hasParams) href += Navigation.hashParamsSeparator + Navigation.hashParamsEncodeString($.paramQuery(hashParams));
        }

        Navigation.href(href);

        return hashParams;

    } else {

        var hashParts = Navigation.hash().split(Navigation.hashParamsSeparator, 2);

        if(hashParts.length > 1) return $.paramDecode(Navigation.hashParamsDecodeString(hashParts[1]));
        else return {};

    }
};


Navigation.hashParamsReplace = function(params) {
    return Navigation.hashParams(params, true);
};

Navigation.query = function(query, merge) {

    if(arguments.length > 0) {


        if(merge) {

            var actualQuery = Navigation.query();
            if(actualQuery) return Navigation.query($.extend({}, $.paramDecode(actualQuery), $.paramDecode(query)), false);

        }

        var href = Navigation.document;
        var query = $.paramQuery(query);

        if(query) href += '?' + query;

        var hash = Navigation.hash();
        if(hash) href += '#' + hash;

        return Navigation.href(href);


    } else return location.href.indexOf('?') == -1 ? '' : location.href.split('?').pop().split('#').shift();

};



Navigation.reload = function(query) {

    if(arguments.length > 0) {

        var actualQuery = Navigation.query();
        if(actualQuery) return Navigation.query($.extend({}, $.paramDecode(actualQuery), $.paramDecode(query)), false);

        var href = Navigation.document;
        var query = $.paramQuery(query);

        if(query) href += '?' + query;

        var hash = Navigation.hash();
        if(hash) href += '#' + hash;

        return location.replace(href);


    } else {

        location.reload();

    }
};

/*---------------------------------------------------------------------------------------------------------- */

/* Parsea el valor de una cookie para devolver nï¿½meros o booleanos (no sï¿½lo strings) */

Navigation.parseCookieValue=function(val){
    val=String(val);
    if(new RegExp(/^(\-?\d+(\.\d+)?)|(true|false)$/i).test(val)) return eval(val.toLowerCase());
    else return val;
};


/* Aplica una funciï¿½n a cada cookie del navegador definido en la web (la funciï¿½n se llama con: nombre_cookie, valor_cookie) */
Navigation.eachCookie=function(callback, parseCookieValue){

    if(parseCookieValue == null) parseCookieValue = true;

    jQuery.each(String(document.cookie).split(';'), function(index,data){
        data=data.trim().split('=');

        if(data.length>1) {
            var cookieName = $.urldecode(data[0]);
            var cookieValue = $.urldecode(data[1]);

            if(parseCookieValue) cookieValue = Navigation.parseCookieValue(cookieValue);
            return callback.call(this,cookieName,cookieValue);
        }
    });
};


/* Se puede llamar de tres formas:
 getCookie() (sin parï¿½metros) devuelve un objeto con todas las cookies
 getCookie(name..., [defaultValue]) (name es array) devuelve un objeto con las cookies que se solicitaron en el array
 getCookie(name, [defaultValue]) devuelve el valor de la cookie "name" (si no existe devuelve defaultValue)
 */

Navigation.getCookie=function(name,defaultValue){

    if(name == null) {
        var data_array=[];
        Navigation.eachCookie(function(data_name,data_value){ data_array.push([data_name,Navigation.parseCookieValue(data_value)]); }, false);
        return jQuery.paramDecode(data_array);

    } else if(jQuery.isArray(name)) {

        var data = {};
        jQuery.each(name, function(index, value) { data[value] = Navigation.getCookie(value, defaultValue); });

        return data;

    } else {

        name=$.urldecode(name);
        var value = defaultValue, value_map = false;

        Navigation.eachCookie(function(data_name,data_value){

            if(data_name==name) {
                value_map=false;
                value=Navigation.parseCookieValue(data_value);
                return false;

            } else if(data_name.indexOf(name)===0 && data_name.length>name.length && data_name.charAt(name.length)=='[') {
                if(!value_map) {
                    value = [];
                    value_map=true;
                }

                value.push([data_name,data_value]);
            }

        }, false);

        if(value_map) value = jQuery.paramDecode(value);

        return value;
    }
};


/* Elimina cookies */
Navigation.removeCookie = function(names,path,domain){

    if(!jQuery.isArray(names)) names = [names];
    var date = new Date(); date.addDays(-1);
    var strsufix = '; expires='+date.toGMTString();
    if(path) strsufix += '; path='+$.urlencode(path);
    if(domain) strsufix += '; domain='+domain;

    Navigation.eachCookie(function(data_name,data_value){
        jQuery.each(names,function(index,name){
            if(data_name==name || (data_name.indexOf(name)===0 && data_name.length>name.length && data_name.charAt(name.length)=='[')) {
                document.cookie = $.urlencode(data_name) + '=null' + strsufix;
                if(data_name==name) return false;
            }

        });
    }, false);

};


/* Se puede llamar de dos formas:
 addCookie(name,value,[days, path], domain]) agrega una cookie de nombre "name" y valor "value"
 addCookie(data,[days, path], domain]) agrega todas las cookies desde el objeto data
 */
Navigation.addCookie = function(data,days,path,domain){

    if(!data) return;

    /* Si es una cadena, el siguiente parï¿½metro es el valor */
    if(data && typeof data == 'string') {
        var cookieData = {};
        cookieData[data] = arguments[1];

        return Navigation.addCookie.apply(this, jQuery.merge([cookieData], jQuery.makeArray(arguments).slice(2)));

    } else {

        var data = jQuery.extend({}, data);
        Navigation.removeCookie(Object.getKeys(data), path, domain);

        var date = new Date(); date.addDays(days==null ? 30 : days);
        var strsufix = '; expires='+date.toGMTString();
        if(path) strsufix += '; path='+$.urlencode(path);
        if(domain) strsufix += '; domain='+domain;

        jQuery.each(jQuery.paramArray(data),function(index,data){
            document.cookie = $.urlencode(data[0])+'='+$.urlencode(data[1])+strsufix;

        });

    }

};

/*----------------------------------------------------------------------------------------------------------------------------------------------- */

Navigation.logoutHTTPAuth = function(redirectUrl, logoutTestUrl) {

    if(arguments.length == 0)
    {
        redirectUrl = '/';
    }

    if(arguments.length < 2)
    {
        logoutTestUrl = redirectUrl;
    }

    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    }
    // code for IE
    else if (window.ActiveXObject) {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (window.ActiveXObject) {
        // IE clear HTTP Authentication
        document.execCommand("ClearAuthenticationCache");
        window.location.href=redirectUrl;
    } else {
        xmlhttp.open("GET", logoutTestUrl, true, "logout", "logout");
        xmlhttp.send("");
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {window.location.href=redirectUrl;}
        }


    }


    return false;
}

/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones y funciones de Fecha y Hora -------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */

/* Simbolos de Parseo de Fechas
 %a 	abbreviated weekday name
 %A 	full weekday name
 %b 	abbreviated month name
 %B 	full month name
 %C 	century number
 %d 	the day of the month ( 00 .. 31 )
 %e 	the day of the month ( 0 .. 31 )
 %H 	hour ( 00 .. 23 )
 %I 	hour ( 01 .. 12 )
 %j 	day of the year ( 000 .. 366 )
 %k 	hour ( 0 .. 23 )
 %l 	hour ( 1 .. 12 )
 %m 	month ( 01 .. 12 )
 %o  month ( 1 .. 12 )
 %M 	minute ( 00 .. 59 )
 %n 	a newline character
 %p 	``PM'' or ``AM''
 %P 	``pm'' or ``am''
 %S 	second ( 00 .. 59 )
 %s 	number of seconds since Epoch (since Jan 01 1970 00:00:00 UTC)
 %t 	a tab character
 %U, %W, %V 	the week number
 %u 	the day of the week ( 1 .. 7, 1 = MON )
 %w 	the day of the week ( 0 .. 6, 0 = SUN )
 %y 	year without the century ( 00 .. 99 )
 %Y 	year including the century ( ex. 1979 )
 %% 	a literal % character
 */


/* Nombres de los dï¿½as de las semana y meses, formatos largos y abreviados */
Date._SDN = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];
Date._DN = ['Lunes', 'Martes', 'Miï¿½rcoles', 'Jueves', 'Viernes', 'Sï¿½bado', 'Domingo'];
Date._SMN = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
Date._MN = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

Date._defaultOutputFormat = '%d/%m/%Y %H:%M';		/* Expresion de formato por defecto */
Date._defaultInputFormat = '%Y-%m-%d %k:%M:%S'; 	/* Expresion de parseo por defecto */


/* --------------------------------------------------------------------------------------------------------- */


/* Parsea una fecha dada una expresion (si no se pasa formato, se usa la expresion de parseo por defecto) */
Date.parseDate = function(str, fmt) {

    if(str && typeof str == 'object' && str instanceof Date) return str;

    if(fmt == null) fmt = Date._defaultInputFormat;
    var str = String(str);

    var today = new Date(), y=0, m=-1, d=0, a=str.split(/\W+/), b = fmt.match(/%./g), i=0, j=0, hr=0, min=0;

    for (i = 0; i < a.length; ++i) {

        if (!a[i]) 	continue;

        switch (b[i]) {

            case "%d": case "%e": d = parseInt(a[i], 10); break;

            case "%m": case '%o': m = parseInt(a[i], 10) - 1; break;

            case "%Y": case "%y":
            y = parseInt(a[i], 10);
            (y < 100) && (y += (y > 29) ? 1900 : 2000);
            break;

            case "%b": case "%B":
            for (j = 0; j < 12; ++j) {
                if (Date._MN[j].substr(0, a[i].length).toLowerCase() == a[i].toLowerCase()) { m = j; break; }
            }
            break;

            case "%H": case "%I": case "%k": case "%l": hr = parseInt(a[i], 10); break;

            case "%P": case "%p":
            if (/pm/i.test(a[i]) && hr < 12)
                hr += 12;
            else if (/am/i.test(a[i]) && hr >= 12)
                hr -= 12;
            break;


            case '%s':

                var date = new Date();
                //alert(parseInt(a[i], 10));
                date.setTime(parseInt(a[i], 10)*1000);
                return date;

                break;

            case "%M": min = parseInt(a[i], 10); break;
        }
    }

    if (isNaN(y)) y = today.getFullYear();
    if (isNaN(m)) m = today.getMonth();
    if (isNaN(d)) d = today.getDate();
    if (isNaN(hr)) hr = today.getHours();
    if (isNaN(min)) min = today.getMinutes();

    if (y != 0 && m != -1 && d != 0)
        return new Date(y, m, d, hr, min, 0);
    y = 0; m = -1; d = 0;

    for (i = 0; i < a.length; ++i) {
        if (a[i].search(/[a-zA-Z]+/) != -1) {
            var t = -1;
            for (j = 0; j < 12; ++j) {
                if (Date._MN[j].substr(0, a[i].length).toLowerCase() == a[i].toLowerCase()) { t = j; break; }
            }
            if (t != -1) {
                if (m != -1) { d = m+1; }
                m = t;
            }

        } else if (parseInt(a[i], 10) <= 12 && m == -1) { m = a[i]-1;

        } else if (parseInt(a[i], 10) > 31 && y == 0) {
            y = parseInt(a[i], 10);
            (y < 100) && (y += (y > 29) ? 1900 : 2000);
        } else if (d == 0) { d = a[i]; }
    }

    if (y == 0) y = today.getFullYear();
    if (m != -1 && d != 0) return new Date(y, m, d, hr, min, 0);
    return today;
};



/* Formatea una fecha de acuerdo a la expresion str, si no se pasa el formato se usa la expresion por defecto*/
Date.formatDate = function (date, str) {
    if(str == null) str = Date._defaultOutputFormat;

    var m = date.getMonth();
    var d = date.getDate();
    var y = date.getFullYear();
    var wn = date.getWeekNumber();
    var w = date.getDay();
    var s = {};
    var hr = date.getHours();
    var pm = (hr >= 12);
    var ir = (pm) ? (hr - 12) : hr;
    var dy = date.getDayOfYear();
    if (ir == 0)
        ir = 12;
    var min = date.getMinutes();
    var sec = date.getSeconds();

    var w_monday = w == 0 ? 6 : (w-1);

    s["%a"] = Date._SDN[w_monday]; // abbreviated weekday name [FIXME: I18N]
    s["%A"] = Date._DN[w_monday]; // full weekday name
    s["%b"] = Date._SMN[m]; // abbreviated month name [FIXME: I18N]
    s["%B"] = Date._MN[m]; // full month name
    // FIXME: %c : preferred date and time representation for the current locale
    s["%C"] = 1 + Math.floor(y / 100); // the century number
    s["%d"] = (d < 10) ? ("0" + d) : d; // the day of the month (range 01 to 31)
    s["%e"] = d; // the day of the month (range 1 to 31)
    // FIXME: %D : american date style: %m/%d/%y
    // FIXME: %E, %F, %G, %g, %h (man strftime)
    s["%H"] = (hr < 10) ? ("0" + hr) : hr; // hour, range 00 to 23 (24h format)
    s["%I"] = (ir < 10) ? ("0" + ir) : ir; // hour, range 01 to 12 (12h format)
    s["%j"] = (dy < 100) ? ((dy < 10) ? ("00" + dy) : ("0" + dy)) : dy; // day of the year (range 001 to 366)
    s["%k"] = hr;		// hour, range 0 to 23 (24h format)
    s["%l"] = ir;		// hour, range 1 to 12 (12h format)
    s["%m"] = (m < 9) ? ("0" + (1+m)) : (1+m); // month, range 01 to 12
    s["%o"] = (1+m); // month, range 1 to 12
    s["%M"] = (min < 10) ? ("0" + min) : min; // minute, range 00 to 59
    s["%n"] = "\n";		// a newline character
    s["%p"] = pm ? "PM" : "AM";
    s["%P"] = pm ? "pm" : "am";
    // FIXME: %r : the time in am/pm notation %I:%M:%S %p
    // FIXME: %R : the time in 24-hour notation %H:%M
    s["%s"] = Math.floor(date.getTime() / 1000);
    s["%S"] = (sec < 10) ? ("0" + sec) : sec; // seconds, range 00 to 59
    s["%t"] = "\t";		// a tab character
    // FIXME: %T : the time in 24-hour notation (%H:%M:%S)
    s["%U"] = s["%W"] = s["%V"] = (wn < 10) ? ("0" + wn) : wn;
    s["%u"] = String(w_monday);	// the day of the week (range 1 to 7, 1 = MON)
    s["%w"] = String(w);		// the day of the week (range 0 to 6, 0 = SUN)
    // FIXME: %x : preferred date representation for the current locale without the time
    // FIXME: %X : preferred time representation for the current locale without the date
    s["%y"] = ('' + y).substr(2, 2); // year without the century (range 00 to 99)
    s["%Y"] = y;		// year with the century
    s["%%"] = "%";		// a literal '%' character

    var re = /%./g;

    var is_ie = ( /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent) );

    var is_ie5 = ( is_ie && /msie 5\.0/i.test(navigator.userAgent) );

    /// detect KHTML-based browsers
    var is_khtml = /Konqueror|Safari|KHTML/i.test(navigator.userAgent);


    if (!is_ie5 && !is_khtml)
        return str.replace(re, function (par) { return s[par] || par; });

    var a = str.match(re);
    for (var i = 0; i < a.length; i++) {
        var tmp = s[a[i]];
        if (tmp) {
            re = new RegExp(a[i], 'g');
            str = str.replace(re, tmp);
        }
    }

    return str;
};


/* Abreviaciones de los metodos de parseo y formato */

Date.parse = Date.parseDate;

Date.format = Date.formatDate;

Date.prototype.format = function (str) { return Date.formatDate(this, str); };


/* --------------------------------------------------------------------------------------------------------- */
/* Funciones estï¿½ticas ï¿½tiles de fecha */


/* Obtiene la cantidad de dias de un mes */
Date.getMonthDays = function(month, year) {

    if(year == null || month == null) {

        var fecha = new Date();
        if(year == null) year = fecha.getFullYear();
        if(month == null) month = fecha.getFullYear();
    }

    if(month == 1) return year % 4 == 0 ? 29 : 28;
    else if(month == 0 || month == 2 || month == 4 || month == 6 || month == 7 || month == 9 || month == 11) return 31;
    else return 30;

};



/* Obtiene la cantidad de dias de un aï¿½o */
Date.getYearDays = function(year) {

    if(year == null) {

        var fecha = new Date();
        year = fecha.getFullYear();
    }

    return year % 4 == 0 ? 366 : 365;
};



/* Obtiene la fecha unix (segundos desde 1/1/1970) actual */
Date.getUnixTime = function(){
    return new Date().getUnixTime();
};


/* Obtiene una fecha desde una fecha unix (segundos) */
Date.fromUnixTime = function(s){
    var date = new Date();
    date.setTime(s*1000);
    return date;
};


/* Obtiene una fecha desde los milisegundos desde 1/1/1970 */
Date.fromTime = function(t){
    var date = new Date();
    date.setTime(t);
    return date;
};


/* Obtiene los milisegundos */
Date.getTime = function() {
    return new Date().getTime();
};

/*---------------------------------------------------------------------------------------------------------- */
/* Funciones adicionales ï¿½tiles de fecha */


/* Obtiene el dï¿½a del aï¿½o */
Date.prototype.getDayOfYear = function() {
    var now = new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
    var then = new Date(this.getFullYear(), 0, 0, 0, 0, 0);
    var time = now - then;
    return Math.floor(time / Date.DAY);
};


/* Obtiene la cantidad de dias (si tambien se obtiene contando horas, el resultado es un decimal) */
Date.prototype.getDays = function( alsoHours ) {

    var days = 0;
    var i;

    var year = this.getFullYear();
    for(i=0; i < year; i++) days += Date.getYearDays( i );

    var month = this.getMonth();
    for(i=0; i < month; i++) days += Date.getMonthDays( i, year );

    days += this.getDate();

    if(alsoHours) {

        days += (this.getHours() / 24 );
        days += (this.getMinutes() / (24 * 60));
        days += (this.getSeconds() / (24 * 60 * 60));
    }

    return days;
};


/* Establece la cantidad de dï¿½as */
Date.prototype.setDays = function( days ) {

    var year = 0;
    var month = 0;
    var day = 0;

    var yearDays;
    var monthDays;

    while(days > (yearDays = Date.getYearDays(year))) {

        days -= yearDays;
        year++;
    }

    while(days > (monthDays = Date.getMonthDays(month, year))) {

        days -= monthDays;
        month++;
    }

    this.setFullYear(year);
    this.setMonth(month);
    this.setDate( Math.floor(days) );

    var seconds = (Math.floor(days) - days) * (24*60*60);
    this.setSeconds( seconds );

};



/* Obtiene la diferencia de dias con otra fecha */
Date.prototype.daysDiff = function( otherDate ) {
    return Math.ceil( this.getDays(true) - otherDate.getDays(true)) + 1;
};



/* Agrega dias a la fecha */
Date.prototype.addDays = function( days ) {
    this.setDate( this.getDate() + days );
    return this;
};


/* Agrega meses a la fecha */
Date.prototype.addMonths = function( months ) {
    this.setMonth( this.getMonth() + months );
    return this;
};


/* Agrega aï¿½os a la fecha */
Date.prototype.addYears = function( years ) {
    this.setFullYear( this.getFullYear() + years );
    return this;
};


/* Returns the number of the week in year, as defined in ISO 8601. */
Date.prototype.getWeekNumber = function() {
    var d = new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
    var DoW = d.getDay();
    d.setDate(d.getDate() - (DoW + 6) % 7 + 3); // Nearest Thu
    var ms = d.valueOf(); // GMT
    d.setMonth(0);
    d.setDate(4); // Thu in Week 1
    return Math.round((ms - d.valueOf()) / (7 * 864e5)) + 1;
};


/* Obtiene la fecha unix de la fecha */
Date.prototype.getUnixTime = function() {
    return Math.floor(this.getTime() / 1000);
};



/* Establece la fecha unix */
Date.prototype.setUnixTime = function(s) {
    this.setTime(s*1000);
    return this;
};


/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Configuraciones Globales --------------------------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */
//
//window['_escape'] = window['escape'];
//window['_unescape'] = window['unescape'];
//
//window['escape'] = function(s) {
//	return window['_escape'](s).replace(/\+/g, '%2B');
//};
//
//window['unescape'] = function(s) {
//	return window['_unescape'](s).replace(/\%2B/g, '+');
//};
//
///* For jquery $.param compat, iso-8859-15 not working in 1.9 */
window['encodeURIComponent'] = $.urlencode;
window['decodeURIComponent'] = $.urldecode;




/*----------------------------------------------------------------------------------------------------------------------------------------------- */
/* Extensiï¿½n de Bootstrap Dialog  --------------------------------------------------------------------------------------------------------------- */
/*----------------------------------------------------------------------------------------------------------------------------------------------- */

jQuery.modal = new Object();

jQuery.modal.close = function(iter)
{
    var iterations = arguments.length > 0 ? iter : 1;

    for(var i=0; i<iterations; i++)
    {
        $('.modal.in').last().modal('hide');
    }

};

jQuery.modal.closeAll = function()
{
    $('.modal').each(function(index, item){
        $(item).modal('hide');
    });
};

jQuery.modal.hideAll = function()
{
    $('.modal.in').css({'visibility': 'hidden'});
};

jQuery.modal.current = function()
{
    return $('.modal.in').last();
};

jQuery.fn.zmodal = function(options)
{
    var $this = this;

    if($this.find('.modal-body').length == 0)
    {
        return $('<div />').append($('<div />').addClass('modal-body').append($this)).zmodal(options);
    }

    if($this.find('.modal-dialog').length == 0)
    {
        return $('<div />').append($('<div />').addClass('modal-dialog').append($this)).zmodal(options);
    }


    options = $.extend({}, jQuery.fn.zmodal.defaultOptions, options);

    if(options['closeAll'])
    {
        $.modal.closeAll();
    }

    var modalBody = $this.find('.modal-body');
    var modalDialog = $this.find('.modal-dialog').css($.extend({}, {'min-width': '600px', 'margin': '2% auto 0'}, options['dialog-style']));

    this.on('shown.bs.modal', function() {

        var $this = $(this);

        $this.find('button').eq(0).focus();
        $this.find('[autofocus]').focus();
        $this.adjustChildrenMaxWidth();

        // setTimeout(function() {
        //    $this.find('[autofocus]').focus();
        // }, options['firstFocusDelay']);

        // setTimeout(function() {
        //     $('body').css({'padding-right': 0});
        //     modalDialog.css({'margin-top': 100});
        // }, 0);
        //console.log('a');
        //modalDialog.width('auto');
        //modalBody.width(modalDialog.width() + 30);
        //modalDialog.css({
        //    'margin-left': ($(window).width() - modalDialog.width()) / 2,
        //    'margin-top': (($(window).height() - modalDialog.height()) / 2) - 40
        //});

        //var $this = $(this);
        //setTimeout(function() {
        //    modalDialog.firstFocus();
        //}, options['firstFocusDelay']);
    });

    return $(this).modal(options);
};

jQuery.fn.zmodal.defaultOptions =
{
    'keyboard': true,
    'firstFocusDelay': 100,
    'backdrop': 'static',
    'closeAll': false,
    'show': true,
    'dialog-style': {}

};

jQuery.zmodalAjax = function(url, options)
{
    return jQuery.zmodalAjaxGet(url, options);
};

jQuery.zmodalAjaxGet = function(url, options)
{
    options = $.extend({}, {url: url, type: 'get'}, options);

    var successCallback = options['success'];
    var $modal = $('<div />').appendTo('body');

    var dialog = $('<div />').addClass('modal fade').appendTo('body');
    var modalDialog = $('<div />').addClass('modal-dialog').appendTo(dialog);
    var modalContent = $('<div />').addClass('modal-content').appendTo(modalDialog);

    if(options['title'])
    {
        $('<div />').addClass('modal-header').appendTo(modalContent).append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>').append($('<h4 />').html(options['title']).css({'font-weight': 'bold'}));
    }

    var dialogBody = $('<div />').addClass('modal-body').appendTo(modalContent);

    var dialogFooter = $('<div />').addClass('modal-footer').appendTo(modalContent);

    if(options['close-button'] || options['close_button']) {

        var button = $('<button type="button" />').addClass('btn btn-primary first-focus').html(options['button'] ? options['button'] : Strings.Get('close')).appendTo(dialogFooter).css({'padding': '6px 40px'});

        button.bind('click', function () { $.zmodal.closeAll(); });
    }

    options['success'] = function(html)
    {
        dialogBody.html(html);

        if(successCallback)
        {
            successCallback.apply(this, arguments);
        }

        dialog.zmodal(options);
    };

    $.ajax(options);

    return $modal;
};

jQuery.zmodalAjaxPost = function(url, options)
{
    return jQuery.zmodalAjaxGet(url, $.extend({}, {type: 'post'}, options));
};

jQuery.zmodalAjaxPostData = function(url, data, options)
{
    options = $.extend({}, {data: data}, options);
    return jQuery.zmodalAjaxPost(url, options);
};

jQuery.zmodalAjaxGetData = function(url, data, options)
{
    options = $.extend({}, {data: data}, options);
    return jQuery.zmodalAjaxGet(url, options);
};


jQuery.zmodalAjaxData = function(url, data, options)
{
    options = $.extend({}, {data: data}, options);
    return jQuery.zmodalAjax(url, options);
};



jQuery.zmodal = new Object();

jQuery.zmodal.close = function()
{
    jQuery.modal.close();
}

jQuery.zmodal.closeAll = function()
{
    jQuery.modal.closeAll();
}

jQuery.zmodal.hideAll = function()
{
    jQuery.modal.hideAll();
}

jQuery.zmodal.current = function()
{
    jQuery.modal.current();
}

jQuery.zmodal.loading = function(callback, options)
{
    var dialog = $('<div />').addClass('modal fade').appendTo('body');
    var modalDialog = $('<div />').addClass('modal-dialog').appendTo(dialog);
    var modalContent = $('<div />').addClass('modal-content').appendTo(modalDialog);
    var dialogBody = $('<div />').addClass('modal-body').appendTo(modalContent);

    var block = $('<div />').addClass('loading modal-loading').appendTo(dialogBody);

    var contentBlock = $('<div />').addClass('content-block').appendTo(block).append('<br />').css({'text-align': 'center'});

    var iconBlock = $('<div />').css({'display': 'inline-block', 'margin': '10px auto 30px', 'padding-left': 40, 'background-position': 'left center'}).appendTo(contentBlock).addClass('loading-center-22').html(Strings.Get('loading') + '...');

    dialog.on('shown.bs.modal', function() {
        if(callback)
        {
            callback.apply(this, arguments);
        }
    });

    return dialog.zmodal(options);
};

jQuery.zmodal.alert = function(msg, callback, title, options)
{
    var dialog = $('<div />').addClass('modal fade').appendTo('body');
    var modalDialog = $('<div />').addClass('modal-dialog').appendTo(dialog);
    var modalContent = $('<div />').addClass('modal-content strong').appendTo(modalDialog);

    if(title)
    {
        var modalTitle = $('<div />').addClass('modal-header').appendTo(modalContent).append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>').append($('<h4 />').html(title).css({'font-weight': 'bold'}));
    }

    var dialogBody = $('<div />').addClass('modal-body').appendTo(modalContent).append(msg);
    var dialogFooter = $('<div />').addClass('modal-footer').appendTo(modalContent);

    options = $.extend({}, options);
    var button = $('<button type="button" />').addClass('btn btn-primary first-focus').html(options['button'] ? options['button'] : Strings.Get('accept')).appendTo(dialogFooter);

    if(callback)
    {
        button.bind('click', callback);
    }
    else
    {
        button.attr('data-dismiss', 'modal');
    }

    return dialog.zmodal(options);
};


jQuery.zmodal.alertClose = function(msg, callback, title, options)
{
    var dialog = $('<div />').addClass('modal fade').appendTo('body');
    var modalDialog = $('<div />').addClass('modal-dialog').appendTo(dialog);
    var modalContent = $('<div />').addClass('modal-content strong').appendTo(modalDialog);

    if(title)
    {
        var modalTitle = $('<div />').addClass('modal-header').appendTo(modalContent).append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>').append($('<h4 />').html(title).css({'font-weight': 'bold'}));
    }

    var dialogBody = $('<div />').addClass('modal-body').appendTo(modalContent).append(msg);
    var dialogFooter = $('<div />').addClass('modal-footer').appendTo(modalContent);

    options = $.extend({}, options);
    var button = $('<button type="button" />').addClass('btn btn-primary first-focus').html(options['button'] ? options['button'] : Strings.Get('accept')).appendTo(dialogFooter);

    button.attr('data-dismiss', 'modal');

    if(callback)
    {
        dialog.on('hidden.bs.modal', callback);
    }

    return dialog.zmodal(options);
};

jQuery.zmodal.alertCloseAll = function(msg, callback, title, options)
{
    return jQuery.zmodal.alert(msg, function() {

        $.zmodal.closeAll();

        if(callback)
        {
            callback.apply(this, arguments);
        }

    }, title, options);
};

jQuery.zmodal.confirm = function(html, callback, title, options)
{
    var dialog = $('<div />').addClass('modal fade').appendTo('body');
    var modalDialog = $('<div />').addClass('modal-dialog').appendTo(dialog);
    var modalContent = $('<div />').addClass('modal-content').appendTo(modalDialog);
    var modalTitle = $('<div />').addClass('modal-header').appendTo(modalContent).append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>').append($('<h4 />').html(title ? title : Strings.Get('confirm')).css({'font-weight': 'bold'}));
    var dialogBody = $('<div />').addClass('modal-body').appendTo(modalContent).append(html);
    var dialogFooter = $('<div />').addClass('modal-footer').appendTo(modalContent);

    var confirmButton = $('<button type="button" />').addClass('btn btn-success first-focus').html(Strings.Get('accept')).appendTo(dialogFooter);
    confirmButton.bind('click', function() {

        if(callback)
        {
            callback.apply(this, arguments);
        }
    });

    var cancelButton = $('<button type="button" />').addClass('btn btn-default').css({'margin-left': 20}).html(Strings.Get('cancel')).appendTo(dialogFooter);
    cancelButton.bind('click', function() { $.zmodal.close(); });


    return dialog.zmodal(options);
};

/*---------------------------------------------------------------------------------------------*/

jQuery.fn.submitAjaxForm = function(callback)
{
    var $form = $(this);

    var url = $form.attr('action') ? $form.attr('action') : '';
    var method = String($form.attr('method') ? $form.attr('method') : 'post').toUpperCase();

    $form.attr('action', url);
    $form.attr('method', method);

    if($form.find('[name=_token]').length == 0)
    {
        $form.append($("<input type='hidden' />").attr('name', '_token').val($('meta[name="csrf-token"]').attr('content')));
    }

    if(method == 'POST' && $form.find('input[type=file]').length > 0)
    {
        $form.attr('enctype', "multipart/form-data");

        var iframeName = $form.data('__submitAjaxFormIframeName__');

        if(!iframeName)
        {
            iframeName = 'unique' + (new Date().getTime());
            $('<iframe src="javascript:false;" name="'+iframeName+'" id="'+iframeName+'" />').hide().appendTo('body');
            $form.data('__submitAjaxFormIframeName__', iframeName);
        }

        var iframe = $('#' + iframeName);

        $form.attr('target',iframeName);

        iframe.unbind('load');

        iframe.bind('load', function() {

            var doc = null;

            try {
                if (iframe[0].contentWindow) {
                    doc = iframe[0].contentWindow.document;
                }
            } catch(err) {
            }

            if (!doc)
            {
                try
                {
                    doc = iframe[0].contentDocument ? iframe[0].contentDocument : iframe[0].document;
                } catch(err) {
                    doc = iframe[0].document;
                }
            }

            if(doc)
            {
                var docRoot = doc.body ? doc.body : doc.documentElement;
                var dataText;

                if(docRoot.innerText)
                {
                    dataText = docRoot.innerText;
                }
                else
                {
                    dataText = docRoot.textContent;
                }

                if(dataText)
                {
                    eval('var data = ' + dataText);

                    if (callback) {
                        callback.call($form, data);
                    }
                }
            }
        });

        $form.get(0).submit();
    }
    else
    {
        var ajaxParams = {};
        ajaxParams['url'] = $form.attr('action');
        ajaxParams['type'] = $form.attr('method');
        ajaxParams['data'] = $form.paramMap();

        ajaxParams['success'] = function (data) {

            if (callback) {
                callback.call($form, data);
            }
        };

        $.ajax(ajaxParams);
    }
};

jQuery.fn.fireSubmitDialog = function(callback)
{
    var $this = this;
    var form = this.get(0);

    if($this.data('fireSubmitDialog'))
    {
        return $this;
    }

    $this.data('fireSubmitDialog', true);

    $.zmodal.loading(function() {

        setTimeout(function () {

            var ajaxFormCallback = function (data) {

                $this.data('fireSubmitDialog', false);

                if (data && data['success']) {

                    $this.find('.error-block').addClass('hide');

                    if (callback) {
                        callback.apply($this, arguments);
                    }
                }
                else {

                    var error = null;

                    if (data && data['error']) {
                        error = data['error'];
                    }

                    if(error)
                    {
                        $this.find('.error-block').html(error).removeClass('hide');
                    }

                    $.zmodal.close();
                }
            };

            $this.submitAjaxForm(ajaxFormCallback);

        }, 500);

    });

    return false;
};

jQuery.fn.bindSubmitDialog = function(callback)
{
    var $this = this;

    if($this.data('bindSubmitDialog'))
    {
        return $this;
    }

    $this.data('bindSubmitDialog', true);

    $this.bind('submit', function(){

        $this.fireSubmitDialog(callback);

        return false;
    });

    return $this;
};


$.fn.formData = function()
{
    return new FormData($(this).get(0));
};

$(document).ready(function() {

    $('body').on('submit', '.form-ajax', function (evt) {
        var form = $(this);
        var url;

        if (form.data('original_action')) {
            url = form.data('original_action');
        }
        else {
            url = form.attr('action');
            form.attr('action', 'javascript:void(0)');
            form.data('original_action', url);
        }

        var isDialog = form.is('.form-dialog');
        var method = form.attr('method') ? form.attr('method') : 'post';
        var data = form.formData();

        var formDataLoadingDialog = String(form.attr('data-loading-dialog')).toLowerCase().trim();
        var showLoadingDialog = !(formDataLoadingDialog == 'false' || formDataLoadingDialog == '0');

        form.find('.form-error-block').remove();

        var ajaxFunction = function () {

            $.ajax({
                'url': url,
                'processData': false,
                'cache': false,
                'contentType': false,
                'type': method,
                'data': data,
                'error': function () {
                    Navigation.reload();
                },
                'success': function (data) {

                    $.zmodal.close();

                    if (data && data['success']) {

                        if(form.triggerHandler('success', data) !== false) {

                            var backUrl = data['redirect_url'];

                            if(!backUrl)
                            {
                                backUrl = form.attr('data-back-url');
                            }

                            if (!backUrl) {

                                if(isDialog)
                                {
                                    if(data['message'])
                                    {
                                        $.zmodal.alertCloseAll(data['message']);
                                    }
                                    else
                                    {
                                        $.zmodal.closeAll();
                                    }
                                }
                                else
                                {
                                    Navigation.reload();
                                }


                            }
                            else {

                                if(data['message'])
                                {

                                    $.zmodal.alertClose(data['message'], function() {
                                        Navigation.go(backUrl);
                                    });

                                }
                                else
                                {
                                    Navigation.go(backUrl);
                                }

                            }
                        }

                    }
                    else {
                        var error;

                        if (data && data['error']) {
                            error = data['error'];
                        }
                        else {
                            error = 'Error';
                        }

                        var errorBlock = $('<div />');
                        errorBlock.addClass('form-error-block alert alert-danger');
                        errorBlock.text(error);

                        if(isDialog)
                        {
                            form.find('.modal-body').prepend(errorBlock);
                        }
                        else
                        {
                            form.prepend(errorBlock);
                        }


                        $('.admin-page-title').scrollWindowViewHeight();

                    }
                }
            });

        };

        if(showLoadingDialog)
        {
            $.zmodal.loading(function () {

                setTimeout(ajaxFunction, 500);
            });
        }
        else
        {
            ajaxFunction();
        }
    });


});