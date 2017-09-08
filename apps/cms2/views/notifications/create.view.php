<?php

$m = Model::getInstance();

$module = $m->get('module');

YuppLoader::load('core.mvc', 'DisplayHelper');
YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');

// El create se usa tambien como edit
$content = '';
$name = '';
if ( ($nl = $m->get('newsletter')) != NULL )
{
   $content = $nl->getContent();
   $name = $nl->getName();
}

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'main')); ?>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'backend')); ?>
    <style type="text/css">
      input[type=text] {
        width: 300px;
      }
      .actions {
        text-align: right;
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
      <h1>Crear news letter</h1>
      <?php if ($m->flash('message')) { ?>
        <div class="flash"><?php echo $m->flash('message'); ?></div>
      <?php } ?>
      <form method="post" action="<?php echo h('url', array('action'=>(($nl==NULL)?'create':'edit'))); ?>">
        <input type="hidden" name="id" value="<?php echo $m->get('id'); ?>" />
        Nombre: <input type="text" name="name" value="<?php echo $name; ?>" />
        <?php DisplayHelper::html( 'content', $content, array('width'=>'100%', 'height'=>490) ); ?>
        <div class="actions">
          <input type="submit" name="doit" value="Guardar" />
        </div>
      </form>
    </div>
  </body>
</html>