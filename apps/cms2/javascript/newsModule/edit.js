$('._news_open_modal').live('click', function(evt) { // Es live por si creo menues y los muestro con ajax, quiero el edit activo en esos nuevos modulos.
   
   modal.modal('load', this.href);
   
   return false;
});

/*
 * Load shorten plugin para texto largo
 * http://code.google.com/p/yupp-cms/issues/detail?id=58
 */
(function(){

  if ($.shorten)
  {
    console.log('Shorten plugin ya esta cargado');
    return;
  }
  
  // Necesito la path que va a ser todo lo que esta adelante del cms2 en la url
  var pathArray = window.location.pathname.split( '/' );
  var path = '';
  for (i=1; i<pathArray.length; i++) 
  {
    if (pathArray[i]=='cms2') break;
    path += "/" + pathArray[i];
  }
  var scriptUrl = window.location.protocol +"//"+ window.location.host + path + '/apps/cms2/javascript/newsModule/jquery.shorten.1.0.js';
  //var scriptUrl = window.location.host + path + '/apps/cms2/javascript/newsModule/jquery.shorten.1.0.js';
  
  //console.log(window.location.protocol, scriptUrl);
  
  // Carga con jQuery
  // http://api.jquery.com/jQuery.getScript/
  $.getScript(scriptUrl, function(data, textStatus, jqxhr) {
    //console.log(data); //data returned
    //console.log(textStatus); //success
    //console.log(jqxhr.status); //200
    //console.log('Load was performed.');
    $('.news_text').shorten({showChars:150});
  });
  
})();

// Inicializacion de los botones de paginacion en cuanto a su visibilidad
(function(){
  
  // Si tiene la class inactive, tengo que esconder el boton
  // Esto no se hace por CSS porque es CSS que depende de un tipo de modulo y ahora no se tiene ese tipo de CSS (esto es un FIXME)
  $('.news_pager').each( function(i, elem) {
    
    $elem = $(elem);
    if ($elem.hasClass('inactive'))
    {
      $elem.hide();
      $elem.removeClass('inactive');
    }
  });
  
})();

/**
 * Click en links de paginacion de modulos de noticias.
 */
