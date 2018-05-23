<?php 
/*add global variables to footer*/
ScriptManager::registerGlobalVariables();
?>
<script src="//www.google.com/recaptcha/api.js?onload=onloadMyCallback&render=explicit" async defer ></script>
</body>
<script>
$(document).ready(function() {


	/* Apply fancybox to multiple items */
	
	$("a.group").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false
	});
	
});
</script>
</html>