<!-- Modal Update Question -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">
                    <i class="bi bi-pencil me-2"></i>Cập nhật câu hỏi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="questionForm" data-action="update" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="questionId" name="question_id" value="">

                    <!-- Content - Full Width -->
                    <div class="mb-3">
                        <label for="questionContent" class="form-label">
                            Câu hỏi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                            id="questionContent"
                            name="content"
                            rows="3"
                            placeholder="Nhập nội dung câu hỏi"
                            required></textarea>
                    </div>

                    <!-- Two Column Layout -->
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Answer A -->
                            <div class="mb-3">
                                <label for="answerA" class="form-label">
                                    Đáp án A <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="answerA"
                                    name="answer_a"
                                    placeholder="Nhập đáp án A"
                                    required>
                            </div>

                            <!-- Answer B -->
                            <div class="mb-3">
                                <label for="answerB" class="form-label">
                                    Đáp án B <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="answerB"
                                    name="answer_b"
                                    placeholder="Nhập đáp án B"
                                    required>
                            </div>

                            <!-- Correct Answer -->
                            <div class="mb-3">
                                <label for="correctAnswer" class="form-label">
                                    Đáp án đúng <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="correctAnswer" name="correct_answer" required>
                                    <option value="">-- Chọn đáp án đúng --</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Answer C -->
                            <div class="mb-3">
                                <label for="answerC" class="form-label">
                                    Đáp án C <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="answerC"
                                    name="answer_c"
                                    placeholder="Nhập đáp án C"
                                    required>
                            </div>

                            <!-- Answer D -->
                            <div class="mb-3">
                                <label for="answerD" class="form-label">
                                    Đáp án D <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="answerD"
                                    name="answer_d"
                                    placeholder="Nhập đáp án D"
                                    required>
                            </div>

                            <!-- Time Limit -->
                            <div class="mb-3">
                                <label for="timeLimit" class="form-label">
                                    Thời gian (giây) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control"
                                    id="timeLimit"
                                    name="time_limit"
                                    placeholder="Ví dụ: 30"
                                    min="5"
                                    max="300"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Explanation - Full Width -->
                    <div class="mb-3">
                        <label for="explanation" class="form-label">
                            Giải thích
                        </label>
                        <textarea class="form-control"
                            id="explanation"
                            name="explanation"
                            rows="3"
                            placeholder="Giải thích đáp án (có thể để trống)"></textarea>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="questionImage" class="form-label">
                            Hình ảnh
                        </label>
                        <input type="file"
                            class="form-control"
                            id="questionImage"
                            name="image"
                            accept="image/*">
                        <small class="text-muted">Chọn file ảnh (jpg, png, gif,...)</small>
                    </div>

                    <!-- Image Preview with Hover Delete Button -->
                    <div id="imagePreviewContainer" class="image-preview-wrapper" style="display: none;">
                        <div class="image-preview-box">
                            <img id="imagePreview" src="" alt="Preview">
                            <div class="image-overlay">
                                <button type="button" class="btn-remove-image" onclick="removeQuestionImage()">
                                    <i class="bi bi-trash3-fill"></i>
                                    <span>Xóa ảnh</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save me-1"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.6;
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

    /* Image Preview Styles */
    .image-preview-wrapper {
        margin-top: 15px;
    }

    .image-preview-box {
        position: relative;
        display: inline-block;
        max-width: 100%;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #dee2e6;
    }

    .image-preview-box img {
        display: block;
        max-width: 100%;
        max-height: 200px;
        object-fit: cover;
        transition: filter 0.3s ease;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-preview-box:hover .image-overlay {
        opacity: 1;
    }

    .image-preview-box:hover img {
        filter: blur(2px) brightness(0.7);
    }

    .btn-remove-image {
        background: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        transform: scale(0.9);
    }

    .image-preview-box:hover .btn-remove-image {
        transform: scale(1);
    }

    .btn-remove-image:hover {
        background: #bb2d3b;
        transform: scale(1.05);
    }

    .btn-remove-image i {
        font-size: 16px;
    }
</style>