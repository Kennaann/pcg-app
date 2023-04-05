// form avis 

const closeForm = document.getElementById('close-form')
const openForm = document.getElementById('ecrire-avis')
const form = document.getElementById('form-avis')
const background = document.getElementById('form-bg')

openForm.addEventListener('click',() => {
    form.classList.remove('closed')
    background.classList.remove('closed')
})

background.addEventListener('click',() => {
    form.classList.add('closed')
    background.classList.add('closed')
})

// pop up not user

const closePopUp = document.getElementById('close-pop-up')
const openPopUp = document.getElementById('open-pop-up')
const popUp = document.getElementById('pop-up')


openPopUp.addEventListener('click',() => {
    popUp.classList.remove('closed')
    background.classList.remove('closed')
})

background.addEventListener('click',() => {
    popUp.classList.add('closed')
    background.classList.add('closed')
})
