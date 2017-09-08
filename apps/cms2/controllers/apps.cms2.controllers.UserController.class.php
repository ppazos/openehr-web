<?php

YuppLoader::load('cms2.model.auth', 'User');

class UserController extends YuppController {

   // ======================================
   // Integracion con fluxBB
   //$flux_dir = "/foro";
   //$flux_login = "$flux_dir/login.php?action=in";
   // ver http://blog.jandorsman.com/2011/01/fluxbb-and-cakephp-integration/
   private $flux = NULL;
   
   public function __construct(ArrayObject $params)
   {
      YuppLoader::load('apps.cms2.services', 'FluxBBService');
      
     // Sino tengo flux quiero que igual funcione
     try
     {
         if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1')
            $this->flux = new FluxBBService();                              // Local
         else
            $this->flux = new FluxBBService('bnninptm_prod', 'foro_users', 'foro_topics', 'foro_topic_subscriptions'); // online
      }
     catch (Exception $e)
     {
     }
     
      parent :: __construct($params);
   }
   // ======================================
   
   /*
   public function indexAction()
   {
     return $this->redirect( array('controller' => 'page',
                           'action' => 'display',
                           'params' => array('_param_1' => 'index') ));
   }
   */
   
   public function listAction()
   {
     if (!isset($this->params['max']))
     {
       $this->params['max']   = 20;
       $this->params['offset'] = 0;
     }

     // Necesito listar todas, las eliminadas y las no eliminadas
     $cond = Condition::_OR()
            ->add( Condition::EQ(User::TABLE, 'deleted', true) )
            ->add( Condition::EQ(User::TABLE, 'deleted', false) );
            
     $this->params['users'] = User::findBy( $cond, $this->params );
     $this->params['count'] = User::countBy( $cond ); // Maximo valor para el paginador.

     return $this->render("list");
   }
   
