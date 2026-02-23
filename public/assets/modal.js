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
            headers: {'Content-Type': 'application/json'},
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

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        window.app.closeModal();
    }
});