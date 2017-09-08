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
         width: 280px;
         height: 160px;
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
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">

	  // No puede renderearse sino es como modal
		//console.log(parent); window
		//console.log(parent.modal); undefined sino se muestra como modal
		if (parent.modal == undefined)
		{
		   location.href = "https://<?php echo $_SERVER['HTTP_HOST']; ?>";
		}
	
      $(document).ready( function() {
        
        $('#login').ajaxForm({success: callback, error: ajaxFormErrorHandler}); 
		
        parent.modal.modal('update');
      });
      
      var callback = function(json, status, response) {
        
        // Ve si todo salio ok y muestra feedback.
        if (json.status == 'ok')
        {
          // El usuario debe cambiar su clave
          if (json.changePassword == '1')
          {
            window.location = '<?php echo h('url', array('action'=>'changePassword')); ?>?flash.message=Ud. posee una cave temporal, por favor elija una nueva clave';
            return;
          }
          
          // Recarga la pagina actual para mostrar las funcionalidades disponibles ahora que esta logueado.
          // Necesito saber si ya hay parametros o no para agregar el flash.message
          if (parent.location.toString().indexOf('?')==-1)
          {
            parent.location = parent.location + '?flash.message='+ json.msg;
            return;
          }
          else
          {
            parent.location = parent.location + '&flash.message='+ json.msg;
            return;
          }
        }
        else
        {
          parent.modal.modal('feedback', json.msg); // Muestra mensaje de error.
          return;
        }
      }
    </script>
  </head>
  <body>
    <h1>Ingrese sus datos</h1>
    <form id="login" action="<?php echo h('url', array('action'=>'login')) ?>" method="post">
      <table>
        <tr>
          <td>Usuario:</td>
          <td><input type="text" name="username" /></td>
        </tr>
        <tr>
          <td>Clave:</td>
          <td><input type="password" name="password" /></td>
        </tr>
        <tr>
          <td>Seguir conectado:</td>
          <td><input type="checkbox" name="remember" /></td>
        </tr>
      </table>
      <div id="actions">
        <input type="submit" value="Entrar" name="doit" />
        <?php echo h('link', array('action'=>'sendPassword', 'body'=>'Recordar clave'))?>
      </div>
    </form>
  </body>
</html>