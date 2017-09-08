<style>
  body {
    padding: 0px;
    margin: 0px;
    padding-bottom: 10px;
  }
  .zone {
    background-color: #ffcc00;
    /* overflow: auto; */
    /*overflow-x: hidden; este overflow hace que no se vea el modulo cuando lo arrastro a otra zona, dicha zona lo tapa */ /* sin scrol horizontal */
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
  .moduleContent {
    background-color: #ddeeff;
    padding: 5px;
    overflow: auto;
  }
  .customModuleActions {
    display: inline-block;
    float: right;
  }
  .customModuleActions img {
    border: 0px;
  }
  
  /* CSS para la estructura de la skin */
  table#layout {
    width: 100%;
    border: 0px;
  }
  table#layout tr {
    padding: 0px;
    margin: 0px;
  }
  table#layout td {
    padding: 0px 10px 5px 10px;
    vertical-align: top;
  }
  #top {
    height: 100px;
  }
  #left, #right {
    width: 200px;
  }
  #content {
    
  }
  #bottom {
    min-height: 200px;
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
  }
  ul.submenu {
    margin-left: 15px; /* Para que el submenu aparezca un poco a la derecha del item padre */
    padding: 2px; /* Para que no agregue espacios innecesarios */
    background-color: #ccddff;
    border: 1px solid #99aaff;
  }
  
  .menu_item {
    margin-right: 2px;
    display: fixed;
  }
  .menu_item a {
    padding: 3px;
    padding-left: 5px;
    padding-right: 5px;
  }
  .menu_item a:hover {
    background-color: #aaccff;
  }
  .menu_item .submenu {
    /*background-color: #ffccaa;*/
    /*padding-left: 5px;*/
    /*display: none;*/
  }
  
</style>
    
<table id="layout">
  <tr>
    <td id="top" colspan="3" class="zone"><?php echo $top; ?></td>
  </tr>
  <tr>
    <td id="left" class="zone"><?php echo $left; ?></td>
    <td id="content" class="zone"><?php echo $content; ?></td>
    <td id="right" class="zone"><?php echo $right; ?></td>
  </tr>
  <tr>
    <td id="bottom" colspan="3" class="zone"><?php echo $bottom; ?></td>
  </tr>
</table>

<?php
YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
CmsHelpers::subpages(array('page'=>$page));
?>