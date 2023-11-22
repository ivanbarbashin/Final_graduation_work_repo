if(document.querySelectorAll('.progress-block__physical-info-item')[0].innerHTML == '75 кг'){
	console.log(true);
}
else{
	console.log(false);
}
if(document.querySelectorAll('.progress-block__physical-info-item')[1].innerHTML == '190 см'){
	console.log(true);
}
else{
	console.log(false);
}


let testPopupInupts = document.querySelectorAll('.popup-physics-data__item-input');
// set physical data and then click on the button
PhysicDataPopup.classList.add("open");
testPopupInupts[0].value = 190;
testPopupInupts[1].value = 75;