<?php
/*
 * in: module
 * in: page
 * in: zone
 */
?>
<?php if ($mode=='edit' || $module->getShowContainer()) : ?>
  <!--
    el id de esta tag es lo que se usara para guardar el nuevo orden
    luego de hacer un movimiento por sortable, de un modulo a otra
    zona, o reordenando un modulo en la misma zona.
  -->
  <div class="moduleContainer <?php echo $module->getClass(); ?>" id="<?php echo $module->getClass(); ?>__<?php echo $module->getId(); ?>"><!-- TODO: discutir si tengo que mostrar el container o no. -->
    <div class="moduleTopBar">
      <?php echo $module->getTitle(); ?>
      <div class="moduleActions">
        <a href="<?php echo h('url', array('action'=>'editModule', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId())); ?>" alt="configuracion" class="edit_module"><?php echo h('img', array('app'=>'cms2', 'src'=>'settings.gif')); ?></a>
        <a href="<?php echo h('url', array('action'=>'removeModule', 'class'=>$module->getClass(), 'moduleId'=>$module->getId(), 'pageId'=>$page->getId(), 'zone'=>$zone->getName())); ?>" alt="remover" class="remove_module"><?php echo h('img', array('app'=>'cms2', 'src'=>'delete.gif')); ?></a>
      </div>
    </div>
    <div class="moduleContent">
      <?php
      Helpers::template(array('controller' => String::firstToLower($module->getClass()), // es el tipo de modulo, como news o html
                              'name' => 'displayModule',
                              'args' => array('module'=>$module, 'mode'=>$mode, 'page'=>$page, 'zone'=>$zone) // podria pasarle mas cosas como page, pageZone, etc.
                             ));
      ?>
    </div>
  </div>
<?php else : ?>
  <div class="moduleContent">
    <?php
    Helpers::template(array('controller' => String::firstToLower($module->getClass()), // es el tipo de modulo, como news o html
                            'name' => 'displayModule',
                            'args' => array('module'=>$module, 'mode'=>$mode, 'page'=>$page, 'zone'=>$zone) // podria pasarle mas cosas como page, pageZone, etc.
                           ));
    ?>
  </div>
<?php endif; ?>