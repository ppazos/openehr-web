<?php

YuppLoader::load('cms2.model.cms', 'Page');
YuppLoader::load('cms2.model.cms', 'Module');

class PageZone extends PersistentObject
{
   const TABLE = 'cms_page_zone';
   
   public static function getByPageAndName($pageId, $name)
   {
      $condition = Condition::_AND()
                    ->add( Condition::EQ(self::TABLE, 'page_id', $pageId) )
                    ->add( Condition::EQ(self::TABLE, 'name', $name) );
                    
      $list = PageZone::findBy( $condition, new ArrayObject() );
      return ( (isset($list[0])) ? $list[0] : null );
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
        
      // Definicion de campos
      $this->addAttribute("name", Datatypes :: TEXT);

      // Definicion de relaciones
      $this->addHasMany("modules", 'Module', PersistentObject::HASMANY_LIST); // Importa el ord de los modules en la zona porque es el ordne en el que se van a mostrar. FIXME: deberia ser ordered set.
      $this->addHasOne("page", 'Page'); // Pagina a la que pertenece la zona
      

      // Definicion de restricciones
      // ...
      
      // Las zonas pertenecen a una pagina. (Page hasMany Zone)
      //$this->belongsTo = array( 'YuppCMSPage' );
      
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