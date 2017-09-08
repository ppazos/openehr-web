<?php
/*
 * Created on 04/02/2012
 */

$page_title = 'openEHR: comunidad de habla hispana';

$cms_sender_email = 'pablo@openehr.org.es';
$cms_sender_name = 'openEHR.org.es';

$sendPassword_subject = 'Recordatorio de clave https://'. $_SERVER['HTTP_HOST'];
$sendPassword_text = "A continuacion se le reenvia su clave:\n\n".
                     "clave: [USER_PASSWORD]\n\n".
                     "Ingrese al portal mediante el siguiente enlace: [DOMAIN]\n\n".
                     "Por cualquier consulta: [ADMIN_EMAIL]";

$changePassword_subject = 'Ha cambiado su clave en https://'. $_SERVER['HTTP_HOST'];
$changePassword_text = "Ha establecido su nueva clave a: [USER_PASSWORD]\n\n".
                       "Ingrese al portal mediante el siguiente enlace: [DOMAIN]\n\n";

$approve_subject = 'Su registro en https://'. $_SERVER['HTTP_HOST'] .' ha sido aprobado';
$approve_text =
      "Su registro ha sido aprobado\n\n".
      "Puede ingresar al sitio mediante:\n\n".
      "usuario: [USER_NAME]\n\n".
      "clave: [USER_PASSWORD]\n\n".
      "Ingrese al portal mediante el siguiente enlace: [DOMAIN]\n".
	  "Tambien puede ingresar al foro de la comunidad: https://openehr.org.es/foro \n\n".
      "Por cualquier consulta: [ADMIN_EMAIL]";

// Correo al admin cuando un usuario se registra
$register_subject = 'Se ha registrado un nuevo usuario en https://'. $_SERVER['HTTP_HOST'];
$register_text =
      "Datos del usuario registrado:\n\n".
      "usuario: [USER_NAME]\n\n".
      "correo: [USER_EMAIL]\n\n".
      "Ver: [DOMAIN]";

?>