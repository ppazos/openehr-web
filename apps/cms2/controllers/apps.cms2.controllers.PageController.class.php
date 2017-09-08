<?php

YuppLoader::load('cms2.model.cms', 'Page');
YuppLoader::load('cms2.model.cms', 'Layout');

class PageController extends YuppController {

   public function indexAction()
   {
      /**
       * Por defecto deberia mostrar la pagina por defecto del sitio por defecto.
       */
      Logger::getInstance()->pm_log("indexAction ===== == ===== == ===== == ===== ==");
      return $this->redirect(array('action'=>'displayPageRO', 'params'=>array('pageId'=>1)));
   }
   
   /**
    * Listado de paginas para gestionar.
    */
   public function listAction()
   {
      // TODO: paginacion
      
      // Necesito listar todas, las eliminadas y las no eliminadas.
      // Pero solo las de nivel 1 porque desde ahi se construye el arbol de paginas y subpaginas.
      $cond = Condition::_AND()
                ->add (
                  Condition::_OR()
                    ->add( Condition::EQ(Page::TABLE, 'deleted', true) )
                    ->add( Condition::EQ(Page::TABLE, 'deleted', false) )
                )
                ->add( Condition::EQ(Page::TABLE, 'parent_id', NULL) ); // Solo las que no tienen parent.
            
      $pages = Page::findBy( $cond, $this->params );
      
      return array('pages'=>$pages);
   }
   
   /**
    * Desde la gestion de paginas, se llama cuando se cambia la estructura de pagians y subpaginas.
    * @param parentId identificador de pagina padre de las paginas identificadas en pageIds. Si es null, las paginas no tienen padre.
    * @param pageIds subpaginas de parendId en el orden que deben guardarse.
    */
   public function updatePageStructAction()
   {
      // obtener la pagina con id parentId, si es vacio, no habria parent
      // si parentId != null
      //   sacar todas las subpaginas (para poder agregar las pageIds en el orden correcto)
      // recorrer todos los pageIds
      //   pedir cada pagina
      //   si la pagina tiene su parent distinto a parentId (puede no tener parent)
      //     pido el parent actual, saco la pagina de las subpaginas de ese parent
      //   seteo el parent de la pagina en parentId
      //   si parentId != null
      //     agrego la pagina como subpagina de parentId
      
      //Logger::getInstance()->on();

      // Si hay nuevo parent
      if (!empty($this->params['parentId']))
      {
         $parentId = $this->params['parentId'];
         $parent = Page::get($parentId);
         $parent->removeAllFromSubpages();
         
         foreach ($this->params['pageIds'] as $id)
         {
            $page = Page::get($id);
            if ($page->hasParent())
            {
               // Si el parent anterior no es el actual, hay que desasociar el parent de la subpagina.
               if ($page->getParent()->getId() != $parentId)
               {
                  $page->getParent()->removeFromSubpages($page); // removeFrom ya guarda
               }
            }
            
            echo $page->getId() . '<br/>';
            
            $parent->addToSubpages($page);
            $page->setParent($parent);
            
            if (!$page->save()) print_r($page->getErrors());
         }
         
         if (!$parent->save()) print_r($parent->getErrors());
      }
      else // Sin parent
      {
         // WARNING: en este caso, el orden de las paginas no es respetado ya que no
         // hay relacion que mantenga el orden como si pasa con las subpaginas.
         foreach ($this->params['pageIds'] as $id)
         {
            $page = Page::get($id);
            if ($page->hasParent())
            {
               $page->getParent()->removeFromSubpages($page); // removeFrom ya guarda
            }
            
            $page->setParent(NULL);
            
            if (!$page->save()) print_r($page->getErrors());
         }
      }
      
      //header('Content-type: application/json');
      return $this->renderString('{"status":"ok"}');
   }
   
   /**
    * Previa CmsController.displayPageRO
    * Se muestra la pagina en base a su nombre normalizado
    */
   public function displayAction()
   {
      if (!isset($this->params['nname']))
      {
         $this->flash['message'] = 'No se encuentra la pagina solicitada';
         $page = Page::get(1);
      }
      else
      {
         $pages = Page::findBy( Condition::EQ(Page::TABLE, 'normalizedName', $this->params['nname']), $this->params );
         
         if (count($pages) == 1) $page = $pages[0];
         else
         {
            $this->flash['message'] = 'No se encuentra la pagina solicitada';
            $page = Page::get(1);
         }
      }

      $this->params['page'] = $page;
      //return $this->render('../cms/displayPageRO');// no funciona en linux
      return $this->render('../cms/displayPageRO');// no funciona en linux
   }
}

?>