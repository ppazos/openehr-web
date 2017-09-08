
  // La pongo en true cuando todos los mapas estan cargados,
  // sirve para darme cuenta si creo un nuevo mapa y esto
  // esta en true, puedo ejecutar codigo en displayModule.template,
  // sino se que aca se va a crear el mapa usando la api de gmaps.
  var __maps_loaded = false;

  // INTENTO con api loader
  /*
  Ver ejemplo de dynamic load: http://code.google.com/intl/es/apis/loader/
  
  function mapsLoaded() {
     var map = new google.maps.Map2(document.getElementById("map"));
     map.setCenter(new google.maps.LatLng(37.4419, -122.1419), 13);
   }
   
   function loadMaps() {
     google.load("maps", "2", {"callback" : mapsLoaded});
   }
   
   function initLoader() {
     var script = document.createElement("script");
     script.src = "https://www.google.com/jsapi?key=INSERT-YOUR-KEY&callback=loadMaps";
     script.type = "text/javascript";
     document.getElementsByTagName("head")[0].appendChild(script);
   }
  
  */
  //alert('mapModule append script');
  
  (function(){
    __script = document.createElement('script');
    __script.type = 'text/javascript';
    __script.src = 'https://www.google.com/jsapi?key=ABQIAAAAexQMi3UN0Gde2yzNrKOyChT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTpLvf7j9va6AFeTfPjyf10n8OsMQ&callback=pepe';
    document.getElementsByTagName("head")[0].appendChild(__script);
  })();
  
  var mapLoaded = function ()
  {
     // v3
     //var map = new google.maps.Map(document.getElementById('map_canvas_14'), {mapTypeId: google.maps.MapTypeId.ROADMAP});
     
     // v2
     // FIXME: id de la div del mapa, es dinamico desde php
     // FIXME: lo mismo para zoo, y centro.
     // Podria ponerlos en un array global cuando carga el modulo desde php, y luego desde
     // aqui recorrerlo e ir creando los mapas. Hay que considerar que esto siempre se carga
     // luego de los modulos, asi que el array estara lleno cuando esto se ejecute.
     //var map = new google.maps.Map2(document.getElementById("map_canvas_14"));
     //map.setCenter(new google.maps.LatLng(37.4419, -122.1419), 6);
     
     
     var mapDataObjs = cache.getObjects('mapModule');
     //console.log(mapDataObjs);
     
     // ====================================================================
     // FIXME: cuando creo un mapa y no hay otro modulo mapa en la pagina,
     // mapDataObjs es null y me da una excepcion al hacer .length abajo.
     // Se deberia corregir que cuando creo un nuevo mapa, sus datos se 
     // carguen en el cache antes de hacer el render!!!.
     // ====================================================================
     
     // Crea cada mapa con sus datos...
     for (var i=0; i<mapDataObjs.length; i++)
     {
        var data = mapDataObjs[i];
        var map = new google.maps.Map2(document.getElementById(data.containerId));
        map.setCenter(new google.maps.LatLng(data.center_lat, data.center_lon), data.zoom);
     }
     
     // FIXME: esto funciona bien en el load desde cero, pero cuando creo un modulo de mapa este codigo no se ejecuta
     //        y no muestra el nuevo mapa. Deberia ver si es un nuevo mapa, que se ejecute el JS para crear el mapa.
     
     __maps_loaded = true; // Para saber si puedo ejecutar JS en los modulos nuevos. 
  }
  
  var pepe = function() 
  {
     google.load("maps", "2", {"callback" : mapLoaded}); // Si le pongo v3 me pide sensor=false y no se como pasarselo
     //alert('mapModule callback pepe');
  }
