<?php
 if(!function_exists('apache_get_modules') ){ phpinfo(); exit; }
 $res = 'Module Unavailable';
 if(in_array('mod_rewrite',apache_get_modules())) 
 $res = 'Module Available';
?>
<html>
<head>
<title>A mod_rewrite availability check !</title></head>
<body>
<p></p>
</body>
</html>