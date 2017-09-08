<?php
/**
 * @author Pablo Pazos Gutierrez (pablo.swp@gmail.com)
 * Created on 05/03/2009
 * ControllerFilers.php
 */

class AppControllerFilters implements IAppControllerFilters {
   
   public static function getBeforeFilters()
   {
      return array('CMSSecurityFilter');
   }
   
   public static function getAfterFilters()
   {
      return array();
   }
}

/**
 * Definicion de los distintos filtros que tiene la app.
 * Cada filtro define una accion que se ejecuta cuando corresponde sobre los 
 * controllers y acciones que se configuren para la app actual.
 */
class CMSSecurityFilter extends YuppController implements IControllerBeforeFilter {
   
   /*
    * Ejemplos de controllerActions>
    * 
    * 1. Que controllers y que acciones
    * 
    *    $controllerActions = array("page"=>array("create","list","edit"), "user"=>array("list","show"));
    * 
    * 2. Que controllers, todas las acciones (para controller page)
    * 
    *    $controllerActions = array("page"=>"*", "user"=>array("list","show"));
    * 
    * 3. Todos los controllers y todas las acciones.
    * 
    *    $controllerActions = "*";
    * 
    * 
    * Para exceptControllerActions sirven solo los formatos 1 y 2. (TIENE SENTIDO QUE EXCEPCIONES TENGAN *???) 
    * Sirve SOLO para cuando marco * en filters y quiero excepciones para todas las acciones de algun controller. 
    * 
    */
   
   // Pueden ser: un array (controller), un nombre de un 'app.controller' o una action, "app.*" que es "para todos".
   private $controllerActions = '*'; // Lista de controllers a los que se aplica este filter.
   private $exceptControllerActions = array(
                                        'user'=>array('login', 'register', 'logout', 'sendPassword', 'publicProfile', 'publicList'),
                                        'cms'=>array('displayPageRO', 'index'), // necesito que se aplique a displayPageRO para poder verificar el remember me
                                        'page'=>array('display'),
                                        'notifications'=>array('display'),
                                        
                                        // Esto se deberia agregar cuando se instala un modulo
                                        'newsModule'=>array('nextPage')
                                      ); // Lista de excepciones.
   
   public function getAllFilters()
   {
      return $this->controllerActions;
   }

   public function getAllExceptions()
   {
      return $this->exceptControllerActions;
   }
   
