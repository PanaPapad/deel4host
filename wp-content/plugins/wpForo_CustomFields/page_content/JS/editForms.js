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
 * @param {string} id
 * @param {string} name
 * @param {string} type
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
 * @param {HTMLButtonElement} btn 
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
 * @param {HTMLFormElement} form 
 */
function submitForm(form){
    const formData = new FormData(form);
    //Add the fields to the form data
    /**
     * @type {HTMLTableElement}
     */
    const table = document.getElementById("fieldsTable");
    const rows = table.rows;
    /**
     * @type {string[]}
     */
    const fieldNames = [];
    for(let i = 1; i < rows.length; i++){
        const row = rows[i];
        const name = row.cells[0].innerHTML;
        fieldNames.push(name);
    }
    formData.append("field_ids", fieldNames);
    formData.append("Add_Custom_WpForo_Form","Create/Save Custom Form");
    //Submit the form with the added data synchronously
    const xhr = new XMLHttpRequest();
    xhr.open("POST", form.action, false);
    xhr.onload = function () {
        if (xhr.status === 200) {
          // Request was successful
          if (xhr.responseURL && xhr.responseURL !== form.action) {
            // Redirect occurred, handle the redirect URL
            console.log("Redirect URL:", xhr.responseURL);
            // You can redirect the user using JavaScript if needed
            window.location.href = xhr.responseURL;
          } else {
            // No redirect occurred, do something else
            console.log("Form submitted successfully");
          }
        } else {
          // Request failed, handle the error
          console.error("Form submission failed with status", xhr.status);
        }
      };
    xhr.send(formData);
}
/**
 * @param {object|string} data 
 */
function setData(data){
    if (typeof data === "string") {
        data = JSON.parse(data);
    }
    const formName = data.form_name;
    const formFields = data.form_fields;
    
    //Set the form name
    /**
     * @type {HTMLInputElement}
     * */
    const formNameInput = document.getElementById("form_name");
    formNameInput.value = formName;
    for(let i = 0; i < formFields.length; i++){
        const field = formFields[i];
        const id = field.ID;
        const name = field.Name;
        const type = field.Type;
        addField(id,name, type);
    }
}
function presentMessages(){
    //Check if toast container exists
    if (document.getElementById('toastCont') === null) {
        createToast();
    }
    //Check if query string contains messages
    //Possible query string args:
    /**
     * 'custom_form_saved',
     * 'custom_form_deleted',
    */
    // 0 is fail, 1 is success
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const form_saved = urlParams.get('custom_form_saved');
    const form_deleted = urlParams.get('custom_form_deleted');
    if(form_saved == 1){
        showToast(1,"Success","Form saved successfully");
    }
    else if(form_saved == 0){
        showToast(0,"Error","Form could not be saved");
    }
    else if(form_deleted == 1){
        showToast(1,"Success","Form deleted successfully")
    }
    else if(form_deleted == 0){
        showToast(0,"Error","Form could not be deleted")
    }
}
// Execute on page load
presentMessages();
/**
 * @type {HTMLButtonElement}
 */
const addFieldBtn = document.getElementById("addFieldBtn");
addFieldBtn.addEventListener("click", addSelectedField);
/**
 * @type {HTMLFormElement}
 */
const form = document.getElementById("creationForm");
form.addEventListener("submit", function(event){
    event.preventDefault();
    submitForm(form);
});