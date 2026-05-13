<?php
class UserModel {
    private $conn;
    private $table_name = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function verifyLogin($identifier, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :identifier OR phone = :identifier OR username = :identifier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            // Verify password hash
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }
        return false;
    }

    public function register($name, $email, $phone, $address, $password) {
        if (empty($email) && empty($phone)) {
            return "Vui lòng cung cấp ít nhất Email hoặc Số điện thoại.";
        }

        // Check if email already exists (if provided)
        if (!empty($email)) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return "Email đã được sử dụng.";
            }
        }

        // Check if phone already exists (if provided)
        if (!empty($phone)) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE phone = :phone";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return "Số điện thoại đã được sử dụng.";
            }
        }

        $query = "INSERT INTO " . $this->table_name . " (name, email, phone, address, password, role) VALUES (:name, :email, :phone, :address, :password, 'user')";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $emailParam = !empty($email) ? $email : null;
        $phoneParam = !empty($phone) ? $phone : null;

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $emailParam);
        $stmt->bindParam(':phone', $phoneParam);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':password', $hashedPassword);
        
        if ($stmt->execute()) {
            return true;
        }
        return "Có lỗi xảy ra khi đăng ký.";
    }

    public function loginOrCreateSocialUser($name, $email, $provider) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            // Create user for first time social login
            $query = "INSERT INTO " . $this->table_name . " (name, email, password, role) VALUES (:name, :email, :password, 'user')";
            $stmt = $this->conn->prepare($query);
            $dummyPass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $dummyPass);
            if ($stmt->execute()) {
                $id = $this->conn->lastInsertId();
                return (object)['id' => $id, 'name' => $name, 'role' => 'user'];
            }
        }
        return false;
    }

    public function updateUser($id, $name, $email, $phone)
    {
        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, phone = :phone WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function adminAddUser($name, $email, $phone, $password, $role)
    {
        // Validation for uniqueness
        if (!empty($email)) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) return "Email đã được sử dụng.";
        }
        if (!empty($phone)) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE phone = :phone";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();
            if ($stmt->rowCount() > 0) return "Số điện thoại đã được sử dụng.";
        }

        $query = "INSERT INTO " . $this->table_name . " (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $emailParam = !empty($email) ? $email : null;
        $phoneParam = !empty($phone) ? $phone : null;

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $emailParam);
        $stmt->bindParam(':phone', $phoneParam);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            return true;
        }
        return "Lỗi hệ thống khi thêm người dùng.";
    }

    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateAvatar($id, $avatarPath)
    {
        $query = "UPDATE " . $this->table_name . " SET avatar = :avatar WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avatar', $avatarPath);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateProfile($id, $name, $gender, $dob, $username = null)
    {
        if (empty($dob)) $dob = null;
        $query = "UPDATE " . $this->table_name . " SET name = :name, gender = :gender, dob = :dob, username = :username WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function isUsernameTaken($userId, $username)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username AND id != :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function changePassword($id, $newPassword)
    {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateEmail($id, $email)
    {
        $query = "UPDATE " . $this->table_name . " SET email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePhone($id, $phone)
    {
        $query = "UPDATE " . $this->table_name . " SET phone = :phone WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateRole($id, $role)
    {
        $query = "UPDATE " . $this->table_name . " SET role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getUserByIdentifier($identifier) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :identifier OR phone = :identifier OR username = :identifier";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function resetPasswordByIdentifier($identifier, $newPassword) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE email = :identifier OR phone = :identifier";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':identifier', $identifier);
        return $stmt->execute();
    }
}
