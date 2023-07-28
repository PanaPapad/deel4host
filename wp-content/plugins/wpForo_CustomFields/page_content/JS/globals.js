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
function showToast(type, title, msg) {
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
createToast();