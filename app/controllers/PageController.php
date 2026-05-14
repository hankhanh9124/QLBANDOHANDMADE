<?php
require_once 'app/config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/CartModel.php';

class PageController
{
    public function about()
    {
        require_once 'app/views/shares/header.php';
        echo "<div class='container my-5' style='min-height: 50vh;'>";
        echo "<h2 class='text-center mb-4' style='color: #ee225b;'>Giới Thiệu GÌ CŨNG MÓC</h2>";
        echo "<p class='text-center'>Chào mừng bạn đến với thế giới đồ len thủ công của GÌ CŨNG MÓC! Chúng tôi cung cấp các sản phẩm len độc đáo, chất lượng cao làm quà tặng.</p>";
        echo "</div>";
        require_once 'app/views/shares/footer.php';
    }

    public function news()
    {
        require_once 'app/views/shares/header.php';
        echo "<div class='container my-5' style='min-height: 50vh;'>";
        echo "<h2 class='text-center mb-4' style='color: #ee225b;'>Tin Tức & Sự Kiện</h2>";
        echo "<p class='text-center text-muted'>Tính năng đang được phát triển. Vui lòng quay lại sau!</p>";
        echo "</div>";
        require_once 'app/views/shares/footer.php';
    }

    public function contact()
    {
        require_once 'app/views/shares/header.php';
        echo "<div class='container my-5' style='min-height: 50vh;'>";
        echo "<h2 class='text-center mb-4' style='color: #ee225b;'>Liên Hệ Với Chúng Tôi</h2>";
        echo "<div class='row justify-content-center'><div class='col-md-6 text-center'>";
        echo "<p><strong>Hotline:</strong> 0964.325.348</p>";
        echo "<p><strong>Email:</strong> heavenhandmade.vn@gmail.com</p>";
        echo "<p><strong>Địa chỉ:</strong> 9 Đường số 16, Linh Trung, Thủ Đức</p>";
        echo "</div></div></div>";
        require_once 'app/views/shares/footer.php';
    }

    public function cart()
    {
        require_once 'app/views/shares/header.php';
        echo "<div class='container my-5' style='min-height: 50vh;'>";
        echo "<h2 class='text-center mb-4' style='color: #ee225b;'>Giỏ Hàng Của Bạn</h2>";
        echo "<div class='alert alert-warning text-center'>Giỏ hàng của bạn đang trống hoặc tính năng đang được bảo trì.</div>";
        echo "<div class='text-center mt-4'><a href='" . BASE_URL . "index.php?url=Product/index' class='btn btn-danger' style='background-color: #ee225b;'>Tiếp tục mua sắm</a></div>";
        echo "</div>";
        require_once 'app/views/shares/footer.php';
    }

