<?php

class Building {
    private $db;
    
    public function __construct($database = null) {
        $this->db = $database ? $database->getConnection() : Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO buildings (
            provider_id, name, address, location, details
        ) VALUES (
            :provider_id, :name, :address, ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326), :details
        ) RETURNING id";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':provider_id' => $data['provider_id'],
                ':name' => $data['name'],
                ':address' => $data['address'],
                ':longitude' => $data['longitude'],
                ':latitude' => $data['latitude'],
                ':details' => isset($data['details']) ? json_encode($data['details']) : null
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'];
        } catch (PDOException $e) {
            throw new Exception("Error creating building: " . $e->getMessage());
        }
    }
    
    public function getById($id) {
        $sql = "SELECT 
                id, provider_id, name, address,
                ST_X(location::geometry) as longitude,
                ST_Y(location::geometry) as latitude,
                details, created_at, updated_at
            FROM buildings 
            WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            if ($result && $result['details']) {
                $result['details'] = json_decode($result['details'], true);
            }
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error fetching building: " . $e->getMessage());
        }
    }
    
    public function getAll() {
        $sql = "SELECT 
                id, provider_id, name, address,
                ST_X(location::geometry) as longitude,
                ST_Y(location::geometry) as latitude,
                details, created_at, updated_at
            FROM buildings 
            ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['details']) {
                    $result['details'] = json_decode($result['details'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error fetching buildings: " . $e->getMessage());
        }
    }
    
    public function getByProviderId($providerId) {
        $sql = "SELECT 
                id, provider_id, name, address,
                ST_X(location::geometry) as longitude,
                ST_Y(location::geometry) as latitude,
                details, created_at, updated_at
            FROM buildings 
            WHERE provider_id = :provider_id
            ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':provider_id' => $providerId]);
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['details']) {
                    $result['details'] = json_decode($result['details'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error fetching provider buildings: " . $e->getMessage());
        }
    }
    
    public function update($id, $data) {
        $updateFields = [];
        $params = [':id' => $id];
        
        if (isset($data['provider_id'])) {
            $updateFields[] = "provider_id = :provider_id";
            $params[':provider_id'] = $data['provider_id'];
        }
        if (isset($data['name'])) {
            $updateFields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (isset($data['address'])) {
            $updateFields[] = "address = :address";
            $params[':address'] = $data['address'];
        }
        if (isset($data['longitude']) && isset($data['latitude'])) {
            $updateFields[] = "location = ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326)";
            $params[':longitude'] = $data['longitude'];
            $params[':latitude'] = $data['latitude'];
        }
        if (array_key_exists('details', $data)) {
            $updateFields[] = "details = :details";
            $params[':details'] = $data['details'] ? json_encode($data['details']) : null;
        }
        
        $updateFields[] = "updated_at = NOW()";
        
        $sql = "UPDATE buildings SET " . implode(", ", $updateFields) . " WHERE id = :id RETURNING id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute($params);
            return $stmt->fetch()['id'];
        } catch (PDOException $e) {
            throw new Exception("Error updating building: " . $e->getMessage());
        }
    }
    
    public function delete($id) {
        $sql = "DELETE FROM buildings WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting building: " . $e->getMessage());
        }
    }
    
    public function findNearby($latitude, $longitude, $radiusInMeters = 1000) {
        $sql = "SELECT 
                id, provider_id, name, address,
                ST_X(location::geometry) as longitude,
                ST_Y(location::geometry) as latitude,
                details,
                ST_Distance(
                    location::geography,
                    ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326)::geography
                ) as distance
            FROM buildings
            WHERE ST_DWithin(
                location::geography,
                ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326)::geography,
                :radius
            )
            ORDER BY distance";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([
                ':latitude' => $latitude,
                ':longitude' => $longitude,
                ':radius' => $radiusInMeters
            ]);
            
            $results = $stmt->fetchAll();
            foreach ($results as &$result) {
                if ($result['details']) {
                    $result['details'] = json_decode($result['details'], true);
                }
            }
            return $results;
        } catch (PDOException $e) {
            throw new Exception("Error finding nearby buildings: " . $e->getMessage());
        }
    }
}
