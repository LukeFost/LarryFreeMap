<?php

class Provider {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO providers (name, email, phone) VALUES (:name, :email, :phone) RETURNING id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'] ?? null
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'];
        } catch (PDOException $e) {
            throw new Exception("Error creating provider: " . $e->getMessage());
        }
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM providers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error fetching provider: " . $e->getMessage());
        }
    }
    
    public function getAll() {
        $sql = "SELECT * FROM providers ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error fetching providers: " . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        $updateFields = [];
        $params = [':id' => $id];
        
        if (isset($data['name'])) {
            $updateFields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $updateFields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        if (isset($data['phone'])) {
            $updateFields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }
        
        $updateFields[] = "updated_at = NOW()";
        
        $sql = "UPDATE providers SET " . implode(", ", $updateFields) . " WHERE id = :id RETURNING id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute($params);
            return $stmt->fetch()['id'];
        } catch (PDOException $e) {
            throw new Exception("Error updating provider: " . $e->getMessage());
        }
    }
    
    public function delete($id) {
        $sql = "DELETE FROM providers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting provider: " . $e->getMessage());
        }
    }
}
