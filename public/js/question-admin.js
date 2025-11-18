// Question Management Script
$(document).ready(function () {
    // Tooltip init
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Bind event cho các buttons
    document.addEventListener('click', function (e) {
        // Update button
        if (e.target.closest('.update-btn')) {
            const button = e.target.closest('.update-btn');
            const rawQuestionData = button.dataset.question;
            openUpdateModal(rawQuestionData);
        }

        // Delete button
        if (e.target.closest('.delete-btn')) {
            const button = e.target.closest('.delete-btn');
            const questionId = button.dataset.questionId;
            const questionContent = button.dataset.questionContent;
            confirmDelete(questionId, questionContent);
        }
    });

    // Submit form handler
    const form = document.getElementById('questionForm');
    form.addEventListener('submit', handleFormSubmit);

    // Image file preview handler
    const imageFileInput = document.getElementById('questionImage');
    imageFileInput.addEventListener('change', handleImageFilePreview);
});

// Modal elements
const modal = new bootstrap.Modal(document.getElementById('questionModal'));
const form = document.getElementById('questionForm');
const modalTitle = document.querySelector('#questionModal .modal-title');
const submitBtn = document.getElementById('submitBtn');

// Reset form
function resetForm() {
    form.reset();
    document.getElementById('questionId').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('imagePreview').src = '';
}

// Open Create Modal
function openCreateModal() {
    modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tạo mới câu hỏi';
    form.dataset.action = 'create';
    resetForm();
    modal.show();
}

// Open Update Modal
function openUpdateModal(rawQuestionData) {
    console.log('Raw question data from data-question:', rawQuestionData);
    try {
        const questionData = JSON.parse(rawQuestionData);
        console.log('Parsed question data:', questionData);

        modalTitle.innerHTML = '<i class="bi bi-pencil me-2"></i>Cập nhật câu hỏi';
        form.dataset.action = 'update';

        document.getElementById('questionId').value = questionData.id || '';
        document.getElementById('questionContent').value = questionData.content || '';
        document.getElementById('answerA').value = questionData.answer_a || '';
        document.getElementById('answerB').value = questionData.answer_b || '';
        document.getElementById('answerC').value = questionData.answer_c || '';
        document.getElementById('answerD').value = questionData.answer_d || '';
        document.getElementById('correctAnswer').value = questionData.correct_answer || '';
        document.getElementById('explanation').value = questionData.explanation || '';
        document.getElementById('timeLimit').value = questionData.time_limit || '';

        // Show existing image if available
        if (questionData.image_url) {
            document.getElementById('imagePreview').src = '../../../../public/uploads/questions/' + questionData.image_url;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        modal.show();
    } catch (error) {
        console.error('Lỗi parse JSON trong openUpdateModal:', error, 'Raw data:', rawQuestionData);
        showToast('error', 'Lỗi dữ liệu', 'Không thể load thông tin câu hỏi. Vui lòng refresh trang.');
    }
}

// Handle Image File Preview
function handleImageFilePreview(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showToast('error', 'Lỗi', 'Vui lòng chọn file ảnh hợp lệ!');
            e.target.value = '';
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showToast('error', 'Lỗi', 'Kích thước ảnh không được vượt quá 5MB!');
            e.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Remove Question Image
function removeQuestionImage() {
    document.getElementById('questionImage').value = '';
    document.getElementById('imagePreview').src = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
}

// Handle Form Submit
async function handleFormSubmit(e) {
    e.preventDefault();

    const content = document.getElementById('questionContent').value.trim();
    const answerA = document.getElementById('answerA').value.trim();
    const answerB = document.getElementById('answerB').value.trim();
    const answerC = document.getElementById('answerC').value.trim();
    const answerD = document.getElementById('answerD').value.trim();
    const correctAnswer = document.getElementById('correctAnswer').value;
    const explanation = document.getElementById('explanation').value.trim();
    const imageFile = document.getElementById('questionImage').files[0];
    const timeLimit = document.getElementById('timeLimit').value;
    const questionId = document.getElementById('questionId').value;
    const action = form.dataset.action;

    // Validation
    if (!content || !answerA || !answerB || !answerC || !answerD || !correctAnswer || !timeLimit) {
        showToast('error', 'Validation Error', 'Vui lòng điền đầy đủ các trường bắt buộc!');
        return;
    }

    if (timeLimit < 5 || timeLimit > 300) {
        showToast('error', 'Validation Error', 'Thời gian phải từ 5-300 giây!');
        return;
    }

    // Prepare FormData
    const fd = new FormData();
    fd.append('content', content);
    fd.append('answer_a', answerA);
    fd.append('answer_b', answerB);
    fd.append('answer_c', answerC);
    fd.append('answer_d', answerD);
    fd.append('correct_answer', correctAnswer);
    fd.append('explanation', explanation);
    fd.append('time_limit', timeLimit);

    // Add image file if selected
    if (imageFile) {
        fd.append('image', imageFile);
    }

    if (action === 'update') {
        fd.append('question_id', questionId);
    }

    // Show loading
    submitBtn.classList.add('btn-loading');
    submitBtn.disabled = true;

    // Determine endpoint
    const url = action === 'create' ? '/question/create' : `/question/edit/${questionId}`;
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
                modal.hide();
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
function confirmDelete(questionId, questionContent) {
    if (confirm(`Bạn có chắc chắn muốn xóa câu hỏi "${questionContent}"?`)) {
        deleteQuestion(questionId);
    }
}

// Delete Question
async function deleteQuestion(questionId) {
    try {
        const fd = new FormData();
        fd.append('question_id', questionId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $.ajax({
            url: `/question/delete/${questionId}`,
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
        toastr.error("Lỗi kết nối đến server.");
    }
}
