<?php

$m = Model::getInstance();

// Pagina a mostrar, tiene a que sitio pertenece
$module = $m->get('module');

YuppLoader::load('core.mvc.form', 'YuppForm2');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'backend')); ?>
    <style type="text/css">
      body {
        width: 305px;
        height: 155px;
        background-color: transparent;
      }
      form {
        display: table;
        margin-top: 10px;
      }
      .label, .field {
        display: table-cell;
      }
      .label {
        width: 165px;
      }
      .submit {
        display: block;
        padding-top: 10px;
        text-align: right;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <script type="text/javascript">

      $(document).ready( function() {
        
        // Notifica para que el parent actualice el tamanio del iframe
        parent.modal.modal('update');
      });
      
      var callback = function(res, status, response) {
          
        // Por ahora en el callback del editModule no actualizo nada en el DOM.
        // TODO: deberia actualizar por ejemplo el titulo del modulo.
        
        parent.modal.modal('hide');
      };
    </script>
  </head>
  <body>
    <h1>Configuracion del modulo</h1>
    <?php
      // TODO: volver deberia ir a la pagina desde la que se ejecuto el edit...
      $f = new YuppForm2(array('action'=>'editModule', 'isAjax'=>true, 'ajaxCallback'=>'callback'));
      $f->add( YuppForm2::hidden(array('name'=>'pageId', 'value'=>$m->get('pageId'))) )
        ->add( YuppForm2::hidden(array('name'=>'class', 'value'=>$m->get('class'))) )
        ->add( YuppForm2::hidden(array('name'=>'id', 'value'=>$m->get('id'))) )
        ->add( YuppForm2::text(array('name'=>'title', 'value'=>$module->getTitle(), 'label'=>'Titulo')) )
        ->add( YuppForm2::select(array('name'=>'status', 'value'=>$module->getStatus(), 'options'=>Module::getStatusMap(), 'label'=>'Estado')) )
        ->add( YuppForm2::check(array('name'=>'showContainer', 'on'=>$module->getShowContainer(), 'label'=>'Mostrar contenedor')) )
        ->add( YuppForm2::check(array('name'=>'showInAllPages', 'on'=>$module->getShowInAllPages(), 'label'=>'Mostrar en todas las paginas')) )
        ->add( YuppForm2::submit(array('name'=>'doit', 'label'=>'Guardar')) );
        //->add( YuppForm2::submit(array('action'=>'displayPage', 'label'=>'Volver') ) );
      YuppFormDisplay2::displayForm( $f );
    ?>
  </body>
</html>