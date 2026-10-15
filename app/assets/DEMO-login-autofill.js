// TODO: A SUPPRIMER EN PROD
const demoRoot = document.querySelector('[data-demo-login-root]');

if (demoRoot) {
    const form = document.querySelector('[data-demo-login-form]');

    if (form) {
        const emailInput = form.querySelector('#username');
        const passwordInput = form.querySelector('#password');

        if (emailInput && passwordInput) {
            demoRoot.querySelectorAll('[data-demo-login-fill]').forEach((button) => {
                button.addEventListener('click', () => {
                    const email = button.getAttribute('data-demo-email') ?? '';
                    const password = button.getAttribute('data-demo-password') ?? '';

                    emailInput.value = email;
                    passwordInput.value = password;

                    emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                    passwordInput.dispatchEvent(new Event('input', { bubbles: true }));

                    passwordInput.focus();
                });
            });
        }
    }
}
