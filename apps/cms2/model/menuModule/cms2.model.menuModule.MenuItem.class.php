<?php

/**
 * Item del menu, apunta a un link externo o a un apagina del CMS.
 */
class MenuItem extends PersistentObject
{
   const TABLE = 'cms_menu_module_items';
   
   public function isLink()
   {
      return $this->getUrl() != NULL;
   }
   
   public function setPage( $page )
   {
      $this->setPageId( $page->getId() );
   }
   public function getPage()
   {
      YuppLoader::load('cms2.model.cms', 'Page');
      return Page::get($this->getPageId());
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
      
      // TODO: agregar que se habra o no en una nueva ventana.
      
      // Modo link
      $this->addAttribute('label', Datatypes::TEXT);
      $this->addAttribute('url',  Datatypes::TEXT);
      
      // Modo pagina del cms
      $this->addAttribute('pageId', Datatypes::INT_NUMBER);


      $this->addConstraints('label', array (
        Constraint :: nullable(true)
      ));
      $this->addConstraints('url', array (
        Constraint :: nullable(true)
      ));
      $this->addConstraints('pageId', array (
        Constraint :: nullable(true)
      ));

      parent :: __construct($args, $isSimpleInstance);
   }
   
   protected function preValidate()
   {
      // Si es vacio, le pongo null
      if ($this->getUrl() == '') $this->setUrl(NULL);
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