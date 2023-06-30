<?php
    //JS files to inject
    //JS files need to be under the page_content/JS dir
    $jsList = array(
        'globals.js',
    );
    inject_js($jsList);
    // CSS files to inject
    // CSS files need to be under the page_content/CSS dir
    $cssList = array(
        'globals.css',
    );
    inject_css($cssList);
?>
<div class="wrap">
    <!-- HTML GOES HERE -->
    <?php
        //Example php code injection. Best used for small snippets.
        //Define functions in page_functions/*.php
        //example_function_call();
    ?>
</div>
