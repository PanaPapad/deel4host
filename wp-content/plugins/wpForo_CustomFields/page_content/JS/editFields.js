function setFieldValues(fieldValues) {
    //convert to json if needed
    if (typeof fieldValues === "string") {
        fieldValues = JSON.parse(fieldValues);
    }
    for (var field in fieldValues) {
        let value = fieldValues[field];
        if (value === null) {
            value = "";
        }
        //Get html element
        const element = document.getElementById(field);
        if (element === null) {
            continue;
        }
        //Check if field is a checkbox
        if (element.type === "checkbox") {
            if (value === "1") {
                element.checked = true;
            }
            else {
                element.checked = false;
            }
        }
        //Check if field is select
        else if (element.tagName === "SELECT") {
            //Get options
            const options = element.options;
            //Loop through options
            for (var i = 0; i < options.length; i++) {
                //Check if option value matches field value
                if (options[i].value === value) {
                    //Set selected option
                    element.selectedIndex = i;
                    break;
                }
            }
        }
        else {
            element.value = value;
        }
    }
}
function presentMessages() {
    //Check if toast container exists
    if (document.getElementById('toastCont') === null) {
        createToast();
    }
    //Check if query string contains messages
    //Possible query string args:
    /**
     * 'custom_field_saved',
     * 'custom_field_deleted',
     */
    // 0 is fail, 1 is success
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const customFieldSaved = urlParams.get('custom_field_saved');
    const customFieldDeleted = urlParams.get('custom_field_deleted');
    if (customFieldSaved === "1") {
        showToast(1, "Success", "Custom field saved successfully.");
    }
    else if (customFieldSaved === "0") {
        showToast(0, "Error", "Custom field could not be saved.");
    }
    else if (customFieldDeleted === "1") {
        showToast(1, "Success", "Custom field deleted successfully.");
    }
    else if (customFieldDeleted === "0") {
        showToast(0, "Error", "Custom field could not be deleted.");
    }
}
function updateForm() {
    //Get field type
    const fieldType = fieldTypeSelect.value;
    //Get field options
    const fieldOptions = document.getElementById("field_options");
    const fieldDefault = document.getElementById("field_default");
    //Check if field type is select
    if (fieldType === "select" || fieldType === "radio") {
        fieldOptions.disabled = false;
    }
    else {
        fieldOptions.disabled = true;
    }
    //Check if field type is checkbox
    if (fieldType === "checkbox") {
        fieldDefault.disabled = true;
    }
    else {
        fieldDefault.disabled = false;
    }
}
const submitBtn = document.getElementById("submitBtn");
const fieldTypeSelect = document.getElementById("field_type");
//submitBtn.addEventListener("click", submitForm);
fieldTypeSelect.addEventListener("change", updateForm);
presentMessages();

