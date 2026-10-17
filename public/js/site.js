(() => {
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        let removalTimer;

        const dismissToast = () => {
            if (toast.classList.contains('is-dismissed')) {
                return;
            }

            toast.classList.add('is-dismissed');
            removalTimer = window.setTimeout(() => toast.remove(), 250);
        };

        toast.querySelector('.toast-dismiss')?.addEventListener('click', () => {
            window.clearTimeout(removalTimer);
            dismissToast();
        });
        window.setTimeout(dismissToast, 5000);
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('.button').forEach((button) => {
        button.addEventListener('pointerdown', (event) => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const bounds = button.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'button-ripple';
            ripple.setAttribute('aria-hidden', 'true');
            ripple.style.left = `${event.clientX - bounds.left}px`;
            ripple.style.top = `${event.clientY - bounds.top}px`;
            button.append(ripple);
            ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
        });
    });

    document.querySelectorAll('input[type="tel"][inputmode="numeric"]').forEach((input) => {
        input.addEventListener('input', () => {
            const cursorPosition = input.selectionStart;
            const numericValue = input.value.replace(/\D/g, '');

            if (numericValue === input.value) {
                return;
            }

            const numericCursorPosition = cursorPosition === null
                ? numericValue.length
                : input.value.slice(0, cursorPosition).replace(/\D/g, '').length;

            input.value = numericValue;
            input.setSelectionRange(numericCursorPosition, numericCursorPosition);
        });
    });

    document.querySelectorAll('[data-customer-select]').forEach((select) => {
        const fields = document.getElementById(select.dataset.target);

        if (!fields) {
            return;
        }

        const updateCustomerFields = () => {
            const creatingCustomer = select.value === '';
            fields.hidden = !creatingCustomer;
            fields.querySelectorAll('input').forEach((input) => {
                input.required = creatingCustomer;
                input.disabled = !creatingCustomer;
            });
        };

        select.addEventListener('change', updateCustomerFields);
        updateCustomerFields();
    });

    document.querySelectorAll('[data-customer-role-select]').forEach((select) => {
        const phoneInput = document.getElementById(select.dataset.target);

        if (!phoneInput) {
            return;
        }

        const updatePhoneRequirement = () => {
            phoneInput.required = select.selectedOptions[0]?.textContent.trim() === 'Customer';
        };

        select.addEventListener('change', updatePhoneRequirement);
        updatePhoneRequirement();
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.dataset.target);

        if (!input) {
            return;
        }

        button.addEventListener('click', () => {
            const isVisible = input.type === 'password';
            input.type = isVisible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(isVisible));
            button.setAttribute(
                'aria-label',
                `${isVisible ? 'Sembunyikan' : 'Tampilkan'} ${input.id === 'password_confirmation' ? 'konfirmasi kata sandi' : 'kata sandi'}`
            );
            input.focus();
        });
    });

    const elements = document.querySelectorAll('.reveal');

    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach((element) => element.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    elements.forEach((element) => observer.observe(element));
})();
