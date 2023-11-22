let testExerciseNames = document.querySelectorAll('.exercise-item__title');
let testExerciseItems = document.querySelectorAll('.exercise-item');

// search logic
function SearchItems(val){
	val = val.trim().replaceAll(' ', '').toUpperCase(); // get value of search's input
	if(val != ''){ // if value not none
		testExerciseNames.forEach(function(elem){
			if(elem.innerText.trim().replaceAll(' ', '').toUpperCase().search(val) == -1){
				let cur_exercise = elem.parentNode;
				if(cur_exercise){
					cur_exercise.classList.add('hide');
				}
			}
			else{ // if name matches print block
				let cur_exercise = elem.parentNode;
				if(cur_exercise){
					cur_exercise.classList.remove('hide');
				}
			}
		});
	}

	else{ // if value none print all cards
		testExerciseNames.forEach(function(elem){
			let cur_exercise = elem.parentNode;
			if(cur_exercise){
				cur_exercise.classList.remove('hide');
			}
		});
	}
}

// // search for the full name
// SearchItems('Планка на локтях');
// let searchCheck = false;

// // check that the remaining block contains the desired name
// for(let i = 0; i < testExerciseItems.length - 1; i++){
// 	if(!testExerciseItems[i].classList.contains('hide')){
// 		if(testExerciseNames[i].innerHTML = 'Планка на локтях'){
// 			searchCheck = true;
// 		}
// 	}
// }

// // if the search worked, output true
// if(searchCheck){
// 	console.log(true);
// }
// else{
// 	console.log(false)
// }


// // search if we start / finish / write spaces in the middle
// SearchItems('   Планка        на     локтях       ');
// let searchCheck = false;

// // check that the remaining block contains the desired name
// for(let i = 0; i < testExerciseItems.length - 1; i++){
// 	if(!testExerciseItems[i].classList.contains('hide')){
// 		if(testExerciseNames[i].innerHTML = 'Планка на локтях'){
// 			searchCheck = true;
// 		}
// 	}
// }

// // if the search worked, output true
// if(searchCheck){
// 	console.log(true);
// }
// else{
// 	console.log(false)
// }


// // search if we enter only the middle of the query
// SearchItems('нка на лок');
// let searchCheck = false;

// // check that the remaining block contains the desired name
// for(let i = 0; i < testExerciseItems.length - 1; i++){
// 	if(!testExerciseItems[i].classList.contains('hide')){
// 		if(testExerciseNames[i].innerHTML = 'Планка на локтях'){
// 			searchCheck = true;
// 		}
// 	}
// }

// // if the search worked, output true
// if(searchCheck){
// 	console.log(true);
// }
// else{
// 	console.log(false)
// }



// // search if there are no results
// SearchItems('ывмывлмтыьвмыв');
// let searchCheck = false;

// // check that the remaining block contains the desired name
// for(let i = 0; i < testExerciseItems.length - 1; i++){
// 	if(!testExerciseItems[i].classList.contains('hide')){
// 		if(testExerciseNames[i].innerHTML = 'Планка на локтях'){
// 			searchCheck = true;
// 		}
// 	}
// }

// // if the search worked, output true
// if(searchCheck){
// 	console.log(true);
// }
// else{
// 	console.log(false)
// }
