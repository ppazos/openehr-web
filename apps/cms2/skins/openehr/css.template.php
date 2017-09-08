<link rel="shortcut icon" href="https://openehr.org.es/favicon.ico">
<style type="text/css">
  body {
    margin: 0px 0px 20px 0px;
    background-color: #efefef;
  }
  p {
    margin-top: 0px;
  }
  h1 {
    font-size: 1.4em;
    margin-top: 0;
  }
  h2 {
    font-size: 1.3em;
    font-style: italic;
    color: #666666;
    margin-top: 0;
  }
  h3 {
    font-size: 1.2em;
    font-style: italic;
    margin-top: 0;
  }
  h4 {
    font-size: 1.2em;
    font-weight: normal;
    margin-top: 0;
  }
  .moduleContent {
    margin-top: 5px; /* Para que no aparezca el contenido pegado al moduleTopBar */
  }
  #logo {
    width: 312px; /* ancho de la imagen */
  }
  #top_right .moduleContent {
    margin: 0; /* Para que no baje el menu principal */
    text-align: right;
  }
  
  
  #login_box {
    border: 1px solid #23337E;
    border-top: 0;
  }
  #login_box.logged {
    padding-top: 1px;
  }
  #login_box.logged a {
  }
  #login_box img {
    position: relative;
    top: 5px;
  }
  
  /** == MenuModule == **/
  .MenuModule li {
    padding: 2px 0 2px 0; /* Un poco de espacio vertical entre items del menu. */
  }
  
  /** === FilesModule === **/
  .files_module_filter_container {
    margin: 10px;
  }
  .files_module_file_filter {
    width: 240px;
  }
  .files_module_file {
    display: table;
    width: 100%;
    margin-bottom: 10px;
  }
  .files_module_file:hover {
    background-color: #efefff;
  }
  .files_module_file_icon {
    display: table-cell;
    vertical-align: top;
    width: 32px;
    padding: 3px;
  }
  .files_module_file_actions {
    display: table-cell;
    padding: 3px;
    width: 60px;
  }
  .files_module_file_data {
    display: table-cell;
    padding: 3px;
    vertical-align: top;
  }
  .files_module_file_name {
  }
  .files_module_file_size {
    color: #999;
    display: inline-block;
    font-size: .8em;
  }
  .files_module_file_lastupdate {
    color: #999;
    float: right;
    display: inline-block;
    font-size: .8em;
  }
  .files_module_file_description
  {
    font-size: .9em;
    color: #555;
    padding: 5px 0px 5px 0px;
  }
  /** === /FilesModule === **/
  

  /* Tamanios y posiciones */
  div.zone {
    <?php if ($mode=='edit') : ?>
    padding: 15px; /* Algo de pad para ver la zona */
    border: 1px dashed #cfcfcf;
    <?php endif; ?>
  }
  
  #top_bg {
    background-color: #23337E;
    padding: 0;
    margin: 0;
    /*height: 89px;*/ /* queda height 99 por el padding */
  }
  #top_container {
    margin: 0;
    margin-left: 10%;
    margin-right: 10%;
    /*padding: 0px 10px 0px 10px;*/
    padding: 0px;
    display: table;
    width: 80%;
    vertical-align: middle;
  }
  #footer_container {
    margin: 0px;
    padding: 10px;
  }
  #footer {
    margin-left: 10%;
    margin-right: 10%;
  }
  
  /* logo */
  #top {
    display: table-cell;
    vertical-align: top;
    padding: 0 5px 0 0;
    margin: 0;
    width: 312px;
    min-width: 312px; /* Sin esto no muestra el logo */
    /*height: 89px;*/
    height: 99px; /* Dejo 10px mas que el H de la imagen para margen */
    background: url('https://openehr.org.es/apps/cms2/skins/openehr/logo1.png') center center no-repeat;
  }
  #top a {
    display: block;
    width: 100%;
    height: 100%;
  }
  
  /* zona top_right */
  #top_right {
    display: table-cell;
    vertical-align: bottom;
    width: 100%;
    position: relative;
    padding: 0;
    margin: 0;
  }
  #top_right h2 {
    color: #fff;
    font-style: italic;
    position: absolute;
    top: 35px; /* Mueve para abajo el eslogan para alinearlo con el logo */
    font-family: arial;
    font-size: 1.6em;
    margin: 0;
  }
  
  /* zona content */
  #content {
    padding: 10px;
	dosplay: table-cell;
	/*width: 100%;*/
  }
  
  /* Disenio liquido, contenido centrado */
  #body_container {
    width: 100%;
    background-color: #fafafa;
    padding: 10px 0 0 0;
    border-bottom: 1px solid #DDD;
  }
  #body {

	margin: 10px 10% 10px 10%;
	
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
	
	/*
	display: table;
	table-layout: fixed;
	width: 80%;
	margin: 0 10% 0 10%;
	*/
  }

  /* Para que las 3 columnas sean del mismo alto */
  #container1, #container2 {
    /*display: table;*/
	/*display: table-row;*/
	
	display: table;
	table-layout: fixed;
	width: 100%;
	/*margin: 0 10% 0 10%;*/
  }
  #col1, #col2, #col3 {
    display: table-cell;
	overflow: hidden;
    padding: 10px;
    vertical-align: top;
    border-right: 1px solid #CEE3F8;
  }
  #col1 {
    width: 190px;
  }
  #col2 {
    width: 100%;
  }
  #col3 {
    border-right: none;
    width: 260px;
  }
  
  /* Esta columna tendra un modulo menu */
  #col1 ul {
    /* list-style-type: none; */
    margin-left: -20px;
    /*padding: 0px;*/
  }
  #col1 ul li {
    /* display: block; Items del menu se muestran VERTICALES
    padding: 0px 10px 0px 10px;
    margin: 0px; Margen entre botones del menu */
  }

  /* Estilo para elementos del CMS */
  .moduleTopBar, .customModuleActions {
    background-color: #f0f0f0;
    overflow: auto;
    padding: 5px 5px 5px 5px;
    font-weight: bold;
    font-size: 1.2em;
  }

  #col1 .moduleContainer .moduleContent {
    margin-bottom: 10px;
    padding-bottom: 5px;
  }
  
  .moduleTopBar .moduleActions, .customModuleActions a {
    float: right;
  }
  img {
    border: none;
  }
  .subpages {
    background-color: #CEE3F8;
    padding: 5px;
    margin-left: 10%;
    margin-right: 10%;
    margin-bottom: 10px;
  }
  .navbar {
    /*margin: 10px;*/
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
  
  /* Menu top */
  #top_right ul {
    list-style-type: none; /* Menu no muestra bullets */
    margin: 0px; /* Debe ser igual al div#topmenu li margin-bottom para mostrar los botones en la posicion vertical correcta */
    padding: 0px;
  }
  #top_right li {
    display: inline-block; /* Items del menu se muestran horizontales */
    background-color: #dddddd;
    padding: 6px;
    margin-right: 2px; /* Margen entre botones del menu */
    color: #fff;
    /* height: 2em; */ /* El alto del boton es el doble del de la letra */
    /* line-height: 2em; */ /* Forma que encontre para mostrar el texto de los botones en el medio, cuando los botones tienen un alto de 2em */
         
    /* Bordes superiores redondeados */
    border-top-right-radius: 5px;
    -moz-border-radius-topright: 5px;
    border-top-left-radius: 5px;
    -moz-border-radius-topleft: 5px;
  }
  #top_right li a {
    text-decoration: none;
    color: blue;
    padding: 3px;
  }
  #top_right li.active a {
    color: #000;
    font-weight: bold;
  }
  #top_right li a:hover {
    color: #ff0000;
  }
  #top_right li.active {
    position: relative;
    /*top: 1px;*/ /* Corro un poco para abajo para que las tabs queden cubriendo el borde de arriba del content para el boton activo. */
    background-color: #fafafa;
  }
  #top_right li.active:hover {
    background-color: #fff; /* No quiero que cambie el bg on hover */
  }
  #top_right li:hover {
    /* background-color: #ffffcc; */
  }
  
  /* responsive */
  @media screen and (max-width: 1000px) {
    /* Ajusta el tamanio del slogan para que no se quiebre */
    #top_right h2 {
      font-size: 1.4em;
    }
    /* La columna 3 va para abajo */
    #col3 {
      display: table-row;
    }
  }
  @media screen and (max-width: 885px) {
    /* Ajusta el tamanio del slogan para que no se quiebre */
    #top_right h2 {
      font-size: 1.2em;
    }
  }
  /* responsive */
</style>