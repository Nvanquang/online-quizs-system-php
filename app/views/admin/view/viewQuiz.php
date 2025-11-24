<!-- Modal View Quiz Details -->
<?php foreach ($quizzes as $quiz): ?>
<div class="modal fade modal-view" id="viewQuizModal_<?php echo $quiz->getId(); ?>" tabindex="-1" aria-labelledby="viewQuizModalLabel_<?php echo $quiz->getId(); ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewQuizModalLabel_<?php echo $quiz->getId(); ?>">
                    <i class="bi bi-eye me-2"></i>Chi tiết trò chơi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Quiz Details Table -->
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td class="fw-bold bg-light" width="40%">ID</td>
                            <td><?php echo $quiz->getId(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Chủ đề</td>
                            <td><?php echo htmlspecialchars($quiz->getTitle()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Mã Quiz</td>
                            <td><?php echo htmlspecialchars($quiz->getQuizCode()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Tác giả</td>
                            <td><?php echo htmlspecialchars($quiz->getAuthor()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Người tạo</td>
                            <td><?php echo htmlspecialchars($quiz->getCreatedBy()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Tổng số câu hỏi</td>
                            <td><?php echo $quiz->getTotalQuestions(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Đánh giá</td>
                            <td><?php echo round($quiz->getRatingSum() / $quiz->getRatingCount(), 2); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Ngày tạo</td>
                            <td><?php echo $quiz->getCreatedAt(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Cập nhật lúc</td>
                            <td><?php echo $quiz->getUpdatedAt(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Trạng thái</td>
                            <td>
                                <?php if ($quiz->isPublic()): ?>
                                    <span class="badge bg-success">Công khai</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Riêng tư</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Hình ảnh</td>
                            <td>
                                <?php if ($quiz->getImage()): ?>
                                    <img src="<?php echo '../../../../public/uploads/quizzes/'.htmlspecialchars($quiz->getImage()); ?>" 
                                         alt="Quiz Image" 
                                         class="img-fluid rounded"
                                         style="max-width: 100%; max-height: 300px;">
                                <?php else: ?>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-image"></i> Không có hình ảnh
                                    </p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<style>
    /* View Modal Styles - Right Side Drawer */
    .modal.modal-view .modal-dialog {
        position: fixed;
        right: 0;
        top: 0;
        margin: 0;
        height: 100vh;
        max-width: 40%;
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    }

    .modal.modal-view.show .modal-dialog {
        transform: translateX(0);
    }

    .modal.modal-view .modal-content {
        height: 100%;
        border-radius: 0;
        border: none;
        border-left: 1px solid #dee2e6;
    }

    .modal.modal-view .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 120px);
    }

    .modal.modal-view .table td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .modal.modal-view .table .bg-light {
        background-color: #f8f9fa !important;
    }

    /* Modal Backdrop */
    .modal.modal-view ~ .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.3);
    }
</style>