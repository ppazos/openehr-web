<?php

YuppLoader::load('cms2.model.filesModule', 'FileInfo');
YuppLoader::load('cms2.model.filesModule', 'FilesModule');

class FilesModuleController extends YuppController {

   /**
    * in: id
    * in: pageId
    */
   public function editAction()
   {
      $module = FilesModule::get($this->params['id']);
      if (isset($this->params['doit']))
      {
         header('Content-type: application/json');
         
         // Chequeos
         //$path = $module->getPath();
         $path = $this->params['path'];
         if (!String::endsWith($path, '/'))
         {
            $path .= '/';
         }
         
         if (!file_exists($path))
         {
            return $this->renderString('{"status":"error", "msg":"La ruta '.$path.' no existe"}');
         }
         if (!is_readable($path))
         {
            return $this->renderString('{"status":"error", "msg":"No se tienen permisos de lectura para la ruta '.$path.'"}');
         }
         
         // regex filter es correcta?
         try
         {
            FileSystem::getFileNames($path, $this->params['filter']);
         }
         catch (Exception $e)
         {
            return $this->renderString('{"status":"error", "msg":"Hay un problema con la expresion \''.$this->params['filter'].'\'"}');
         }
         
         // TODO: ver que la ruta existe, si puede ser escrita y leida.
         // Sino puede ser escrita, no se puede ejecutar upload,
         // sino puede leerse, no se puede hacer scan.
         $module->resetBooleans();
         $module->setProperties($this->params);
         if (!$module->save())
         {
            print_r($module->getErrors()); // TODO: considerar errores para mostrar feedback.
            return $this->renderString('{"status":"error", "msg":"Ha ocurrido un error, intente de nuevo"}');
         }
         // FIXME: si hay error en el salvado no deberia caer aca
         // FIXME: esto deberia retornar AJAX
         //return $this->renderString('Modulo salvado correctamente');
         
         return $this->renderString('{"status":"ok", "msg":"Modulo actualizado con éxito", "id":'.$module->getId().', "pageId":'.$this->params['pageId'].', "zone":"'.$this->params['zone'].'"}');
      }
      
      return array('module'=>$module);
   }
   
   /**
    * Lee la path del modulo y aplica filtros,
    * creando FileInfos y asociandolos al modulo.
    */
   public function scanAction()
   {
      $module = FilesModule::get($this->params['id']);
      $path = $module->getPath();
      if (!String::endsWith($path, '/'))
      {
         $path .= '/';
      }
      
      header('Content-type: application/json');
      
      if (!file_exists($path))
      {
         // TODO: no existe path
         return $this->renderString('{"status":"error", "msg":"La ruta '.$path.' no existe"}');
      }
      if (!is_readable($path))
      {
         // TODO: no se puede leer path
         return $this->renderString('{"status":"error", "msg":"No se tienen permisos de lectura para la ruta '.$path.'"}');
      }
      
      // Quiero actualizar solo los archivos que no esten registrados.
      $currentFiles = $module->getFiles();
      
      // TODO: falta implementar el except con la regex
      $names = FileSystem::getFileNames($path, $module->getFilter());
      
      // Solo los archivos que no estan ya en el modulo
      foreach ($currentFiles as $cf)
      {
         //echo array_search($cf->getFullName(), $names) . '<br/>';
         if ( ($i = array_search($cf->getFullName(), $names)) !== false)
         {
            $names[$i] = NULL;
         }
      }

      // Saca nulls del array de nombres de archivos
      $new_files = array_filter($names);
      //print_r($new_files);
      
      foreach ($new_files as $name)
      {
         // Filtrar archivos que empieza con ., por ejemplo .htaccess
         // Si el archivo no esta en los archivos actuales del modulo
         //   Crear su FileInfo, asociarlo al modulo y guardarlo
         
         // Saco nombre y extension (si la tiene)
         $dot = strrpos($name, '.');
         if ($dot == 0) continue; // El nombre del archivo empieza con . (ej. .htaccess)
         if (!$dot)
         {
            $fnm = $name;
            $ext = '';
         }
         else
         {
            $fnm = substr($name, 0, $dot);
            $ext = substr($name, $dot+1);
         }
         //echo $fnm .'_'. $ext .'<br/>';
         
         
         // La descripcion se la pone el usuario despues
         $file = new FileInfo(array(
           'fullName'=>$name,
           'name'=>$fnm,
           'extension'=>$ext,
           'size'=>filesize($path.$name),
           'module'=>$module
         ));
         
         if (!$file->save())
         {
            // TODO: mostrar error que ocurrio
            print_r($file->getErrors());
            return $this->renderString('{"status":"error", "msg":"Ha ocurrido un problema al actualizar los archivos"}');
         }
      }
      
      // Actualizar el lastScan del modulo
      
      // Devolver la informacion de los archivos del modulo
      // en json para que se actualice la vista con JS.
      return $this->renderString('{"status":"ok", "msg":"Referencias a archivos actualizadas con éxito", "id":'.$this->params['id'].', "pageId":'.$this->params['pageId'].', "zone":"'.$this->params['zone'].'"}');
   }
   
