<?php

YuppLoader::load('core.mvc.form', 'YuppForm2');

$m = Model::getInstance();
$page = $m->get('page');

$possible_parents = $m->get('possible_parents');
$parentId = (( is_null($page->getParent()) ) ? 0 : $page->getParent()->getId() );

$possible_parents_map = array(''=>'');
foreach ($possible_parents as $pg) { $possible_parents_map[ $pg->getId() ] = $pg->getName(); }

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'backend')); ?>
    <style type="text/css">
      body {
        width: 185px;
        height: 275px;
        background-color: transparent;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <script type="text/javascript">

      // El evento de actualizar la modal se ejecuta en parent.
      
      // Callback del submit del form.
      var ep_callback = function(res, status, response) {
        
        // { status="ok", id="36" }
        //console.log(res); // quiero el id de la pagina recien creada.

        <?php if ($m->get('backend') != NULL) : ?>
          parent.location = '<?php echo h('url', array('action'=>'listPages')); ?>?flash.message=Pagina actualizada con exito';
        <?php else : ?>
          // Cierro la ventana modal en la que me estoy mostrando
          parent.modal.modal('hide');
        
          // Tengo que actualizar toda la pagina porque pudo cambiar el nombre y descripcion. Es mas facil actualizar todo que cambiar eso por javascript.
          // TODO: I18N para flash.message
          parent.location = '<?php echo h('url', array('action'=>'displayPage')); ?>?pageId='+res.id+'&flash.message=Pagina actualizada con exito';
        <?php endif; ?>
      };
    </script>
  </head>
  <body>
    <h1>Editar p&aacute;gina</h1>
    <?php
      $f = new YuppForm2(array('action'=>'editPage', 'isAjax'=>true, 'ajaxCallback'=>'ep_callback'));
      $f->add( YuppForm2::hidden(array('name'=>'pageId', 'value'=>$page->getId())) )
        ->add( YuppForm2::text(array('name'=>'name', 'value'=>$page->getName(), 'label'=>'Nombre')) )
        ->add( YuppForm2::select(array('name'=>'status', 'value'=>$page->getStatus(), 'options'=>Page::getStatusMap(), 'label'=>'Estado')) )
        ->add( YuppForm2::select(array('name'=>'parentId', 'value'=>$parentId, 'options'=>$possible_parents_map, 'label'=>'Padre')) )
        ->add( YuppForm2::hidden(array('name'=>'prevParentId', 'value'=>$parentId)) )
        ->add( YuppForm2::bigtext(array('name'=>'description', 'value'=>$page->getDescription(), 'label'=>'Descripcion')) )
        ->add( YuppForm2::bigtext(array('name'=>'keywords', 'value'=>$page->getKeywords(), 'label'=>'Palabras clave')) )
        ->add( YuppForm2::submit(array('name'=>'doit', 'label'=>'Actualizar')) );
      YuppFormDisplay2::displayForm( $f );
    ?>
  </body>
</html>