
<div id="map_canvas_<?php echo $module->getId(); ?>" class="map_canvas"></div>

<?php if ($mode=='show') : ?>
  <script type="text/javascript">
    // Le hago trampa al show para que cargue el edit.js para el show tambien, asi carga la api de google maps.
    (function(){
      script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = '<?php echo $_base_dir .'/apps/cms2/javascript/'; ?>mapModule/edit.js';;
      document.getElementsByTagName("head")[0].appendChild(script);
    })();
  </script>
<?php endif; ?>

<script type="text/javascript">
  // FIXME: esta variable se sobreescribe con cada instancia de mapa que se cargue!!!
  // Para pasarle datos del PHP al JS, que quedan cacheados para cuando el mapa se carga y renderea.
  var mapData = {
    zoom: <?php echo $module->getZoom(); ?>,
    center_lat: <?php echo $module->getCenterLat(); ?>,
    center_lon: <?php echo $module->getCenterLon(); ?>,
    containerId: "map_canvas_<?php echo $module->getId(); ?>" 
  };
  

  try
  {
    // Cuando creo un nuevo modulo y lo pone en la pagina me dice que
    // "cache" no existe... y es una variable global de displayPage!
    // Lo mismo debe pasar con __maps_loaded !!!
    // Por eso uso parent que funciona. Lo raro es que es el mismo document, y no es un iframe.
    
    // Lo que pasa es que el codigo del nuevo modulo se ejecuta en el insert
    // del nuevo nodo obtenido desde el servidor al terminar de crear el modulo
    // y eso esta dentro de un iframe, por eso hay problema con que no se ven
    // las variables globales...
    
    // No uso parent.cache porque en la vista del modulo existente, el cache esta
    // definido, y en la vista de un nuevo modulo, no uso el cache porque uso el
    // codigo de abajo para mostrarlo.
    cache.add('mapModule', mapData);
  }
  catch (err)
  {
    // Siempre tira error para modulos nuevos, no importa.
    //console.log(err);
  }

  // Siempre es window=displayPage
  //console.log(parent);

  // Si muestra un modulo existente, window=displayPage
  // Si muestra un modulo nuevo, window=createModule!!!
  //console.log(window);


  // Si los mapas de la pagina ya se cargaron
  // Le hago lo del typeof porque si no se declaro la variable
  // quiere decir que todavia no se cargo el mapa, porque la
  // variable esta declarada en el edit.js
  if (typeof(parent.__maps_loaded) != "undefined" && parent.__maps_loaded)
  {
     // Esto se ejecuta siempre cuando se crean nuevos modulos, no cuando esta mostrando uno existente.
     // El if esta de mas, siempre hay que ir a parent.google porque la ventana actual
     // (createModule dentro del iframe de la modal) no tiene definido google.
     if (typeof(google) == "undefined") google = parent.google;
     
     // Si window no es en la que se carga google maps, me dice que google no esta definido. 
     
     var map = new google.maps.Map2(parent.document.getElementById(mapData.containerId));
     map.setCenter(new google.maps.LatLng(mapData.center_lat, mapData.center_lon), mapData.zoom);
     
     //console.log('ejecuta crear nuevo mapa!');
  }
</script>

<style>
  #map_canvas_<?php echo $module->getId(); ?> {
    width: 100%;
    height: 300px;
  }
</style>