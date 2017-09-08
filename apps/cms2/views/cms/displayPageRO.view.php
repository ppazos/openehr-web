<?php

global $_base_dir;

$m = Model::getInstance();

// Pagina a mostrar, tiene a que sitio pertenece
$page = $m->get('page');
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
      #modal_close {
        background: url("<?php echo $_base_dir ?>/apps/cms2/images/modal-close.png") no-repeat;
      }
    </style>
    <!-- CSS del layout -->
    <script>
    <!-- redirect to https if not currently on the url -->
    var loc = window.location.href+'';
    var parser = document.createElement('a');
    parser.href = loc;
    
    if (parser.hostname != 'localhost' && !parser.hostname.startsWith('192') && loc.indexOf('http://')==0){
      window.location.href = loc.replace('http://','https://');
    }
    </script>
    <?php
       $zoneContent['page'] = $page; // Le agrego la pagina actual a los params del template.
       // TODO: falta pasarle (o que el template chequee) si el usuario logueado tiene permisos
       //       para ejecutar las acciones que muestra el template (como el edit de HtmlModule).
       $zoneContent['mode'] = 'show';
       
       // Se incluye el template correspondiente a la skin para que muestre el contenido como quiera
       Helpers::template(array('name' => 'css',
                               'path' => '../../skins/'.$layout->getName(),
                               'args' => $zoneContent // podria pasarle mas cosas como page, pageZone, etc.
                              ));
    ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'superfish-1.4.8/js/superfish')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    
    <!-- Necesarios para la modal -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-center-min')); ?><!-- para centrar la ventana modal -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.ba-resize.min')); ?><!-- para poder saber cuando un elemento es resizeado, tambien es para la modal, para poder centrarla cuando el contenido le cambia de tamanio -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-ui-1.8.13.effects.fade.min')); ?><!-- para mostrar la modal con fade -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ModuleObjectCache')); ?>
    
    <script type="text/javascript">
    
      // ----------------------------------------------------
      // Cache de objetos JS que los modulos pueden necesitar en
      // algun momento para mostrarse correctamente.
      var cache = new ModuleObjectCache();
    
      // ----------------------------------------------------      
      // Librerias javascript a cargar
      var libs = new Array();

      // Variable global que mantiene puntero a la modal
      var modal;
      
      // Debe ser variable global para poder ser invocada desde modal.
      var loadLib;
    
      // ----------------------------------------------------  
      $(document).ready( function() {
        
        // Dejo la modal guardada den variable global para acceso desde
        // el document del iframe usando parent.modal
        modal = $('#modal').modal();
        
        // Hace que los menus sean desplegables
        // FIXED: si modifico el contenido de un menu, en la actualizacion, no se aplica el superfish sobre los botones con subpaginas.
        //$('ul.menu').superfish(); // FIXME: la aplicacion de superfish a un menu deberia ser configurable.
        
        <?php if ($m->flash('message')) : ?>
          $('#global_feedback').feedback('<?php echo $m->flash('message'); ?>');
        <?php endif; ?>
    
        // Clase generica para asociar el link simple por ajax a la carga de la modal.
        // Todos los demas links deberian corregirse para tener un solo handler porque el codigo es el mismo.
        // http://code.google.com/p/yupp-cms/issues/detail?id=36
        $('a.simple_ajax_link').live('click', function(evt) {
          
          // Muestra el creador de modulos en el iframe de la pantalla modal
          modal.modal('load', this.href);

          // Para que no cargue la pagina al hacer clic en el link 
          return false;
        });
        
        
        // ===============================================================
        // Igual que en displayPage 
        //
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
        // ===============================================================
        
        
        // http://code.google.com/p/yupp-cms/issues/detail?id=7
        // Se necesitan los scripts de edit porque registran handlers
        // de clicks para cada modulo que necesito en el show.
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

        // ==============================================
        // Carga JS de cada modulo, se carga aca porque me asegura de que todos los modulos
        // hicieron referencia a su JS porque el dom esta ready.
        // lib es la key en el array libs, asi aseguro que cargo cada lib una sola vez.
        for (var lib in libs)
        {
           loadLib(lib);
        }
        // ==============================================
      });
    </script>
    
    <?php
      $pageZoneIds = '';
      foreach ( $layout->getZones() as $zone ) $pageZoneIds .= '#'.$zone->getName().', ';
      $pageZoneIds = substr($pageZoneIds, 0, -2);
    ?>
  </head>
  <body>
    <!-- para mostrar pantallas modales -->
    <div id="modal"></div>
    
    <div id="global_feedback_wrapper">
      <span id="global_feedback"></span>
    </div>
    
    <?php CmsHelpers::loginBox($page); ?>
    
    <?php $zoneContent = array(); ?>
    <?php foreach ( $layout->getZones() as $zone ): ?>
      <?php
      $pageZone = PageZone::getByPageAndName($page->getId(), $zone->getName());
      $zoneModules = array();
      if ($pageZone != NULL) $zoneModules = $pageZone->getModules();

      // Para verificar permisos de modulos
      $user = User::getLogged();

      // =================================================================================
      // Agarro la salida para luego guardarla en $zoneContent
      ob_start();
      ?>
      <?php foreach ($zoneModules as $module) : ?>
        <?php // echo $module->getStatus() .' '.$module->getId(); ?>
        <?php
        // Si es draft o disabled, solo se muestra para editores
        // en este caso no muestra ni siquiera el contenedor!
        if (in_array($module->getStatus(), array(Module::STATUS_DRAFT, Module::STATUS_DISABLED)))
        {
           // El usuario debe estar logueado y ser editor o admin (no puede ser null o USER)
           if ($user == NULL || $user->getUsertype()==User::TYPE_USER)
              continue;
        }
        ?>
        <script type="text/javascript">
          // Agrega referencia a un modulo en la lista de librerias a cargar.
          // TODO: luego de que se ejecuta, quitar del DOM
          libs['<?php echo $module->getClass(); ?>'] = false; // Lo pongo en true cuando cargue efectivamente.
        </script>
        <?php if ($module->getShowContainer()) : ?>
          <!--
            el id de esta tag es lo que se usara para guardar el nuevo orden luego de hacer un movimiento
            por sortable, de un modulo a otra zona, o reordenando un modulo en la misma zona.
          -->
          <div class="moduleContainer <?php echo $module->getClass(); ?>" id="<?php echo $module->getClass(); ?>__<?php echo $module->getId(); ?>"><!-- TODO: discutir si tengo que mostrar el container o no. -->
            <div class="moduleTopBar">
              <?php echo $module->getTitle(); ?>
            </div>
        <?php endif; ?>
         
        <?php // si es hidden, debe estar logueado
          if ( $module->getStatus() == Module::STATUS_HIDDEN && $user == NULL ) :
        ?>
          <div class="private_module_content">
            Contenido protegido para usuarios registrados, por favor
            <a href="<?php echo h('url', array('controller'=>'user', 'action'=>'login')); ?>" class="simple_ajax_link">ingrese</a>
            con su usuario y clave o 
            <a href="<?php echo h('url', array('controller'=>'user', 'action'=>'register')); ?>" class="simple_ajax_link">solicite ingreso</a> a la comunidad registrándose en el portal.
          </div>
        <?php else : ?>
          <div class="moduleContent <?php echo $module->getClass(); ?>">
            <?php Helpers::template(array('controller' => String::firstToLower($module->getClass()), // es el tipo de modulo, como news o html
                                          'name' => 'displayModule',
                                          'args' => array('module'=>$module, 'page'=>$page, 'mode'=>'show', 'zone'=>$zone) )); ?>
          </div>
        <?php endif; ?>
        <?php if ($module->getShowContainer()) : ?>
          </div>
        <?php endif; ?>
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
      // TODO: el modulo Menu y la muestra de las subpaginas, debe tambien considerar que estoy en modo read only, y generar los links acordes, no al modo edit.
      $zoneContent['mode'] = 'show';
    
      // Se incluye el template correspondiente a la skin para que muestre el contenido como quiera
      // En args podria pasarle mas cosas como page, pageZone, etc.
      Helpers::template(array('name' => 'layout',
                              'path' => '../../skins/'.$layout->getName(),
                              'args' => $zoneContent )); 
    ?>
    <div id="powered_by">
      <a href="http://code.google.com/p/yupp-cms" target="_blank"><img src="<?php echo $_base_dir; ?>/apps/cms2/images/yuppcms.png" /></a>
    </div>
  </body>
</html>