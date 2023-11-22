// ===tests for the inputs and the function of the click function for the register button===

if(document.querySelector('.reg-form__warning')){
	console.log('false');
}
else{
	console.log('true');
}



let reguserName = 'Иван';
let reguserSurname = 'Барбашин';
document.querySelectorAll('.reg-form__profile-input[name="reg_status"]')[0].checked = true; // select type of profile(sportsman)
let reguserLogin = '';
let reguserPassword = '';
let reguserCkeckPassword = '';

// ---test for too short login---
// reguserLogin = 'a';
// reguserPassword = '123154223od';
// reguserCkeckPassword = '123154223od';

// document.querySelector('.reg-form__input[name="reg_name"]').value = userName;
// document.querySelector('.reg-form__input[name="reg_surname"]').value = userSurname;
// document.querySelector('.reg-form__input[name="reg_login"]').value = userLogin;
// document.querySelector('.reg-form__input[name="reg_password"]').value = userPassword;
// document.querySelector('.reg-form__input[name="reg_password2"]').value = userCkeckPassword;


// ---test for too short password---
// reguserLogin = 'ivan999';
// reguserPassword = '1234567';
// reguserCkeckPassword = '1234567';

// document.querySelector('.reg-form__input[name="reg_name"]').value = reguserName;
// document.querySelector('.reg-form__input[name="reg_surname"]').value = reguserSurname;
// document.querySelector('.reg-form__input[name="reg_login"]').value = reguserLogin;
// document.querySelector('.reg-form__input[name="reg_password"]').value = reguserPassword;
// document.querySelector('.reg-form__input[name="reg_password2"]').value = reguserCkeckPassword;


// ---test if the password does not contain a letter---
// reguserLogin = 'ivan999';
// reguserPassword = '2932388352293';
// reguserCkeckPassword = '2932388352293';

// document.querySelector('.reg-form__input[name="reg_name"]').value = reguserName;
// document.querySelector('.reg-form__input[name="reg_surname"]').value = reguserSurname;
// document.querySelector('.reg-form__input[name="reg_login"]').value = reguserLogin;
// document.querySelector('.reg-form__input[name="reg_password"]').value = reguserPassword;
// document.querySelector('.reg-form__input[name="reg_password2"]').value = reguserCkeckPassword;


// ---checking that the login is busy---
// reguserLogin = 'ivanbarbash';
// reguserPassword = '123154223do';
// reguserCkeckPassword = '123154223do';

// document.querySelector('.reg-form__input[name="reg_name"]').value = reguserName;
// document.querySelector('.reg-form__input[name="reg_surname"]').value = reguserSurname;
// document.querySelector('.reg-form__input[name="reg_login"]').value = reguserLogin;
// document.querySelector('.reg-form__input[name="reg_password"]').value = reguserPassword;
// document.querySelector('.reg-form__input[name="reg_password2"]').value = reguserCkeckPassword;


// ---checking if passwords don't match---
// reguserLogin = 'ivanbarbash123';
// reguserPassword = '123154223d';
// reguserCkeckPassword = '123154223do';

// document.querySelector('.reg-form__input[name="reg_name"]').value = reguserName;
// document.querySelector('.reg-form__input[name="reg_surname"]').value = reguserSurname;
// document.querySelector('.reg-form__input[name="reg_login"]').value = reguserLogin;
// document.querySelector('.reg-form__input[name="reg_password"]').value = reguserPassword;
// document.querySelector('.reg-form__input[name="reg_password2"]').value = reguserCkeckPassword;


// ---if everything is fine, then we register the user---
// reguserLogin = 'ivanbarbash12345';
// reguserPassword = '123154223do';
// reguserCkeckPassword = '123154223do';

// document.querySelector('.reg-form__input[name="reg_name"]').value = reguserName;
// document.querySelector('.reg-form__input[name="reg_surname"]').value = reguserSurname;
// document.querySelector('.reg-form__input[name="reg_login"]').value = reguserLogin;
// document.querySelector('.reg-form__input[name="reg_password"]').value = reguserPassword;
// document.querySelector('.reg-form__input[name="reg_password2"]').value = reguserCkeckPassword;