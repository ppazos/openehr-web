<?php

$m = Model::getInstance();

YuppLoader::load('core.mvc.form', 'YuppForm2');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'backend')); ?>
    <style type="text/css">
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      body {
        width: 170px;
        height: 265px;
        background-color: transparent;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">

      $(document).ready( function() {
        
        // Notifica para que el parent actualice el tamanio del iframe
        parent.modal.modal('update');
      });
      
      var callback = function(res, status, response) {
        
        var moduleClass = $('select[name=class]').val();
        var zoneName = $('select[name="zone"]').val(); // La zona seleccionada por el usuario
        
        // Llamo al servidor para que me de el HTML del nuevo modulo
        $.ajax({
          url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContainer')); ?>',
          data: {
            class: moduleClass,
            id: res.id, // id del modulo creado
            pageId: res.pageId, // id de la pagina (se necesita para hacer display del moduleContainer)
            zone: zoneName
          },
          success: function (newModule, status) {
              
            // Agrega el modulo al DOM
            var zone = $('#'+zoneName, parent.document); // El id del elemento DOM de la zona es su nombre
            
            
            // Mete el modulo al final de la zona
            // Tambien ejecuta todo el JS que tenga adentro
            // Hay que tener cuidado por que el contexto de ejecucion de ese
            // JS es esta ventana modal (el iframe), no la ventana padre.
            zone.append( newModule );
            
            
            // Intenta cargar el javascript de edicion para la clase de modulo creada.
            // Asi se habilitan las acciones por ajax en la edicion del modulo.
            // loadLib esta definida en displayPage.view.php
            parent.loadLib(moduleClass);
            
        
            // Cierro la ventana modal en la que me estoy mostrando
            parent.modal.modal('hide');
          },
          error: ajaxFormErrorHandler
        });
      };
    </script>
  </head>
  <body>
    <?php
      // TODO: volver deberia ir a la pagina desde la que se ejecuto el edit...
      $f = new YuppForm2(array('action'=>'createModule', 'isAjax'=>true, 'ajaxCallback'=>'callback'));
      $f->add( YuppForm2::hidden(array('name'=>'pageId', 'value'=>$m->get('pageId'))) )
        ->add( YuppForm2::text(array('name'=>'title', 'value'=>$m->get('title'), 'label'=>'Titulo')) )
        ->add( YuppForm2::select(array('name'=>'status', 'value'=>$m->get('status'), 'options'=>Module::getStatusMap(), 'label'=>'Estado')) )
        ->add( YuppForm2::check(array('name'=>'showContainer', 'on'=>$m->get('showContainer'), 'label'=>'Mostrar contenedor')) )
        ->add( YuppForm2::check(array('name'=>'showInAllPages', 'on'=>$m->get('showInAllPages'), 'label'=>'Mostrar en todas las paginas')) )
        ->add( YuppForm2::select(array('name'=>'class', 'value'=>$m->get('class'), 'options'=>$m->get('classes'), 'label'=>'Tipo')) )
        ->add( YuppForm2::select(array('name'=>'zone', 'value'=>$m->get('zone'), 'options'=>$m->get('zones'), 'label'=>'Zona')) )
        ->add( YuppForm2::submit(array('name'=>'doit', 'label'=>'Crear')) );
      YuppFormDisplay2::displayForm( $f );
    ?>
  </body>
</html>