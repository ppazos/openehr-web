/*!
 * jQuery Modal Plugin
 * version: 1.0.0 (22-JAN-2012)
 * @requires jQuery v1.6.2 or later
 *
 * @author Pablo Pazos Gutierrez <pablo.swp@gmail.com>
 *
 * Licensed under Apache 2.0 license:
 * http://www.apache.org/licenses/LICENSE-2.0.html
 */
;(function($) {

  var underlay = $('<div id="modal_underlay"><img src="../../apps/cms2/images/loader2.gif" /></div>');
  
  var viewport = function() {
  
    var e = window, a = 'inner';
    if ( !( 'innerWidth' in window ) )
    {
      a = 'client';
      e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] }
  }

  var methods = {
  
    /* Se llama cuando se aplica el plugin sobre un elemento */
    init : function(options) {
    
      this.parent().append(underlay);
      
      // ===================================================
      // Centrar verticalmente el loading
      // {height:xx, width:yy}
      var visible_area = viewport();
      var loading = $('img', underlay);
      loading.css({'position':'absolute', 'top': (visible_area.height/2 - loading.width()/2)+'px' });
      //console.log( 'top: ' + (visible_area.height/2 - loading.height()/2)+'px' );
      // ===================================================
      
      
      // http://code.google.com/p/yupp-cms/issues/detail?id=69
//      this.append('<div id="modal_feedback_container"><span id="modal_feedback" /></div>');
      this.parent().append('<div id="modal_feedback_container"><span id="modal_feedback" /></div>'); // para que se muestre fuera de la modal el feedback
      
      
      this.append('<div id="modal_close_container"><div id="modal_close"></div></div>'); // Le pone el estilo por fuera, mostrando una imagen con la X
      
      var iframe = $('<iframe src="" name="modal_frame"></iframe>');
      this.append(iframe);
  
  
      // Resetea el tamanio de la modal al igual que se hace en el hide,
      // es para que si hace clic en un boton que abre la modal y hace
      // clic en otro sin hacer hide, que la neuva modal no quede con el tamanio de la anterior.
      // TODO: Cuando se implemente bien la modal underlay no se podra abrir otra modal sin cerrar primero la activa, ahi esto no sera necesario.
      iframe.css({'width':'0px', 'height':'0px'});
      
      
      // ==========================================================================
      // Para centrar pantalla modal cuando el contenido le cambia de tamanio
      //  1. Observar cuando cambia de tamanio
      //  2. Centrarla
      this.resize(function(evt){ // http://benalman.com/projects/jquery-resize-plugin
          
        $(this).center(); // http://andreaslagerkvist.com/jquery/center/
      });
      
      close_btn = $('#modal_close', this);
      
      // Si pongo this dentro del metodo agarra al propio boton de close.
      var $this = this;
      close_btn.click( function(evt) {

        $this.modal('hide');
      });
      
      return this;
    },
  
    /* Muestra feedback en la modal, antes era showModalFeedback */
    feedback : function(msg) {
    
      console.log('feedback');
    
      // http://code.google.com/p/yupp-cms/issues/detail?id=69
      var fb = $('#modal_feedback', this.parent());
      

      fb.css({'display': 'none', 'opacity':'1.0'}); // para que haga fade, deberia venir con opacity 0      
      fb.text(msg);

      
      // Plugin UI effect fade
      // http://api.jquery.com/fadeIn/
      fb.fadeIn('slow');
         
      // Luego de 6 segundos se va el feedback
      setTimeout( function() {
         
        fb.fadeOut('slow', function() {
            
          fb.text(''); // Cuando termina la animacion le saca el texto
          fb.css({'display': 'none', 'opacity': '0.0'});
        });
            
      }, 6000); // TODO: que el tiempo sea configurable
	  
	  console.log('/feedback');
    },
    
    
    /* Notificacion a la modal de que hubo un cambio en el contenido,
     * ajusta dimensiones y centrado.
     * Antes era la funcion modalReady
     */
    update : function() {
    
      //console.log('modal.update');
    
      iframe = $('iframe', this);

      // Reset, sin esto, si muestro una modal y luego otra,
      // la segunda queda con el tamanio de la primera.         
      //if ($('#modal iframe').css('width') != '0px') alert('distinto de 0');
      iframe.css({'width': '0px', 'height': '0px'});
    
      // Contenido del iframe para sacar el tamanio de resize
      //doc = iframe.contents();
      
      // http://www.bennadel.com/blog/1592-Getting-IFRAME-Window-And-Then-Document-References-With-contentWindow.htm
      //doc = window.frames['modal_frame'].document;
    
      // Use the contentWindow property of the iFrame.
      //objDoc = jFrame[ 0 ].contentWindow.document;
      doc = iframe[0].contentWindow.document;
      
    
      // Para arreglar los distintos W y H en distintos browsers (me quedo con el mayor w y h)
      //var w = $(doc).width();
      
      //console.log($(doc)); // Document
      //console.log(doc.clientWidth); // undefined
      //console.log(doc.body.clientWidth); // 0
      //console.log('w: ' + w);
      //console.log('doc.scrollWidth: ' + doc.scrollWidth);
      //console.log($(window).width());
      
      // No actualiza bien si el w y h del iframe ya estaban seteados
      //if (w < doc.scrollWidth) w = doc.scrollWidth + 15; // el + 15 es porque en CHROME se come el padding de la derecha.
      //var h = $(doc).height();
      
      // ==========================================================
      // Usar las dimensiones del body es lo mas acertado
      //var w = $(doc.body).width() + 30; // + 30 para considerar los paddings
      //var h = $(doc.body).height() + 30;
      
      var w = $(doc.body).width();
      var h = $(doc.body).height();
      
      //console.log($(doc.body).width());
      //console.log(w + ' x ' + h);
      
      // Seteo tamanio y visibilidad de la vista modal
      iframe.css({
        'width': w,
        'height': h 
      });
         
      // debe venir con opacity 0
      // display none para que haga fade
      this.css({'display': 'none', 'opacity': '1.0'});
         
      // Posiciona en el medio antes de empezar el fade
      this.center(); // http://andreaslagerkvist.com/jquery/center/
         
      // Plugin UI effect fade
      // http://api.jquery.com/fadeIn/
      this.fadeIn('slow');
    },
    
    
    /* 
     * Cierra la modal.
     */
    hide : function() {
    
      //console.log('modal.hide');
    
      iframe = $('iframe', this);

      // http://code.google.com/p/yupp-cms/issues/detail?id=69
      var fb = $('#modal_feedback', this.parent());
      
      
      // oculta underlay
      $('#modal_underlay', $(this).parent()).fadeOut('fast', function() {
        $('#modal_underlay', $(this).parent()).css('display', 'none');
      });
      
      this.fadeOut('slow', function() {
        
        // Tengo que hacer $ porque en this llega el elemento pelado que no tiene css()
        $(this).css({'opacity':'0.0', 'display':'none'});
        
        fb.text(''); // Saca el texto del feedback
        fb.css({'display': 'none', 'opacity': '0.0'}); // Ahora el feedback esta afuera de la modal asi que tengo que esconderlo
        
        iframe.attr('src', ''); // Descarga pagina cargada en el iframe
        iframe.css({'width':'0px', 'height':'0px'});
      });
    },
    
    
    /* Carga contenido en la modal */
    load : function(url) {
    
      //console.log('modal load url: ' + url);
    
      // Oculto mientras cargo
      this.css({'display': 'block', 'opacity': '0.0'});
    
      iframe = $('iframe', this);
      iframe.attr('src', url);
      
      
      // muestra underlay
      $('#modal_underlay', this.parent()).css({'display': 'block'});
      //$('#modal_underlay', this.parent()).fadeIn('slow'); // no hace el fade! ya al hacer display block se muestra instantaneamente...
    },
    
    
    /* Registra handler para ser llamado cuando el contenido del iframe
     * de la modal se haya cargado. Handler es una funcion.
     */
    onload : function(handler) {
    
      //console.log('modal.onload');
      
      iframe = $('iframe', this);
      
      /*
        Funciona pero: a veces se quiere actualizar el tamanio de la ventana
        modal luego de que se cargo, ahi deberia hacer algun metodo update
        en la modal, y cuando cargo el contenido en el iframe, el documento
        del iframe deberia pedir la modal y notificarla del update.
        (esto es mas o menos lo que se hace ahora, pero creo que podria hacerse mas prolijo).
       */
        
      // .onload es un namespace que se usa para unbind http://docs.jquery.com/Plugins/Authoring#Events
      iframe.bind('load.onload', handler);
    },
    
    
    /* Hace unbind del handler registrado para el load */
    reset : function() {
    
      //console.log('modal.reset');
    
      iframe = $('iframe', this);
      iframe.unbind('.onload'); // Unbind usando el namespace
    }
  };


  /**
   * msg String mensaje a mostrar
   * options Array argumentos como hideTime
   */
  $.fn.modal = function(method) {
  
    // Create some defaults, extending them with any options that were provided
    /*
    var settings = $.extend( {
      'hideTime'               : 6000,     // 6 segundos por defecto
      'background-color'       : '#ffff00' // color de fondo por defecto
    }, options);
    */
    
    
    // Llama al metodo correcto
    if ( methods[method] )
    {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    }
    else if ( typeof method === 'object' || !method )
    {
      return methods.init.apply( this, arguments );
    }
    else
    {
      $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
    }
    
    return this; // chaining
  };
  
})(jQuery);