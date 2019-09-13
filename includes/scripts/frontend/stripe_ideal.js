jQuery(document).ready(function() {
	var stripe = Stripe(localisation.stripe_key);
	var elements = stripe.elements();
	var idealBank = elements.create('idealBank');
	idealBank.mount('#ideal-bank-element');
	jQuery('#ideal_trigger').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox({
			'inline': true,
			'href': '#cqpim_payment_ideal',	
			'opacity': '0.5',
		});	
	});	
	var form = document.getElementById('payment-form');
	form.addEventListener('submit', function(event) {
		event.preventDefault();
		stripe.createSource({
			type: 'ideal',
			amount: document.querySelector('input[name="ideal_amount"]').value,
			currency: 'eur',
			owner: {
				name: document.querySelector('input[name="ideal_name"]').value,
			},
			redirect: {
				return_url: document.querySelector('input[name="ideal_return"]').value,
			},
		}).then(function(result) {
			if (result.error) {
				var errorElement = document.getElementById('error-message');
				errorElement.textContent = error.message;
			} else {
				stripeSourceHandler(result.source);
			}			
		});
	});
});
function stripeSourceHandler(source) {
  document.location.href = source.redirect.url;
}