   /**
    * Sube un archivo a path, crea su FileInfo y lo asocia al modulo.
    */
   public function uploadAction()
   {
      //print_r($this->params);
    
      $module = FilesModule::get($this->params['id']);
      $path = $module->getPath();
      if (!String::endsWith($path, '/'))
      {
         $path .= '/';
      }
      
      $filedata = $this->params['filedata']; 
      
      //print_r($filedata);
      //echo $path.$filedata['name'];
      
      // FIXME: si hay un archivo con mismo nombre ya registrado 1. avisar al usuario para que confirme si desea sobreescribirlo, 2. actualizar el FileInfo con el tamanio del nuevo archivo (que puede ser distinto al actual y correompe el download).
      
      if ($filedata['error'] != UPLOAD_ERR_OK)
      { 
         $this->flash['message'] = $this->file_upload_error_message($filedata['error']); 
      }
      else if (!move_uploaded_file($filedata['tmp_name'], $path.$filedata['name'])) // src_file, dest_file
      {
         $this->flash['message'] = "Error al subir el archivo";
         //return $this->renderString('{"status":"error", "msg":"Error al subir el archivo", "id":'.$this->params['id'].', "pageId":'.$this->params['pageId'].', "zone":"'.$this->params['zone'].'"}');
      }
      else
      {
         $this->flash['message'] = "Archivo subido con exito";
         
         // TODO: aqui ya podria actualizar el FileInfo, ademas en filedata viene el type
         // que dice el mime type del archivo subido.
      }

      return $this->redirect(array('action'=>'edit', 'params'=>array('id'=>$this->params['id'], 'pageId'=>$this->params['pageId'], 'zone'=>$this->params['zone'])));
      
      //return $this->renderString('{"status":"ok", "msg":"Archivo subido con exito", "id":'.$this->params['id'].', "pageId":'.$this->params['pageId'].', "zone":"'.$this->params['zone'].'"}');
   }
   
   private function file_upload_error_message($error_code)
   {
      switch ($error_code) { 
        case UPLOAD_ERR_INI_SIZE: 
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini'; 
        case UPLOAD_ERR_FORM_SIZE: 
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; 
        case UPLOAD_ERR_PARTIAL: 
            return 'The uploaded file was only partially uploaded'; 
        case UPLOAD_ERR_NO_FILE: 
            return 'No file was uploaded'; 
        case UPLOAD_ERR_NO_TMP_DIR: 
            return 'Missing a temporary folder'; 
        case UPLOAD_ERR_CANT_WRITE: 
            return 'Failed to write file to disk'; 
        case UPLOAD_ERR_EXTENSION: 
            return 'File upload stopped by extension'; 
        default: 
            return 'Unknown upload error'; 
      } 
   }
   
