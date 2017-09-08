<?php

class CmsHelpers {

   /**
    * @param Page paramsMap[page]
    * @param hideIfNone indica si hay que ocultar la div de subpages si la pagona no tiene subpaginas.
    */
   public static function subpages($paramsMap, $hideIfNone = false)
   {
      $page = NULL;
      // http://www.zomeoff.com/php-fast-way-to-determine-a-key-elements-existance-in-an-array/
      if (isset($paramsMap['page']) || array_key_exists('page', $paramsMap)) // Si no me lo pasan, tengo que poner el actual.
         $page = $paramsMap['page'];
      else
         throw new Exception("El parametro 'page' es obligatorio y no esta presente. " . __FILE__ . " " . __LINE__);

      if ($hideIfNone && count($page->getSubpages()) == 0)
      {
          return '';
      }

      $out = '';
      foreach ($page->getSubpages() as $subpage)
      {
         // Como se pueden eliminar paginas y mantenerse en las subpages, debo preguntar si no esta eliminada para mostrarla en el menu.
         if (!$subpage->getDeleted())
         {
            $out .= h('link', array('controller'=>'cms', 'action'=>'displayPage', 'pageId'=>$subpage->getId(), 'body'=>$subpage->getName()));
            $out .= ' | ';
         }
      }
      if (strlen($out)>=3)
         $out = substr($out, 0, -3);
      
      return '<div class="subpages">Subpaginas: ' . $out . '</div>'; // TODO: I18N
   }
   
   /**
    * Muestra el nombre de la pagina y de todos sus ancestros.
    * @param Page paramsMap[page]
    */
   public static function navbar($paramsMap)
   {
      $page = NULL;
      // http://www.zomeoff.com/php-fast-way-to-determine-a-key-elements-existance-in-an-array/
      if (isset($paramsMap['page']) || array_key_exists('page', $paramsMap)) // Si no me lo pasan, tengo que poner el actual.
         $page = $paramsMap['page'];
      else
         throw new Exception("El parametro 'page' es obligatorio y no esta presente. " . __FILE__ . " " . __LINE__);
      
      
      $out = '<div class="navbar">';
      // Link no amigable
      //$ps = h('link', array('controller'=>'cms', 'action'=>'displayPage', 'pageId'=>$page->getId(), 'body'=>$page->getName(), 'attrs'=>array('class'=>'active')));
      
      // Link amigable
      $ps = h('link', array('controller'=>'display', 'action'=>$page->getNormalizedName(), 'body'=>$page->getName(), 'attrs'=>array('class'=>'active')));
      
      while (($page = $page->getParent()) != NULL)
      {
         // Link no amigable
         //$ps = h('link', array('controller'=>'cms', 'action'=>'displayPage', 'pageId'=>$page->getId(), 'body'=>$page->getName())) .' &gt; ' . $ps;
         
         // Link amigable
         $ps = h('link', array('controller'=>'display', 'action'=>$page->getNormalizedName(), 'body'=>$page->getName())) .' &gt; ' . $ps;
      }
      
      $out .= $ps . '</div>';
      return $out;
   }
   
   /**
    * Muestra una tag img con la direccion de un gravatar para el usuario logueado.
    * https://es.gravatar.com/site/implement/images/
    * 
    * @param $size indica las dimensiones de la imagen 1..512
    * @param $default indica el codigo gravatar o la url de la imagen por defecto sino se encuentra el gravatar para el email del usuario logueado.
    */
   public static function gravatar($size = 40, $user = NULL, $default = 'mm')
   {
       if ($user == NULL)
       {
          $user = User::getLogged();
       }
       
       // Si no le paso usuario y ademas no hay usuario logueado
       if ($user == NULL)
       {
          throw new Exception('Deberia haber un usuario logueado o pasar un usuario por parametro');
       }
       
       $hash = md5( strtolower( trim( $user->getEmail() ) ) );
       
       // $size <= 512 && $size > 0
       if ($size > 512) $size = 512;
       if ($size <= 0) $size = 40;
       
       echo '<img src="https://www.gravatar.com/avatar/'.$hash.'.jpg?s='.$size.'&d='.$default.'" />';
   }
   
