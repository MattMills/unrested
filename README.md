# unrested

Unrested is a very simple PHP REST API.

Example usage:

```
<?php
require_once('unrested/unrested.php');
$unrested = new unrested();

$unrested->register('GET', '/demo/{string:string_value}/{integer:integer_value}', function($input){
    return array(200, $input); 
});

$unrested->run();
```

Alongside the .htaccess to get requests to this file, the request would be /api/v1/demo/this_is_a_string/12345
