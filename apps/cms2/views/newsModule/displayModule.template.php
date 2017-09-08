<div id="news_module_news_<?php echo $module->getId(); ?>">
<?php
echo h('template', array('name'=>'../newsModule/displayNews',
                         'args'=>array('module'=>$module, 'offset'=>(isset($offset)?$offset:0)))); ?>
</div>
<?php
  if ($module->getPaged()) :    
  // TODO: mostrar la pagina actual y la cantidad todal de paginas
?>
<div class="news_module_pager" style="text-align: center;">
  <!-- AJAX, se pone en edit.js -->
  <a href="<?php echo h('url', array(
                        'controller'=>'newsModule',
                        'action'=>'nextPage', // FIXME: es igual la accion, corregir nombre
                        'id'=>$module->getId(),
                        'pageId'=>$page->getId(),
                        'zone'=>$zone->getName(),
                        'offset'=>0,
                        'max'=>$module->getNewsPerPage(),
                        'count'=>$module->countNews() ));?>"
     class="news_pager news_pager_prev inactive"
     id="news_pager_prev_<?php echo $module->getId(); ?>"><?php echo h('img', array('app'=>'cms2', 'src'=>'left_arrow.gif')); ?></a>
  <!-- AJAX, se pone en edit.js -->
  <a href="<?php echo h('url', array(
                        'controller'=>'newsModule',
                        'action'=>'nextPage',
                        'id'=>$module->getId(),
                        'pageId'=>$page->getId(),
                        'zone'=>$zone->getName(),
                        'offset'=>$module->getNewsPerPage(),
                        'max'=>$module->getNewsPerPage(),
                        'count'=>$module->countNews() ));?>"
     class="news_pager news_pager_next <?php echo (($module->countNews() <= $module->getNewsPerPage())?'inactive':''); ?>"
     id="news_pager_next_<?php echo $module->getId(); ?>"><?php echo h('img', array('app'=>'cms2', 'src'=>'right_arrow.gif')); ?></a>
</div>
<?php
  endif;
?>

<?php if ($mode=='edit') : ?>
  <div class="customModuleActions">
    <a href="<?php echo h('url', array('controller'=>'newsModule', 'action'=>'edit', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId(), 'zone'=>$zone->getName())); ?>" alt="editar news" class="_news_open_modal"><?php echo h('img', array('app'=>'cms2', 'src'=>'edit.gif')); ?></a>
    <?php echo h('link', array('controller'=>'newsModule',
                               'action'=>'addNews',
                               'pageId'=>$page->getId(),
                               'zone'=>$zone->getName(),
                               'id'=>$module->getId(),
                               'body'=>h('img', array('app'=>'cms2', 'src'=>'plus_icon.gif')),
                               'attrs'=>array('class'=>'_news_open_modal')
                              )); ?>
  </div>
<?php endif; ?>