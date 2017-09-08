<?php

YuppLoader::load('cms2.model.cms', 'Page');
YuppLoader::load('cms2.model.cms', 'PageZone');
YuppLoader::load('cms2.model.cms', 'Zone');
YuppLoader::load('cms2.model.cms', 'Module');
YuppLoader::load('cms2.model.cms', 'Layout');

YuppLoader::load('cms2.model.htmlModule', 'HtmlModule');
YuppLoader::load('cms2.model.menuModule', 'MenuModule');

YuppLoader::load('cms2.model.auth', 'User');

$layouts = array();

// Definicion de Layout
// ------ top -------
// - left - content -
// ----- bottom -----

// En disco tengo: skins/default/layout.template.php
$layouts[] = new Layout(array(
  'name' => 'default',
  'active' => false,
  'zones' => array(
    new Zone(array(
      'name'=>'top',
      'x'=>0,
      'y'=>0,
      'width'=>800,
      'height'=>100
    )),
    new Zone(array(
      'name'=>'left',
      'x'=>0,
      'y'=>100,
      'width'=>200,
      'height'=>400
    )),
    new Zone(array(
      'name'=>'content',
      'x'=>200,
      'y'=>100,
      'width'=>600,
      'height'=>400
    )),
    new Zone(array(
      'name'=>'bottom'
    ))
  )
));

/*
// En disco tengo: skins/3cols/layout.template.php
$layouts[] = new Layout(array(
  'name' => '3cols',
  'active' => false,
  'zones' => array(
    new Zone(array(
      'name'=>'top',
    )),
    new Zone(array(
      'name'=>'left'
    )),
    new Zone(array(
      'name'=>'content'
    )),
    new Zone(array(
      'name'=>'right'
    )),
    new Zone(array(
      'name'=>'bottom'
    ))
  )
));
*/

/*
$layouts[] = new Layout(array(
  'name' => 'cubik',
  'active' => false,
  'zones' => array(
    new Zone(array(
      'name'=>'top',
    )),
    new Zone(array(
      'name'=>'left'
    )),
    new Zone(array(
      'name'=>'content'
    )),
    new Zone(array(
      'name'=>'right'
    ))
  )
));
*/

$layouts[] = new Layout(array(
  'name' => 'webdesign',
  'active' => false,
  'zones' => array(
    new Zone(array(
      'name'=>'logo',
    )),
    new Zone(array(
      'name'=>'topmenu',
    )),
    new Zone(array(
      'name'=>'content'
    )),
    new Zone(array(
      'name'=>'col1'
    )),
    new Zone(array(
      'name'=>'col2'
    )),
    new Zone(array(
      'name'=>'col3'
    ))
  )
));

$layouts[] = new Layout(array(
  'name' => 'lindo1',
  'active' => false,
  'zones' => array(
    new Zone(array(
      'name'=>'top',
    )),
    new Zone(array(
      'name'=>'topmenu'
    )),
    new Zone(array(
      'name'=>'content'
    )),
    new Zone(array(
      'name'=>'col1'
    )),
    new Zone(array(
      'name'=>'col2'
    )),
    new Zone(array(
      'name'=>'col3'
    ))
  )
));

$layouts[] = new Layout(array(
  'name' => 'openehr',
  'active' => true,
  'zones' => array(
    new Zone(array(
      'name'=>'top_right',
    )),
    new Zone(array(
      'name'=>'content'
    )),
    new Zone(array(
      'name'=>'col1'
    )),
    new Zone(array(
      'name'=>'col2'
    )),
    new Zone(array(
      'name'=>'col3'
    )),
    new Zone(array(
      'name'=>'footer'
    ))
  )
));

$layouts[] = new Layout(array(
  'name' => 'citylights',
  'active' => false,
  'zones' => array(
    new Zone(array(
      'name'=>'top',
    )),
    new Zone(array(
      'name'=>'top_right'
    )),
    new Zone(array(
      'name'=>'content'
    )),
    new Zone(array(
      'name'=>'right'
    )),
    new Zone(array(
      'name'=>'footer'
    ))
  )
));

foreach ($layouts as $layout)
{
   if (!$layout->save()) print_r($layout->getErrors());
}

// abajo le seteo las paginas al menu
$menu = new MenuModule(array('title'=>'menu', 'showInAllPages'=>true));

