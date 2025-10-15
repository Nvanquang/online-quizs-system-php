<?php

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    protected $casts = [];
    protected $rules = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Mass-assign attributes using $fillable and $casts
     */
    public function fill(array $data): void
    {
        // Assign primary key if present
        if (array_key_exists($this->primaryKey, $data)) {
            $this->setAttribute($this->primaryKey, $data[$this->primaryKey]);
        }

        // Assign fillable fields only
        foreach ($this->fillable as $field) {
            if (array_key_exists($field, $data)) {
                $this->setAttribute($field, $data[$field]);
            }
        }
    }

    /**
     * Backwards compatible alias
     */
    public function fromArray(array $data): void
    {
        $this->fill($data);
    }

    /**
     * Build array using primary key + fillable properties
     */
    public function toArray(): array
    {
        $keys = array_values(array_unique(array_merge([$this->primaryKey], $this->fillable)));
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->getAttribute($key);
        }
        return $this->hideFields($result);
    }

    /**
     * Apply casting by field name
     */
    protected function applyCast(string $field, $value)
    {
        if ($value === null) {
            return null;
        }
        $type = $this->casts[$field] ?? null;
        switch ($type) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'float':
            case 'double':
                return (float)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'string':
                return (string)$value;
            case 'datetime':
                return $value;
            default:
                return $value;
        }
    }

    /**
     * Find record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $row = $this->db->fetch($sql, [$id]);
        if (!$row) {
            return null;
        }
        return $this->mapRowToModel($row);
    }

    /**
     * Find all records
     */
    public function findAll($conditions = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        $rows = $this->db->fetchAll($sql, $params);
        return array_map(function ($row) {
            return $this->mapRowToModel($row);
        }, $rows);
    }

    /**
     * Find one record by conditions
     */
    public function findOne($conditions = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        $sql .= " LIMIT 1";

        $row = $this->db->fetch($sql, $params);
        if (!$row) {
            return null;
        }
        return $this->mapRowToModel($row);
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        // Filter data by fillable fields TRƯỚC TẤT CẢ (để validate chính xác)
        $data = $this->filterFillable($data);

        // Validate (full create) dựa trên data đã filter
        $errors = $this->validate($data, false);
        if (!empty($errors)) {
            throw new InvalidArgumentException('Validation thẩt bại: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
        }

        
        $originalFillable = $this->fillable;  // Lưu tạm để check
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            if (in_array('created_at', $originalFillable)) {
                $data['created_at'] = $now;
            }
            if (in_array('updated_at', $originalFillable)) {
                $data['updated_at'] = $now;
            }
        }

        
        if (empty($data)) {
            throw new InvalidArgumentException('Không có dữ liệu');
        }

        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $values = array_values($data);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

        $this->db->query($sql, $values);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update record by ID
     */
    public function update($id, array $data)
    {
        // Filter data by fillable fields TRƯỚC
        $data = $this->filterFillable($data);

        // Validate (partial update) dựa trên data đã filter
        $errors = $this->validate($data, true);
        if (!empty($errors)) {
            throw new InvalidArgumentException('Validation thất bại: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
        }

        // Add updated timestamp AN TOÀN nếu có data để update
        if (!empty($data) && $this->timestamps && in_array('updated_at', $this->fillable)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        // Nếu không có field nào để update, skip
        if (empty($data)) {
            $sql = "UPDATE {$this->table} SET updated_at = updated_at WHERE {$this->primaryKey} = ?";  // Hoặc return early
            return $this->db->query($sql, [$id]);
        }

        $fields = [];
        $values = [];
        foreach ($data as $field => $value) {
            $fields[] = "{$field} = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = ?";

        return $this->db->query($sql, $values);
    }

    /**
     * Delete record by ID
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id]);
    }

    /**
     * Count records
     */
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }

    /**
     * Paginate records
     */
    public function paginate($page = 1, $perPage = 10, $conditions = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;

        // Get total count
        $total = $this->count($conditions);

        // Get records
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        $rows = $this->db->fetchAll($sql, $params);
        $data = array_map(function ($row) {
            return $this->mapRowToModel($row);
        }, $rows);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Execute raw query
     */
    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Execute raw query and fetch all
     */
    public function fetchAll($sql, $params = [])
    {
        $rows = $this->db->fetchAll($sql, $params);
        return array_map(function ($row) {
            return (object)$row;
        }, $rows);
    }

    /**
     * Execute raw query and fetch one
     */
    public function fetch($sql, $params = [])
    {
        $row = $this->db->fetch($sql, $params);
        return $row !== false && $row !== null ? (object)$row : null;
    }

    /**
     * Filter data by fillable fields
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Hide sensitive fields
     */
    protected function hideFields($data)
    {
        if (empty($this->hidden)) {
            return $data;
        }

        return array_diff_key($data, array_flip($this->hidden));
    }

    /**
     * Convert snake_case to StudlyCase (for setter/getter names)
     */
    protected function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));
    }

    /**
     * Set attribute using setter when available, otherwise via reflection
     */
    protected function setAttribute(string $field, $value): void
    {
        $casted = $this->applyCast($field, $value);
        $setter = 'set' . $this->studly($field);
        if (method_exists($this, $setter)) {
            $this->{$setter}($casted);
            return;
        }
        if (property_exists($this, $field)) {
            $ref = new ReflectionProperty($this, $field);
            $ref->setAccessible(true);
            $ref->setValue($this, $casted);
        }
    }

    /**
     * Get attribute using getter when available, otherwise via reflection
     */
    protected function getAttribute(string $field)
    {
        $getter = 'get' . $this->studly($field);
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }
        if (property_exists($this, $field)) {
            $ref = new ReflectionProperty($this, $field);
            $ref->setAccessible(true);
            return $ref->getValue($this);
        }
        return null;
    }

    /**
     * Convert a DB row (assoc array) to a model object of the current class
     */
    protected function mapRowToModel(array $row)
    {
        $className = get_class($this);
        $model = new $className();
        $model->fromArray($row);
        return $model;
    }

    /**
     * Validate data against $rules. If $partial is true, only validate provided fields.
     * Supported rules: required, string, int, integer, float, boolean, email, enum:VAL1,VAL2, min:N, max:N, nullable, regex:/pattern/
     * Returns array of errors [field => [messages...]]
     */
    public function validate(array $data, bool $partial = false): array
    {
        if (empty($this->rules)) {
            return [];
        }

        $errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = is_array($ruleString) ? $ruleString : explode('|', (string)$ruleString);

            $hasValue = array_key_exists($field, $data);
            $value = $hasValue ? $data[$field] : null;

            if ($partial && !$hasValue) {
                continue; // skip fields not present in partial update
            }

            $ruleMap = [];
            foreach ($rules as $r) {
                $parts = explode(':', $r, 2);
                $name = strtolower(trim($parts[0]));
                $param = $parts[1] ?? null;
                $ruleMap[$name] = $param;
            }

            // nullable
            if (($ruleMap['nullable'] ?? null) !== null && ($value === null || $value === '')) {
                continue;
            }

            // required
            if (($ruleMap['required'] ?? null) !== null && ($value === null || $value === '')) {
                $errors[$field][] = 'Trường bắt buộc';
                continue;
            }

            if ($value === null) {
                continue; // nothing else to validate
            }

            // type checks
            if (isset($ruleMap['string']) && !is_string($value)) {
                $errors[$field][] = 'Phải là chuỗi';
            }
            if ((isset($ruleMap['int']) || isset($ruleMap['integer'])) && filter_var($value, FILTER_VALIDATE_INT) === false) {
                $errors[$field][] = 'Phải là số nguyên';
            }
            if (isset($ruleMap['float']) && filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
                $errors[$field][] = 'Phải là số thực';
            }
            if (isset($ruleMap['boolean']) && !in_array($value, [0, 1, true, false, '0', '1'], true)) {
                $errors[$field][] = 'Phải là boolean';
            }
            if (isset($ruleMap['email']) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field][] = 'Email không hợp lệ';
            }
            if (isset($ruleMap['enum'])) {
                $allowed = array_map('trim', explode(',', (string)$ruleMap['enum']));
                if (!in_array($value, $allowed, true)) {
                    $errors[$field][] = 'Giá trị không hợp lệ';
                }
            }
            if (isset($ruleMap['min'])) {
                $min = (int)$ruleMap['min'];
                if (is_string($value) && strlen($value) < $min) {
                    $errors[$field][] = "Độ dài tối thiểu {$min}";
                }
            }
            if (isset($ruleMap['max'])) {
                $max = (int)$ruleMap['max'];
                if (is_string($value) && strlen($value) > $max) {
                    $errors[$field][] = "Độ dài tối đa {$max}";
                }
            }
            if (isset($ruleMap['regex'])) {
                $pattern = $ruleMap['regex'];
                if (@preg_match($pattern, '') === false || preg_match($pattern, (string)$value) !== 1) {
                    $errors[$field][] = 'Sai định dạng';
                }
            }
        }

        return $errors;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return $this->db->rollback();
    }
}
