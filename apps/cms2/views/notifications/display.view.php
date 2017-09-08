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
    <?php /* echo h('css', array('app'=>'cms2', 'name'=>'backend')); */ ?>
    <style type="text/css">
      body {
        padding-top: 10px;
      }
      body, table, p {
        font-size: 12px;
      }
      h1 {
        font-size: 1.7em;
      }
      h2 {
        font-size: 1.5em;
      }
      h3 {
        font-size: 1.3em;
      }
    </style>
  </head>
  <body>
    <div id="body">
      <div class="flash"><?php echo $nl->getContent(); ?></div>
    </div>
  </body>
</html>