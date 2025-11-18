// User Management API Handler
$(document).ready(function () {
    // Tooltip init
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Bind event cho update buttons
    document.addEventListener('click', function (e) {
        if (e.target.closest('.update-btn')) {
            const button = e.target.closest('.update-btn');
            const rawUserData = button.dataset.user;
            openUpdateModal(rawUserData);
        }

        // Bind event cho delete buttons
        if (e.target.closest('.delete-btn')) {
            const button = e.target.closest('.delete-btn');
            const userId = button.dataset.userId;
            const username = button.dataset.username;
            confirmDelete(userId, username);
        }
    });

    // Submit form handler
    const form = document.getElementById('userForm');
    form.addEventListener('submit', handleFormSubmit);
});

// Modal elements
const modal = new bootstrap.Modal(document.getElementById('userModal'));
const form = document.getElementById('userForm');
const modalTitle = document.querySelector('#userModal .modal-title');
const passwordField = document.getElementById('passwordField');
const passwordRequired = document.getElementById('passwordRequired');
const submitBtn = document.getElementById('submitBtn');

// Reset form
function resetForm() {
    form.reset();
    document.getElementById('userId').value = '';
    passwordField.disabled = false;
    passwordField.required = true;
    passwordRequired.style.display = 'inline';
    passwordField.placeholder = 'Enter password (min 6 chars)';
}

// Open Create Modal
function openCreateModal() {
    modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Create User';
    form.dataset.action = 'create';
    resetForm();
    modal.show();
}

// Open Update Modal
function openUpdateModal(rawUserData) {
    console.log('Raw user data from data-user:', rawUserData);
    try {
        const userData = JSON.parse(rawUserData);
        console.log('Parsed user data:', userData);

        modalTitle.innerHTML = '<i class="bi bi-pencil me-2"></i>Update User';
        form.dataset.action = 'update';

        document.getElementById('userId').value = userData.id || '';
        document.querySelector('input[name="username"]').value = userData.username || '';
        document.querySelector('input[name="email"]').value = userData.email || '';
        document.querySelector('input[name="full_name"]').value = userData.full_name || '';
        document.querySelector('input[name="is_admin"]').checked = !!userData.is_admin;

        // Password cho update
        passwordField.disabled = true;
        passwordField.value = '';
        passwordField.required = false;
        passwordRequired.style.display = 'none';
        passwordField.placeholder = 'Leave blank to keep current password';

        modal.show();
    } catch (error) {
        console.error('Lỗi parse JSON trong openUpdateModal:', error, 'Raw data:', rawUserData);
        showToast('error', 'Lỗi dữ liệu', 'Không thể load thông tin user. Vui lòng refresh trang.');
    }
}

// Handle Form Submit
async function handleFormSubmit(e) {
    e.preventDefault();

    const username = document.querySelector('input[name="username"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const fullName = document.querySelector('input[name="full_name"]').value.trim();
    const password = passwordField.value.trim();
    const isAdmin = document.querySelector('input[name="is_admin"]').checked ? 1 : 0;
    const userId = document.getElementById('userId').value;
    const action = form.dataset.action;

    // Validation
    if (!username || !email || !fullName) {
        showToast('error', 'Validation Error', 'Vui lòng điền đầy đủ thông tin bắt buộc!');
        return;
    }

    if (action === 'create' && (!password || password.length < 6)) {
        showToast('error', 'Validation Error', 'Password là bắt buộc và phải có ít nhất 6 ký tự!');
        return;
    }

    // Prepare data
    const fd = new FormData();
    fd.append('username', username);
    fd.append('email', email);
    fd.append('full_name', fullName);
    fd.append('is_admin', isAdmin);

    // Add password if provided
    if (password) {
        fd.append('password', password);
    }

    // Add user_id for update
    if (action === 'update') {
        fd.append('user_id', userId);
    }

    // Show loading
    submitBtn.classList.add('btn-loading');
    submitBtn.disabled = true;

    // Determine endpoint
    const url = action === 'create' ? '/user/create' : '/user/update';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

    try {
        $.ajax({
            url,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-Token': csrfToken },
            success: (res) => {
                if (res.success) {
                    modal.hide();
                    toastr.options = { "timeout": 2000 }
                    toastr.success(res.message);
                    setTimeout(() => {
                        location.reload()
                    }, 2000)
                }
                else {
                    toastr.options = { "timeout": 2000 }
                    toastr.error(res.message);
                }

            },
            error: (xhr) => {
                toastr.options = { "timeout": 2000 }
                toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
            }
        })
    } catch (error) {
        toastr.options = { "timeout": 2000 }
        toastr.error("Lỗi kết nối đến server.");
    } finally {
        // Hide loading
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
    }
}

// Confirm Delete
function confirmDelete(userId, username) {
    if (confirm(`Bạn có chắc chắn muốn xóa user "${username}"?`)) {
        deleteUser(userId);
    }
}

// Delete User
async function deleteUser(userId) {
    try {
        const fd = new FormData();
        fd.append('user_id', userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        $.ajax({
            url: '/user/delete',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-Token': csrfToken },
            success: (res) => {
                if (res.success) {
                    toastr.options = { "timeout": 2000 }
                    toastr.success(res.message);
                    setTimeout(() => {
                        location.reload()
                    }, 2000)
                }
                else {
                    toastr.options = { "timeout": 2000 }
                    toastr.error(res.message);
                }

            },
            error: (xhr) => {
                toastr.options = { "timeout": 2000 }
                toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
            }
        })
    } catch (error) {
        console.error('Delete Error:', error);
        toastr.options = { "timeout": 2000 }
        toastr.error("Lỗi kết nối đến server.");
    }
}
