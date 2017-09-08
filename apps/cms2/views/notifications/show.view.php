<?php

$m = Model::getInstance();

$nl = $m->get('newsletter');

global $_base_dir;

YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'main')); ?>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'backend')); ?>
    <style type="text/css">
      table {
        border: 1px solid #000;
        /* spacing: 0px; */
        border-collapse: separate;
        border-spacing: 0px;
      }
      td {
        border-bottom: 1px solid #ddd;
        padding: 5px;
        background-color: #f5f5f5;
        width: 400px;
      }
      
      form {
        width: 220px;
      }
      label {
        display: block;
      }
      .actions {
        margin: 5px;
        text-align: right;
        background: #fff url(<?php echo $_base_dir; ?>/images/shadow.jpg) bottom repeat-x;
        border: 1px solid #ccc;
        border-style: solid none solid none;
        padding: 5px;
      }

      th {
      }
      .details th {
        vertical-align: top;
        text-align: right;
        width: 130px;
        padding-right: 10px;
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
      <h1>Detalle del boletin</h1>
    
      <?php if ($m->flash('message')) : ?>
        <div class="flash"><?php echo $m->flash('message'); ?></div>
      <?php endif; ?>
                                         
      <table class="details">
      <tr>
        <th>Nombre</th>
        <td><?php echo $nl->getName(); ?></td>
      </tr>
      <tr>
        <th>Creada</th>
        <td><?php echo $nl->getCreatedOn(); ?></td>
      </tr>
      <tr>
        <th>Contenido</th>
        <td><?php echo $nl->getContent(); ?></td>
      </tr>
      </table>
      
      <h2>Env&iacute;o</h2>
      <form method="post" action="<?php echo h('url', array('action'=>'send')); ?>">
        <input type="hidden" name="id" value="<?php echo $nl->getId(); ?>" />
        <label>
          <input type="radio" name="usertype" value="" checked="true" />
          Todos
        </label>
        <label>
          <input type="radio" name="usertype" value="pending" />
          Pendientes
        </label>
        <label>
          <input type="radio" name="usertype" value="user" />
          Usuarios
        </label>
        <label>
          <input type="radio" name="usertype" value="content_editor" />
          Editores de contenido
        </label>
        <label>
          <input type="radio" name="usertype" value="editor" />
          Editores
        </label>
        <label>
          <input type="radio" name="usertype" value="admin" />
          Administradores
        </label>
        <div class="actions">
          <input type="submit" name="Enviar" />
        </div>
      </form>
      
      <h2>Envios realizados (<?php echo count($m->get('logs')); ?>)</h2>
      <table>
        <tr>
          <th>Enviado a</th>
          <th>Enviado</th>
          <th>Intentos</th>
          <th>Estado</th>
          <th>Comentario</th>
          <th>Acciones</th>
        </tr>
        <?php foreach ($m->get('logs') as $log) : ?>
          <tr>
            <td><?php echo $log->getTo()->getName(); ?> (<?php echo $log->getTo()->getEmail(); ?>)</td>
            <td><?php echo $log->getSentOn(); ?></td>
            <td><?php echo $log->getTries(); ?></td>
            <td><?php echo (($log->getStatus() == NewsLetterSendLog::STATUS_OK)?'ok':'error'); ?></td>
            <td><?php echo $log->getComment(); ?></td>
            <td><?php echo h('link', array('action'=>'dellog', 'id'=>$log->getId(), 'body'=>'eliminar')); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      
    </div>
  </body>
</html>