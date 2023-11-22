let testWorkoutList = document.querySelectorAll('.c-program__workouts-name');
let testWorkoutNames = [];
for(let i = 0; i < testWorkoutList.length; i++){
	let currentName = testWorkoutList[i].innerHTML.split(' ');
	currentName.shift();
	testWorkoutNames.push(currentName.join(' '));
}

let testCardNames = document.querySelectorAll('.day-workouts__card-name');
console.log(testCardNames)

// check the match between the lists after clearing one of the workouts
for(let i = 0; i < testCardNames.length; i++){
	let checkName = false;
	for(let j = 0; j < testWorkoutNames.length; j++){
		if(testCardNames[i].innerHTML == testWorkoutNames[j]){
			checkName = true;
		}
	}
	if(checkName){
		console.log(true);
	}
	else{
		console.log(false);
	}
}