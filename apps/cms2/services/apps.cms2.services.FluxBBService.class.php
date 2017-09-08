<?php
/*
 * Integracion de usuarios del CMS con usuarios de FluxBB.
 * 
 * Requisitos:
 * FluxBB debe estar instalado en la misma base de datos que YuppCMS.
 * 
 */
class FluxBBService {
   
   // ver http://blog.jandorsman.com/2011/01/fluxbb-and-cakephp-integration/
   
   private $dbname;
   private $user_table;
   private $forums_table;
   private $subscriptions_table;
   private $dal;
   
   // Group ids
   const GR_ADMINS = 1;
   const GR_MODERATORS = 2;
   const GR_GUESTS = 3;
   const GR_MEMBERS = 4;
   
   public function FluxBBService($dbname = 'fluxbb', $user_table = 'users', $forums_table = 'forums', $subscriptions_table = 'forum_subscriptions')
   {
      $this->dbname = $dbname;
      $this->user_table = $user_table;
      $this->forums_table = $forums_table;
      $this->subscriptions_table = $subscriptions_table;
      
      // NO PUEDO ESCRIBIREL YUPPCONFIG ASI QUE NO PUEDO PASARLE OTRO DATASOURCE...
      // Le agregue un segundo paramentro a DAL para pasarle un datasource forzado.
      // Esto puede servir para usar distintos datasources para distintas pruebas  mas alla de los 3 modos de ejecucion.
      
      // Me guardo el datasourse actual y le pongo uno para que consulte la DB donde esta el FluxBB.
      $cfg = YuppConfig::getInstance();
      $datasource = $cfg->getDatasource('cms2');
      
      // Supongo que url, user y pass son los mismos que para el datasource actual.
      $flux_datasource = array(
        'url'  => $datasource['url'],
        'user' => $datasource['user'],
        'pass' => $datasource['pass'],
        'type' => $datasource['type'],
        'database' => $dbname
      );
      $this->dal = new DAL('cms2', $flux_datasource);
   }
   
   /**
    * Crea un registro de usuario en el foro cuando se crea en el CMS.
    */
   public function insert($username, $password, $email, $realname, $group_id = 4)
   {
      // FIXME: la tabla de usuarios del foro tiene restriccion de username unique:
      //        UNIQUE KEY `foro_users_username_idx` (`username`(25))
      //        si llega a entrar un username que ya existe, esta consulta va a tirar una excepcion!
      
      /* http://fluxbb.org/docs/v1.4/dbstructure
      email_setting 1
      notify_with_post 0
      auto_notify 0
      show_smilies 1
      show_img 1
      show_img_sig 0
      show_avatars 1
      show_sig 1
      timezone 0
      language Spanish
      style Air
      */
      $this->dal->sqlExecute('INSERT INTO '.$this->user_table.' (' .
            'group_id,' .
            'username,' .
            'password,' .
            'email,' .
            'realname,' .
            'email_setting,' .
            'notify_with_post,' .
            'auto_notify,' .
            'show_smilies,' .
            'show_img,' .
            'show_img_sig,' .
            'show_avatars,' .
            'show_sig,' .
            'timezone,' .
            'language,' .
            'style) VALUES (' .
            "'$group_id'," .
            "'$username'," .
            "'".md5($password)."'," .
            "'$email'," .
            "'$realname'," .
            "'1'," .
            "'0'," .
            "'0'," .
            "'1'," .
            "'1'," .
            "'0'," .
            "'1'," .
            "'1'," .
            "'0'," .
            "'Spanish'," .
            "'Air');");
            
            
   }
   
   /**
    * Actualiza datos de usuarios en el foro cuando se actualizan en el CMS.
    * No le dejo cambiar el username porque es lo que uso para buscar el usuario en el foro.
    */
   public function update($username, $password, $email, $realname)
   {
      $this->dal->sqlExecute('UPDATE '.$this->user_table.' SET '.
            "password = '".md5($password)."', " .
            "email = '$email', " .
            "realname = '$realname' " .
            "WHERE username = '$username'");
   }
   
   public function exists($username)
   {
      $res = $this->dal->sqlQuery("SELECT COUNT(username) as cant FROM ".$this->user_table." WHERE username = '$username'");
      //print_r($res);
      return $res[0]['cant'] == 1;
   }
   
   /**
    * Single sign on desde el CMS al foro.
    */
   public function login()
   {
      // TODO
   }
   
   /**
    * Logout de single sign on desde el CMS al foro.
    */
   public function logout()
   {
      // TODO
   }
   
   /**
    * Suscribe a todos los usuarios a todos los temas del foro.
    * Solo si no se esta suscripto.
    * Si se ejecuta de nuevo, agrega las suscripciones a los temas nuevos.
    */
   public function subscribeAll()
   {
      $forums = $this->dal->sqlQuery("SELECT id FROM ".$this->forums_table);
      $users = $this->dal->sqlQuery("SELECT id FROM ".$this->user_table);
      $user_id;
      $forum_id;
      foreach ($forums as $forum_data)
      {
         $forum_id = $forum_data['id'];
         foreach ($users as $user_data)
         {
            // IF NOT EXISTS on forums_table._subscriptions user_id, forum_id
            $user_id = $user_data['id'];
            
            $subscription = $this->dal->sqlQuery(
                              "SELECT * FROM ".$this->subscriptions_table." ".
                              "WHERE user_id=$user_id AND forum_id=$forum_id");
            
            //print_r($subscription); array => ( array => [user_id][forum_id] )
            // Si la suscripcion del usuario al forum no existe, se crea
            if (count($subscription) == 0)
            {
               $this->dal->sqlExecute("INSERT INTO ".$this->subscriptions_table." (user_id, forum_id) ".
                                      "VALUES ('$user_id', '$forum_id');");
            }
         }
      }
        
      //print_r($forums);
      //print_r($users);
   }
}

?>