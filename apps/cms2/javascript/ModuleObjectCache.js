// Cache de objetos de modulos
function ModuleObjectCache()
{
  this.cache = new Array();
  
  /**
   * Agrega un objeto obj para el modulo module.
   */
  this.add = function(module, obj)
  {
    if (!this.cache[module])
    {
      this.cache[module] = new Array();
    }
    
    this.cache[module].push( obj );
  };
  
  /**
   * Obtiene todos los objetos agregados por module. Si no se agrega ningun objeto, deberia retornar undefined.
   */
  this.getObjects = function(module)
  {
    return this.cache[module];
  }
  
  /**
   * Devuelve la cantidad de objetos agregados por el module.
   */
  this.objectCount = function(module)
  {
    if (this.cache[module])
    {
      return this.cache[module].length;
    }
    
    return 0;
  }
  
  /**
   * Obtiene el objeto con indice idx de los objetos agregados por module.
   */
  this.getObject = function(module, idx)
  {
    if (this.cache[module] && this.cache[module][idx])
    {
      return this.cache[module][idx];
    }
    
    return undefined;
  }
}