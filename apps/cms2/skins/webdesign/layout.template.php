<style>
  body {
    padding: 0px;
    margin: 0px;
    padding-bottom: 10px;
    background-color: #dfdfef;
  }
  .zone {
    background-color: #ffcc00;
    vertical-align: top;
    
    <?php if ($mode=='edit') : ?>
    border: 1px dashed #000; /* En modo edit quiero ver los bordes de las zonas. */
    min-height: 120px;       /* Ayuda a visualizar la zona aunque no tenga contenido */
    padding: 10px;           /* Ayuda a visualizar la zona aunque no tenga contenido */
    <?php endif; ?>
  }
  .moduleContainer {
    border: 1px solid #000;
  }
  .moduleTopBar {
    background-color: #aaccff;
    padding: 5px;
  }
  .moduleActions {
    display: inline-block;
    float: right;
  }
  .moduleActions img {
    border: 0px;
  }
  <?php if ($mode=='edit') : ?>
  .moduleContent {
    background-color: #ddeeff;
    padding: 5px;
    overflow: show;
    min-height: 30px;
  }
  <?php endif; ?>
  <?php if ($mode=='show') : ?>
  .moduleContent {
    background-color: transparent;
    padding: 5px;
    overflow: show;
    min-height: 30px;
  }
  <?php endif; ?>
  .customModuleActions {
    display: inline-block;
    float: right;
  }
  .customModuleActions img {
    border: 0px;
  }
  
  /* Para que se vea bien el mapa, solo para modulos MapModule */
  .MapModule .moduleContent {
    height: 300px;
  }
  
  .map_canvas {
    width: 100%;
    height: 280px;
  }
  
  /* CSS para la estructura de la skin */

/* column container */
.colmask {
  position:relative;      /* This fixes the IE7 overflow hidden bug and stops the layout jumping out of place */
  clear:both;
  float:left;
  width:100%;         /* width of whole page */
  overflow:hidden;    /* This chops off any overhanging divs */
}

/* 2 column left menu settings */
.leftmenu {
    /*background:#FFD8B7;*/
}
.leftmenu .colright {
    float:left;
    width:200%;
    position:relative;
    left:200px;
    /* background:#fff; */
}
.leftmenu .col1wrap {
    float: right;
    width: 50%;
    position:relative;
    right: 200px;
    padding-bottom:1em;
}

  #logo {
    float: left;
    width: 210px;
    position: relative;
    right: 200px; /* width - right es el padding-left del logo */
    background-color: green;
    min-height: 180px;
    padding: 0px;
  }

  #topmenu {
    margin: 0 0 0 210px; /* Si el margen derecho es igual que el #logo.width, donde termina el logo empieza el topmenu. */
    position:relative;
    right:100%;
    overflow: hidden;
    background-color: red;
    min-height: 180px;
  }
  
  <?php if ($mode=='show') : ?>
  #logo .moduleContent {
    background-color: transparent;
    text-align: center;
    padding: 0px;
  }
  #topmenu .moduleContent {
    background-color: transparent;
    margin-bottom: 5px;
    padding: 0px;
  }
  #topmenu .MenuModule {
    padding: 5px;
    height: 30px;
  }
  <?php endif; ?>

  #content {
    clear: both;
    background-color: purple;
  }
  #bottom {
    width: 100%;
    overflow: auto;
    border: 0px;
    margin: 0px;
  }
  #col1, #col2, #col3 {
    overflow: visible;
    height: 200px;
    background-color: blue;
    padding: 10px;
  }
  #col1, #col3 {
    width: 33%;
  }
  
  /* FIXME: para estas cosas deberia haber un CSS por defecto */
  /* Para los items de modulos MenuModule */
  ul.menu, ul.submenu {
    margin: 0;
    padding: 0;
    list-style-type: none;
  }
  /* Para que aparezca el menu desplegado sobre el contenido de la pagina */
  ul.horizontal ul, ul.vertical ul {
    position: absolute;
  }
  ul.horizontal li {
    display: inline;
    float: left;
    height: auto;
  }
  ul.vertical li {
    /*overflow: auto;*/
    /*display: fixed;*/
    padding: 10px;
  }
  ul.submenu {
    margin-left: 25px; /* Para que el submenu aparezca un poco a la derecha del item padre */
    padding: 2px; /* Para que no agregue espacios innecesarios */
    width: 100%;
  }
  
  .menu_item {
    margin-right: 2px;
  }
  .menu_item a {
    text-decoration: none;
    padding: 10px;  
    background-color: #2175bc;  
    color: #fff;  
  }
  .menu_item a:hover {
    background-color: #2586d7; 
  }
  .menu_item .submenu {
    /*background-color: #ffccaa;*/
    /*padding-left: 5px;*/
    /*display: none;*/
  }
  
</style>

<!-- por css este disenio hace que tenga 2 columnas: |-- LOGO --|-- TOPMENU --| -->
<div class="colmask leftmenu">
  <div class="colright">
    <div class="col1wrap">
      <div id="topmenu" class="zone"><?php echo $topmenu; ?></div>
    </div>
    <div id="logo" class="zone"><?php echo $logo; ?></div>
  </div>
</div>

<div id="content" class="zone"><?php echo $content; ?></div>

<table id="bottom">
  <tr>
    <td id="col1" class="zone"><?php echo $col1; ?></td>
    <td id="col2" class="zone"><?php echo $col2; ?></td>
    <td id="col3" class="zone"><?php echo $col3; ?></td>
  </tr>
</table>

<?php
YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
CmsHelpers::subpages(array('page'=>$page));
?>