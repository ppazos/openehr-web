<?php

$m = Model::getInstance();

YuppLoader::load('core.mvc.form', 'YuppForm2');

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
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    <script type="text/javascript">

      var isSubPage = <?php echo (($m->get('parentId')!==NULL)?'true':'false'); ?>;

      $(document).ready( function() {
        
        <?php if ($m->get('backend') == NULL) : ?>
        // Hago if parent, porque esta vista puede mostrarse sin ser modal.
        if (parent) parent.modal.modal('update');
        <?php endif; ?>
      });
      
      var callback = function(res, status, response) {
        
        // { status="ok", id="36", parentId="3"}
        //console.log(res); // quiero el id de la pagina recien creada.
        
        // TODO: si es subpage, actualizar la zona de subpaginas de la pagina actual.
        // Si no es subpagina, hago redirect a la nueva pagina.
        if (isSubPage)
        {
            // TODO: http://code.google.com/p/yupp-cms/issues/detail?id=1

            // Llamo al servidor para que me de el HTML con las subpaginas de la pagina padre, asi actualizo el area de subpaginas.
            $.ajax({
              url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'pageSubpages')); ?>',
              data: {
                pageId: res.parentId
              },
              success: function (html, status) {
                
                //console.log(html);
                
                
                // Actualizo las subpaginas
                $('.subpages', parent.document).html(html);
                

                // Cierro la ventana modal en la que me estoy mostrando
                parent.modal.modal('hide');
                
                
                // FIXME: en lugar de quedarme en la pagina actual y actualizar
                // las subpages porque no hago el redirect a la nueva pagina?
                
              },
              error: ajaxFormErrorHandler
            });
        }
        else
        {
          <?php if ($m->get('backend') != NULL) : ?>
          parent.modal.modal('hide');
          <?php endif; ?>
            
          // TODO: I18N para flash.message
          parent.location = '<?php echo h('url', array('action'=>'displayPage')); ?>?pageId='+res.id+'&flash.message=Nueva pagina creada con exito';
        }
      };
    </script>
  </head>
  <body>
    <h1>Crear p&aacute;gina</h1>
    <?php
      $f = new YuppForm2(array('action'=>'createPage', 'isAjax'=>true, 'ajaxCallback'=>'callback'));
      $f->add( YuppForm2::hidden(array('name'=>'parentId', 'value'=>(($m->get('parentId')!==NULL)?$m->get('parentId'):'') )) )
        ->add( YuppForm2::text(array('name'=>'name', 'value'=>$m->get('name'), 'label'=>'Nombre')) )
        ->add( YuppForm2::select(array('name'=>'status', 'value'=>$m->get('status'), 'options'=>Page::getStatusMap(), 'label'=>'Estado')) )
        ->add( YuppForm2::bigtext(array('name'=>'description', 'value'=>$m->get('description'), 'label'=>'Descripcion')) )
        ->add( YuppForm2::bigtext(array('name'=>'keywords', 'value'=>$m->get('keywords'), 'label'=>'Palabras clave')) )
        ->add( YuppForm2::submit(array('name'=>'doit', 'label'=>'Crear')) );
      YuppFormDisplay2::displayForm( $f );
    ?>
  </body>
</html>