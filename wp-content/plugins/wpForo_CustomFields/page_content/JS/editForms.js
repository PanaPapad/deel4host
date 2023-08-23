/**
 * Add the selected field to the table
 */
function addSelectedField(){
    /**
     * @type {HTMLSelectElement}
     */
    const selectElem = document.getElementById("fieldSelect");
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const fieldId = selectedOption.value;
    if(fieldId == "-1"){
        return;
    }
    const selectedText = selectedOption.text;
    //Remove the option
    selectElem.remove(selectedOption.index);
    const fieldName = selectedText.split(" : ")[0];
    const fieldType = selectedText.split(" : ")[1];
    addField(fieldId, fieldName, fieldType);
}
/**
 * Add a field from the select element using the field id.
 * This function is used when loading a form to edit.
 * The fields that are already in the form are added to the table.
 * @param {number} fieldId ID of the field to add
 */
function addFieldFromId(fieldId){
    //Get the option element with the fieldId
    /**
     * @type {HTMLSelectElement}
     * */
    const selectElem = document.getElementById("fieldSelect");
    const options = selectElem.options;
    let selectedOption = null;
    for(let i = 0; i < options.length; i++){
        const option = options[i];
        if(option.value == fieldId){
            selectedOption = option;
            break;
        }
    }
    if(selectedOption == null){
        return;
    }
    const selectedText = selectedOption.text;
    //Remove the option
    selectElem.remove(selectedOption.index);
    const fieldName = selectedText.split(" : ")[0];
    const fieldType = selectedText.split(" : ")[1];
    addField(fieldId, fieldName, fieldType);
}
/**
 * Add a field to the table
 * @param {string} id ID of the field
 * @param {string} name Name of the field
 * @param {string} type Type of the field
 */
function addField(id,name, type){
//Add the field to the table
    /**
     * @type {HTMLTableSectionElement}
     */
    const tableBody = document.getElementById("fieldsTableBody");
    const row = tableBody.insertRow(-1);
    const id_cell = row.insertCell(0);
    const name_cell = row.insertCell(1);
    const type_cell = row.insertCell(2);
    const delBtn_cell = row.insertCell(3);
    id_cell.innerHTML = id;
    name_cell.innerHTML = name;
    type_cell.innerHTML = type;
    delBtn_cell.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteField(this)'>Delete</button>";
}
/**
 * Delete a field from the table and add it back to the select element
 * @param {HTMLButtonElement} btn  The delete button that was clicked
 */
function deleteField(btn){
    //Add the option back
    /**
     * @type {HTMLTableRowElement}
     * */
    const row = btn.parentNode.parentNode;
    /**
     * @type {HTMLSelectElement}
     * */
    const selectElem = document.getElementById("fieldSelect");
    const newOption = document.createElement("option");
    newOption.value = row.cells[0].innerHTML;
    newOption.text = row.cells[1].innerHTML;
    selectElem.add(newOption);
    //Delete the row
    row.parentNode.removeChild(row);
}
/**
 * Initialize the Page
 */
async function initPage(){
    presentLoadingScreen();
    //Get all fields
    try{
        await getAllFields();
    }
    catch(err){
        showToast(0, "Error", "Failed to get fields");
        return
    }
    //Check if there is a query parameter edit_form
    const urlParams = new URLSearchParams(window.location.search);
    const formId = urlParams.get("edit_form");
    if(formId == null){
        dismissLoadingScreen();
        return;
    }
    //Get the form data
    getFormData(formId);
}
/**
 * Get the data for the form that is being edited.
 * @param {number} formId ID of the form to edit
 */
async function getFormData(formId){
    const dataRequest = new XMLHttpRequest();
    dataRequest.open("GET", WPF_CUSTOM_API.baseUrl + "/form?form_id=" + formId, true);
    dataRequest.setRequestHeader("Content-Type", "application/json");
    dataRequest.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    dataRequest.onload = function () {
        if (dataRequest.status !== 200) {
            showToast(0, "Error", dataRequest.responseText);
            return null;
        }
        const data = JSON.parse(dataRequest.responseText);
        /** @type {HTMLInputElement} */
        const formNameInput = document.getElementById("form_name");
        formNameInput.value = data['form_name'];
        const formFields = data['form_fields'];
        for(let i = 0; i < formFields.length; i++){
            const fieldId = formFields[i]['id'];
            addFieldFromId(fieldId);
        }
        dismissLoadingScreen();
    }
    dataRequest.onerror = function () {
        showToast(0, "Error", dataRequest.responseText);
        return null;
    }
    dataRequest.send();
}
/**
 * Save the form
 * If the form is new, send a POST request
 * If the form is being edited, send a PUT request
*/
async function saveForm(){
    const urlParams = new URLSearchParams(window.location.search);
    const formId = urlParams.get("edit_form");
    if(formId === null){
        saveNewForm();
    }
    else{
        replaceForm(formId);
    }
}
/**
 * Save a new form using a POST request
 */
