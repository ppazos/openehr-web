<?php

global $_base_dir;

$m = Model::getInstance();

// Pagina a mostrar, tiene a que sitio pertenece
$page = $m->get('page');
//$layoutNames = $m->get('layoutNames'); // TODO: poner en helpers para no tener que pasarle el modelo desde el controller.
$layout = Layout::getActive();

// Para cargar los modulos, necesito las clases del modelo de modulos cargados,
// como a priori no se cuales tengo que cargar, necesitaria cargar todas las clases.
// Por ahora cargo todo y luego veo como cargar solo el modelo del cms2.
YuppLoader::loadModel(); // Carga todo el Model de la aplicacion CMS
YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <?php include('./apps/cms2/config/cms_config.php'); ?>
    <title><?php echo $page_title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'main')); ?>
    <style type="text/css">
      body.deleted {
        background: url("<?php echo $_base_dir ?>/apps/cms2/images/deleted.gif"); /* no-repeat; */
      }
      #modal_close {
        background: url("<?php echo $_base_dir ?>/apps/cms2/images/modal-close.png") no-repeat;
      }
    </style>
    <!-- CSS del layout -->
    <?php
       $zoneContent['page'] = $page; // Le agrego la pagina actual a los params del template.
       // TODO: falta pasarle (o que el template chequee) si el usuario logueado tiene permisos
       //       para ejecutar las acciones que muestra el template (como el edit de HtmlModule).
       $zoneContent['mode'] = 'edit';
       
       // Se incluye el template correspondiente a la skin para que muestre el contenido como quiera
       Helpers::template(array('name' => 'css',
                               'path' => '../../skins/'.$layout->getName(),
                               'args' => $zoneContent // podria pasarle mas cosas como page, pageZone, etc.
                              ));
    ?>
    
    <!-- TODO: algunas se incluyen siempre, otras solo en el edit -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-ui-1.8.17.sortable.min')); ?><!-- para el sortable -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-center-min')); ?><!-- para centrar la ventana modal -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.ba-resize.min')); ?><!-- para poder saber cuando un elemento es resizeado, tambien es para la modal, para poder centrarla cuando el contenido le cambia de tamanio -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-ui-1.8.13.effects.fade.min')); ?><!-- para mostrar la modal con fade -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    
    <!-- solo para MenuModule -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'superfish-1.4.8/js/superfish')); ?>
    <?php
      $pageZoneIds = '';
      foreach ( $layout->getZones() as $zone ) $pageZoneIds .= '#'.$zone->getName().', ';
      $pageZoneIds = substr($pageZoneIds, 0, -2);
    ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ModuleObjectCache')); ?>
    <script type="text/javascript">
      
      var modal;
      
      // ----------------------------------------------------
      // Cache de objetos JS que los modulos pueden necesitar en
      // algun momento para mostrarse correctamente.
      var cache = new ModuleObjectCache();

      // Librerias javascript a cargar
      var libs = new Array();

      // Debe ser variable global sino no se puede invocar desde modal.
      var loadLib;
    
      $(document).ready( function() {
        
        // ----------------------------------------------------    
        // Dado el nombre de un modulo, incluye el javascript para editar ese modulo.
        // La inclusion verifica que no se haya incluido antes ese modulo, asi no se incluye 2 veces.
        //
        // Lo tengo que definir en el onload, sino me dice que scripts.length es undefined.
        // Poniendolo aca sigue diciendo lo de undefined, pero en el log dice que esta ok, asi que lo dejo.
        //
        loadLib = function(moduleClass)
        {
          var head = document.getElementsByTagName("head")[0];
          var scripts = head.getElementsByTagName("script");
            
          // console.log(moduleClass[0].toLowerCase()+moduleClass.substring(1)); // FirstToLower
          // <?php $_base_dir .'/apps/cms2/javascript/'. String::firstToLower('moduleClass') .'/edit.js'; ?>
            
          var _moduleClass = moduleClass[0].toLowerCase()+moduleClass.substring(1);  // FirstToLower
          var scriptRef = '<?php echo $_base_dir .'/apps/cms2/javascript/'; ?>' + _moduleClass + '/edit.js';
            
          // Ojo: scriptRef es relativa y scripts[i].src es absoluta! siempre da distinto...
          console.log('scriptRef: ' + scriptRef); // /YuppPHPFramework/apps/cms2/javascript/htmlModule/edit.js
          //console.log(scripts);
          //console.log(scripts.length);
            
          var incluir = true;
          for (var i=0; i<scripts.length; i++)
          {
              // El src puede ser null si el script se define en la misma pagina
              //console.log('ya esta cargado?: '+scripts[i].src);                                  // http://localhost/YuppPHPFramework/apps/cms2/javascript/htmlModule/edit.js
              //console.log('ya esta cargado?: '+scripts[i].attributes.getNamedItem("src"));       // [object Attr]
              //console.log('ya esta cargado?: '+scripts[i].attributes.getNamedItem("src").value); // /YuppPHPFramework/apps/cms2/javascript/htmlModule/edit.js
    
              var srcAttr = scripts[i].attributes.getNamedItem("src");
    
              //if (scripts[i].src == scriptRef) // Esto compara url absoluta con una relativa, siempre da false
              if (srcAttr != undefined && srcAttr.value == scriptRef) // srcAttr.value es la url relativa
              {
                incluir = false;
                break;
              }
          }
            
          if (incluir)
          {
            script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = scriptRef;
            head.appendChild(script);
              
            console.log('incluye: ' + scriptRef);
              
            libs[moduleClass] = true; // Marca de que se cargo
          }
          else
          {
            console.log('no incluye porque ya esta cargado: ' + scriptRef);
          }
        };
        
        
        // Dejo la modal guardada den variable global para acceso desde
        // el document del iframe usando parent.modal
        modal = $('#modal').modal();
        
        // TEST
        //console.log(cache); // OK!
        
        // ==============================================
        // Carga JS de cada modulo, se carga aca porque me asegura de que todos los modulos
        // hicieron referencia a su JS porque el dom esta ready.
        // lib es la key en el array libs, asi aseguro que cargo cada lib una sola vez.
        for (var lib in libs)
        {
           loadLib(lib);
        }
        // ==============================================

        <?php if ($m->flash('message')) : ?>
          $('#global_feedback').feedback('<?php echo $m->flash('message'); ?>');
          //$('#global_feedback').feedback('un mensaje!!!!!', {'hideTime': 1000}); // ok 1 segundo de duracion.
        <?php endif; ?>

        // FIXME: esto se aplica a todos los MenuModule, pero se deberia poner
        //        como configuracion de cada menu si se muestra desplegable o no.
        // Hace que los menus sean desplegables
        // FIXED: si modifico el contenido de un menu, en la actualizacion, no se aplica el superfish sobre los botones con subpaginas.
        //$('ul.menu').superfish(); // FIXME: la aplicacion de superfish a un menu deberia ser configurable.

        // Menu desplegable para la topbar:
        $('#topbar > ul').superfish({ 
          delay:       600,                             // one second delay on mouseout 
          animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
          speed:       'fast',                          // faster animation speed 
          autoArrows:  false                            // disable generation of arrow mark-up 
        });
        
        
        // Sortable para los modulos en las zonas
        $( "<?php echo $pageZoneIds; ?>" ).sortable({
        
          connectWith: ".zone", // Se pueden ordenar los modulos entre las zonas
          
          // se llama por cada div afectada, o sea 2 veces (de la que saco y en la que pongo).
          // llama una sola vez si el modulo se mueve en la misma zona.
          update: function(event, ui) {

            var zone = event.target.id;
            var module_ids = new Array(); // Identificadores de modulos en la zona
            var module_classes = new Array();
            
            //console.log(event);
            //console.log(this); // zona
            //console.log(event.target); // zona (igual a this)
            
            $(event.target).children().each( function(idx, module) {
              
              //console.log(this); // module
              //console.log(idx);
              //console.log(module);
              //console.log(module.id); // HTMLModule__5
              
              // CUIDADO: para que esto funcione, en las zonas SOLO debe haber modulos, no puede haber otro contenido.

              // module.id = ModuleClass__id por ejemplo: HtmlModule__23
              //modules.push( module.id.substring(7) ); // me quedo solo con el id del modulo
              data = module.id.split("__");
              
              if (data.length == 0) alert('Se encuentra contenido que no es modulo!');
              
              module_classes.push( data[0] );
              module_ids.push( data[1] );
              
              //alert(module.id.substring(7));
            });
            
            //console.log(module_classes);
            //console.log(module_ids);
            
            $.get("<?php echo h('url', array('action'=>'updateModules')); ?>", { 'pageId': '<?php echo $page->getId(); ?>', 'zone': zone, 'module_classes[]': module_classes, 'module_ids[]': module_ids} );
          }
          
        }).disableSelection();
        
        
        /*
        // ==========================================================================
        // Para centrar pantalla modal cuando el contenido le cambia de tamanio
        //  1. Observar cuando cambia de tamanio
        //  2. Centrarla
        $('#modal').resize(function(evt){ // http://benalman.com/projects/jquery-resize-plugin
          
          $('#modal').center(); // http://andreaslagerkvist.com/jquery/center/
        });
        */
        
        
        // Clase generica para asociar el link simple por ajax a la carga de la modal.
        // Todos los demas links deberian corregirse para tener un solo handler porque el codigo es el mismo.
        // http://code.google.com/p/yupp-cms/issues/detail?id=36
        $('a.simple_ajax_link').live('click', function(evt) {
          
          // Muestra el creador de modulos en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link 
          return false;
        });
        
        
        // ================================================================================
        // Asocia handler para mostrar ventana modal para crear un modulo
        $('a#new_module').click( function(evt) {
          
          // Muestra el creador de modulos en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link 
          return false;
        });
        
        
        // =============================== MODULE ACTIONS ==================================
        // Asocia handler a la edicion de la configuracion general de los modulos
        $('a.edit_module').live('click', function(evt) { // Si se agregan modulos huerfanos, esto se tiene que bindear, por eso el live
            
          // Muestra el editor del modulo en el iframe de la pantalla modal
          modal.modal('load', this.href);
          
          // Para que no cargue la pagina al hacer clic en el link 
          return false;
        });
        
        
        // Asocia handler a la edicion de la configuracion general de los modulos
        $('a.remove_module').live('click', function(evt) { // Si se agregan modulos huerfanos, esto se tiene que bindear, por eso el live
          
          if (confirm("\u00BFRelmente desea remover el modulo de esta zona?"))
          {
            // Llamar al servidor para remover
            // Quitar el modulo de la zona con js
            // Muestra feedback
              
            $.ajax({
              url: this.href,
              success: function (res, status, response) {
                
                // res: {status (ok, error), class (ModuleClass), moduleId}
                console.log(res);
                  
                if (res.status == 'ok')
                {
                  // Quita el modulo del dom (ej: id="HTMLModule__11")
                  $('#'+res.class+'__'+res.moduleId).remove();
                    
                  // Feedback al usuario
                  $('#global_feedback').feedback('El modulo ha sido removido con exito');
                }
                else
                {
                  // TODO
                }
              }
            });
          }
          
          // Para que no cargue la pagina al hacer clic en el link 
          return false;
        });
        // =============================== MODULE ACTIONS ==================================
        
        
        // ================================================================================
        // Listar modulos huerfanos en la ventana modal para poder elegir y agregar en las zonas de la pagina actual
        $('a#list_orphan_modules').click( function(evt) {
        
          // Quiero mostrar la modal sin poner JS en la pagina que se muestra dentro de la modal
          modal.modal('onload', function() {
      
            // Actualiza dimensiones y centrado
            modal.modal('update');
            
            // Desregistro el evento sino todo cambio que se haga en el iframe.src ejecuta esto.
            modal.modal('reset');
          });
          
          
          // Muestra el editor del modulo en el iframe de la pantalla modal
          modal.modal('load', this.href);
          
          // Para que no cargue la pagina al hacer clic en el link
          return false;
        });
        
        
        // ================================================================================
        // Asocia handler para mostrar ventana modal para crear una nueva pagina
        $('a#new_page').click( function(evt) {

          // Muestra el creador de paginas en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link
          return false;
        });
      
        // Asocia handler para mostrar ventana modal para crear una subpagina de la pagina actual
        $('a#new_subpage').click( function(evt) {
          
          // Muestra la creacion de subpagina en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link
          return false;
        });
        
        
        // Confirmacion de eliminacion de la pagina,
        // deja ejecutar el link porque debe recargar la pagina eliminada.
        $('a#delete_page').click( function(evt) {
          
          return confirm("\u00BFRelmente desea eliminar esta pagina?");
        });
        
        // editar la pagina actual, ya se encarga de actualizar la modal cuando se
        // carga el iframe (no se hace en la vista editPage)
        $('a#edit_page').click( function(evt) {
        
          // Quiero mostrar la modal sin poner JS en la pagina que se muestra dentro de la modal
          modal.modal('onload', function() {
      
            // Actualiza dimensiones y centrado
            modal.modal('update');
            
            // Desregistro el evento sino todo cambio que se haga en el iframe.src ejecuta esto.
            modal.modal('reset');
          });
          
          // Muestra el editor de la pagina en el iframe de la pantalla modal
          modal.modal('load', this.href);
          
          // Para que no cargue la pagina al hacer clic en el link 
          return false;
        });
        
        // Para gestion de usuarios
        $('a#login_btn').click( function(evt) {

          // Muestra el creador de paginas en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link
          return false;
        });
        $('a#register_btn').click( function(evt) {

          // Muestra el creador de paginas en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link
          return false;
        });
        $('a#showprofile_btn').click( function(evt) {
          
          // Quiero mostrar la modal sin poner JS en la pagina que se muestra dentro de la modal
          modal.modal('onload', function() {
      
            // Actualiza dimensiones y centrado
            modal.modal('update');
            
            // Desregistro el evento sino todo cambio que se haga en el iframe.src ejecuta esto.
            modal.modal('reset');
          });
          
          // Muestra la creacion de subpagina en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link
          return false;
        });
        $('a#logout_btn').click( function(evt) {
          
          // Hace el logout por ajax
          $.ajax({
            url: this.href,
            success: function (json, status, response) {
            
              if (json.status == "ok")
              {
                // Recarga la pagina actual para ocultar las funcionalidades disponibles
                // solo para los que estan logueado. Es lo mismo que se hace para el login.
                // Necesito saber si ya hay parametros o no para agregar el flash.message
                var firstChar = '&';
                if (parent.location.toString().indexOf('?')==-1) firstChar = '?';
                
                parent.location = parent.location + firstChar + 'flash.message='+ json.msg;
              }
              else
              {
                // TODO
              }
            },
            error: function(xhr, status, thrownError) { // TODO: hacer que este handler sea una funcion global porque se usa en 1000 lugares distintos.
              
              fb = $('#global_feedback');
            
              if(xhr.status==0)
              {
                fb.feedback('Parece desconectado, verifique su conexion a internet');
              }
              else if(xhr.status==404)
              {
                fb.feedback('Error, la direccion no existe');
              }
              else if(xhr.status==500)
              {
                fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
              }
              else if(status=='parsererror')
              {
                fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
              }
              else if(status=='timeout')
              {
                fb.feedback('Ocurrio un error, vuelva a intentar en unos momentos');
              }
              else
              {
                fb.feedback('Ocurrio un error '+ xhr.responseText);
              }
            }
          }); // ajax
          

          // Para que no cargue la pagina al hacer clic en el link
          return false;
          
        }); // logout
        
      }); // document ready
    </script>
  </head>
  <body class="edit<?php echo ($page->getDeleted())?' deleted"':''; ?>">
  
    <!-- para mostrar pantallas modales -->
    <div id="modal"></div>
  
    <?php $user = User::getLogged(); // para verificar permisos ?>
    
    <?php CmsHelpers::topbar($page, $layout); ?>
    
    <div id="global_feedback_wrapper">
      <span id="global_feedback"></span>
    </div>
    
    <?php CmsHelpers::loginBox($page); ?>

    <?php $zoneContent = array(); ?>
    <?php foreach ( $layout->getZones() as $zone ): ?>
      <?php
      $pageZone = PageZone::getByPageAndName($page->getId(), $zone->getName());
          
      //echo "quiero pageZone para pageId: ". $page->getId() ." y zona: ". $zone->getName() ."<br/>";
          
        // pageZone NO puede ser null, las zonas definidas en el layout DEBEN
        // estar en cada pagina como pageZones.
        // ¿porque? con este codigo sino hay un pageZone, no se genera ningun HTML de modulos, pero si la zona en la pagina.
          
      $zoneModules = array();
      if ($pageZone != NULL) $zoneModules = $pageZone->getModules();

      // =================================================================================
      // Agarro la salida para luego guardarla en $zoneContent
      ob_start();
      ?>  
      <?php foreach ($zoneModules as $module) : ?>

        <?php /*
         - No se verifican permisos porque para estar en edit se debe ser editor o admin, y siendo de estos roles se pueden ver todos los modulos.
         - En edit siempre se muestra el container
         */ ?>
        <!--
            el id de esta tag es lo que se usara para guardar el nuevo orden
            luego de hacer un movimiento por sortable, de un modulo a otra
            zona, o reordenando un modulo en la misma zona.
        -->
        <div class="moduleContainer <?php echo $module->getClass(); ?>" id="<?php echo $module->getClass(); ?>__<?php echo $module->getId(); ?>"><!-- TODO: discutir si tengo que mostrar el container o no. -->
        
          <script type="text/javascript">
            // Agrega referencia a un modulo en la lista de librerias a cargar.
            // TODO: luego de que se ejecuta, quitar del DOM
            libs['<?php echo $module->getClass(); ?>'] = false; // Lo pongo en true cuando cargue efectivamente.
          </script>
        
          <!-- FIXME: este es el mismo codigo que en module/displayModule.template.php -->
          <div class="moduleTopBar">
            <?php echo $module->getTitle(); ?>
            <div class="moduleActions">
              <a href="<?php echo h('url', array('action'=>'editModule', 'class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$page->getId())); ?>" alt="configuracion" class="edit_module"><?php echo h('img', array('app'=>'cms2', 'src'=>'settings.gif')); ?></a>
              <a href="<?php echo h('url', array('action'=>'removeModule', 'class'=>$module->getClass(), 'moduleId'=>$module->getId(), 'pageId'=>$page->getId(), 'zone'=>$zone->getName())); ?>" alt="remover" class="remove_module"><?php echo h('img', array('app'=>'cms2', 'src'=>'delete.gif')); ?></a>
            </div>
          </div>
          <div class="moduleContent">
            <?php
              Helpers::template(array('controller' => String::firstToLower($module->getClass()), // es el tipo de modulo, como news o html
                                      'name' => 'displayModule',
                                      'args' => array('module'=>$module, 'page'=>$page, 'mode'=>'edit', 'zone'=>$zone) // podria pasarle mas cosas como page, pageZone, etc.
                                     ));
            ?>
          </div>
        </div>
      <?php endforeach; ?>
      <?php
      // Contenido de la zona actual
      $content = ob_get_clean();
      $zoneContent[$zone->getName()] = $content;
      // =================================================================================
      ?>
    <?php endforeach; ?>
    <?php
       $zoneContent['page'] = $page; // Le agrego la pagina actual a los params del template.
       // TODO: falta pasarle (o que el template chequee) si el usuario logueado tiene permisos
       //       para ejecutar las acciones que muestra el template (como el edit de HtmlModule).
       $zoneContent['mode'] = 'edit';
       
       // Se incluye el template correspondiente a la skin para que muestre el contenido como quiera
       Helpers::template(array('name' => 'layout',
                               'path' => '../../skins/'.$layout->getName(),
                               'args' => $zoneContent // podria pasarle mas cosas como page, pageZone, etc.
                              ));
    ?>
    <div id="powered_by">
      <a href="https://code.google.com/p/yupp-cms" target="_blank"><img src="<?php echo $_base_dir; ?>/apps/cms2/images/yuppcms.png" /></a>
    </div>
  </body>
</html>