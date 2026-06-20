<script>
    (function () {
        const wrapper = document.getElementById('boatsWrapper');
        if (!wrapper) {
            return;
        }

        const captainRequiredMsg = @json(__('owner.trips.boats.captain_required'));

        function updateCaptainLabel(dropdown) {
            if (!dropdown) {
                return;
            }
            const label = dropdown.querySelector('.captain-dropdown-label');
            const names = Array.from(dropdown.querySelectorAll('.captain-checkbox:checked'))
                .map(cb => cb.dataset.name);
            label.textContent = names.length ? names.join('، ') : (dropdown.dataset.placeholder || '');
        }

        document.addEventListener('click', function (e) {
            const addBtn = e.target.closest('#addBoatRow');
            if (addBtn) {
                let index = parseInt(wrapper.dataset.nextIndex, 10) || 0;
                let html = document.getElementById('boatRowTemplate').innerHTML.replace(/__INDEX__/g, index);
                wrapper.insertAdjacentHTML('beforeend', html);
                wrapper.dataset.nextIndex = index + 1;
                return;
            }

            const removeBtn = e.target.closest('.btn-remove-boat');
            if (removeBtn) {
                let rows = wrapper.querySelectorAll('.boat-row');
                let row = removeBtn.closest('.boat-row');
                if (rows.length > 1) {
                    row.remove();
                } else {
                    row.querySelectorAll('select').forEach(function (sel) {
                        sel.value = '';
                    });
                    row.querySelectorAll('.captain-checkbox').forEach(cb => { cb.checked = false; });
                    updateCaptainLabel(row.querySelector('.captain-dropdown'));
                }
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('captain-checkbox')) {
                updateCaptainLabel(e.target.closest('.captain-dropdown'));
            }
        });

        const form = wrapper.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                let valid = true;
                wrapper.querySelectorAll('.boat-row').forEach(function (row) {
                    const toggle = row.querySelector('.captain-dropdown-toggle');
                    const checked = row.querySelectorAll('.captain-checkbox:checked').length;
                    if (checked === 0) {
                        valid = false;
                        if (toggle) { toggle.classList.add('is-invalid'); }
                    } else if (toggle) {
                        toggle.classList.remove('is-invalid');
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    alert(captainRequiredMsg);
                }
            }, true);
        }
    })();
</script>
