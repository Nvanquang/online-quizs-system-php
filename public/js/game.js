// Copy PIN
function copyPin() {
    const el = document.getElementById('pinNumber') || document.querySelector('.modal-pin-number');
    const pin = el ? (el.textContent || '').trim() : '';
    if (!pin) return;
    navigator.clipboard.writeText(pin);
    alert('PIN đã được sao chép!');
}

// Toggle PIN + QR visibility and show info message
let pinHidden = false;
function togglePin() {
    const hideText = document.getElementById('hideText');
    const pinBox = document.querySelector('.pin-box');
    const pinNumber = document.querySelector('.pin-box .pin-number');
    const pinActions = document.querySelector('.pin-box .pin-actions');
    const hiddenInfo = document.getElementById('hiddenInfo');
    const qrCode = document.querySelector('.qr-box .qr-code');
    const qrPlaceholder = document.querySelector('.qr-box .qr-placeholder');

    pinHidden = !pinHidden;

    if (pinHidden) {
        if (pinNumber) pinNumber.style.display = 'none';
        if (pinActions) pinActions.style.display = 'none';
        if (hiddenInfo) hiddenInfo.style.display = 'flex';
        if (qrCode) qrCode.style.display = 'none';
        if (qrPlaceholder) qrPlaceholder.style.display = 'block';
        if (hideText) hideText.textContent = 'Show';
    } else {
        if (pinNumber) pinNumber.style.display = '';
        if (pinActions) pinActions.style.display = '';
        if (hiddenInfo) hiddenInfo.style.display = 'none';
        if (qrCode) qrCode.style.display = '';
        if (qrPlaceholder) qrPlaceholder.style.display = 'none';
        if (hideText) hideText.textContent = 'Hide';
    }
}

// Start game - Now shows modal
function startGame() {
    document.getElementById('startModal').style.display = 'flex';
}

// Close modal on outside click
document.getElementById('startModal').addEventListener('click', function (e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});

// Fullscreen
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

// Checkbox functionality
document.querySelectorAll('.checkbox-option input').forEach(input => {
    input.addEventListener('change', function () {
        const icon = this.parentElement.querySelector('.checkbox-box i');
        icon.style.display = this.checked ? 'block' : 'none';
    });
});

// Slider functionality
document.querySelectorAll('.slider-handle').forEach(handle => {
    let isDragging = false;
    const container = handle.parentElement;
    const bar = container.querySelector('.slider-bar');

    handle.addEventListener('mousedown', (e) => {
        isDragging = true;
        e.preventDefault();
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;

        const rect = container.getBoundingClientRect();
        let percentage = ((e.clientX - rect.left) / rect.width) * 100;
        percentage = Math.max(0, Math.min(100, percentage));

        handle.style.left = percentage + '%';
        bar.style.width = percentage + '%';
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
    });

    // Touch support
    handle.addEventListener('touchstart', (e) => {
        isDragging = true;
    });

    document.addEventListener('touchmove', (e) => {
        if (!isDragging) return;

        const touch = e.touches[0];
        const rect = container.getBoundingClientRect();
        let percentage = ((touch.clientX - rect.left) / rect.width) * 100;
        percentage = Math.max(0, Math.min(100, percentage));

        handle.style.left = percentage + '%';
        bar.style.width = percentage + '%';
    });

    document.addEventListener('touchend', () => {
        isDragging = false;
    });
});

// Clear nickname
const btnClearNickname = document.getElementById('btn-clear-nickname');
const nicknameInput = document.getElementById('nickname');
btnClearNickname.addEventListener('click', () => {
    nicknameInput.value = '';
});

// Avatar upload & preview
const avatarCircle = document.getElementById('avatarCircle');
const avatarUpload = document.getElementById('avatarUpload');
const avatarPreview = document.getElementById('avatarPreview');

if (avatarCircle && avatarUpload && avatarPreview) {
    avatarCircle.style.cursor = 'pointer';
    avatarCircle.addEventListener('click', () => {
        avatarUpload.click();
    });

    avatarUpload.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            avatarPreview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
}

// Random avatar logic
function ensureAvatarImg() {
    let img = document.getElementById('avatarPreview');
    if (!img && avatarCircle) {
        img = document.createElement('img');
        img.id = 'avatarPreview';
        avatarCircle.appendChild(img);
    }
    return img;
}

