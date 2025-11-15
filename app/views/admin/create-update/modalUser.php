<style>
/* Ant Design Modal - Enhanced Style */
#userModal .modal-content {
    border-radius: 8px;
    box-shadow: 0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 3px 6px -4px rgba(0, 0, 0, 0.12), 0 9px 28px 8px rgba(0, 0, 0, 0.05);
    border: none;
    max-width: 520px;
    margin: 0 auto;
}

#userModal .modal-header {
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    border-radius: 8px 8px 0 0;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#userModal .modal-title {
    font-size: 16px;
    font-weight: 600;
    color: rgba(0, 0, 0, 0.88);
    margin: 0;
    line-height: 1.5;
}

#userModal .btn-close {
    font-size: 14px;
    color: rgba(0, 0, 0, 0.45);
    opacity: 1;
    padding: 0;
    width: 22px;
    height: 22px;
    background: none;
    border: none;
    line-height: 1;
    transition: color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

#userModal .btn-close:hover {
    color: rgba(0, 0, 0, 0.88);
    background-color: rgba(0, 0, 0, 0.06);
    border-radius: 4px;
}

#userModal .modal-body {
    padding: 24px;
    background: #fff;
}

#userModal .form-label {
    display: block;
    font-size: 14px;
    font-weight: 400;
    color: rgba(0, 0, 0, 0.88);
    margin-bottom: 8px;
    line-height: 1.5714;
}

#userModal .form-control {
    width: 100%;
    height: 32px;
    padding: 4px 11px;
    font-size: 14px;
    line-height: 1.5714;
    color: rgba(0, 0, 0, 0.88);
    background: #ffffff;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
    box-shadow: none;
}

#userModal .form-control::placeholder {
    color: rgba(0, 0, 0, 0.25);
}

#userModal .form-control:hover {
    border-color: #4096ff;
}

#userModal .form-control:focus {
    border-color: #4096ff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(5, 145, 255, 0.1);
    background: #fff;
}

#userModal .form-control:disabled {
    background-color: rgba(0, 0, 0, 0.04);
    border-color: #d9d9d9;
    color: rgba(0, 0, 0, 0.25);
    cursor: not-allowed;
}

#userModal .form-text {
    font-size: 12px;
    color: rgba(0, 0, 0, 0.45);
    margin-top: 4px;
    line-height: 1.5714;
}

#userModal .form-check-input {
    width: 16px;
    height: 16px;
    margin-top: 2px;
    margin-right: 0;
    border: 1px solid #d9d9d9;
    border-radius: 2px;
    background-color: #fff;
    cursor: pointer;
    transition: all 0.3s;
    flex-shrink: 0;
}

#userModal .form-check-input:hover {
    border-color: #4096ff;
}

#userModal .form-check-input:checked {
    background-color: #1677ff;
    border-color: #1677ff;
}

#userModal .form-check-input:focus {
    box-shadow: 0 0 0 2px rgba(5, 145, 255, 0.1);
    outline: none;
}

#userModal .form-check-label {
    font-size: 14px;
    color: rgba(0, 0, 0, 0.88);
    cursor: pointer;
    line-height: 1.5714;
    user-select: none;
}

#userModal .modal-footer {
    background: #fff;
    border-top: 1px solid #f0f0f0;
    border-radius: 0 0 8px 8px;
    padding: 10px 16px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

#userModal .btn {
    height: 32px;
    padding: 4px 15px;
    font-size: 14px;
    font-weight: 400;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
    border: 1px solid transparent;
    cursor: pointer;
    line-height: 1.5714;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    text-align: center;
    user-select: none;
}

#userModal .btn-primary {
    background: #1677ff;
    border-color: #1677ff;
    color: #fff;
    box-shadow: 0 2px 0 rgba(5, 145, 255, 0.1);
}

#userModal .btn-primary:hover {
    background: #4096ff;
    border-color: #4096ff;
    color: #fff;
}

#userModal .btn-primary:active {
    background: #0958d9;
    border-color: #0958d9;
}

#userModal .btn-primary:disabled {
    background: rgba(0, 0, 0, 0.04);
    border-color: #d9d9d9;
    color: rgba(0, 0, 0, 0.25);
    cursor: not-allowed;
    box-shadow: none;
}

#userModal .btn-secondary {
    background: #fff;
    border-color: #d9d9d9;
    color: rgba(0, 0, 0, 0.88);
    box-shadow: 0 2px 0 rgba(0, 0, 0, 0.02);
}

#userModal .btn-secondary:hover {
    border-color: #4096ff;
    color: #4096ff;
}

#userModal .btn-secondary:active {
    border-color: #0958d9;
    color: #0958d9;
}

#userModal .text-danger {
    color: #ff4d4f !important;
}

#userModal .mb-3 {
    margin-bottom: 24px;
}

#userModal .mb-3:last-child {
    margin-bottom: 0;
}

/* Toast notification style */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 3px 6px -4px rgba(0, 0, 0, 0.12);
    padding: 16px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast-notification.success {
    border-left: 4px solid #52c41a;
}

.toast-notification.error {
    border-left: 4px solid #ff4d4f;
}

.toast-notification .icon {
    font-size: 20px;
}

.toast-notification.success .icon {
    color: #52c41a;
}

.toast-notification.error .icon {
    color: #ff4d4f;
}

.toast-notification .content {
    flex: 1;
}

.toast-notification .title {
    font-weight: 600;
    margin-bottom: 4px;
}

.toast-notification .message {
    font-size: 14px;
    color: rgba(0, 0, 0, 0.65);
}

/* Loading spinner on button */
.btn-loading {
    position: relative;
    pointer-events: none;
    color: transparent !important;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spinner 0.6s linear infinite;
}

@keyframes spinner {
    to {
        transform: rotate(360deg);
    }
}
</style>

<!-- User Modal - Ant Design Style -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger" id="usernameRequired">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required minlength="3" maxlength="50" placeholder="Enter username">
                        <div class="form-text">Tên đăng nhập, tối thiểu 3 ký tự, không dấu cách.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email">
                        <div class="form-text">Email hợp lệ để đăng nhập và khôi phục.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="passwordField" class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                        <input type="password" class="form-control" id="passwordField" name="password" placeholder="Enter password (min 6 chars)" minlength="6">
                        <div class="form-text">Tối thiểu 6 ký tự. Để trống khi update để giữ mật khẩu cũ.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required maxlength="100" placeholder="Enter full name">
                        <div class="form-text">Tên đầy đủ của người dùng.</div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" defaultValue="0">
                        <label class="form-check-label" for="is_admin">Is Admin</label>
                        <div class="form-text">Kích hoạt quyền admin.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>