jQuery(document).ready(function(){
		jQuery('.churchthemes_options').slideUp();

		jQuery('.churchthemes_section h3').click(function(){
			if(jQuery(this).parent().next('.churchthemes_options').css('display')==='none')
				{	jQuery(this).removeClass('inactive').addClass('active').children('img').removeClass('inactive').addClass('active');

				}
			else
				{	jQuery(this).removeClass('active').addClass('inactive').children('img').removeClass('active').addClass('inactive');
				}

			jQuery(this).parent().next('.churchthemes_options').slideToggle('slow');
		});
});