   /**
    * $page es la pagina actual. Es necesario para generar los links. Hay que ver si hay otra forma de hacerlo sin pasarle la pagina.
    */
   public static function loginBox($page)
   {
      // TODO: i18n para el texto hardcoded
      $user = User::getLogged();
      echo '<div id="login_box"'. (($user != NULL )?' class="logged"':'') .'">';
      
      if ($user != NULL)
      {
         echo '<div>';
         echo 'Hola '. h('link', array('controller'=>'user', 'action'=>'showProfile', 'id'=>$user->getId(), 'body'=>$user->getName(), 'attrs'=>array('id'=>'showprofile_btn')));
         echo '</div>';
         
         echo '<div>';
         // Muestra el gravatar del usuario
         self::gravatar(20);
         echo '</div>';
         
         echo '<div>';
         // Si el usuario esta logueado y tiene permisos de edicion, lo dejo entrar al modo edit
         // Hago el chequeo por la inversa que es mas simple, en lugar de ver si es editor o admin, me fijo sino es user.
         if ($user->getUsertype() != User::TYPE_USER)
         {
            $ctx = YuppContext::getInstance();
            
            if ($ctx->getAction() == 'displayPageRO' ||
                $ctx->getController() == 'page' && $ctx->getAction() == 'display') // Para soportar url amigables
            {
               echo h('link', array('controller'=>'cms', 'action'=>'displayPage', 'pageId'=>$page->getId(), 'body'=>'Editar', 'attrs'=>array('id'=>'btn_cms_edit', 'class'=>'action')));
            }
            else
            {
               // Url no amigable
               //echo h('link', array('action'=>'displayPageRO', 'pageId'=>$page->getId(), 'body'=>'Ver', 'attrs'=>array('id'=>'btn_cms_show', 'class'=>'action')));
               
               // Url amigable
               // controller=display
               // action=page.nname
               // AppMapping corrige a PageController.display(nname)
               echo h('link', array('controller'=>'display', 'action'=>$page->getNormalizedName(), 'body'=>'Ver', 'attrs'=>array('id'=>'btn_cms_show', 'class'=>'action')));
            }
         }
         
         echo '<div>';
         // Link al listado de miembros de la comunidad
         // FIXME: esto no deberia estar en el login box, deberia ser parte de un menu del usuario logueado, pero lo dejo aca de mientras.
         echo h('link', array('controller'=>'user', 'action'=>'publicList', 'body'=>'Miembros', 'attrs'=>array('class'=>'action simple_ajax_link')));
         echo '</div>';
         
         echo h('link', array('controller'=>'user', 'action'=>'logout', 'body'=>'Salir', 'attrs'=>array('id'=>'logout_btn', 'class'=>'action')));
         echo '</div>';
      }
      else
      {
         // No esta logueado
         echo h('link', array('controller'=>'user', 'action'=>'login', 'body'=>'Ingresar', 'attrs'=>array('id'=>'login_btn', 'class'=>'action')));
         echo h('link', array('controller'=>'user', 'action'=>'register', 'body'=>'Registrarse', 'attrs'=>array('id'=>'register_btn', 'class'=>'action')));
      }
      echo '</div>';
   }
   
