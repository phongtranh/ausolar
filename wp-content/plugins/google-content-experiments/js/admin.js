  var $j = jQuery.noConflict();
		
  $j(function() {
    $j("#wpe_gce_active").change( function() {
      $j(".contexpcode").toggle();
    });
  });