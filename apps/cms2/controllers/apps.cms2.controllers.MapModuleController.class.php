<?php

YuppLoader::load('cms2.model.mapModule', 'MapModule');

class MapModuleController extends YuppController {

   /**
    * Edita el contenido del modulo de HTML.
    * 
    * Si se ejecuta por ajax, es porque se crea una pantalla modal.
    * Al guardar, habria que retornar el contenido actualizado del modulo para
    * ver el contenido actualizado en la pagina (por ahora podria hacer un refresh
    * de la pagina y listo).
    * 
    * in: id
    * in: pageId
    */
   public function editAction()
   {
      /* TODO
      $module = HtmlModule::get($this->params['id']);
      if (isset($this->params['doit']))
      {
         $module->setProperties($this->params);
         if (!$module->save()) print_r($module->getErrors());
         
         // no hacer redirect si la accion se ejecuta por ajax
         //return $this->redirect(array('controller'=>'cms', 'action'=>'displayPage', 'params'=>array('pageId'=>$this->params['pageId'])));
         return $this->renderString('Modulo salvado correctamente');
      }
      
      return array('module'=>$module);
      */
   }
}

?>