//var _news_handler_next = function (html, status, response) {
$('.news_pager').live('click', function(evt) {

   //console.log(evt.target.href); // news_pager
   //console.log(evt.target.id);   // news_pager_[next|prev]_[module_id]
   //console.log(evt.target.id.split('_')); // ["news", "pager", "next", "10"] // idx 2 es la accion, idx 3 es el id del modulo

   //console.log(evt.target.parentNode.href);
   var link = evt.target.parentNode; // Ahora como los links son imagenes, el target es img, para obtener el link es parentNode.
   //var url = evt.target.parentNode.href;

   var id_parts = link.id.split('_');

   /**
    * Implementa la llamada por ajax al servidor para pedir y actualizar las noticias paginadas.
    */
   $.ajax({
     url: link.href,
     success: function (html, status) {
     
       //console.log(html);
       
       // Seccion que muestra las noticias dentro del modulo
       var news_section = $('#news_module_news_'+id_parts[3]);
       news_section.html(html);
       
       
       /****************************************************************************************
        * Se deben actualizar los links de next y prev para considerar el nuevo offset,
        * dependiendo de si la accion es 'next' o 'prev'.
        */
       var purl = parseUri(link.href);
       var offset = parseInt(purl.queryKey['offset']);
       
       if (id_parts[2] == 'next') // Actualiza el offset para la pagina siguiente
       {
         // NEXT
         purl.queryKey['offset'] = offset + parseInt(purl.queryKey['max']); // parseInt porque son strings
           
         // Si el nuevo offset es mayor que la cantidad de registros, inactivo el NEXT
         if (purl.queryKey['offset'] >= purl.queryKey['count']) $(link).hide(); // $(link).addClass('inactive');
         else
         {
           link.href = createUrl(purl); // Actualiza la URL en el link clickeado
           //console.log(link.href);
         }
           
         // El otro link de paginacion
         purl.queryKey['offset'] = offset - parseInt(purl.queryKey['max']);
         var prev_link = $('#news_pager_prev_'+id_parts[3]);
         
         prev_link.attr('href', createUrl(purl));
         
         // Si el link prev estaba inactivo, lo activo porque estoy haciendo NEXT.
         //if (prev_link.hasClass('inactive')) prev_link.removeClass('inactive');
         prev_link.show();
       }
       else // Actualiza el offset para la pagina previa
       {
         // PREV
         // Si se llega al offset 0, se inactiva el link de prev y no actualizo link
         if (offset == 0) $(link).hide(); // $(link).addClass('inactive');
         else
         {
           purl.queryKey['offset'] = offset - parseInt(purl.queryKey['max']);
           
           // Actualiza la URL en el link clickeado
           link.href = createUrl(purl);
         }
         
         
         // NEXT
         purl.queryKey['offset'] = offset + parseInt(purl.queryKey['max']);
           
         var next_link = $('#news_pager_next_'+id_parts[3]); 
         next_link.attr('href', createUrl(purl));
         
         // Si el next esta inactivo, lo activo porque estoy haciendo PREV
         //if (next_link.hasClass('inactive')) next_link.removeClass('inactive');
         next_link.show();
       }
       /**
        * Actualiza links de prev y next
        ************************************************************************************/
       
     },
     error: function (response, status) {
     
       fb = $('#global_feedback', parent.document);

       if (response.status == 0) fb.feedback('Parece desconectado, verifique su conexion a internet');
       else if(response.status == 404) fb.feedback('Error, la direccion no existe');
       else if(response.status == 500) fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
       else if(status == 'parsererror') fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
       else if(status == 'timeout') fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
       else fb.feedback('Ocurrio un error '+ response.responseText);
     }
   });
   
   //console.log( parseUri(evt.target.href) ); // http://localhost/YuppPHPFramework/cms2/newsModule/nextPage?id=10&pageId=1&zone=col2&offset=4&max=4
   /*
   Object
      anchor: ""
      authority: "localhost"
      directory: "/YuppPHPFramework/cms2/newsModule/nextPage"
      file: ""
      host: "localhost"
      password: ""
      path: "/YuppPHPFramework/cms2/newsModule/nextPage"
      port: ""
      protocol: "http"
      query: "id=10&pageId=1&zone=col2&offset=4&max=4"
      queryKey: Object
         id: "10"
         max: "4"
         offset: "4"
         pageId: "1"
         zone: "col2"
      __proto__: Object
      relative: "/YuppPHPFramework/cms2/newsModule/nextPage?id=10&pageId=1&zone=col2&offset=4&max=4"
      source: "http://localhost/YuppPHPFramework/cms2/newsModule/nextPage?id=10&pageId=1&zone=col2&offset=4&max=4"
      user: ""
      userInfo: ""
   */
   
   return false;
});


// Auxiliar para modificar URLs
//
// http://blog.stevenlevithan.com/archives/parseuri
//
// parseUri 1.2.2
// (c) Steven Levithan <stevenlevithan.com>
// MIT License

function parseUri (str) {
   var   o   = parseUri.options,
         m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
         uri = {},
         i   = 14;

   while (i--) uri[o.key[i]] = m[i] || "";

   uri[o.q.name] = {};
   uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
      if ($1) uri[o.q.name][$1] = $2;
   });

   return uri;
};

parseUri.options = {
   strictMode: false,
   key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
   q:   {
      name:   "queryKey",
      parser: /(?:^|&)([^&=]*)=?([^&]*)/g
   },
   parser: {
      strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
      loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
   }
};

/**
 * Metodo para obtener el URL string desde un parserUrl.
 * Cuidado que no es generico, falta considerar protoco, puerto, etc.
 */
function createUrl(parsedUrl) {

   var new_url = parsedUrl.path + '?';
   for ( k in parsedUrl.queryKey )
   {
     //console.log(k); // id, pageId, zone, ...
     new_url += k + '=' + parsedUrl.queryKey[k] + '&';
   }
   
   // TODO: sacar le ultimo & de la url
   
   return new_url;
};