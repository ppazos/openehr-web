<?php

YuppLoader::load('cms2.model.notifications', 'NewsLetter');
YuppLoader::load('cms2.model.auth', 'User');

class NewsLetterSendLog extends PersistentObject
{
   const TABLE = 'cms_notifs_sendlog';

   const STATUS_OK    = 1;
   const STATUS_ERROR = 2;
   
   static public function getByNewsletterAndUser($newsletter, $user)
   {
      if (empty($newsletter) || empty($user))
      {
         return NULL; // TODO: except
      }
      
      $list = NewsLetterSendLog::findBy( Condition::_AND()
                ->add( Condition::EQ(self::TABLE, 'item_id', $newsletter->getId()) )
                ->add( Condition::EQ(self::TABLE, 'to_id',  $user->getId()) ), new ArrayObject );
      
      if ( count($list) == 1 )
      {
         return $list[0];
      }
      
      // TODO: verificar si hay mas de un log para el mismo nl y us
      
      return NULL;
   }
   
   /**
    * Todos los envios para un newsletter
    */
   static public function getByNewsletter($newsletter)
   {
      if (empty($newsletter))
      {
         return NULL; // TODO: except
      }
      
      return NewsLetterSendLog::findBy( Condition::EQ(self::TABLE, 'item_id', $newsletter->getId()), new ArrayObject() );
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
      
      $this->addAttribute('sentOn',  Datatypes :: DATETIME);   // Envio del newsletter
      $this->addAttribute('tries',   Datatypes :: INT_NUMBER); // Cantidad de envios (cuando hay varios intentos)
      $this->addAttribute('status',  Datatypes :: INT_NUMBER);       // Estado del ultimo intento de envio
      $this->addAttribute('comment', Datatypes :: TEXT);   // Comentario, ej. excepcion de un intento de envio fallido
      
      // Verificar que cada vez que salvo el log no salva el newsletter en cascada
      $this->addHasOne('item', 'NewsLetter');
      $this->addHasOne('to', 'User');
      
      //$this->addConstraints('status', array (
      //  Constraint :: maxLength(10)
      //));
      
      $this->setSentOn(date('Y-m-d H:i:s'));
      $this->setTries(0); // Se inicializa en 0 asi el 1er try es 1 porque se le hace +1
      
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