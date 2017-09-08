<style>
   /* Tamanios y posiciones */
   div.zone {
        /*border: 1px solid #000;*/
        <?php if ($mode=='edit') : ?>
        padding: 15px; /* Algo de pad para ver la zona */
        border: 1px dashed #cfcfcf;
        <?php endif; ?>
   }
   /* Disenio liquido, contenido centrado */
   div#top {
        margin-bottom: 1em; /* Distancia entre el top del body */
        margin-left: 10%;
        margin-right: 10%;
   }
   /* Disenio liquido, contenido centrado */
   div#body {
        padding: 0px; /* Como no es una zona, es un container, no necesita padding */
        border: 0px;
        margin-left: 10%;
        margin-right: 10%;
        margin-bottom: 1em; /* Distancia entre el body y a3cols */
   }
   div#a3cols {
        text-align: center; /* Centra las 3 cols */
   }
   div#col1, div#col2, div#col3 {
        width: 30%;
        display: inline-block;
        text-align: left;
        vertical-align: top;
   }
      
   /* Colores */
   div#content {
      
        padding: 10px; /* Un poco de espacio */
      
        /* Content con gradiente de fondo: http://gradients.glrzad.com */
        background-image: linear-gradient(bottom, rgb(186,186,186) 30%, rgb(226,226,226) 75%);
        background-image: -o-linear-gradient(bottom, rgb(186,186,186) 30%, rgb(226,226,226) 75%);
        background-image: -moz-linear-gradient(bottom, rgb(186,186,186) 30%, rgb(226,226,226) 75%);
        background-image: -webkit-linear-gradient(bottom, rgb(186,186,186) 30%, rgb(226,226,226) 75%);
        background-image: -ms-linear-gradient(bottom, rgb(186,186,186) 30%, rgb(226,226,226) 75%);

        background-image: -webkit-gradient(
            linear,
            left bottom,
            left top,
            color-stop(0.30, rgb(186,186,186)),
            color-stop(0.75, rgb(226,226,226))
        );
      
        /* Borde con efecto de colores metalicos */
        border-bottom: 1px solid #E2E2E2;
        border-left: 1px solid #E2E2E2;
        border-right: 1px solid #DCDCDC;
        border-top: 1px solid #DCDCDC;
        
        /* Bordes bottom redondeados */
        border-bottom-right-radius: 5px;
        -moz-border-radius-bottomright: 5px;
        border-bottom-left-radius: 5px;
        -moz-border-radius-bottomleft: 5px;
   }
      
   <?php if ($mode=='show') : ?>
      div#topmenu {
        padding: 0px;
        border: 0px;
      }
   <?php endif; ?>
   div#topmenu ul {
        list-style-type: none; /* Menu no muestra bullets */
        margin: 0px; /* Debe ser igual al div#topmenu li margin-bottom para mostrar los botones en la posicion vertical correcta */
        padding: 0px;
   }
   div#topmenu li {
         display: inline-block; /* Items del menu se muestran horizontales */
         background-color: #2C2C2C;
         padding: 0px 10px 0px 10px;
         margin-right: 2px; /* Margen entre botones del menu */
         color: #fff;
         height: 2em; /* El alto del boton es el doble del de la letra */
         line-height: 2em; /* Forma que encontre para mostrar el texto de los botones en el medio, cuando los botones tienen un alto de 2em */
         
        /* Bordes superiores redondeados */
        border-top-right-radius: 5px;
        -moz-border-radius-topright: 5px;
        border-top-left-radius: 5px;
        -moz-border-radius-topleft: 5px;
   }
      
   /* Para submenues no quiero estilo de tabs */
   div#topmenu ul.submenu {
     position: absolute; /* Para que el LI que contiene este submenu no cambie su tamanio si el submenu es mas ancho */
   }
   div#topmenu ul.submenu li {
     display: block; /* Items del menu se muestran VERTICALES */
     padding: 0px 10px 0px 10px;
     margin: 0px; /* Margen entre botones del menu */
     border-top-right-radius: 0px;
     -moz-border-radius-topright: 0px;
     border-top-left-radius: 0px;
     -moz-border-radius-topleft: 0px;
     background-color: #dedede;
     color: #000;
     font-weight: normal;
  }
  div#topmenu ul.submenu li a {
    color: #000;
    font-weight: normal;
  }
  div#topmenu li a {
    text-decoration: none;
    color: #ccc;
  }
  div#topmenu li.active a {
    color: #000;
    font-weight: bold;
  }
  /* Boton activo del menu */
  div#topmenu .active {
    position: relative;
    top: 1px; /* Corro un poco para abajo para que las tabs queden cubriendo el borde de arriba del content para el boton activo. */
    background-color: #E2E2E2;
    color: #2C2C2C;
    /* Asimila el borde del content */
    border-right: 1px solid #DCDCDC;
    border-top: 1px solid #DCDCDC;
    border-left: 1px solid #E2E2E2;
  }
  /* Estilo para elementos del CMS */
  .moduleTopBar, .customModuleActions {
    background-color: #f0f0f0;
    overflow: auto;
    padding: 5px;
  }
  .moduleTopBar .moduleActions, .customModuleActions a {
    float: right;
  }
  img {
    border: none;
  }
  .subpages {
    margin: 10px;
    padding-left: 5px;
  }
  .navbar {
    margin: 10px;
    background-color: #a0a0ff;
    padding: 5px;
    color: #fff;
  }
  .navbar a {
    color: #fff;
    text-decoration: none;
  }
  .navbar .active {
    font-weight: bold;
  }
</style>

<?php
YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
echo CmsHelpers::navbar(array('page'=>$page));
?>

<div id="top" class="zone"><?php echo $top; ?></div>
<div id="body">
  <div id="topmenu" class="zone">
    <?php echo $topmenu; ?>
  </div>
  <div id="content" class="zone"><?php echo $content; ?></div>
</div>
<div id="a3cols">
  <div id="col1" class="zone"><?php echo $col1; ?></div>
  <div id="col2" class="zone"><?php echo $col2; ?></div>
  <div id="col3" class="zone"><?php echo $col3; ?></div>
</div>
<div id="footer"></div>

<?php
echo CmsHelpers::subpages(array('page'=>$page));
?>