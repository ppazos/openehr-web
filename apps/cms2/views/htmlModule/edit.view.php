<?php

$m = Model::getInstance();

$module = $m->get('module');

YuppLoader::load('core.mvc', 'DisplayHelper');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      body {
        width: 770px; /* El editor de HTML se adapta al 100% de width de la pagina */
        height: 540px;
      }
      input[type=submit] {
        float: right;
        margin-top: 10px;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    
    <?php /* echo h('js', array('app'=>'cms2', 'name'=>'htmlModule/jquery.autosave.pack')); // http://rikrikrik.com/jquery/autosave */ ?>
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
          
          beforeSubmit: function (formData, form, options) {
              
            parent.modal.modal('feedback', 'Guardando...');
          },
          
          // Cuando el servidor responde ok, quiero actualizar
          // automaticamente el HTML del modulo sin hacer F5.
          success: function (res, status, response) {
            
            // Quiero el modulo que cambie
             
            // OK!!! esto me da el modulo
            //console.log( parent.document.getElementById('<?php echo $module->getClass().'__'.$module->getId(); ?>') );
             
            // Este es el modulo que cambie en el dom
            // Lo pido ala jQuery
            var module = $('#<?php echo $module->getClass().'__'.$module->getId(); ?>', parent.document);
            
            // Tengo que pedir al servidor el HTML actualizado para este modulo
            // Luego meto el actualizado en el dom, antes del que cambie
            // Luego elimino el modulo viejo del dom para que quede solo el actualizado
            
            $.ajax({
              url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContent', 'params'=>array('class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone')))); ?>',
              success: function (newModuleContent, status) {
                
                //console.log(newModuleHTML);
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
      <?php DisplayHelper::html( 'content', $module->getContent(), array('width'=>760, 'height'=>490) ); ?>
      <input type="submit" name="doit" value="Guardar" />
    </form>
  </body>
</html>