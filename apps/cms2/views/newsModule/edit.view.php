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
      body {
        width: 170px; /* El editor de HTML se adapta al 100% de width de la pagina */
        height: 100px;        
      }
      input[type=submit] {
        float: right;
        margin-top: 15px;
      }
      input[type=text] {
        width: 35px;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">
      
      $(document).ready( function() {
        
        // Notifica para que el parent actualice el tamanio del iframe
        parent.modal.modal('update');
        
        
        // Submitea el form por ajax
        $('#editForm').ajaxForm({
          
          // Cuando el servidor responde ok, quiero actualizar
          // automaticamente el HTML del modulo sin hacer F5.
          success: function (res, status, response) {
            
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
                $('#global_feedback', parent.document).feedback('Modulo actualizado correctamente');
                
                parent.modal.modal("hide");
              },
              error: ajaxFormErrorHandler
            });
          }
        });
      });
    </script>
  </head>
  <body>
    <form id="editForm" method="post" action="<?php echo h('url', array('action'=>'edit')); ?>">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      Paginado: <input type="checkbox" name="paged" value="true" <?php echo (($module->getPaged())?'checked="true"':''); ?> /><br/>
      Noticias por pagina: <input type="text" name="newsPerPage" value="<?php echo $module->getNewsPerPage(); ?>" /><br/>
      <input type="submit" name="doit" value="Guardar" />
    </form>
  </body>
</html>