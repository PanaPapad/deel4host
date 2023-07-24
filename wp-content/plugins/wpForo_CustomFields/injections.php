<?php
function inject_wpforo_fields($fields, $type, $forum){
    global $wpdb;
    $fields_table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $query = "SELECT * FROM $fields_table_name";
    $customFields = $wpdb->get_results($query);
    for($i=0;$i<count($customFields);$i++){
        $field = $customFields[$i];
        $fields[$field->field_name] = [
            'fieldKey'       => $field->field_name,
            'type'           => $field->field_type,
            'isDefault'      => 0,
            'isRemovable'    => 1,
            'isRequired'     => (int)$field->field_required,
            'isEditable'     => 1,
            'label'          => wpforo_phrase( $field->field_label, false ),
            'title'          => wpforo_phrase( $field->field_label, false ),
            'placeholder'    => wpforo_phrase( $field->field_placeholder, false ),
            'minLength'      => 0,
            'maxLength'      => 0,
            'faIcon'         => $field->field_fa_icon,
            'name'           => $field->field_name,
            'cantBeInactive' => [ 'topic', 'post', 'comment', 'reply' ],
            'canEdit'        => [1,2,3,4,5],
            'canView'        => [1,2,3,4,5],
            'can'            => '',
            'isSearchable'   => 0,
            'value'          => $field->field_default_value,
            'isLabelFirst'   => 0,
        ];
        if($field->field_type === 'select' || $field->field_type === 'radio'){
            //Trim whitespaces before or after commas
            $field->field_options = preg_replace('/\s*,\s*/', ',', $field->field_options);
            $fields[$field->field_name]['values'] = explode(',', $field->field_options);
        }
        if($field->field_type === 'checkbox'){
            //Keep only the first value
            $field->field_options = preg_replace('/\s*,\s*/', ',', $field->field_options);
            $fields[$field->field_name]['values'] = explode(',', $field->field_options)[0];
        }

    }
    return $fields;
}
function inject_wpforo_forms($fields, $forum, $guest){
    global $wpdb;
    $forum_form_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    $form_fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    $fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    if(array_key_exists('forumid', $forum) == false){
        return $fields;
    }
    
    $formIdQuery = "SELECT form_id FROM $forum_form_table WHERE forum_id = {$forum['forumid']}";
    $formId = $wpdb->get_var($formIdQuery);
    //if there is no form for this forum, return the default fields
    if(!$formId){
        return $fields;
    }

    $formFieldsQuery = "SELECT field_id FROM $form_fields_table WHERE form_id = $formId";
    $formFields = $wpdb->get_results($formFieldsQuery);
    //if there are no fields for this form, return the default fields
    if(!$formFields){
        return $fields;
    }

    for($i=0;$i<count($formFields);$i++){
        $fieldId = $formFields[$i]->field_id;
        $fieldName = $wpdb->get_var("SELECT field_name FROM $fields_table WHERE id = $fieldId");
        $fields[0][0][2+$i] = $fieldName;
    }
    return $fields;
}
function add_custom_fields_to_post($postArgs, $forum){
    global $wpdb;
    //Get the post id
    $postId = WPF()->db->insert_id;
    $table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    //Get form id
    $query = "SELECT form_id FROM $table_name WHERE forum_id = {$forum['forumid']}";
    $formId = $wpdb->get_var($query);
    //Get custom field list
    $customFields = $wpdb->get_results("SELECT field_id FROM {$GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS']} WHERE form_id = $formId");
    
    //Insert the custom field into the database
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i]->field_id;
        $field = $wpdb->get_row("SELECT * FROM {$GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS']} WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $fieldValue = $postArgs[$fieldName];
        $result = $wpdb->insert("{$GLOBALS['CUSTOM_WPFORO_TABLES']['POSTS']}", array(
            'post_id' => $postId,
            'field_id' => $fieldId,
            'field_value' => $fieldValue
        ));
        echo $wpdb->last_error;
    }
}
function inject_post_field($postContent,$post){
    global $wpdb;
    $table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['POSTS'];
    $postId = $post['postid'];
    $customFields = $wpdb->get_results("SELECT field_id, field_value FROM $table_name WHERE post_id = $postId");
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i]->field_id;
        $fieldValue = $customFields[$i]->field_value;
        $field = $wpdb->get_row("SELECT * FROM {$GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS']} WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $postContent .= '<p><b>' . $fieldName . '</b>: ' . $fieldValue . '</p>';
    }
    return $postContent;
}
function clean_fields($content,$post){
    $x=0;
    return $content;
}
// Hooks/Filters
add_filter('wpforo_post_after_init_fields','inject_wpforo_fields',10,3);
add_filter('wpforo_get_topic_fields_structure','inject_wpforo_forms',10,3);
//add_filter('wpforo_content_after','clean_fields',200,2);
//add_filter('wpforo_content','inject_post_field',10,3);
//add_action('wpforo_after_add_topic','add_custom_fields_to_post',10,2);
?>