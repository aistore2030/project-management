jQuery(document).ready(function() {
	jQuery('.repeater').repeater({
		hide: function (deleteElement) {
			if(confirm('Are you sure you want to delete this role? Any Team Members that use this role will lose access to the site. This cannot be undone.')) {
				jQuery(this).slideUp(deleteElement);
			} else {
				return false;
			}
		},	
	});
	jQuery('.repeater2').repeater();
});