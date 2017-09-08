/*!
 * jQuery Table Filter Plugin
 * version: 1.0.0 (18-DEC-2011)
 * @requires jQuery v1.6.2 or later
 *
 * @author Pablo Pazos Gutierrez <pablo.swp@gmail.com>
 *
 * Licensed under Apache 2.0 license:
 * http://www.apache.org/licenses/LICENSE-2.0.html
 */
;(function($) {

  /**
   * What this does?
   * ---------------
   *
   * This plugin should be applied on an input[type=text],
   * that will be used to input the filter criteria.
   * The filter will apply on a table, and the filter will
   * hide the table TRs that do not match the filter criteria.
   *
   * Take into account the structure of the table
   * --------------------------------------------
   *
   * The table should have the following structure.
   * If the column number to filter is 1, then the
   * value on this column *** will be taken to
   * compare with the filter criteria. 
   *
   * table id=tableId
   *  thead
   *   tr
   *    th
   *    th
   *    ...
   *  tbody
   *   tr
   *    td *** (column 1)
   *    td
   *    ...
   *   tr
   *    td *** (column 1)
   *    td
   *    ...
   *
   *
   * Code sample:
   * ------------
   *
   * <input type="text" name="filter" id="filter" />
   *
   * $(document).ready(function() {
   *   $('#filter').tableFilter( $('#tableId'), 1 );
   * });
   *
   *
   * TODOs:
   * ------
   *
   * - Filter by multiple columns
   * - Highlight the matched filter with the text value 
   *
   */

  /**
   * table: table object to filter on.
   * column: column number that contains the text to compare with the filter criteria.
   */
  $.fn.tableFilter = function(table, column) {
  
    //console.log('tableFilter');
    //console.log(this); // input
    //console.log(table); // content
    
    var input = this;
    
    // this is the text input in which the plugin is applied
    this.bind('input', function(evt) {
    //this.bind('change', function(evt) {
    //this.live('change', function(evt) {
    //input.keypress( function(evt) {
      
      //console.log('keypress');
      //console.log(evt);
      
      $.each( table.children(), function (i, tr) {
           
        $tr = $(tr);
        
        //console.log($tr);
        //console.log(this); // tr
        //console.log($tr.text().toLowerCase()); // texto de cada archivo> nombre, desc, etc.
        //console.log(input.val());
        //console.log( $tr.text().toLowerCase().indexOf( input.val().toLowerCase() ) );
        
        // Filters by the text from the input (evt.target.value), against the text
        // on the #column column of the table, both are compared in lowercase.
        if ( $tr.text().toLowerCase().indexOf( input.val().toLowerCase() ) == -1 )
        {
          $tr.hide();
        }
        else
        {
          $tr.show();
        }
      });
    });
    
  };

})(jQuery);