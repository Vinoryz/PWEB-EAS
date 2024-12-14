<!-- components/confirmation-modal.php -->
<div class="modal-overlay" id="confirmationModal">
    <div class="modal-content">
        <h2><?= $modalTitle ?? 'Confirm Action' ?></h2>
        <p><?= $modalMessage ?? 'Are you sure you want to proceed?' ?></p>
        <div class="modal-buttons">
            <button type="button" class="modal-btn cancel" onclick="closeModal()">Cancel</button>
            <button type="button" class="modal-btn confirm" onclick="confirmAction()">Confirm</button>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 90%;
    text-align: center;
}

.modal-content h2 {
    margin-top: 0;
    color: #333;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.modal-content p {
    color: #666;
    margin-bottom: 1.5rem;
}

.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.modal-btn {
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.modal-btn.cancel {
    background-color: #e0e0e0;
    color: #333;
}

.modal-btn.confirm {
    background-color: #4CAF50;
    color: white;
}

.modal-btn.cancel:hover {
    background-color: #d0d0d0;
}

.modal-btn.confirm:hover {
    background-color: #45a049;
}
</style>

<script>
let formToSubmit = null;

function showModal(formId) {
    formToSubmit = document.getElementById(formId);
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'none';
    formToSubmit = null;
}

function confirmAction() {
    if (formToSubmit) {
        const originalOnSubmit = formToSubmit.onsubmit;
        formToSubmit.onsubmit = null;
        
        formToSubmit.submit();
        
        formToSubmit.onsubmit = originalOnSubmit;
    }
    closeModal();
}

window.onclick = function(event) {
    const modal = document.getElementById('confirmationModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>