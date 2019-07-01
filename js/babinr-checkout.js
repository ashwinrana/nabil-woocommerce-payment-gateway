jQuery(document.body).on('update_checkout', function(e){ 
	e.stopImmediatePropagation();
	console.log("Here i am working");
});
// var successCallback = function(data) {
 
// 	var checkout_form = $( 'form.woocommerce-checkout' );
 
// 	// add a token to our hidden input field
// 	// console.log(data) to find the token
 
// 	// deactivate the tokenRequest function event
 
// 	// submit the form now
// 	console.log(checkout_form);
 
// };
 
// var errorCallback = function(data) {
//     console.log('Error Is here: ' +data);
// };

// jQuery(function($){
 
// 	var checkout_form = $( 'form.woocommerce-checkout' );
// 	console.log(checkout_form);
 
// });