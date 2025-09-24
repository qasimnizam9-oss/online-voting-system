document.addEventListener('DOMContentLoaded', function() {
    // Candidate selection
    const candidateCards = document.querySelectorAll('.candidate-card');
    candidateCards.forEach(card => {
        card.addEventListener('click', function() {
            const positionId = this.dataset.position;
            
            // Remove previous selection for this position
            document.querySelectorAll(`.candidate-card[data-position="${positionId}"]`).forEach(c => {
                c.classList.remove('selected');
            });
            
            // Select current card
            this.classList.add('selected');
            
            // Update hidden input
            document.querySelector(`input[name="position_${positionId}"]`).value = this.dataset.candidate;
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill all required fields');
            }
        });
    });

    // Toast notifications
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showToast(urlParams.get('success'), 'success');
    }
    if (urlParams.has('error')) {
        showToast(urlParams.get('error'), 'error');
    }
});

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to container
    const container = document.getElementById('toastContainer') || createToastContainer();
    container.appendChild(toast);
    
    // Show toast
    new bootstrap.Toast(toast).show();
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}