   public static function topbar($page = NULL, $layout = NULL, $extraMenus = array())
   {
      $user = User::getLogged();
    
      echo '<div id="topbar">';
      echo '<ul>';
      if ($user != NULL && in_array($user->getUsertype(), array(User::TYPE_EDITOR, User::TYPE_ADMIN)))
      {
         echo '<li>';
         echo 'Paginas';
         echo '<ul>';
         echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'listPages', 'body'=>'Gestionar')) .'</li>';
         echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'createPage', 'body'=>'Nueva pagina', 'attrs'=>array('id'=>'new_page'))) .'</li>';
         if ($page != NULL)
         {
            echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'createPage', 'parentId'=>$page->getId(), 'body'=>'Crear subpagina', 'attrs'=>array('id'=>'new_subpage'))) .'</li>';
            echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'displayPageRO', 'pageId'=>$page->getId(), 'body'=>'Ver pagina', 'attrs'=>array('target'=>'_blank'))) .'</li>';
            echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'editPage', 'pageId'=>$page->getId(), 'body'=>'Editar pagina', 'attrs'=>array('id'=>'edit_page'))) .'</li>';
            echo '<li>';
            if ($page->getDeleted())
              echo h('link', array('body'=>'Recuperar', 'controller'=>'cms', 'action'=>'undeletePage', 'pageId'=>$page->getId(), 'attrs'=>array('id'=>'undelete_page')) );
            else
              echo h('link', array('controller'=>'cms', 'action'=>'deletePage', 'pageId'=>$page->getId(), 'body'=>'Eliminar pagina', 'attrs'=>array('id'=>'delete_page')));
            echo '</li>';
         }
         echo '</ul>';
         echo '</li>';
      }
      echo '<li>';
         echo 'Modulos';
         echo '<ul>';
         echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'listModules', 'body'=>'Gestionar')) .'</li>';
         if ($page != NULL)
         {
            echo '<li>'. h('link', array('controller'=>'cms', 'action'=>'createModule', 'pageId'=>$page->getId(), 'body'=>'Nuevo modulo', 'attrs'=>array('id'=>'new_module'))) .'</li>';
            echo '<li>';
            // m=1 indica que quiero mostrar en la modal, el problema es que en el controller no sabe si lo llamo para mostrar en la modal o para ver la pagina completa porque no le mando el request por ajax
            echo h('link', array('controller'=>'cms', 'action'=>'listModules', 'pageId'=>$page->getId(), 'h'=>1, 'm'=>1, 'body'=>'Modulos huerfanos', 'attrs'=>array('id'=>'list_orphan_modules')));
            echo '</li>';
         }
         // <li>Agregar existente (agrega un modulo que ya esta en otra pagina a esta pagina)</li>
         echo '</ul>';
      echo '</li>';
      if ($user != NULL && in_array($user->getUsertype(), array(User::TYPE_EDITOR, User::TYPE_ADMIN)))
      {
         if ($layout != NULL)
         {
            echo '<li id="menu_layout">';
            echo 'Layouts';
            echo '<form method="post" action="'. h('url', array('controller'=>'cms', 'action'=>'useLayout')) .'">';
             echo '<input type="hidden" name="pageId" value="'. $page->getId() .'" />';
             echo '<select name="id">';
               
             // TODO: hacer consulta
             $dal = new DAL('cms2');
             $layoutNames = $dal->sqlQuery('SELECT id, name FROM cms_layouts');
             foreach ($layoutNames as $layoutData)
             {
                echo '<option value="'. $layoutData['id'] .'" '. (($layoutData['name']==$layout->getName())?'selected="selected"':'') .'>'. $layoutData['name'] .'</option>';
             }
             echo '</select>';
             echo '<input type="submit" name="doit" value="Cambiar layout" />';
            echo '</form>';
            echo '</li>';
         }
      }
      if ($user != NULL && $user->getUsertype() == User::TYPE_ADMIN)
      {
        echo '<li>';
        echo 'Usuarios';
        echo '<ul>';
        echo '<li>'. h('link', array('controller'=>'user', 'action'=>'list', 'body'=>'Gestionar')) .'</li>';
        echo '<li>'. h('link', array('controller'=>'user', 'action'=>'listPending', 'body'=>'Ver pendientes')) .'</li>';
        echo '<li>'. h('link', array('controller'=>'user', 'action'=>'create', 'body'=>'Crear usuario')) .'</li>';
        echo '</ul>';
        echo '</li>';
        
        echo '<li>';
        echo 'News Letters';
        echo '<ul>';
        echo '<li>'. h('link', array('controller'=>'notifications', 'action'=>'list', 'body'=>'Gestionar')) .'</li>';
        echo '<li>'. h('link', array('controller'=>'notifications', 'action'=>'create', 'body'=>'Crear newsletter')) .'</li>';
        echo '</ul>';
        echo '</li>';
      }
      echo '</ul>';
      echo '</div>';
   }
}
?>