function pickRandomAvatar() {
    const list = Array.isArray(window.AVATAR_LIST) ? window.AVATAR_LIST : [];
    if (!list.length) return null;
    const idx = Math.floor(Math.random() * list.length);
    return (window.AVATAR_BASE_URL || '') + list[idx];
}

function setRandomAvatar() {
    const src = pickRandomAvatar();
    if (!src) return;
    const img = ensureAvatarImg();
    if (!img) return;
    img.src = src;
}

// Bind randomization: if there is no upload input, clicking circle randomizes
if (avatarCircle && !avatarUpload) {
    avatarCircle.style.cursor = 'pointer';
    avatarCircle.addEventListener('click', setRandomAvatar);
}

// Handle Shuffle clicks via event delegation so reset buttons work automatically
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-shuffle-avatar');
    if (!btn) return;
    e.preventDefault();
    setRandomAvatar();
    // Only convert the TOP Shuffle button (has .modal-btn-shuffle).
    // Keep the BOTTOM Shuffle button (has .modal-bottom-btn) unchanged.
    if (btn.classList.contains('modal-btn-shuffle')) {
        try {
            btn.classList.remove('btn-shuffle-avatar', 'modal-btn-shuffle');
            btn.classList.add('modal-btn-done');
            btn.textContent = 'Done';
            // Arm Done after this click cycle to avoid immediate submit
            btn.setAttribute('data-done-armed', '0');
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            setTimeout(() => {
                try { btn.setAttribute('data-done-armed', '1'); } catch (_) {}
            }, 0);
            return;
        } catch (_) { }
    }
});

// Handle Done: submit join info
document.addEventListener('click', async (e) => {
    const doneBtn = e.target.closest('.modal-btn-done');
    if (!doneBtn) return;
    e.preventDefault();
    // Require arming flag to ensure this is a subsequent click
    if (doneBtn.getAttribute('data-done-armed') !== '1') return;
    doneBtn.setAttribute('data-done-armed', '0');
    const nicknameEl = document.getElementById('nickname');
    const avatarEl = document.getElementById('avatarPreview');
    const nickname = nicknameEl ? nicknameEl.value.trim() : '';
    const avatar = avatarEl ? (avatarEl.getAttribute('src') || '') : '';

    const form = document.getElementById('joinForm');
    const joinNickname = document.getElementById('joinNickname');
    const joinAvatar = document.getElementById('joinAvatar');
    if (joinNickname) joinNickname.value = nickname;
    if (joinAvatar) joinAvatar.value = avatar;
    if (form) {
        form.submit();
    }
});

// Reset button shuffle
function resetButtonShuffle() {
    // Normalize Shuffle buttons:
    // - TOP button: has no .modal-bottom-btn, should regain .modal-btn-shuffle
    // - BOTTOM button: has .modal-bottom-btn, should NOT have .modal-btn-shuffle
    document.querySelectorAll('.modal-btn-done, .btn-shuffle-avatar, .modal-bottom-btn').forEach(btn => {
        try {
            // common resets
            btn.classList.remove('modal-btn-done');
            if (!btn.classList.contains('btn-shuffle-avatar')) btn.classList.add('btn-shuffle-avatar');

            if (btn.classList.contains('modal-bottom-btn')) {
                // Bottom: ensure it never gets modal-btn-shuffle so it never turns to Done
                btn.classList.remove('modal-btn-shuffle');
                btn.innerHTML = '<span><i class="bi bi-shuffle"></i></span>';
            } else {
                // Top: ensure it has modal-btn-shuffle so it can turn to Done on click
                if (!btn.classList.contains('modal-btn-shuffle')) btn.classList.add('modal-btn-shuffle');
                // Restore text label, not icon
                btn.textContent = 'Shuffle';
            }
        } catch (_) { }
    });
}

// Clear avatar button: remove current avatar image
document.querySelectorAll('.btn-clear-avatar').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const img = document.getElementById('avatarPreview');
        if (img && img.parentElement) {
            img.parentElement.removeChild(img);
        }
        // reset button
        resetButtonShuffle();
    });
});

// Waiting dots animation (".", "..", "...")
const waitingDots = document.getElementById('waitingDots');
if (waitingDots) {
    let i = 0;
    setInterval(() => {
        const count = (i % 4) + 1;
        waitingDots.textContent = '.'.repeat(count);
        i++;
    }, 500);
}
