<?php

YuppLoader::load('cms2.model.cms', 'Module');
//YuppLoader::load('cms2.model.cms', 'Page');
YuppLoader::load('cms2.model.menuModule', 'MenuItem');

class MenuModule extends Module
{
   /**
    * Crea un MenuItem a partir de una pagina y se la asocia al modulo.
    */
   public function addPageItem( $page )
   {
      $item = new MenuItem();
      $item->setPage($page); // Setea el pageId internamente
      $this->addToItems($item);
   }
   
   /**
    * Ideam a addPageItem pero se le pasa el id de la pagina.
    */
   public function addPageIdItem( $pageId )
   {
      $this->addToItems( new MenuItem(array('pageId'=>$pageId)) );
   }
   
   /**
    * Crea un MenuItem a partir de una etiqueta y url, y se la asocia al modulo.
    */
   public function addLinkItem($label, $url)
   {
      $this->addToItems( new MenuItem(array('label'=>$label, 'url'=>$url)) );
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable('cms_menu_module');
      
      $this->addAttribute('vertical', Datatypes::BOOLEAN); // true si es vertical, false es horizontal. Sirve para saber como mostrar el menu.
      $this->addAttribute('level', Datatypes::INT_NUMBER); // Muestra hasta este nivel de hijos, vacio muestra todos los niveles
      
      //$this->addHasMany('pages', 'Page', PersistentObject::HASMANY_LIST);
      $this->addHasMany('items', 'MenuItem', PersistentObject::HASMANY_LIST);

      $this->addConstraints('level', array (
        Constraint :: nullable(true)
      ));

      $this->setVertical(true);
      $this->setLevel(0); // Por defecto no muestra hijos

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