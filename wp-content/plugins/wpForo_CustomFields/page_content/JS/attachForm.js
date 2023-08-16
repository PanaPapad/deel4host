/**
 * Save the changes made.
 */
function saveChanges() {
  /**@type {HTMLTableSectionElement} */
  const tableBody = document.getElementById('relationsTableBody');
  const selectElems = tableBody.getElementsByTagName('select');
  const data = [];
  for (let i = 0; i < selectElems.length; i++) {
    const selectValue = selectElems[i].value;
    const currentValue = selectElems[i].dataset.current;
    if (selectValue !== currentValue) {
      data.push({
        forum_id: selectElems[i].dataset.id,
        form_id: selectValue
      });
    }
  }
  //Submit the data
  const request = new XMLHttpRequest();
  const url = WPF_CUSTOM_API.baseUrl + "/attach_form";
  request.open('POST', url, true);
  request.setRequestHeader("Content-Type", "application/json");
  request.setRequestHeader("X-WP-Nonce", WPF_CUSTOM_API.nonce);
  request.onload = function () {
    if (request.status >= 200 && request.status < 300) {
      // Success!
      showToast(1, "Success", "Data saved successfully");
      updateData();
    }
    else{
      // We reached our target server, but it returned an error
      showToast(0, "Error", "An error occurred while saving the data");
    }
  };
  request.onerror = function () {
    // There was a connection error of some sort
    showToast(0, "Error", "Request failed");
  }
  request.send(JSON.stringify(data));
}
/**
 * Check if the user has made any changes that need saving.
 * @returns {boolean} true if changes have been made, false otherwise.
 */
function checkForEdits() {
  //Get all the select elems in the table body
  const tableBody = document.getElementById('relationsTableBody');
  const selectElems = tableBody.getElementsByTagName('select');
  for (let i = 0; i < selectElems.length; i++) {
    const selectValue = selectElems[i].value;
    const currentValue = selectElems[i].dataset.current;
    if (selectValue !== currentValue) {
      return true;
    }
  }
  return false;
}
/**
 * Update the data-current attribute of the select elems to match their current value.
 */
function updateData(){
  //Get all the select elems in the table body
  const tableBody = document.getElementById('relationsTableBody');
  const selectElems = tableBody.getElementsByTagName('select');
  for (let i = 0; i < selectElems.length; i++) {
    const selectValue = selectElems[i].value;
    selectElems[i].dataset.current = selectValue;
  }
}

//Warn the user if they try to leave the page without saving
window.addEventListener('beforeunload',function(e){
  if(checkForEdits()){
    const warningText = "You have unsaved changes! Are you sure you want to leave?";
    e.returnValue = warningText;
    return warningText;
  }
});