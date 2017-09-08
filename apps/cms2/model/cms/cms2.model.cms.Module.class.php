<?php

class Module extends PersistentObject
{
   const TABLE = "cms_module";
   
   // idem que en Page
   const STATUS_NORMAL   = "normal";   // Vista publica.
   const STATUS_DRAFT    = "draft";    // Solo vista por editores o adminsitradores, es una pagina con contenido incompleto.
   const STATUS_HIDDEN   = "hidden";   // Solo visibles por usuarios logueados.
   const STATUS_DISABLED = "disabled"; // Solo vista por editores o adminsitradores.
   
   static function getStatusList()
   {
      return array(
        self::STATUS_NORMAL,
        self::STATUS_DRAFT,
        self::STATUS_HIDDEN,
        self::STATUS_DISABLED
      );
   }
   
   /**
    * Idem a getStatusList, pero con las keys iguales a los values.
    * Sirve para generar los options de los selects en YuppForm.
    */
   static function getStatusMap()
   {
      return array(
        self::STATUS_NORMAL => self::STATUS_NORMAL,
        self::STATUS_DRAFT => self::STATUS_DRAFT,
        self::STATUS_HIDDEN => self::STATUS_HIDDEN,
        self::STATUS_DISABLED => self::STATUS_DISABLED
      );
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
        
      $this->addAttribute("title",          Datatypes :: TEXT);
      $this->addAttribute("createdOn",      Datatypes :: DATETIME);
      $this->addAttribute("showContainer",  Datatypes :: BOOLEAN);
      $this->addAttribute("showInAllPages", Datatypes :: BOOLEAN);

      $this->addAttribute("status",         Datatypes :: TEXT); // idem a Page

      $this->setCreatedOn(date("Y-m-d H:i:s")); // Ya con formato de MySQL!
      $this->setShowContainer(true);
      $this->setShowInAllPages(false);
      $this->setStatus(self::STATUS_NORMAL);

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