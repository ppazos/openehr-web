<?php

class Zone extends PersistentObject
{
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable("cms_zones");
      
      // Definicion de campos
      $this->addAttribute("name",   Datatypes :: TEXT);       // nombre para referirse a la zona
      $this->addAttribute("x",      Datatypes :: INT_NUMBER); // ubicacion en px
      $this->addAttribute("y",      Datatypes :: INT_NUMBER); // ubicacion en px
      $this->addAttribute("width",  Datatypes :: INT_NUMBER); // ancho en px
      $this->addAttribute("height", Datatypes :: INT_NUMBER); // alto en px
      
      
      /*
      // Definicion de relaciones
      // ...
      
      // Inicializacion de campos
      $this->setWidth( 300 );
      $this->setHeight( 300 );
      
      // Si el tamanio es menos de 20x20 no se va a ver nada (esto es para prevenir accidentes al redimencionar, para que no desaparezca la zona y se quede por lo menos de 20x20)
      $this->addConstraints("width", array (
         Constraint :: min(20)
      ));
      $this->addConstraints("height", array (
         Constraint :: min(20)
      ));
      
      // Las zonas pertenecen a una pagina. (Page hasMany Zone)
      $this->belongsTo = array( 'TemplatePage' );
      */
      
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