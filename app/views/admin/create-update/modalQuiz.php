<!-- Modal Create/Update Quiz -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quizModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tạo mới trò chơi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quizForm" data-action="create" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="quizId" name="quiz_id" value="">
                    
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="quizTitle" class="form-label">
                            Chủ đề <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="quizTitle" 
                               name="title" 
                               placeholder="Nhập chủ đề trò chơi" 
                               required>
                    </div>

                    <!-- Is Public -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="isPublic" 
                                   name="is_public" 
                                   checked>
                            <label class="form-check-label" for="isPublic">
                                Công khai (Public)
                            </label>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="quizImage" class="form-label">
                            Hình ảnh
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="quizImage" 
                               name="image" 
                               accept="image/*">
                        <small class="text-muted">Chọn file ảnh (jpg, png, gif,...)</small>
                        
                        <!-- Image Preview -->
                        <div id="imagePreviewContainer" style="display: none; margin-top: 10px;">
                            <img id="imagePreview" 
                                 src="" 
                                 alt="Preview" 
                                 style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                            <button type="button" 
                                    class="btn btn-sm btn-danger mt-2" 
                                    onclick="removeImagePreview()">
                                <i class="bi bi-x-circle me-1"></i>Xóa ảnh
                            </button>
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
</style>