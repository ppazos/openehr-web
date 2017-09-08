$('.HtmlModule a.edit_html').live('click', function(evt) {

   modal.modal('load', this.href);

   return false;
});