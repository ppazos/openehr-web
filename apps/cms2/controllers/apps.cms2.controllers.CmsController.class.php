<?php

YuppLoader::load('cms2.model.cms', 'Page');
YuppLoader::load('cms2.model.cms', 'Layout');

class CmsController extends YuppController {

   public function indexAction()
   {
      /**
       * Por defecto deberia mostrar la pagina por defecto del sitio por defecto.
       */
      Logger::getInstance()->pm_log("indexAction ===== == ===== == ===== == ===== ==");
      return $this->redirect(array('action'=>'displayPageRO', 'params'=>array('pageId'=>1)));
   }
   
   /**
    * Listado de paginas para gestionar.
    */
   public function listPagesAction()
   {
      // TODO: necesito volver a la misma pagina de la que se ejecuto la accion al terminar de gestionar paginas?
      // TODO: paginacion
      
      // Necesito listar todas, las eliminadas y las no eliminadas
      $cond = Condition::_OR()
            ->add( Condition::EQ(Page::TABLE, 'deleted', true) )
            ->add( Condition::EQ(Page::TABLE, 'deleted', false) );
            
      $pages = Page::findBy( $cond, $this->params );
      
      return array('pages'=>$pages);
   }
   
   /**
    * Crea una nueva pagina para un sitio. TODO: HACER QUE SIEMPRE SEA MODAL.
    */
   public function createPageAction()
   {
      // FIXME:!
      // Debe crear laz pageZones de la pagina, en base a las zonas del layout seleccionado.
      
      if (isset($this->params['doit'])) // save
      {
         $page = new Page($this->params);
         
         // Crea las pageZones de la pagina, basadas en las zonas del layout activo.
         $layout = Layout::getActive();
         foreach ($layout->getZones() as $zone)
         {
            $page->addToZones( new PageZone(array('name'=>$zone->getName(), 'page'=>$page)));
         }
         
         //Logger::getInstance()->setFile("logger_create_page.txt");
         //Logger::getInstance()->on();
         
         // ======================================================================================
         // Verificacion de los modulos que se muestran en todas las paginas,
         // se agregan a la nueva pagina de forma automatica.
         $modules = Module::findBy( Condition::EQ(Module::TABLE, 'showInAllPages', true), new ArrayObject() );
         
         
         //Logger::struct($modules);
         
         // Pongo los modulos en la zona que se que existe, el problema es que no puedo
         // decir en que zona poner cada modulo, porque en cada pagina, el mismo modulo
         // podria estar en distintas zonas.
         $contentZone = $page->getZone('content');
         foreach ($modules as $module)
         {
             $contentZone->addToModules($module);
         }
         // ======================================================================================
         
         
      
         // Tengo que salvar la pagina antes de relacionarla con su parent,
         // sino no salva la relacion hasMany porque no tiene idref.
         if (!$page->save()) print_r($page->getErrors());
         
         //Logger::getInstance()->off();
         
         if (!empty($this->params['parentId']))
         {
            $parent = Page::get($this->params['parentId']);
            $parent->addToSubpages($page);
            
            
            // nuevo
            $page->setParent($parent);
            $page->save();
            
            
            // FIXME: no esta guardando las relaciones!!!!!
            // UPDATE: la relacion se guarda bien, no se que estaba mal.
            if (!$parent->save()) print_r($parent->getErrors());
         }

         // El request del doit es siempre ajax.
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "id":"'.$page->getId().'", "parentId":"'.$this->params['parentId'].'"}');
      }
   } // createPage
   
   
   /**
    * @param pageId integer
    */
   public function editPageAction()
   {
      $page = Page::get($this->params['pageId']);
      
      // Todas las paginas menos esta, para mostrar una lista de parents posibles que se pueden setear.
      $possible_parents = Page::findBy( Condition::NEQ(Page::TABLE, 'id', $this->params['pageId']), new ArrayObject() );
      
      if (isset($this->params['doit'])) // update
      {
         // TODO: que se actualice el lastUpdate (usar el preValidate) 
         $page->setProperties($this->params);
         if (!$page->save())
         {
            // TODO: caso de error de que no pudo salvar.
            header('Content-type: application/json');
            return $this->renderString('{"status":"error", "id":"'.$page->getId().'"}'); // TODO: pasarle la lista de errores de validacion
         }
         
         
         // Saca del parent anterior / actual
         if (!empty($this->params['prevParentId']))
         {
            $prevParent = Page::get($this->params['prevParentId']);
            $prevParent->removeFromSubpages($page);
         }

         // Sino viene el parentId, creo asociaciones corresp.
         if (!empty($this->params['parentId']))
         {
            $parent = Page::get($this->params['parentId']);
            $parent->addToSubpages($page);
            
            $page->setParent($parent);
            $page->save();
            
            // FIXME: no esta guardando las relaciones!!!!!
            // UPDATE: la relacion se guarda bien, no se que estaba mal.
            if (!$parent->save()) print_r($parent->getErrors());
         }
         else // Sino viene un nuevo parentId, pongo null en page porque puede quedar seteado el anterior
         {
            $page->setParent(NULL);
            $page->save();
         }
         
         
         // El request del doit es siempre ajax.
         // El redirect a listPages o displayPage (segun si es backend o no) lo hace la vista desde JS, yo solo le tiro el JSON.
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "id":"'.$page->getId().'"}');
      }
      
      // Se muestra en la modal, la vista es igual a la de create
      return array('page'=>$page, 'possible_parents'=>$possible_parents);
   }
   
   /**
    * @param pageId identificador de la pagina a mostrar.
    * Si es multisitio, necesito el id del sitio tambien? NO! la pagina ya tiene el sitio asociado. 
    */
   public function displayPageAction()
   {
      // Prueba para consultar, mostrar y cambiar de layout
      //$dal = new DAL('cms2');
      //$layouts = $dal->sqlQuery('SELECT id, name FROM cms_layouts');
      //print_r($layouts); // Array ( [0] => Array ( [name] => simple ) [1] => Array ( [name] => 3cols ) ) 
      
      $page = Page::get( $this->params['pageId'] );
      return array('page'=>$page);
   }
   
   /**
    * ReadOnly: es la pagina sin que es usuario este logueado
    */
   public function displayPageROAction()
   {
      $page = Page::get( $this->params['pageId'] );
      
      //return array('page'=>$page);
      
      // Nuevo por urls amigables
      // controller = display
      // action = p.nname
      // En AppMapping se mapea a PageController.display(p.nname)
      return $this->redirect(array('controller'=>'display', 'action'=>$page->getNormalizedName()));
   }
   
   public function deletePageAction()
   {
      // Elimina la pagina pero no la saca de las subpages de la pagina padre en caso de tener una pagina padre.
      
      $page = Page::get( $this->params['pageId'] );
      $page->delete(true); // Eliminacion logica
      
      $this->flash['message'] = 'Pagina eliminada con exito';
      
      // Si el delete viene desde la vista de listado de paginas (backend)
      if (isset($this->params['backend']))
      {
         return $this->redirect(array('action'=>'listPages'));
      }
      
      // Esto lo resuelvo chequeando el page->getDeleted y cambiando el css en la vista (con addClass de jquery)
      return $this->redirect(array('action'=>'displayPage', 'params'=>array('pageId'=>$this->params['pageId'])));
   }
   
   /**
    * Accion inversa a delete.
    */
   public function undeletePageAction()
   {
      $page = Page::get( $this->params['pageId'] );
      $page->setDeleted(false); // Quita eliminacion logica
      $page->save(); // TODO: verif. errores.
      
      $this->flash['message'] = 'Pagina reestablecida con exito';
      
      // Si el delete viene desde la vista de listado de paginas (backend)
      if (isset($this->params['backend']))
      {
         return $this->redirect(array('action'=>'listPages'));
      }
      
      return $this->redirect(array('action'=>'displayPage', 'params'=>array('pageId'=>$this->params['pageId'])));
   }
   
   // =======================================================================
   // ACCIONES DE MODULOS
   
   /**
    * Agega un modulo de una clase dada dentro de un pageZone de una pagina.
    * Luego de agregar un modulo, deberia ir a la vista de edicion de los
    * datos segun la clase de modulo.
    */
   public function createModuleAction()
   {
      // FIXME: esta accion tiene varios save que deberia verificar por errores,
      //        y si alguno falla deberia garantizar que el sistema queda en un
      //        estado consistente, el problema es que deberia soportar la session
      //        de DB para hacer rollback en caso de error, sino me queda basura activa.
      
      if (isset($this->params['doit']))
      {
         // Donde se va a ubicar el modulo
         $pageZone = PageZone::getByPageAndName( $this->params['pageId'], $this->params['zone'] );
        
         // Si la pageZone del layout seleccionado no esta creada para esta pagina, lo creo.
         if ($pageZone == NULL)
         {
            // crea la zona y la socia a la pagina
            $page = Page::get($this->params['pageId']);
            $pageZone = new PageZone(array('name'=>$this->params['zone'], 'page'=>$page));
            $page->addToZones($pageZone);
            $pageZone->save();
            $page->save(); // Esto NO guarda la zona en la pagina
         }
         
         
         $module = new $this->params['class']($this->params);
         $module->setShowContainer( isset($this->params['showContainer']) );
         $module->setShowInAllPages( isset($this->params['showInAllPages']) ); // TODO: si es true, tengo que crear el modulo en las demas paginas
         
         
         // ------ logica parecida a la que esta en createModule
         // Si tengo que poner los modulos en todas las paginas
         if ($module->getShowInAllPages())
         {
            // DIFERENCIA CON EDIT:            
            // Como este modulo es nuevo, no esta en ninguna pagina,
            // entonces agrego directamente.
            
            // Zonas con el mismo nombre que la zona donde se esta mostrando actualmente el modulo.
            $zones = PageZone::findBy(
                       Condition::EQ(PageZone::TABLE, 'name', $this->params['zone']),
                       new ArrayObject()
                     );
            
            
            // Para cada zona, me fijo:
            //  1. Que la zona no sea la actual
            //  2. Que el modulo no esté ya en esa zona (TODO)
            foreach ($zones as $zone)
            {
               // Agrega el modulo a cada zona
               $zone->addToModules( $module );
                  
               //echo 'agrega a zona '. $zone->getId() .'<br/>';
                  
               // FIXME: al salvar la zona, se salva el modulo (y el modulo ya se salva al final). Es sacarle el belongsTo.
               // Ademas si guarda aca el modulo, se deberian limpiar los dirtys, y en los proximos intentnos no deberia
               // guardar por estar limpia, pero se ve que en el guardado en cascada no se limpian los dirtys! 
               if ( !$zone->save() ) print_r($zone->getErrors());
            }
         }
         else // Si no hay showInAllPages se agrega solo a la zona indicada en la pagina actual.
         {
            $pageZone->addToModules( $module );
            if (!$pageZone->save()) print_r($pageZone->getErrors());
         }
         
         
         // Como la pantalla ahora es modal, no tengo que redireccionar a otra accion
         //return $this->redirect(array('action'=>'displayPage', 'params'=>array('pageId'=>$this->params['pageId'])));
         //return $this->renderString('Modulo creado con exito');
         
         // Tengo que decirle a la pagina el id del nuevo modulo
         // Para el display, se necesita tambien la pagina.
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "id":"'.$module->getId().'", "pageId":"'.$this->params['pageId'].'"}');
      }
      
      // Necesito los nombres de las zonas para indicar en que zona se pondra el modulo.
      $zones = array();
      $layout = Layout::getActive();
      foreach ($layout->getZones() as $zone)
      {
         $zones[$zone->getName()] = $zone->getName(); // FIXME: para que hago un array con key/value igual?
      }
      
      // Necesito las clases de modulos para indicar de cual quiero crear.
      $mclasses = ModelUtils::getAllSubclassesOf( 'Module' );
      $classes = array();
      foreach ($mclasses as $mclass) $classes[$mclass] = $mclass;
      
      return array('zones'=>$zones, 'classes'=>$classes);
   }
   
   
   /**
    * Muestra los modulos para gestionarlos.
    * La idea es poder ver tambien los modulos eliminados de forma logica con removeModule,
    * para poder reestablecerlos y ubicarlos en alguna pageZone de alguna pagina, o para 
    * eliminarlos fisicamente.
    */
   public function listModulesAction()
   {
      // Lista todos los modulos, borrados o activos
      // FIXME: http://code.google.com/p/yupp/issues/detail?id=109
      
      // Sino se piden los herfanos, listo todos
      if (!isset($this->params['h']))
      {
         $cond = Condition::_OR()
            ->add( Condition::EQ(Module::TABLE, 'deleted', true) )
            ->add( Condition::EQ(Module::TABLE, 'deleted', false) );
      }
      else // Listo solo los herfanos (deleted)
      {
         $cond = Condition::EQ(Module::TABLE, 'deleted', true);
      }
      
      $modules = Module::findBy( $cond, $this->params );
      
      /*
      // Mientras lo resuelvo de otra forma
      $active = Module::listAll($this->params); // OJO, puede paginar solo
      
      $tableName = YuppConventions::tableName( 'Module' );
      $cond = Condition::EQ($tableName, 'deleted', true);
      $deleted = Module::findBy( $cond, $this->params ); // OJO, puede paginar solo
      
      return array('modules'=>array_merge($active, $deleted));
      */
      
      // Si se indica que quiero ver la modal, muestro la modal con los modulos huerfanos para poder agregarlos a alguna zona de la pagina actual.
      // No puedo hacer isAjax porque el request viene normal, no ajax, porque es poner la url en el iframe de la modal.
      if (isset($this->params['m']))
      {
         /* No necesito mandarle JSON, tengo que mandarle HTML a la modal...
         YuppLoader::load('core.persistent.serialize', 'JSONPO');
         
         // FIXME: armo el array JSON porque toJSON aun no soporta que le pase un array de PersistentObject (con esto resuelvo: http://code.google.com/p/yupp/issues/detail?id=125)
         $json = '['; 
         foreach ($modules as $module)
         {
             $json .= JSONPO::toJSON( $module ) .', ';
         }
         
         $json = substr($json, 0, -2);
         $json .= ']';
         
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "id":"'.$page->getId().'", "parentId":"'.$this->params['parentId'].'"}');
         */
         
         // Necesito los nombres de las zonas para indicar en que zona se pondra el modulo.
         $zones = array();
         $layout = Layout::getActive();
         foreach ($layout->getZones() as $zone)
         {
            $zones[] = $zone->getName();
         }
         
         $this->params['zones'] = $zones;
         $this->params['modules'] = $modules;
         return $this->render('listOrphanModules'); // FIXME: poder pasarle el model en el render.
      }

      return array('modules'=>$modules);
   }
   
   
   /**
    * Luego de que listModules para los modulos huerfanos en la modal,
    * puede agregar un modulo a una zona de una pagina.
    * 
    * @param pageId pagina donde se agregara el modulo huerfano
    * @param zone nombre de la zona en la pagina donde se agregara el modulo
    * @param moduleId identificador del modulo a agregar
    */
   public function addOrphanModuleToPageZoneAction()
   {
       // Codigo similar a createModule
       //print_r($this->params);
       
       // Donde se va a ubicar el modulo
       $pageZone = PageZone::getByPageAndName( $this->params['pageId'], $this->params['zone'] );
       
       // Si la pageZone del layout seleccionado no esta creada para esta pagina, lo creo.
       // TODO: ahora al cambiar de layout se crean todas las zonas faltantes, asi que esto deberia ser redundante.
       if ($pageZone == NULL)
       {
          // crea la zona y la socia a la pagina
          $page = Page::get($this->params['pageId']);
          $pageZone = new PageZone(array('name'=>$this->params['zone'], 'page'=>$page));
          $page->addToZones($pageZone);
          $pageZone->save();
          $page->save(); // Esto NO guarda la zona en la pagina, // TODO: respuesta con error
       }
       
       // El modulo huerfano (tiene deleted en true)
       $module = Module::get($this->params['moduleId']);
       
       // Undelete
       $module->setDeleted(false);
       if (!$module->save()) print_r($module->getErrors()); // TODO: respuesta con error
       
       
       // Agrega el modulo a la zona
       $pageZone->addToModules( $module );
       if (!$pageZone->save()) print_r($pageZone->getErrors()); // TODO: respuesta con error
       
       // La respuesta es JSON porque el request es AJAX
       header('Content-type: application/json');
       return $this->renderString('{"status":"ok", "id":"'.$module->getId().'", "zoneId":"'.$pageZone->getId().'", "pageId":"'.$this->params['pageId'].'"}');
   }
   
   
   /**
    * Quita un modulo de una pageZone.
    * Posiblemente, la instancia del modulo quede sin estar en ninguna pageZone,
    * por lo que es necesario tener una accion para mostrar todas las instancias 
    * de modulos que no estan en ninguna pageZone. TODO.
    */
   public function removeModuleAction()
   {
      YuppLoader::loadModel(); // Carga todo el Model de la aplicacion CMS, si no falla en:
      // Fatal error: Class 'HtmlModule' not found in 
      // C:\wamp\www\YuppPHPFramework\core\persistent\core.persistent.PersistentManager.class.php on line 690
      // El problema es que la clase especifica del Module no esta cargada, igual cargar todo el modelo puede ser innecesario...
      
      // FIXME: antes de eliminar el modulo tengo que eliminarlo de la pageZone de la pagina actual
      // necesito una operacion que me pueda decir en que pageZone de una pagina esta un modulo, o
      // necesito un dato en el modelo que me lo diga, p.e. poniendo una referencia para atras en el modulo.
      // Si no se hace removeFrom del modulo de los modulos de la pageZone, y se hace el delete, ocurre un error al volver a mostrar la pagina. 
    
      // El problema es que un mismo modulo puede estar en varios pageZone de distintas paginas, incluso de la misma.
      // Es el tipico caso del menu que aparece en todas las paginas.
      
      $module = Module::get($this->params['moduleId']);
      
      $pageZone = PageZone::getByPageAndName($this->params['pageId'], $this->params['zone']);
      $pageZone->removeFromModules($module); // TODO: Siempre dederia existir esta pageZone, pero igual verificar si existe (podria haber problemas de concurrencia que hagan que no exista)
      
      /* esto se hace con las 2 lineas de arriba
      $page = Page::get($this->params['pageId']);
      foreach ($page->getZones() as $pageZone)
      {
         if ($pageZone->modulesContains($module))
         {
            $pageZone->removeFromModules($module);
            break;
         }
      }
      */
      
      
      // TICKET #11: http://code.google.com/p/yupp-cms/issues/detail?id=11
      // Codigo de editmodule:
      
      // Busca las zonas en la que esta el modulo ahora, para luego mostrar
      // el modulo en las zonas que se llamen igual de las demas paginas
      // que no tengan el modulos.
      $dal = new DAL('cms2');
      $res = $dal->sqlQuery('SELECT count(owner_id) as c FROM cms_page_zone_modules_cms_module WHERE ref_id='. $module->getId());
      
      /*
       * Array( 0 => Array (c => xx) )
       */
      
      /*
      Logger::getInstance()->setFile("logger_remove_module.txt");
      Logger::getInstance()->on();
      Logger::struct($res);
      Logger::struct($res[0]['c'] === "0"); // 1
      Logger::struct($res[0]["c"] === "0"); // 1
      Logger::struct($res[0]['c'] === '0'); // 1
      Logger::struct($res[0]["c"] === '0'); // 1
      //Logger::struct($res[0]['c']); // '0'
      Logger::struct(gettype($res[0]['c'])); // es un string no un numero!
      Logger::getInstance()->off();
      */
      
      
      // FIXME: si el modulo se muestra en otras paginas NO deberia borrarse!
      //        deberia preguntarle al usuario si quiere que lo borre de todas
      //        las paginas o solo de esta.
      if ($res[0]['c'] === "0")
         $module->delete(true); // logico
      
      
      // Ahora responde json
      //$this->flash['message'] = 'Module eliminado con exito';
      
      // Si se quita de la pagina actual y se mostraba en todas las pagians, tengo que apagar la bandera
      $module->setShowInAllPages(false);
      if (!$module->save()) print_r($module->getErrors); // TODO: check error
      

      // Ahora es ajax: http://code.google.com/p/yupp-cms/issues/detail?id=8
      header('Content-type: application/json');
      return $this->renderString('{"status":"ok", "class":"'.$this->params['class'].'", "moduleId":"'.$this->params['moduleId'].'"}');
   }
   
   /**
    * Se utiliza para editar la configuracion basica de cualquier modulo, 
    * como por ejemplo si se muestra o no el contenedor o si el modulo 
    * debe mostrarse en todas las paginas.
    * Cada controlador de modulos particulares, tendra su propia accion
    * editModule, donde se editaran las opciones de ese tipo de modulo
    * en particular.
    */
   public function editModuleAction()
   {
      // Como no se de que clase sera el modulo, tengo que cargar todos los
      // modulos para poder hacer la consulta.
      YuppLoader::loadModel(); // Carga todo el Model de la aplicacion CMS

      // El problema es que los ids de los modulos no son unicos entre si, ya que 
      // distintas clases de modulos se guardan en distintas tablas, y el id es
      // unico por tabla.
      
      // TODO: verificar que viene id
      $module = Module::get($this->params['id']);
      
      // Si se quiere salvar...
      if (isset($this->params['doit']))
      {
         $module->setTitle($this->params['title']);
         $module->setShowContainer( isset($this->params['showContainer']) );
         $module->setStatus($this->params['status']);
         
         $setSIAP = isset($this->params['showInAllPages']);
         
         // Si tengo que ocultar los modulos de todas las paginas
         if ($module->getShowInAllPages() && !$setSIAP)
         {
            // Quita de todas las paginas
            // FIXME: esta no deberia quitar el modulo, deberia solo apagar la bandera.
            // Deberia haber otra accion que se encargue de sacar un modulo de todas las paginas.
            // El problema es que aca no deberia sacar el modulo de la pagina actual, y si se saca
            // deberia marcar como deleted para que la lista de huerfanos lo vea, sino se pierde en el limbo.
            //
            // Mas abajo se setea la bandera en el nuevo valor.
            
            /*
            // Busca las zonas en la que esta el modulo ahora, para luego mostrar
            // el modulo en las zonas que se llamen igual de las demas paginas
            // que no tengan el modulos.
            $dal = new DAL('cms2');
            $res = $dal->sqlQuery('SELECT owner_id FROM cms_page_zone_modules_cms_module WHERE ref_id='. $module->getId());
            
            // Recorro cada PageZone y voy sacando el modulo.
            foreach ($res as $r)
            {
               $pz = PageZone::get($r['owner_id']);
               $pz->removeFromModules($module); // Ya actualiza la base
            }
            */
            
            // TODO: tendria que marcar algo de que el modulo NO SE MUESTRA EN NINGUNA PAGINA!
            // Esto seria tambien que la consulta SQL de arriba devuelve 0 registros.
         }
         // Si tengo que poner los modulos en todas las paginas
         else if (!$module->getShowInAllPages() && $setSIAP)
         {
            // Agrega en todas las paginas que no lo tenian
            
            // Busca las zonas en la que esta el modulo ahora, para luego mostrar
            // el modulo en las zonas que se llamen igual de las demas paginas
            // que no tengan el modulos.
            $dal = new DAL('cms2');
            $res = $dal->sqlQuery('SELECT owner_id FROM cms_page_zone_modules_cms_module WHERE ref_id='. $module->getId());

            /*
             * array(
             *   0 => array(
             *     owner_id => x
             *     name     => y
             *   )
             *   1 => array(
             *     owner_id => x
             *     name     => y
             *   )
             *   2 => ...
             * )
             */
             
            //print_r($res);
            
            // TODO: si se elimina un modulo de una pagina, y ese se muestra en todas las paginas,
            // le deberia preguntar al usuario si quiere eliminarlo de todas las paginas.
            // Si dice que no, se deberia eliminar SOLO de la pagina actual, y desmarcar el modulo como que
            // se muestra en todas las paginas. En este caso el modulo se mostraria en todas menos una.
            // Hay que tenermo en cuenta. ***
            
            
            // CHECK: deberia haber una sola zona con ese modulo!
            //if (count($res)>1) throw new Exception('deberia haber una sola zona con ese modulo!');
            
            // Obtengo la zona de la pagina donde se esta mostrando el modulo actualmente.
            // Por lo comentado antes *** podria estar mostrandose en varias paginas,
            // pero no en todas, o sea que pueden haber varios ids de zonas en $res.
            $zone_id = $res[0]['owner_id'];
            $current_zone = PageZone::get($zone_id);
            $zoneName = $current_zone->getName();
            
            // en la tabla de join no tengo name..
            //$zoneName = $res[0]['name']; // Me ahorro una consulta pidiendo el name de la zona
            
            
            // FIXME: el problema es que pueden haber zonas definidas en el layout
            // pero que aun no han sido creadas en la pagina. Entonces si busco por
            // PageZone, solo me aparecen las zonas creadas. Para las demas paginas,
            // deberia crear tambien la zona.
            
            
            // Pregunta: cuando hay que crear las zonas de una pagina con respecto al layout activo?
            // Seria cuando se establece el nuevo layout? 
            // Tambien cuando se crea una nueva pagina?
            // O solo cuando quiero poner un modulo en la zona del layout que todabia no esta en la pagina?
            // Si hago las 2 primeras, la tercera no es necesaria.
            // Cuando se cambia de layout, ademas deberia borrar las zonas del layout anterior
            // que no tienen el mismo nombre en el layout actual.
           

            // ==================================================
            // FIXME: esto obtiene todas las zonas del mismo nombre en todas las paginas, pero
            //        pueden haber paginas que todavia no crearon esa zona, y que para agregar
            //        el moulo habria que crearla.
            
            // Necesito el NOT IN de los ids de las paginas porque son las paginas que no tienen la zona.
            //$pagesWithoutZone = $dal->sqlQuery('SELECT owner_id FROM cms_page_zones_cms_page_zone pzns, cms_page_zone pz WHERE pzns.ref_id=pz.id AND pz.name='. $zoneName);
            
            // Quiero los ids de las paginas
            // Que no estan en..
            //   Las paginas que tienen una zona que se llama $zoneName
            //Logger::getInstance()->setFile("logger_edit_module.txt");
            //Logger::getInstance()->on();
            
            /*
            $pagesWithoutZone = $dal->sqlQuery(
            "SELECT id FROM cms_page
             WHERE id NOT IN (
               SELECT owner_id
               FROM cms_page_zones_cms_page_zone pzns, cms_page_zone pz
               WHERE pzns.ref_id=pz.id AND pz.name='$zoneName'
             )");
            */
            
            //Logger::struct( $pagesWithoutZone );
            //Logger::getInstance()->off();
            // ==================================================
            
            
            // Zonas con el mismo nombre que la zona donde se esta mostrando actualmente el modulo.
            $zones = PageZone::findBy(
                       Condition::EQ(PageZone::TABLE, 'name', $zoneName), //$current_zone->getName()),
                       new ArrayObject()
                     );
            
            
            // Quiero un array con los ids de las zonas que ya tienen el modulo
            $zoneIds = array();
            foreach ($res as $r)
            {
               $zoneIds[] = $r['owner_id'];
            }
            
            //echo 'zoneIds';
            //print_r($zoneIds);
            
            // Para cada zona, me fijo:
            //  1. Que la zona no sea la actual
            //  2. Que el modulo no esté ya en esa zona (TODO)
            foreach ($zones as $zone)
            {
               // Agrega el modulo a cada zona, menos a la zona en la que ya esta.
               //if ($zone_id != $zone->getId()) // No lo agrego a la zona que ya esta
               
               // Si el modulo no esta en la zona (me fijo contra los ids de las zonas donde si esta el modulo).
               if (!in_array($zone->getId(), $zoneIds))
               {
                  $zone->addToModules( $module );
                  
                  //echo 'agrega a zona '. $zone->getId() .'<br/>';
                  
                  // FIXME: al salvar la zona, se salva el modulo (y el modulo ya se salva al final). Es sacarle el belongsTo.
                  // Ademas si guarda aca el modulo, se deberian limpiar los dirtys, y en los proximos intentnos no deberia
                  // guardar por estar limpia, pero se ve que en el guardado en cascada no se limpian los dirtys! 
                  if ( !$zone->save() ) print_r($zone->getErrors());
               }
            }
         }
         
         
         // Setea la bandera de que se muestra en todas las paginas
         $module->setShowInAllPages( $setSIAP ); // TODO: si es true, tengo que crear el modulo en las demas paginas
         
         
         if (!$module->save()) print_r($module->getErrors());
         
         // No vuelve porque es una ventana modal!
         // TODO: devolver algo de estado, como el modulo que se cambio, la pagina, o si salio todo bien o no.
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "class":"'.$module->getClass().'", "moduleId":"'.$this->params['id'].'"}');
         //return $this->renderString('Hecho');
         
         // Vuelve a la pagina en la que estaba
         //return $this->redirect(array('action'=>'displayPage', 'params'=>array('pageId'=>$this->params['pageId'])));
      }
      
      return array('module'=>$module);
      
   } // editModule
   
   /**
    * Actualiza el orden de los modulos de una pageZone, cuando se mueve
    * un modulo de una pageZone a otra o cambia el orden dentro de la misma
    * pageZone.
    * Esta accion sera invocada mediante ajax.
    */
   public function updateModulesAction()
   {
      Logger::getInstance()->pm_log("updateModulesAction1 -------------------------------- --- ---");
      /*
      $log = '';
      foreach ($this->params as $key => $val)
      {
         if (is_array($val))
         {
            $log .= $key .'=';
            foreach ($val as $val1) $log .= $val1 .', ';
         }
         else
           $log .= $key .'='. $val .', ';
      }
      return $this->renderString($log);
      */
      
      $pageZone = PageZone::getByPageAndName($this->params['pageId'], $this->params['zone']);
      
      //Logger::getInstance()->pm_log('pageZone:   id: '.$pagezone->getId() .', name: '.$this->params['zone']);
      
      // Si la pagina no tiene la zona que esta definida en el layout actual, la creo
      if ($pageZone == NULL)
      {
         // crea la zona y la socia a la pagina
         $page = Page::get($this->params['pageId']);
         $pageZone = new PageZone(array('name'=>$this->params['zone'], 'page'=>$page));
         $page->addToZones($pageZone);
         $pageZone->save();
         $page->save(); // Esto NO guarda la zona en la pagina
      }
      
      
      // Saco los modulos actuales
      foreach ($pageZone->getModules() as $module)
      {
         // Hay un problema al querer quitar modulos de la relacion.
         // En la tabla de join se guarda como ref_id el id de la superclase
         // Modulo, no el id de la instancia que puede ser una subclase de
         // Modulo. Y al querer eliminar, usa el id de la instancia especializada
         // no el id del modulo, por lo que en la tabla de join no encuentra
         // cual es el registro a elminar.
         // Sol 1: que remove se de cuenta su lo que quiero remover es una
         //        instancia especializada o no, y que si no lo es, se use
         //        el id de la superclase de nivel 1 (podrian haber varios
         //        niveles de herencia, por eso no uso el super_id directo).
         // Sol 2: que se de cuenta de que se esta usando una clase especializada
         //        al hacer addTo, entonces que guarde el id de la especializada
         //        como ref_id en la tabla de join, en lugar de usar el id de la
         //        superclase de nivel 1.
         
         // FIXME:
         // Ver PM desde linea 193
         
         $pageZone->removeFromModules($module);
      }
      
      // Pongo los modulos nuevos, en el orden correcto
      // TODO: Optimizacion si el modulo ya fue cargado en los modulos de la pagezone,
      //       lo puedo guardar antes de hacer el removeFrom, en lugar de cargarlo de
      //       nuevo de la base. Ojo, si fue cargado, deberia estar en el cache de objetos,
      //       entonces no lo carga de nuevo de la base.
      $i = 0;
      if (isset($this->params['module_ids']) && is_array($this->params['module_ids']) && count(array_filter($this->params['module_ids']))>0) // Sin chequeo, si viene un string vacio o un array con un elemento vacio, falla el eval.
      {
         //print_r($this->params['module_ids']);
         foreach ($this->params['module_ids'] as $moduleId)
         {
            //eval( '$module = '. $this->params['module_classes'][$i] .'::get($moduleId);' ); // Si hiciera Module::get($id), id deberia ser el de Module, no el de MenuModule o de HtmlModule.
            $module = Module::get($moduleId);
            $pageZone->addToModules($module);
            $i++;
         }
      }
      
      //print_r($pagezone); // Quiero ver si saco el modulo de la pagezone
      
      if (!$pageZone->save())
      {
         //print_r($pageZone->getErrors()); // TODO poner los errores en el json
         header('Content-type: application/json');
         return $this->renderString('{"status":"error"}');
      }
      
      //return $this->renderString('fin update');
      header('Content-type: application/json');
      return $this->renderString('{"status":"ok"}');
      
   } // updateModules
   
   
   /**
    * Devuelve el HTML generado para el contenido de una zona de una pagina determinada.
    * Se llamará mediante AJAX cuando se detecten cambios en el contenido de una zona,
    * para actualizar con lo que haya en la base de datos, sin necesidad de recargar
    * toda la página de nuevo.
    * 
    * pageId identificador de la pagina
    * zone nombre de la zona del layout
    */
   public function zoneContentAction()
   {
      // TODO
      // return $this->renderTemplate(...);
      // Es necesario esta accion?
      // o con obtener el contenido de un modulo (en lugar de toda la zona) alcanza?
      
      // Codigo parecido al displayPage.view.php
      // TODO: ver que los modulos pueden requerir cargar/ejecutar javascript
      //   Esto creo que lo resolvi, aca dice lo de poner el JS en el header: http://www.webdeveloper.com/forum/showthread.php?t=138830
      //   pero el JS viene con el html mezclado, deberia procesar todo y sacar el JS, eso puede afectar cuando se ejecuta.
      
      $pageZone = PageZone::getByPageAndName( $this->params['pageId'], $this->params['zone'] );
      $zoneModules = $pageZone->getModules();
      
      $page = Page::get($this->params['pageId']);
      
      $content = '';
      $moduleClasses = array(); // Lista de clases de modulos de la zona para devolver, asi desde JS se puede cargar el JS de edit de cada modulo si es que no estaba cargado antes.
      foreach ($zoneModules as $module)
      {
        /* TODO: meter esto en un template...
        <div class="moduleContainer <?php echo $module->getClass(); ?>" id="<?php echo $module->getClass(); ?>__<?php echo $module->getId(); ?>"><!-- TODO: discutir si tengo que mostrar el container o no. -->
        
          <script type="text/javascript">
            // Agrega referencia a un modulo en la lista de librerias a cargar.
            libs['<?php echo $module->getClass(); ?>'] = false; // Lo pongo en true cuando cargue efectivamente.
          </script>
        
          <div class="moduleTopBar">
            <?php echo $module->getTitle(); ?>
            <div class="moduleActions">
              <a href="<?php echo h('url', array('action'=>'editModule', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId())); ?>" alt="configuracion" class="edit_module"><?php echo h('img', array('app'=>'cms2', 'src'=>'settings.gif')); ?></a>
              <a href="<?php echo h('url', array('action'=>'removeModule', 'class'=>$module->getClass(), 'moduleId'=>$module->getId(), 'pageId'=>$page->getId())); ?>" alt="remover"><?php echo h('img', array('app'=>'cms2', 'src'=>'delete.gif')); ?></a>
            </div>
          </div>
          <div class="moduleContent">
            <?php
              Helpers::template(array('controller' => String::firstToLower($module->getClass()), // es el tipo de modulo, como news o html
                                      'name' => 'displayModule',
                                      'args' => array('module'=>$module, 'page'=>$page, 'mode'=>'edit') // podria pasarle mas cosas como page, pageZone, etc.
                                     ));
            ?>
          </div>
        </div>
        */
        
         // Pongo la class como key para usar el array como un set, necesito cada clase una sola vez.
         $moduleClasses[$module->getClass()] = 1;
        
        
         // -------------------------------------------------------------------------------------------
         // Codigo basado en moduleContainerAction
        
         // El directorio donde esta el template es igual que la clase con la primer letra en minuscula.
         // p.e. views/htmlModule/
         $templateDir = String::firstToLower($module->getClass());
         YuppLoader::load("cms2.model.$templateDir", $module->getClass());
      
         // tengo que usar displayModule.template
         //$module = Module::get($this->params['id']);
         //$page = Page::get($this->params['pageId']); // la cargo arriba
      
         // El template generico module/displayModule se encarga de mostrar el contenedor
         //return $this->renderTemplate("../module/displayModule", array('module'=>$module, 'page'=>$page, 'mode'=>'edit')); // Supongo que si llama a esta accion es porque estoy editando (modo=edit)
      
         // El render template a mano
         $params = array();
         $params['name'] = '../module/displayModule'; // Nombre del template
         $params['args'] = array('module'=>$module, 'page'=>$page, 'mode'=>'edit', 'zone'=>$pageZone); // Supongo que si llama a esta accion es porque estoy editando (modo=edit)
         
         // Agarro la salida para luego guardarla en $zoneContent
         ob_start();
         
         Helpers::template($params); // No retorna, renderea directamente
         
         // Capturo contenido generado
         $content .= ob_get_clean();
      }
      
      
      // El HTML rompe el JSON, tengo que encodear
      // TODO: verificar que json_encode esta habilitada
      $content = json_encode($content);
      

      // TODO: array2JSON
      $jsonClasses = '[';
      if (count($moduleClasses) > 0)
      {
         foreach (array_keys($moduleClasses) as $i => $class)
         {
            $jsonClasses .= '"'.$class.'", ';
         }
         $jsonClasses = substr($jsonClasses, 0, -2);
      }
      $jsonClasses .= ']';

//      return $this->renderString($jsonClasses);
      
      header('Content-type: application/json');
      return $this->renderString('{"status":"ok", "moduleClasses":'.$jsonClasses.', "zone":"'.$this->params['zone'].'", "content":'.$content.'}');
   }
   
   
   /**
    * Obtiene el HTML de subpaginas de la pagina con id pageId.
    * Se usa luego de crear una subpagina de una pagina para actualizar el area de subpaginas con la nueva subpagina creada.
    * Siempre se llama por ajax.
    */
   public function pageSubpagesAction()
   {
      $page = Page::get($this->params['pageId']);
      
      YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
      $html = CmsHelpers::subpages(array('page'=>$page));
      
      // Se usa addslashes porque el html tiene comillas dobles y rompe el json.
      // En lugar de tirar json tiro html derecho
      return $this->renderString(substr($html, 22, -6)); // Saco <div class="subpages"> del inicio y </div> del final porque en la pagina ya esta esa tag.
   }
   
   
   /**
    * Dada una clase de modulo y un id, devuelve el HTML generado para el contenido del modulo.
    * Se usara para actualizar por ajax el contenido de un modulo cuando este cambie.
    */
   public function moduleContentAction()
   {
      // El directorio donde esta el template es igual que la clase con la primer letra en minuscula.
      // p.e. views/htmlModule/
      $templateDir = String::firstToLower($this->params['class']);
      
      YuppLoader::load("cms2.model.$templateDir", $this->params['class']);
      
      // tengo que usar displayModule.template
      $module = Module::get($this->params['id']);
      $page = Page::get($this->params['pageId']);
      $pageZone = PageZone::getByPageAndName( $this->params['pageId'], $this->params['zone'] );
      
      // displayModule necesita la zona.
      return $this->renderTemplate("../$templateDir/displayModule", array('module'=>$module, 'page'=>$page, 'mode'=>'edit', 'zone'=>$pageZone)); // Supongo que si llama a esta accion es porque estoy editando (modo=edit)
   }
   
   
   /**
    * Igual que la anterior, pero devuelve el modulo completo, contenedor y contenido.
    */
   public function moduleContainerAction()
   {
      // El directorio donde esta el template es igual que la clase con la primer letra en minuscula.
      // p.e. views/htmlModule/
      $templateDir = String::firstToLower($this->params['class']);
      
      YuppLoader::load("cms2.model.$templateDir", $this->params['class']);
      
      // tengo que usar displayModule.template
      $module = Module::get($this->params['id']);
      $page = Page::get($this->params['pageId']);
      $pageZone = PageZone::getByPageAndName( $this->params['pageId'], $this->params['zone'] );
      
      // El template generico module/displayModule se encarga de mostrar el contenedor
      return $this->renderTemplate("../module/displayModule", array('module'=>$module, 'page'=>$page, 'mode'=>'edit', 'zone'=>$pageZone)); // Supongo que si llama a esta accion es porque estoy editando (modo=edit)
   }
   
   
   // ================== OPERACIONES SOBRE LAYOUT ================== //
   
   /**
    * Crea un layout. Esta accion vendria del editor de layouts,
    * y tendria la informacion del layout y sus zonas.
    */
   public function createLayoutAction()
   {
      // TODO: para cuando el layout se pueda diseniar desde la GUI como en el CMS anterior con jQuery UI.
   }
   
   /**
    * Envia el layout al editor para modificar su informacion
    * y sus zonas, tanto en ubicacion como agregando o quitando
    * zonas. Si es el layout usado en el sitio, se va a tener
    * que revisar si hay modulos en las zonas eliminadas, y estos
    * serian asignados a alguna otra zona de forma automatica.
    * TODO: En el caso de la eliminacion de zonas, se le podria
    * preguntar al usuario en que zona quiere colocar los modulos
    * que estan en la zona eliminada.
    */
   public function editLayoutAction()
   {
      
   }
   
   /**
    * Exporta un layout en un formato que permita ser instalado
    * en otras instancias de Yupp CMS.
    */
   public function exportLayoutAction()
   {
      
   }
   
   /**
    * Instala un layout exportado. Lo que hace es tomar el formato
    * exportado, procesarlo y crear los registros en la base.
    * Luego, el layout aparecera como una opcion para usar en el
    * sitio, y se podra ejecutar la accion userLayout con el.
    */
   public function installLayoutAction()
   {
      
   }
   
   /**
    * Establece el layout a usar en el sitio.
    */
   public function useLayoutAction()
   {
      // TODO: pueden haber modulos en zonas de la pagina que esten
      // en el layout que voy a sacar pero que no esten en el layout
      // que seleccione', deberia recorrer para verificar esto, y
      // todo lo que no encuentre, va para la zona 'content' que
      // es obligatoria. CUIDADO! tengo que recorrer todas las zonas de todas las paginas!
      
      // FIXME:
      // Esto es facil de implementar reutilizando la recorrida que se hace abajo:
      // 1. Necesito todas las zonas del layout anterior
      // 2. Necesito todas las zonas del layout nuevo
      // 3. Veo cuales zonas no estas en el layout nuevo
      // 4. En el recorrido de abajo, de las zonas que no estan en el layout nuevo, 
      //    saco los modulos de las zonas que no existen, las elimino, 
      //    y pongo los modulos en una zona por defecto (ej. content)
      //    Una mejora es mostrarle esto al usuario y que diga donde quiere poner los modulos de las zonas que no existen.
      
      $layout = Layout::getActive();
      $layout->setActive(false);
      $layout->save();
      
      $layout = Layout::get($this->params['id']); // identificador del layout que quiero activar
      $layout->setActive(true);
      $layout->save();
      
      $pages = Page::listAll(new ArrayObject());
      
      // Recorre las zonas del nuevo layout y las paginas actuales,
      // verificando si la zona esta en la pagina o no. Sino esta, la crea.
      foreach ($pages as $page)
      {
         $savePage = false; // Optimizacion para no guardar las paginas si no se cambian, es necesario? creo q sino se modifica, no se guarda por dirty.
         foreach ($layout->getZones() as $zone)
         {
            if (!$page->hasZone($zone->getName()))
            {
               $page->addToZones( new PageZone(array('name'=>$zone->getName(), 'page'=>$page)) );
               $savePage = true;
            }
         }
         if ($savePage)
            $page->save(); // TODO: verif error
      }
      
      return $this->redirect(array('action'=>'displayPage', 'params'=>array('pageId'=>$this->params['pageId'])));
   }
   
   // ================== OPERACIONES SOBRE LAYOUT ================== //
}

?>