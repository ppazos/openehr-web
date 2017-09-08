<?php

/**
 * Lista modulos huerfanos en la ventana modal, para
 * agregarlos a una zona de la pagina actual.
 * 
 * Esta vista es siempre modal.
 */

$m = Model::getInstance();

$modules = $m->get('modules');
$zones = $m->get('zones');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
      body {
        width: 540px;
        height: 400px;
      }
      label {
        display: block;
        min-width: 200px;
      }
      form {
        display: table;
        width: 100%;
        margin: 15px 0 0 0;
        padding: 0;
      }
      #left_col {
        display: table-cell;
        vertical-align: top;
        overflow-y: scroll;
        width: 50%;
        margin-bottom: 10px;
        height: 300px;
      }
      #right_col {
        display: table-cell;
        vertical-align: top;
        padding-left: 10px;
        width: 47%;
        text-align: right;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">
      /**
       * El form debe submitearse por ajax, porque tiene que ir al server:
       *  - agregarlo en la zona,
       *  - venir acutalizar la zona,
       *  - dar feedback en message,
       *  - remover el modulo de la lista de huerfanos (o relistar),
       *  - y dejar que el usuario siga agregando modulos hasta que quiera y cierre la modal.
       */
      $(document).ready( function() {
        
        if (parent) parent.modal.modal('update');
        
        // Submitea el form por ajax
        $('#frm1').ajaxForm({
          
          beforeSubmit: function (formData, form, options) {
              
              for (var i=0; i < formData.length; i++)
              { 
                if (formData[i].name == 'moduleId') return true; 
              }
              
              //console.log(formData); // array de objetos con name y value
              
              alert('Por favor seleccione un modulo huerfano');
              return false;
          },
          
          // Si el servidor responde ok, quiero actualizar la zona donde se agrego el modulo.
          success: function (res, status, response, form) {
            
            //alert("la accion zoneContentAction no esta implementada y es la que necesito para esto...");
            //console.log(res); // id, pageId, status
            
            if (res.status == "ok")
            {
              $.ajax({
                url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'zoneContent', 'params'=>array('pageId'=>$m->get('pageId')))); ?>',
                data: {
                  zone: $('select[name=zone]').val()
                },
                success: function (info, status1, response1) {
                
                  //console.log(info); // moduleClases ([]), zone, content (HTML)
                  
                  
                  // ==================================================================
                  // Carga el js de edit de cada clase de modulo en esta zona.
                  // EN REALIDAD SERIA SOLO PARA LA CLASE DEL MODULO QUE SEA AGREGA
                  // PORQUE LOS OTROS YA SE ESTABAN MOSTRANDO Y SU JS ESTA CARGADO
                  // ==================================================================
                  
                  //console.log( $('input[name=moduleId]:checked').parent() ); // label id=moduleClass__moduleId
                  label = $('input[name=moduleId]:checked').parent();
                  data = label.attr('id').split("__");
                  moduleClass = data[0];
                  //moduleId = data[1];
                  
                  parent.loadLib(moduleClass); // Intenta cargar el js de edit de esta clase de modulo, y carga solo si no esta ya cargado
                  
                  
                  // Actualiza la zona, con todos sus modulos
                  $('#'+info.zone, parent.document).html( info.content ); // OJO: content es HTML serializado a JSON
                  
                  // Da feedback al usuario en la modal
                  //parent.showModalFeedback('Se ha agregado el modulo a la zona de la pagina actual');
                  // Ahora implementado con plugin modal
                  parent.modal.modal('feedback', 'Se ha agregado el modulo a la zona de la pagina actual');
                  
                  // Quita el modulo de la lista de huerfanos
                  label.remove();
                  
                },
                error: ajaxFormErrorHandler
              });
            }
          }
        });
      });
    </script>
  </head>
  <body>
    <h1>Modulos huerfanos</h1>
    Seleccione un modulo para ubicarlo en una zona de la pagina actual.
    <form id="frm1" method="post" action="<?php echo h('url', array('action'=>'addOrphanModuleToPageZone')); ?>">
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <div id="left_col">
        <?php if ( count($modules) == 0 ) echo 'No hay modulos huerfanos en este momento...'; ?>
        <?php foreach ( $modules as $module ) : ?>
          <label id="<?php echo $module->getClass(); ?>__<?php echo $module->getId(); ?>">
            <input type="radio" name="moduleId" value="<?php echo $module->getId(); ?>" />
            <?php echo $module->getTitle(); ?> (<?php echo $module->getClass(); ?>)
            <!-- <?php echo $module->getCreatedOn(); ?> -->
          </label>
        <?php endforeach; ?>
      </div>
      <div id="right_col">
        <select name="zone">
          <option value="">Seleccione una zona...</option>
          <?php foreach ( $zones as $zonename ) : ?>
            <option value="<?php echo $zonename; ?>"><?php echo $zonename; ?></option>
          <?php endforeach; ?>
        </select>
        <input type="submit" value="Agregar" name="doit" /> 
      </div>
    </form>
  </body>
</html>