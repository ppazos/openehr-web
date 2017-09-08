<?php

$m = Model::getInstance();

$module = $m->get('module');
$file = $m->get('file');

YuppLoader::load('core.mvc', 'DisplayHelper');

global $_base_dir;

// http://stackoverflow.com/questions/2510434/php-format-bytes-to-kilobytes-megabytes-gigabytes
function formatBytes($bytes, $precision = 2)
{ 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- TODO: el estilo dependera del layout seleccionado -->
    <style>
      body {
        width: 620px;
        height: 400px;
        margin: 0;
      }
      input[type=text], textarea {
        width: 410px;
      }
      input[type=submit] {
        float: right;
        margin-top: 10px;
      }
      label {
        display: inline-block;
        width: 110px;
        vertical-align: top;
      }
      textarea {
        height: 200px;
      }
    </style>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">
      $(document).ready( function() {
        
        // Notifica para que el parent actualice el tamanio del iframe
        parent.modal.modal('update');
        
        
        // Submitea el form por ajax con datos para actualizar el modulo.
        $('#editForm').ajaxForm({
          
          // Cuando el servidor responde ok, quiero actualizar
          // automaticamente el HTML del modulo sin hacer F5.
          success: function (json, status, response) {
            
            // TODO: verificar errores y mostrar feedback.
            //console.log(json); // {"status":"ok", "msg":"Modulo actualizado con éxito", "id":12, "pageId":1, "zone":"col2"}
            if (json.status == "ok")
            {
              // Actualiza contenido de modulo porque si cambiaron las opciones de vista, cambia el contenido
                
              // Este es el modulo que cambie en el dom, lo pido ala jQuery
              var module = $('#<?php echo $module->getClass().'__'.$module->getId(); ?>', parent.document);
                 
              // Pide al servidor el HTML actualizado para este modulo
              // Actualiza el dom con el HTML antes del contenido anteior
              // Elimina el modulo viejo del dom para que quede solo el actualizado
                
              $.ajax({
                url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContent', 'params'=>array('class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone')))); ?>',
                success: function (newModuleContent, status) {
                    
                    console.log('success editFile');
                    
                    // Actualiza solo el contenido!
                    module.children('.moduleContent').html(newModuleContent);
                    
                    
                    parent.modal.modal('feedback', json.msg); // La modal sigue abierta.
                    
                    
                    /* No quiero recargar porque el form va por ajax, sino carga de nuevo lo que ya estoy viendo.
                     *
                    // Recarga la pagina actual en la modal sin modificar el dom de la pagina atras.
                    if (parent.location.toString().indexOf('?')==-1)
                      window.location = window.location + '?flash.message='+ json.msg +'&id=<?php echo $module->getId(); ?>&pageId=<?php echo $m->get('pageId'); ?>&zone=<?php echo $m->get('zone'); ?>';
                    else
                      window.location = window.location + '&flash.message='+ json.msg +'&id=<?php echo $module->getId(); ?>&pageId=<?php echo $m->get('pageId'); ?>&zone=<?php echo $m->get('zone'); ?>';
                    */
                },
                error: ajaxFormErrorHandler
                
              }); // ajax actualiza ontenido de modulo
               

              // Que el usuario cierre la modal cuando termine.
            }
            else
            {
               // TODO: mostrar errores para los campos y que los corrija
               parent.modal.modal('feedback', json.msg); // La modal sigue abierta.
            }
            
          }, // ajax form success
          error: ajaxFormErrorHandler
          
        }); // ajaxForm
      });
    </script>
  </head>
  <body>
    <h1>Configuracion del modulo de archivos</h1>
    <form id="editForm" method="post" action="<?php echo h('url', array('action'=>'editFile')); ?>">
      
      <!-- datos necesarios para el callback -->
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      
      <input type="hidden" name="fileId" value="<?php echo $file->getId(); ?>" />
      
      <label>Nombre</label>
      <input type="text" name="name" value="<?php echo $file->getName(); ?>" disabled="true" /><br/>
      
      <label>Descripci&oacute;n</label>
      <textarea name="description" ><?php echo $file->getDescription(); ?></textarea></br>
      
      <label>Tama&ntilde;o</label>
      <input type="text" name="size" value="<?php echo $file->getSize(); ?>" disabled="true" /><br/>
      
      <label>Modificado</label>
      <input type="text" name="lastUpdate" value="<?php echo $file->getLastUpdate(); ?>" disabled="true" /><br/>
            
      <input type="submit" name="doit" value="Guardar" />
      <?php echo h('link', array('action'=>'edit', 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone'), 'body'=>'Volver')); ?>
    </form>
  </body>
</html>