   /**
   * Accion para crear un usuario pendiente, para que la gente se pueda registrar en el sitio para posterior aprobacion.
   * Es analoga a create.
   */
   public function registerAction()
   {
      // Garantiza que no se accede directamente, sino que se accede desde el CMS.
      // HTTP_HOST = www.pepe.com sin http
      // REFERER = http://www....
      // TODO: esta puede ser una funcion del controller...
      if (!isset($_SERVER['HTTP_REFERER']) || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['HTTP_HOST'])
      {
         return $this->redirect(array('controller'=>'cms', 'action'=>'index'));
      }
     
      //print_r($_SERVER);
      //print_r($_SERVER['HTTP_REFERER']);
     
      if (isset($this->params['doit'])) // create
      {
         $errs = ''; // errores en json para el usuario
         
         // TODO: implementar estos chequeos como restricciones particulares en User, para poder mostrar junto
         //       a los demas errores de validacion, p.e. si no ingresa su nombre.
         
         // ================================================================================================
         // Verificacion de humanidad
         if (!isset($this->params['humanity_check_response']))
         {
            header('Content-type: application/json');
            return $this->renderString('{"status":"error", "msg":"Debe ingresar la verificacion.", "errors":{}}');
         }
         if ($this->params['humanity_check_response'] !== User::$humanity_test[$this->params['question']])
         {
            header('Content-type: application/json');
            return $this->renderString('{"status":"error", "msg":"La verificacion es incorrecta.", "errors":{}}');
         }
         
         // ================================================================================================
         // Hay un usuario con ese email?
         //
         if (User::countBy(Condition::EQ(User::TABLE, 'email', $this->params['email']), new ArrayObject()) == 1)
         {
            $errs .= '"email": "El correo '. $this->params['email'] .' ya est&aacute; registrado",';
         }
         
         // =================================================================================================
         // Nombre de usuario tambien debe ser unico
         if (User::countBy(Condition::EQ(User::TABLE, 'username', $this->params['username']), new ArrayObject()) == 1)
         {
            $errs .= '"username": "Nombre de usuario no disponible '. $this->params['username'] .', por favor elija otro",';
            //header('Content-type: application/json');
            //return $this->renderString('{"status":"error", "msg":"El nombre de usuario: '.$this->params['username'].' no se encuentra disponibles."}');
         }
         
         // Muestro errores de verificacioens inicales
         if ($errs !== '')
         {
            $errs .= substr($errs, 0, -1); // saco ultima ,
            
            header('Content-type: application/json');
            
            // Para hacerlo mas amigable, si el usuario ya esta registrado y pendiente, le digo que espere a ser aprobado.
            $cnd = Condition::_AND()
                     ->add( Condition::EQ(User::TABLE, 'username', $this->params['username']) )
                     ->add( Condition::EQ(User::TABLE, 'usertype', User::TYPE_PENDING) );
            
            if (User::countBy($cnd, new ArrayObject()) == 1)
            {
               // Si el registro esta pendiente, solo muestra error global, no muestra errores en campos
               return $this->renderString('{"status":"error", "msg":"Su registro está pendiente de aprobación, será aprobado en breve.", "errors":{}}');
            }
            
            return $this->renderString('{"status":"error", "msg":"Por favor verifique sus datos, si ya es un usuario y no recuerda su clave, puede solicitar una nueva clave, haga clic en \'Ingresar\'", "errors":{'.$errs.'}}');
         }


         $obj = new User();
       
         // FIXME: que el setProperties pueda hacer el bind del datepicker de abajo.
         $obj->setProperties($this->params);
       
         // FIXME: verficar que la fecha que queda tiene el formato correcto, y si no poner NULL.
         $d = $this->params['birthdate_day'];
         $m = $this->params['birthdate_month'];
         $y = $this->params['birthdate_year'];
         $obj->setBirthdate( $y.'-'.$m.'-'.$d );
       
         if (!$obj->save()) // Con validacion de datos!
         {
            $errors = $obj->getErrors();
            
            // TODO: verificar otros errores p.e. los de la fecha.
            if ($errors->hasFieldErrors("name"))
               $errs .= '"name": "Ingrese un nombre completo",';
            if ($errors->hasFieldErrors("username"))
               $errs .= '"username": "Ingrese un nombre de usuario",';
            if ($errors->hasFieldErrors("password"))
               $errs .= '"password": "Su clave debe tener 5 caracteres como m&iacute;nimo",';
            if ($errors->hasFieldErrors("email"))
               $errs .= '"email": "El email brindado no es v&aacute;lido",';
            
            $errs .= substr($errs, 0, -1); // saca ultima coma
         
            header('Content-type: application/json');
            return $this->renderString('{"status":"error", "msg":"Ocurrio un error, verifique los datos ingresados", "errors":{'.$errs.'}}');
         }
         
         
         // =================================================================
         // Aqui se definen las variables para el envio
         include('./apps/cms2/config/cms_config.php');
         global $_base_dir;
            
         // Send password resetea la clave del usuario, la guarda, y se manda en texto plano en el email.
         // En la base se guarda como md5 (TODO).
         $mail_body = $register_text;
         $mail_body = preg_replace('/\[USER_NAME\]/i', $obj->getUsername(), $mail_body);
         $mail_body = preg_replace('/\[USER_EMAIL\]/i', $obj->getEmail(), $mail_body);
         $mail_body = preg_replace('/\[DOMAIN\]/i', 'https://'.$_SERVER['HTTP_HOST'].$_base_dir.'/'.$this->appName, $mail_body); // TODO: ponerle la ruta hasta el login de recordar el password
    
         $header = "From: ". $cms_sender_name . " <" . $cms_sender_email . ">\r\n"; //optional headerfields
         $header .="Return-Path:<" . $cms_sender_email . ">\r\n"; // avoid ending in spam folder http://php.net/manual/en/function.mail.php
           
         ini_set('sendmail_from', $cms_sender_email);
         
         // TODO: los errores logueados a disco
         try
         {  // Por si no tengo servidor de email
         
            // El receptor es tambie n el administrador
            if (!mail($cms_sender_email, $register_subject, $mail_body, $header))
            {
               //return $this->renderString('{"status":"error", "msg":"No se pudo enviar notificacioa a '.$recipient.'"}');
            }
            else
            {
               //return $this->renderString('{"status":"ok", "msg":"Notificacion enviada a '.$recipient.'"}');
            }
         }
         catch (Exception $e)
         {
            // Cae aqui cuando no hay un servidor de mail para enviar el correo
            //return $this->renderString('{"status":"error", "msg":"Por problemas t&eacute;cnicos no se pudo enviar notificacion a '.$recipient.', contacte al administrador"}');
         }
         // =================================================================
         

         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "msg":"Registro exitoso, en breve será aprobado por nuestro administrador"}');
      }
      
