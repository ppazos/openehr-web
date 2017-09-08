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
    margin-left: 10%;
    margin-right: 10%;
    background-color: #23337E;
  }
  /* Disenio liquido, contenido centrado */
  div#body {
    
    /*
    padding: 0px;
    border: 0px;
    
    margin-bottom: 1em; / * Distancia entre el body y a3cols * /
    text-align: center; / * Centra las 3 cols * /
    */
    
    margin-left: 10%;
    margin-right: 10%;
    
    /* padding: 10px;  Un poco de espacio */
    
    background-color: #FFF;
    border: 1px solid #CEE3F8;

    
    /* Bordes bottom redondeados */
    border-bottom-right-radius: 5px;
    -moz-border-radius-bottomright: 5px;
    border-bottom-left-radius: 5px;
    -moz-border-radius-bottomleft: 5px;
    border-top-right-radius: 5px;
    -moz-border-radius-topright: 5px;
    border-top-left-radius: 5px;
    -moz-border-radius-topleft: 5px;
  }


  #container3 {
    float:left;
    width:100%;
    /* background:green; */
    overflow:hidden;
    position:relative;
  }
  #container2 {
        float:left;
        width:100%;
        /* background:yellow; */
        position:relative;
        right:33%;
  }
  #container1 {
        float:left;
        width:100%;
        /*background:red; */
        position:relative;
        right:33%;
  }


  div#col1, div#col2, div#col3 {
    /*
    width: 30%;
    display: inline-block;
    text-align: left;
    vertical-align: top;
    */
    height: 100%;
    border-right: 1px solid #CEE3F8;
    float:left;
  }
  div#col1 {
    width: 33%;
    position:relative;
    left: 66%;
    overflow:hidden;
  }
  div#col2 {
    width:33%;
    position:relative;
    left: 66%;
    overflow:hidden;
  }
  div#col3 {
    width:33%;
    position:relative;
    left: 66%;
    overflow:hidden;

    border-right: none;
    text-align: center;
  }
  
  
  /* Esta columna tendra un modulo menu */
  div#col1 ul {
    list-style-type: none; /* Menu no muestra bullets */
    margin: 0px;
    padding: 0px;
  }
  div#col1 ul li {
      
    display: block; /* Items del menu se muestran VERTICALES */
    padding: 0px 10px 0px 10px;
    margin: 0px; /* Margen entre botones del menu */
  }
      

  div#content {

    
  }
  div#footer {

    background-color: #efefef;
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
    background-color: #CEE3F8;
    padding: 5px;
    margin-left: 10%;
    margin-right: 10%;
  }
  .navbar a {
    color: #00F;
    text-decoration: none;
  }
  .navbar .active {
    font-weight: bold;
  }
</style>

<?php
YuppLoader::load('apps.cms2.helpers', 'CmsHelpers');
?>

<!-- zonas: top, col1, col2, col3, content, footer -->

<div id="top" class="zone"><?php echo $top; ?></div>

<?php
echo CmsHelpers::navbar(array('page'=>$page));
?>

<div id="body">

  <!-- http://matthewjamestaylor.com/blog/equal-height-columns-cross-browser-css-no-hacks -->
  <div id="container3">
    <div id="container2">
      <div id="container1">
        <div id="col1" class="zone"><?php echo $col1; ?></div>
        <div id="col2" class="zone"><?php echo $col2; ?></div>
        <div id="col3" class="zone"><?php echo $col3; ?></div>
      </div>
    </div>
  </div>

  <div id="content" class="zone"><?php echo $content; ?></div>
</div>
<div id="footer" class="zone"><?php echo $footer; ?></div>

<?php
echo CmsHelpers::subpages(array('page'=>$page));
?>