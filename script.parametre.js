function checkName() {
    const nameInput = document.querySelector('.Nom');
    const nameErrorMsg = document.querySelector('#error-Nom');
    if (nameInput.value.length < 3 || !nameInput.value.includes(' ')) {
        nameErrorMsg.classList.remove('titanic');
        return 1;
    } else {
        nameErrorMsg.classList.add('titanic');
        return 0;
    }
}

function checkRole() {
    const roleInput = document.querySelector('.Role');
    const roleErrorMsg = document.querySelector('#error-Role');
    if (roleInput.value.length < 3) {
        roleErrorMsg.classList.remove('titanic');
        return 1;
    } else {
        roleErrorMsg.classList.add('titanic');
        return 0;
    }
}

function checkEmail() {
    const emailInput = document.querySelector('.Email');
    const emailErrorMsg = document.querySelector('#error-Email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value)) {
        emailErrorMsg.classList.remove('titanic');
        return 1;
    } else {
        emailErrorMsg.classList.add('titanic');
        return 0;
    }
}

const form = document.querySelector('form');
form.addEventListener("submit", function(event) {
    event.preventDefault();
    let nbErrors = 0;
    nbErrors += checkName();
    nbErrors += checkRole();
    nbErrors += checkEmail();
    console.log("Nombre d'erreurs : ", nbErrors);
    if (nbErrors === 0) {
        const toast = document.querySelector("#success");
        toast.classList.remove('titanic');
        setTimeout(() => {toast.classList.add('titanic');}, 2000);
    }   
});