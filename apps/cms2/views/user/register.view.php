<?php

$m = Model::getInstance();

YuppLoader::load('core.mvc.form', 'YuppForm2');
YuppLoader::load('apps.cms2.helpers', 'CountryHelpers');

?>
<html>
  <head>
    <!-- TODO: el estilo dependera del layout seleccionado -->
    <style type="text/css">
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      body {
        width: 580px;
        height: 300px;
      }
      form {
        margin-top: 10px;
      }
      input[type=text], input[type=password], select[name=country] {
        width: 280px;
      }
      .field.submit {
        text-align: right;
        padding-top: 10px;
        padding-right: 20px;
        width: 575px;
      }
      .global_error {
        display: none;
        background-color: #F2DEDE;
        border: 1px solid #EED3D7;
        color: #B94A48;
        padding: 6px;
        margin-bottom: 10px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        -webkit-border-radius: 4px;
        border-radius: 4px;
        width: 550px;
      }
      .error_container {
        background-color: #F2DEDE;
        border: 1px solid #EED3D7;
        color: #B94A48;
        padding: 6px;
        margin-bottom: 10px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        -webkit-border-radius: 4px;
        border-radius: 4px;
        width: 268px;
      }
      
      .field_container {
        width: 290px;
        float: left;
        margin: 0 0 5px 0;
      }
    </style>
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <script type="text/javascript">

		  // No puede renderearse sino es como modal
		//console.log(parent); window
		//console.log(parent.modal); undefined sino se muestra como modal
		if (parent.modal == undefined)
		{
		   location.href = "https://<?php echo $_SERVER['HTTP_HOST']; ?>";
		}
	
      $(document).ready( function() {
        
        parent.modal.modal('update');
      });
      
      var callback = function(json, status, response) {
        
        // TODO: verificar si currio algun error en el registro y mostrar feedback
        console.log($(json));
        
        // Borra error glogal actual
        $('.global_error').hide();
        $('.global_error').text(''); // Para que no core texto largo
        
        // Quita errores anteriores
        $('.error_container').remove();
        $("input").removeClass('error');
        
        //console.log(json.status); // ok/error
        //console.log(status); // success
        //console.log($(response)); // response.responseText == json
        
        if (json.status == 'ok') // FIXME: el mensaje no viene en json.msg?
        {
          //parent.modal.modal('feedback', 'Registro exitoso, su registro queda pendiente de aprobacion');
          var fb = $('#global_feedback', parent.document);
          fb.feedback('Registro exitoso, su registro queda pendiente de aprobacion');
          
          // Cierro la ventana modal en la que me estoy mostrando
          parent.modal.modal('hide');
        }
        else
        {
          //parent.modal.modal('feedback', json.msg); // FIXME: se corta si es un mensaje largo
          $('.global_error').text(json.msg); // Para que no core texto largo
          $('.global_error').show();
          
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
      
      // FIXME: YuppForm no tiene soporte para callbacks en errores de conexion.
    </script>
  </head>
  <body>
    <h1>Registro de usuario</h1>
    <div class="global_error"></div>
    <?php
      
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

      $f = new YuppForm2(array("action"=>"register", 'method'=>'post', "isAjax"=>true, "ajaxCallback"=>"callback") );
      $f->add( YuppForm2::text(     array('name'=>"name",      'value'=>'', 'label'=>"Nombre") ) )
        ->add( YuppForm2::text(     array('name'=>"email",     'value'=>'', 'label'=>"Email" ) ) )
        ->add( YuppForm2::text(     array('name'=>"username",  'value'=>'', 'label'=>"Nombre de usuario" ) ) )
        ->add( YuppForm2::password( array('name'=>"password",  'value'=>'', 'label'=>"Clave" ) ) ) // TODO: password retype
        ->add( YuppForm2::date(     array('name'=>"birthdate", 'value_year'=>1980, 'label'=>"Fecha de nacimiento") ) )
        //->add( YuppForm2::hidden(   array('name'=>"usertype",  'value'=>User::TYPE_PENDING ) ) )
        ->add( YuppForm2::select(   array('name'=>"country",   'value'=>'', 'options'=>$continentCountryGroups, 'label'=>'Pais', 'hasGroups'=>true ) ) )
        ->add( YuppForm2::text(     array('name'=>"company",   'value'=>'', 'label'=>"Institucion") ) )
        ->add( YuppForm2::text(     array('name'=>"position",  'value'=>'', 'label'=>"Cargo") ) )
        ->add( YuppForm2::submit(   array('name'=>"doit",      'label'=>"Enviar registro")) );

      YuppFormDisplay2::displayForm( $f );
    ?>
    <?php // Humanity check
      //print_r ( User::$humanity_test );
      
      $question_idx = rand( 0, count(User::$humanity_test) - 1 );
      
      //echo $question_idx;
      
      $questions = array_keys( User::$humanity_test );
      $question_text = $questions[$question_idx];
      
      //echo $question_text;
    ?>
    <script type="text/javascript">
      // Fields to append to the form
      // TODO: radio button answers should be random placed
      var humanity_test = $('<div>Verificacion<div><?php echo $question_text; ?><input type="hidden" name="question" value="<?php echo $question_text; ?>" /><label><input type="radio" name="humanity_check_response" value="No se" /> No se</label> <label><input type="radio" name="humanity_check_response" value="No" /> No</label> <label><input type="radio" name="humanity_check_response" value="Si" /> Si</label></div></div>');
      
      $submit_button_container = $('#form_0 > div:last-child');
      $submit_button_container.before(humanity_test);
      
      //console.log(humanity_test);
      //console.log($submit_button_container);
    </script>
  </body>
</html>