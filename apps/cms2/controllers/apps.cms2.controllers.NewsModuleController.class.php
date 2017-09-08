<?php

YuppLoader::load('cms2.model.newsModule', 'NewsModule');
YuppLoader::load('cms2.model.newsModule', 'News');

class NewsModuleController extends YuppController {

   /**
    * in: id
    * in: pageId
    */
   public function editAction()
   {
      $module = NewsModule::get($this->params['id']);
      
      if (isset($this->params['doit']))
      {
         $module->setProperties($this->params);
         if (isset($this->params['paged'])) $module->setPaged(true);
         else $module->setPaged(false);
         
         if (!$module->save())
         {
            //print_r($module->getErrors());
            header('Content-type: application/json');
            return $this->renderString('{"status":"ok", "msg":"xxxx"}');
         }
      }
      
      return array('module'=>$module);
   }
   
   public function addNewsAction()
   {
      $module = NewsModule::get($this->params['id']);
      
      if (isset($this->params['doit']))
      {
         header('Content-type: application/json');
         
         $news = new News($this->params);
         if (!$news->save())
         {
            // FIXME: deberia indicar que errores ocurrieron
            return $this->renderString('{"status":"error", "msg":"Ha ocurrido un error, intente de nuevo"}');
         }
         
         $module->addToNews($news);
         if (!$module->save())
         {
            // FIXME: deberia hacer rollback del save anterior
            // FIXME: deberia indicar que errores ocurrieron
            return $this->renderString('{"status":"error", "msg":"Ha ocurrido un error, intente de nuevo"}');
         }
         
         return $this->renderString('{"status":"ok", "msg":"Noticia creada con éxito"}');
      }
      
      return array('module'=>$module);
   }
   
   
   // TODO: funciones de paginacion AJAX
   // Es igual el de prev, corregir nombre.
   public function nextPageAction()
   {
      // Mismo codigo que CmsController.moduleContent
      
      YuppLoader::load('cms2.model.cms', 'Module');
      YuppLoader::load('cms2.model.cms', 'Page');
      YuppLoader::load('cms2.model.cms', 'PageZone');
      
      // tengo que usar displayModule.template
      $module = Module::get($this->params['id']);
      $page = Page::get($this->params['pageId']);
      $pageZone = PageZone::getByPageAndName( $this->params['pageId'], $this->params['zone'] );
      
      // displayModule necesita la zona.
      // FIXME: usar template solo para el render de las noticias que no incluya al paginador.
      return $this->renderTemplate('../newsModule/displayNews',
                                   array('offset'=>$this->params['offset'], 'module'=>$module, 'page'=>$page, 'mode'=>'edit', 'zone'=>$pageZone)); // Supongo que si llama a esta accion es porque estoy editando (modo=edit)
   }
}

?>