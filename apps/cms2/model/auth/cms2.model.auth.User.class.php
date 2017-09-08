<?php

class User extends PersistentObject
{
   const TABLE = 'cms_user';
   
   const TYPE_PENDING         = 'pending'; // Usuario pendiente de aprobacion
   const TYPE_USER            = 'user';    // Puede ver contenido privado
   const TYPE_CONTENT_EDITOR  = 'content_editor'; // Puede edotar contenido de modulos pero no puede gestionar paginas
   const TYPE_EDITOR          = 'editor';  // Puede editar paginas
   const TYPE_ADMIN           = 'admin';   // Puede crear y eliminar paginas y usuarios
   
   const LOGIN_ERR_INCOMPLETE = 1; // Falta username o password
   const LOGIN_ERR_FAILED     = 2; // Login fallido, usuario no existe
   const LOGIN_ERR_PENDING    = 3; // Usuario aun pendiente de aprobacion
   const LOGIN_ERR_SUCCESS    = 4; // Login exitoso
   
   // Test for human register
   static $humanity_test_retries = 2;
   static $humanity_test = array(
     "¿Eres humano?"                     => "Si",
     "¿Eres un robot?"                   => "No",
     "¿Eres un perrito?"                 => "No",
     "¿Eres un gatito?"                  => "No",
     "¿Eres el presidente de los EEUU?"  => "No",
     "¿Puedes volar?"                    => "No",
     "¿Puedes respirar debajo del agua?" => "No",
     "¿Puedes usar una computadora?"     => "Si",
     "¿Puedes usar Google?"              => "Si",
     "¿Puedes leer esto?"                => "Si",
     "¿Puedes leer un libro?"            => "Si",
     "¿Puedes escribir tu nombre?"       => "Si",
     "¿El café es amarillo?"             => "No",
     "¿Las bananas son azules?"          => "No",
     "¿Existen las manzanas verdes?"     => "Si",
     "¿El azúcar es rojo?"               => "No",
     "¿La leche es blanca?"              => "Si"
   );
   
   static public function findByUserType($type)
   {
      // TODO: ver que type sea valido
      return User::findBy( Condition::EQ(self::TABLE, 'usertype', $type), new ArrayObject() );
   }
   
   /**
    * @param string username
    * @param string password
    * @param boolean remember
    */
   static public function login($username, $password, $remember = false)
   {
      if (empty($username) || empty($password))
      {
         return self::LOGIN_ERR_INCOMPLETE;
      }
      
      $cond = Condition::_AND()
                ->add( Condition::EQ(self::TABLE, 'username', $username) )
                ->add( Condition::EQ(self::TABLE, 'password',  $password) );
    
      $list = User::findBy( $cond, new ArrayObject() );
      
      if ( count($list) == 0 )
      {
         return self::LOGIN_ERR_FAILED;
      }
      
      $user = $list[0];
      if ( $user->getUsertype() == self::TYPE_PENDING )
      {
         return self::LOGIN_ERR_PENDING;
      }
      
      // Uusario logueado queda en session
      YuppSession::set('_yupp_cms_user', $user);
      
      
      $newLastAccess = date("Y-m-d H:i:s"); // TODO: esto deberia ir en un log
      $user->setLastAccess($newLastAccess);
      
      if ($remember)
      {
         $cookie_name = 'frm'.md5('user'.$user->getId()); // frm es function remember me, me sirve para poder saber cual cookie agarrar para chequear los datos y loguearlo automaticamente
         
         // http://stackoverflow.com/questions/3531377/best-practise-for-remember-me-feature
         // http://tycoontalk.freelancer.com/php-forum/47470-tip-passwords-security-remember-me.html
         
         $cookie_value = md5($user->getUsername().$user->getPassword()); // En el proximo ingreso tambien se verifica el tiempo del ingreso anterior comparado con el guardado en la base.
         setcookie($cookie_name, $cookie_value, time()+31536000, '/'); // segundos en 365 dias
         $user->setCookie($cookie_value);
      }
      
      $user->save(); // TODO: check 4 errors
      
      // TODO: se deberia llevar log de la IP+userid+fecha
      // Se podria hacer un archivo de log en disco por cada user id y poner fechas con ips nomas
      
      return self::LOGIN_ERR_SUCCESS;
   }
   
   /**
    * Actualiza al usuario en sesion.
    * Se usa para cuando se actualizan datos en la base pero que
    * usuario esta logueado y la sesion queda desactualizada.
    */
   public function refresh()
   {
      $user = YuppSession::get('_yupp_cms_user');
      $user = User::get($user->getId()); // Recarga de la base
      YuppSession::set('_yupp_cms_user', $user);
   }
   
   static public function logout()
   {
      $user = YuppSession::get('_yupp_cms_user');
      
      $cookie_value = $user->getCookie();
      if (!empty($cookie_value))
      {
         // Matar la cookie de remember me
         $cookie_name = 'frm'.md5('user'.$user->getId()); // frm es function remember me, me sirve para poder saber cual cookie agarrar para chequear los datos y loguearlo automaticamente
         setcookie($cookie_name, '', time()-31536000, '/');
         
         $user->setCookie(NULL);
         $user->save();
      }
          
      return YuppSession::remove('_yupp_cms_user');
   }
   
