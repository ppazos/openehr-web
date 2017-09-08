<?php

YuppLoader::load('cms2.model.cms', 'Module');
YuppLoader::load('cms2.model.newsModule', 'News');

class NewsModule extends Module
{
   const TABLE = 'cms_news_module';
   
   /**
    * Si el modulo usa paginacion para las noticias, se llama a este metodo
    * para obtener las noticias en cierto offset, y el max es el attr newsPerPage.
    */
   function getNewsPaged($offset)
   {
      // Quiero los ids de las noticias del modulo con paginacion
      $dal = new DAL('cms2');
      $newsIds = $dal->sqlQuery('SELECT ref_id FROM cms_news_module_news_cms_news_module_news ' .
                                'WHERE owner_id='.$this->getId().
                                ' ORDER BY id DESC'.
                                ' LIMIT '. $this->getNewsPerPage().' OFFSET '. $offset);
                                
      //print_r($newsIds);
      /*
       *
        Array
        (
            [0] => Array
                (
                    [ref_id] => 1
                )
        
            [1] => Array
                (
                    [ref_id] => 2
                )
        )
       */
      // Carga las noticias con esos ids
      $res = array();
      foreach ($newsIds as $i => $v)
      {
         $res[] = News::get($v['ref_id']);
      }
      
      return $res;
   }
   
   /**
    * Cantidad total de noticias del modulo. Sirve para paginar.
    * 
    * TODO: cuando hace esto, debe cargar todas las noticias de la base.
    * Se podria agregar un atributo que vaya contando para no tener que cargar noticias.
    * Lo otro es que al pedir esto, el getNewsPaged deberia detectar que los objetos estan en sesion y no carcarlos de nuevo desde la base.
    */
   function countNews()
   {
      return count($this->getNews());
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
      
      $this->addAttribute('paged', Datatypes::BOOLEAN);
      $this->addAttribute('newsPerPage', Datatypes::INT_NUMBER);
      
      $this->addHasMany('news', 'News', PersistentObject::HASMANY_LIST);


      $this->setPaged(true);
      $this->setNewsPerPage(5);

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