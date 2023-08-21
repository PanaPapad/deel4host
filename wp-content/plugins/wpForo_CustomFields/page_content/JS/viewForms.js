function createStickyTable() {
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
function getFormFields(formId) {
    const getReq = new XMLHttpRequest();
    const url = WPF_CUSTOM_API.baseUrl + "/form_fields?form_id=" + formId;
    getReq.open("GET", url, true);
    getReq.setRequestHeader("Content-Type", "application/json");
    getReq.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    getReq.onload = function () {
        if (getReq.status !== 200) {
            showToast(0, "Error", getReq.responseText);
            return;
        }
        const fields = JSON.parse(getReq.responseText);
        /**
         * @type {HTMLTableElement}
         */
        const fieldsTbody = document.getElementById("fieldsTableBody");
        fieldsTbody.innerHTML = "";
        for (let i = 0; i < fields.length; i++) {
            const row = fieldsTbody.insertRow(i);
            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            cell1.innerHTML = fields[i]['field_name'];
            cell2.innerHTML = fields[i]['field_type'];
        }
    }
    getReq.send();
}
/**
 * @param {number} formId 
 */
function editForm(formId) {
    window.location.href = "admin.php?page=custom-wpforo-forms-edit&edit_form=" + formId;
}
function deleteForm(formId) {
    const deleteReq = new XMLHttpRequest();
    const url = WPF_CUSTOM_API.baseUrl + "/delete_form";
    deleteReq.open("DELETE", url, true);
    deleteReq.setRequestHeader("Content-Type", "application/json");
    deleteReq.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
    deleteReq.onload = function () {
        if (deleteReq.status !== 200) {
            showToast(0, "Error", deleteReq.responseText);
            return;
        }
        showToast(1, "Success", deleteReq.responseText);
    }
    deleteReq.onerror = function () {
        showToast(0, "Error", deleteReq.responseText);
    }
    deleteReq.send(JSON.stringify([{ form_id: formId }]));
}
createStickyTable();


