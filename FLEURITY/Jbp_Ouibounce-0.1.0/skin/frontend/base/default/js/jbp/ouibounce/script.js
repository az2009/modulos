$jo = jQuery.noConflict();

$jo(function(){

	$jo('.motivo').click(function(){

		var opt = $jo(this).val();

		if(opt == 'encontrou_problema'){
			$jo('.textarea_problema').slideDown().show();
			$jo('#ouibounce-modal .modal').height(500);
		}else{
			$jo('#ouibounce-modal .modal').height(350);
			$jo('.textarea_problema').slideUp().hide();
			$jo('.res').val('');
		}
		$modal('.span-msg').empty();
	});

    var _ouibounce = ouibounce(document.getElementById('ouibounce-modal'), {
        aggressive: true,
        timer: 0,
        callback: function() {
        	$jo('#ouibounce-modal').show();
         }
      });

      // $modal('body').on('click', function() {
      //   $modal('#ouibounce-modal').hide();
      // });

      //$modal('#ouibounce-modal .modal-footer').on('click', function() {
      //  $modal('#ouibounce-modal').hide();
      //});

      $jo('#ouibounce-modal .modal').on('click', function(e) {
        e.stopPropagation();
      });

});

function closeOuibounce(){
	$jo('#ouibounce-modal').hide();
}