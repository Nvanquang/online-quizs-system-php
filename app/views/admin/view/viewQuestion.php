<!-- Modal View Question Details -->
<?php foreach ($questions as $question): ?>
<div class="modal fade modal-view" id="viewQuestionModal_<?php echo $question->getId(); ?>" tabindex="-1" aria-labelledby="viewQuestionModalLabel_<?php echo $question->getId(); ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewQuestionModalLabel_<?php echo $question->getId(); ?>">
                    <i class="bi bi-eye me-2"></i>Chi tiết câu hỏi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Question Details Table -->
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td class="fw-bold bg-light" width="40%">ID</td>
                            <td><?php echo $question->getId(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Câu hỏi</td>
                            <td><?php echo htmlspecialchars($question->getContent()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Đáp án A</td>
                            <td><?php echo htmlspecialchars($question->getAnswerA()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Đáp án B</td>
                            <td><?php echo htmlspecialchars($question->getAnswerB()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Đáp án C</td>
                            <td><?php echo htmlspecialchars($question->getAnswerC()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Đáp án D</td>
                            <td><?php echo htmlspecialchars($question->getAnswerD()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Đáp án đúng</td>
                            <td>
                                <span class="badge bg-success fs-6">
                                    <?php echo htmlspecialchars($question->getCorrectAnswer()); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Giải thích</td>
                            <td>
                                <?php if ($question->getExplanation()): ?>
                                    <?php echo htmlspecialchars($question->getExplanation()); ?>
                                <?php else: ?>
                                    <span class="text-muted">Không có giải thích</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Thời gian</td>
                            <td><?php echo $question->getTimeLimit(); ?> giây</td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Người tạo</td>
                            <td><?php echo htmlspecialchars($question->getCreatedBy()); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Ngày tạo</td>
                            <td><?php echo $question->getCreatedAt(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Cập nhật lúc</td>
                            <td><?php echo $question->getUpdatedAt(); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Hình ảnh</td>
                            <td>
                                <?php if ($question->getImageUrl()): ?>
                                    <img src="<?php echo '../../../../public/uploads/questions/'.htmlspecialchars($question->getImageUrl()); ?>" 
                                         alt="Question Image" 
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