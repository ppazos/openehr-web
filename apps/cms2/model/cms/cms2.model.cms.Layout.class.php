<?php

YuppLoader::load('cms2.model.cms', 'Zone');

class Layout extends PersistentObject
{
   /**
    * Devuelve el layout activo.
    */
   public static function getActive()
   {
      $tableName = YuppConventions::tableName( 'Layout' );
      $condition = Condition::EQ($tableName, 'active', true);
      $list = Layout::findBy( $condition, new ArrayObject() );
      return $list[0]; // Siempre hay un layout activo
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable("cms_layouts");
      
      // Definicion de campos
      $this->addAttribute("name",   Datatypes :: TEXT);
      $this->addAttribute("active", Datatypes :: BOOLEAN); // Solo un layout activo por vez
      
      // Definicion de relaciones
      $this->addHasMany("zones", 'Zone' );
      
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