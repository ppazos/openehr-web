<?php

$m = Model::getInstance();

YuppLoader::load('core.mvc.form', 'YuppForm2');
YuppLoader::load('core.basic', 'YuppDateTime');

$user = $m->get('user');

YuppLoader::load('core.mvc', 'DisplayHelper');
YuppLoader::load('apps.cms2.helpers', 'CountryHelpers');

global $_base_dir;

?>
<html>
  <head>
    <style type="text/css">
      body {
        width: 570px;
        height: 525px;
      }
      
      /* Estilo para YuppForm */
      .field_container .label {
        display: inline;
        vertical-align: top;
      }
      .field_container .field {
        display: block;
      }
      input[type=text] {
        width: 280px;
      }
      .field_container .field input[type=submit] {
        width: 120px;
        float: right;
      }
      .field_container .field textarea {
        width: 300px;
        height: 200px;
      }
      
      fieldset {
        width: 545px;
        margin: 5px 0 5px 0;
        border: 1px solid #79C;
        padding: 0 0 10px 10px;
      }
      fieldset input[type=text] {
        width: 530px;
      }
      
      /* fieldset de 2 columnas */
      fieldset.two_cols select[name=country], fieldset.two_cols input {
        width: 260px;
      }
      fieldset.two_cols .field_container {
        width: 270px;
      }
      fieldset.two_cols .field_container .label {
        display: inline;
        vertical-align: top;
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
      .global_error {
        display: none;
        background-color: #F2DEDE;
        border: 1px solid #EED3D7;
        color: #B94A48;
        padding: 5px;
        margin-bottom: 10px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        -webkit-border-radius: 4px;
        border-radius: 4px;
        width: 530px;
      }
      .error_container {
        background-color: #F2DEDE;
        border: 1px solid #EED3D7;
        color: #B94A48;
        padding: 5px;
        margin-bottom: 10px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        -webkit-border-radius: 4px;
        border-radius: 4px;
        width: 520px;
      }
      .two_cols.error_container {
        width: 268px;
      }
      
      /* Deberia ir en CSS de todas las modales donde hay ingreso de datos */
      .error {
        border: 1px solid red;
      }
      
      .field_container {
        /*width: 290px;*/
        float: left;
        margin: 5px 0 0 0;
      }
      .field.submit {
        width: 555px;
        text-align: right;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <script type="text/javascript">
      $(document).ready( function()
      {
        parent.modal.modal('update');
      });
      
      var validate = function (formData, theForm, options) {
      	
        //console.log(formData);
        
        var filterHttp = new Array('googleplus', 'linkedin', 'facebook');
        for (i in formData)
        {
        	//console.log(formData[i]);
        	if (filterHttp.indexOf(formData[i].name) >= 0)
        	{
        		formData[i].value = formData[i].value.replace("http://", "");
        		formData[i].value = formData[i].value.replace("https://", "");
        	}
        }
        
        /*
        for (i in formData)
        {
        	console.log(formData[i]);
        }
        */
        
        return true;
      };
      
      // form submit success
      var callback = function(json, status, response) {
        
        console.log('callback');
        console.log(json);
        console.log(status);
        
        $('.error_container').remove(); // Quita errores anteriores
        $("input").removeClass('error');
        
        // Mismo codigo que en register.view.php
        if (json.status == 'ok')
        {
          parent.modal.modal('feedback', json.msg);
          
          // Hacer un refresh de toda la pagina para que se actualice todo lo que depende del usuario como el login.
          if (parent.location.toString().indexOf('?')==-1)
            parent.location = parent.location + '?flash.message='+ json.msg;
          else
            parent.location = parent.location + '&flash.message='+ json.msg;
        }
        else
        {
          parent.modal.modal('feedback', json.msg);
          for (var name in json.errors)
          {
            //console.log(name + ':' + json.errors[name]) // name value
            //$("input[name="+name+"]").css({'border':'1px solid red'});
            $("input[name="+name+"]").addClass("error");
            $("input[name="+name+"]").parent().append( '<div class="error_container">'+json.errors[name]+'</div>' );
          }
          
          // Pudo cambiar el alto de la ventana.
          parent.modal.modal('update');
        }
      };
    </script>
  </head>
  <body>
    <h1>Editar perfil</h1>
      
    <?php echo DisplayHelper::errors( $user ); ?>

    <div class="global_error"></div>

    <div class="create">
      <?php
         // Por si no tiene la fecha seteada.
         $birthDate = NULL;
         try { $birthDate = YuppDateTime::dateParts( $user->getBirthdate() ); }
         catch( Exception $e) {}
         
         // Armo valores para el select de paisese agrupados por continente
          // contient->country code->country name
          $continentCountryGroups = array();
          foreach (CountryHelpers::getContinentCountries() as $continentCode => $countryCodes)
          {
             $continentName = CountryHelpers::getContinentName($continentCode);
             if (!isset($continentCountryGroups[$continentName]))
             {
                $continentCountryGroups[$continentName] = array();
             }
    
             foreach ($countryCodes as $countryCode)
                $continentCountryGroups[$continentName][$countryCode] = CountryHelpers::getCountryName($countryCode);
          }
         
         $f = new YuppForm2( array('action'=>'editProfile', 'method'=>'post', 'isAjax'=>true, 'ajaxCallback'=>'callback', 'ajaxBeforeSubmit'=>'validate') );
         
         
         // Datos personales ======
         $personal = new YuppFormField2Group('Datos personales', array('class'=>'two_cols'));
         $personal
           ->add( YuppForm2::text(array('name'=>'name',  'value'=>$user->getName(),  'label'=>'Nombre') ) )
           ->add( YuppForm2::text(array('name'=>'email', 'value'=>$user->getEmail(), 'label'=>'Email' ) ) );
           //->add( YuppForm2::text(   array('name'=>'username', 'value'=>$user->getUsername(), 'label'=>'Usuario' ) ) );
           //->add( YuppForm2::text(   array('name'=>'password', 'value'=>$user->getPassword(),  'label'=>'Clave' ) ) );
         
         if ( $birthDate !== NULL )
           $personal->add( YuppForm2::date( array(
                            'name'       =>'birthdate', 
                            'value_year' =>$birthDate['year'],
                            'value_month'=>$birthDate['month'],
                            'value_day'  =>$birthDate['day'], 
                            'label'      =>'Fecha de nacimiento'
                          ) ) );
         else
           $personal->add( YuppForm2::date( array(
                            'name'       =>'birthdate', 
                            'value_year' =>1980,
                            'label'      =>'Fecha de nacimiento'
                          ) ) );
         
         $personal->add( YuppForm2::select( array('name'=>"country", 'value'=>$user->getCountry(), 'options'=>$continentCountryGroups, 'label'=>'Pais', 'hasGroups'=>true ) ) );
         
         // Redes sociales ======
         $redes = new YuppFormField2Group('Redes sociales');
         $redes
           ->add( YuppForm2::text( array('name'=>'facebook',   'value'=>$user->getFacebook(),   'label'=>'Vinculo a perfil de Facebook') ) )
           ->add( YuppForm2::text( array('name'=>'linkedin',   'value'=>$user->getLinkedin(),   'label'=>'Vinculo a perfil de LinkedIn') ) )
           ->add( YuppForm2::text( array('name'=>'twitter',    'value'=>$user->getTwitter(),    'label'=>'Nombre de usuario en Twitter') ) )
           ->add( YuppForm2::text( array('name'=>'googleplus', 'value'=>$user->getGoogleplus(), 'label'=>'Vinculo a perfil de Google+') ) );

         // Informacion laboral ======
         $laboral = new YuppFormField2Group('Informacion profesional', array('class'=>'two_cols'));
         $laboral
           ->add( YuppForm2::text( array('name'=>'company',  'value'=>$user->getCompany(),  'label'=>'Institucion/Empresa') ) )
           ->add( YuppForm2::text( array('name'=>'position', 'value'=>$user->getPosition(), 'label'=>'Cargo') ) );
         
         $f->add( $personal )
           ->add( $redes )
           ->add( $laboral )
           ->add( YuppForm2::submit( array('name'=>'doit', 'label'=>'Guardar cambios')) );
         
         YuppFormDisplay2::displayForm( $f );
      ?>
    </div>
  </body>
</html>