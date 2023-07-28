<>php 
add_action('rest_api_init', function () {
  register_rest_route('myplugin/v1', '/endpoint', array(
    'methods' => 'GET',
    'callback' => 'my_awesome_func',
    'permission_callback' => function () {
      return current_user_can('administrator');
    }
  ));
});

function my_awesome_func(WP_REST_Request $request) {
  // Perform your data retrieval logic here
  $data = array(
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => 'value3'
  );
  // Return response
  return new WP_REST_Response($data, 200);
}

?>