$('.MenuModule a.edit_menu').live('click', function(evt) { // Es live por si creo menues y los muestro con ajax, quiero el edit activo en esos nuevos modulos.
   
   modal.modal('load', this.href);
   
   return false;
});