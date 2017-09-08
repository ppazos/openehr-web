<?php

global $_base_dir;

$m = Model::getInstance();

$modules = $m->get('modules');

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
      });
    </script>
  </head>
  <body>
    <?php CmsHelpers::topbar(); ?>
    
    <div id="body">
    
    <h1>Gestionar modulos</h1>
  
    <!-- feedback podria ser un modulo por defecto, ahi se imprimen los feedbasck y errores -->
    <!-- tambien, cada modulo podria tener su propio feedback para sus mensajes -->
    <?php if ($m->flash('message')) { ?>
      <div class="flash"><?php echo $m->flash('message'); ?></div>
    <?php } ?>

    <table>
      <tr>
        <th>Id</th>
        <th>Titulo</th>
        <th>Creada</th>
        <th>Mostrar contenedor</th>
        <th>Mostrar en todas las paginas</th>
        <th>Estado</th>
        <th>Borrado</th>
        <th>Tipo</th>
        <th>Acciones</th>
      </tr>
      <?php foreach ( $modules as $module ): ?>
        <tr>
          <td><?php echo $module->getId(); ?></td>
          <td><?php echo $module->getTitle(); ?></td>
          <td><?php echo $module->getCreatedOn(); ?></td>
          <td><?php echo (($module->getShowContainer())?'Si':'No'); ?></td>
          <td><?php echo (($module->getShowInAllPages())?'Si':'No'); ?></td>
          <td><?php echo $module->getStatus(); ?></td>
          <td><?php echo (($module->getDeleted())?'Si':'No'); ?></td>
          <td><?php echo $module->getClass(); ?></td>
          <td>
            [Activar/Desactivar] [Eliminar]
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    </div>
  </body>
</html>