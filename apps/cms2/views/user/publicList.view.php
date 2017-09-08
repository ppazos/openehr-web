<?php

$m = Model::getInstance();

$users = $m->get('users');

global $_base_dir;

?>
<html>
  <head>
    <style type="text/css">
      body {
         font-family: arial, verdana, tahoma;
         font-size: 12px;
         padding: 0px;
         margin: 0px;

         width: 700px;
         height: 460px;
      }
      #menu {
        background-color: #9BE;
        padding: 10px;
        margin-bottom: 10px;
        
        border-bottom-right-radius: 5px;
        -moz-border-radius-bottomright: 5px;
        border-bottom-left-radius: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-top-right-radius: 5px;
        -moz-border-radius-topright: 5px;
        border-top-left-radius: 5px;
        -moz-border-radius-topleft: 5px;
      }
      .pager {
        padding: 10px 10px 0 10px;
        text-align: right;
      }
      form {
        margin: 0;
      }
     
      /** ==== **/
      #pesonal_card {
        border: 1px solid #000;
        width: 48%;
        background-color: #efefef;
        
        display: inline-block;
        
        /* Bordes redondeados */
        border-bottom-right-radius: 5px;
        -moz-border-radius-bottomright: 5px;
        border-bottom-left-radius: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-top-right-radius: 5px;
        -moz-border-radius-topright: 5px;
        border-top-left-radius: 5px;
        -moz-border-radius-topleft: 5px;
      }
      #personal_info {
        width: 100%;
        text-align: left;
      }
      #name {
        font-size: 1.6em;
      }
      #avatar, #personal_info {
        display: table-cell;
        vertical-align: top;
        padding: 5px;
      }
     </style>
     <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
     <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
     <script type="text/javascript">
      $(document).ready( function() {
        
        // Notifica para que el parent actualice el tamanio del iframe
        parent.modal.modal('update');
      });
     </script>
   </head>
   <body>
      <?php if ($m->flash('message')) { ?>
        <div class="flash"><?php echo $m->flash('message'); ?></div><br/>
      <?php } ?>
      <div id="menu">
        <form action="<?php echo h('url', array('action'=>'publicList')) ?>" method="post">
          <input type="text" name="q" value="<?php echo $m->get('q'); ?>" />
          <input type="submit" name="doit" value="Buscar" />
        </form>
      </div>

      <div align="center">
      <?php foreach ( $users as $user ): ?>
        <div id="pesonal_card">
          <div id="avatar">
            <?php // Muestra el gravatar del usuario
              YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
              CmsHelpers::gravatar(78, $user);
            ?>
          </div>
          <div id="personal_info">
            <div id="name">
              <?php echo h('link', array('action'=>'publicProfile', 'id'=>$user->getId(), 'body'=>$user->getName()));  ?>
            </div>
            <?php if ( $user->getCompany() ) : ?>
              <div id="institution"><?php echo $user->getPosition(); ?> @ <?php echo $user->getCompany(); ?></div>
            <?php endif; ?>
            <div id="location">
              <?php
                YuppLoader::load('apps.cms2.helpers', 'CountryHelpers');
                echo CountryHelpers::getCountryName( $user->getCountry() );
              ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
      
      <div class="pager">
      <?php echo h('pager', array('offset'=>$m->get('offset'), 'max'=>$m->get('max'), 'count'=>$m->get('count'))); ?>
      </div>
   </body>
</html>