   /**
    * Verifica por AJAX que un archivo se puede descargar, antes de llamar a download.
	* Esta verificacion no se hace en download porque para descargar el archivo NO se
	* puede usar AJAX, y si no se usa AJAX y hay error, la pagina navega afectando al usuario.
	* Permite enviar feedback al usuario y evitar que la pagina actual navegue a otra si hay errores en la descarga.
	*/
   public function verifyDownloadAction()
   {
	  header('Content-type: application/json');
	  
	  $file = FileInfo::get($this->params['fileId']);
	  if (is_null($file))
	  {
	     return $this->renderString('{"status":"error", "msg":"No existe la referencia al archivo"}');
	  }
	  
	  $module = FilesModule::get($this->params['id']);
	  if (is_null($module))
	  {
	     return $this->renderString('{"status":"error", "msg":"No existe el modulo"}');
	  }
	  
	  $path = $module->getPath();
	  
	  // TODO: esto se va a solucionar cuando la path se elija
	  //       de una lista en lugar de ponerla a mano.
	  
	  // debe empezar con ./
	  if (!String::startsWith($path, './'))
	  {
		if (String::startsWith($path, '/'))
		{
			$path = '.'.$path;
		}
		else
		{
			$path = './'.$path;
		}
	  }
	  
	  // debe terminar con /
	  if (!String::endsWith($path, '/'))
	  {
		 $path .= '/';
	  }
	  
	  
	  if(!file_exists($path.$file->getFullName()))
	  {
	     return $this->renderString('{"status":"error", "msg":"No existe el archivo"}');
	  }
	  
	  return $this->renderString('{"status":"ok", "msg":"Validacion correcta"}');
   }
   
   /**
    * Descarga un archivo de un modulo creando un log.
	* Previamente se debio hacer la verificacion de que el archivo existe y se puede descargar con verifyDownload.
    */
   public function downloadAction()
   {
      // Sino viene doit, muestro la vista de download que se carga en un iframe oculto
      // y submitea el form para hacer el download, esta es la unica forma de controlar
      // errores de descarga de archivos, dar feedback y evitar que se navegue a otra pagina.
	  
	  $file = FileInfo::get($this->params['fileId']);
	  
	  // FIXME: file no existe
	  
	  $module = FilesModule::get($this->params['id']);
	  
	  // FIXME: modulo no existe
	  
	  $path = $module->getPath();
	  
	  // TODO: esto se va a solucionar cuando la path se elija
	  //       de una lista en lugar de ponerla a mano.
	  
	  // debe empezar con ./
	  if (!String::startsWith($path, './'))
	  {
		if (String::startsWith($path, '/'))
		{
			$path = '.'.$path;
		}
		else
		{
			$path = './'.$path;
		}
	  }
	  
	  // debe terminar con /
	  if (!String::endsWith($path, '/'))
	  {
		 $path .= '/';
	  }
	  
	  // INTENTO OBTENER MIME TYPE DEL ARCHIVO, pero si descargo siempre no es necesario!
	  /*
	  // deprecated
	  //return $this->renderString( mime_content_type( $path.$file->getFullName() ));
	  
	  // Funciona pero no con path, tiene que ser una URL
	  //$nfo = apache_lookup_uri($path.$file->getFullName());
	  
	  return $this->renderString( print_r( $nfo, true ));
	  
	  Mas info: http://www.darian-brown.com/php-function-to-get-file-mime-type/
	  */
	  
	  //
	  // TODO: log de descarga, quiero hora e ip del usuario
	  //
	  
	  // Descarga
	  $status = $this->downloadFile( $path.$file->getFullName(), $file, "application/force-download");

	  return $this->renderString( 'status: '. $status );
   }
   
   /**
    * Si en mime type uso "application/force-download" se descarga en lugar de verse online.
    */
   private function downloadFile ($path, $file, $mimetype)
   {
      $status = 0;
      if (($path != NULL) && file_exists($path))
      {
         if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']))
         {
            ini_set( 'zlib.output_compression','Off' );
         }
           
         header('Content-type: '. $mimetype);
         header('Content-Disposition: attachment; filename="'.$file->getFullName().'"'); 
         header('Expires: '.gmdate('D, d M Y H:i:s', mktime(date('H')+2, date('i'), date('s'), date('m'), date('d'), date('Y'))).' GMT');
         header('Accept-Ranges: bytes');
         header('Cache-control: private');                  
         header('Pragma: private');
           
         $size = $file->getSize();
         if(isset($_SERVER['HTTP_RANGE']))
         {
            list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
             
            str_replace($range, "-", $range);
            $size2 = $size-1;
            $new_length = $size2-$range;
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range$size2/$size");
         }
         else
         {
            $size2 = $size-1;
            header("Content-Range: bytes 0-$size2/$size");
            header("Content-Length: ".$size);
         }
         
         // TODO: usar FileSystem class
         if ($loadedFile = fopen($path, 'r'))
         {
            while(!feof($loadedFile) and (connection_status()==0))
            {
               print(fread($loadedFile, 4096*8)); // buffer 4096 bytes = 4KB
               flush();
            }
            $status = (connection_status() == 0);
            fclose($loadedFile);
         }
      }
      return($status);
   }
   
