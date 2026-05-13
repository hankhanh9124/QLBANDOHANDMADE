<?php
require_once 'app/config/database.php';
require_once 'app/models/AddressModel.php';
require_once 'app/models/UserModel.php';

class AddressController
{
    private $db;
    private $addressModel;
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Please login']);
                exit;
            }
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->addressModel = new AddressModel($this->db);
        $this->userModel = new UserModel($this->db);
    }

    private function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $addresses = $this->addressModel->getByUser($userId);
        $user = $this->userModel->getUserById($userId);
        require_once 'app/views/profile/address.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            $userId = $_SESSION['user_id'];

            // Xử lý gộp địa chỉ chi tiết từ 2 dòng (nếu cần)
            $addr1 = $_POST['address_line_1'] ?? '';
            $addr2 = $_POST['address_line_2'] ?? '';
            $fullAddressLine = $_POST['address_line'] ?? trim($addr1 . ($addr2 ? ', ' . $addr2 : ''));

            $data = [
                'user_id' => $userId,
                'name' => $_POST['name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'city' => $_POST['city_text'] ?? ($_POST['city'] ?? ''),
                'district' => $_POST['district_text'] ?? ($_POST['district'] ?? ''),
                'ward' => $_POST['ward_text'] ?? ($_POST['ward'] ?? ''),
                'address_line' => $fullAddressLine,
                'address_type' => $_POST['address_type'] ?? 'Nhà Riêng',
                'is_default' => isset($_POST['is_default']) ? 1 : 0
            ];

            if ($id) {
                $result = $this->addressModel->update($id, $data);
            } else {
                $result = $this->addressModel->add($data);
            }

            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => (bool)$result,
                    'message' => $result ? 'Thành công' : 'Có lỗi xảy ra khi lưu dữ liệu'
                ]);
                exit;
            }

            header('Location: ' . BASE_URL . 'index.php?url=Address/index');
            exit;
        }
    }

    public function delete($id)
    {
        $result = $this->addressModel->delete($id, $_SESSION['user_id']);
        if ($this->isAjax()) {
            echo json_encode(['success' => $result]);
            exit;
        }
        header('Location: ' . BASE_URL . 'index.php?url=Address/index');
    }

    public function setDefault($id)
    {
        $result = $this->addressModel->setDefault($id, $_SESSION['user_id']);
        if ($this->isAjax()) {
            echo json_encode(['success' => $result]);
            exit;
        }
        header('Location: ' . BASE_URL . 'index.php?url=Address/index');
    }

    // Get list of addresses via AJAX for checkout
    public function getList()
    {
        $addresses = $this->addressModel->getByUser($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode($addresses);
        exit;
    }
}