    public function login()
    {
        require_once 'app/views/account/login.php';
    }

    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = $_POST['identifier'] ?? '';
            $password = $_POST['password'] ?? '';

            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);
            $user = $userModel->verifyLogin($identifier, $password);

            if ($user) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_role'] = $user->role;
                $_SESSION['user_avatar'] = $user->avatar;

                // Sync Cart from Session to DB
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    $cartModel = new CartModel($db);
                    foreach ($_SESSION['cart'] as $productId => $item) {
                        $cartModel->addItem($user->id, $productId, $item['quantity']);
                    }
                    // Refresh session cart from DB to ensure consistency
                    $_SESSION['cart'] = $cartModel->getItems($user->id);
                }

                header('Location: ' . BASE_URL . 'index.php?url=Product/index');
                exit;
            } else {
                $error = "Email hoặc mật khẩu không chính xác.";
                require_once 'app/views/account/login.php';
            }
        }
    }

    public function register()
    {
        require_once 'app/views/account/register.php';
    }

    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $identifier = $_POST['identifier'] ?? '';
            $password = $_POST['password'] ?? '';

            $email = '';
            $phone = '';

            if (strpos($identifier, '@') !== false) {
                $email = $identifier;
            } else {
                $phone = trim($identifier);
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    $error = "Số điện thoại phải bao gồm đúng 10 chữ số!";
                    require_once 'app/views/account/register.php';
                    return;
                }
            }

            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);
            $result = $userModel->register($name, $email, $phone, '', $password);

            if ($result === true) {
                // Registration success, redirect to login
                header('Location: ' . BASE_URL . 'index.php?url=Page/login');
                exit;
            } else {
                // Show error on register page
                $error = $result;
                require_once 'app/views/account/register.php';
            }
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ' . BASE_URL . 'index.php?url=Page/login');
        exit;
    }

    public function socialLogin()
    {
        $provider = $_GET['provider'] ?? 'google';
        require_once 'app/views/account/social_connecting.php';
    }

    public function processSocialLogin()
    {
        $provider = $_GET['provider'] ?? 'google';
        $db = (new Database())->getConnection();
        $userModel = new UserModel($db);

        // Mock data based on provider
        $name = ($provider === 'google') ? 'Google User' : 'Facebook User';
        $email = ($provider === 'google') ? 'google_user@gmail.com' : 'fb_user@facebook.com';

        $user = $userModel->loginOrCreateSocialUser($name, $email, $provider);

        if ($user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['user_avatar'] = $user->avatar;

            // Sync Cart from Session to DB
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $cartModel = new CartModel($db);
                foreach ($_SESSION['cart'] as $productId => $item) {
                    $cartModel->addItem($user->id, $productId, $item['quantity']);
                }
                // Refresh session cart from DB to ensure consistency
                $_SESSION['cart'] = $cartModel->getItems($user->id);
            }

            header('Location: ' . BASE_URL . 'index.php?url=Product/index');
            exit;
        }

        header('Location: ' . BASE_URL . 'index.php?url=Page/login');
        exit;
    }
    public function uploadAvatar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $user_id = $_SESSION['user_id'];
            $file = $_FILES['avatar'];

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['error_message'] = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).";
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['error_message'] = "Kích thước ảnh không được vượt quá 5MB.";
            } else {
                $target_dir = "public/uploads/avatars/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = "avatar_" . $user_id . "_" . time() . "." . $extension;
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $db = (new Database())->getConnection();
                    $userModel = new UserModel($db);
                    if ($userModel->updateAvatar($user_id, $file_name)) {
                        $_SESSION['user_avatar'] = $file_name;
                        $_SESSION['success_message'] = "Cập nhật ảnh đại diện thành công!";
                    } else {
                        $_SESSION['error_message'] = "Lỗi khi lưu vào cơ sở dữ liệu.";
                    }
                } else {
                    $_SESSION['error_message'] = "Lỗi khi tải ảnh lên server.";
                }
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?: BASE_URL));
        exit;
    }

    public function orders()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $db = (new Database())->getConnection();
        $orderModel = new OrderModel($db);
        $userModel = new UserModel($db);

        $status = $_GET['status'] ?? null;
        if ($status === 'all') $status = null;

        $orders = $orderModel->getOrdersByUserId($userId, $status);
        $user = $userModel->getUserById($userId);

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/orders.php';
        require_once 'app/views/shares/footer.php';
    }

    public function cancelOrder()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            $reason = $_POST['reason'] ?? 'Không có lý do';
            $userId = $_SESSION['user_id'];

            if (!$orderId) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID đơn hàng không hợp lệ.']);
                return;
            }

            $db = (new Database())->getConnection();
            $orderModel = new OrderModel($db);

            // Verify order belongs to user and is pending
            $query = "SELECT status FROM orders WHERE id = :id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $orderId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$order) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
                return;
            }

            if ($order->status !== 'pending') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Chỉ có thể hủy đơn hàng đang chờ thanh toán.']);
                return;
            }

            if ($orderModel->cancelOrderWithReason($orderId, $reason)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Lỗi khi hủy đơn hàng.']);
            }
        }
    }

    public function cancelOrderDetail()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?url=Page/login");
            exit();
        }

        $orderId = $_GET['id'] ?? null;
        if (!$orderId) {
            header("Location: " . BASE_URL . "index.php?url=Page/orders");
            exit();
        }

        $db = (new Database())->getConnection();
        $orderModel = new OrderModel($db);
        $userModel = new UserModel($db);

        $order = $orderModel->getOrderById($orderId);
        $userId = $_SESSION['user_id'];
        $user = $userModel->getUserById($userId);

        if (!$order || $order->user_id != $userId || $order->status != 'cancelled') {
            header("Location: " . BASE_URL . "index.php?url=Page/orders");
            exit();
        }

        require 'app/views/shares/header.php';
        require 'app/views/account/cancel_order_detail.php';
        require 'app/views/shares/footer.php';
    }
    public function profile()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $db = (new Database())->getConnection();
        $userModel = new UserModel($db);
        $user = $userModel->getUserById($_SESSION['user_id']);

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/profile_details.php';
        require_once 'app/views/shares/footer.php';
    }

    public function bank()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $db = (new Database())->getConnection();
        $userModel = new UserModel($db);
        $user = $userModel->getUserById($_SESSION['user_id']);

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/bank.php';
        require_once 'app/views/shares/footer.php';
    }

    public function address()
    {
        header('Location: ' . BASE_URL . 'index.php?url=Address/index');
        exit;
    }

    public function password()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $db = (new Database())->getConnection();
        $userModel = new UserModel($db);
        $user = $userModel->getUserById($_SESSION['user_id']);

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/change_password.php';
        require_once 'app/views/shares/footer.php';
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $id = $_SESSION['user_id'];
            $name = $_POST['name'] ?? '';
            $username = $_POST['username'] ?? null;
            $gender = $_POST['gender'] ?? 'khac';
            $dob = $_POST['dob'] ?? null;

            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);

            // Check if username is taken
            if (!empty($username) && $userModel->isUsernameTaken($id, $username)) {
                $_SESSION['error_message'] = "Tên đăng nhập này đã có người sử dụng!";
                header('Location: ' . BASE_URL . 'index.php?url=Page/profile');
                exit;
            }

            if ($userModel->updateProfile($id, $name, $gender, $dob, $username)) {
                $_SESSION['user_name'] = $name;
                $_SESSION['success_message'] = "Cập nhật hồ sơ thành công!";
            } else {
                $_SESSION['error_message'] = "Có lỗi xảy ra khi cập nhật hồ sơ.";
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Page/profile');
        exit;
    }

    public function processChangePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $id = $_SESSION['user_id'];
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if ($new !== $confirm) {
                $_SESSION['error_message'] = "Mật khẩu xác nhận không khớp.";
                header('Location: ' . BASE_URL . 'index.php?url=Page/password');
                exit;
            }
            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);
            $user = $userModel->getUserById($id);
            if (password_verify($current, $user->password)) {
                if ($userModel->changePassword($id, $new)) {
                    $_SESSION['success_message'] = "Đổi mật khẩu thành công!";
                } else {
                    $_SESSION['error_message'] = "Lỗi khi đổi mật khẩu.";
                }
            } else {
                $_SESSION['error_message'] = "Mật khẩu hiện tại không chính xác.";
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Page/password');
        exit;
    }

    public function updateEmail()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $id = $_SESSION['user_id'];
            $email = $_POST['email'] ?? '';
            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);
            if ($userModel->updateEmail($id, $email)) {
                echo json_encode(['success' => true]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }

    public function updatePhone()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $id = $_SESSION['user_id'];
            $phone = $_POST['phone'] ?? '';
            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);
            if ($userModel->updatePhone($id, $phone)) {
                echo json_encode(['success' => true]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }

    public function forgotPassword()
    {
        require_once 'app/views/account/forgot_password.php';
    }

    public function sendOTP()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = $_POST['identifier'] ?? '';
            if (empty($identifier)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập Email hoặc Số điện thoại.']);
                return;
            }

            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);
            $user = $userModel->getUserByIdentifier($identifier);

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại trên hệ thống.']);
                return;
            }

            if (session_status() === PHP_SESSION_NONE) session_start();
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['reset_identifier'] = $identifier;
            $_SESSION['otp_time'] = time();

            // Mock sending OTP - in real case, use Mailer or SMS API
            // For demo purposes, we return success. You could also log it or send via email here.

            echo json_encode(['success' => true, 'message' => 'Mã xác minh đã được gửi! (Demo: ' . $otp . ')']);
        }
    }

    public function verifyOTP()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = $_POST['otp'] ?? '';
            if (session_status() === PHP_SESSION_NONE) session_start();

            if (!isset($_SESSION['reset_otp']) || $_SESSION['reset_otp'] !== $otp) {
                echo json_encode(['success' => false, 'message' => 'Mã xác minh không chính xác.']);
                return;
            }

            // Check expiry (e.g., 5 minutes)
            if (time() - $_SESSION['otp_time'] > 300) {
                echo json_encode(['success' => false, 'message' => 'Mã xác minh đã hết hạn.']);
                return;
            }

            $_SESSION['otp_verified'] = true;
            echo json_encode(['success' => true]);
        }
    }

    public function resetPassword()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng xác thực mã OTP trước.']);
                return;
            }

            $password = $_POST['password'] ?? '';
            $identifier = $_SESSION['reset_identifier'];

            $db = (new Database())->getConnection();
            $userModel = new UserModel($db);

            if ($userModel->resetPasswordByIdentifier($identifier, $password)) {
                // Clear session data
                unset($_SESSION['reset_otp']);
                unset($_SESSION['reset_identifier']);
                unset($_SESSION['otp_time']);
                unset($_SESSION['otp_verified']);

                echo json_encode(['success' => true, 'message' => 'Đặt lại mật khẩu thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu.']);
            }
        }
    }
}
