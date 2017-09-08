<?php

$m = Model::getInstance();

$module = $m->get('module');

YuppLoader::load('core.mvc', 'DisplayHelper');

?>
<html>
  <head>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <style>
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      /* Esta tiene contenido variable en alto, no se puede setear estaticamente con css */
      body {
        width: 300px;
      }
      #menu ul {
          padding: 10px;
          border: 1px solid #36c;
          width: 220px;
      }
      #menu ul li {
          padding: 10px;
          border: 1px solid #36c;
          background-color: #9cf;
          width: 200px;
          /*height: 30px;*/
          list-style: none;
      }
      .removeItem {
          text-align: center;
          padding: 2px;
          width: 60px;
          float: right;
          border: 1px solid #000080;
          background-color: #9999ff;
          cursor: pointer;
      }
    </style>
    
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-ui-1.8.17.sortable.min')); ?><!-- para el sortable del menu -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    
    <script type="text/javascript">

      // TODO
      
    </script>
  </head>
  <body>
    <form id="editForm" method="post" action="<?php echo h('url', array('action'=>'edit')); ?>">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />
      
      TODO
      
      <input type="submit" name="doit" value="Guardar" />
    </form>
  </body>
</html>