<?php

YuppLoader::load('cms2.model.menuModule', 'MenuItem');
YuppLoader::load('cms2.model.menuModule', 'MenuModule');
YuppLoader::load('cms2.model.cms', 'Page');

class MenuModuleController extends YuppController {

   /**
    * Agrega una pagina al menu.
    * Se llamara desde ajax en el edit del menu.
    */
   public function addPageAction()
   {
      
   }

   /**
    * Quita una pagina del menu.
    * Se llamara desde ajax en el edit del menu.
    */
   public function removePageAction()
   {
      
   }
   
   /**
    * Edita la informacion del menu.
    * Desde aqui se podran agregar o quitar paginas del menu.
    */
   public function editAction()
   {
      $module = MenuModule::get($this->params['id']);
      
      if (isset($this->params['doit']))
      {
         /*
         [items] => Array
            (
                [0] => 1 // pageId
                [1] => 2 // pageId
                [2] => http://www.google.com // url
                [3] => http://www.openehr.org.es // url
            )

         [labels] => Array
            (
                [0] => default
                [1] => pagina2
                [2] => Google // label
                [3] => openEHR en español // label
            )
         */
         //print_r($this->params);
         //return;
        
        
         // Saca todos los items del menu.
         foreach ($module->getItems() as $item)
         {
            $module->removeFromItems( $item );
            $item->delete(); // eliminacion fisica
         }
          
         // Pone todas las paginas seleccionadas en la edicion
         foreach ($this->params['items'] as $i => $pageIdOrUrl)
         {
            if (is_numeric($pageIdOrUrl))
               $module->addPageIdItem( $pageIdOrUrl ); // Crea el MenuItem con el pageId
            else
               $module->addLinkItem( $this->params['labels'][$i], $pageIdOrUrl ); // Crea el MenuItem con el label y url
         }
         
         $module->setVertical( isset($this->params['vertical']) );
         $module->setLevel( ((is_numeric($this->params['level']))?$this->params['level']:NULL) ); // Es un numero o un string vacio, y quiero guardar el numero o null
         
         if (!$module->save())
         {
            //print_r($module->getErrors());
            // TODO: devolver los errores en JSON
            header('Content-type: application/json');
            return $this->renderString('{"status":"error", "moduleId":"'.$module->getId().'"}');
         }
         
         // no hacer redirect si la accion se ejecuta por ajax
         
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "moduleId":"'.$module->getId().'"}');
      }
      
      // Obtener paginas que no estan en el menu
      // Ciudado al armar la consulta, la condicion AND es valida solo si el menu tiene alguna pagina.
      $hayPagina = false;
      $condition = Condition::_AND();
      foreach ($module->getItems() as $item)
      {
         if (!$item->isLink())
         {
            $condition->add( Condition::NEQ(Page::TABLE, 'id', $item->getPageId()) );
            $hayPagina = true;
         }
      }

      if ($hayPagina)
         $pagesNotInMenu = Page::findBy( $condition, new ArrayObject() );
      else
         $pagesNotInMenu = Page::listAll( new ArrayObject() ); // Todas las paginas NO estan en el menu
      
      return array('module'=>$module, 'pagesNotInMenu'=>$pagesNotInMenu);
   }
}

?>