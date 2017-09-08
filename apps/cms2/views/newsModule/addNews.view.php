<?php

$m = Model::getInstance();

$module = $m->get('module');

YuppLoader::load('core.mvc', 'DisplayHelper');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- TODO: el estilo dependera del layout seleccionado -->
    <style>
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      body {
        width: 480px; /* El w de la pagina es mas chico que el del html porque se le suma un padding y queda mas grande */
        height: 400px;
        margin: 0;
        overflow-x: hidden;
      }
      input[type=submit] {
        float: right;
        margin-top: 15px;
      }
      input[type=text] {
        width: 250px;
      }
      /* width del editor html */
      iframe#text_ifr {
        width: 470px !important;
      }
    </style>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">
      
      // Funcion que va a llamar el editor cuando termine de cargar (ver config del TinyMCE)
      var htmlinit = function() {

         // Notifica para que el parent actualice el tamanio del iframe
         parent.modal.modal('update');
      }
      
      $(document).ready( function() {
        
        // Para que actualice el textarea con el contenido del TinyMCE, antes de mandarlo por ajax.
        // Ref: http://maestric.com/doc/javascript/tinymce_jquery_ajax_form
        $('#editForm').bind('form-pre-serialize', function(e) {
            
            tinyMCE.triggerSave();
        });
        
        
        // Submitea el form por ajax
        $('#editForm').ajaxForm({
          
          // Cuando el servidor responde ok, quiero actualizar
          // automaticamente el HTML del modulo sin hacer F5.
          success: function (json, status, response) {
            
            if (json.status == 'ok')
            {
              // Este es el modulo que cambie en el dom, lo pido ala jQuery
              var module = $('#<?php echo $module->getClass().'__'.$module->getId(); ?>', parent.document);
                 
              // Tengo que pedir al servidor el HTML actualizado para este modulo
              // Luego meto el actualizado en el dom, antes del que cambie
              // Luego elimino el modulo viejo del dom para que quede solo el actualizado
                
              $.ajax({
                  url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContent', 'params'=>array('class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone')))); ?>',
                  success: function (newModuleContent, status) {
                    
                    // Actualiza solo el contenido!
                    module.children('.moduleContent').html(newModuleContent);
    
                    // Feedback arriba como tiene gmail...
                    $('#global_feedback', parent.document).feedback(json.msg);
              
                    parent.modal.modal("hide");
                  }
                  
                  //
                  // TODO: caso de error
                  //
                  
              });
              
              // Si se esconde la modal aqui, se cancela el request de
              // actualizacion del contenido del modulo que se manda por ajax.
            }
            else
            {
              // Caso de error
              parent.modal.modal('feedback', json.msg);
            }
          },
          error: ajaxFormErrorHandler
          
        });
      });
    </script>
  </head>
  <body>
    <h1>Crear noticia</h1>
  
    <form id="editForm" method="post" action="<?php echo h('url', array('action'=>'addNews')); ?>">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      Titulo: <input type="text" name="title" /><br/>
      Texto:
      <?php DisplayHelper::html( 'text', '', array('height'=>140, 'width'=>420) ); ?>
      Link: <input type="text" name="link" /><br/>
      
      <!--
      TODO: poder hacer que el link sea a una pagina del CMS
      Agregar link? <input type="checkbox" name="has_link" onclick="linkClick()" /><br /><br />

        <div id="link_data" style="display:none">
        
            Tipo de destino del link:<br />
            <select name="local" onChange="updateLinkControls()">
            <option value="1">Página del sitio</option>
            <option value="0">Otro sitio web</option>
            </select>
            
            <br /><br />
        
            <div id="url_div" style="display:none">
            URL: <input type="text" name="link_url" size="50" /><br />
            </div>
            
            <div id="page_select">
            Páginas del sitio:<br /> [::PAGE_SELECTOR::]
            </div>
        
        </div><br />
      -->
      
      <input type="submit" name="doit" value="Guardar" />
    </form>
  </body>
</html>