   /**
    * @return User el usuario logueado o null.
    */
   static public function getLogged()
   {
      return YuppSession::get('_yupp_cms_user');
   }
   
   /**
    * Resetea el password del usuario y lo guarda.
    * Devuelve el nuevo password.
    */
   public function resetPassword()
   {
      $new_pass = self::md5_pass();
      $this->setPassword($new_pass);
      $this->save(); // TODO: garantizar que esto no falla
      return $new_pass;
   }
   
   /**
    * Generador de claves rando de largo length.
    */
   static private function md5_pass($length = 6)
   {
      return substr(md5(rand().rand()), 0, $length);
   }
   
   static public function getTypes()
   {
      return array(self::TYPE_PENDING, self::TYPE_USER, self::TYPE_CONTENT_EDITOR, self::TYPE_EDITOR, self::TYPE_ADMIN);
   }
   
   /**
    * Retorna todos los roles menos el pending.
    */
   static public function getActiveTypes()
   {
      return array(self::TYPE_USER, self::TYPE_CONTENT_EDITOR, self::TYPE_EDITOR, self::TYPE_ADMIN);
   }
   
   function __construct($args = array (), $isSimpleInstance = false)
   {
      $this->setWithTable(self::TABLE);
      
      $this->addAttribute('name',       Datatypes :: TEXT);
      $this->addAttribute('email',      Datatypes :: TEXT);
      
      $this->addAttribute('birthdate',  Datatypes :: DATE);
      $this->addAttribute('usertype',   Datatypes :: TEXT); // Rol
      $this->addAttribute('company',    Datatypes :: TEXT); // Nombre de la institucion en la que trabaja.
      $this->addAttribute('position',   Datatypes :: TEXT); // Nombre del cargo, rol o tarea que realiza en la institucion.
      $this->addAttribute('changePassword', Datatypes :: BOOLEAN); // Bandera que indica que tiene que cambiar su clave en el proximo login
      
      $this->addAttribute('country',    Datatypes :: TEXT);
      
      // auth
      $this->addAttribute('username',   Datatypes :: TEXT);
      $this->addAttribute('password',   Datatypes :: TEXT); // TODO: md5
      
      // session
      $this->addAttribute('cookie',     Datatypes :: TEXT); // guarda el cookie name generado en el login con 'remember me'
      $this->addAttribute('ip',         Datatypes :: TEXT);
      $this->addAttribute('session',    Datatypes :: TEXT); // no se usa ...
      $this->addAttribute('lastAccess', Datatypes :: DATETIME);
      
      // redes sociales
      $this->addAttribute('facebook',   Datatypes :: TEXT); // link
      $this->addAttribute('linkedin',   Datatypes :: TEXT); // link
      $this->addAttribute('twitter',    Datatypes :: TEXT); // username
      $this->addAttribute('googleplus', Datatypes :: TEXT); // link
      
      /* TODO: http://stackoverflow.com/questions/3531377/best-practise-for-remember-me-feature
       * 
       * You can store the time stamp of each user's last visit
       * in your database and in the cookie. Each time you read the 
       * cookie to log the user in, you check to see that both timestamps 
       * match. If they don't, deny the user. If they do, update the timestamps.
       * 
       */
      
      
      $this->setUsertype( self::TYPE_PENDING );
      $this->setChangePassword( false );
      
      $this->addConstraints('name' , array (
         Constraint :: minLength(1),
         Constraint :: maxLength(255),
         Constraint :: nullable(false),
         Constraint :: blank(false)
      ));
      $this->addConstraints('username' , array (
         Constraint :: blank(false)
      ));
      $this->addConstraints('password', array (
         Constraint :: minLength(4)
      ));
      $this->addConstraints('email', array (
         Constraint :: email()
      ));
      $this->addConstraints('usertype', array (
         Constraint :: blank(false),
         Constraint :: inList(self::getTypes())
      ));
      $this->addConstraints('country', array (
         Constraint :: maxLength(3)
      ));
      
      $url_constraints = array (
        Constraint :: nullable(true),
        Constraint :: maxLength(255),
        //Constraint :: matches('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@') // URL
        Constraint :: matches('@(([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@') // URL sin http(s)
      );
      
      // redes sociales
      $this->addConstraints('facebook',   $url_constraints);
      $this->addConstraints('linkedin',   $url_constraints);
      $this->addConstraints('googleplus', $url_constraints);
      
      /*
      $this->addConstraints('facebook',   array (
        Constraint :: nullable(true),
        Constraint :: maxLength(255),
        Constraint :: matches('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@') // URL
      ));
      $this->addConstraints('linkedin',   array (
        Constraint :: nullable(true),
        Constraint :: maxLength(255),
        Constraint :: matches('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@') // URL
      ));
      $this->addConstraints('googleplus', array (
        Constraint :: nullable(true),
        Constraint :: maxLength(255),
        Constraint :: matches('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@') // URL
      ));
      */
      $this->addConstraints('twitter',    array (
        Constraint :: nullable(true),
        Constraint :: maxLength(255)
      ));
      

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