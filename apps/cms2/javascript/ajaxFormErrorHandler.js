// x XMLHttpResponse
// e status
//
var ajaxFormErrorHandler = function(x, e, errorThrown) { // http://www.maheshchari.com/jquery-ajax-error-handling/
  
  //console.log(x, e, errorThrown);
  
  fb = $('#global_feedback', parent.document);
      
  if(x.status==0)
  {
    fb.feedback('Parece desconectado, verifique su conexion a internet');
  }
  else if(x.status==404)
  {
    fb.feedback('Error, la direccion no existe');
  }
  else if(x.status==500)
  {
    fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
  }
  else if(e=='parsererror')
  {
    fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
  }
  else if(e=='timeout')
  {
    fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
  }
  else {
    fb.feedback('Ocurrio un error '+ x.responseText);
  }
}