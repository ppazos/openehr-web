<?php

$_action = (($mode=='edit')?'displayPage':'displayPageRO');

if (!function_exists('displayMenuRecursive')) // necesario por si se ponen 2 modulos menu en la misma pagina, para que no diga que la funcion se declaro 2 veces.
{
   function displayMenuRecursive($page, $module, $_action, $level)
   {
       echo '<li class="menu_item">';
       
       // Link con url no amigable
       //echo h('link', array('controller'=>'cms', 'action'=>$_action, 'pageId'=>$page->getId(), 'body'=>$page->getName()));
       
       // Url amigable
       if ($_action == 'displayPageRO')
       {
          /**
           * Se ponen estos datos, luego AppMapping corrige a la path fisica PageController.display(nname):
           * controller = display
           * action = page.normalizedName
           */
          echo h('link', array('controller'=>'display', 'action'=>$page->getNormalizedName(), 'body'=>$page->getName()));
       }
       else
          echo h('link', array('controller'=>'cms', 'action'=>$_action, 'pageId'=>$page->getId(), 'body'=>$page->getName()));
      
       
       $subpages = $page->getSubpages();
       if (count($subpages)>0)
       {
          if ($module->getLevel() == NULL || $module->getLevel() > $level) // Si es NULL, muestra todos los niveles
          {
             //echo h('img', array('app'=>'cms2', 'src'=>'plus_icon.gif'));
             echo '<ul class="submenu">';
             foreach ($subpages as $subpage)
             {
                // Como se pueden eliminar paginas y mantenerse en las subpages, debo preguntar si no esta eliminada para mostrarla en el menu.
                if (!$subpage->getDeleted())
                   displayMenuRecursive($subpage, $module, $_action, $level+1);
             }
             echo '</ul>';
          }
       }
       echo '</li>';
       
       // Deja breaks solo si es vertical
       //if ($module->getVertical()) echo '<br/>';
   }
}

echo '<ul class="menu '. (($module->getVertical())?'vertical':'horizontal').'">';

$items = $module->getItems();
foreach ($items as $item)
{
   // Link
   if ($item->isLink())
   {
      echo '<li class="menu_item">';
      echo '<a href="'.$item->getUrl().'">'.$item->getLabel().'</a>';
      echo '</li>';
      continue;
   }
   
   // Page
   $p = $item->getPage();
   if (!$p->getDeleted())
   {
      echo '<li class="menu_item '. (($page->getId() == $p->getId())?'active':'') .'">';
      
      // Url no amigable
      //echo h('link', array('controller'=>'cms', 'action'=>$_action, 'pageId'=>$p->getId(), 'body'=>$p->getName()));
      
      // Url amigable
      if ($_action == 'displayPageRO')
      {
         /**
          * Se ponen estos datos, luego AppMapping corrige a la path fisica PageController.display(nname):
          * controller = display
          * action = page.normalizedName
          */
         echo h('link', array('controller'=>'display', 'action'=>$p->getNormalizedName(), 'body'=>$p->getName()));
      }
      else
        echo h('link', array('controller'=>'cms', 'action'=>$_action, 'pageId'=>$p->getId(), 'body'=>$p->getName()));
      
      
      $subpages = $p->getSubpages();
      if (count($subpages)>0)
      {
         if ($module->getLevel() == NULL || $module->getLevel() > 0) // Si es NULL, muestra todos los niveles
         {
            //echo h('img', array('app'=>'cms2', 'src'=>'plus_icon.gif'));
            echo '<ul class="submenu">';
            foreach ($subpages as $subpage)
            {
               // Como se pueden eliminar paginas y mantenerse en las subpages,
               // debo preguntar si no esta eliminada para mostrarla en el menu.
               if (!$subpage->getDeleted())
                  displayMenuRecursive($subpage, $module, $_action, 1);
            }
            echo '</ul>';
         }
      }
      
      echo '</li>';
   }
   // Deja breaks solo si es vertical
   //if ($module->getVertical()) echo '<br/>';
}
echo '</ul>';
?>
<?php if ($mode=='edit') : ?>
  <div class="customModuleActions">
    <a href="<?php echo h('url', array('controller'=>'menuModule', 'action'=>'edit', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId(), 'zone'=>$zone->getName())); ?>" alt="editar menu" class="edit_menu"><?php echo h('img', array('app'=>'cms2', 'src'=>'edit.gif')); ?></a>
  </div>
<?php endif; ?>