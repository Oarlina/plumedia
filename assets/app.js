import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


// ceci est un timer
setTimeout(() => {
    // je recupere la classe alert, j'en fais un tableau et mais une transition ou au bout de 5 secondes le message s'efface en 1seconde puis se supprimer au bout d'une seconde
    document.querySelectorAll('.alert').forEach(el => {
        el.style.transition = 'opacity 1s ease-in-out';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 1000); // supprime après le fondu
    });
}, 5000); // 10000ms = 10 secondes

// je met mindow.variable pour qu'elle soit accessible dans l'HTML
// pour rendre visible ou non les mots de passe lors de l'inscription et de la connexion d'un utilisateur 
// (nameInput permet de recuperer le nom de l'id de l'input pour pouvoir le realiser sur plusieurs input different avec une seule fonction)
window.changeInput = function (nameInput){
    let input = document.getElementById(nameInput);
    // let icon = event.target;

    if (input.type === "password") {
        input.type = "text";
        // icon.classList.remove("fa-eye");
        // icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        // icon.classList.remove("fa-eye-slash");
        // icon.classList.add("fa-eye");
    }
}