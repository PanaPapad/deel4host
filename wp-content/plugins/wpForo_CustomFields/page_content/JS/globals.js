/**
 * Create a toast container and append it to the body.
 */
function createToast(){
    const toastCont = document.createElement('div');
    toastCont.id = "toastCont";
    toastCont.classList.add("toast-container", "position-fixed", "top-0", "start-50", "translate-middle-x", "mt-3");
    toastCont.style.zIndex = "999999";
    const toast = document.createElement('div');
    toast.id = "toast";
    toast.classList.add("toast");
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");
    toast.setAttribute("data-bs-autohide", "true");
    toast.setAttribute("data-bs-delay", "3000");
    const toastHeader = document.createElement('div');
    toastHeader.classList.add("toast-header");
    const toastTitle = document.createElement('strong');
    toastTitle.classList.add("me-auto");
    toastTitle.id = "toastTitle";
    const toastCloseBtn = document.createElement('button');
    toastCloseBtn.classList.add("btn-close");
    toastCloseBtn.setAttribute("data-bs-dismiss", "toast");
    toastCloseBtn.setAttribute("aria-label", "Close");
    const toastBody = document.createElement('div');
    toastBody.classList.add("toast-body");
    toastBody.id = "toastBody";

    toastHeader.appendChild(toastTitle);
    toastHeader.appendChild(toastCloseBtn);
    toast.appendChild(toastHeader);
    toast.appendChild(toastBody);
    toastCont.appendChild(toast);
    document.body.appendChild(toastCont);
}
/**
 * Present a toast message. 
 * @param {number} type 1 for success, 0 for error.
 * @param {string} title Title of the toast.
 * @param {string} msg Message of the toast.
 */
async function showToast(type, title, msg) {
    var toast = new bootstrap.Toast(document.getElementById('toast'));
    var toastTitle = document.getElementById('toastTitle');
    var toastBody = document.getElementById('toastBody');

    if (type === 1) {
        toastTitle.textContent = title;
        toastBody.textContent = msg;
        toastBody.classList.remove('text-danger');
        toastBody.classList.add('text-success');
    } else if (type === 0) {
        toastTitle.textContent = title;
        toastBody.textContent = msg;
        toastBody.classList.remove('text-success');
        toastBody.classList.add('text-danger');
    }

    toast.show();
}
/**
 * Create a modal container and append it to the body.
 */
function createModal(){
    const modal = document.createElement('div');
    modal.id = "staticModal";
    modal.classList.add("modal", "fade");
    modal.setAttribute("data-bs-backdrop", "static");
    modal.setAttribute("data-bs-keyboard", "false");
    modal.setAttribute("tabindex", "-1");
    modal.setAttribute("aria-labelledby", "staticBackdropLabel");
    modal.setAttribute("aria-hidden", "true");

    const modalDialog = document.createElement('div');
    modalDialog.classList.add("modal-dialog");

    const modalContent = document.createElement('div');
    modalContent.classList.add("modal-content");

    const modalHeader = document.createElement('div');
    modalHeader.classList.add("modal-header");

    const modalTitle = document.createElement('h1');
    modalTitle.classList.add("modal-title", "fs-5");
    modalTitle.id = "modalTitle";

    const modalCloseBtn = document.createElement('button');
    modalCloseBtn.classList.add("btn-close");
    modalCloseBtn.setAttribute("data-bs-dismiss", "modal");
    modalCloseBtn.setAttribute("aria-label", "Close");

    const modalBody = document.createElement('div');
    modalBody.classList.add("modal-body");
    modalBody.id = "modalBody";

    const modalFooter = document.createElement('div');
    modalFooter.classList.add("modal-footer");

    const modalCloseBtn2 = document.createElement('button');
    modalCloseBtn2.classList.add("btn", "btn-secondary");
    modalCloseBtn2.setAttribute("data-bs-dismiss", "modal");
    modalCloseBtn2.textContent = "Close";

    const modalConfirmBtn = document.createElement('button');
    modalConfirmBtn.id = "modalConfirmBtn";
    modalConfirmBtn.classList.add("btn", "btn-danger");
    modalConfirmBtn.setAttribute("data-bs-dismiss", "modal");
    modalConfirmBtn.textContent = "Understood";

    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(modalCloseBtn);
    modalFooter.appendChild(modalCloseBtn2);
    modalFooter.appendChild(modalConfirmBtn);
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);
    modalDialog.appendChild(modalContent);
    modal.appendChild(modalDialog);
    document.body.appendChild(modal);
}
/**
 * Show a modal.
 * @param {string} title Title of the modal.
 * @param {string} msg Message of the modal.
 * @param {function} confirmCallback Callback function to be called when the confirm button is clicked.
 * @param  {...any} args Arguments to be passed to the callback function.
 */
async function showModal(title, msg, confirmBtnText, confirmCallback, ...args) {
    const modal = new bootstrap.Modal(document.getElementById('staticModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');

    modalTitle.textContent = title;
    modalBody.textContent = msg;
    modalConfirmBtn.textContent = confirmBtnText;
    modalConfirmBtn.addEventListener("click", function(){
        confirmCallback(...args);
    });
    modal.show();
}
/**
 * Delete a row from a table. 
 * The row is deleted from the DOM with a fade out animation.
 * @param {HTMLTableRowElement} rowElement
 * */
async function deleteRow(rowElement){
    rowElement.addEventListener("transitionend", function(){
        rowElement.remove();
    });
    rowElement.classList.add("fadeOut");
}
function presentLoadingScreen(){
    const loadingScreen = document.createElement('div');
    loadingScreen.id = "loadingScreen";
    loadingScreen.classList.add("loadingScreen");
    loadingScreen.innerHTML = "<div class='spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div>";
    document.body.appendChild(loadingScreen);
}
function dismissLoadingScreen(){
    const loadingScreen = document.getElementById("loadingScreen");
    if(!loadingScreen){
        return;
    }
    loadingScreen.remove();
}
createToast();
createModal();