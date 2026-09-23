document.addEventListener('DOMContentLoaded', function () {
    const attributeSelect = document.getElementById('user_attribute_attribute');
    const valueContainer = document.getElementById('value-field-container');
    const modal = document.getElementById('attributeModal');

    if (!attributeSelect || !valueContainer) return;

    attributeSelect.addEventListener('change', function () {
        const attributeId = this.value;

        if (!attributeId) {
            valueContainer.innerHTML = '';
            return;
        }

        fetch('/profile/attribute/value-field', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ attribute: attributeId }),
        })
        .then(r => r.ok ? r.text() : '')
        .then(html => {
            valueContainer.innerHTML = html;
        })
        .catch(() => {
            valueContainer.innerHTML = '';
        });
    });

    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            valueContainer.innerHTML = '';
            attributeSelect.value = '';
        });
    }
});
