// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/phoneResponsive.css';


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// ceci est un timer
setTimeout(() => {
    // je recupere la classe alert, j'en fais un tableau et mais une transition ou au bout de 5 secondes le message s'efface en 1seconde puis se supprimer au bout d'une seconde
    document.querySelectorAll('.alert').forEach(el => {
        el.style.transition = 'opacity 1s ease-in-out';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 3000); // supprime après le fondu
    });
}, 7000); // 7000ms = 7 secondes

// je met mindow.variable pour qu'elle soit accessible dans l'HTML
// pour rendre visible ou non les mots de passe lors de l'inscription et de la connexion d'un utilisateur 
// (nameInput permet de recuperer le nom de l'id de l'input pour pouvoir le realiser sur plusieurs input different avec une seule fonction)
window.changeInput = function (nameInput){
    let input = document.getElementById(nameInput);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// gestion de la navbar pour les populaires

let scrollContainer = document.querySelector('.gallery');
let backBtn = document.getElementById("backBtn");
let nextBtn = document.getElementById("nextBtn");

// c'est un évènement sur la roulette de la souris
window.scrollContainer = function () {
    evt.preventDefault();
    scrollContainer.scrollLeft += evt.deltaY;
    scrollContainer.style.scrollBehavior = 'auto';
};
// c'est l'évènement quand on veut aller à droite
nextBtn.addEventListener("click", function() {
    scrollContainer.style.scrollBehavior = 'smooth';
    scrollContainer.scrollLeft += window.innerWidth /2.1;
});
// c'est l'évènement pour aller à gauche
backBtn.addEventListener("click", function() {
    scrollContainer.style.scrollBehavior = 'smooth';
    scrollContainer.scrollLeft -= window.innerWidth /2.1;
});
function changeNbSlide(n, change){
    if (change == 1) {
        if (n == 3) {
            return 1;
        } else {
          return n++;  
        }
    } else {
        if (n==1) {
           return 3;
        } else {
            return n--;
        }
        return n;
    }
}
// leftPopulars.addEventListener("click", changeSlide(n,nbSlide));
// rightPopulars.addEventListener("click", changeSlide(n,nbSlide));
window.changeSlide = function  (n, nbSlide){
    nbSlide = document.getElementsByClassName('slide-'.nbSlide);
    nbSlide.classList.add('no-active');
    // nbslide = changeNbSlide(nbSlide, n);
    nbSlide = document.getElementsByClassName('slide'.changeNbSlide(nbSlide, n));
    nbSlide.classList.remove('no-active');
        
}

// je fais la gestion du bouton qui me fait remonter la page en haut

const scrollToTopBtn = document.getElementById("scrollToTopBtn");
// si je suis à une certaine distance dsu haut de la page, le bouton il s'affiche
window.onscroll = function (){
    if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50)
    {
        scrollToTopBtn.classList.add('show');
    } else 
    {
        scrollToTopBtn.classList.remove('show');
    }
};
// si je clique sur le bouton sa me retroune sur le haut de la page de façon réguliere
scrollToTopBtn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
});


const burgerMenu = document.getElementById('burger-menu');
const navRight = document.querySelector('.rightnav');
burgerMenu.addEventListener('click', () => {
    if(navRight.style.display == "" || navRight.style.display == "none"){
        navRight.style.display = 'block';
    }else {
        navRight.style.display = 'none';
    }
})