async function saveNewForm(){
    const formName = document.getElementById("form_name").value;
    const formFields = getFormFields();
    const data = {
        "form_name": formName,
        "form_fields": formFields
    }
    const saveReq = new XMLHttpRequest();
    saveReq.open("POST", WPF_CUSTOM_API.baseUrl + "/form", true);
    saveReq.setRequestHeader("Content-Type", "application/json");
    saveReq.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    saveReq.onload = function () {
        if (saveReq.status !== 200) {
            showToast(0, "Error", saveReq.responseText);
            return;
        }
        showToast(1, "Success", saveReq.responseText);
    }
    saveReq.onerror = function () {
        showToast(0, "Error", saveReq.responseText);
    }
    saveReq.send(JSON.stringify(data));
}
/**
 * Replace a form using a PUT request. This is used when editing a form.
 * @param {number} formId The id of the form to replace
 */
async function replaceForm(formId){
    const formName = document.getElementById("form_name").value;
    const formFields = getFormFields();
    const data = {
        "form_id": formId,
        "form_name": formName,
        "form_fields": formFields
    };
    const saveReq = new XMLHttpRequest();
    saveReq.open("PUT", WPF_CUSTOM_API.baseUrl + "/form", true);
    saveReq.setRequestHeader("Content-Type", "application/json");
    saveReq.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    saveReq.onload = function () {
        if (saveReq.status !== 200) {
            showToast(0, "Error", saveReq.responseText);
            return;
        }
        showToast(1, "Success", saveReq.responseText);
    }
    saveReq.onerror = function () {
        showToast(0, "Error", saveReq.responseText);
    }
    saveReq.send(JSON.stringify(data));
}
/**
 * Get the ids of the fields in the table.
 * This is used when saving a form.
 * @returns {Array<number>} An array of field ids
 */
function getFormFields(){
    const tableBody = document.getElementById("fieldsTableBody");
    const rows = tableBody.rows;
    const fields = [];
    for(let i = 0; i < rows.length; i++){
        const row = rows[i];
        const fieldId = row.cells[0].innerHTML;
        fields.push(fieldId);
    }
    return fields;
}
/**
 * Get all fields from the database and add them to the select element.
 * @returns {Promise} A promise that resolves when all fields are loaded
 */
function getAllFields() {
    return new Promise((resolve, reject) => {
        const dataRequest = new XMLHttpRequest();
        dataRequest.open("GET", WPF_CUSTOM_API.baseUrl + "/field", true);
        dataRequest.setRequestHeader("Content-Type", "application/json");
        dataRequest.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);

        dataRequest.onload = function () {
            if (dataRequest.status !== 200) {
                showToast(0, "Error", dataRequest.responseText);
                reject(dataRequest.responseText);  // Reject the promise
                return;
            }
            const data = JSON.parse(dataRequest.responseText);
            const selectElem = document.getElementById("fieldSelect");
            for (let i = 0; i < data.length; i++) {
                const field = data[i];
                const newOption = document.createElement("option");
                newOption.value = field['id'];
                newOption.text = field['field_name'] + " : " + field['field_type'];
                selectElem.add(newOption);
            }
            resolve();  // Resolve the promise
        }

        dataRequest.onerror = function () {
            showToast(0, "Error", dataRequest.responseText);
            reject(dataRequest.responseText);  // Reject the promise
        }

        dataRequest.send();
    });
}
initPage();
/** @type {HTMLButtonElement} */
const addFieldBtn = document.getElementById("addFieldBtn");
addFieldBtn.addEventListener("click", addSelectedField);
/** @type {HTMLButtonElement} */
const saveFormBtn = document.getElementById("saveFormBtn");
saveFormBtn.addEventListener("click", saveForm);
