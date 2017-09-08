<?php

$m = Model::getInstance();

$module = $m->get('module');
$pagesNotInMenu = $m->get('pagesNotInMenu');

YuppLoader::load('core.mvc', 'DisplayHelper');
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo h('css', array('app'=>'cms2', 'name'=>'modal')); ?>
    <style>
      /* Para que el document padre sepa que tamanio darle a la modal, se debe especificar en CSS */
      /* Esta tiene contenido variable en alto, no se puede setear estaticamente con css */
      body {
        width: 300px;
        height: 480px;
      }
      label {
        display: inline-block;
        width: 55px;
      }
      label.name {
        display: inline-block;
        width: 180px;
      }
      input[type="text"] {
        width: 200px;
      }
      .container {
        padding: 0;
        width: 275px;
      }
      #menu ul {
        padding: 0;
        margin: 0;
      }
      #menu ul li {
        padding: 10px;
        border: 1px solid #36c;
        background-color: #9cf;        
        list-style: none;
      }
      .removeItem {
        text-align: center;
        padding: 2px;
        width: 24px;
        float: right;
        border: 1px solid #000080;
        background-color: #9999ff;
        cursor: pointer;
      }
      .editItem {
        cursor: pointer;
      }
      .add_item, #menu {
        border: 1px solid #36c;
        margin-top: 5px;
        padding: 5px;
      }
      .actions {
        padding: 10px 0 0 0;
        text-align: right;
      }
      .actions #edit { /* edicio de links apagado por defecto */
        display: none;
      }
    </style>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-1.7.1.min')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery.form-2.84')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery/jquery-ui-1.8.17.sortable.min')); ?><!-- para el sortable del menu -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'jquery.feedback-1.0.0')); ?>
    <?php echo h('js', array('app'=>'cms2', 'name'=>'ajaxFormErrorHandler')); ?>
    
    <!-- solo para MenuModule -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'superfish-1.4.8/js/jquery.hoverIntent.min')); ?><!-- Para que no dispare onmouseover en seguida, espera que el mouse se detenga arriba para dispararlo. -->
    <?php echo h('js', array('app'=>'cms2', 'name'=>'superfish-1.4.8/js/superfish')); ?>
    <script type="text/javascript">
      
      $(document).ready( function() {
        
        // =====================================================================================
        // Para editar el menu con sorting.
        $("#menu > ul").sortable({
          dropOnEmpty: true
        });
        
        // =====================================================================================
        // Handler de click para quitar un elemento del menu y 
        // devolverlo a la lista de paginas que no estan en el menu.
        $(".removeItem").live('click', function(evn) {

          pageName = $(this).prev().val();
          pageId = $(this).prev().prev().val();
             
          // Argegar el elemento al select:
          $('#select_page').append('<option value="'+pageId+'">'+pageName+'</option>');
          
          // Quita el item
          $(this).parent().remove();
          
          // Adaptar de nuevo el tamanio de la pantalla modal a su contenido
          //adaptModalSize();
          
        });
        
        // ===========================================================
        // Edicion de links
        
        var editItem;
        
        $(".editItem").live('click', function(evn) {
          
          editItem = $(this);                    // item que se esta editando
          pageName = editItem.prev().val();      // name
          pageId = editItem.prev().prev().val(); // link
          
          //console.log(pageName, pageId);
          //console.log(editItem);
          
          // Muesta datos a editar
          $('#label').val(pageName);
          $('#url').val(pageId);
          
          // Cambiar el boton de agregar link para modificar
          $('.actions #add').hide();
          $('.actions #edit').show();
        });
        
        $("#edit_link_button").click( function(evn) {
          
          editItem.prev().val( $('#label').val() );      // name
          editItem.prev().prev().val( $('#url').val() ); // link
          editItem.prev().prev().prev().text( $('#label').val() ); // label
          
          $("#cancel_link_button").click();
        });
        
        // Oculta edicion y muestra agregar link de nuevo
        $("#cancel_link_button").click( function(evn) {
          
          $('#label').val('');
          $('#url').val('');
          
          $('.actions #add').show();
          $('.actions #edit').hide();
          
          editItem = undefined;
        });
        // ===========================================================
        
        
        // =====================================================================================
        // Agregar un item al menu y sacarlo de la lista de paginas que no estan en el menu
        $("#add_item_button").click( function(evn) {

          // Toma del select el nombre de la pagina.
          pageName = $('#select_page :selected').text()
         
          if ( pageName == "" )
          {
            alert("No quedan paginas para agregar al menu");
            return false;
          }
         
          // FIXME: esto devuelve el id? no deberia ser $('#select_page :selected').val()
          pageId = $('#select_page').val()
         
          // Sacar el option del select
          $('#select_page :selected').remove()
         
          // TODO: hacer funcion addItem
          $("#menu > ul").append('<li>'+pageName+'<input type="hidden" name="items[]" value="'+pageId+'" /><input type="hidden" name="labels[]" value="'+pageName+'" /><div class="removeItem">X</div></li>');
          
          // Adaptar de nuevo el tamanio de la pantalla modal a su contenido
          //parent.modal.modal('update');
        });
        
        
        $("#add_link_button").click( function(evn) {

          // Toma del select el nombre de la pagina.
          label = $('#label').val();
          url = $('#url').val();
          
          if ( label == "" )
          {
            alert("Ingrese una etiqueta");
            return false;
          }
          if ( url == "" )
          {
            alert("Ingrese una url");
            return false;
          }
         
          // Sacar los valores
          $('#label').val('');
          $('#url').val('');
         
          // TODO: hacer funcion addItem
          $("#menu > ul").append('<li>'+label+'<input type="hidden" name="items[]" value="'+url+'" /><input type="hidden" name="labels[]" value="'+label+'" /><div class="removeItem">X</div></li>');
      
          // Adaptar de nuevo el tamanio de la pantalla modal a su contenido
          //parent.modal.modal('update');
        });
        
        
        // Submitea el form por ajax
        $('#editForm').ajaxForm({
          
          // Cuando el servidor responde ok, quiero actualizar
          // automaticamente el HTML del modulo sin hacer F5.
          success: function (res, status, response) {
            
            
            // TODO: verificar el json si tira ok o error en el status del res json
            
            
            // Este es el modulo que cambie en el dom
            var module = $('#<?php echo $module->getClass().'__'.$module->getId(); ?>', parent.document);
            
            // Pido el contenido del modulo actualizado para actualizarlo en la pagina
            $.ajax({
              url: '<?php echo h('url', array('controller'=>'cms', 'action'=>'moduleContent', 'params'=>array('class'=>$module->getClass(), 'id'=>$module->getId(), 'pageId'=>$m->get('pageId'), 'zone'=>$m->get('zone')))); ?>',
              success: function (newModuleContent, status) {
                
                var item = $(newModuleContent);
                
                // Actualiza solo el contenido!
                module.children('.moduleContent').html(item);
                
                // FIX: para que solucione los submenus del menu recien insertado
                item.superfish();


                // Cierro la ventana modal en la que me estoy mostrando
                parent.modal.modal('hide');
                
                
                // Feedback
                $('#global_feedback', parent.document).feedback('Modulo actualizado correctamente');
                
              },
              error: ajaxFormErrorHandler
            });
          }
        }); // submit form por ajax
        
        parent.modal.modal('update');
      });
    </script>
  </head>
  <body>
    <form id="editForm" method="post" action="<?php echo h('url', array('action'=>'edit')); ?>" class="container">
      <input type="hidden" name="id" value="<?php echo $module->getId(); ?>" />
      <input type="hidden" name="pageId" value="<?php echo $m->get('pageId'); ?>" />
      <input type="hidden" name="zone" value="<?php echo $m->get('zone'); ?>" />

      <div class="add_item">
        <label>Vertical:</label>
        <input type="checkbox" name="vertical" <?php echo (($module->getVertical())?'checked="true"':''); ?>" />
      </div>
      <div class="add_item">
        <label>Nivel:</label>
        <select name="level">
          <option value="0" <?php echo (($module->getLevel()==0)?'selected="true"':''); ?>>0</option>
          <option value="1" <?php echo (($module->getLevel()==1)?'selected="true"':''); ?>>1</option>
          <option value="2" <?php echo (($module->getLevel()==2)?'selected="true"':''); ?>>2</option>
          <option value="3" <?php echo (($module->getLevel()==3)?'selected="true"':''); ?>>3</option>
          <option value="4" <?php echo (($module->getLevel()==4)?'selected="true"':''); ?>>4</option>
          <option value="5" <?php echo (($module->getLevel()==5)?'selected="true"':''); ?>>5</option>
          <option value="" <?php echo (($module->getLevel()==NULL)?'selected="true"':''); ?>>todos</option>
        </select>
      </div>
      <div class="add_item">
        <label>Página:</label>
        <select name="select_page" id="select_page">
          <?php foreach ($pagesNotInMenu as $pageNIM): ?>
            <option value="<?php echo $pageNIM->getId(); ?>"><?php echo $pageNIM->getName(); ?></option>
          <?php endforeach; ?>
        </select>
        <input id="add_item_button" type="button" value="Agregar pagina" />
      </div>
      <div class="add_item">
        <label>Etiqueta:</label> <input name="label" id="label" type="text" /><br/> 
        <label>URL:</label> <input name="url" id="url" type="text" /><br/>
        <div class="actions">
          <div id="add">
            <input id="add_link_button" type="button" value="Agregar link" />
          </div>
          <div id="edit">
            <input id="edit_link_button" type="button" value="Actualizar link" />
            <input id="cancel_link_button" type="button" value="Cancelar" />
          </div>
        </div>
      </div>
      <div id="menu">
        <ul>
          <?php foreach ($module->getItems() as $item): ?>
            <li>
              <?php if ($item->isLink()) : ?>
                <label class="name"><?php echo $item->getLabel(); ?></label>
                <input type="hidden" name="items[]" value="<?php echo $item->getUrl(); ?>" />
                <input type="hidden" name="labels[]" value="<?php echo $item->getLabel(); ?>" />
                <span class="editItem"><?php echo h('img', array('app'=>'cms2', 'src'=>'edit.gif')); ?></span>
              <?php else : ?>
                <label class="name"><?php echo $item->getPage()->getName(); ?></label>
                <input type="hidden" name="items[]" value="<?php echo $item->getPageId(); ?>" />
                <input type="hidden" name="labels[]" value="<?php echo $item->getPage()->getName(); ?>" />
              <?php endif; ?>
              <span class="removeItem">X</span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="actions">
        <input type="submit" name="doit" value="Guardar" />
      </div>
    </form>
  </body>
</html>