function check_Password() {
    const password_input = document.querySelector('.Password'); 
    const password_error_msg = document.querySelector('#error-Password');
    if (password_input.value.length < 6) {
        password_error_msg.classList.remove('titanic'); 
        return 1; 
    }
    else {
        password_error_msg.classList.add('titanic'); 
        return 0; 
    }
}

const form = document.querySelector('form'); 

form.addEventListener("submit", function(event) {
    event.preventDefault(); // Bloque l'envoi classique
    
    let nb_errors = 0;
    nb_errors += check_Password();
    console.log("nb_errors : ", nb_errors);

    if (nb_errors == 0) {
        window.location.href = "index.html";
    }
});