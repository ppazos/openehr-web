<?php

$m = Model::getInstance();

$module = $m->get('module');

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
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <style>
      body {
        width: 480px;
        height: 520px;
      }
      #body_container { /* pongo esto y overflow-x: hidden en body en lugar de poner width 90% a todo */
        width: 460px;
        height: 520px;
        margin: 0;
        padding: 0;
      }
      h1, h2, h3 {
        margin-bottom: 5px;
      }
      form {
        border: 1px solid #9bd;
        padding: 3px;
        margin: 0;
        /*width: 90%;*/ /* porque siempre muestra scrol vertical y si width es 100% se pasa de horizontal y muestra el scroll hor tambien. */
      }
      .submit {
        text-align: right;
      }
      input[type=submit] {
      }
      label {
        display: inline-block;
        width: 200px;
      }
      #registered_files {
        padding: 0px;
        margin: 0px;
        height: 150px;
        overflow: scroll;
        overflow-x: hidden; /* Scroll hor escondido */
        /*width: 90%;*/ /* porque siempre muestra scrol vertical y si width es 100% se pasa de horizontal y muestra el scroll hor tambien. */
      }
      .file {
        font-size: .8em; /* Nombres de archivos un poco mas chicos que lo normal */
        display: table;
      }
      .file.odd {
        background-color: #cfefff;
      }
      .file.even {
        background-color: #bfdfff;
      }
      .file img {
        vertical-align: middle;
      }
      .icon, .info, .actions {
        display: table-cell;
        padding: 3px;
      }
      .icon {
        width: 32px;
      }
      .actions {
        width: 50px;
      }
      .help {
        font-size: .8em;
        color: #444;
        margin-bottom: 5px;
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
        
        <?php if ($m->flash('message')) : ?>
          parent.modal.modal('feedback', '<?php echo $m->flash('message'); ?>'); // La modal sigue abierta luego de editar vuelvo y muestro feedback.
        <?php endif; ?>
        
        // =======================================================================================
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
                    
                    console.log('success');
                    
                    // Actualiza solo el contenido!
                    module.children('.moduleContent').html(newModuleContent);
                    
                    // Recarga la pagina actual en la modal sin modificar el dom de la pagina atras.
                    if (parent.location.toString().indexOf('?')==-1)
                      window.location = window.location + '?flash.message='+ json.msg +'&id='+json.id+'&pageId='+json.pageId+'&zone='+json.zone;
                    else
                      window.location = window.location + '&flash.message='+ json.msg +'&id='+json.id+'&pageId='+json.pageId+'&zone='+json.zone;
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
        
        
        // =======================================================================================
        // TODO: actualizar el dom del modulo
        //
        // Submitea el form por ajax con datos para actualizar el modulo.
        //
        $('#scanForm').ajaxForm({
          
          // Cuando el servidor responde ok, quiero actualizar
          // automaticamente el HTML del modulo sin hacer F5.
          success: function (json, status, response) {
            
            // TODO: verificar errores y mostrar feedback.
            //console.log(json); // {"status":"ok", "msg":"Modulo actualizado con éxito", "id":12, "pageId":1, "zone":"col2"}
            if (json.status == "ok")
            {
              // Actualizar el contenido del modulo deberia hacerse al escanear,
              // subir, modificar o eliminar archivos.
                
              // Este es el modulo que cambie en el dom, lo pido ala jQuery
              var module = $('#<?php echo $module->getClass().'__'.$module->getId(); ?>', parent.document);
                 
              // Pide al servidor el HTML actualizado para este modulo
              // Actualiza el dom con el HTML antes del contenido anteior
              // Elimina el modulo viejo del dom para que quede solo el actualizado
                
              $.ajax({
                  url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContent', 'params'=>array('class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone')))); ?>',
                  success: function (newModuleContent, status) {
                    
                    console.log('success');
                    
                    // Actualiza solo el contenido!
                    module.children('.moduleContent').html(newModuleContent);
    
                    // El usuario cierra la ventana cuando termina
                    // Feedback arriba como tiene gmail...
                    //$('#global_feedback', parent.document).feedback('Modulo actualizado correctamente');
                    //parent.modal.modal("hide");
                    
                    
                    // Recarga la pagina actual en la modal sin modificar el dom de la pagina atras.
                    if (parent.location.toString().indexOf('?')==-1)
                      window.location = window.location + '?flash.message='+ json.msg +'&id='+json.id+'&pageId='+json.pageId+'&zone='+json.zone;
                    else
                      window.location = window.location + '&flash.message='+ json.msg +'&id='+json.id+'&pageId='+json.pageId+'&zone='+json.zone;
                    
                  },
                  error: function(x,e) { // http://www.maheshchari.com/jquery-ajax-error-handling/
                    
                    console.log('error');
                    
                    fb = $('#global_feedback', parent.document);
                    if(x.status==0) {
                      fb.feedback('Parece desconectado, verifique su conexion a internet');
                    }
                    else if(x.status==404) {
                      fb.feedback('Error, la direccion no existe');
                    }
                    else if(x.status==500) {
                      fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
                    }
                    else if(e=='parsererror') {
                      fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
                    }
                    else if(e=='timeout') {
                      fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
                    }
                    else {
                      fb.feedback('Ocurrio un error '+ x.responseText);
                    }
                  }
              }); // ajax actualizar contenido de modulo
            }
            else
            {
               // TODO: mostrar errores para los campos y que los corrija
               parent.modal.modal('feedback', json.msg); // La modal sigue abierta.
            }
            
          }, // ajax form success
          error: ajaxFormErrorHandler
          
        });
        
        
        // =======================================================================================
        // TODO: que actualice el DOM del modulo
        $('.remove_file').click( function (evn) {
          
          $.ajax({
            url: this.href,
            success: function (json, status, response) {
              
              console.log('success edit filesModule remove_file');
              console.log(json);
              
              if (json.status == "ok")
              {
                // Actualizar el contenido del modulo deberia hacerse al escanear,
                // subir, modificar o eliminar archivos.
                
                // Este es el modulo que cambie en el dom, lo pido ala jQuery
                var module = $('#<?php echo $module->getClass().'__'.$module->getId(); ?>', parent.document);
                 
                // Pide al servidor el HTML actualizado para este modulo
                // Actualiza el dom con el HTML antes del contenido anteior
                // Elimina el modulo viejo del dom para que quede solo el actualizado
                
                $.ajax({
                  url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContent', 'params'=>array('class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone')))); ?>',
                  success: function (newModuleContent, status) {
                    
                    console.log('success');
                    
                    // Actualiza solo el contenido!
                    module.children('.moduleContent').html(newModuleContent);
                    
                    // Recarga la pagina actual en la modal sin modificar el dom de la pagina atras.
                    if (parent.location.toString().indexOf('?')==-1)
                      window.location = window.location + '?flash.message='+ json.msg +'&id='+json.id+'&pageId='+json.pageId+'&zone='+json.zone;
                    else
                      window.location = window.location + '&flash.message='+ json.msg +'&id='+json.id+'&pageId='+json.pageId+'&zone='+json.zone;
                  },
                  error: ajaxFormErrorHandler
                  
                }); // ajax actualizar contenido de modulo
              }
              else
              {
                // TODO
              }
            }, // success
            error: ajaxFormErrorHandler
            
          }); // ajax
          
          return false;
          
        }); // remove_file click
        
      }); // document ready
    </script>
  </head>
  <body>
    <div id="body_container">
    <h1>M&oacute;dulo de archivos</h1>
    <form id="editForm" method="post" action="<?php echo h('url', array('action'=>'edit')); ?>">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      
      <label>Ruta de archivos</label>
      <input type="text" name="path" value="<?php echo $module->getPath(); ?>" /><br/>
      
      <label>Filtro por expresion regular</label>
      <input type="text" name="filter" value="<?php echo $module->getFilter(); ?>" /><br/>
      <div class="help">
        <b>/.*\.pdf/</b> solo los pdf<br/>
        <b>/.*.(pdf|docx)$/</b> pdf o docx<br/>
        <b>/.*.(pdf|docx)$/i</b> pdf o docx sin diferencia entre mayusculas y minusculas<br/>
      </div>
      
      <!--
      <label>Expresion regular para excluir</label>
      <input type="text" name="except" value="<?php echo $module->getExcept(); ?>" /><br/>
      -->
      
      <label for="iconSize">Tama&ntilde;o de icono</label>
      <select name="iconSize">
        <?php foreach ($module->iconSizeMap() as $key=>$val ) : ?>
          <option value="<?php echo $val; ?>" <?php echo (($val==$module->getIconSize())?'selected="selected"':''); ?>><?php echo $key; ?></option><!-- TODO: i18n -->
        <?php endforeach; ?>
      </select><br/>
      
      <label for="sortBy">Ordenar por</label>
      <select name="sortBy">
        <?php foreach ($module->getConstraintOfClass('sortBy', 'InList')->getList() as $val ) : ?>
          <option value="<?php echo $val; ?>" <?php echo (($val==$module->getSortBy())?'selected="selected"':''); ?>><?php echo $val; ?></option><!-- TODO: i18n -->
        <?php endforeach; ?>
      </select><br/>
      
      <label for="showSize">Mostrar tama&ntilde;o de archivos</label>
      <input type="checkbox" name="showSize" <?php echo (($module->getShowSize())?'checked="checked"':''); ?> value="1" /><br/>
      
      <label for="showDescription">Mostrar descripci&oacute;n de archivos</label>
      <input type="checkbox" name="showDescription" <?php echo (($module->getShowDescription())?'checked="checked"':''); ?> value="1" /><br/>
      
      <label for="showExtension">Mostrar extensi&oacute;n de archivos</label>
      <input type="checkbox" name="showExtension" <?php echo (($module->getShowExtension())?'checked="checked"':''); ?> value="1" /><br/>
      
      <label for="showLastUpdate">Mostrar fecha de actualizacion</label>
      <input type="checkbox" name="showLastUpdate" <?php echo (($module->getShowLastUpdate())?'checked="checked"':''); ?> value="1" /><br/>
      
      <div class="submit">
        <input type="submit" name="doit" value="Guardar" />
      </div>
    </form>
    
    <h2>Archivos registrados</h2>
    <div id="registered_files">
    <?php
      // con esta file_exists no funciona bien, pero funciona para img src...
      $ipath_base_img = $_base_dir.'/apps/cms2/images/filesModule/';
      
      // con esta img no funciona bien pero si file_exists
      $ipath_base = './apps/cms2/images/filesModule/';
    
      foreach($module->getFiles() as $i=>$file) :
    ?>
      <div class="file <?php echo (($i%2==0)?'even':'odd');?>">
        <div class="icon">
          <?php
           // con esta img no funciona bien pero si file_exists
           $ipath = $ipath_base.$file->getExtension().'-32.png';
           // con esta agarra el icono bien pero no file_exists
           $ipath_src = $ipath_base_img.$file->getExtension().'-32.png';
           if (!file_exists($ipath))
           {
              $ipath_src = $ipath_base_img.'default-32.png';
           }
           echo '<img src="'.$ipath_src.'" />';
          ?>
        </div>
        <div class="info">
          <?php echo $file->getFullName() .' ('. formatBytes($file->getSize(), 0) .')<br/>'; ?>
        </div>
        <div class="actions">
          <a href="<?php echo h('url', array('action'=>'editFile', 'fileId'=>$file->getId(), 'id'=>$module->getId(), 'zone'=>$m->get('zone'), 'pageId'=>$m->get('pageId'))); ?>"><img src="<?php echo $_base_dir.'/apps/cms2/images/edit.gif'; ?>" alt="Editar datos del archivo" /></a>
          <a href="<?php echo h('url', array('action'=>'removeFile', 'fileId'=>$file->getId(), 'id'=>$module->getId(), 'zone'=>$m->get('zone'), 'pageId'=>$m->get('pageId'))); ?>" class="remove_file"><img src="<?php echo $_base_dir.'/apps/cms2/images/delete.gif'; ?>" alt="Eliminar referencia al archivo" /></a>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
    
    <h2>Otras acciones</h2>
    <h3>Registrar nuevos archivos</h3>
    <form id="scanForm" method="post" action="<?php echo h('url', array('action'=>'scan')); ?>">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      <div class="submit">
        <input type="submit" name="doit" value="Explorar nuevos archivos" />
      </div>
    </form>
    
    <h3>Subir archivo</h3>
    <form enctype="multipart/form-data" action="<?php echo h('url', array('action'=>'upload')); ?>" method="POST">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      
      <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
      <label for="filedata">Seleccione un archivo</label><input name="filedata" type="file" id="filedata" />
      <div class="submit">
        <input type="submit" value="Subir archivo" />
      </div>
    </form>
    </div>
  </body>
</html>