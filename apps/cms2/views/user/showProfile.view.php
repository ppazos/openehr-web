<?php

$m = Model::getInstance();

$user = $m->get('user');

global $_base_dir;

?>
<html>
  <head>
    <style type="text/css">
      body {
        width: 300px;
        height: 225px;
      }
      table {
        width: 100%;
        margin-top: 10px;
      }
      td, th {
        width: 50%;
        text-align: left;
      }
      form {
        margin-top: 10px;
        text-align: right;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <script type="text/javascript">
      $(document).ready( function() {
        
      });
    </script>
  </head>
  <body>
    <h1>Perfil de usuario</h1>
    
    <?php if ($m->flash('message')) : ?>
      <div class="flash"><?php echo $m->flash('message'); ?></div>
    <?php endif; ?>
                               
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
        <td><?php echo h('link', array('controller'=>'user', 'action'=>'changePassword', 'body'=>'Cambiar')); ?></td>
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
        <th>Instituci&oacute;n</th>
        <td><?php echo $user->getCompany(); ?></td>
      </tr>
      <tr>
        <th>Cargo</th>
        <td><?php echo $user->getPosition(); ?></td>
      </tr>
    </table>
    
    <form action="<?php echo h('url', array('action'=>'editProfile')) ?>" method="post">
      <input type="submit" value="Modificar perfil" />
    </form>
    
  </body>
</html>