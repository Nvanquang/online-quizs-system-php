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
     * Kiểm tra record có tồn tại theo conditions
     */
    public function exists(array $conditions = []): bool
    {
        if (empty($conditions)) {
            throw new InvalidArgumentException("conditions không được rỗng");
        }

        $sql = "SELECT 1 FROM {$this->table}";
        $params = [];

        $where = [];
        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = ?";
            $params[] = $value;
        }

        $sql .= " WHERE " . implode(' AND ', $where) . " LIMIT 1";

        $result = $this->db->fetch($sql, $params);
        return $result !== false && $result !== null;
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
    public function create(array $data): object
    {
        // Lọc dữ liệu theo fillable
        $data = $this->filterFillable($data);

        if (empty($data)) {
            throw new InvalidArgumentException('Không có dữ liệu');
        }

        // Build SQL insert
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $values = array_values($data);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $this->db->query($sql, $values);

        $primaryKey = $this->primaryKey;
        if ($primaryKey !== null) {
            $id = $this->db->lastInsertId();
            if (!empty($id)) {
                $sqlSelect = "SELECT * FROM {$this->table} WHERE {$primaryKey} = ?";
                $record = $this->db->query($sqlSelect, [$id])->fetch(PDO::FETCH_ASSOC);
                if ($record) {
                    return $this->mapRowToModel($record);
                }
            }
        }

        $found = $this->findOne($data);
        if ($found) {
            return $found;
        }

        $className = get_class($this);
        $model = new $className();
        $model->fromArray($data);
        return $model;
    }


    /**
     * Update record by ID
     */
    public function update($id, array $data)
    {
        // Filter data by fillable fields TRƯỚC
        $data = $this->filterFillable($data);

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
     * Paginate records with LIKE search
     */
    public function paginateWithSearch($searchField, $keyword, $page = 1, $perPage = 10, $extraConditions = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = [];

        // Search condition
        if ($searchField && $keyword !== '') {
            $where[] = "{$searchField} LIKE ?";
            $params[] = '%' . $keyword . '%';
        }

        // Extra conditions
        if (!empty($extraConditions)) {
            foreach ($extraConditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
            }
        }

        $whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';

        // Count total
        $countSql = "SELECT COUNT(*) as count FROM {$this->table}{$whereSql}";
        $total = $this->db->fetch($countSql, $params)['count'];

        // Get records
        $sql = "SELECT * FROM {$this->table}{$whereSql}";
        if ($orderBy) {
            if (is_array($orderBy)) {
                // Handle $orderBy as associative array like ['column' => 'ASC']
                $orderParts = [];
                foreach ($orderBy as $column => $direction) {
                    $orderParts[] = "{$column} {$direction}";
                }
                $sql .= " ORDER BY " . implode(', ', $orderParts);
            } else {
                $sql .= " ORDER BY {$orderBy}";
            }
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
}