      // Muestra el register en la modal
   }
   

   /**
   * Listado de usuarios pendientes.
   */
   public function listPendingAction()
   {
     // Verificar que es un ADMIN.
     // FIXME: esto podria ir en un filter porque es para todas las acciones de User menos registerUser.
     /*
     $loggedUser = YuppSession::get("portal_user");
     if ($loggedUser->getUsertype()!==User::TYPE_ADMIN) // Se que hay usuario logueado por los filters.
     {
       $this->flash['message'] = "Ud. no tiene permisos suficientes para realizar esta acci&oacute;n";
       return $this->redirect( array("controller"=>"page", "action"=>"list") );
     }
     */
     
     // paging
     if (!isset($this->params['max']))
     {
       $this->params['max']   = 20;
       $this->params['offset'] = 0;
     }
     
     // sorting
     if (!isset($this->params['sort']))
     {
       $this->params['sort'] = 'id';
       $this->params['dir'] = 'desc';
     }
     
     $tableName = YuppConventions::tableName( 'User' ); // Se le pasa la clase, para saber la tabla donde se guardan sus instancias.
     $condition = Condition::_AND()
                   ->add( Condition::EQ($tableName, "usertype", User::TYPE_PENDING) )
                   ->add( Condition::EQ($tableName, "deleted", 0) ); // FIXME: con false no funciona!
     
     $this->params['users'] = User::findBy( $condition, $this->params );
     $this->params['count'] = User::countBy( $condition ); // Maximo valor para el paginador.

     return;
   }
   
   /**
    * Accion de aprobar un usuario pendiente.
    */
   public function approveAction()
   {
     $user = User::get( $this->params['id'] );
     $user->setUsertype(USER::TYPE_USER);
     $user->save(); // TODO: ver errores
     
     // ======================================
     // Integracion con fluxBB
     // - crea el usuario en el foro
     //
    if (!is_null($this->flux))
    {
       $this->flux->insert(
         $user->getUsername(),
         $user->getPassword(),
         $user->getEmail(),
         $user->getName()
       );
    }
     // ======================================
     
     // ===============================
     // Al aprobar al usuario, hay que dar de alta tambien en el foro y la wiki.
     // Para hacerlo independiente, se podrian registrar JOBS que se ejecutan
     // cuando las acciones terminan de forma correcta, y en esos JOBS le doy
     // de alta al usuario en los demas sistemas.
     // ===============================
     
     global $_base_dir;
     
     // Aqui se definen las variables para el envio
     include('./apps/cms2/config/cms_config.php');
     
     $mail_body = $approve_text;
     $mail_body = preg_replace('/\[USER_NAME\]/i', $user->getUsername(), $mail_body);
     $mail_body = preg_replace('/\[USER_PASSWORD\]/i', $user->getPassword(), $mail_body);
     $mail_body = preg_replace('/\[ADMIN_EMAIL\]/i', $cms_sender_email, $mail_body);
     $mail_body = preg_replace('/\[DOMAIN\]/i', 'https://'.$_SERVER['HTTP_HOST'].$_base_dir.'/'.$this->appName, $mail_body); // TODO: calcular link a login

     $header = "From: ". $cms_sender_name . " <" . $cms_sender_email . ">\r\n"; //optional headerfields
     $header .="Return-Path:<" . $cms_sender_email . ">\r\n"; // avoid ending in spam folder http://php.net/manual/en/function.mail.php
   
     ini_set('sendmail_from', $cms_sender_email);
     
     try // Por si no tengo servidor de email
     {
        if (!mail($user->getEmail(), $approve_subject, $mail_body, $header))
        {
           //return $this->renderString('{"status":"error", "msg":"No se pudo enviar notificacioa a '.$user->getEmail().'"}');
           $this->flash['message'] = "No se pudo enviar notificacioa a ".$user->getEmail();
        }
        else
        {
           //return $this->renderString('{"status":"ok", "msg":"Notificacion enviada a '.$user->getEmail().'"}');
           $this->flash['message'] = "Notificacion enviada a ".$user->getEmail();
        }
     }
     catch (Exception $e)
     {
        // Cae aqui cuando no hay un servidor de mail para enviar el correo
        //return $this->renderString('{"status":"error", "msg":"Por problemas t&eacute;cnicos no se pudo enviar notificacion a  '.$user->getEmail().', contacte al administrador"}');
        $this->flash['message'] = "Por problemas t&eacute;cnicos no se pudo enviar notificacion a ".$user->getEmail().", contacte al administrador";
     }
     
     // test
     //$this->flash['message'] .= $mail_body;
     return $this->redirect( array('action' => 'listPending') );
   }
   
   public function createAction()
   {
      $obj = new User(); // Crea instancia para mostrar en la web los valores por defecto para los atributos que los tengan.

      // View create, que es como edit pero la accion de salvar vuelve aqui.

      if (isset($this->params['doit'])) // create
      {
         $obj->setProperties($this->params);
       
         // TODO: que el bind pueda bindear fechas del datepicker.
         // TODO: verificar formato de la fecha
         $d = $this->params['birthdate_day'];
         $m = $this->params['birthdate_month'];
         $y = $this->params['birthdate_year'];
         $obj->setBirthdate( $y.'-'.$m.'-'.$d );
      
         // FIXME: el password deberia autogenerarse y enviarse por correo al usuario y que el usuario
         // lo tenga que cambiar en el proximo login. Tengo que poner changePassword en true!!!
         if (!$obj->save()) // Con validacion de datos!
         {
            $this->flash['message'] = 'Ocurrio un error al crear el nuevo usuario.';
            return array('user'=>$obj);
         }
         
         // ======================================
         // Integracion con fluxBB
         // - crea el usuario en el foro
         //
       if (!is_null($this->flux))
        {
           $this->flux->insert(
             $this->params['username'],
             $this->params['password'],
             $this->params['email'],
             $this->params['name']
           );
       }
         // ======================================

         return $this->redirect( array('action'=>'show', 'params'=>array('id'=>$obj->getId())) );
      }

      return array('user'=>$obj);
     
   } // create
   
   public function showAction()
   {
      // TODO: verificar error si no viene id o si viene un id y no existe el usuario.
      return array('user'=>User::get( $this->params['id'] ));
   }
   
   /**
    * Edit de usuarios en el backend.
    */
   public function editAction()
   {
      // TODO: verificar error si no viene id o si viene un id y no existe el usuario.
      $this->params['user'] = User::get( $this->params['id'] );
      return;
   }
   
   /********************************************************************
    * 
    * El usuario tiene acciones para modificar sus datos:
    * 
    * - showProfile ve sus datos
    * - changePassword cambia su clave (desde showProfile)
    * - editProfile edita sus datos menos la clave (desde showProfile)
    * 
    ********************************************************************/
   
   /**
    * Edit del usuario logueado. Similar a edit pero se verifica que este el usuario logueado.
    */
   public function editProfileAction()
   {
      //Logger::getInstance()->log("editProfile");
      
      $user = User::getLogged();
      
      /**
       * Puede ser null y haber una seccion activa, porque puedo abrir otra pagina y hacer logout,
       * y luego en la pagina de edicion del perfile hacer submits que envian el id de sesion,
       * y ess id no se elimino todavia en el servidor (tengo que ver como eliminarlo en el logout).
       * 
       * http://code.google.com/p/yupp-cms/issues/detail?id=70
       */
      if ($user === NULL)
      {
         // EL problema es que el request a esta accion es por ajax!, el redirect lo
         // deberia hacer la propia vista y aqui le deberia devolver un error en json.
         return $this->redirect(array('controller'=>'cms', 'action'=>'index'));
      }
      
      if (isset($this->params['doit']))
      {
         //Logger::getInstance()->log("doit");
        
         $user->setProperties( $this->params ); // No cambia username
         
         // TODO: que el binder tambien bindee el datepicker.
         $d = $this->params['birthdate_day'];
         $m = $this->params['birthdate_month'];
         $y = $this->params['birthdate_year'];
         $user->setBirthdate( $y.'-'.$m.'-'.$d );
         
         if ( !$user->save() ) // Con validacion de datos!
         {
            // Errores en JSON igual que en registerAction
            $errs = '';
            $errors = $user->getErrors();
            
            // TODO: verificar otros errores p.e. los de la fecha.
            if ($errors->hasFieldErrors("name"))
               $errs .= '"name": "Ingrese un nombre completo",';
            
            if ($errors->hasFieldErrors("facebook"))
               $errs .= '"facebook": "Debe ser una direccion web valida",';
            if ($errors->hasFieldErrors("linkedin"))
               $errs .= '"linkedin": "Debe ser una direccion web valida",';
            if ($errors->hasFieldErrors("googleplus"))
               $errs .= '"googleplus": "Debe ser una direccion web valida",';
            // Por ahora nombre de usuario y clave no se pueden cambiar desde el editProfile.
            //if ($errors->hasFieldErrors("username"))
            //   $errs .= '"username": "Ingrese un nombre de usuario",';
            //if ($errors->hasFieldErrors("password"))
            //   $errs .= '"password": "Su clave debe tener 5 caracteres como m&iacute;nimo",';
            
            if ($errors->hasFieldErrors("email"))
               $errs .= '"email": "El email brindado no es v&aacute;lido",';
            
            $errs .= substr($errs, 0, -1);
         
            // TODO: ver codigo de la vista de register para ver como handlea los errores
         
            header('Content-type: application/json');
            return $this->renderString('{"status":"error", "msg":"Ocurrio un error, verifique los datos ingresados", "errors":{'.$errs.'}}');
         }
         
         // Actualiza la sesion
         $user->refresh();
         
         
         // ======================================
         // Integracion con fluxBB
         // - actualiza el usuario en el foro
         //
       if (!is_null($this->flux))
        {
            $this->flux->update($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getName());
       }
         // ======================================
         
         
         header('Content-type: application/json');
         return $this->renderString('{"status":"ok", "msg":"Su perfil ha sido modificado"}');
      }
      
      //Logger::getInstance()->log("NO HAY doit!!!");
      
      return array('user'=>$user);
   }
   
   public function showProfileAction()
   {
      if (!isset($this->params['id']))
      {
         $this->flash['message'] = 'No se especifica el identificador del usuario';
         return $this->redirect(array('ation'=>'list'));
      }
      
      $user = User::get( $this->params['id'] );
      
      if ($user == NULL)
      {
         $this->flash['message'] = 'Usuario no encontrado';
         return $this->redirect(array('ation'=>'list'));
      }
      
      return array('user'=>$user);
   }
   
   // TODO: publicList y publicProfile deberian ser accesibles solo si se esta logueado.
   public function publicListAction()
   {
     if (!isset($this->params['max']))
     {
       $this->params['max']   = 8;
       $this->params['offset'] = 0;
     }

     // Lista miembros activos y no eliminados
     $cond = Condition::NEQ(User::TABLE, 'usertype', User::TYPE_PENDING);
     
     if (isset($this->params['q']))
     {
        $q = $this->params['q'];
        $cand = Condition::_AND()
                 ->add( $cond )
                 ->add(
                   Condition::_OR() // Matchea con alguno de los campos de los usuarios...
                     ->add( Condition::ILIKE(User::TABLE, 'name', "%$q%") )
                     ->add( Condition::ILIKE(User::TABLE, 'company', "%$q%") )
                     // TODO: pais, ciudad
                 );
        
        $cond = $cand;
     }
     
     return array('users'=>User::findBy($cond, $this->params), 'count'=>User::countBy($cond));
   }
   
   // TODO: public member list desde la que se selecciona el usuario para ver sus detalles.
   public function publicProfileAction()
   {
      if (!isset($this->params['id']))
      {
         $this->flash['message'] = 'Ha ocurrido un error, por favor vuelva a intentar mas tarde';
         return $this->redirect(array('ation'=>'publicList'));
      }
      
      $user = User::get( $this->params['id'] );
      
      if ($user == NULL)
      {
         $this->flash['message'] = 'Usuario no encontrado';
         return $this->redirect(array('ation'=>'publicList'));
      }
      
      return array('user'=>$user);
   }
   
   public function changePasswordAction()
   {
      if (isset($this->params['doit']))
      {
         // 1. Verifica el password actual del usuario logueado
         // 2. Verifica que el nuevo y el retype sean iguales
         // 3. Setea el nuevo password
         // 4. Manda correo al usuario avisandole del cambio de clave
         
         header('Content-type: application/json');
         
         $user = User::getLogged();
         if ($user->getPassword()!=$this->params['password']) // TODO: md5
         {
            return $this->renderString('{"status":"error", "msg":"Su clave actual es incorrecta, por favor vuelva a intentarlo"}');
         }
         
         if ($this->params['new_password'] != $this->params['new_password_rt'])
         {
            return $this->renderString('{"status":"error", "msg":"Ambos ingresos de su nueva clave no coinciden, corrijalos e intente de nuevo"}');
         }
         
         
         $user->setPassword($this->params['new_password']); // TODO: md5
         
         if (($changePassword = $user->getChangePassword()))
            $user->setChangePassword(false); // Si estaba para cambiar la clave, ahora se marca como cambiada
         
         $user->save(); // TODO: ver que guarde ok
         
         // ==================================================================================
         // Aqui se definen las variables para el envio
         include('./apps/cms2/config/cms_config.php');
         
         global $_base_dir;
         
         $recipient = $user->getEmail(); //recipient
            
         // Send password resetea la clave del usuario, la guarda, y se manda en texto plano en el email.
         // En la base se guarda como md5 (TODO).
         $mail_body = $changePassword_text;
         $mail_body = preg_replace('/\[USER_PASSWORD\]/i', $user->getPassword(), $mail_body); // TODO: reset password
         $mail_body = preg_replace('/\[DOMAIN\]/i', 'https://'.$_SERVER['HTTP_HOST'].$_base_dir.'/'.$this->appName, $mail_body); // TODO: ponerle la ruta hasta el login de recordar el password
    
         $header = "From: ". $cms_sender_name . " <" . $cms_sender_email . ">\r\n"; //optional headerfields
         $header .="Return-Path:<" . $cms_sender_email . ">\r\n"; // avoid ending in spam folder http://php.net/manual/en/function.mail.php
         
         ini_set('sendmail_from', $cms_sender_email);
         
         /*
         Logger::getInstance()->setFile("logger_send_password.txt");
         Logger::getInstance()->on();
         Logger::struct($mail_body);
         Logger::struct($this);
         Logger::getInstance()->off();
         */
    
         try // Por si no tengo servidor de email
         {
            if (!mail($recipient, $changePassword_subject, $mail_body, $header))
            {
               return $this->renderString('{"status":"error", "msg":"No se pudo enviar notificacioa a '.$recipient.'"}');
            }
         }
         catch (Exception $e)
         {
            // Cae aqui cuando no hay un servidor de mail para enviar el correo
            return $this->renderString('{"status":"error", "msg":"Por problemas t&eacute;cnicos no se pudo enviar notificacion a  '.$recipient.', contacte al administrador"}');
         }
         // ==================================================================================
         
         
         // ======================================
         // Integracion con fluxBB
         // - actualiza el usuario en el foro
         //
       if (!is_null($this->flux))
        {
            $this->flux->update($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getName());
       }
         // ======================================
         
         
         $user->refresh(); // Actualiza la sesion
         return $this->renderString('{"status":"ok", "msg":"Se ha modificado su clave, recibira un correo confirmado su cambio", "changePassword":"'.$changePassword.'"}');
      }
      
      return array('user'=>User::getLogged());
   }
   
   public function saveAction()
   {
      // FIXME: verificar que venga el id
      
      $user = User::get( $this->params['id'] );
      $user->setProperties( $this->params ); // No puede cambiar el username.
     
      // TODO: que el binder tambien bindee el datepicker.
      $d = $this->params['birthdate_day'];
      $m = $this->params['birthdate_month'];
      $y = $this->params['birthdate_year'];
      $user->setBirthdate( $y.'-'.$m.'-'.$d );
      
      if ( !$user->save() ) // Con validacion de datos!
      {
         $this->params['user'] = $user;
         return $this->render("edit");
      }
      
      // ======================================
      // Integracion con fluxBB
      // - actualiza el usuario en el foro
      //
     if (!is_null($this->flux))
     {
         $this->flux->update($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getName());
     }
      // ======================================

      $this->flash['message'] = "Los datos del usuario fueron actualizados";
      return $this->redirect( array('action'=>'show', 'params'=>array('id' => $user->getId()) ));
   }
   
   
   public function loginAction()
   {
      // Garantiza que no se accede directamente, sino que se accede desde el CMS.
      // HTTP_HOST = www.pepe.com sin http
     // REFERER = http://www....
     // TODO: esta puede ser una funcion del controller...
      if (!isset($_SERVER['HTTP_REFERER']) || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['HTTP_HOST'])
     {
        return $this->redirect(array('controller'=>'cms', 'action'=>'index'));
     }
     
      // OBS: si retorno NULL o modelo, desde la accion index, se intenta mostrar la vista index.view.php.
      if ( isset($this->params['doit']) )
      {
         // Dentro del doit todas las respuestas son json
         header('Content-type: application/json');
         
         $ret = User::login( ((isset($this->params['username']))? $this->params['username'] : NULL),
                             ((isset($this->params['password']))? $this->params['password'] : NULL),
                             ((isset($this->params['remember']))? $this->params['remember'] : false));
         switch ($ret)
         {
            case User::LOGIN_ERR_INCOMPLETE:
               return $this->renderString('{"status":"error", "msg":"Por favor ingrese nombre de usuario y clave"}');
            break;
            case User::LOGIN_ERR_FAILED:
               return $this->renderString('{"status":"error", "msg":"El usuario no existe"}');
            break;
            case User::LOGIN_ERR_PENDING:
               return $this->renderString('{"status":"error", "msg":"No puede ingresar porque el administrador aún no ha aprobado su cuenta"}');
            break;
            case User::LOGIN_ERR_SUCCESS:
               // changePassword se envia para redirigir a la vista para que cambie la clave
               return $this->renderString('{"status":"ok", "msg":"Usuario logueado con éxito", "changePassword": "'. User::getLogged()->getChangePassword() .'"}');
            break;
            // No hay otro caso
         }
      }
      
      // Muestra el login
      
   } // login

   public function logoutAction()
   {
      User::logout();

      header('Content-type: application/json');
      return $this->renderString('{"status":"ok", "msg":"Vuelve a ingresar en otra ocasión!"}');
   }
   
   public function deleteAction()
   {
      // TODO: verificar que viene el id y que existe el usuario con ese id.
      $id  = $this->params['id'];
      $ins = User::get( $id );
      $ins->delete(true); // Eliminacion logica, si fuera fisica tendria que actualizar los links a las entradas, o borrar tambien las entradas del user.

      $this->flash['message'] = "Elemento [User:$id] eliminado.";
      return $this->redirect( array("action" => "list") );
   }
   
   // TICKET #2: http://code.google.com/p/yupp-portal/issues/detail?id=2
   public function sendPasswordAction()
   {
      if (isset($this->params['doit']))
      {
         header('Content-type: application/json');
          
         global $_base_dir; // /YuppPHPFramework
          
         /*
          // CHECK: ingreso de email
          if (!isset($this->params['email']))
          {
             $this->flash['message'] = 'Debe ingresar un email';
             return $this->redirect( array('controller'=>'page', 'action'=>'display', 'params'=>array('_param_1'=>'login')) );
          }
         */
          
         // TODO: CHECK: email valido
         
         // ================================================================================================
         // Hay un usuario con ese email?
         //
         $condition = Condition::EQ(User::TABLE, 'email', $this->params['email']);
         $list = User::findBy( $condition, $this->params );
       
         if ( count($list) === 1 )
         {
            $user = $list[0];
            
            // ==============================================================================================
            // Aqui se definen las variables para el envio
            include('./apps/cms2/config/cms_config.php');
            
            $user->setChangePassword(true); // Para que en el prox login lo cambie. El user.save se hace en resetPassword
            
            $recipient = $user->getEmail(); //recipient
            
            // Send password resetea la clave del usuario, la guarda, y se manda en texto plano en el email.
            // En la base se guarda como md5 (TODO).
            $mail_body = $sendPassword_text;
            $mail_body = preg_replace('/\[USER_PASSWORD\]/i', $user->resetPassword(), $mail_body);
            $mail_body = preg_replace('/\[ADMIN_EMAIL\]/i', $cms_sender_email, $mail_body);
            $mail_body = preg_replace('/\[DOMAIN\]/i', 'https://'.$_SERVER['HTTP_HOST'].$_base_dir.'/'.$this->appName, $mail_body); // TODO: ponerle la ruta hasta el login de recordar el password
    
            $header = "From: ". $cms_sender_name . " <" . $cms_sender_email . ">\r\n"; //optional headerfields
            $header .="Return-Path:<" . $cms_sender_email . ">\r\n"; // avoid ending in spam folder http://php.net/manual/en/function.mail.php
           
            ini_set('sendmail_from', $cms_sender_email);
            
            /*
            Logger::getInstance()->setFile("logger_send_password.txt");
            Logger::getInstance()->on();
            Logger::struct($mail_body);
            //Logger::struct($_SERVER);
            Logger::struct($this);
            Logger::getInstance()->off();
            */
         
         // ======================================
            // Integracion con fluxBB
            // - actualiza el usuario en el foro
            //
         if (!is_null($this->flux))
           {
               $this->flux->update($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getName());
         }
            // ======================================
    
            try
            {  // Por si no tengo servidor de email
               if (!mail($recipient, $sendPassword_subject, $mail_body, $header))
               {
                  return $this->renderString('{"status":"error", "msg":"No se pudo enviar notificacioa a '.$recipient.'"}');
               }
               else
               {
                  return $this->renderString('{"status":"ok", "msg":"Notificacion enviada a '.$recipient.'"}');
               }
            }
            catch (Exception $e)
            {
               // Cae aqui cuando no hay un servidor de mail para enviar el correo
               return $this->renderString('{"status":"error", "msg":"Por problemas t&eacute;cnicos no se pudo enviar notificacion a '.$recipient.', contacte al administrador"}');
            }
         }
         else
         {
            // FIXME: verificar que no haya 2 usuarios con el mismo mail...
            // No le puedo decir que el usuario para ese mail no existe porque es una falla de seguridad,
            // tengo que decir que verifique el email ingresado.
            
            return $this->renderString('{"status":"error", "msg":"No se pudo enviar notificacion, verifique el email ingresado..."}');
         }
      } // if doit
   }
   
   /**
    * Utilitaria para verificar sincronizacion entre usuarios del foro y del cms.
    */
   public function synchroAction()
   {
      $users = User::listAll(new ArrayObject());
      
     if (!is_null($this->flux))
     {
        //Logger::getInstance()->on();
        foreach ($users as $user)
        {
          if (!$this->flux->exists( $user->getUsername() ))
          {
            echo "user ". $user->getUsername() ." no existe<br/>";
            
            $this->flux->insert(
               $user->getUsername(),
               $user->getPassword(), // Cuidado que luego ya va a estar en md5, no hacer md5 2 veces!
               $user->getEmail(),
               $user->getName()
            );
          }
        }
     }
   }
   
   /**
    * Suscribe a todos los usuarios del foro a todos los temas.
    */
   public function subscribeAllAction()
   {
      if (!is_null($this->flux))
     {
           $this->flux->subscribeAll();
     }
   }
   
   public function addTagAction()
   {
      // Si viene tagName, creo nueva tag
      // Si ademas viene tagId, ese id corresponde a la tag padre
      if (!empty($this->params['tagName']))
      {
         $tag = new UserTag(array(
           'name'=>$this->params['tagName'],
           'color'=>$this->params['color'],
           'image'=>$this->params['image'],
           'points'=>$this->params['points']));
         
         $tag->save();
         
         if (!empty($this->params['tagId']))
         {
            $parent = UserTag::get( $this->params['tagId'] );
            $parent->addToSubtags( $tag );
            $parent->save();
         }
      }
      else
      {
         // Verifica que no se haya hecho submit sin haber seleccionado una tag
         if (empty($this->params['tagId']))
         {
            header("Content-Type: text/plain");
            return $this->renderString( 'Debe seleccionar una etiqueta a asignar' );
         }
         
         $tag = UserTag::get( $this->params['tagId'] );
      }
      
      $user = User::get( $this->params['id'] );
      $user->addToTags($tag);
      $user->save();
      
      // FIXME: render del template de las tags del usuario.
      return $this->renderTemplate( 'showUserTags', array('user'=>$user) );
   }
}
?>