<?php

class NewsLetter extends PersistentObject
{
   /**
    * Envia el newsletter al usuario por mail.
    * Devuelve NULL si envio correctamente y un string no NULL con el mensaje de error cuando hubo algun problema.
    */
   public function sendTo($user)
   {
      include('./apps/cms2/config/cms_config.php'); // cms_sender_xxx
      $headers = "From: ". $cms_sender_name ." <". $cms_sender_email . ">\r\n"; //optional headerfields
      $headers .="Return-Path: <" . $cms_sender_email . ">\r\n"; // avoid ending in spam folder http://php.net/manual/en/function.mail.php
      
      // To send HTML mail, the Content-type header must be set
      $headers .= 'MIME-Version: 1.0'. "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1'. "\r\n";
      
      ini_set('sendmail_from', $cms_sender_email);
     
      // Link a "ver online"
      // En lugar de tener el id en el link, poner una key sin estructura para que no puedan ingresar el id a mano
      $linkToDisplayOnline = '<div align="center">Si ud. no visualiza correctamente este correo, haga clic <a href="https://'. $_SERVER['HTTP_HOST'] . '/cms2/notifications/display?id='. $this->getId() .'" target="_blank">aqui</a></div>';
     
      try
      {
         // TODO: sacar el asunto del boletin de la config
         if (!mail($user->getEmail(), 'Boletin de noticias', $linkToDisplayOnline . $this->getContent(), $headers)) // to, subject, message, additional headers, additional parameters
         {
            // TODO: obtener el last error del mail
            return "No se pudo enviar notificacioa a '.$user->getEmail().'"; // Mensaje de error
         }
      }
      catch (Exception $e)
      {
        // Cae aqui cuando no hay un servidor de mail para enviar el correo
        return $e->getMessage(); // Mensaje de error
      }
      
      return NULL; // ok!
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable('cms_notifs_newsletter');
      
      $this->addAttribute('name',      Datatypes :: TEXT);     // Nombre para referencia rapida
      $this->addAttribute('content',   Datatypes :: TEXT);     // Contenido del newsletter
      $this->addAttribute('createdOn', Datatypes :: DATETIME); // Creacion del nl
      
      // TODO:
      // - sendStatus (si se e nvio ok a todos los destinatarios)
      
      // Por ahora va a todos los miembros, se podrian agregar atributos para
      // establecer a que tipos de usuarios va el newsletter.
      
      $this->setCreatedOn(date('Y-m-d H:i:s'));
      
      parent :: __construct($args, $isSimpleInstance);
   }
   public static function listAll(ArrayObject $params)
   {
      self :: $thisClass = __CLASS__;
      return PersistentObject :: listAll($params);
   }
   public static function count()
   {
      self :: $thisClass = __CLASS__;
      return PersistentObject :: count();
   }
   public static function get($id)
   {
      self :: $thisClass = __CLASS__;
      return PersistentObject :: get($id);
   }
   public static function findBy(Condition $condition, ArrayObject $params)
   {
      self :: $thisClass = __CLASS__;
      return PersistentObject :: findBy($condition, $params);
   }
   public static function countBy(Condition $condition)
   {
      self :: $thisClass = __CLASS__;
      return PersistentObject :: countBy($condition);
   }
}
?>