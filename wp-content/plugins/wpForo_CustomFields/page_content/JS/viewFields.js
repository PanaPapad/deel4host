/**
 * @param {HTMLAnchorElement} linkElem
 * @param {string} fieldName
 */
async function addWarningModal(linkElem, fieldName){
    //Before sending the request, we need to add a modal to the page
    //This modal will be used to display the warning message
    //Add the event listener to the link
    linkElem.addEventListener("click", function(event){
        event.preventDefault();
        //Set the message of the modal
        modalMessage.innerHTML = "Are you sure you want to delete the field " + fieldName + "?";
        //Clear event listeners
        const modalButton = document.getElementById("confirmBtn");
        modalButton.removeEventListener("click", null);
        modalButton.addEventListener("click", function(){
            window.location = linkElem.href;
        });
        bootstrapModal.show();
    });
    linkElem.removeAttribute("class");
}
async function addWarningModals(){
    //From each row the last column is the delete <a> tag
    const table = document.getElementById("fieldsTable");
    const rows = table.getElementsByTagName("tr");
    for(let i = 1; i < rows.length; i++){
        const row = rows[i];
        const linkElem = row.getElementsByTagName("td")[row.getElementsByTagName("td").length - 1].getElementsByTagName("a")[0];
        if(linkElem === undefined) continue;
        addWarningModal(linkElem, row.getElementsByTagName("td")[0].innerHTML);
    }

}
/**
* @type {HTMLDivElement}
*/
const modal = document.getElementById("staticBackdrop");
/**
* @type {bootstrap.Modal}
*/
const bootstrapModal = new bootstrap.Modal(modal);
/**
* @type {HTMLDivElement}
*/
const modalMessage = document.getElementById("modalMessage");
addWarningModals();