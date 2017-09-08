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
      body {
        width: 220px;
        height: 230px;
        margin: 0;
      }
    </style>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.modal-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <script type="text/javascript">
      $(document).ready( function() {
        
        // Notifica para que el parent actualice el tamanio del iframe
        parent.modal.modal('update');
        
        <?php if ($m->flash('message')) { ?>
          parent.modal.modal('feedback', '<?php echo $m->flash('message'); ?>');
        <?php } ?>
      });
      
      // Callback del submit del form.
      var _callback = function(json, status, response) {
        
        if (json.status == 'ok')
        {
          if (json.changePassword == '1')
          {
            if (parent.location.toString().indexOf('?')==-1)
              parent.location = parent.location + '?flash.message='+ json.msg;
            else
              parent.location = parent.location + '&flash.message='+ json.msg;
          }
          else
          {
            var fb = $('#global_feedback', parent.document);
            fb.feedback(json.msg);
          
            parent.modal.modal('hide');
          }
        }
        else
        {
          parent.modal.modal('feedback', json.msg);
        }
      };
    </script>
  </head>
  <body>
    <h1>Cambiar clave</h1>
      
    <?php echo DisplayHelper::errors( $user ); ?>

    <div class="create">
      <?php
        $f = new YuppForm2( array('action'=>'changePassword', 'method'=>'post', 'isAjax'=>true, 'ajaxCallback'=>'_callback') );
        $f->add( YuppForm2::hidden(   array('name'=>'id',              'value'=>$user->getId()) ) )
          ->add( YuppForm2::password( array('name'=>'password',        'label'=>'Clave actual' ) ) )
          ->add( YuppForm2::password( array('name'=>'new_password',    'label'=>'Nueva clave' ) ) )
          ->add( YuppForm2::password( array('name'=>'new_password_rt', 'label'=>'Repita nueva clave' ) ) )
          ->add( YuppForm2::submit(   array('name'  =>'doit',          'label'=>'Cambiar clave')) );
         
        YuppFormDisplay2::displayForm( $f );
      ?>
    </div>
  </body>
</html>