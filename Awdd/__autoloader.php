<?php
 function my_autoload ($pClassName) 
{
	if (file_exists(__DIR__ . "/" . $pClassName . ".php")):
		 include(__DIR__ . "/" . $pClassName . ".php");
	endif;
   
}
spl_autoload_register("my_autoload");
?>
