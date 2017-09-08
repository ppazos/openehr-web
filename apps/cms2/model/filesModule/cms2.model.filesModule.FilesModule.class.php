<?php

YuppLoader::load('cms2.model.cms', 'Module');
YuppLoader::load('cms2.model.filesModule', 'FilesModule');

class FilesModule extends Module
{
   const TABLE = 'cms_files_module';
   
   /**
    * Devuelve todos los FileInfo asociados a este modulo.
    */
   public function getFiles()
   {
      return FileInfo::findBy( Condition::EQ(FileInfo::TABLE, 'module_id', $this->getId()),
                               new ArrayObject(array('sort'=>$this->getSortBy(), 'ord'=>'asc')) );
   }
   
   // Pone los atributos boolean en false para que funcione la actualizacion con setProperties()
   public function resetBooleans()
   {
      $this->setShowSize(false);
      $this->setShowDescription(false);
      $this->setShowExtension(false);
      $this->setShowLastUpdate(false);
   }
   
   public function iconSizeMap()
   {
      return array('small'=>32, 'mid'=>64, 'big'=>128);
   }
   
   protected function preValidate()
   {
      // Para que la regex se guarde y cargue igual a como la especifica el usuario.
      $this->setFilter( preg_quote($this->getFilter()) );
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
        
      $this->addAttribute('path', Datatypes :: TEXT);   // desde donde se van a leer los archivos
      $this->addAttribute('filter', Datatypes :: TEXT); // regex, si matchea se incluye
      $this->addAttribute('except', Datatypes :: TEXT); // regex, si matchea se excluye
      $this->addAttribute('lastScan', Datatypes :: DATETIME); // ultima vez que se reviso la path en busca de nuevos archivos
      
      // opciones de visualizacion
      $this->addAttribute('showSize', Datatypes :: BOOLEAN); // muestra tamanio de los archivos
      $this->addAttribute('showDescription', Datatypes :: BOOLEAN); // muestra descripcion de los archivos
      $this->addAttribute('showExtension', Datatypes :: BOOLEAN); // muestra extension con el nombre del archivo
      $this->addAttribute('showLastUpdate', Datatypes :: BOOLEAN); // muestra fecha de actualizacion de los archivos
      
      $this->addAttribute('sortBy', Datatypes :: TEXT); // Atributo por el que se ordenan los FileInfo del modulo.
      $this->addAttribute('iconSize', Datatypes :: INT_NUMBER); // Tamanio de icono a mostrar
      
      
      $this->addConstraints('path', array (
        //Constraint :: nullable(false),  // problema porque el create module es generico y no incluye un campo para path.
        Constraint :: maxLength(255)
      ));
      
      $this->addConstraints('sortBy', array (
        Constraint :: inList(array('lastUpdate', 'name', 'size')),
        Constraint :: maxLength(15)
      ));
      
      $this->setShowSize(false);
      $this->setShowDescription(true);
      $this->setShowExtension(false);
      $this->setShowLastUpdate(true);
      $this->setSortBy('name');
      $this->setIconSize(32);

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