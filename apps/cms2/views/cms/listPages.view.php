<?php

global $_base_dir;

$m = Model::getInstance();

$pages = $m->get('pages'); // Pagina a mostrar, tiene a que sitio pertenece

YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'main')); ?>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'backend')); ?>
    <style type="text/css">
      th {
         background: #fff url(<?php echo $_base_dir; ?>/images/shadow.jpg) bottom repeat-x;
      }
      #actions {
          background: #fff url(<?php echo $_base_dir; ?>/images/shadow.jpg) bottom repeat-x;
      }
      .order_asc {
         background-image: url(<?php echo $_base_dir; ?>/images/order_asc.gif);
      }
      .order_desc {
         background-image: url(<?php echo $_base_dir; ?>/images/order_desc.gif);
      }
    </style>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'superfish-1.4.8/js/superfish')); ?>
    <script type="text/javascript">
      $(document).ready( function() {
        
        // Menu desplegable para la topbar:
        $('#topbar > ul').superfish({ 
            delay:       600,                             // one second delay on mouseout 
            animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
            speed:       'fast',                          // faster animation speed 
            autoArrows:  false                            // disable generation of arrow mark-up 
        });
        
        $('a#delete_page').click( function(evt) {
            
          return confirm("\u00BFRelmente desea eliminar esta pagina?");
        });
      });
    </script>
  </head>
  <body>
    <?php CmsHelpers::topbar(); ?>
    <div id="body">
    
    <h1>Gestionar paginas</h1>
    
    <!-- feedback podria ser un modulo por defecto, ahi se imprimen los feedbasck y errores -->
    <!-- tambien, cada modulo podria tener su propio feedback para sus mensajes -->
    <?php if ($m->flash('message')) { ?>
      <div class="flash"><?php echo $m->flash('message'); ?></div>
    <?php } ?>

    <table>
      <tr>
        <th>Nombre</th>
        <th>Creada</th>
        <th>Ultima actualizacion</th>
        <th>Estado</th>
        <th>Borrado</th>
        <th>Acciones</th>
      </tr>
      <?php foreach ( $pages as $page ): ?>
        <tr>
          <td>
            <?php echo h('link', array('body'=>$page->getName(), 'action'=>'displayPage', 'pageId'=>$page->getId()) ); ?>
          </td>
          <td>
            <?php echo $page->getCreatedOn(); ?>
          </td>
          <td>
            <?php echo $page->getLastUpdate(); ?>
          </td>
          <td>
            <?php echo $page->getStatus(); ?>
          </td>
          <td>
            <?php echo (($page->getDeleted())?'Si':'No'); ?>
          </td>
          <td>
            <?php echo h('link', array('body'=>'Editar', 'action'=>'editPage', 'pageId'=>$page->getId(), 'backend'=>true, 'attrs'=>array('id'=>'edit_page')) ); ?>
            <?php
            if ($page->getDeleted())
              echo h('link', array('body'=>'Recuperar', 'action'=>'undeletePage', 'pageId'=>$page->getId(), 'backend'=>true, 'attrs'=>array('id'=>'undelete_page')) );
            else
              echo h('link', array('body'=>'Eliminar', 'action'=>'deletePage', 'pageId'=>$page->getId(), 'backend'=>true, 'attrs'=>array('id'=>'delete_page')) );
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    </div>
  </body>
</html>