   /**
    * http://php.net/manual/en/function.header.php
    * 
    * function downloadFile( $fullPath ){ 

  // Must be fresh start 
  if( headers_sent() ) 
    die('Headers Sent'); 

  // Required for some browsers 
  if(ini_get('zlib.output_compression')) 
    ini_set('zlib.output_compression', 'Off'); 

  // File Exists? 
  if( file_exists($fullPath) ){ 
    
    // Parse Info / Get Extension 
    $fsize = filesize($fullPath); 
    $path_parts = pathinfo($fullPath); 
    $ext = strtolower($path_parts["extension"]); 
    
    // Determine Content Type 
    switch ($ext) { 
      case "pdf": $ctype="application/pdf"; break; 
      case "exe": $ctype="application/octet-stream"; break; 
      case "zip": $ctype="application/zip"; break; 
      case "doc": $ctype="application/msword"; break; 
      case "xls": $ctype="application/vnd.ms-excel"; break; 
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break; 
      case "gif": $ctype="image/gif"; break; 
      case "png": $ctype="image/png"; break; 
      case "jpeg": 
      case "jpg": $ctype="image/jpg"; break; 
      default: $ctype="application/force-download"; 
    } 

    header("Pragma: public"); // required 
    header("Expires: 0"); 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Cache-Control: private",false); // required for certain browsers 
    header("Content-Type: $ctype"); 
    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" ); 
    header("Content-Transfer-Encoding: binary"); 
    header("Content-Length: ".$fsize); 
    ob_clean(); 
    flush(); 
    readfile( $fullPath ); 

  } else 
    die('File Not Found'); 
    */
   
   /**
    * Editar un archivo de un modulo.
    */
   public function editFileAction()
   {
      $module = FilesModule::get($this->params['id']);
      $file = FileInfo::get($this->params['fileId']);
      
      if (isset($this->params['doit']))
      {
         header('Content-type: application/json');
         
         // TODO: ver que la ruta existe, si puede ser escrita y leida.
         // Sino puede ser escrita, no se puede ejecutar upload,
         // sino puede leerse, no se puede hacer scan.
        
         $file->setProperties($this->params);
         if (!$file->save())
         {
            print_r($file->getErrors()); // TODO: considerar errores para mostrar feedback msg
            return $this->renderString('{"status":"error", "msg":"Ha ocurrido un error, intente de nuevo"}');
         }
         
         // FIXME: si hay error en el salvado no deberia caer aca
         // FIXME: esto deberia retornar AJAX
         
         // FIXME: doit no deberia estar en params
         // TAMPOCO DEBERIA TENER LOS DATOS DEL FILE QUE FUERON SUBMITEADOS!!!!!
         //$params = (array)$this->params;
         //$params['doit'] = NULL;
         //return $this->redirect(array('action'=>'edit', 'params'=>array_filter($params)));
         
         return $this->renderString('{"status":"ok", "msg":"Archivo actualizado con éxito"}');
      }
      
      $this->params['module'] = $module;
      $this->params['file'] = $file;
   }
   
   /**
    * Quita un archivo del modulo.
    */
   public function removeFileAction()
   {
      $file = FileInfo::get($this->params['fileId']);
      $file->delete(); // Eliminacion fisica!
      
      header('Content-type: application/json');
      return $this->renderString('{"status":"ok", "msg":"Referencia a archivo eliminada con éxito", "id":'.$this->params['id'].', "pageId":'.$this->params['pageId'].', "zone":"'.$this->params['zone'].'"}');
   }
}

?>