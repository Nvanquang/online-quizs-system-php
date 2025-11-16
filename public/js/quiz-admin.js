// Quiz Management Script
$(document).ready(function () {
    // Tooltip init
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Bind event cho các buttons
    document.addEventListener('click', function (e) {
        // Update button
        if (e.target.closest('.update-btn')) {
            const button = e.target.closest('.update-btn');
            const rawQuizData = button.dataset.quiz;
            openUpdateModal(rawQuizData);
        }

        // Delete button
        if (e.target.closest('.delete-btn')) {
            const button = e.target.closest('.delete-btn');
            const quizId = button.dataset.quizId;
            const quizTitle = button.dataset.quizTitle;
            confirmDelete(quizId, quizTitle);
        }
    });

    // Submit form handler
    const form = document.getElementById('quizForm');
    form.addEventListener('submit', handleFormSubmit);

    // Image preview handler
    const imageInput = document.getElementById('quizImage');
    imageInput.addEventListener('change', handleImagePreview);
});

// Modal elements
const modal = new bootstrap.Modal(document.getElementById('quizModal'));
const form = document.getElementById('quizForm');
const modalTitle = document.querySelector('#quizModal .modal-title');
const submitBtn = document.getElementById('submitBtn');

// Reset form
function resetForm() {
    form.reset();
    document.getElementById('quizId').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('imagePreview').src = '';
}

// Open Create Modal
function openCreateModal() {
    modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tạo mới trò chơi';
    form.dataset.action = 'create';
    resetForm();
    modal.show();
}

// Open Update Modal
function openUpdateModal(rawQuizData) {
    console.log('Raw quiz data from data-quiz:', rawQuizData);
    try {
        const quizData = JSON.parse(rawQuizData);
        console.log('Parsed quiz data:', quizData);

        modalTitle.innerHTML = '<i class="bi bi-pencil me-2"></i>Cập nhật trò chơi';
        form.dataset.action = 'update';

        document.getElementById('quizId').value = quizData.id || '';
        document.getElementById('quizTitle').value = quizData.title || '';
        document.getElementById('isPublic').checked = !!quizData.is_public;

        // Show existing image if available
        if (quizData.image) {
            document.getElementById('imagePreview').src = '../../../../public/uploads/quizzes/' + quizData.image;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        modal.show();
    } catch (error) {
        console.error('Lỗi parse JSON trong openUpdateModal:', error, 'Raw data:', rawQuizData);
        showToast('error', 'Lỗi dữ liệu', 'Không thể load thông tin quiz. Vui lòng refresh trang.');
    }
}

// Open View Modal
function openViewModal(rawQuizData) {
    console.log('Raw quiz data from data-quiz:', rawQuizData);

    try {
        const quiz = JSON.parse(rawQuizData);
        console.log('Parsed quiz data:', quiz);

        // Show modal immediately with data
        viewModal.show();

        // Populate data
        document.getElementById('view_title').textContent = quiz.title || '-';
        document.getElementById('view_quiz_code').textContent = quiz.quiz_code || '-';
        document.getElementById('view_author').textContent = quiz.author || '-';
        document.getElementById('view_created_by').textContent = quiz.created_by || '-';
        document.getElementById('view_total_questions').textContent = quiz.total_questions || '0';
        document.getElementById('view_rating').textContent = quiz.rating || '0';
        document.getElementById('view_created_at').textContent = quiz.created_at || '-';
        document.getElementById('view_updated_at').textContent = quiz.updated_at || '-';

        // Is Public
        const isPublicBadge = document.getElementById('view_is_public');
        if (quiz.is_public == 1 || quiz.is_public === true) {
            isPublicBadge.textContent = 'Công khai';
            isPublicBadge.className = 'badge-public';
        } else {
            isPublicBadge.textContent = 'Riêng tư';
            isPublicBadge.className = 'badge-private';
        }

        // Image
        const imageEl = document.getElementById('view_image');
        const noImageEl = document.getElementById('view_no_image');
        if (quiz.image) {
            imageEl.src = quiz.image;
            imageEl.style.display = 'block';
            noImageEl.style.display = 'none';
        } else {
            imageEl.style.display = 'none';
            noImageEl.style.display = 'block';
        }

    } catch (error) {
        console.error('Lỗi parse JSON trong openViewModal:', error, 'Raw data:', rawQuizData);
        showToast('error', 'Lỗi dữ liệu', 'Không thể load thông tin quiz. Vui lòng refresh trang.');
    }
}

// Handle Image Preview
function handleImagePreview(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Remove Image Preview
function removeImagePreview() {
    document.getElementById('quizImage').value = '';
    document.getElementById('imagePreview').src = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
}

// Handle Form Submit
async function handleFormSubmit(e) {
    e.preventDefault();

    const title = document.getElementById('quizTitle').value.trim();
    const isPublic = document.getElementById('isPublic').checked ? 1 : 0;
    const imageFile = document.getElementById('quizImage').files[0];
    const quizId = document.getElementById('quizId').value;
    const action = form.dataset.action;

    // Validation
    if (!title) {
        showToast('error', 'Validation Error', 'Vui lòng nhập chủ đề trò chơi!');
        return;
    }

    // Prepare FormData
    const fd = new FormData();
    fd.append('title', title);
    fd.append('is_public', isPublic);

    if (imageFile) {
        fd.append('image', imageFile);
    }

    if (action === 'update') {
        fd.append('quiz_id', quizId);
    }

    // Show loading
    submitBtn.classList.add('btn-loading');
    submitBtn.disabled = true;

    // Determine endpoint
    const url = action === 'create' ? '/quiz/create' : `/quiz/edit-quiz/${quizId}`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        $.ajax({
            url: url,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-Token': csrfToken },
            success: (res) => {
                toastr.options = { "timeout": 2000 }
                toastr.success(res.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            },
            error: (res) => {
                toastr.options = { "timeout": 2000 }
                toastr.error(res.message);
            }
        });
    } catch (error) {
        console.error('Submit Error:', error);
        toastr.options = { "timeout": 2000 }
        toastr.error("Lỗi kết nối đến server.");
    } finally {
        // Hide loading
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
    }
}

// Confirm Delete
function confirmDelete(quizId, quizTitle) {
    if (confirm(`Bạn có chắc chắn muốn xóa quiz "${quizTitle}"?`)) {
        deleteQuiz(quizId);
    }
}

// Delete Quiz
async function deleteQuiz(quizId) {
    try {
        const fd = new FormData();
        fd.append('quiz_id', quizId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $.ajax({
            url: `/quiz/delete/${quizId}`,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-Token': csrfToken },
            success: (res) => {
                toastr.options = { "timeout": 2000 }
                toastr.success(res.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            },
            error: (res) => {
                toastr.options = { "timeout": 2000 }
                toastr.error(res.message);
            }
        });
    } catch (error) {
        console.error('Delete Error:', error);
        toastr.options = { "timeout": 2000 }
        toastr.error('Lỗi kết nối đến server.');
    }
}
