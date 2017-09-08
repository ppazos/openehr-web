;(function($) {

  // TODO: que el corte no ocurra a la mitad de una palabra
  $.fn.shorten = function(settings) {
  
   var config = $.extend( {
     showChars : 100,
     ellipsesText : "...",
     moreText : "expandir",
     lessText : "contraer"
    }, settings);
  
    $('.morelink').live('click', function() {
      var $this = $(this);
      
      // Toggle del nombre del link
      if ($this.hasClass('less')) { // clic en more para mostrar less
        
        $this.removeClass('less');
        $this.html(config.moreText);
        
        // muestro shorcontent y escondo allcontent
        $this.parent().prev().prev().show(); // shortcontent
        $this.parent().prev().hide(); // allcontent
        
      } else { // click en less para mostrar more
        
        $this.addClass('less');
        $this.html(config.lessText);
        
        $this.parent().prev().prev().hide(); // shortcontent
        $this.parent().prev().show(); // allcontent
      }
      
      return false;
    });
  
    return this.each(function() {
      var $this = $(this);
  
      var content = $this.html();
      if (content.length > config.showChars) {
        var c = content.substr(0, config.showChars);
        if (c.indexOf('<') >= 0) // Si hay HTML tengo que tener cuidado de no cortarlo!
        {
          //var index = 0; // Indice de recorrida del string
          var inTag = false; // Indicador de que el indice actual esta dentro de una tag
          var bag = ''; // String donde voy acumulando los caracteres que se muestran
          // Recorro el texto, cortando lo que no es tag hasta alcanzar el largo deseado, y asegurar de que no hay una tag abierta cordada
          var countChars = 0; // Caracteres del string resultante
          var openTags = []; // sirve como stack para poner y sacar tags, si termina no vacio, hay tags sin cerrar
          
          for (i=0; i<content.length; i++)
          {
		    //console.log('0. openTags: '+ openTags);
			// Si encuentro una tag
            if (content[i] == '<' && !inTag)
            {
              inTag = true;
              
              // Cuidado, puede ser un cierre de tag y le pone /tag
              tagName = content.substring(i+1, content.indexOf('>', i));
              
              // Si es una tag que cierra otra, la que cierra deberia estar arriba en el stack (verifico por las dudas)
              if (tagName[0] == '/')
              {
			    // openTags[0] puede tener atributos, los saco para comparar solo el nombre con el cierre
				idx_space = openTags[0].indexOf(' '); // Si la tag tiene atributos, el indice del espacio sera != -1 ej. 'a href="..."'
				openTagName = openTags[0]; // Por defecto supongo que openTags[0] no tiene atributos (es solo el nombre)
				if (idx_space != -1) openTagName = openTags[0].substring(0, idx_space); // Tiene atributos, me quedo solo con el nombre
				
				/*
				console.log('a tagName: '+ tagName);
                console.log('a tagName[0]: '+ tagName[0]);
				console.log('a idx_space: '+ idx_space);
                console.log('a openTags[0]: '+ openTags[0]);
                console.log('a openTagName: **'+ openTagName +'**');
				*/
				
                if (tagName != '/'+openTagName)
               	{
                  console.log('ERROR en HTML: el tope del stack debe ser la tag que cierra');
                  console.log('tagName: '+ tagName);
                  console.log('tagName[0]: '+ tagName[0]);
                  console.log('openTags[0]: '+ openTags[0]);
                  console.log('openTagName: '+ openTagName);
                }
                else
                  openTags.shift(); // Saca el tope del stack (tag que acabo de cerrar) 
              }
              else
              {
                // Evita poner en el stack la tag que no se cierra, TODO: pueden haber otras...
                if (tagName.toLowerCase() != 'br')
                  openTags.unshift( tagName ); // Agrega al inicio el nombre de la tag que abre
              }
              
              
              // TODO: asegurarme de que la tag esta cerrada cuando termine de cortar el texto
            }
            if (inTag && content[i] == '>')
            {
              inTag = false;
            }
            
            if (inTag) bag += content[i]; // Agrego toda la tag
            else
            {
              // Si faltan caracteres para poner en la bag
              if (countChars < config.showChars)
              {
                bag += content[i];
                countChars ++;
              }
              else // Ya tengo los caracteres necesarios
              {
                if (openTags.length > 0) // Tengo tags sin cerrar
                {
                  //console.log('Quedaron tags abiertas');
                  //console.log(openTags);
                  for (j=0; j<openTags.length; j++)
                  {
                    //console.log('Cierro tag '+ openTags[j]);
                    bag += '</'+ openTags[j] +'>'; // Cierro todas las tags que quedaron abiertas
                    
                    // FIXED arriba:
                    // tags como <br> pueden abrirse y no tener cierre, o sea que quedan en el stack pero si las cierro no queda un HTML valido porque no hay apertura.
                    
                    // Podria sacar la tag cerrada del stack para que salga un stack vacio.
                  }
                  break; // Del for por los caracteres
                }
				else // Si tengo los caracteres y no tengo tags abiertas, puedo terminar el loop
				{
				  break; // Del for por los caracteres
				}
              }
            }
			
			//console.log('bag ', i, ' : ', bag);
          }
          //console.log(openTags);
          
          c = bag;
        }
        
        // No quiero el resto del string, quiero ocultar la span con el string completo y cuadno hago more muestro esas y oculto el string cortado.
        //var h = content.substr(config.showChars , content.length - config.showChars);
        var html = '<span class="shortcontent">' + c + '&nbsp;' + config.ellipsesText +
                   '</span><span class="allcontent">' + content +
                   '</span>&nbsp;&nbsp;<span><a href="javascript://nop/" class="morelink">' + config.moreText + '</a></span>';
        
        $this.html(html);
        $(".allcontent").hide(); // Esconde el contenido completo para todos los textos
      }
    });
  };
})(jQuery);