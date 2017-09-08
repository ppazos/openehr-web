<?php

YuppLoader::load('cms2.model.filesModule', 'FilesModule');

class FileInfo extends PersistentObject
{
   const TABLE = 'cms_files_module_file';
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
        
      $this->addAttribute('name', Datatypes :: TEXT);
      $this->addAttribute('extension', Datatypes :: TEXT);
      $this->addAttribute('fullName', Datatypes :: TEXT); // name+ext
      $this->addAttribute('description', Datatypes :: TEXT);
      $this->addAttribute('size', Datatypes :: INT_NUMBER);
      $this->addAttribute('lastUpdate', Datatypes :: DATETIME);
      // TODO: se podria agregar un icono custom para este archivo
      
      // 1-* con FilesModule implementado como backlink desde el lado 1.
      $this->addHasOne('module', 'FilesModule');
      
      $this->addConstraints('name', array (
        //Constraint :: nullable(false),
        Constraint :: maxLength(255)
      ));
      $this->addConstraints('extension', array (
        Constraint :: nullable(true),
        Constraint :: maxLength(5)
      ));
      
      $this->setLastUpdate(date("Y-m-d H:i:s"));
      

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