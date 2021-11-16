//menu deroulant "trier par"
const arrow = document.querySelector(".arrow")
const hiddenLi = document.querySelectorAll(".li-hidden")

arrow.addEventListener('click', () => {
    arrow.classList.toggle('arrow-clicked')

    for(i = 0; i < hiddenLi.length; i++) {
        hiddenLi[i].classList.toggle("li-hidden")
    }
})

//filtre par prix
let prix = document.querySelector(".prix-dynamique")
let prixInput = document.getElementById("prix")
let prixValue = prixInput.value

prix.innerHTML = prixValue + ' €'

prixInput.addEventListener("mousemove", () => {
    
    let prixValue = prixInput.value
    prix.innerHTML = prixValue + ' €'
})

// Filtre sur plus petits ecrans
let filterTitle = document.getElementById('filter-title')
let form = document.getElementById('filter-form')

filterTitle.addEventListener('click', () => {
    form.classList.toggle('form-hidden')
})






