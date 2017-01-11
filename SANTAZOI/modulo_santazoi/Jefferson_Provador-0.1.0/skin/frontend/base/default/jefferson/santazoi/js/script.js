$jq = jQuery.noConflict();

var prov_checked = function(){
	$jq("div.provador-sim input[type='checkbox'].provador-sim").prop('checked',true);
}

var prov_unchecked = function(){
	$jq("div.provador-sim input[type='checkbox'].provador-sim").prop('checked',false);
}

$jq(function(){

	$jq("#product-addtoprov-button").click(function(){
		$jq("div.provador-sim input[type='checkbox'].provador-sim").prop('checked',true);
		//$jq(this).css('background-color','green');
		//$jq('.button.btn-cart').css('background-color','#2e8ab8');

	});

	$jq('.button.btn-cart').click(function(){
		$jq("div.provador-sim input[type='checkbox'].provador-sim").prop('checked',false);
		//$jq('.button.btn-cart').css('background-color','green');
		//$jq('#product-addtoprov-button').css('background-color','#2e8ab8');
	});

});
