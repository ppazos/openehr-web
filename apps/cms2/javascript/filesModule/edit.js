// Carga download plugin
(function(){

  if ($.download)
  {
    console.log('Download plugin ya esta cargado');
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
  var scriptUrl = window.location.protocol +"//"+ window.location.host + path + '/apps/cms2/javascript/filesModule/jquery.download.js';
  
  console.log(scriptUrl);
  
  // Carga con jQuery
  // http://api.jquery.com/jQuery.getScript/
//  $.getScript(scriptUrl, function(data, textStatus, jqxhr) {
  
    //console.log(data); //data returned
    //console.log(textStatus); //success
    //console.log(jqxhr.status); //200
    console.log('Load was performed.');
	
	// live() esta deprecated ahora se usa on()
	$('a.download_file').on('click', function(evt) {
	
	  evt.preventDefault();
	  //console.log('a.download_file click');
	
	  /*
	  var ifrm_id = 'ifrm_'+ Math.floor((Math.random()*100000)+1); // ifrm_1 .. 100000
	  var ifrm = $('<iframe src="'+ window.location.protocol +"//"+ window.location.host + path + '/cms2/filesModule/download"></iframe>');
	  console.log('iframe a :', window.location.protocol +"//"+ window.location.host + path + '/cms2/filesModule/download' );
	  $('body').append(ifrm);
	  
	  console.log('agrega iframe');
	  */
	  
	  var download_url = this.href;
	  
	  // Armo url a verifyDownload con los params del link (this.href)
	  //var nvpair = {}; // si quisiera parsear los params...
	  var params = download_url.split('?')[1];
	  /*
      var pairs = params.split('&');
      $.each(pairs, function(i, v){
        var pair = v.split('=');
        nvpair[pair[0]] = pair[1];
      });
	  */
	  
	  var verifyDownloadUrl = window.location.protocol +"//"+ window.location.host + path + '/cms2/filesModule/verifyDownload?'+ params;
	  
	  console.log(verifyDownloadUrl);
	  
	  // TODO: ver status de redireccion en lugar de retorno json, eso tambien es un error: no tiene permisos!
	  // Validacion de la descarga
	  $.get(
	    verifyDownloadUrl,
		function(data, textStatus, jqXHR) {
		
		  console.log(data, textStatus, jqXHR);
		  
		  if (jqXHR.status == 200 && typeof(data) === "string") // si se retorna algo que no es json
		  {
		    // Error, seguramente no tiene permisos...
			//console.log('no tiene permisos');
			$('#global_feedback').feedback('Es probable que no tenga permisos para descargar el archivo, ingrese con su usuario y clave');
		  }
		  else if (data.status == "ok")
		  {
		    $('#global_feedback').feedback('La descarga comenzará en breve...');
			
		    // Si esta ok, hacer la descarga usando form...
		    // download plugin no garantiza que si hay errores no se navegue a otra pagina
	        //$.download('/export.php','filename=mySpreadsheet&format=xls&content=' + spreadsheetData );
			//$.fdownload(this.href, '', '');
			$('<form action="'+ download_url +'" method="post"></form>').appendTo('body').submit().remove();
	      }
		  else
		  {
			$('#global_feedback').feedback('Ocurrió un error: '+ data.msg);
		  }
		}
	  )
	  .error(function(xhr, error) {
	  
	    console.log(xhr, error);
		$('#global_feedback').feedback('Ocurrió un error al intentar descargar el archivo');
	  });
	  
	  
	  return false; // previene navegacion
	});
	
//  });
  
})();