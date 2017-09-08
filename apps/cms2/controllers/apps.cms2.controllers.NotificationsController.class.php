<?php

YuppLoader::load('cms2.model.notifications', 'NewsLetterSendLog');
YuppLoader::load('cms2.model.notifications', 'NewsLetter');

class NotificationsController extends YuppController {

   public function indexAction()
   {
      /**
       * Por defecto deberia mostrar la pagina por defecto del sitio por defecto.
       */
      //Logger::getInstance()->pm_log("indexAction ===== == ===== == ===== == ===== ==");
      //return $this->redirect(array('action'=>'displayPageRO', 'params'=>array('pageId'=>1)));
      
      // TODO
   }
   
   public function listAction()
   {        
      $nls = NewsLetter::listAll($this->params);
      $count = NewsLetter::count();
      return array('nls'=>$nls, 'count'=>$count);
   }
   
   public function createAction()
   {
      if (isset($this->params['doit']))
      {
         $nl = new NewsLetter();
         $nl->setProperties($this->params);
         if (!$nl->save())
         {
            $this->flash['message'] = 'Ocurrio un error';
            return array('newsletter'=>$nl);
         }
         return $this->redirect(array('action'=>'list'));
      }
   }
   
   public function editAction()
   {
      $nl = NewsLetter::get($this->params['id']);
      if (isset($this->params['doit']))
      {
         $nl->setProperties($this->params);
         if (!$nl->save())
         {
            $this->flash['message'] = 'Ocurrio un error';
            $this->params['newsletter'] = $nl;
            return $this->render('create'); // reutilizo create para edit
         }
         return $this->redirect(array('action'=>'list'));
      }
      $this->params['newsletter'] = $nl;
      return $this->render('create'); // reutilizo create para edit
   }
   
   public function showAction()
   {
      $nl = NewsLetter::get($this->params['id']);
      
      YuppLoader::load('cms2.model.notifications', 'NewsLetterSendLog');
      $logs = NewsLetterSendLog::getByNewsletter($nl);
      
      return array('newsletter'=>$nl, 'logs'=>$logs);
   }
   
   /**
    * Accion publica para ver el newsletter online.
    */
   public function displayAction()
   {
      if (!isset($this->params['id'])) return $this->render('404'); // Para no dejar que cambien la url a mano y saquen el id
      
      $nl = NewsLetter::get($this->params['id']);
      
      if ($nl == NULL) return $this->render('404'); // Para no dejar que cambien la url a mano para probar ids
      
      return array('newsletter'=>$nl);
   }
   
   // TODO: edit, ...
   
   /**
    * @param id newsletter id
    * @param userType tipo de usuario destino, si es null es para todos los usuarios
    */
   public function sendAction()
   {
      $nl = NewsLetter::get($this->params['id']);
      
      // TODO: get users by usertype
      if (isset($this->params['usertype']))
      {
         $users = User::findByUserType($this->params['usertype']);
      }
      else
      {
         $users = User::listAll(new ArrayObject());
      }
      
      foreach ($users as $user)
      {
         $log = NewsLetterSendLog::getByNewsletterAndUser($nl, $user);
         $err_msg = NULL;
         try
         {
            // No enviada o enviada pero dio error, tengo que enviar o reenviar
            if ($log == NULL || $log->getStatus() == NewsLetterSendLog::STATUS_ERROR)
            {
               $err_msg = $nl->sendTo($user); // Devuelve NULL si manda ok
            }
            else continue; // No envia, sigue con el siguiente
         }
         catch (Exception $e)
         {
            // TODO: crear o actualizar log on error para nl y user
            $err_msg = $e->getMessage();
         }
         
         // Si llega aqui es porque envia o intenta enviar
         
         // Crea o actualiza log
         if ($log == NULL)
         {
            $log = new NewsLetterSendLog(array('item'=>$nl, 'to'=>$user));
         }
         
         // Actualiza datos del log
         $log->setStatus( ($err_msg != NULL) ? NewsLetterSendLog::STATUS_ERROR : NewsLetterSendLog::STATUS_OK ); // Error?
         $log->setSentOn(date('Y-m-d H:i:s'));
         $log->setTries( $log->getTries()+1 ); // Se inicializa en 0 asi el 1er try es 1
         $log->setComment($err_msg); // Puede ser NULL
         $log->save();
      }
      
      $this->flash['message'] = 'Boletin enviado'; // Poner cantidad de envios y exitos/fracasos
      return $this->redirect(array('action'=>'show', 'params'=>array('id'=>$nl->getId())));
   }
   
   public function dellogAction()
   {
      //print_r($this->params);
      //return;
    
      $log = NewsLetterSendLog::get($this->params['id']);
      $log->delete(true); // logico
      
      $this->flash['message'] = 'Log eliminado';
      return $this->redirect(array('action'=>'show', 'params'=>array('id'=>$log->getItem()->getId())));
   }
}

?>