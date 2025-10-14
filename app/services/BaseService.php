<?php

/**
 * BaseService - Abstract base class for all services
 * 
 * Services contain business logic and coordinate between repositories and controllers
 */
abstract class BaseService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = $this->getRepositoryInstance();
    }

    /**
     * Child classes must return a new Repository instance
     */
    abstract protected function getRepositoryInstance();

    /**
     * Find entity by ID
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Find one entity by conditions
     */
    public function findOneBy(array $conditions = [])
    {
        return $this->repository->findOneBy($conditions);
    }

    /**
     * Find all entities by conditions
     */
    public function findAll(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->repository->findAll($conditions, $orderBy, $limit);
    }

    /**
     * Find entities by conditions (alias for findAll)
     */
    public function findBy(array $conditions = [], $orderBy = null, $limit = null)
    {
        return $this->repository->findBy($conditions, $orderBy, $limit);
    }

    /**
     * Check if entity exists
     */
    public function exists(array $conditions = []): bool
    {
        return $this->repository->exists($conditions);
    }

    /**
     * Count entities by conditions
     */
    public function countBy(array $conditions = []): int
    {
        return $this->repository->countBy($conditions);
    }

    /**
     * Paginate entities
     */
    public function paginate(int $page = 1, int $perPage = 10, array $conditions = [], $orderBy = null)
    {
        return $this->repository->paginate($page, $perPage, $conditions, $orderBy);
    }

    /**
     * Create new entity
     */
    public function create(array $data)
    {
        // Validate data before creating
        $this->validateCreateData($data);
        
        // Pre-process data if needed
        $data = $this->preprocessCreateData($data);
        
        return $this->repository->create($data);
    }

    /**
     * Update entity
     */
    public function update($id, array $data)
    {
        // Validate data before updating
        $this->validateUpdateData($id, $data);
        
        // Pre-process data if needed
        $data = $this->preprocessUpdateData($id, $data);
        
        return $this->repository->update($id, $data);
    }

    /**
     * Delete entity
     */
    public function delete($id)
    {
        // Validate before deleting
        $this->validateDelete($id);
        
        return $this->repository->delete($id);
    }

    /**
     * Execute custom query
     */
    public function query(string $sql, array $params = [])
    {
        return $this->repository->query($sql, $params);
    }

    /**
     * Fetch single row
     */
    public function fetch(string $sql, array $params = [])
    {
        return $this->repository->fetch($sql, $params);
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = [])
    {
        return $this->repository->fetchAll($sql, $params);
    }

    /**
     * Validate data before creating (override in child classes)
     */
    protected function validateCreateData(array $data): void
    {
        // Override in child classes for specific validation
    }

    /**
     * Validate data before updating (override in child classes)
     */
    protected function validateUpdateData($id, array $data): void
    {
        // Override in child classes for specific validation
    }

    /**
     * Validate before deleting (override in child classes)
     */
    protected function validateDelete($id): void
    {
        // Override in child classes for specific validation
    }

    /**
     * Pre-process data before creating (override in child classes)
     */
    protected function preprocessCreateData(array $data): array
    {
        // Override in child classes for specific preprocessing
        return $data;
    }

    /**
     * Pre-process data before updating (override in child classes)
     */
    protected function preprocessUpdateData($id, array $data): array
    {
        // Override in child classes for specific preprocessing
        return $data;
    }

    /**
     * Get repository instance (for direct access if needed)
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
