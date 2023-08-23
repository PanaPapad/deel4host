/**
 * Initialize the page
 */
async function initPage(){
    //Check if field id exists in query string
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const fieldId = urlParams.get('field_id');
    if (fieldId === null) {
        return;
    }
    presentLoadingScreen();
    //Get field values
    getFieldValues(fieldId);
}
/**
 * Fetch the field values from the database
 * @param {number} fieldId 
 */
async function getFieldValues(fieldId) {
    const fieldDataRequest = new XMLHttpRequest();
    fieldDataRequest.open("GET", WPF_CUSTOM_API.baseUrl + `/field?field_id=${fieldId}`, true);
    fieldDataRequest.setRequestHeader("Content-Type", "application/json");
    fieldDataRequest.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    fieldDataRequest.onload = function () {
        //Any code other than 200 is an error
        if (this.status !== 200) {
            showToast(0, "Error", "Could not fetch field data.");
            return;
        }
        const fieldData = JSON.parse(this.response);
        setFieldValues(fieldData);
    }
    fieldDataRequest.onerror = function () {
        showToast(0, "Error", "Could not fetch field data.");
    }
    fieldDataRequest.send();
}
/**
 * Set the input values of the form
 * @param {object} fieldValues 
 */
async function setFieldValues(fieldValues) {
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
    updateForm();
    dismissLoadingScreen();
}
/**
 * Update the form based on the field type
 */
function updateForm() {
    //Get field type
    const fieldType = fieldTypeSelect.value;
    //Get field options
    const fieldOptions = document.getElementById("field_options");
    const fieldDefault = document.getElementById("field_default");
    const fieldPlaceholder = document.getElementById("field_placeholder");
    const fieldRequired = document.getElementById("field_required");
    //Check if field type is select
    if (fieldType === "select" || fieldType === "radio" || fieldType === "checkbox") {
        fieldOptions.disabled = false;
        fieldOptions.required = true;
    }
    else {
        fieldOptions.disabled = true;
        fieldOptions.required = false;
    }
    //Check if field type is checkbox
    if (fieldType === "checkbox") {
        fieldPlaceholder.disabled = true;
        fieldRequired.disabled = true;
    }
    else {
        fieldPlaceholder.disabled = false;
        fieldRequired.disabled = false;
    }
}
/**
 * Save the field to the Database.
 * If this is an edit the field is updated.
 */
function saveField(){
    //Get field id
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const fieldId = urlParams.get('field_id');
    //Get field values
    const fieldValues = getInputValues();
    //Check if field id exists
    if (fieldId === null) {
        //Create new field
        saveNewField(fieldValues);
    }
    else {
        //Update field
        updateField(fieldId, fieldValues);
    }
}
/**
 * 
 * @param {object} fieldValues 
 */
function saveNewField(fieldValues){
    //Send request
    const fieldDataRequest = new XMLHttpRequest();
    fieldDataRequest.open("POST", WPF_CUSTOM_API.baseUrl + "/field", true);
    fieldDataRequest.setRequestHeader("Content-Type", "application/json");
    fieldDataRequest.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    fieldDataRequest.onload = function () {
        //Any code other than 200 is an error
        if (this.status !== 200) {
            showToast(0, "Error", "Could not create field.");
            return;
        }
        showToast(1, "Success", "Field created successfully.");
    }
    fieldDataRequest.onerror = function () {
        showToast(0, "Error", "Creation request failed.");
    }
    fieldDataRequest.send(JSON.stringify(fieldValues));
}
/**
 * 
 * @param {number}} fieldId 
 * @param {object} fieldValues 
 */
function updateField(fieldId, fieldValues){
    //Send request
    const fieldDataRequest = new XMLHttpRequest();
    fieldDataRequest.open("PUT", WPF_CUSTOM_API.baseUrl + `/field?field_id=${fieldId}`, true);
    fieldDataRequest.setRequestHeader("Content-Type", "application/json");
    fieldDataRequest.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    fieldDataRequest.onload = function () {
        //Any code other than 200 is an error
        if (this.status !== 200) {
            showToast(0, "Error", "Could not update field.");
            return;
        }
        showToast(1, "Success", "Field updated successfully.");
    }
    fieldDataRequest.onerror = function(){
        showToast(0, "Error", "Update request failed.");
    }
    //Append field id to field values
    fieldValues.field_id = fieldId;
    fieldDataRequest.send(JSON.stringify(fieldValues));
}
function getInputValues(){
    //Get field values
    const fieldValues = {
        field_name: document.getElementById("field_name").value,
        field_label: document.getElementById("field_label").value,
        field_type: document.getElementById("field_type").value,
        field_options: document.getElementById("field_options").value,
        field_placeholder: document.getElementById("field_placeholder").value,
        field_required: document.getElementById("field_required").checked,
        field_fa_icon : document.getElementById("field_fa_icon").value,
    }
    return fieldValues;
}
initPage();
const submitBtn = document.getElementById("submitBtn");
const fieldTypeSelect = document.getElementById("field_type");
submitBtn.addEventListener("click", saveField);
fieldTypeSelect.addEventListener("change", updateForm);

