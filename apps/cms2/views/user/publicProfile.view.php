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
         padding: 0px;
         margin: 0px;
         
         width: 700px;
         height: 500px;
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
      
      /** ==== **/
      #pesonal_card {
        border: 1px solid #000;
        width: 400px;
        background-color: #efefef;
        
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
        /*width: 100%;*/
      }
      #name {
        font-size: 1.6em;
      }
      #avatar, #personal_info {
        display: table-cell;
        vertical-align: top;
        /*border: 1px solid red;*/
        padding: 5px;
      }
      #personal_card_footer {
        display: table;
        /*border: 1px solid blue;*/
        width: 100%;
      }
      .footer_link {
        display: table-cell;
        /*border: 1px solid gold;*/
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
    <?php if ($m->flash('message')) : ?>
      <div class="flash"><?php echo $m->flash('message'); ?></div><br/>
    <?php endif; ?>
    <div id="menu">
      <?php echo h('link', array('action'=>'publicList', 'body'=>'Miembros')); ?>
    </div>

    <div id="pesonal_card">
      <div id="avatar">
        <?php // Muestra el gravatar del usuario
          YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
          CmsHelpers::gravatar(100, $user);
        ?>
      </div>
      <div id="personal_info">
        <div id="name"><?php echo $user->getName(); ?></div>
        <div id="email"><?php echo $user->getEmail(); ?></div>
        <div id="institution"><?php echo $user->getPosition(); ?> @ <?php echo $user->getCompany(); ?></div>
        <div id="location">
          <?php
            YuppLoader::load('apps.cms2.helpers', 'CountryHelpers');
            echo CountryHelpers::getCountryName( $user->getCountry() );
          ?>
        </div>
      </div>
      <div id="personal_card_footer">
        <?php if ($f = $user->getFacebook()) : ?>
          <div class="footer_link">
            <a href="https://<?php echo $f; ?>" target="_blank">facebook</a>
          </div>
        <?php endif; ?>
        <?php if ($l = $user->getLinkedin()) : ?>
          <div class="footer_link">
            <a href="https://<?php echo $l; ?>" target="_blank">linkedin</a>
          </div>
        <?php endif; ?>
        <?php if ($t = $user->getTwitter()) : ?>
          <div class="footer_link">
            <a href="https://twitter.com/#!/<?php echo $t; ?>" target="_blank">twitter</a>
          </div>
        <?php endif; ?>
        <?php if ($g = $user->getGoogleplus()) : ?>
          <div class="footer_link">
            <a href="https://<?php echo $g; ?>" target="_blank">google+</a>
          </div>
        <?php endif; ?>
      </div>
    </div>

<?php /*echo $user->getUsername(); ?>
<?php echo $user->getLastAccess(); */ ?>

  </body>
</html>