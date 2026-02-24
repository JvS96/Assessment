<?php
require_once __DIR__ . '/bootstrap.php';

use Core\Session;
use Core\Csrf;

Session::start();

if (!Session::get('access_token')) {
    http_response_code(403);
    exit('Unauthorized');
}

$csrfToken = Csrf::generateToken();
?>

<style>
    .modal {
        background: white;
        width: 520px;
        max-width: 95%;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal h2 {
        margin-top: 0;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-size: 13px;
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
    }

    input, textarea, select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        font-size: 14px;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    input.error, textarea.error, select.error {
        border-color: #dc2626;
    }

    .error-text {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .primary {
        background: #2563eb;
        color: white;
    }

    .secondary {
        background: #e5e7eb;
    }

    button {
        padding: 8px 14px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }

    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .success-msg {
        background: #dcfce7;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 10px;
        font-size: 13px;
    }
</style>

<div class="modal" id="createModal">

    <h2>Create Issue</h2>

    <div id="successMsg" class="success-msg" style="display:none;">
        Issue created successfully!
    </div>

    <div class="form-group">
        <label>Title</label>
        <input id="title">
        <div class="error-text" id="titleError"></div>
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea id="body"></textarea>
        <div class="error-text" id="bodyError"></div>
    </div>

    <div class="form-group">
        <label>Client</label>
        <select id="client">
            <option value="">Select Client</option>
            <option>ABC</option>
            <option>XYZ</option>
            <option>MNO</option>
        </select>
        <div class="error-text" id="clientError"></div>
    </div>

    <div class="form-group">
        <label>Priority</label>
        <select id="priority">
            <option value="">Select Priority</option>
            <option>Low</option>
            <option>Medium</option>
            <option>High</option>
        </select>
        <div class="error-text" id="priorityError"></div>
    </div>

    <div class="form-group">
        <label>Type</label>
        <select id="type">
            <option value="">Select Type</option>
            <option value="Bug">Bug</option>
            <option value="Feature">Feature</option>
            <option value="Task">Task</option>>
        </select>
        <div class="error-text" id="typeError"></div>
    </div>

    <input type="hidden" id="csrf" value="<?= $csrfToken ?>">

    <div class="actions">
        <button class="secondary" onclick="window.app.closeModal()">Cancel</button>
        <button class="primary" id="submitBtn" onclick="submitIssue()">Create</button>
    </div>
</div>

<script>
    function clearErrors() {
        document.querySelectorAll('.error-text').forEach(e => e.textContent = '');
        document.querySelectorAll('input, textarea, select')
            .forEach(e => e.classList.remove('error'));
    }

    function validateForm(data) {
        let valid = true;

        if (!data.title) {
            document.getElementById('titleError').textContent = 'Title is required';
            document.getElementById('title').classList.add('error');
            valid = false;
        }

        if (!data.body) {
            document.getElementById('bodyError').textContent = 'Description is required';
            document.getElementById('body').classList.add('error');
            valid = false;
        }

        if (!data.client) {
            document.getElementById('clientError').textContent = 'Client is required';
            document.getElementById('client').classList.add('error');
            valid = false;
        }

        if (!data.priority) {
            document.getElementById('priorityError').textContent = 'Priority is required';
            document.getElementById('priority').classList.add('error');
            valid = false;
        }

        if (!data.type) {
            document.getElementById('typeError').textContent = 'Type is required';
            document.getElementById('type').classList.add('error');
            valid = false;
        }

        return valid;
    }

    async function submitIssue() {
        clearErrors();

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Creating...';

        const data = {
            title: document.getElementById('title').value.trim(),
            body: document.getElementById('body').value.trim(),
            client: document.getElementById('client').value,
            priority: document.getElementById('priority').value,
            type: document.getElementById('type').value,
            _csrf: document.getElementById('csrf').value
        };

        if (!validateForm(data)) {
            btn.disabled = false;
            btn.textContent = 'Create';
            return;
        }

        try {
            const response = await fetch('/api/create.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                alert(result.error || 'Something went wrong');
                btn.disabled = false;
                btn.textContent = 'Create';
                return;
            }

            document.getElementById('successMsg').style.display = 'block';

            setTimeout(() => {
                window.app.closeModal();
                window.app.fetchIssues();
            }, 1000);

        } catch (err) {
            alert('Network error');
            btn.disabled = false;
            btn.textContent = 'Create';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.app.closeModal();
        }
    });
</script>