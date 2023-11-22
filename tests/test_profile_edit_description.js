// click save button and checking for changes
if(document.querySelector('.user-about__description-text').innerHTML == 'я начинающий спорстсмен'){
	console.log(true);
}
else{
	console.log(false);
}

// text for testing
let descriptionText = 'я начинающий спорстсмен';

// set value to textarea of description
document.querySelector('.popup-exercise--description-edit').classList.add("open");
document.querySelector('.popup-user__description-edit-text').value = descriptionText;