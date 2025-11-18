<?php

// core/Validator.php
class Validator
{
    private $data;
    private $errors = [];
    private $validated = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate($rules)
    {
        foreach ($rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, $rule);
            }

            // Nếu field không có lỗi, thêm vào validated data
            if (!isset($this->errors[$field])) {
                $this->validated[$field] = $value;
            }
        }

        return empty($this->errors);
    }

    private function applyRule($field, $value, $rule)
    {
        // Parse rule và params
        if (strpos($rule, ':') !== false) {
            list($ruleName, $param) = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        switch ($ruleName) {
            case 'required':
                if ((is_null($value) || trim($value) === '') && $value !== '0') {
                    $this->addError($field, $this->getFieldLabel($field) . ' là bắt buộc!');
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $this->getFieldLabel($field) . ' không hợp lệ!');
                }
                break;

            case 'min':
                if (!empty($value) && mb_strlen($value) < $param) {
                    $this->addError($field, $this->getFieldLabel($field) . " phải có ít nhất {$param} ký tự!");
                }
                break;

            case 'max':
                if (!empty($value) && mb_strlen($value) > $param) {
                    $this->addError($field, $this->getFieldLabel($field) . " không được vượt quá {$param} ký tự!");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, $this->getFieldLabel($field) . ' phải là số!');
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, $this->getFieldLabel($field) . ' phải là số nguyên!');
                }
                break;

            case 'positive':
                if (!empty($value) && is_numeric($value) && $value <= 0) {
                    $this->addError($field, $this->getFieldLabel($field) . ' phải là số dương lớn hơn 0!');
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, $this->getFieldLabel($field) . ' không phải URL hợp lệ!');
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                $confirmValue = $this->data[$confirmField] ?? null;
                if ($value !== $confirmValue) {
                    $this->addError($field, $this->getFieldLabel($field) . ' không khớp!');
                }
                break;

            case 'in':
                $allowed = explode(',', $param);
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->addError($field, $this->getFieldLabel($field) . ' không hợp lệ!');
                }
                break;
                
            case 'enum':
                $allowed = explode(',', $param);
                if (!in_array($value, $allowed, true)) {
                    $this->addError($field, $this->getFieldLabel($field) . ' phải là một trong: ' . implode(', ', $allowed));
                }
                break;
        }
    }


    private function getFieldLabel($field)
    {
        $labels = [
            // Bảng users (Class User)
            'username' => 'Tên đăng nhập',
            'full_name' => 'Họ và tên',
            'avatar_url' => 'URL ảnh đại diện',
            'is_admin' => 'Là admin',
            'total_points' => 'Tổng điểm tích lũy',
            'games_played' => 'Số game đã chơi',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',

            // Bảng questions (Class Question)
            'content' => 'Nội dung',
            'answer_a' => 'Đáp án A',
            'answer_b' => 'Đáp án B',
            'answer_c' => 'Đáp án C',
            'answer_d' => 'Đáp án D',
            'correct_answer' => 'Đáp án đúng',
            'explanation' => 'Giải thích',
            'image_url' => 'URL hình ảnh',
            'time_limit' => 'Giới hạn thời gian',
            'created_by' => 'Tạo bởi',
            'updated_by' => 'Cập nhật bởi',

            // Bảng quizzes (Class Quiz)
            'title' => 'Tiêu đề',
            'quiz_code' => 'Mã quiz',
            'author' => 'Tác giả',
            'image' => 'Hình ảnh',
            'is_public' => 'Công khai',
            'total_questions' => 'Tổng câu hỏi',
            'rating' => 'Đánh giá',

            // Bảng quiz_questions (Class QuizQuestion - liên kết)
            'order_number' => 'Thứ tự',

            // Bảng game_sessions (Class GameSession)
            'session_code' => 'Mã phiên',
            'pin_code' => 'Mã PIN',
            'host_id' => 'ID host',
            'actual_mode' => 'Chế độ thực tế',
            'status' => 'Trạng thái',
            'current_question' => 'Câu hỏi hiện tại',
            'total_players' => 'Tổng người chơi',
            'started_at' => 'Bắt đầu lúc',
            'ended_at' => 'Kết thúc lúc',

            // Bảng session_players (Class SessionPlayer)
            'nickname' => 'Biệt danh',
            'avatar' => 'Avatar',
            'total_score' => 'Tổng điểm',
            'rank_position' => 'Vị trí xếp hạng',
            'is_ready' => 'Sẵn sàng',
            'joined_at' => 'Tham gia lúc',

            // Bảng game_history (Class GameHistory)
            'final_score' => 'Điểm cuối',
            'final_rank' => 'Xếp hạng cuối',
            'correct_answers' => 'Số câu đúng',
            'avg_response_time' => 'Thời gian phản hồi trung bình',
            'played_at' => 'Chơi lúc',

            // Các trường chung (id, khóa ngoại, v.v.)
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'name' => 'Tên',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'user_id' => 'ID người dùng',
            'quiz_id' => 'ID quiz',
            'question_id' => 'ID câu hỏi',
            'session_id' => 'ID phiên',
        ];

        return $labels[$field] ?? ucfirst($field);
    }

    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function validated()
    {
        return $this->validated;
    }

    public function firstError($field = null)
    {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }

        // Trả về lỗi đầu tiên
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }

        return null;
    }
}
