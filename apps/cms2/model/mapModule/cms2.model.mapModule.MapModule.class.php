<?php

YuppLoader::load('cms2.model.cms', 'Module');

class MapModule extends Module
{
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable("cms_map_module");
        
      $this->addAttribute("centerLat", Datatypes :: FLOAT_NUMBER);
      $this->addAttribute("centerLon", Datatypes :: FLOAT_NUMBER);
      $this->addAttribute("zoom", Datatypes :: INT_NUMBER);

      // Ubicacion central en montevideo
      $this->setCenterLat(-34.9);
      $this->setCenterLon(-56.16);

      // Nivel inicial de zoom
      $this->setZoom(5);

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