<?php

YuppLoader::load('cms2.model.cms', 'PageZone');

class Page extends PersistentObject
{
   const TABLE = "cms_page";
   
   const STATUS_NORMAL   = "normal";  // Vista publica.
   const STATUS_DRAFT    = "draft";   // Solo vista por editores o adminsitradores, es una pagina con contenido incompleto.
   const STATUS_HIDDEN   = "hidden";  // Solo visibles por usuarios logueados.
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
   
   
   /**
    * Devuelve la zona con el nombre $name. NULL si no la encuentra.
    */
   function getZone($name)
   {
      foreach ($this->getZones() as $zone)
      {
         if ($zone->getName() == $name) return $zone;
      }
      
      return NULL;
   }
   
   function hasZone($name)
   {
      return $this->getZone($name) != NULL;
   }
   
   function hasParent()
   {
      return $this->getParent() != NULL;
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
      
      // Definicion de campos
      $this->addAttribute("name",           Datatypes :: TEXT);
      $this->addAttribute("normalizedName", Datatypes :: TEXT); // para la url, debe ser unico.
      
      $this->addAttribute("createdOn",   Datatypes :: DATETIME);
      $this->addAttribute("lastUpdate",  Datatypes :: DATETIME);
      
      $this->addAttribute("status",      Datatypes :: TEXT);
      $this->addAttribute("description", Datatypes :: TEXT);
      $this->addAttribute("keywords",    Datatypes :: TEXT);
      
      // Definicion de relaciones
      //
      // En realidad lo mas facil seria que una pagina tenga a todas sus 
      // paginas hijas y que el tipo de la coleccion sea lista asi se
      // inyecta automaticamente el ord (necesario para mostrar las
      // paginas en orden).
      $this->addHasMany("zones", 'PageZone');
      
      
      // Si tengo una relacion hasMany con migo mismo, es como tener una relacion *-*, entonces necesito tener belongsTo declarado.
      // Entonces tendria A hasMany A y A belongsTo A.
      $this->addHasMany("subpages", 'Page', PersistentObject::HASMANY_LIST); // TODO: conservar orden
      $this->addHasOne("parent", 'Page'); // Pagina padre, puede ser null
      
      //$this->belongsTo = array('Page'); // FIXME: no quiero salvar toda la estructura en cascada cada vez que guardo una pagina!
                                        // El problema es que si no declaro belongsTo, no guarda el hasMany hacia esta misma clase.
                                        // Voy a hacer un ticket para agregar un modificador que indique que guarde la relacion, pero no el objeto. 
      
      // Inicializacion de campos
      $this->setCreatedOn(date("Y-m-d H:i:s")); // Ya con formato de MySQL!
      $this->setLastUpdate(date("Y-m-d H:i:s"));
      $this->setStatus(self::STATUS_NORMAL);
      
      parent :: __construct($args, $isSimpleInstance);
   }
   
   protected function preValidate()
   {
      // En windows sin esto, si el nombre tiene tildes se ve ok en la web.
      //$this->setName( utf8_decode( $this->getName() ));
      
      // FIXME: letras con tildes y enies
      $this->setNormalizedName(
        String::filterCharacters(
          String::toUnderscore(
            String::removeNonLetterChars( $this->getName() )
          )
        )
      );
      
      $this->setLastUpdate(date("Y-m-d H:i:s"));
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