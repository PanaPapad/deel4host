<?php 
    /**
     * Inject a list of javascript files into the page.
     * JS files need to be under the page_content/JS dir
     * @param array $jsfileList The list of JS files to inject
     */
    function inject_js($jsfileList) {
        foreach ($jsfileList as $jsfile) {
            echo '<script src="'. plugin_dir_url(__FILE__). 'page_content/JS/' . $jsfile . '" defer></script>';
        }
    }
    $GLOBALS['jsCallCounter'] = 0;
    /**
     * Calls a javascript function after the page loads.
     * This is useful for calling js functions on load using data from the backend.
     * The call string should be a valid javascript function call.
     * The function will be called with the window.onload event.
     * The script tag will be deleted after it runs.
     * @param string $callString The string to call the function
     */
    function call_js_fn_onload($callString) {
        $scriptTagId = 'jsCall' . $GLOBALS['jsCallCounter'];
        echo '<script id="'. $scriptTagId .'"> window.onload = function() {' . $callString . 
            ';document.getElementById("'.$scriptTagId.'").remove();} </script>';
        $GLOBALS['jsCallCounter']++;
    }
    /**
     * Injects a javascript object or array into the page.
     * The object will be available in the page as a javascript variable.
     * @param string $objectName The name of the object
     * @param object|array $object The object to inject
     */
    function injectObject($objectName, $object){
        echo '<script> var ' . $objectName . ' = JSON.parse(\'' . json_encode($object) . '\'); </script>';
    }
    /**
     * Inject a list of css files into the page.
     * CSS files need to be under the page_content/CSS dir
     * @param array $cssList The list of CSS files to inject
     */
    function inject_css($cssList){
        foreach ($cssList as $cssfile) {
            echo '<link rel="stylesheet" href="'. plugin_dir_url(__FILE__). 'page_content/CSS/' . $cssfile . '">';
        }
    }
    //Constants
    global $wpdb;
    $GLOBALS['CUSTOM_JS'] = array();// List of JS files to inject, scripts are added at the admin_enqueue_scripts hook
    $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'] = $table_prefix = $wpdb->prefix .'custom_wpforo_fields';
    $GLOBALS['CUSTOM_WPFORO_TABLES']['POSTS'] = $table_prefix = $wpdb->prefix.'custom_wpforo_posts';
    $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'] = $table_prefix = $wpdb->prefix.'custom_wpforo_forms';
    $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'] = $table_prefix = $wpdb->prefix.'custom_wpforo_form_fields';
    $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'] = $table_prefix = $wpdb->prefix.'custom_wpforo_forum_forms';
?>