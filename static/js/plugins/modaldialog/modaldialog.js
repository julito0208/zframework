
(function(jQuery){

    if(jQuery.modalDialog) return; 		/* Si ya se definio el plugin se termina */

    /*--------------------------------------------------------------------------------*/
    /* Variables globales privadas ---------------------------------------------------*/


    var
        _defaultOptions = { 	/* Opciones por defecto */

            theme: null,										/* Tema para agregar una imagen al dialogo */
            title: false,										/* Titulo de la ventana del dialogo */
            closeButton: true,									/* Si se debe mostrar el boton de cerrar */
            closeEvent: 'terminate',							/* Evento q se dispara por defecto al salir (con esc del teclado, haciendo click afuera o en el boton del dialogo*/
            easyClose: true,									/* Si se activa el cerrado facil por medio del teclado (tecla esc) o con un click fuera del dialogo) */
            classname: '',										/* Clase que tendr� el dialogo */
            mode: 1,											/* Forma de abrir el dialogo, por defecto reemplaza al anterior */
            animation: 0,										/* Duracion de la animacion de abrir/cerrar */
            stack: true,										    /* Si el dialogo se puede guardar en la pila de dialogos */
            controlSize: false,
            top: 0.2,
            left: 0.5,
            moveable: true,
            autoOpen: false,
            transparent: false
        },

        _openDialogEventName = 'modaldialog-open';

    _readyDialogEventName = 'modaldialog-ready';

    _ajaxRepositionTimeout = 20,


        /* Forma de abrir dialogos */
        _dialogModes = {
            APPEND: 1,				/* Agrega el dialogo a la pila d dialogos */
            REPLACE: 2,				/* Reemplaza el dialogo anterior */
            REPLACEALL: 3			/* Reemplaza todos los dialogos */
        },


        _bodyMinWidth = 965,
        _bodyMinHeight = 0,



        _overlay = null,  										/* Contenedor principal que bloquea la pantalla */
        _overlayVisible = false,								/* Si el overlay est� visible */
        _overlayId = 'modaldialog-overlay',

        _overlayAnimation = 0,

        _overlayOpacity = 0.96,

        _bodySize = {width: 0, height: 0},
        _windowSize = {width: 0, height: 0},

        _overlayZIndex = 1050,

        /* Estilo del overlay */
        _overlayStyle = {
            zIndex: _overlayZIndex,
            top: 0,
            left: 0,
            display: 'none',
            position:'absolute',
            overflow: 'hidden',
            'background-color': '#111'
        },

        /* Espacio minimo entre todo el overlay o ventana y el dialogo (algo asi como el margen del dialogo) */
        _overlayMinSpace = 20,

        _dialogContainerOpacity = 1,
        _dialogContainer = null,								/* Bloque de contenedor del dialogo */
        _dialogContainerVisible = false,						/* Si el dialogo esta visible */
        _dialogContainerClass = 'modaldialog repaint',					/* Clase del dialogo */
        _dialogContainerId = 'modaldialog-container',

        /* Estilo del di�logo */
        _dialogContainerStyle = {
            position: 'absolute',
            zIndex: _overlayZIndex+1,
            display: 'none',
            'text-align': 'left'
        },


        _dialogBody = jQuery(),										/* Cuerpo del dialogo */
        _dialogBodyClass = 'modaldialog-body body repaint',								/* Clase del cuerpo del dialogo */
        _dialogBodyId = 'modaldialog-body',

        /* Estilo del cuerpo del di�logo */
        _dialogBodyStyle = {
            height: 'auto',
            width: 'auto'
        },


        /* Cabecera del dialogo */
        _dialogHeader = null,
        _dialogHeaderId = 'modaldialog-header',
        _dialogHeaderClass = 'header',
        _dialogHeaderStyle = {
            'width': 'auto',
            'height': 'auto',
            'text-align': 'right'
        },

        /* Parte del titulo */
        _dialogHeaderTitle = null,
        _dialogHeaderTitleId = 'modaldialog-header-title',
        _dialogHeaderTitleClass = 'title',
        _dialogHeaderTitleStyle = {},


        /* Texto del titulo */
        _dialogHeaderTitleText = null,
        _dialogHeaderTitleTextId = 'modaldialog-header-title-text',
        _dialogHeaderTitleTextClass = 'text',
        _dialogHeaderTitleTextStyle = { 'float': 'left', 'cursor': 'default' },


        /* Boton salir del titulo */
        _dialogHeaderTitleCloseButton = null,
        _dialogHeaderTitleCloseButtonId = 'modaldialog-header-title-close-button',
        _dialogHeaderTitleCloseButtonClass = 'close-button',
        _dialogHeaderTitleCloseButtonHtml = '&times;',
        _dialogHeaderTitleCloseButtonStyle = { },


        /* Boton salir del dialogo (cuando no hay titulo) */
        _dialogHeaderCloseButton = null,
        _dialogHeaderCloseButtonId = 'modaldialog-header-closebutton',
        _dialogHeaderCloseButtonClass = 'close-button',
        _dialogHeaderCloseButtonHtml = '&times;',
        _dialogHeaderCloseButtonStyle = { },



        _dialog = null,											/* Di�logo activo actualmente */
        _options = null,										/* Opciones actuales del dialogo */
        _content = jQuery(),										/* Contenido actual del dialogo */
        _dialogStack = [],	 									/* Array con los dialogos cargados secuencialmente */
        _listeners = {},


        /* Selectores para obtener el primer elemento en foco del dialogo cuando se abre, se hace array para que se respete el orden */
        _firstFocusElementSelectors = ['.first-focus','.focus','button.button-default','button.default','button','a','input','select','textarea','*'],

        /* Selectores de elementos para foco */
        _focusElementSelectors = 'a,button,textarea,input,iframe,.focus,select,object',

        /* El elemento que debe obtener el foco cuando el dialogo obtiene el foco, generalmente el primer elemento enfocable */
        _focusElement = jQuery(),

        /* Elementos fuera del dialogo que pueden obtener el foco */
        _outerFocusElements = jQuery(),

        /* Algunos eventos m�s comunes */
        _events = ['close', 'terminate', 'focus', 'unload', 'open', 'load', 'resize','paint'],

        /* Disparadores de los eventos */
        _dialogEventsTriggers = {},

        /* Selectores para los elementos que dispararan los eventos, tambi�n se asignan m�s adelante */
        _eventsSelectors = {},

        /* Tecla que cerrar� el dialogo */
        _keyboardCloseEventKey = jQuery.KEY_ESC,

        /* Cual es el evento que se escucha de la tecla para cerrar el dialogo */
        _keyboardCloseEventType = 'keydown',


        /* Formatos de botones */
        _buttonsPresets = {

            //'default': {classname: 'button-default btn btn-default', defaultLabel: Strings.Get('accept'), defaultAction: 'close', defaultType: 'button' },
            'default': {classname: 'btn btn-default', defaultLabel: Strings.Get('accept'), defaultAction: 'close', defaultType: 'button' },

            //'close': {classname: 'button-close btn btn-default', defaultLabel: Strings.Get('close'), defaultAction: 'close', defaultType: 'button' },
            'close': {classname: 'btn btn-info', defaultLabel: Strings.Get('close'), defaultAction: 'close', defaultType: 'button' },

            //'submit': {classname: 'button-submit btn btn-success', defaultLabel: Strings.Get('accept'), defaultAction: 'submit', defaultType: 'submit' },
            'submit': {classname: 'btn btn-success', defaultLabel: Strings.Get('accept'), defaultAction: 'submit', defaultType: 'submit' },

            //'accept': {classname: 'button-accept btn btn-success', defaultLabel: Strings.Get('accept'), defaultAction: 'accept', defaultType: 'button' },
            'accept': {classname: 'btn btn-success', defaultLabel: Strings.Get('accept'), defaultAction: 'accept', defaultType: 'button' },

            //'cancel': {classname: 'button-cancel btn btn-default', defaultLabel: Strings.Get('cancel'), defaultAction: 'cancel', defaultType: 'button' },
            'cancel': {classname: 'btn btn-default', defaultLabel: Strings.Get('cancel'), defaultAction: 'cancel', defaultType: 'button' },

            'next': {classname: 'button-next btn btn-default', defaultLabel: Strings.Get('next'), defaultAction: 'next', defaultType: 'button' },

            'back': {classname: 'button-back btn btn-default', defaultLabel: Strings.Get('previous'), defaultAction: 'back', defaultType: 'button' },

            'download': {classname: 'button-download btn btn-default', defaultLabel: Strings.Get('download'), defaultAction: 'download', defaultType: 'button' },

            'print': {classname: 'button-print btn btn-default', defaultLabel: Strings.Get('print'), defaultAction: 'print', defaultType: 'button' },

            'save': {classname: 'button-save btn btn-default', defaultLabel: Strings.Get('save'), defaultAction: 'save', defaultType: 'button' },

            'delete': {classname: 'button-delete btn btn-default', defaultLabel: Strings.Get('delete'), defaultAction: 'delete', defaultType: 'button' },

            'refresh': {classname: 'button-refresh btn btn-default', defaultLabel: Strings.Get('update'), defaultAction: 'refresh', defaultType: 'button' }
        },


        _buttonsClass = 'modaldialog-button',
        //_buttonsClass = '',

        //_buttonsHoverClass = 'modaldialog-button-hover',
        _buttonsHoverClass = '',

        //_buttonsPressedClass = 'modaldialog-button-pressed',
        _buttonsPressedClass = '',

        //_buttonsFocusedClass = 'modaldialog-button-focused',
        _buttonsFocusedClass = '',

        _buttonsIconClass = 'modaldialog-button-icon',
        _buttonsSelector = '.button-default, .button-close',
        _buttonsIconSelector = '.button-submit, .button-accept, .button-cancel, .button-next, .button-back, .button-download, .button-print, .button-save, .button-refresh, .button-delete',


        _documentBodyClass = 'under-modaldialog',


        /* Clase para algunos temas predefinidos en la hoja de estilo */
        _themesClasses = {'error': 'modaldialog-error', 'caution': 'modaldialog-caution', 'question': 'modaldialog-question'},



        _customDialogPosition = false,

        _customDialogPositionOffset = {},

        _customDialogPositionAllowedRanges = {}

    _dialogHeaderTitleCloseButtonAloneClass = 'alone-button';


    jQuery.each(['close','terminate','submit','cancel','next','back','download','print','save','go','refresh'], function(index, name){
        _eventsSelectors[name] = '.action-' + name;
    });



    jQuery('#' + _dialogContainerId + ' .' + _buttonsClass).
    live('mouseenter', function() { jQuery(this).addClass(_buttonsHoverClass); }).
    live('mouseleave', function() { jQuery(this).removeClass(_buttonsHoverClass + ' ' + _buttonsPressedClass); }).
    live('mousedown', function() { jQuery(this).addClass(_buttonsPressedClass); }).
    live('mouseup', function() { jQuery(this).removeClass(_buttonsPressedClass); }).
    live('focus', function() { jQuery(this).addClass(_buttonsFocusedClass); } ).
    live('blur', function() { jQuery(this).removeClass(_buttonsFocusedClass); } );



    _closeAllDialogsListeners = [];
    _allDialogsClosed = true;

    _currentDialog = null;

    /*--------------------------------------------------------------------------------*/
    /*--------------------------------------------------------------------------------*/

    var _isNode = function(arg, nodeType) {

        if(arg && (typeof arg == 'string' || (typeof arg == 'object' && (arg instanceof jQuery || arg.nodeType)))) {
            arg = jQuery(arg);
            if(arg.length > 0 && (!nodeType || (nodeType && arg.is(nodeType)))) return true;
        }

        return false;

    };


    var _objectDeepCopy = function(obj) {

        if(jQuery.isPlainObject(obj) || jQuery.isArray(obj)) {

            var copy = jQuery.isArray(obj) ? [] : {};
            jQuery.each(obj, function(key, value) { copy[key] = _objectDeepCopy(value); } );
            return copy;

        } else return obj;

    };

    /*--------------------------------------------------------------------------------*/
    /*--------------------------------------------------------------------------------*/

    var _parsePositionValue = function(value, avalSize) {

        if(typeof value != 'number') {

            value = String(value).replace(/\s/g, '').toLowerCase();

            if(value == '')	value = 0;

            else {

                var match = value.match(/^(\-?\d+(?:\.\d+)?)(?:px|(\%))?$/);

                if(match) {

                    value = parseInt(match[1]);
                    if(match[2]) value = value / 100;

                } else value = 0;
            }
        }


        if(value < 1 && value > -1) return Math.floor(avalSize * value);
        else return value;

    };


    /*--------------------------------------------------------------------------------*/
    /* M�todos globales privados para el overlay -------------------------------------*/


    var _updateOverlaySize = function() {

        _bodySize = {width: document.body.clientWidth, height: document.body.scrollHeight + document.body.scrollTop + window.pageYOffset};

        /*
         _windowSize = {
         width: document.documentElement.clientWidth ? document.documentElement.clientWidth : document.documentElement.scrollWidth,
         height: document.documentElement.clientHeight ? document.documentElement.clientHeight : document.documentElement.scrollHeight};
         */

        _windowSize = {
            width: window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth||0,
            height: window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight||0};

        _overlay.css({width: Math.max(_bodyMinWidth, _bodySize.width), height: Math.max(_bodyMinHeight, Math.max(_bodySize.height, _windowSize.height))});

        _updateDialogSize();
    };


    /* Funci�n para el evento resize de la ventana */
    var _windowResizeListener = function(event) {
        _updateOverlaySize();

    };


    /* Funci�n para el evento scroll de la ventana */
    var _windowScrollListener = function(event) {

        //if(_bodySize.width != document.body.clientWidth || _bodySize.height != document.body.scrollHeight) _updateOverlaySize();
        //else _updateDialogPosition();
        _updateOverlaySize();
    };


    /* Muestra el overlay */
    var _showOverlay = function(callback) {

        if(_overlayVisible) {
            if(callback) callback();
            return;  	/* Si ya est� visible termina */
        }


        /* S�lo cuando el documento est� cargado */
        jQuery(document).ready(function() {

            /* Si todav�a no se cre� el overlay, se lo crea ahora */
            if(!_overlay) {

                _overlay = jQuery('<div />').
                attr('id', _overlayId).
                css(_overlayStyle).
                css('opacity',0).
                prependTo(document.body);

            }


            /* Se obtienen todos los elementos que no sean del dialogo para evitar que obtengan el foco */

            jQuery(document.body).addClass(_documentBodyClass);

            var bodyElements = jQuery(document.body).children().not(_overlay.add(_dialogContainer));

            _outerFocusElements = bodyElements.
            filter(_focusElementSelectors).
            add(bodyElements.find(_focusElementSelectors)).
            bind('focus',_outerFocusElementsFocus).
            bind('blur',_outerFocusElementsBlur);

            _updateOverlaySize();

            jQuery(window).
            bind('resize', _windowResizeListener).
            bind('scroll', _windowScrollListener);

            _overlayVisible = true;
            _overlay.show().fadeTo(_overlayAnimation, _overlayOpacity, callback);
        });

    };


    /* Oculta el overlay */
    var _hideOverlay = function(callback) {

        if(!_overlayVisible) {
            if(callback) callback();
            return;   /* Si no est� visible termina */
        }

        jQuery(window).
        unbind('resize', _windowResizeListener).
        unbind('scroll', _windowScrollListener);


        /* Se devuelve el comportamiento normal de los elementos fuera del dialogo */
        _outerFocusElements.
        unbind('focus',_outerFocusElementsFocus).
        unbind('blur',_outerFocusElementsBlur);

        _overlay.fadeTo(_overlayAnimation, 0, function(){
            jQuery(document.body).removeClass(_documentBodyClass);
            _overlayVisible = false;
            _overlay.hide();


            if(callback) callback();
        });


    };


    /*--------------------------------------------------------------------------------*/
    /* M�todos globales privados para el di�logo -------------------------------------*/



    /* Actualiza el tama�o del di�logo */
    var _updateDialogSize = function() {

        if(!_dialogContainerVisible) return;

        _dialogContainer.width('auto').height('auto');

        _dialogContainer.css('overflow','hidden');

        _dialogContainer.repaintAll();

        var _prevDialogContainerSize = _dialogContainer.dimension();
        var _dialogContainerSize = _dialogContainer.dimension();

        if(_overlayVisible && _options.controlSize) {

            if(_windowSize.width - (_overlayMinSpace*2) < _dialogContainerSize.width) _dialogContainerSize.width = _dialogContainer.width(_windowSize.width - (_overlayMinSpace*2)).width();
            if(_windowSize.height - (_overlayMinSpace*2) < _dialogContainerSize.height) _dialogContainerSize.height = _dialogContainer.height(_windowSize.height - (_overlayMinSpace*2)).height();
        }


        _dialogContainer.repaintAll();
        _dialogContainer.css('overflow','');

        _handleEvent('resize');

        _updateDialogPosition();
    };



    /* Actualiza la posici�n del di�logo */
    var _updateDialogPosition = function() {

        if(!_dialogContainerVisible) return;

        var $window = jQuery(window);

        if(_customDialogPosition && _options.moveable) {
            _customDialogPositionAllowedRanges = {left: [$window.scrollLeft(), $window.scrollLeft() + _windowSize.width - _dialogContainer.outerWidth()], top: [$window.scrollTop(), $window.scrollTop() + _windowSize.height - _dialogContainer.outerHeight()]};

            var _dialogPos = _dialogContainer.positionAbsolute();
            var top = _dialogPos.top;
            var left = _dialogPos.left;

            if(top > _customDialogPositionAllowedRanges.top[1]) top = _customDialogPositionAllowedRanges.top[1];
            else if(top < _customDialogPositionAllowedRanges.top[0]) top = _customDialogPositionAllowedRanges.top[0];

            if(left > _customDialogPositionAllowedRanges.left[1]) left = _customDialogPositionAllowedRanges.left[1];
            else if(left < _customDialogPositionAllowedRanges.left[0]) left = _customDialogPositionAllowedRanges.left[0];

            _dialogContainer.css({top: top, left: left});

        } else {

            _dialogContainer.css('overflow','hidden').repaintAll();

            _dialogContainer.css({'top': $window.scrollTop(), 'left': $window.scrollLeft()});

            var dialogContainerWidth = _dialogContainer.width();
            var dialogContainerHeight = _dialogContainer.height();

            var dialogContainerX = _parsePositionValue(_options.left, _windowSize.width - dialogContainerWidth) + $window.scrollLeft();
            var dialogContainerY = _parsePositionValue(_options.top, _windowSize.height - dialogContainerHeight) + $window.scrollTop();

            if(dialogContainerY < 0) dialogContainerY = 5;

            _dialogContainer.css({
                left: dialogContainerX,
                top:  dialogContainerY});


            _dialogContainer.css('overflow','');

        }

    };


    /* Actualiza las opciones del di�logo */
    var _updateDialogOptions = function(options) {

        _clearDialogOptions();

        if(options) {

            _options = options;

            _dialogContainer.addClass(_options.classname);

            if(options.transparent)
            {
                _dialogContainer.addClass('transparent');
            }

            if(_options.theme && _themesClasses[_options.theme])
                _dialogContainer.addClass(_themesClasses[_options.theme]);

            /* Si se activo el cerrado facil, el dialogo dispara el evento _defaultCloseHandler cuando se aprieta esc o un click fuera */
            if(_options.easyClose) {
                jQuery(document).add(jQuery(window)).bind(_keyboardCloseEventType, _keyboardCloseEventHandler);
                _overlay.bind('click', _defaultCloseHandler);
            }

            if(_options.closeButton || _options.title) {

                if(_options.title) {

                    _dialogHeaderTitleText.empty().html(_options.title === true ? '&nbsp;' : _options.title);

                    if(_options.closeButton) _dialogHeaderTitleCloseButton.show();
                    else _dialogHeaderTitleCloseButton.hide();

                    _dialogHeaderTitle.show();
                    _dialogHeaderCloseButton.hide();

                    _dialogHeader.height('auto');

                } else {

                    _dialogHeaderTitle.hide();
                    _dialogHeaderCloseButton.show();

                    _dialogHeader.height('10px');

                }

                _dialogHeader.show();

            } else _dialogHeader.hide();

        }
    };



    /* Actualiza el contenido del di�logo */
    var _updateDialogContent = function(content) {

        _clearDialogContent();

        if(content) {

            _content = jQuery(content).css('display', '').css('visibility', '').removeClass('hidden');
            _dialogBody.append(_content);

            _dialogBody.find(_buttonsSelector).addClass(_buttonsClass);
            _dialogBody.find(_buttonsIconSelector).addClass(_buttonsClass).addClass(_buttonsIconClass);

            /* Se buscan elementos disparadores de eventos */
            jQuery.each(_eventsSelectors, function(action, selector){
                _bindEventTrigger(_dialogBody.find(selector), action, 'click');
            });

            /* Se actualiza el tama�o del dialogo, ya que el contenido ha cambiado */
            _updateDialogSize();


            /* Se buscan los elementos que pueden obtener el foco en el dialogo, el primero de ellos sera el que reciba el foco cuando el dialogo obtenga el foco */
            _focusElement = _dialogBody.find(_focusElementSelectors).eq(0);

            _dialogContainer.repaintAll();

        }

        _handleEvent('repaint');
    };



    /* Elimina las opciones del dialogo, devuelve el comportamiento normal de los elementos que fueron modificados */
    var _clearDialogOptions = function() {

        if(_options) {

            _dialogContainer.removeClass(_options.classname);

            _dialogBody.find(_buttonsSelector).removeClass(_buttonsClass);
            _dialogBody.find(_buttonsIconSelector).removeClass(_buttonsClass).removeClass(_buttonsIconClass);


            if(_options.theme && _themesClasses[_options.theme])
                _dialogContainer.removeClass(_themesClasses[_options.theme]);

            if(_options.easyClose) {
                jQuery(document).add(jQuery(window)).unbind(_keyboardCloseEventType, _keyboardCloseEventHandler);
                _overlay.unbind('click', _defaultCloseHandler);
            }

            _options = null;
        }

    };


    /* Se limpia el contenido del dialogo */
    var _clearDialogContent = function() {

        if(_content) {
            _content.detach();
            _content = jQuery();
        }
    };


    /* Actualiza un dialogo */
    var _openDialog = function(dialog, mode, options, content, listeners) {

        _allDialogsClosed = false;

        if(!_dialogContainer) { /* Si no se creo, se deben crear los elementos del dialogo */

            /* Elemento del texto del titulo de la cabecera */
            _dialogHeaderTitleText = jQuery('<span />').
            addClass(_dialogHeaderTitleTextClass).
            attr('id', _dialogHeaderTitleTextId).
            css(_dialogHeaderTitleTextStyle);

            /* Boton de cerrar del titulo de la cabecera */
            _dialogHeaderTitleCloseButton = jQuery('<span />').
            addClass(_dialogHeaderTitleCloseButtonClass).
            attr('id', _dialogHeaderTitleCloseButtonId).
            css(_dialogHeaderTitleCloseButtonStyle).
            html(_dialogHeaderTitleCloseButtonHtml).
            bind('mousedown', function() { return false; }).
            bind('click', _defaultCloseHandler);


            /* Bloque del titulo de la cabecera (solo aparece si tiene texto) */
            _dialogHeaderTitle = jQuery('<div />').
            addClass(_dialogHeaderTitleClass).
            attr('id', _dialogHeaderTitleId).
            css(_dialogHeaderTitleStyle).
            append(_dialogHeaderTitleText).
            append(_dialogHeaderTitleCloseButton).
            append("<div style='clear: both; max-height: 0px; padding: 0; margin: 0'> </div>").
            bind('mousedown', _startDialogMove);


            /* Boton de cerrar cuando no hay un titulo de cabecera */
            _dialogHeaderCloseButton = jQuery('<span />').
            addClass(_dialogHeaderCloseButtonClass).
            addClass(_dialogHeaderTitleCloseButtonAloneClass).
            attr('id', _dialogHeaderCloseButtonId).
            css(_dialogHeaderCloseButtonStyle).
            html(_dialogHeaderCloseButtonHtml).
            bind('click', _defaultCloseHandler);


            /* Bloque de la cabecera */
            _dialogHeader = jQuery('<div />').
            addClass(_dialogHeaderClass).
            attr('id', _dialogHeaderId).
            css(_dialogHeaderStyle).
            append(_dialogHeaderTitle).
            append(_dialogHeaderCloseButton);

            /* Cuerpo del dialogo */
            _dialogBody = jQuery("<div />").
            addClass(_dialogBodyClass).
            attr('id', _dialogBodyId).
            css(_dialogBodyStyle);


            /* Contenedor principal del dialogo */
            _dialogContainer = jQuery("<div />").
            addClass(_dialogContainerClass).
            attr('id', _dialogContainerId).
            css(_dialogContainerStyle).
            css('opacity', 0).
            append(_dialogHeader).
            append(_dialogBody).
            prependTo(document.body);
        }

        /*--------------------------------------------------*/

        if(dialog != _dialog && _dialog) {

            if(mode == _dialogModes.APPEND && (!_options || _options.stack)) _dialogStack.push(_dialog);
            else if(mode == _dialogModes.REPLACEALL) _dialogStack = [];
        }

        _dialog = dialog;
        _customDialogPosition = false;

        if(_dialogContainerVisible) {

            _dialogContainer.fadeTo(_options ? _options.animation : 0, 0, function() {

                _dialogContainerVisible = false;
                _openDialog(dialog, mode, options, content, listeners);
            });

            return;

        }

        /*--------------------------------------------------*/


        _listeners = listeners;

        _showOverlay(function(){

            _currentDialog = dialog;

            _updateDialogOptions(options);

            _dialogContainerVisible = true;
            _updateDialogContent(content);

            _handleEvent('prepare');

            if(!_currentDialog._initialized) {

                _currentDialog._initialized = true;

                _handleEvent('init');

            }

            _dialogBody.find('.modaldialog-button-hover').removeClass('modaldialog-button-hover');

            _dialogContainer.find('*').trigger(_openDialogEventName);

            _dialogContainer.fadeTo(_options.animation, _dialogContainerOpacity, function() {

                _dialogContainer.repaintAll();

                /* Se busca el elemento que tendra el foco por primera vez */
                jQuery.each(_firstFocusElementSelectors, function(index, selector){

                    var element = _dialogBody.find(selector).eq(0);
                    if(element.length > 0) {
                        element.trigger('focus');
                        return false;
                    }
                });

                _handleEvent('load');

                _dialogContainer.find('*').trigger(_readyDialogEventName);
            });

            if($.modalDialog.dialog()) {
                $.modalDialog.dialog().resize();
                $.modalDialog.dialog().reposition();
            }

        });

    };




    /* Cierra el di�logo actual */
    var _closeDialog = function(closeAll) {

        if(closeAll) _dialogStack = [];

        if(_dialog && _dialogContainerVisible) {

            _dialogContainerVisible = false;
            _handleEvent('terminate');

            if(_dialogStack.length > 0)
            {
                _currentDialog = _dialogStack[_dialogStack.length-1];
            }

            _dialogContainer.fadeTo(_options.animation, 0, function() {

                _clearDialogOptions();
                _clearDialogContent();

                _dialogContainer.hide();

                if(_dialogStack.length > 0) {

                    _handleEvent('unload');

                    _dialog = null;

                    _listeners = {};

                    _dialogStack.pop().open();

                } else _hideOverlay(function() {

                    _handleEvent('unload');

                    _dialog = null;
                    _listeners = {};

                    _allDialogsClosed = true;

                    _callCloseAllDialogsListeners();

                });
            });
        }
    };


    /* Cierra el di�logo actual */
    var _callCloseAllDialogsListeners = function() {

        jQuery.each(_closeAllDialogsListeners, function(index, fn) {
            fn.call();
        });

        _closeAllDialogsListeners = [];
    };


    /*--------------------------------------------------------------------------------*/
    /* Algunos handlers comunes a varios elementos -----------------------------------*/



    /* Funcion cuando un elemento fuera del dialogo obtiene el foco (se evita que pueda tener el foco y se lo devuelve al dialogo) */
    var _outerFocusElementsFocus = function(event) {
        event.preventDefault();
        event.stopImmediatePropagation();
        event.stopPropagation();
        jQuery(this).blur();
        _focusElement.focus();
    };


    /* Funcion cuando un elemento fuera del dialogo pierde el foco, se cancelan todos los "handlers" siguientes y se evita el comportamiento por defecto */
    var _outerFocusElementsBlur = function(event) {
        event.stopImmediatePropagation();
        event.stopPropagation();
    };


    /* Funcion que cierra el dialogo, disparando el evento indicado en las opciones (por defecto terminate) */
    var _defaultCloseHandler = function(event) {
        return _getEventTrigger(_options.closeEvent).call(this, event);
    };



    /* Funcion "handler" del evento del teclado cuando esta activo easyClose */
    var _keyboardCloseEventHandler = function(event) {
        if(event.which == _keyboardCloseEventKey)
            _defaultCloseHandler.call(this, event);
    };



    /*--------------------------------------------------------------------------------*/


    var _dialogMouseMove = function(event) {

        var top = event.pageY - _customDialogPositionOffset.top;
        var left = event.pageX - _customDialogPositionOffset.left;

        if(top > _customDialogPositionAllowedRanges.top[1]) top = _customDialogPositionAllowedRanges.top[1];
        else if(top < _customDialogPositionAllowedRanges.top[0]) top = _customDialogPositionAllowedRanges.top[0];

        if(left > _customDialogPositionAllowedRanges.left[1]) left = _customDialogPositionAllowedRanges.left[1];
        else if(left < _customDialogPositionAllowedRanges.left[0]) left = _customDialogPositionAllowedRanges.left[0];

        _dialogContainer.css({top: top, left: left});

        return false;
    };

    var _startDialogMove = function(event) {

        if(_options.moveable) {

            var $window = $(window);

            _customDialogPosition = true;
            _customDialogPositionOffset = _dialogContainer.getMousePosition(event);

            _customDialogPositionAllowedRanges = {left: [$window.scrollLeft(), $window.scrollLeft() + _windowSize.width - _dialogContainer.outerWidth()], top: [$window.scrollTop(), $window.scrollTop() + _windowSize.height - _dialogContainer.outerHeight()]};

            _dialogHeaderTitle.css('cursor','move');
            _dialogHeaderTitleText.css('cursor','move');

            $(document).bind('mousemove', _dialogMouseMove).one('mouseup', _stopDialogMove);

            return false;
        }


    };



    var _stopDialogMove = function(event) {

        _dialogHeaderTitle.css('cursor','');
        _dialogHeaderTitleText.css('cursor','default');

        $(document).unbind('mousemove', _dialogMouseMove);

        return false;

    };

    /*--------------------------------------------------------------------------------*/
    /* M�todos globales privados para los eventos ------------------------------------*/


    var _getEventTrigger = function(eventName) {

        if(_dialogEventsTriggers[eventName] == null)
            _dialogEventsTriggers[eventName] = function(event) { return _triggerEvent.call(this, eventName); };

        return _dialogEventsTriggers[eventName];
    };


    /* Funci�n para asignar un evento del dialogo (y su funcion por defecto) a un elemento */
    var _bindEventTrigger = function(element, dialogEvent, elementEvent) {

        if(element.length > 0) {

            if(!elementEvent) elementEvent = 'click';
            var handler = _getEventTrigger(dialogEvent);

            jQuery(element).each(function(){
                var _this = jQuery(this);
                if(_this.is('a') && _this.attr('href') == null) _this.attr('href', 'javascript: void(0);');

                _this.unbind(elementEvent, handler).bind(elementEvent, handler);
            });
        }
    };


    var _callEventHandlers = function(name, listeners, dialog, data, callOnlyData) {

        if(dialog && dialog.content() && dialog.content().tirggerHandler)
            dialog.content().triggerHandler('modaldialog-' + name);


        var handlers = listeners[name];

        if(handlers) {

            var event = jQuery.Event(name);
            var handlerCallArguments = callOnlyData ? [data] : [event, data];

            for(var i=0; i<handlers.length && !event.isImmediatePropagationStopped(); i++) {

                if(handlers[i]) {
                    var result = handlers[i].apply(dialog, handlerCallArguments);

                    if(result === false) {
                        event.stopPropagation();
                        event.preventDefault();
                        break;
                    }
                }
            }

            return !event.isDefaultPrevented();

        } else return true;
    };


    /* Dispara un evento */
    var _triggerEvent = function(name) {
        if(_dialog) _dialog.trigger.apply(_dialog, arguments);
    };



    var _handleEvent = function(name) {
        return _callEventHandlers(name, _listeners, _dialog);
    };


    /*--------------------------------------------------------------------------------*/
    /* Clase del modal dialog (es el objeto devuelto por las funciones de jQuery) ----*/


    var modalDialog = function(content, options) {

        var _content = null;
        var _nodeContent = jQuery();
        var _options = {};
        var _listeners = {};
        var _this = this;
        var _value = null;

        this._initialized = false;
        this._contentExists = false;

        /*------------------------------------------------------------*/

        /* Si este dialogo es el activo actualmente */
        this.isActive = function() {
            return _dialogContainerVisible && _dialog == _this;
        };


        this.attr = function(arg1, arg2) {

            if(arguments.length == 1 && jQuery.isPlainObject(arg1)){

                var needUpdateOptions = false;
                var needUpdateContent = false;
                var needUpdateSize = false;
                var needUpdatePosition = false;

                jQuery.each(arg1, function(name, value){

                    name = String(name).trim();

                    if(name == 'content' || name == 'html' || name == 'body') {

                        needUpdateContent = true;

                        _content = value;

                        if(_isNode(value)) {
                            _nodeContent = $(value).detach();
                            this._contentExists = true;
                        } else {
                            _nodeContent = $('<div />').html(value == null ? '' : value);
                            this._contentExists = false;
                        }

                    } else if(name == 'callback') {

                        _this.unbind('terminate');
                        _this.bind('terminate', value);


                    } else if(name.indexOf('on') === 0) {

                        var event = name.substr(2);
                        _this.unbind(event);
                        _this.bind(event, value);

                    } else if(name == 'value')	{

                        _value = value;

                    } else {

                        if(value === '' || value === 'auto') value = _defaultOptions[name];

                        _options[name] = value;

                        if(name == 'maxWidth' || name == 'minWidth' || name == 'maxHeight' || name == 'minHeight') needUpdateSize = true;
                        else if(name == 'theme' || name == 'title' || name == 'closeButton' || name == 'easyClose' || name == 'classname' || name == 'stack' || name == 'top' || name == 'left' || name == 'moveable') {

                            needUpdateOptions = true;
                            if(name == 'top' || name == 'left' || name == 'moveable') needUpdatePosition = true;
                        }


                    }

                });

                if(_this.isActive()) {


                    if(needUpdateOptions) {
                        _updateDialogOptions(_options);
                        needUpdateSize = true;
                    }

                    if(needUpdateContent) {
                        _updateDialogContent(_nodeContent);
                        needUpdateSize = false;
                    }

                    if(needUpdateSize) _updateDialogSize();
                    else if(needUpdatePosition) _updateDialogPosition();

                }


                return this;

            } else if(arguments.length > 1) {

                var attrs = {};
                attrs[arg1] = arg2;
                return this.attr(attrs);

            } else {

                var name = String(arg1).trim();

                if(name == 'content' || name == 'html') return _content;
                else if(name == 'body') return _nodeContent;
                else if(name.indexOf('on') === 0) return _listeners[name.substr(2)] ? Object.copy(_listeners[name.substr(2)]) : [];
                else if(name == 'callback') return _listeners['terminate'] ? Object.copy(_listeners['callback']) : [];
                else if(name == 'value') return _value;
                else return _objectDeepCopy(_options[name]);

            }
        };



        jQuery.each(['maxWidth','minWidth','maxHeight','minHeight','theme','title','closeButton','easyClose','classname','closeEvent','content','html','mode','stack','value','body','top','left'], function (index, name) {
            _this[name] = function() { return _this.attr.apply(this, jQuery.merge([name], arguments)); };
        });

        /*-----------------------------------------------------------------------------------------------*/

        this.container = function()
        {
            return this.body().getParent().getParent();
        }

        /* Agrega una funcion para ejecutarse en un evento */
        this.bind = function(event, handler) {

            var handlers = [];

            jQuery.each($.makeArray(arguments).slice(1), function(index, value) {
                if(jQuery.isArray(value)) handlers = jQuery.merge(handlers, value);
                else handlers.push(value);
            });

            handlers.remove(null);

            jQuery.each(String(jQuery.isArray(event) ? event.join(' ') : event).replace(/ +/g, ' ').trim().split(' '), function(index, event) {
                if(_listeners[event] == null) _listeners[event] = [];
                _listeners[event].merge(handlers);
            });

            return this;
        };


        /* Remueve una funcion que se ejecuta en un evento */
        this.unbind = function(event, handler) {

            if(arguments.length > 1) {

                var handlers = [];

                jQuery.each($.makeArray(arguments).slice(1), function(index, value) {
                    if(jQuery.isArray(value)) handlers = jQuery.merge(handlers, value);
                    else handlers.push(value);
                });


                jQuery.each(String(jQuery.isArray(event) ? event.join(' ') : event).replace(/ +/g, ' ').trim().split(' '), function(index, event) {
                    if(_listeners[event] != null) _listeners[event].removeAll(handlers);
                });

            } else {

                jQuery.each(String(jQuery.isArray(event) ? event.join(' ') : event).replace(/ +/g, ' ').trim().split(' '), function(index, event) {
                    _listeners[event] = [];
                });

            }



            return this;
        };



        /*-----------------------------------------------------------------------------------------------*/



        this.trigger = function(event, data) {

            if(event == 'open') {

                if(_callEventHandlers(event, _listeners, this, data))
                    _openDialog(this, data == null ? _options.mode : data, _options, _nodeContent, _listeners);

            } else if(event == 'close' || event == 'closeAll') {

                if(this.isActive() && _callEventHandlers(event, _listeners, this, data))
                    if(this.isActive()) _closeDialog(Boolean(data) || event == 'closeAll');

            } else if(event == 'terminate' || event == 'terminateAll' || event == 'cancel') {

                if(this.isActive()) _closeDialog(Boolean(data) || event == 'terminateAll');

            } else if(event == 'resize') {

                if(this.isActive()) _updateDialogSize();

            } else if(event == 'reposition') {

                if(this.isActive()) _updateDialogPosition();

            } else if(event == 'repaint') {

                if(this.isActive()) _updateDialogContent(_nodeContent);

            } else if(event == 'submit') {

                if(arguments.length > 1) {
                    var old_value = _value;
                    _value = data;
                }

                if(!_callEventHandlers(event, _listeners, this, _value, true) && arguments.length > 1)
                    _value = old_value;

            } else {

                _callEventHandlers(event, _listeners, this, data);

            }

            return this;
        };

        this.action = this.trigger;

        jQuery.each(['submit','open','close','terminate','resize','repaint','terminateAll','closeAll','cancel','reposition'], function(index, name){
            _this[name] = function() { return _this.trigger.apply(_this, jQuery.merge([name], jQuery.makeArray(arguments))); }
        });


        this.isInitialized = function() {
            return this._initialized;
        };


        /*-----------------------------------------------------------------------------------------------*/

        jQuery.each(['css','find','children'], function(index, name){
            _this[name] = function() {

                var returnValue = _nodeContent[name].apply(_nodeContent, $.makeArray(arguments));
                if(returnValue === _nodeContent) return _this;
                else return returnValue;

            }
        });

        /*-----------------------------------------------------------------------------------------------*/

        options = jQuery.extend({}, _defaultOptions, options);

        this.attr(options);
        this.content(content);

        if(options.autoOpen) this.open();

        return this;

    };


    /*----------------------------------------------------------------------------------- */
    /* Funcion estatica de jQuery para obtener un objeto modaldialog a partir de un nodo */

    jQuery.modalDialog = function(node, options) {

        options = jQuery.extend.apply(jQuery, jQuery.merge([{}], $.makeArray(arguments).slice(1)));

        if(_isNode(node)) {

            node = $(node);

            var dialog = node.data('__modalDialog__');

            if(!dialog) {
                dialog = new modalDialog(node, options);
                node.data('__modalDialog__', dialog);

            } else dialog.attr(options);

            return dialog;

        } return new modalDialog(node, options);
    };


    jQuery.modalDialog.modeAppend = _dialogModes.APPEND;

    jQuery.modalDialog.modeReplace = _dialogModes.REPLACE;

    jQuery.modalDialog.modeReplaceAll = _dialogModes.REPLACEALL;


    jQuery.modalDialog.modes = _objectDeepCopy(_dialogModes);

    jQuery.modalDialog.current = function() { return _currentDialog; };

    jQuery.modalDialog.dialog = function() { return _dialog; };


    jQuery.modalDialog.active = function() { return _dialog != null; };


    jQuery.modalDialog.body = function() { return _dialogBody; };


    jQuery.modalDialog.trigger = function() { return _dialog ? _dialog.trigger.apply(_dialog, arguments) : null; };


    jQuery.modalDialog.action = jQuery.modalDialog.trigger;


    jQuery.modalDialog.closeAll = function() { return _closeDialog(true); };

    jQuery.modalDialog.close = function() { return _dialog ? _dialog.close() : null };

    jQuery.modalDialog.terminate = function() { return _dialog ? _dialog.terminate() : null };

    jQuery.modalDialog.openDialogEventName = _openDialogEventName;

    jQuery.modalDialog.readyDialogEventName = _readyDialogEventName;

    jQuery.modalDialog.allDialogsClosed = function() {
        return _allDialogsClosed;
    };

    jQuery.modalDialog.appendCloseAllListener = function(callback) {

        _closeAllDialogsListeners.push(callback);

        if(jQuery.modalDialog.allDialogsClosed()) {
            _callCloseAllDialogsListeners();
        }


    };

    jQuery.modalDialog.setOverlayOpacity = function(value)
    {
        _overlayOpacity = value;
    };

    jQuery.each(['submit','resize','paint','cancel'], function(index, name){
        jQuery.modalDialog[name] = function() { return jQuery.modalDialog.trigger.apply(jQuery.modalDialog, jQuery.merge([name], jQuery.makeArray(arguments))); }
    });


    jQuery.modalDialog.button = function(preset, data) {

        var presetData = _buttonsPresets[preset] ? _buttonsPresets[preset] : _buttonsPresets['default'];
        data = $.isFunction(data) ? {click: data} : (typeof data == 'string' ? {action: data} : jQuery.extend({}, data));

        var button = jQuery("<button type='"+ (data.type ? data.type : presetData.defaultType) + "' />").addClass(presetData.classname);
        button.append(jQuery('<span />').html(data.label != null ? data.label : (data.text != null ? data.text : presetData.defaultLabel)));

        if(data.classname) button.addClass(data.classname);

        if(data.click) button.bind('click', data.click);
        else _bindEventTrigger(button, data.action ? data.action : presetData.defaultAction, 'click');

        return button;
    };


    jQuery.modalDialog.buttonsBlock = function(buttons) {

        var block = jQuery('<div />').addClass('buttons');

        jQuery.each(arguments, function(index, argument) {
            jQuery.each(jQuery.isArray(argument) ? argument : [argument], function (index, data) {
                block.append('&nbsp;&nbsp;&nbsp;');
                block.append(_isNode(data) ? data : jQuery.modalDialog.button.apply(jQuery.modalDialog, jQuery.isPlainObject(data) ? [data.preset ? data.preset : 'default', data] : [data]));
            });
        });

        return block;
    };


    jQuery.modalDialog.alert = function(text, options) {

        var content = jQuery('<div />').
        append(jQuery('<p />').html(text ? text : '').css({'font-weight': 'bold'})).
        append(jQuery.modalDialog.buttonsBlock('default'));

        var optionsIsObject	= jQuery.isPlainObject(options);

        var dialog = content.modalDialog(jQuery.extend({}, {theme: optionsIsObject ? '' : options, closeButton: true, easyClose: true, stack:false, autoOpen: true, title: true}, optionsIsObject ? options : {}));
        dialog.body().addClass('alert-dialog');

        return dialog;

    };



    jQuery.modalDialog.confirm = function(text, callback, options) {

        var content = jQuery('<div />').
        append(jQuery('<p />').html(text ? text : '')).
        append(jQuery.modalDialog.buttonsBlock({preset: 'accept', action: 'accept'}, 'cancel'));

        var dialog = content.
        modalDialog(jQuery.extend({}, {theme: 'question', closeButton: false, easyClose: true, stack:false, title: '&nbsp;'},options)).
        bind('accept', function() { if(callback.apply(this, $.makeArray(arguments)) !== false) this.close(); });

        dialog.body().addClass('confirm-dialog');
        dialog.open();

    };


    jQuery.modalDialog.lock = function(html, callback, options) {

        return jQuery('<div />').
        append(jQuery('<div />').css({'padding': '20px'}).html(html)).
        modalDialog(jQuery.extend({}, {closeButton: false, easyClose: false, stack: false, title: false}, options)).
        bind('load', callback).
        open();
    };



    jQuery.modalDialog.loading = function(html, callback, options) {

        if(html && typeof html == 'function')
        {
            return jQuery.modalDialog.loading(Strings.Get('loading') + '...', html, callback);
        }

        var body = jQuery('<div />').append($('<div />').html(html ? html : Strings.Get('loading') + '...').addClass('text')).css({'padding':'20px 70px', 'font-weight': 'bold'}).addClass('modaldialog-loading');
        return body.modalDialog(jQuery.extend({}, {closeButton: false, easyClose: false, stack: false, title: null}, options)).bind('load',callback).open();
    };


    jQuery.modalDialog.loadingAjax = function(ajaxOptions, html)
    {
        return jQuery.modalDialog.loading(html, function() {$.ajax(ajaxOptions);});
    };

    jQuery.modalDialog.loadingPostAjax = function(url, data, success, error, complete)
    {
        return jQuery.modalDialog.loadingAjax({url: url, type: 'post', data: data, success: success, error: error, complete: complete});
    };

    jQuery.modalDialog.ajax = function(ajaxOptions, dialogOptions) {

        var ajaxOptions = jQuery.extend({}, {type: 'post'}, ajaxOptions);
        var dialogOptions = jQuery.extend({}, {showLoading: true}, dialogOptions);

        var onLoadCallback = dialogOptions['onload'];

        var loadingHtml = dialogOptions['loadingHtml'];
        delete dialogOptions['loadingHtml'];

        var loadHtml = function() {

            var ajaxParams = jQuery.extend({}, ajaxOptions, {

                error: function() {

                    if(ajaxOptions.error) ajaxOptions.error.apply(this, arguments);
                    jQuery.modalDialog.close();
                },

                success: function(data) {

                    var loadDialog = function(html, options) {

                        if(ajaxOptions.success) ajaxOptions.success.apply(this, arguments);

                        var dialog = new $.modalDialog(null, $.extend({}, dialogOptions, options));
                        dialog.content(html);
                        dialog.bind('load', function() {

                            setTimeout(function() { dialog.reposition();}, _ajaxRepositionTimeout);

                            if(onLoadCallback) onLoadCallback.apply(this, arguments);

                        });

                        dialog.open();
                    };

                    if($.isPlainObject(data)) {

                        var cssFiles = data['css_files'];
                        var jsFiles = data['js_files'];

                        var loadCSSFiles = function(){

                            if(loadCSSFiles.fileIndex < cssFiles.length) {

                                var u = cssFiles[loadCSSFiles.fileIndex];

                                var t=[];
                                var n=document.getElementsByTagName('link');
                                var r=document.getElementsByTagName('head')[0];

                                for(var i=0;i<n.length;i++){
                                    var s=n[i];
                                    var o=s.getAttribute('rel');

                                    if(o&&o=='stylesheet'){

                                        t.push(s.getAttribute('href'));
                                    }
                                }


                                if(!(t.indexOf(u['file'])>=0)){

                                    var a=document.createElement('link');
                                    a.setAttribute('type','text/css');
                                    a.setAttribute('rel','stylesheet');
                                    a.setAttribute('href',u['file']);
                                    a.setAttribute('media',u['media']);

                                    $(a).bind('load', function() {
                                        loadCSSFiles.fileIndex++;
                                        loadCSSFiles();
                                    });

                                    r.appendChild(a);

                                    t.push(u['file']);
                                } else {
                                    loadCSSFiles.fileIndex++;
                                    loadCSSFiles();
                                }
                            } else {
                                loadJSFiles();
                            }
                        };

                        loadCSSFiles.fileIndex = 0;


                        var loadJSFiles = function(){

                            if(loadJSFiles.fileIndex < jsFiles.length) {

                                var n=document.getElementsByTagName('script');

                                var i=jsFiles[loadJSFiles.fileIndex];
                                var ii= i.replace(/\?.*/, '');
                                var s=false;

                                for(var o=0;o<n.length;o++){

                                    var u=n[o];

                                    if(u&&u.getAttribute('type')=='text/javascript'&&u.getAttribute('src')&&u.getAttribute('src').replace(/\?.*/, '')==ii){

                                        s=true;
                                        break
                                    }

                                }

                                if(!s){

                                    var u=document.createElement('script');

                                    u.setAttribute('type','text/javascript');
                                    u.setAttribute('src',i);
                                    $(u).bind('load', function() {
                                        loadJSFiles.fileIndex++;
                                        loadJSFiles();
                                    });


                                    document.getElementsByTagName('body')[0].appendChild(u);
//									document.getElementsByTagName('head')[0].appendChild(u);

                                } else {
                                    loadJSFiles.fileIndex++;
                                    loadJSFiles();
                                }
                            } else {

                                loadHTML();
                            }
                        };

                        loadJSFiles.fileIndex = 0;

                        var loadHTML = function() {

                            loadDialog(data['html'], data['options']);
                        };

                        loadCSSFiles();

                    } else {

                        loadDialog(data);
                    }
                }

            });

            jQuery.ajax(ajaxParams);

        };

        if(dialogOptions.showLoading) {

            return jQuery.modalDialog.loading(loadingHtml, function() {

                loadHtml();

            });

        } else {

            loadHtml();

        }

    };


    jQuery.modalDialog.image = function(data, dialogOptions) {

        if(!$.isPlainObject(data)) data = {src: data};

        if(!$.isPlainObject(dialogOptions)) dialogOptions = {title: dialogOptions};
        dialogOptions = $.extend({}, {closeButton: true, easyClose: true, showLoading: true}, dialogOptions);

        if(dialogOptions['showLoading']) {
            jQuery.modalDialog.loading('Cargando...', function () {

                dialogOptions = $.extend({}, dialogOptions, {showLoading: false});
                jQuery.modalDialog.image(data, dialogOptions);

            });

        }
        else
        {

            var dialogBlock = $('<div />').addClass('repaint').css({'visibility': 'hidden', 'position': 'absolute', 'width': 0, 'height': 0}).appendTo(document.body);
            var dialogBlockContent = $('<div />').addClass('image-dialog-content').appendTo(dialogBlock);

            if(data.title) {
                dialogBlockContent.append($('<div />').addClass('image-title').html(data.title));
                //dialogOptions['title'] = data.title;
            }

            var image = $('<img />').addClass('image dialog-image').attr({'alt': data.title ? data.title : (data.description ? data.description: 'Image'),  'title': data.title ? data.title : (data.description ? data.description: 'Image')});

            if(data.css)
            {
                image.css(data.css);
            }

            var imageOptions = {};

            if(data.options)
            {
                imageOptions = $.extend({}, {'fill-window': true, 'height-space': -170}, data.options);
            }

            var updateImageSize = function()
            {
                var avalWidth = $(window).width() - 50;
                var avalHeight = $(window).outerHeight() - imageOptions['height-space'];

                if(imageOptions['fill-window'])
                {

                    image.css('width', '');
                    image.css('height', '');

                    var imageWidth = image.width();
                    var imageHeight = image.height();

                    if(imageWidth > 0 && imageHeight > 0 && avalWidth > 0 && avalHeight > 0)
                    {
                        var imageAspect = imageWidth / imageHeight;

                        var newWidth = avalWidth;
                        var newHeight = newWidth / imageAspect;

                        if(newHeight > avalHeight )
                        {
                            var newHeight = avalHeight - 50;
                            var newWidth = imageAspect * newHeight;
                        }

                         image.width(newWidth);
                        image.height(newHeight);

                        // dialogBlockContent.find('.image-title').width(newWidth);
                        dialogBlock.find('.images-list-buttons-block').css({ 'margin-top': 50});
                        //dialogBlock.find('.images-list-buttons-block .play-pause-button').css({'margin-top': (newHeight/2)-0});
                    }

                    image.css({'visibility': 'visible'});
                    $.modalDialog.resize();

                }
                else
                {
                    image.css('max-width', avalWidth);
                    image.css('max-height', avalHeight);
                }
            };

            var windowResize = function()
            {
                updateImageSize();
            };

            var imageBlock = $('<a />').addClass('dialog-image-container').css({'text-align': 'center'}).attr({'href': 'javascript: void(0)'});

            if(data['click'])
            {
                imageBlock.bind('click', data['click']);
            }

            imageBlock.append(image);

            dialogBlockContent.append(imageBlock);


            $(window).bind('resize', windowResize);

            image.bind('load', function() {

                updateImageSize();

                if(dialogOptions['load'])
                {
                    dialogOptions['load'].call(this);
                }
            });

            image.bind('error', function() {
                if(dialogOptions['onerror'])
                {
                    dialogOptions['onerror'].call(this);
                }
            });

            if(data.description) dialogBlockContent.append($('<div />').addClass('description').html(data.description));

            var buttonsBlock = $('<div />');

            if(data.buttons) {

                buttonsBlock.append(data.buttons);

            } else if(data.downloadUrl) {

                var buttons = $('<div />').addClass('buttons');
                var buttonsRight = $('<div />').css({'float': 'right'}).append($.modalDialog.button('close').addClass('default'));

                var buttonDownload = $.modalDialog.button('download', function() { Navigation.go(data.downloadUrl); });

                var buttonsLeft = $('<div />').addClass('buttons-left').append(buttonDownload);

                buttons.append(buttonsLeft);
                buttons.append(buttonsRight);
                buttons.append($('<div />').css({'clear': 'both'}));
                buttonsBlock.append(buttons);


            } else {

                buttonsBlock.append($.modalDialog.buttonsBlock('close'));

            }


            dialogBlock.append(buttonsBlock);

            image.loadImage(data.src, function() {

                var imageWidth = $(this).width();

                dialogBlock.hide().css({'visibility': 'visible', 'position': '', 'width': '', 'height': ''});
                // dialogBlock.find('.image-title').css({'width': imageWidth});
                // dialogBlock.find('.description').css({'width': imageWidth});
                var dialog = dialogBlock.modalDialog($.extend({}, {classname: 'modaldialog-image', stack: false, 'animation': 0 }, dialogOptions));
                dialog.bind('close', function() {
                    $(window).unbind('resize', windowResize);
                });
                dialog.open();
            });


        }
    };



    jQuery.modalDialog.imagesList = function(dataList, dialogOptions, startIndex) {

        if(dataList.length == 0) return;
        else if(dataList.length == 1) return jQuery.modalDialog.image(dataList[0], dialogOptions);

        if(!$.isPlainObject(dialogOptions)) dialogOptions = {title: dialogOptions};
        dialogOptions = $.extend({}, {closeButton: true, easyClose: true, enablePlay: false, playInterval: 4000}, dialogOptions);

        var selectedIndex = startIndex ? startIndex : 0;
        var onloadWrapper = dialogOptions.onload && typeof dialogOptions.onload == 'function' ? dialogOptions.onload : function() {};
        var onunloadWrapper = dialogOptions.onunload && typeof dialogOptions.onunload == 'function' ? dialogOptions.onunload : function() {};
        var isPlaying = false;
        var playInterval = null;
        var playButtonPlayHtml = '<span class="fa fa-play"></span>&nbsp;Play';
        var playButtonPauseHtml = '<span class="fa fa-pause"></span>&nbsp;Pause';

        for(var i=0; i<dataList.length; i++)
        {
            var dataListItem = dataList[i];

            if(!$.isPlainObject(dataListItem)) dataListItem = {src: dataListItem};

            dataList[i] = dataListItem;
        }

        jQuery.modalDialog.imagesList.dataList = dataList;

        if(!Number.isNumeric(selectedIndex))
        {
            for(var i=0; i<jQuery.modalDialog.imagesList.dataList.length; i++)
            {
                if(jQuery.modalDialog.imagesList.dataList[i]['src'] == selectedIndex)
                {
                    selectedIndex = i;
                    break;
                }
            }
        }

        jQuery.modalDialog.imagesList.selectedIndex = selectedIndex;

        var appendImage = function(index, offset)
        {
            var newIndex = index + offset;

            if(newIndex >= 0 && newIndex < jQuery.modalDialog.imagesList.dataList.length)
            {
                $('<img />').attr({'alt': 'Image', 'src': jQuery.modalDialog.imagesList.dataList[newIndex]['src']}).hide().appendTo(document.body);
            }
        };

        var setPosition = function(index, sign) {

            //if(index == selectedIndex) return;

            if(playInterval)
            {
                clearTimeout(playInterval);
                playInterval = null;
            }

            selectedIndex = index;
            jQuery.modalDialog.imagesList.selectedIndex = selectedIndex;

            jQuery(window).unbind('keydown', keyListener);

            $('.dialog-image-container .dialog-image').css({'visibility': 'hidden'});
            $('.dialog-image-container .dialog-image').attr('src', jQuery.modalDialog.imagesList.dataList[index]['src']);
            appendImage(index,-2);
            appendImage(index,-1);
            appendImage(index, 0);
            appendImage(index, 1);
            appendImage(index, 2);
            appendImage(index, 3);

            var data = jQuery.modalDialog.imagesList.dataList[index];
            var dialogBlockContent = $('.image-dialog-content');

            dialogBlockContent.find('.image-title').remove();

            if(data.title && !ZPHP.isMobile()) {
                dialogBlockContent.prepend($('<div />').addClass('image-title').html(data.title));
            }

            var image = dialogBlockContent.find('.dialog-image');

            if(data.css)
            {
                image.css(data.css);
            }

            if(isPlaying)
            {
                setPlayTimeout();
            }

        }

        jQuery.modalDialog.imagesList.setPosition = setPosition;

        var prevFunction = function() {

            var index = selectedIndex - 1;

            // if(index < 0) return;
            if(index < 0) index = jQuery.modalDialog.imagesList.dataList.length - 1;

            setPosition(index, -1);
        };


        var nextFunction = function() {

            var index = selectedIndex + 1;

            // if(index >= jQuery.modalDialog.imagesList.dataList.length) index = jQuery.modalDialog.imagesList.dataList.length-1;
            if(index >= jQuery.modalDialog.imagesList.dataList.length) index = 0;

            setPosition(index, 1);
        };

        var playPauseFunction = function()
        {
            var button = $('.image-dialog-content .images-list-buttons-block .play-pause-button');

            if(isPlaying)
            {
                button.html(playButtonPlayHtml);

                if(playInterval)
                {
                    clearTimeout(playInterval);
                    playInterval = null;
                }
            }
            else
            {
                button.html(playButtonPauseHtml);
                setPlayTimeout();
            }

            isPlaying = !isPlaying;
        };

        var setPlayTimeout = function()
        {
            playInterval = setTimeout(function() {
                var index = selectedIndex + 1;
                if(index >= jQuery.modalDialog.imagesList.dataList.length) index = 0;
                setPosition(index);
                $('div#modaldialog-container .dialog-image-container .images-list-buttons-block').addClass('visible');
            }, dialogOptions['playInterval']);
        }

        var keyListener = function(evt) {

            if(evt.which == jQuery.KEY_RIGHT || evt.which == jQuery.KEY_SPACE) {

                evt.preventDefault();
                evt.stopPropagation();
                nextFunction();

            } else if(evt.which == jQuery.KEY_LEFT) {
                evt.preventDefault();
                evt.stopPropagation();
                prevFunction();

            } else if(evt.which == jQuery.KEY_HOME) {
                evt.preventDefault();
                evt.stopPropagation();
                setPosition(0);

            } else if(evt.which == jQuery.KEY_END) {
                evt.preventDefault();
                evt.stopPropagation();
                setPosition(jQuery.modalDialog.imagesList.dataList.length-1);

            } else if(evt.which == jQuery.KEY_ENTER) {
                evt.preventDefault();
                evt.stopPropagation();
                playPauseFunction();
            }


        };

        var wheelListener = function (event) {
            if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
                prevFunction();
            }
            else {
                nextFunction();
            }
            event.preventDefault();
        };

        dialogOptions['onload'] = function() {

            var image = this.find('.dialog-image');
            image.bind('click', nextFunction);

            var dialogContent = this.find('.image-dialog-content');
            var dialogImageContainter = this.find('.dialog-image-container');
            var imageWidth = this.find('.dialog-image').width();
            var imageHeight = this.find('.dialog-image').height();

            var listButtonsBlock = $('<div />').addClass('images-list-buttons-block visible').css({'width': '100%', 'margin-top': (imageHeight/2), 'white-space': 'nowrap'}).prependTo(dialogImageContainter);
            var prevButton = $('<a />').addClass('prev-button button').attr({'href': 'javascript: void(0)', 'title': 'Anterior'}).html('&nbsp;').appendTo(listButtonsBlock);
            var nextButton = $('<a />').addClass('next-button button').attr({'href': 'javascript: void(0)', 'title': 'Siguiente'}).html('&nbsp;').appendTo(listButtonsBlock);

            var playContainer = $('<div />').css({'position': 'fixed', 'bottom': '50px', 'left': '0', 'width': '100%', 'height': '0'}).prependTo(listButtonsBlock);
            var playPauseButton = $('<a />').addClass('play-pause-button button').attr({'href': 'javascript: void(0)', 'title': 'Play/Pause'}).css({}).html(playButtonPlayHtml).appendTo(playContainer);
            var downloadButton = $('<a />').addClass('download-button button').attr({'href': 'javascript: void(0)', 'title': 'Download'}).css({'margin-left': '20px'}).html('Download').appendTo(playContainer);

            var positionBlock = $('<div />').addClass('position-block').appendTo(dialogContent).html('Imagen ' + String(selectedIndex+1) + ' / ' + String(jQuery.modalDialog.imagesList.dataList.length));
            // var positionBlock = $('<div />').addClass('position-block').html('Imagen ' + String(selectedIndex+1) + ' / ' + String(jQuery.modalDialog.imagesList.dataList.length));

            prevButton.bind('click', prevFunction);
            nextButton.bind('click', nextFunction);
            playPauseButton.bind('click', playPauseFunction);

            if(dataList.length < 2)
            {
                prevButton.hide();
                nextButton.hide();
                playPauseButton.hide();
            }

            if(!dialogOptions['enablePlay'])
            {
                playPauseButton.hide();
            }

            if(ZPHP.isMobile())
            {
                jQuery(window).bind('swipeleft', nextFunction);
                jQuery(window).bind('swiperight', prevFunction);
                jQuery(window).bind('swipedown', $.modalDialog.closeAll);

                listButtonsBlock.css({'visibility':'visible'});
            }

            jQuery(document).bind('DOMMouseScroll', wheelListener);
            jQuery(document).bind('keydown', keyListener);

            onloadWrapper.apply(this, arguments);

            $('div#modaldialog-container .dialog-image-container').bind('mouseover', function() {
                $('div#modaldialog-container .dialog-image-container .images-list-buttons-block').addClass('visible');
            });

            $('div#modaldialog-container .dialog-image-container').bind('mouseout', function() {
                $('div#modaldialog-container .dialog-image-container .images-list-buttons-block').removeClass('visible');
            });
        };


        dialogOptions['onunload'] = function() {

            if(ZPHP.isMobile())
            {
                jQuery(window).unbind('swipeleft', nextFunction);
                jQuery(window).unbind('swiperight', prevFunction);
                jQuery(window).unbind('swipedown', $.modalDialog.closeAll);
            }

            jQuery(document).unbind('keydown', keyListener);
            jQuery(document).unbind('DOMMouseScroll', wheelListener);
            onunloadWrapper.apply(this, arguments);

        };

        dialogOptions['fill-window'] = true;
        dialogOptions['onerror'] = function()
        {
            nextFunction();
        };

        if(!$('.dialog-image-container .dialog-image').data('modaldialog_imagelist_callbacks'))
        {
            $('.dialog-image-container .dialog-image').bind('error', function(evt) {

                nextFunction();

                if(dialogOptions['errorCallback'])
                {
                    dialogOptions['errorCallback'].call(this, evt);
                }
            });

            $('.dialog-image-container .dialog-image').data('modaldialog_imagelist_callbacks', true);
        }

        jQuery.modalDialog.image({'src': jQuery.modalDialog.imagesList.dataList[selectedIndex]['src'], 'options': {'height-space': 30}}, dialogOptions);

    };


    jQuery.modalDialog.imagesList.selectedIndex = -1;
    jQuery.modalDialog.imagesList.dataList = [];
    jQuery.modalDialog.imagesList.onLoad = null;
    jQuery.modalDialog.imagesList.onError = null;
    jQuery.modalDialog.imagesList.onChangeSign = 0;

    jQuery.modalDialog.prompt = function(label, value, callback, title, dialogOptions, textStyle, textClass, errorStr) {

        var textInput = $("<input type='text' />").val(value ? value : '').addClass('focus prompt-text dialog-prompt-text').css({'width': '270px'}).addClass(textClass).prop({'name': 'value'});

        if(textStyle != null && (typeof textStyle == 'string' || $.isPlainObject(textStyle))) {
            textInput.css(textStyle);
        }

        var errorRow = null;

        if(errorStr != null) {

            errorRow = $('<tr />').append($('<td />').attr({'colspan': '2'}).html(errorStr).addClass('error bold').css({'padding': '0 0 15px 0', 'font-size': '10pt'}));
        }

        $('<form />').append($('<table />').css({'margin': '10px 20px 0', 'text-align': 'left'}).
            append(errorRow).
            append($('<tr />').
            append($('<th />').css({'padding-right': '30px'}).append($('<label />').addClass('area-color').css({'font-size': '10pt'}).html(String(label)).bind('click', function() { textInput.focus(); }))).
            append($('<td />').append(textInput)))

        ).append($.modalDialog.buttonsBlock('submit', 'cancel')).bind('submit', function() {

            var value = String(textInput.val()).trim();

            if(callback) {
                callback.call(this, value)
            }

            return false;

        }).modalDialog($.extend({}, {animation: 10, title: title, closeButton: true}, dialogOptions)).bind('load', function() { textInput.focus().select();  }).open();

    };

    jQuery.modalDialog.promptNumber = function(label, value, callback, title, dialogOptions, textStyle, textClass, errorStr) {

        dialogOptions = $.extend({}, dialogOptions);

        var textInput = $("<input type='number' />").val(value ? value : '').addClass('focus prompt-text dialog-prompt-text').css({'width': '270px'}).addClass(textClass).prop({'name': 'value'});

        if(dialogOptions['min'])
        {
            textInput.attr('min', dialogOptions['min']);
        }

        if(dialogOptions['max'])
        {
            textInput.attr('max', dialogOptions['max']);
        }


        if(textStyle != null && (typeof textStyle == 'string' || $.isPlainObject(textStyle))) {
            textInput.css(textStyle);
        }

        if(!title) title = label;

        var errorRow = null;

        if(errorStr != null) {

            errorRow = $('<tr />').append($('<td />').attr({'colspan': '2'}).html(errorStr).addClass('error bold').css({'padding': '0 0 15px 0', 'font-size': '10pt'}));
        }

        $('<form />').append($('<table />').css({'margin': '10px 20px 0', 'text-align': 'left'}).
            append(errorRow).
            append($('<tr />').
            append($('<th />').css({'padding-right': '30px'}).append($('<label />').addClass('area-color').css({'font-size': '10pt'}).html(String(label)).bind('click', function() { textInput.focus(); }))).
            append($('<td />').append(textInput)))

        ).append($.modalDialog.buttonsBlock('submit', 'cancel')).bind('submit', function() {

            var value = String(textInput.val()).trim();

            if(callback) {
                callback.call(this, value)
            }

            return false;

        }).modalDialog($.extend({}, {animation: 10, title: title, closeButton: true}, dialogOptions)).bind('load', function() { textInput.focus().select();  }).open();

    };

    jQuery.modalDialog.promptTextarea = function(label, value, callback, title, dialogOptions, textStyle, textClass, errorStr) {

        var textInput = $("<textarea />").val(value ? value : '').addClass('focus prompt-text dialog-prompt-text').css({'width': '270px'}).addClass(textClass).prop({'name': 'value'});

        if(textStyle != null && (typeof textStyle == 'string' || $.isPlainObject(textStyle))) {
            textInput.css(textStyle);
        }

        var errorRow = null;

        if(errorStr != null) {

            errorRow = $('<tr />').append($('<td />').attr({'colspan': '2'}).html(errorStr).addClass('error bold').css({'padding': '0 0 15px 0', 'font-size': '10pt'}));
        }

        $('<form />').append($('<table />').css({'margin': '10px 20px 0', 'text-align': 'left'}).
            append(errorRow).
            append($('<tr />').
            append($('<th />').css({'padding-right': '30px'}).append($('<label />').addClass('area-color').css({'font-size': '10pt'}).html(String(label)).bind('click', function() { textInput.focus(); }))).
            append($('<td />').append(textInput)))

        ).append($.modalDialog.buttonsBlock('submit', 'cancel')).bind('submit', function() {

            var value = String(textInput.val()).trim();

            if(callback) {
                callback.call(this, value)
            }

            return false;

        }).modalDialog($.extend({}, {animation: 10, title: title, closeButton: true}, dialogOptions)).bind('load', function() { textInput.focus().select();  }).open();

    };



    jQuery.modalDialog.calendar = function(initialDate, callback, options) {


        var options = $.extend({}, {title: 'Seleccionar fecha', closeButton: true, easyClose: true, dateFormat: '%d / %m / %Y', parseFormat: '%Y-%m-%d'}, options);

        var dateFormat = options.dateFormat;

        if(!initialDate) initialDate = new Date();
        else initialDate = Date.parse(initialDate, options.parseFormat);

        var actualDate = new Date();

        var actualDateMonth = actualDate.getMonth();
        var actualDateYear = actualDate.getFullYear();
        var actualDateDay = actualDate.getDate();

        /*------------------------------------------------------------------------------------------------------*/

        var block = $('<div />').addClass('calendar-dialog');

        var fechaBlock = $('<div />').addClass('fecha-block').appendTo(block).html("<span class='title'>Fecha seleccionada: </span>").append($('<span />').addClass('fecha-text')).append($('<a />').addClass('fecha-text-button').attr({'href': 'javascript: void(0)', 'title': 'Ir a la fecha'}).html('[mostrar]').bind('click', function() { updateMonth(selectedDate); }));

        var calendarTable = $('<table />').addClass('calendar-table').appendTo(block);

        //var fechaActualBlock = $('<div />').addClass('fecha-actual-block').appendTo(block).html("<span class='title'>Hoy: </span>").append($('<span />').addClass('fecha-text').html(actualDate.format(dateFormat))).append($('<a />').addClass('fecha-text-button').attr({'href': 'javascript: void(0)', 'title': 'Ir a la fecha'}).html('[mostrar]').bind('click', function() { updateMonth(actualDate); }));

        $('<tr />').addClass('title-row').appendTo(calendarTable).


        append($('<td />').attr({colspan: 7}).css({'padding': '0'}).append($('<table />').append('<tr />').

            append(
            $('<th />').addClass('button-cell left').
            append($('<a />').addClass('month-button').attr({'href': 'javascript: void(0)', 'title': 'Mes Anterior'}).html('&lt;').bind('click', function() { moveMonth(-1, 0); })).
            append($('<a />').addClass('year-button').attr({'href': 'javascript: void(0)', 'title': 'A�o Anterior'}).html('&lt;&lt;').bind('click', function() { moveMonth(0, -1); }))).

            append("<th class='month-name-cell'></th>").



            append(
            $('<th />').addClass('button-cell right').
            append($('<a />').addClass('year-button').attr({'href': 'javascript: void(0)', 'title': 'A�o Siguiente'}).html('&gt;&gt;').bind('click', function() { moveMonth(0, 1); })).
            append($('<a />').addClass('month-button').attr({'href': 'javascript: void(0)', 'title': 'Mes Siguiente'}).html('&gt;').bind('click', function() { moveMonth(1, 0); })))

        ));

        $('<tr />').addClass('days-names-row').appendTo(calendarTable).
        append("<th>L</th>").
        append("<th>M</th>").
        append("<th>M</th>").
        append("<th>J</th>").
        append("<th>V</th>").
        append("<th>S</th>").
        append("<th>D</th>");


        for(var i=0; i<6; i++)
            $('<tr />').addClass('days-row').appendTo(calendarTable).html(
                "<td class='day-cell' style='border-left: 0'><a href='javascript: void(0)'>&nbsp;</a></td>"+
                "<td class='day-cell'><a href='javascript: void(0)'>&nbsp;</a></td>"+
                "<td class='day-cell'><a href='javascript: void(0)'>&nbsp;</a></td>"+
                "<td class='day-cell'><a href='javascript: void(0)'>&nbsp;</a></td>"+
                "<td class='day-cell'><a href='javascript: void(0)'>&nbsp;</a></td>"+
                "<td class='day-cell'><a href='javascript: void(0)'>&nbsp;</a></td>"+
                "<td class='day-cell' style='border-right: 0'><a href='javascript: void(0)'>&nbsp;</a></td>");

        var dateClickListener = function() {

            var $this = $(this);
            setSelectedDate(new Date(actualYear, actualMonth, $this.data('button-day'), 0, 0, 0));

            calendarTable.find('tr.days-row').children('td.day-cell').removeClass('day-cell-selected');

            $this.parents('td').addClass('day-cell-selected');

        };



        var actualMonth, actualYear, selectedDate, selectedDateDay, selectedDateMonth, selectedDateYear;



        var moveMonth = function(monthDiff, yearDiff) {

            var newMonth = actualMonth + monthDiff;

            if(newMonth < 0) {

                newMonth = 11;
                yearDiff--;

            } else if(newMonth > 11) {

                newMonth = 0;
                yearDiff++;
            }

            updateMonth(newMonth, actualYear + yearDiff);

        };


        var updateMonth = function(month, year) {

            if(year == null) return updateMonth(month.getMonth(), month.getFullYear());

            if(month == actualMonth && year == actualYear) return;

            var firstDayDate = new Date(year, month, 1, 0, 0, 0);
            var daysOffset = parseInt(firstDayDate.format("%u"));

            calendarTable.find('tr.title-row th.month-name-cell').html(firstDayDate.format("%B &nbsp; %Y"));

            var cells = calendarTable.find('tr.days-row').children('td.day-cell');

            cells.find('a').html('').unbind('click', dateClickListener);
            cells.removeClass('day-cell-enabled day-cell-disabled day-cell-selected day-cell-actual');

            var beforeMonth = month-1;
            var beforeYear = year;

            if(beforeMonth < 0) {

                beforeMonth = 11;
                beforeYear--;

            }

            var beforeMonthDays = Date.getMonthDays(beforeMonth, beforeYear);
            var monthDays = Date.getMonthDays(month, year);

            var hasSelectedDate = month == selectedDateMonth && year == selectedDateYear;
            var hasActualDate = month == actualDateMonth && year == actualDateYear;

            for(var i=daysOffset; i>0; i--) {

                var cell = cells.eq(i-1).addClass('day-cell-disabled');
                cell.find('a').html(String(beforeMonthDays));

                beforeMonthDays--;

            }


            for(var i=0; i<monthDays; i++) {

                var day = i+1;
                var cell = cells.eq(i+daysOffset).addClass('day-cell-enabled');
                cell.find('a').html(String(day)).data('button-day', day).bind('click',dateClickListener);

                if(hasSelectedDate && i+1 == selectedDateDay) cell.addClass('day-cell-selected');
                if(hasActualDate && i+1 == actualDateDay) cell.addClass('day-cell-actual');
            }


            for(var i=monthDays+daysOffset; i<cells.length; i++) {

                var cell = cells.eq(i).addClass('day-cell-disabled');
                cell.find('a').html(String(i-(monthDays+daysOffset)+1));

            }

            actualMonth = month;
            actualYear = year;

        };


        var setSelectedDate = function(date) {

            selectedDate = date;
            selectedDateDay = date.getDate();
            selectedDateMonth = date.getMonth();
            selectedDateYear = date.getFullYear();
            fechaBlock.find('.fecha-text').html(date.format(dateFormat));

        };


        setSelectedDate(initialDate);
        updateMonth(initialDate);



        block.append($.modalDialog.buttonsBlock('accept', 'cancel')).modalDialog(options).bind('accept', function() { if(callback) callback.call(this, selectedDate); this.close(); }).open();

    };


    jQuery.modalDialog.imagesSearch = function(selectCallback, initialSearch, options)
    {
        options = $.extend({}, jQuery.modalDialog.imagesSearch.defaultOptions, options);

        jQuery.modalDialog.imagesSearch.options = options;

        if(!jQuery.modalDialog.imagesSearch.created) {

            jQuery.modalDialog.imagesSearch.created = true;

            var dialogHtml = ' <div id="isf-producto-search-image-dialog" style="display: none; width: 900px"><form id="isf-producto-image-form" method="post" action="javascript:void(0)"><div class="fieldset">    <div class="error hidden"></div>    <table>     <tr id="isf-producto-imagen-actual-row">      <td class="col-label">       <label>Buscar:</label>      </td>      <td>       <input type="text" id="isf-image-search" name="search" style="width: 300px" />      </td>      <td>       <button type="submit" class="btn btn-success"><span>Buscar</span></button>      </td>     </tr>    </table>   </div>   </form>   <br />    <div id="isf-images-result-count">&nbsp;</div>  <div id="isf-images-result" style="border-top: solid 1px #CCC; margin-top: 30px; padding: 20px; width: 700px; max-height: 500px; overflow: auto; border: solid 1px #CCC;"></div>   <div class="buttons"><button type="button" class="btn btn-default" onclick="$.modalDialog.close()"><span>Cerrar</span></button></div></div>';
            var dialogHtml = ' <div id="isf-producto-search-image-dialog" style="width: 900px"><h4 style="margin: 0 0 20px; text-decoration: underline;">Buscar Imágenes</h4><form id="isf-producto-image-form" method="post" action="javascript:void(0)"><div class="fieldset">    <div class="error hidden"></div>    <table>     <tr id="isf-producto-imagen-actual-row">      <td class="col-label" style="padding: 2px 10px 0 0;">       <label>Buscar:</label>      </td>      <td style="padding: 0 30px 0 0;">       <input type="text" id="isf-image-search" name="search" style="width: 300px" class="first-focus" />      </td>      <td>       <button type="submit" class="btn btn-success"><span>Buscar</span></button>      </td>     </tr>    </table>   </div>   </form>   <br />    <div id="isf-images-result-count">&nbsp;</div>  <div id="isf-images-result" style="border-top: solid 1px #CCC; margin-top: 30px; padding: 20px; max-height: 450px; overflow-y: auto; overflow-x: hidden;  border: solid 1px #CCC;"></div>   <div class="buttons"><button type="button" class="btn btn-default" onclick="$.modalDialog.close()"><span>Cerrar</span></button></div></div>';
            var dialogHtml = ' <div id="isf-producto-search-image-dialog" style="width: 600px"><h4 style="margin: 0 0 20px; text-decoration: underline;">Buscar Imágenes</h4><form id="isf-producto-image-form" method="post" action="javascript:void(0)"><div class="fieldset">    <div class="error hidden"></div>    <table>     <tr id="isf-producto-imagen-actual-row">      <td style="padding: 0 20px 0 0;">       <input type="text" id="isf-image-search" name="search" placeholder="Buscar" style="width: 300px" class="first-focus" />      </td>      <td>       <button type="submit" class="btn btn-default search-button" style="font-weight: normal; padding: 4px 20px; background: #333 none repeat scroll 0 0;border: 1px solid #999;color: #fff;padding: 4px 20px;box-shadow: 0 1px 0 rgba(255, 255, 255, 0.15) inset, 0 1px 1px rgba(0, 0, 0, 0.075)"><span class="fa fa-search"></span>&nbsp;&nbsp;<span>Buscar</span></button>      </td>     </tr>    </table>   </div>   </form>   <br />    <div id="isf-images-result-count">&nbsp;</div>  <div id="isf-images-result" style="border-top: solid 1px #CCC; margin-top: 30px; padding: 20px; max-height: 350px; overflow-y: auto; overflow-x: hidden; border: solid 1px #CCC;"></div>   <div class="buttons"><button type="button" class="btn btn-default" style="padding: 5px 20px; font-weight: bold;" onclick="$.modalDialog.close()"><span>Cerrar</span></button></div></div>';
            var dialogHtml = ' <div id="isf-producto-search-image-dialog" style="width: 1300px"><h4 style="margin: 0 0 20px; text-decoration: underline;">Buscar Imágenes</h4><form id="isf-producto-image-form" method="post" action="javascript:void(0)"><div class="fieldset">    <div class="error hidden"></div>    <table class="fill-width">     <tr id="isf-producto-imagen-actual-row">      <td style="padding: 0 20px 0 0; width:340px;">       <input type="text" id="isf-image-search" name="search" placeholder="Buscar" style="width: 300px" class="first-focus" />      </td>      <td>       <button type="submit" class="btn btn-success" style="padding: 4px 15px;"><span class="fa fa-search"></span>&nbsp;&nbsp;<span>Buscar</span></button>     <div class="pull-right" id="isf-images-result-count">&nbsp;</div> </td>     </tr>    </table>   </div>   </form>   <div id="isf-images-result" style="border-top: solid 1px #CCC; margin-top: 30px; padding: 20px; max-height: '+String($(window).height()-300)+'px; overflow-y: auto; overflow-x: hidden; border: solid 1px #CCC;"></div>   <div class="buttons"><button type="button" class="btn btn-default" style="padding: 5px 20px; font-weight: bold;" onclick="$.modalDialog.close()"><span>Cerrar</span></button></div></div>';
            $('body').prepend($('<div />').html(dialogHtml));

            $('#isf-image-search').bind('keydown keyup keypress', function(evt) {
                evt.stopPropagation();
                // return false;
            });

            var ProductoSearchImageDialog = new Object();

            ProductoSearchImageDialog.search = initialSearch;
            ProductoSearchImageDialog.loading = false;

            ProductoSearchImageDialog.dialog = $('#isf-producto-search-image-dialog').modalDialog($.extend({}, options, {
                // 'title': 'Buscar Imagen',
                'top': 0.1,
                'width': $(document).width()
            }));

            ProductoSearchImageDialog.dialog.body().find('#isf-images-result').css({'background': '#EAEAEA'});

            ProductoSearchImageDialog.dialog.open();

            ProductoSearchImageDialog.dialog.emptyImagesBlock = function () {

                var search = ProductoSearchImageDialog.dialog.body().find('#isf-image-search').val().trim();

                if(search.length < options['minLength'])
                {
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result').hide();
                }
                else
                {
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result').show().html("<div class='empty' style='padding-left: 0px'><br /><div class='' style='display: inline-block;'><span class='fa fa-spinner fa-spin fa-fw fa-1x'></span></span></div>&nbsp;&nbsp;Cargando<br /><br /></div>");
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result-count').html("&nbsp;");
                }

            };

            ProductoSearchImageDialog.dialog.updateTotal = function () {
                var countImages = ProductoSearchImageDialog.dialog.body().find('#isf-images-result a').length;

                if (countImages > 0) {
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result-count').html("Se encontraron " + String(countImages) + " imágenes");
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result').show();
                }
                else {
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result-count').html("No se encontraron imágenes");
                    ProductoSearchImageDialog.dialog.body().find('#isf-images-result').hide();
                }

            };

            ProductoSearchImageDialog.dialog.bind('open', function () {
                ProductoSearchImageDialog.dialog.emptyImagesBlock();
                ProductoSearchImageDialog.dialog.body().find('#isf-image-search').val(ProductoSearchImageDialog.search);
                ProductoSearchImageDialog.dialog.body().find('form').submit();
                ProductoSearchImageDialog.dialog.search();
            });

            ProductoSearchImageDialog.dialog.search = function () {

                var search = ProductoSearchImageDialog.dialog.body().find('#isf-image-search').val().trim();
                ProductoSearchImageDialog.dialog.body().find('#isf-image-search').attr('disabled', true);
                ProductoSearchImageDialog.dialog.body().find('.search-button').attr('disabled', true);

                if(search.length < options['minLength'])
                {
                    ProductoSearchImageDialog.dialog.emptyImagesBlock();
                    ProductoSearchImageDialog.dialog.body().find('#isf-image-search').attr('disabled', false);
                    ProductoSearchImageDialog.dialog.body().find('.search-button').attr('disabled', false);
                }
                else if (!ProductoSearchImageDialog.loading) {

                    ProductoSearchImageDialog.loading = true;
                    ProductoSearchImageDialog.dialog.emptyImagesBlock();

                    ProductoSearchImageDialog.dialog.body().find('#isf-image-search').attr('disabled', true);
                    ProductoSearchImageDialog.dialog.body().find('.search-button').attr('disabled', true);

                    $.ajax({
                        'url': '/images/search',
                        'type': 'post',
                        'data': {'pages': options['pages'], 'search': search ? search : ''},
                        'success': function (data) {

                            ProductoSearchImageDialog.loading = false;

                            var block = ProductoSearchImageDialog.dialog.body().find('#isf-images-result');

                            block.masonryUpdate = function()
                            {
                                block.find('.container').masonry({
                                    gutter: 20,
                                    itemSelector: '.find-link-image'
                                });
                            };

                            if (data && data['thumbs'] && data['thumbs'].length > 0) {

                                // block.empty();

                                data['thumbs'] = data['thumbs'].slice(0, data['thumbs'].length);

                                var countUrls = data['thumbs'].length;
                                var loadedUrls = 0;
                                var errorUrls = 0;

                                function updateImagesCount()
                                {
                                    if(loadedUrls+errorUrls >= countUrls || loadedUrls+errorUrls > options['limit'] || loadedUrls+errorUrls > 10)
                                    {

                                        ProductoSearchImageDialog.dialog.updateTotal();
                                        block.find('.empty').remove();
                                        blockContainer.css({'visibility': 'visible', 'position': 'relative', 'width': '1300', 'height': ''});
                                        block.find('.container a img').css({'visibility': 'visible'});
                                        block.find('.container a').css({'visibility': 'visible'});
                                        // block.find('a.find-link-image').css({'display': 'inline-block'});
                                        block.masonryUpdate();

                                        ProductoSearchImageDialog.dialog.body().find('#isf-image-search').attr('disabled', false);
                                        ProductoSearchImageDialog.dialog.body().find('.search-button').attr('disabled', false);
                                    }

                                }

                                var blockContainer = $('<div />').
                                    appendTo(block).
                                    addClass('container').
                                    css({'visibility': 'hidden', 'position': 'absolute', 'width': '0', 'height': '0'});

                                $.each(data['thumbs'], function (index, item) {

                                    if(loadedUrls+errorUrls > options['limit'])
                                    {
                                        return false;
                                    }

                                    if(jQuery.modalDialog.imagesSearch.options['avoidUrls'].indexOf(item) !== -1)
                                    {
                                        return;
                                    }

                                    var link = $('<a />').addClass('find-link-image').css({'margin': '0 0px 30px 0', 'display': 'inline-block', 'vertical-align': 'top'});

                                    var image = $('<img />');
                                    image.attr({'src': item});
                                    image.attr({'data-url': data['urls'][index]});
                                    image.appendTo(link);
                                    image.bind('load', function () {

                                        var node = $(this).data('loaded', true);
                                        var link = node.getParent('a').addClass('load');
                                        var imageWidth = node.width();
                                        var imageHeight = node.height();

                                        if (imageWidth < 200 || imageHeight < 120) {
                                            link.remove();
                                            errorUrls++;
                                            updateImagesCount();
                                        }
                                        else
                                        {
                                            link.addClass('loaded');
                                            loadedUrls++;
                                            updateImagesCount();
                                        }


                                        // ProductoSearchImageDialog.dialog.updateTotal();

                                         block.masonryUpdate();
                                    });

                                    image.bind('error', function () {

                                        var node = $(this).data('loaded', true);
                                        var link = node.getParent('a').addClass('load');

                                        link.remove();
                                        errorUrls++;
                                        updateImagesCount();

                                        block.masonryUpdate();
                                    });

                                    // setTimeout(function() {
                                    //
                                    //     if(!image.data('loaded'))
                                    //     {
                                    //         image.getParent('a').remove();
                                    //         block.masonryUpdate();
                                    //     }
                                    //
                                    // }, 10000);

                                    link.appendTo(blockContainer);

                                    link.bind('click', function (evt) {

                                        // var imgSrc = $(this).find('img').attr('src');
                                        var imgSrc = $(this).find('img').attr('data-url');

                                        if (evt.ctrlKey) {
                                            Navigation.openWindow(imgSrc);
                                        }
                                        else {

                                            if(jQuery.modalDialog.imagesSearch.options['showThumb']) {

                                                $.modalDialog.image({

                                                    'src': imgSrc,
                                                    'css': {
                                                        'max-width': '600px',
                                                        'max-height': $(window).height() - 200
                                                    },
                                                    'click': function () {
                                                        selectCallback.call(image.get(0), imgSrc);
                                                    }

                                                });

                                            }
                                            else
                                            {
                                                selectCallback.call(image.get(0), imgSrc);
                                            }


                                        }
                                    });

                                });
                            }
                            else {
                                block.html("<br /><br />No existen imagenes<br /><br />");
                                ProductoSearchImageDialog.dialog.body().find('#isf-image-search').attr('disabled', false);
                                ProductoSearchImageDialog.dialog.body().find('.search-button').attr('disabled', false);
                            }

                            block.masonryUpdate();
                        }
                    });
                }
            };

            ProductoSearchImageDialog.dialog.body().find('form').bind('submit', function () {
                ProductoSearchImageDialog.dialog.search();
            });

            jQuery.modalDialog.imagesSearch.dialog = ProductoSearchImageDialog.dialog;

        }

        jQuery.modalDialog.imagesSearch.dialog.open();
    };

    jQuery.modalDialog.imagesSearch.created = false;

    jQuery.modalDialog.imagesSearch.options = {};

    jQuery.modalDialog.imagesSearch.dialog = null;

    jQuery.modalDialog.imagesSearch.defaultOptions = {
        'minLength': 2,
        'showThumb': false,
        'avoidUrls': [],
        'pages': 2,
        'limit': 100
    };

    /* Funcion de jQuery para crear un modaldialog ------------------------------ */

    jQuery.fn.modalDialog = function(options) {

        if(this.data('__modalDialog__')) {
            var modaldialog = this.data('__modalDialog__');
            modaldialog.attr(options);
            return modaldialog;
        } else {
            var modaldialog = jQuery.modalDialog.apply(this, jQuery.merge([this], arguments));
            this.data('__modalDialog__', modaldialog);
            return modaldialog;
        }
    };


})(jQuery);
