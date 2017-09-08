/*!
 * jQuery Feedback Plugin
 * version: 1.0.0 (04-JAN-2012)
 * @requires jQuery v1.6.2 or later
 *
 * @author Pablo Pazos Gutierrez <pablo.swp@gmail.com>
 *
 * Licensed under Apache 2.0 license:
 * http://www.apache.org/licenses/LICENSE-2.0.html
 */
;(function($) {

  /**
   * msg String mensaje a mostrar
   * options Array argumentos como hideTime
   */
  $.fn.feedback = function(msg, options) {
  
    // Create some defaults, extending them with any options that were provided
    var settings = $.extend( {
      'hideTime'               : 6000,     // 6 segundos por defecto
      'background-color'       : '#ffff00' // color de fondo por defecto
    }, options);
    
    
    // estilo
    this.css({'opacity':'1.0'}); // Para que se muestre
    this.css('background-color', settings['background-color']);
  
    // para mostrar siempre en el mismo lugar aunque se haga scroll
    this.css('top', ($(window).scrollTop()+10) + 'px');
    //console.log( $(window).scrollTop() );
  
  
    // Muestra mensaje
    this.text(msg);
    
    // show
    // Plugin UI effect fade: http://api.jquery.com/fadeIn/
    this.fadeIn('slow');
    
    // FIXME: si se hace feedback de nuevo antes de terminar de mostrar el mensaje anterior, no deberia ocurrir el timeout anterior.
    // hide
    // Luego de 6 segundos se va el feedback
    setTimeout( function(fb) {
      
      // hide
      fb.fadeOut('slow', function() {
         
        fb.text(''); // Cuando termina la animacion le saca el texto
        fb.css({'opacity':'0.0', 'display':'none'});
      });
         
    }, settings['hideTime'], this); // le pasa this como parametro fb al callback de setTimeout
    
    
    return this; // chaining
  };
  
})(jQuery);