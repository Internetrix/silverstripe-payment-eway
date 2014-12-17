;(function($) {
	$.entwine('sws', function($){
		$('form#Form_PayForm input:submit').entwine({
			onmatch : function() {
				var self = this;

				this.on('click', function(e) {
					e.preventDefault();
					
					//after click, disable submit button. prevent submitting payment form twice.
					$(this)
						.prop('disabled', 'disabled')
						.val('Submitting...')
						.closest('form')
						.submit();
				});
			}
		});
	});
})(jQuery);
