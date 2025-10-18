
window._notifyQueue = window._notifyQueue || [];

// Exposed helper (will be set to enqueue while script is loading)
window.notify = function(message, type) {
    window._notifyQueue.push({ message: message, type: type || 'info' });
};

// Hàm chính để hiển thị thông báo
function showNotify(message, type = 'info') {
    let container = document.getElementById('notify-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notify-container';
        container.style.position = 'fixed';
        container.style.top = '24px';
        container.style.left = '50%';
        container.style.transform = 'translateX(-50%)';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.alignItems = 'center';
        container.style.gap = '8px';
        container.style.maxWidth = '90%';
        document.body.appendChild(container);
    }

    // Tạo toast
    const toast = document.createElement('div');
    toast.className = `ant-message ant-message-${type}`;
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.minWidth = '300px';
    toast.style.maxWidth = '400px';
    toast.style.padding = '12px 16px';
    toast.style.borderRadius = '4px';
    toast.style.boxShadow = '0 3px 6px -4px rgba(0, 0, 0, 0.12), 0 6px 16px 0 rgba(0, 0, 0, 0.08), 0 9px 28px 8px rgba(0, 0, 0, 0.05)';
    toast.style.background = '#fff';
    toast.style.color = '#333';
    toast.style.fontSize = '14px';
    toast.style.fontWeight = '400';
    toast.style.position = 'relative';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    toast.style.transition = 'all 0.3s cubic-bezier(0.645, 0.045, 0.355, 1)';
    
    // Màu border dựa trên type (tương tự Ant Design)
    const borderColor = type === 'success' ? '#52c41a' : 
                        type === 'error' ? '#ff4d4f' : 
                        type === 'warning' ? '#faad14' : '#1890ff';
    toast.style.border = `1px solid ${borderColor}`;
    toast.style.borderLeft = `4px solid ${borderColor}`;

    // Icon
    const icon = document.createElement('span');
    icon.style.fontSize = '16px';
    icon.style.marginRight = '8px';
    icon.style.flexShrink = '0';
    icon.innerHTML = type === 'success' ? '✅' : 
                     type === 'error' ? '❌' : 
                     type === 'warning' ? '⚠️' : 'ℹ️';
    icon.style.color = borderColor;
    toast.appendChild(icon);

    // Nội dung
    const text = document.createElement('span');
    text.textContent = message;
    text.style.flex = '1';
    text.style.lineHeight = '1.5715';
    toast.appendChild(text);

    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '&times;';
    closeBtn.style.background = 'none';
    closeBtn.style.border = 'none';
    closeBtn.style.color = '#999';
    closeBtn.style.fontSize = '16px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.marginLeft = '8px';
    closeBtn.style.padding = '4px';
    closeBtn.style.opacity = '0.6';
    closeBtn.style.lineHeight = '1';
    closeBtn.onmouseover = () => closeBtn.style.opacity = '1';
    closeBtn.onmouseout = () => closeBtn.style.opacity = '0.6';
    closeBtn.onclick = (e) => {
        e.stopPropagation();
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (container.contains(toast)) container.removeChild(toast);
        }, 300);
    };
    toast.appendChild(closeBtn);

    container.appendChild(toast);

    // Hiệu ứng hiện
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    // Tự động ẩn sau 3s (default Ant Design)
    let duration = 3000;
    if (type === 'error' || type === 'warning') duration = 4500; // Lâu hơn cho error/warning
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (container.contains(toast)) container.removeChild(toast);
        }, 300);
    }, duration);
}

// Các hàm tiện ích
function notifySuccess(message) {
    showNotify(message, 'success');
}

function notifyError(message) {
    showNotify(message, 'error');
}

function notifyInfo(message) {
    showNotify(message, 'info');
}

function notifyWarning(message) {
    showNotify(message, 'warning');
}

// Process any queued notifications that were added before this script loaded
function _flushNotifyQueue() {
    if (!window._notifyQueue || !Array.isArray(window._notifyQueue)) return;
    while (window._notifyQueue.length) {
        var item = window._notifyQueue.shift();
        try {
            if (item && item.message) {
                showNotify(item.message, item.type || 'info');
            }
        } catch (e) {
            // ignore individual notification errors
            console.error('notify queue item failed', e);
        }
    }
}

// Wait for DOMContentLoaded then flush queue
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        _flushNotifyQueue();
    });
} else {
    // DOM already ready
    _flushNotifyQueue();
}