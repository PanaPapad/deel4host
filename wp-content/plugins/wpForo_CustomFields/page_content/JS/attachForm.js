/**
 * 
 * @param {HTMLButtonElement} btnElem 
 */
function enableEdit(btnElem) {
  //Get the row 
  /** @type {HTMLTableRowElement} */
  const row = btnElem.parentElement.parentElement;
  //Change the cell to contanin a select elem with all options
  //The select option should be the same as the current value
  /** @type {HTMLTableCellElement} */
  const formCellValue = row.querySelector('td:nth-child(2)').innerHTML;
  const selectElem = document.createElement('select');
  //add None option
  const noneOption = document.createElement('option');
  noneOption.value = 'None';
  noneOption.innerHTML = 'None';
  noneOption.selected = true; //Default
  selectElem.appendChild(noneOption);
  for (let i = 0; i < forms.length; i++) {
    const optionElem = document.createElement('option');
    optionElem.value = forms[i].id;
    optionElem.innerHTML = forms[i].form_name;
    if (forms[i] === formCellValue) {
      optionElem.selected = true;
    }
    selectElem.appendChild(optionElem);
  }
  row.querySelector('td:nth-child(2)').innerHTML = '';
  row.querySelector('td:nth-child(2)').appendChild(selectElem);
  //CHange the button onclick to save
  btnElem.innerHTML = 'Save';
  btnElem.onclick = function () {
    disableEdit(btnElem);
  }
}
/**
 *  @param {HTMLButtonElement} btnElem
 */
function disableEdit(btnElem) {
  /** @type {HTMLTableRowElement} */
  const row = btnElem.parentElement.parentElement;
  /** @type {HTMLTableCellElement} */
  const cell = row.querySelector('td:nth-child(2)');
  const formCellValue = cell.querySelector('select').value;
  const formCellText = cell.querySelector('select').selectedOptions[0].innerHTML;

  cell.innerHTML = formCellText;
  cell.setAttribute('data-id', formCellValue);

  btnElem.innerHTML = 'Edit';
  btnElem.onclick = function () {
    enableEdit(btnElem);
  }
}
/**
 * @param {HTMLFormElement} form 
 */
function submitForm(form) {
  const formData = new FormData(form);

  /** @type {HTMLTableBodyElement} */
  const relationsTable = document.getElementById('relationsTable').getElementsByTagName('tbody')[0];
  /**
   * @type {Array<{forumId: string, formId: string}>}
   */
  const relations = [];
  for (let i = 0; i < relationsTable.rows.length; i++) {
    const row = relationsTable.rows[i];
    const forumCell = row.querySelector('td:nth-child(1)');
    const formCell = row.querySelector('td:nth-child(2)');
    const forumId = forumCell.dataset.id;
    const formId = formCell.dataset.id;
    if (forumId && formId) {
      relations.push({
        forumId: forumId,
        formId: formId
      });
    }
  }
  formData.append("forum_form_relations", JSON.stringify(relations));
  formData.append("Save Changes", "Save Changes");
  //Submit the form with the added data synchronously
  const xhr = new XMLHttpRequest();
  xhr.open("POST", form.action, false);
  xhr.onload = function () {
    if (xhr.status === 200) {
      // Request was successful
      if (xhr.responseURL && xhr.responseURL === form.action) {
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

//Prevent default form submission
const form = document.getElementById("submitForm");
form.addEventListener("submit", function (event) {
  event.preventDefault();
  submitForm(form);
});