   /**
    * Debe retornar true si pasa o un ViewCommand si no pasa, o sea redireccionar o ejecutar una accion de un cotroller o hacer render de un string...
    * FIXME: $app ya no es necesario xq el filtro es por la app.
    */
   public function apply($app, $controller, $action)
   {
      //echo __FILE__ . "<hr/>";
      
      YuppLoader::load('cms2.model.auth', 'User');
      
      $user = User::getLogged();
      if ($user == NULL)
      {
         // Si no hay usuario logueado, verifico las cookies por "remember me",
         // si la cookie coincide, loguea al usuario correspondiente.
         //echo 'user 1<br/>';
         
         // Sino esta logueado, verificar si tiene cookies para abrirle la sesion (remember me)
         
         // La cookie tiene un nombre autogenerado, que empieza en frm (no se el nombre sino
         // tengo el usuario y estoy buscando al usuario)
         $ks = array_keys($_COOKIE); // function remember me
         
         //print_r($ks);
         
         $key = NULL;
         foreach ($ks as $k)
         {
            //echo "k $k<br/>";
            //if (strpos($k, 'frm') !== false) // FIXME: deberia ser === 0
            if (strpos($k, 'frm') === 0) // el nombre de la cookie empieza con frm
            {
               $key = $k;
               break;
            }
         }
         
         if ($key != NULL)
         {
            //echo 'ks1<br/>';
            
            $cookie_value = $_COOKIE[$key];
            
            //echo "cookie value: $cookie_value";
            
            $cond = Condition::EQ(User::TABLE, 'cookie', $cookie_value);
            $list = User::findBy( $cond, new ArrayObject() );
            
            //print_r($list);
            /*
             * Array (
             *  [0] => User Object (
             *    [withTable:protected] => cms_user [attributeValues:protected] => Array (
             *      [usertype] => admin
             *      [changePassword] =>
             *      [class] => User
             *        [deleted] =>
             *        [id] => 1
             *        [name] => Admin
             *        [email] => admin@admin.com
             *        [birthdate] =>
             *        [company] =>
             *        [position] =>
             *        [username] =>
             *        .....
             */
            if ( count($list) == 1 )
            {
               //echo 'user 1 1<br/>';
               
               $user = $list[0];
               User::login($user->getUsername(), $user->getPassword()); // No proceso errores porque se que fue exitoso.
               
               // verificar los permisos del usuario para saber a donde redirigir
               // editores y administradores van a displayPage, usuarios van a displayPageRO
               // CON EL MODO DE VISUALIZACION Y EDICION ESTO NO ES NECESARIO, TODOS VAN A RO y el que tiene permisos puede editar.
               return $this->redirect( array('app'=>'cms2', 'controller'=>'cms', 'action'=>'displayPageRO',
                                             'params'=>array('pageId'=>1)) );
               /*
               if ($user->getUsertype() == User::TYPE_USER)
               {
                  return $this->redirect( array('app'=>'cms2', 'controller'=>'cms', 'action'=>'displayPageRO',
                                                'params'=>array('pageId'=>1)) );
               }
               else // La unica alternativa es que sea editor o admin, pending no puede ser porque ni siquiera puede hacer login, y menos remember me.
               {
                  return $this->redirect( array('app'=>'cms2', 'controller'=>'cms', 'action'=>'displayPage',
                                                'params'=>array('pageId'=>1)) );
               }
               */
            }
            else
            {
               //echo 'user 1 2<br/>';
            }
         }
         else
         {
            //echo 'ks 2<br/>';
         }
         
         // =========================================================================================
         // Permisos sin usuario logueado
         // Solo puede ver controller=cms y action=displayPageRO
         if ($controller != 'cms' || $action != 'displayPageRO') // FIXME: este chequeo lo puedo meter como exception y si llega aqui siempre tiro error.
         {
             $this->flash['message'] = 'Ud. no tiene permisos suficientes para realizar la acción, ingrese con su usuario y clave o regístrese.' . " $controller $action";
             return $this->redirect( array('app'  => 'cms2',
                                           'controller' => 'cms',
                                           'action'     => 'displayPageRO',
                                           'params'     => array('pageId'=>1) ) );
         }
         
      }
      else // Usuario logueado
      {
         //echo 'usuario logueado<br/>';
         
         // =========================================================================================
         // Verificacion de permisos
         
         // No se verifica por usuario pendiente porque no se puede loguear.
         
         // Solo los ADMIN pueden ejecutar acciones de controller=user
         if ($controller == 'user') 
         {
            Logger::getInstance()->log( "controller user ". __FILE__ );
            if ($user->getUsertype() == User::TYPE_ADMIN) return true;
            
            Logger::getInstance()->log( "usuario no admin". __FILE__ );
            // Usuario no admin
            $anyUserActions = array('editProfile', 'showProfile', 'changePassword');
            if (in_array($action, $anyUserActions))
            {
                return true; // Si es una accion de cualquier usuario, lo deja pasar.
            }
            
            Logger::getInstance()->log( "usuario no admin y accion de admin". __FILE__ );
            
            $this->flash['message'] = 'Ud. no tiene permisos suficientes para realizar la acción';
            return $this->redirect( array('app'  => 'cms2',
                                          'controller' => 'cms',
                                          'action'     => 'displayPageRO',
                                          'params'     => array('pageId'=>1) ) );
         }
         
         // Si estoy logueado y
         // Estoy en cms.x con x != displayPageRO
         // Tengo que ser editor para poder pasar (si es USER, tiro error)
         if ($controller == 'cms' && $action != 'displayPageRO')
         {
            if ($user->getUsertype() == User::TYPE_USER)
            {
               $this->flash['message'] = 'Ud. no tiene permisos suficientes para realizar la acción';
               return $this->redirect( array('app'  => 'cms2',
                                             'controller' => 'cms',
                                             'action'     => 'displayPageRO',
                                             'params'     => array('pageId'=>1) ) );
            }
            
            // Solo pueden gestionar paginas los editores y admins
            $pageManagementActions = array('createPage', 'editPage', 'deletePage');
            if (in_array($action, $pageManagementActions) && in_array($user->getUsertype(), array(User::TYPE_USER, User::TYPE_CONTENT_EDITOR)))
            {
               $this->flash['message'] = 'Ud. no tiene permisos suficientes para realizar la acción';
               return $this->redirect( array('app'  => 'cms2',
                                             'controller' => 'cms',
                                             'action'     => 'displayPageRO',
                                             'params'     => array('pageId'=>1) ) );
            }
            
            // Los editores de contenido pueden ejecutar todas las acciones de gestion de modulos.
         }
         
         
         // =====
         // FIXME: http://code.google.com/p/yupp-cms/issues/detail?id=54
         // reglas para el modulo files
         
         // La accion download debe permitirse si esta logueado
         if ($controller == 'filesModule' && ($action == 'download' || $action == 'verifyDownload'))
         {
            return true;
         }
         
         // Si el controlador no es cms, o sea que tiene que ser de algun modulo
         // (de user no es porque ya se hicieron chekeos para ese), el usuario
         // debe ser editor y si no es tiro error.
         if ($controller != 'cms' && $user->getUsertype() == User::TYPE_USER)
         {
            $this->flash['message'] = 'Ud. no tiene permisos suficientes para realizar la acción';
            return $this->redirect( array('app'  => 'cms2',
                                          'controller' => 'cms',
                                          'action'     => 'displayPageRO',
                                          'params'     => array('pageId'=>1) ) );
         }
         
         // ====================================================================================
         //
         // TODO: faltan los chequeos de editores para que no administren usuarios (en la gui)
         // TODO: faltan los chequeos de editores para que no gestionen paginas los editores de contenido.
         //
         // ====================================================================================
      }
      /*
      // FIXME: usar YuppLoader.
      include_once("apps/portal/AuthorizationRules.php");
      
      // CUSTOM ACTION!
      $u = YuppSession::get("portal_user"); // Lo pone en session en el login.
      
      // Si el controller es User, solo lo dejo pasar si el usuario logueado es de tipo ADMIN.
      // Si no lo es, lo mando al index y la digo que no tiene permisos para hacer esas acciones.
      
      // Para controller user solo debe entrar un admin!
      if ($controller==="user") 
      {
         if ( !AuthorizationRules::isLoggedUserAdmin() )
         {
            //echo __FILE__ . " - user logueado pero no admin<hr/>";
            $this->flash['message'] = "Ud. no tiene permisos suficientes para realizar la acción";
            return $this->redirect( array("app"  => "portal",
                                          "controller" => "page",
                                          "action"     => "display",
                                          "params"     => new ArrayObject( array("_param_1"=>"index") )) );
         }
      }

      // Si estoy en controller page y la accion es de edicion, debe ser editor o admin.
      if ($controller==="page" && in_array($action, array("create", "edit", "save", "show", "delete", "list", "changeStatus"))) 
      {
         //echo __FILE__ . " - accion de edicion<hr/>";
         if ( !AuthorizationRules::isLoggerUserEditorOrAdmin() )
         {
            $this->flash['message'] = "Ud. no tiene permisos suficientes para realizar la acción";
            return $this->redirect( array("app"  => "portal",
                                          "controller" => "page",
                                          "action"     => "display",
                                          "params"     => new ArrayObject( array("_param_1"=>"index") )) );
         }
      }
      
      // Solo editores o admins pueden entrar al controller menu.
      if ($controller==="menu")
      {
         //echo __FILE__ . " - controller menu<hr/>";
         if ( !AuthorizationRules::isLoggerUserEditorOrAdmin() )
         {
            $this->flash['message'] = "Ud. no tiene permisos suficientes para realizar la acción";
            return $this->redirect( array("app"  => "portal",
                                          "controller" => "page",
                                          "action"     => "display",
                                          "params"     => new ArrayObject( array("_param_1"=>"index") )) );
         }
      }
      
      if ($controller==="config")
      {
         //echo __FILE__ . " - controller config<hr/>";
         if ( !AuthorizationRules::isLoggerUserEditorOrAdmin() )
         {
            $this->flash['message'] = "Ud. no tiene permisos suficientes para realizar la acción";
            return $this->redirect( array("app"  => "portal",
                                          "controller" => "page",
                                          "action"     => "display",
                                          "params"     => array("_param_1"=>"index")) );
         }
      }
      */

      // Si el tipo no esta logueado lo dejo navegar por el sitio publico, los permisos sobre las paginas se chequean en "display".
      return true;
   }
}

?>