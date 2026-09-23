document.addEventListener('DOMContentLoaded', function () {
            function toggleOptions() {
                const optionsField = document.querySelector('[data-ea-collection-field="true"]');
                const typeSelect = document.getElementById('Attribute_type');
                const isMultipleInput = document.querySelector('.field-is-multiple');

                if (!optionsField || !typeSelect || !isMultipleInput) return;

                optionsField.style.display = typeSelect.value === 'select' ? '' : 'none';
                isMultipleInput.style.display = typeSelect.value === 'select' ? '' : 'none';


            }

            toggleOptions();

            document.addEventListener('change', function(e) {
                if (e.target?.id === 'Attribute_type') {
                    toggleOptions();
                }
            });
        });
