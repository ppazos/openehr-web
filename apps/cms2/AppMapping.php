<?php

class AppMapping {
   
   public $mapping = "/cms2(\/.*(\/.*)?)?/"; // conrtoller y action son opcionales! // TODO: hacer una expresion dividida por / que se esplitee por / y se chekeen las 3 regexps, o las N que sean, viendo cada pedazo de url. ASI ES MAS SENCILLA la regexp esta.
   
   // FIXME: sacar el pasar ArrayObject
   public function getLogicalRoute( $field_list )
   {
      if (!isset($field_list[1]))
         return array('app'        => $field_list[0], 
                      'controller' => 'cms', 
                      'action'     => 'displayPageRO',
                      'params'     => array('pageId'=>'1'));
      
      // display (read only) con friendly url
      // si viene 'display' como controller, voy a PageController.page
      // en la accion viene el nombre normalizado de la pagina
      if ($field_list[1] == 'display')
      {
	     if (!isset($field_list[2])) // Da errores de que falta el indice 2 pero no se en que URL
         {
		    // cms/displayPage?pageId=1
            return array('app'        => $field_list[0], 
                         'controller' => 'cms', 
                         'action'     => 'displayPageRO',
                         'params'     => array('pageId'=>1));
		 }
		 
         return array('app'        => $field_list[0], 
                      'controller' => 'page', 
                      'action'     => 'display',
                      'params'     => array('nname'=>$field_list[2]));
      }
      
      
      return array('app'        => $field_list[0], 
                   'controller' => (!isset($field_list[1])) ? 'cms' : $field_list[1], 
                   'action'     => (!isset($field_list[2])) ? 'displayPageRO' : $field_list[2]);
      
      /*
      $loguedUser = YuppSession::get("user"); // Lo pone en session en el login.
      if ($loguedUser !== NULL || $field_list[2]==='user-gadget')
      {
         return array('app'  => $field_list[0], 
                      'controller' => (!isset($field_list[1])) ? 'entradaBlog' : $field_list[1], 
                      'action'     => (!isset($field_list[2])) ? 'list' : $field_list[2]);
      }
      else
      {
          return array('app'  => $field_list[0], 
                       'controller' => 'usuario', 
                       'action'     => 'login');
      }
      */
   }
}
?>