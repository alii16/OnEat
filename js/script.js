document.addEventListener('DOMContentLoaded', (event) => {
    // Example: Adding event listener to forms
    const registerForm = document.querySelector('#registerForm');
    const loginForm = document.querySelector('#loginForm');
    const addToCartForms = document.querySelectorAll('.addToCartForm');

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Add your validation and submission logic here
            alert('Register form submitted');
            registerForm.submit();
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Add your validation and submission logic here
            alert('Login form submitted');
            loginForm.submit();
        });
    }

    if (addToCartForms.length > 0) {
        addToCartForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                // Add your logic to add item to cart
                alert('Item added to cart');
                form.submit();
            });
        });
    }
});