// Defino paginas
$pages = array(
  new Page(array(
    'name'=>'default',
    'zones'=>array(
      new PageZone(array(
        'name'=>'top',
        // TODO: setear pagina
        'modules'=>array(
          new HtmlModule(array(
            'title'=>'banner',
            'content'=>'<div align="center"><img src="http://www.fsrgrp.com/sitebuilder/images/landscape_banner-924x339.jpg" border="0" alt="dsfgsdfg" title="sfgsdgds" width="760" /></div>',
            'showContainer'=>false
          ))
        )
      )),
      new PageZone(array(
        'name'=>'content',
        // TODO: setear pagina
        'modules'=>array(
          $menu,
          new HtmlModule(array(
            'title'=>'contenido1',
            'content'=>'<table border="0"><tbody><tr>
<td><img src="http://farm3.static.flickr.com/2396/2078094921_ee60c42d0f.jpg" border="0" /></td>
<td><a href="http://informatica-medica.blogspot.com/2011/07/modelado-de-procedimientos-con-openehr.html">Modelado de procedimientos con openEHR</a>
<div class="diggBox" style="margin: 4px; float: right;"><span class="db-wrapper db-clear db-large"><span><span class="db-container db-submit"><span class="db-body db-large"><span class="db-count"><br /></span><span class="db-copy"></span></span></span></span></span></div>
<div class="post-header">&nbsp;</div>
<a href="http://omowizard.wordpress.com/2011/07/11/anatomy-of-an-procedure-action-archetype/">Este</a> es otro excelente post de la <a href="http://omowizard.wordpress.com/showcase/about/">Dra. Heather Leslie</a>, donde adjunta una presentaci&oacute;n que describe el modelado de los procedimientos cl&iacute;nicos mediante arquetipos openEHR. Personalmente me gusta mucho esta forma de modelado, porque ayuda a que quede plasmado en el registro cl&iacute;nico todo el proceso de instrucciones/&oacute;rdenes y las acciones que se llevan a cabo para dichas &oacute;rdenes.&nbsp;</td>
</tr></tbody></table>',
            'showContainer'=>false
          )),
          new HtmlModule(array(
            'title'=>'contenido2',
            'content'=>'<img src="http://culturacomic.files.wordpress.com/2008/10/superman-logo.jpg" border="0" width="180" height="177" />',
            'showContainer'=>false
          )),
          new HtmlModule(array(
            'title'=>'contenido3',
            'content'=>'<u>dfgdfgdfg</u>'
          ))
        )
     ))
   )
 )),
 new Page(array(
    'name'=>'pagina2',
    'zones'=>array(
      new PageZone(array(
        'name'=>'top',
        // TODO: setear pagina
        'modules'=>array()
      )),
      new PageZone(array(
        'name'=>'content',
        // TODO: setear pagina
        'modules'=>array(
          $menu,
          new HtmlModule(array(
            'title'=>'contenido 21',
            'content'=>'<b>Modulo</b> html para mostrar'
          )),
          new HtmlModule(array(
            'title'=>'contenido 22',
            'content'=>'<i>que loco</i>'
          )),
          new HtmlModule(array(
            'title'=>'contenido 23',
            'content'=>'<u>uuuuuuuuuuuuuuu</u>'
          ))
        )
     ))
   )
 ))
);

$activeLayout = Layout::getActive();

// Setea pagina padre de cada pageZone de cada pagina.
foreach ($pages as $page)
{
   // Se recorren las zonas del layout activo.
   foreach ($activeLayout->getZones() as $zone)
   {
      // Si la pagina no tiene definida la zona que si esta en el layout, crearla.
      if ( ($pz = $page->getZone($zone->getName())) == NULL )
      {
         // Agrega la zona que esta en el layout pero no en la pagina.
         $pz = new PageZone(array('name'=>$zone->getName()));
         $page->addToZones($pz);
      }
   }
   
   // A todas las zonas de la pagina, les seteo a que pagina pertenecen.
   foreach ($page->getZones() as $pageZone)
   {
      $pageZone->setPage($page);
   }
   
   if (!$page->save()) print_r($page->getErrors());
   
   Logger::struct( $page );
}

// Seteo todas las paginas al menu
$menu->setPages($pages);
$menu->save();



// Usuarios por defecto
$admin = new User(array(
'name'=>'Admin',
'email'=>'admin@admin.com',
'usertype'=>User::TYPE_ADMIN,
'username'=>'admin',
'password'=>'admin'
));
$admin->save();

$editor = new User(array(
'name'=>'Editor',
'email'=>'editor@editor.com',
'usertype'=>User::TYPE_EDITOR,
'username'=>'editor',
'password'=>'editor'
));
$editor->save();

$content_editor = new User(array(
'name'=>'Content Editor',
'email'=>'ceditor@ceditor.com',
'usertype'=>User::TYPE_CONTENT_EDITOR,
'username'=>'ceditor',
'password'=>'ceditor'
));
$content_editor->save();

$user = new User(array(
'name'=>'User',
'email'=>'user@user.com',
'usertype'=>User::TYPE_USER,
'username'=>'user',
'password'=>'user'
));
$user->save();

?>