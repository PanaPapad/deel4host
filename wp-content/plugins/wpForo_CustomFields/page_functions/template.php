<?php
/* 
    Variables and functions defined in the Globals.php file are available here.

    Example:

    $someVar = $GLOBALS['page_content_path'];

    function example_function($args){
        Do something
        some_global_function();
    }
*/

/*
    CODE BELOW RUNS ON ENTRY

    $someVar = x; <-- Not accessible in functions
    $GLOBALS['someVar'] = x; <-- Accessible in functions

    Inject JS code that will run on page load
    $somePHPVarJSON = json_encode($somePHPVar); <-- Convert PHP var to JSON string
    call_js_fn_onload("someJSFunction($somePHPVarJSON, 'someString')");
*/
?>