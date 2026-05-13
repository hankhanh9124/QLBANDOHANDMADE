<?php
class AddressModel {
    private $conn;
    private $table_name = "addresses";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByUser($userId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY is_default DESC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function add($data) {
        // If this is the first address, make it default
        $existing = $this->getByUser($data['user_id']);
        if (empty($existing)) {
            $data['is_default'] = 1;
        }

        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->resetDefault($data['user_id']);
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, name, phone, email, city, district, ward, address_line, address_type, is_default) 
                  VALUES (:user_id, :name, :phone, :email, :city, :district, :ward, :address_line, :address_type, :is_default)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':name' => $data['name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'] ?? '',
            ':city' => $data['city'],
            ':district' => $data['district'],
            ':ward' => $data['ward'],
            ':address_line' => $data['address_line'],
            ':address_type' => $data['address_type'] ?? 'Nhà Riêng',
            ':is_default' => $data['is_default'] ?? 0
        ]);
    }

    public function update($id, $data) {
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->resetDefault($data['user_id']);
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, phone = :phone, email = :email, city = :city, 
                      district = :district, ward = :ward, address_line = :address_line, 
                      address_type = :address_type, is_default = :is_default 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'] ?? '',
            ':city' => $data['city'],
            ':district' => $data['district'],
            ':ward' => $data['ward'],
            ':address_line' => $data['address_line'],
            ':address_type' => $data['address_type'] ?? 'Nhà Riêng',
            ':is_default' => $data['is_default'] ?? 0
        ]);
    }

    public function delete($id, $userId) {
        // If deleting default, set another one as default if exists
        $address = $this->getById($id);
        if ($address && $address->is_default) {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            if ($stmt->execute([':id' => $id])) {
                $other = $this->getByUser($userId);
                if (!empty($other)) {
                    $this->setDefault($other[0]->id, $userId);
                }
                return true;
            }
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    public function setDefault($id, $userId) {
        $this->resetDefault($userId);
        $query = "UPDATE " . $this->table_name . " SET is_default = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    private function resetDefault($userId) {
        $query = "UPDATE " . $this->table_name . " SET is_default = 0 WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':user_id' => $userId]);
    }
}
