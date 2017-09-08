<?php

$m = Model::getInstance();

YuppLoader::load("core.mvc.form", "YuppForm2");

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
    <h1>Crear usuario</h1>
      
    <?php echo DisplayHelper::errors( $user ); ?>

    <div class="create">
      <?php
         $f = new YuppForm2( array('action'=>'create', 'method'=>'post') );
         $f->add( YuppForm2::text( array('name'=>'name',         'value'=>$m->get('name'),     'label'=>'Nombre') ) )
           ->add( YuppForm2::text( array('name'=>'email',        'value'=>$m->get('email'),    'label'=>'Email' ) ) )
           ->add( YuppForm2::text( array('name'=>'username',     'value'=>$m->get('username'), 'label'=>'Nombre de usuario' ) ) )
           ->add( YuppForm2::password( array('name'=>'password', 'value'=>$m->get('password'), 'label'=>'Clave' ) ) )
           ->add( YuppForm2::date( array('name'=>'birthdate',    'value_year'=>1980,           'label'=>'Fecha de nacimiento') ) ) // TODO: date select
           ->add( YuppForm2::select(
                      array(
                        'name'    => 'usertype', 
                        'value'   => $m->get('usertype'), 
                        'label'   => 'Tipo',
                        'options' => array( // TODO i18n
                                        User::TYPE_PENDING =>'pendiente',
                                        User::TYPE_USER    =>'usuario',
                                        User::TYPE_CONTENT_EDITOR  =>'editor de contenido',
                                        User::TYPE_EDITOR  =>'editor',
                                        User::TYPE_ADMIN   =>'admin' )
                      )
                   )
                )
           ->add( YuppForm2::text( array('name'=>'company',  'value'=>$m->get('company'),  'label'=>'Institucion') ) )
           ->add( YuppForm2::text( array('name'=>'position', 'value'=>$m->get('position'), 'label'=>'Cargo') ) )
           ->add( YuppForm2::submit( array('name'  =>'doit', 'label'=>'Crear')) )
           ->add( YuppForm2::submit( array('action'=>'list', 'label'=>'Cancelar')) );
         YuppFormDisplay2::displayForm( $f );
      ?>
    </div>
  </body>
</html>