<?php

$m = Model::getInstance();

YuppLoader::load('core.mvc.form', 'YuppForm2');

?>
<html>
  <head>
    <!-- TODO: el estilo dependera del layout seleccionado -->
    <style type="text/css">
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      body {
         width: 215px;
         height: 90px;
      }
      input[type=text], input[type=password] {
        width: 170px;
      }
      form {
        margin-top: 5px;
      }
      #actions {
        margin-top: 5px;
        text-align: right;
      }
      .zone {
        background-color: #ffcc00;
      }
      .moduleContainer {
        background-color: #ccff00;
        margin: 1px;
        border: 1px solid #000;
        padding: 5px; /* para que se vea el containder verde */
      }
      .moduleTopBar {
        background-color: #aaccff;
        padding: 3px;
      }
      .moduleActions {
        background-color: #ccddff;
        padding: 3px;
        display: inline-block;
        float: right;
      }
      .moduleContent {
        background-color: #ccffff;
        padding: 3px;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.94')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">

      $(document).ready( function() {
        
        $('#sendPassword').ajaxForm({success: callback, error: ajaxFormErrorHandler}); 
        
        parent.modal.modal('update');
      });
      
      var callback = function(json, status, response) {
        
        //console.log($(json));
        //console.log(status);
        //console.log($(response));
        
        // Ve si todo salio ok y muestra feedback.
        if (json.status == 'ok')
        {
          // Recarga la pagina actual para mostrar las funcionalidades disponibles ahora que esta logueado.
          // Necesito saber si ya hay parametros o no para agregar el flash.message
          if (parent.location.toString().indexOf('?')==-1)
            parent.location = parent.location + '?flash.message='+ json.msg;
          else
            parent.location = parent.location + '&flash.message='+ json.msg;
        }
        else
        {
          parent.modal.modal('feedback', json.msg); // Muestra mensaje de error.
        }
      }
    </script>
  </head>
  <body>
    <h1>Recordar clave</h1>
    <form id="sendPassword" action="<?php echo h('url', array('action'=>'sendPassword')) ?>" method="post">
      <table>
        <tr>
          <td>Email:</td>
          <td><input type="text" name="email" /></td>
        </tr>
      </table>
      <div id="actions">
        <input type="submit" value="Recordar clave" name="doit" />
      </div>
    </form>
  </body>
</html>