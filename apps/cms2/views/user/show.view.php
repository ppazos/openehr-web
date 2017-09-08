<?php

$m = Model::getInstance();

$user = $m->get('user');

global $_base_dir;

?>
<html>
  <head>
    <style type="text/css">
      body {
        font-family: arial, verdana, tahoma;
        font-size: 12px;
        background-color: #efefef;
      }
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
      #actions {
        background: #fff url(<?php echo $_base_dir; ?>/images/shadow.jpg) bottom repeat-x;
        border: 1px solid #ccc;
        border-style: solid none solid none;
        padding: 7px 12px;
      }
      #actions a {
        padding-right: 5px;
        padding-left: 5px;
      }
      th {
        vertical-align: top;
        text-align: right;
        width: 130px;
        padding-right: 10px;
      }
      
      /* Global, es el menu para editar lo que se esta viendo */
      #topbar {
        height: 30px;
        background-color: #fc3;
        overflow: visible;
        position: relative;
        z-index: 999;
        padding: 5px;
        margin: 10px;
      }
      #topbar ul {
        margin: 0px;
        padding: 0px;
        list-style-type: none; /* para hacer el menu horizontal */
      }
      #topbar ul li {
        display: inline;
        float: left;
        padding: 5px;
        margin-right: 5px;
      }
      #topbar ul li:hover {
        background-color: #cf3;
      }
     </style>
     <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
     <script type="text/javascript">
      $(document).ready( function() {
        
        // Menu desplegable para la topbar:
        $('#delete_btn').click( function() {
          return confirm("\u00BFRelmente desea eliminar a este usuario?");
        });
      });
     </script>
  </head>
  <body>
    <h1>Detalle de usuario</h1>
    
    <?php if ($m->flash('message')) : ?>
      <div class="flash"><?php echo $m->flash('message'); ?></div>
    <?php endif; ?>
      
    <div id="topbar">
      <ul>
        <li><?php echo h('link', array('action'=>'list',   'body'=>'Usuarios') ); ?></li>
        <li><?php echo h('link', array('action'=>'edit',   'id'=>$user->getId(), 'body'=>'Editar') ); ?></li>
        <li><?php echo h('link', array('action'=>'delete', 'id'=>$user->getId(), 'body'=>'Eliminar', 'attrs'=>array('id'=>'delete_btn')) ); ?></li>
        <?php if ( $user->getUsertype()===User::TYPE_PENDING ): ?>
          <li><?php echo h('link', array('action'=>'approve', 'id'=>$user->getId(), 'body'=>'Aprobar usuario') ); ?></li>
        <?php endif; ?>
      </ul>
    </div>
                                         
    <table>
      <tr>
        <th>Nombre</th>
        <td><?php echo $user->getName(); ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?php echo $user->getEmail(); ?></td>
      </tr>
      <tr>
        <th>Usuario</th>
        <td><?php echo $user->getUsername(); ?></td>
      </tr>
      <tr>
        <th>Clave</th>
        <td><?php echo $user->getPassword(); ?></td>
      </tr>
      <tr>
        <th>Fecha de nacimiento</th>
        <td><?php echo $user->getBirthdate(); ?></td>
      </tr>
      <tr>
        <th>Tipo de usuario</th>
        <td><?php echo $user->getUsertype(); ?></td>
      </tr>
      <tr>
        <th>Pa&iacute;s</th>
        <td>
          <?php
            YuppLoader::load('apps.cms2.helpers', 'CountryHelpers');
            echo CountryHelpers::getCountryName( $user->getCountry() );
          ?>
        </td>
      </tr>
      <tr>
        <th>Instituci&oacute;n</th>
        <td><?php echo $user->getCompany(); ?></td>
      </tr>
      <tr>
        <th>Cargo</th>
        <td><?php echo $user->getPosition(); ?></td>
      </tr>
    </table>
  </body>
</html>