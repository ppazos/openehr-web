<?php echo $module->getContent(); ?>
<?php if ($mode=='edit') : ?>
  <div class="customModuleActions">
    <a href="<?php echo h('url', array('controller'=>'htmlModule', 'action'=>'edit', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId(), 'zone'=>$zone->getName())); ?>" alt="editar html" class="edit_html"><?php echo h('img', array('app'=>'cms2', 'src'=>'edit.gif')); ?></a>
    <?php /*echo h('ajax_link', array('controller'=>'htmlModule',
                                  'action'=>'edit',
                                  'class'=>$module->getClass(),
                                  'id'=>$module->getId(),
                                  'pageId'=>$page->getId(), // no necesita pageId para el edit del html
                                  'body'=>h('img', array('app'=>'cms2', 'src'=>'edit.gif')),
                                  'after'  => 'after_function',
                                  'before' => 'before_function' ));*/ ?>
  
  </div>
<?php endif; ?>