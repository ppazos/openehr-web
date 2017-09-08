<?php

$m = Model::getInstance();

YuppLoader::load("core.mvc.form", "YuppForm2");
YuppLoader::load("core.basic", "YuppDateTime");

$user = $m->get('user');

YuppLoader :: load('core.mvc', 'DisplayHelper');

global $_base_dir;

?>
<html>
   <head>
      <style type="text/css">
         /* Estilo para YuppForm */
         .field_container {
            width: 540px;
            text-align: left;
         	display: block;
            padding-top: 10px;
         }
         .field_container .label {
            display: inline;
            padding-right: 10px;
            vertical-align: top;
         }
         .field_container .field {
            display: block;
         }
         .field_container .field input {

         }
         .field_container .field input[type=text] {
         	width: 400px;
         }
         .field_container .field input[type=submit] {
            width: 100px;
         }
         .field_container .field textarea {
            width: 540px;
            height: 200px;
         }
         
         body {
            font-family: arial, verdana, tahoma;
            font-size: 12px;
            background-color: #efefef;
          }
          table {
            border: 1px solid #000;
            /* spacing: 0px; */
            border-collapse: separate;
            border-spacing: 0px;
          }
          td {
            border-bottom: 1px solid #ddd;
            padding: 5px;
            background-color: #f5f5f5;
          }
          #actions {
            background: #fff url(<?php echo $_base_dir; ?>/images/shadow.jpg) bottom repeat-x;
            border: 1px solid #ccc;
            border-style: solid none solid none;
            padding: 7px 12px;
          }
          #actions a {
            padding-right: 5px;
            padding-left: 5px;
          }
      </style>
   </head>
   <body>
      <h1>Editar usuario</h1>
      
      <?php echo DisplayHelper::errors( $user ); ?>

      <div class="create">
      <?php
         // Por si no tiene la fecha seteada.
         $birthDate = NULL;
         try { $birthDate = YuppDateTime::dateParts( $user->getBirthdate() ); }
         catch( Exception $e) {}
         
         $f = new YuppForm2( array('action'=>'save', 'method'=>'post') );
         $f->add( YuppForm2::hidden( array('name'=>'id',       'value'=>$user->getId()) ) )
           ->add( YuppForm2::text(   array('name'=>'name',     'value'=>$user->getName(),  'label'=>'Nombre') ) )
           ->add( YuppForm2::text(   array('name'=>'email',    'value'=>$user->getEmail(), 'label'=>'Email' ) ) )
           ->add( YuppForm2::text(   array('name'=>'username', 'value'=>$user->getUsername(), 'label'=>'Usuario' ) ) );
           //->add( YuppForm2::text(   array('name'=>'password', 'value'=>$user->getPassword(),  'label'=>'Clave' ) ) );
         
         if ( $birthDate !== NULL )
           $f->add( YuppForm2::date(
                          array(
                            'name'       =>'birthdate', 
                            'value_year' =>$birthDate['year'],
                            'value_month'=>$birthDate['month'],
                            'value_day'  =>$birthDate['day'], 
                            'label'      =>'Fecha de nacimiento'
                          ) ) );
         else
            $f->add( YuppForm2::date(
                          array(
                            'name'       =>'birthdate', 
                            'value_year' =>1980,
                            'label'      =>'Fecha de nacimiento'
                          ) ) );
                                     
         $f->add( YuppForm2::select(
                          array(
                            'name'    => 'usertype', 
                            'value'   => $user->getUsertype(), 
                            'label'   => 'Tipo',
                            'options' => array(
                                User::TYPE_PENDING =>'pendiente',
                                User::TYPE_USER    =>'usuario',
                                User::TYPE_EDITOR  =>'editor',
                                User::TYPE_ADMIN   =>'admin',
                            )
                          ) ) )
           ->add( YuppForm2::text(   array('name'  =>'company',  'value'=>$user->getCompany(),  'label'=>'Institucion') ) )
           ->add( YuppForm2::text(   array('name'  =>'position', 'value'=>$user->getPosition(), 'label'=>'Cargo') ) )
           ->add( YuppForm2::submit( array('name'  =>'doit',     'label'=>'Guardar cambios')) )
           ->add( YuppForm2::submit( array('action'=>'show',     'label'=>'Cancelar')) );
         
         YuppFormDisplay2::displayForm( $f );
      ?>
      </div>
   </body>
</html>