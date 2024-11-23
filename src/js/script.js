document.addEventListener('DOMContentLoaded', function () {

  const loginForm = document.getElementById('login-form');
  const registrationForm = document.getElementById('registration-form');
  const showRegistrationLink = document.getElementById('show-registration');
  const showLoginLink = document.getElementById('show-login');

  showRegistrationLink.addEventListener('click', function (event) {
    event.preventDefault();
    loginForm.style.display = 'none';
    registrationForm.style.display = 'block';
  });

  showLoginLink.addEventListener('click', function (event) {
    event.preventDefault();
    registrationForm.style.display = 'none';
    loginForm.style.display = 'block';
  });
});
