import '/public/vendor/statamic/frontend/js/helpers.js';

export default () => ({
    statamic: window.Statamic,
    formErrors: {},
    formResult: false,
    showSuccess: false,

    fieldHasError: function (field) {
        if (this.formErrors[field]) {
            return this.formErrors[field];
        }

        return false;
    },

    scrollToTop: function () {
        this.$refs.form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    getResults: function (formElement, redirect) {
        let context = this;
        let data = new FormData(formElement);

        fetch(formElement.action, {
            method: formElement.method,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data,
        })
        .then(async (response) => {
            let json;
            try {
                json = await response.json();
            } catch (e) {
                json = { errors: { general: [formElement.dataset.genericError || 'Something went wrong. Please try again later.'] } };
            }

            const normalizedErrors = json && (json.errors || json.error) || {};
            context.formErrors = normalizedErrors;
            context.formResult = json || {};

            const hasErrors = normalizedErrors && Object.keys(normalizedErrors).length > 0;

            if (!hasErrors && redirect) {
                window.location.href = redirect;
                return;
            }

            if (!hasErrors && !redirect) {
                context.showSuccess = true;
                context.scrollToTop();
                return;
            }

            context.scrollToTop();
        })
        .catch(() => {
            context.formErrors = { general: [formElement.dataset.genericError || 'Something went wrong. Please try again later.'] };
            context.formResult = { errors: context.formErrors };
            context.scrollToTop();
        });
    },
});
