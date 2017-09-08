<?php

class News extends PersistentObject
{
   const TABLE = 'cms_news_module_news';
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
      
      $this->addAttribute('title', Datatypes::TEXT);
      $this->addAttribute('text',  Datatypes::TEXT);
      $this->addAttribute('link',  Datatypes::TEXT);
      $this->addAttribute('creationDate', Datatypes::DATETIME);

      $this->addConstraints('link', array (
        Constraint :: nullable(true)
      ));

      $this->setCreationDate(date("Y-m-d H:i:s"));

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