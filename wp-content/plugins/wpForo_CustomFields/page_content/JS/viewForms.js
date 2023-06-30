function createStickyTable(){
    const stickyTable = document.createElement("table");
    const header = stickyTable.createTHead();
    const tbody = stickyTable.createTBody();
    tbody.id = "fieldsTableBody";
    const row = header.insertRow(0);
    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    cell1.innerHTML = "<b>Field Name</b>";
    cell2.innerHTML = "<b>Field Type</b>";

    stickyTable.id = "fieldsTable";
    stickyTable.className = "table table-striped table-hover table-responsive";
    document.getElementById('wpbody').appendChild(stickyTable);
}
/**
 * @param {number} formId 
 */
function getFormFields(formId){
    const getReq = new XMLHttpRequest();
    const url = baseApiUrl + "get-form-fields.php?form_id=" + formId;
    getReq.open("GET",url,true);
    getReq.onload = function(){
        if(getReq.status !== 200){
            showToast(0,"Error",getReq.responseText);
            return;
        }
        const fields = JSON.parse(getReq.responseText);
        /**
         * @type {HTMLTableElement}
         */
        const fieldsTbody = document.getElementById("fieldsTableBody");
        fieldsTbody.innerHTML = "";
        for(let i = 0; i < fields.length; i++){
            const row = fieldsTbody.insertRow(i);
            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            cell1.innerHTML = fields[i].field_name;
            cell2.innerHTML = fields[i].field_type;
        }
    }
    getReq.send();
}
/**
 * @param {number} formId 
 */
function editForm(formId){
    window.location.href = "admin.php?page=custom-wpforo-forms-edit&edit_form=" + formId;
}
function deleteForm(formId){
    const delReq = new XMLHttpRequest();
    const url = baseApiUrl + "delete-form.php";
    delReq.open("POST",url,true);
    delReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    delReq.onload = function(){
        if(delReq.status !== 200){
            showToast(0,"Error",delReq.responseText);
            return;
        }
        showToast(1,"Success",delReq.responseText);
    }
    delReq.send("form_id=" + formId);
}
createStickyTable();