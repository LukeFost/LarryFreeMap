<?php

class Unit {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO units (
            building_id, unit_number, bedrooms, bathrooms, 
            square_feet, rent_amount, is_available, available_from, features
        ) VALUES (
            :building_id, :unit_number, :bedrooms, :bathrooms,
            :square_feet, :rent_amount, :is_available, :available_from, :features
        ) RETURNING id";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':building_id' => $data['building_id'],
                ':unit_number' => $data['unit_number'],
                ':bedrooms' => $data['bedrooms'] ?? null,
                ':bathrooms' => $data['bathrooms'] ?? null,
                ':square_feet' => $data['square_feet'] ?? null,
                ':rent_amount' => $data['rent_amount'] ?? null,
                ':is_available' => isset($data['is_available']) ? $data['is_available'] : false,
                ':available_from' => $data['available_from'] ?? null,
                ':features' => isset($data['features']) ? json_encode($data['features']) : null
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'];
        } catch (PDOException $e) {
            throw new Exception("Error creating unit: " . $e->getMessage());
        }
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM units WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            if ($result && $result['features']) {
                $result['features'] = json_decode($result['features'], true);
            }
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error fetching unit: " . $e->getMessage());
        }
    }
    
    public function getAll() {
        $sql = "SELECT * FROM units ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['features']) {
                    $result['features'] = json_decode($result['features'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error fetching units: " . $e->getMessage());
        }
    }
    
    public function getByBuildingId($buildingId) {
        $sql = "SELECT * FROM units WHERE building_id = :building_id ORDER BY unit_number";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':building_id' => $buildingId]);
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['features']) {
                    $result['features'] = json_decode($result['features'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error fetching building units: " . $e->getMessage());
        }
    }
    
    public function getAvailableUnits() {
        $sql = "SELECT * FROM units WHERE is_available = true ORDER BY available_from, rent_amount";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['features']) {
                    $result['features'] = json_decode($result['features'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error fetching available units: " . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        $updateFields = [];
        $params = [':id' => $id];
        
        if (isset($data['building_id'])) {
            $updateFields[] = "building_id = :building_id";
            $params[':building_id'] = $data['building_id'];
        }
        if (isset($data['unit_number'])) {
            $updateFields[] = "unit_number = :unit_number";
            $params[':unit_number'] = $data['unit_number'];
        }
        if (isset($data['bedrooms'])) {
            $updateFields[] = "bedrooms = :bedrooms";
            $params[':bedrooms'] = $data['bedrooms'];
        }
        if (isset($data['bathrooms'])) {
            $updateFields[] = "bathrooms = :bathrooms";
            $params[':bathrooms'] = $data['bathrooms'];
        }
        if (isset($data['square_feet'])) {
            $updateFields[] = "square_feet = :square_feet";
            $params[':square_feet'] = $data['square_feet'];
        }
        if (isset($data['rent_amount'])) {
            $updateFields[] = "rent_amount = :rent_amount";
            $params[':rent_amount'] = $data['rent_amount'];
        }
        if (isset($data['is_available'])) {
            $updateFields[] = "is_available = :is_available";
            $params[':is_available'] = $data['is_available'];
        }
        if (isset($data['available_from'])) {
            $updateFields[] = "available_from = :available_from";
            $params[':available_from'] = $data['available_from'];
        }
        if (array_key_exists('features', $data)) {
            $updateFields[] = "features = :features";
            $params[':features'] = $data['features'] ? json_encode($data['features']) : null;
        }
        
        $updateFields[] = "updated_at = NOW()";
        
        $sql = "UPDATE units SET " . implode(", ", $updateFields) . " WHERE id = :id RETURNING id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute($params);
            return $stmt->fetch()['id'];
        } catch (PDOException $e) {
            throw new Exception("Error updating unit: " . $e->getMessage());
        }
    }
    
    public function delete($id) {
        $sql = "DELETE FROM units WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting unit: " . $e->getMessage());
        }
    }
    
    public function searchUnits($criteria) {
        $conditions = [];
        $params = [];
        
        if (isset($criteria['min_bedrooms'])) {
            $conditions[] = "bedrooms >= :min_bedrooms";
            $params[':min_bedrooms'] = $criteria['min_bedrooms'];
        }
        if (isset($criteria['max_bedrooms'])) {
            $conditions[] = "bedrooms <= :max_bedrooms";
            $params[':max_bedrooms'] = $criteria['max_bedrooms'];
        }
        if (isset($criteria['min_bathrooms'])) {
            $conditions[] = "bathrooms >= :min_bathrooms";
            $params[':min_bathrooms'] = $criteria['min_bathrooms'];
        }
        if (isset($criteria['max_rent'])) {
            $conditions[] = "rent_amount <= :max_rent";
            $params[':max_rent'] = $criteria['max_rent'];
        }
        if (isset($criteria['min_square_feet'])) {
            $conditions[] = "square_feet >= :min_square_feet";
            $params[':min_square_feet'] = $criteria['min_square_feet'];
        }
        if (isset($criteria['available_from'])) {
            $conditions[] = "available_from <= :available_from";
            $params[':available_from'] = $criteria['available_from'];
        }
        
        // Always include only available units in search
        $conditions[] = "is_available = true";
        
        $sql = "SELECT * FROM units";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY rent_amount, available_from";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['features']) {
                    $result['features'] = json_decode($result['features'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error searching units: " . $e->getMessage());
        }
    }
}
