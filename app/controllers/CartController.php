<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/CartModel.php';
require_once 'app/models/AddressModel.php';

class CartController
{
    private $productModel;
    private $cartModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->cartModel = new CartModel($this->db);
    }

    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            // Priority: Load from DB if logged in
            $_SESSION['cart'] = $this->cartModel->getItems($userId);
        }

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        
        // REFRESH DATA FROM DB: Tự động cập nhật lại tên, giá, ảnh từ DB để tránh lỗi dữ liệu cũ
        if (!empty($cart)) {
            $newSessionCart = [];
            foreach ($cart as $cartKey => $item) {
                // Parse product_id và variant_id từ cartKey (format: pid_vid hoặc pid)
                $parts = explode('_', $cartKey);
                $pid = (int)$parts[0];
                $vid = isset($parts[1]) ? (int)$parts[1] : 0;

                $product = $this->productModel->getProductById($pid);
                if ($product) {
                    $discount = isset($product->discount_percent) ? (int)$product->discount_percent : 0;
                    
                    // Lấy giá từ Variant if có
                    $price = $product->price;
                    $vName = "";
                    $vImg = $product->image;

                    if ($vid > 0) {
                        require_once 'app/models/VariantModel.php';
                        $vModel = new VariantModel($this->db);
                        $variant = $vModel->getVariantById($vid);
                        if ($variant) {
                            $price = $variant->price > 0 ? $variant->price : $product->price;
                            $vName = " - Mẫu: " . $variant->name;
                            $vImg = !empty($variant->image) ? $variant->image : $product->image;
                        }
                    }

                    $finalPrice = ($discount > 0) ? $price * (1 - $discount/100) : $price;
                    $stock = ($vid > 0 && isset($variant)) ? $variant->stock : $product->stock;
                    
                    $newSessionCart[$cartKey] = $item;
                    $newSessionCart[$cartKey]['name'] = $product->name . $vName;
                    $newSessionCart[$cartKey]['price'] = $finalPrice;
                    $newSessionCart[$cartKey]['image'] = $vImg;
                    $newSessionCart[$cartKey]['stock'] = $stock;
                    $newSessionCart[$cartKey]['product_id'] = $pid;
                    $newSessionCart[$cartKey]['variant_id'] = $vid;
                }
            }
            $_SESSION['cart'] = $newSessionCart;
            $cart = $_SESSION['cart'];
        }
        
        require_once 'app/views/cart/index.php';
    }

    public function checkout()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $selectedIds = [];
        
        // Handle selected IDs from URL
        if (isset($_GET['ids']) && !empty($_GET['ids'])) {
            $selectedIds = explode(',', $_GET['ids']);
            $filteredCart = [];
            foreach ($selectedIds as $sid) {
                if (isset($cart[$sid])) {
                    $filteredCart[$sid] = $cart[$sid];
                }
            }
            $cart = $filteredCart;
        } else {
            // Nếu không có ids, mặc định chọn tất cả trong giỏ
            $selectedIds = array_keys($cart);
        }
        
        // Luôn đồng bộ dữ liệu trước khi thanh toán
        if (!empty($cart)) {
            foreach ($cart as $id => $item) {
                $parts = explode('_', $id);
                $pid = (int)$parts[0];
                $vid = isset($parts[1]) ? (int)$parts[1] : 0;
                
                $product = $this->productModel->getProductById($pid);
                if ($product) {
                    $discount = isset($product->discount_percent) ? (int)$product->discount_percent : 0;
                    $price = $product->price;
                    $vName = "";
                    $vImg = $product->image;

                    if ($vid > 0) {
                        require_once 'app/models/VariantModel.php';
                        $vModel = new VariantModel($this->db);
                        $variant = $vModel->getVariantById($vid);
                        if ($variant) {
                            $price = $variant->price > 0 ? $variant->price : $product->price;
                            $vName = " - Mẫu: " . $variant->name;
                            $vImg = !empty($variant->image) ? $variant->image : $product->image;
                        }
                    }

                    $finalPrice = ($discount > 0) ? $price * (1 - $discount/100) : $price;
                    $stock = ($vid > 0 && isset($variant)) ? $variant->stock : $product->stock;
                    
                    if (isset($_SESSION['cart'][$id])) {
                        $_SESSION['cart'][$id]['name'] = $product->name . $vName;
                        $_SESSION['cart'][$id]['price'] = $finalPrice;
                        $_SESSION['cart'][$id]['image'] = $vImg;
                        $_SESSION['cart'][$id]['stock'] = $stock;
                    }
                    // Update local $cart as well
                    $cart[$id]['name'] = $product->name . $vName;
                    $cart[$id]['price'] = $finalPrice;
                    $cart[$id]['image'] = $vImg;
                    $cart[$id]['stock'] = $stock;
                }
            }
        }
        
        // Fetch addresses for logged in user
        $addresses = [];
        if (isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $addressModel = new AddressModel($db);
            $addresses = $addressModel->getByUser($_SESSION['user_id']);
        }

        require_once 'app/views/cart/checkout.php';
    }

    public function add($id)
    {
        $variant_id = isset($_GET['variant_id']) ? (int)$_GET['variant_id'] : 0;
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            $userId = $_SESSION['user_id'] ?? null;
            $cartKey = $id . ($variant_id > 0 ? '_' . $variant_id : '');

            if ($userId) {
                // Check if user is the owner
                if (isset($product->user_id) && $userId == $product->user_id) {
                    $_SESSION['error_message'] = "Không thể mua sản phẩm của chính bạn";
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                }

                // Check stock
                $currentQty = 0;
                if (isset($_SESSION['cart'][$cartKey])) {
                    $currentQty = $_SESSION['cart'][$cartKey]['quantity'];
                }
                
                $stock = $product->stock;
                if ($variant_id > 0) {
                    require_once 'app/models/VariantModel.php';
                    $vModel = new VariantModel($this->db);
                    $variant = $vModel->getVariantById($variant_id);
                    if ($variant) {
                        $stock = $variant->stock;
                    }
                }

                if ($currentQty + 1 > $stock) {
                    $_SESSION['error_message'] = "Số lượng trong giỏ hàng đã đạt giới hạn tồn kho (" . $stock . ")";
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                }

                $this->cartModel->addItem($userId, $id, $variant_id, 1);
                $_SESSION['cart'] = $this->cartModel->getItems($userId);
            } else {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // Check stock for guest
                $currentQty = 0;
                if (isset($_SESSION['cart'][$cartKey])) {
                    $currentQty = $_SESSION['cart'][$cartKey]['quantity'];
                }

                $stock = $product->stock;
                if ($variant_id > 0) {
                    require_once 'app/models/VariantModel.php';
                    $vModel = new VariantModel($this->db);
                    $variant = $vModel->getVariantById($variant_id);
                    if ($variant) {
                        $stock = $variant->stock;
                    }
                }

                if ($currentQty + 1 > $stock) {
                    $_SESSION['error_message'] = "Số lượng trong giỏ hàng đã đạt giới hạn tồn kho (" . $stock . ")";
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                }

                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['quantity']++;
                } else {
                    $discount = isset($product->discount_percent) ? (int)$product->discount_percent : 0;
                    
                    // Lấy giá và ảnh từ Variant if có
                    $price = $product->price;
                    $vName = "";
                    $vImg = $product->image;

                    if ($variant_id > 0) {
                        if (!isset($vModel)) {
                            require_once 'app/models/VariantModel.php';
                            $vModel = new VariantModel($this->db);
                        }
                        if (!isset($variant)) {
                            $variant = $vModel->getVariantById($variant_id);
                        }
                        if ($variant) {
                            $price = $variant->price > 0 ? $variant->price : $product->price;
                            $vName = " - Mẫu: " . $variant->name;
                            $vImg = !empty($variant->image) ? $variant->image : $product->image;
                        }
                    }

                    $finalPrice = ($discount > 0) ? $price * (1 - $discount/100) : $price;

                    $_SESSION['cart'][$cartKey] = [
                        'id' => $id,
                        'product_id' => $id,
                        'variant_id' => $variant_id,
                        'name' => $product->name . $vName,
                        'price' => $finalPrice,
                        'image' => $vImg,
                        'stock' => $stock,
                        'quantity' => 1
                    ];
                }
            }
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: ' . BASE_URL . 'index.php?url=Product/index');
        }
        exit;
    }

    public function addAjax()
    {
        header('Content-Type: application/json');
        
        $id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $variant_id = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            $userId = $_SESSION['user_id'] ?? null;
            $cartKey = $id . ($variant_id > 0 ? '_' . $variant_id : '');

            if ($userId) {
                // Check if user is the owner
                if (isset($product->user_id) && $userId == $product->user_id) {
                    echo json_encode(['success' => false, 'message' => 'Không thể mua sản phẩm của chính bạn']);
                    exit;
                }

                // Check stock
                $currentQty = 0;
                if (isset($_SESSION['cart'][$cartKey])) {
                    $currentQty = $_SESSION['cart'][$cartKey]['quantity'];
                }
                
                $stock = $product->stock;
                if ($variant_id > 0) {
                    require_once 'app/models/VariantModel.php';
                    $vModel = new VariantModel($this->db);
                    $variant = $vModel->getVariantById($variant_id);
                    if ($variant) {
                        $stock = $variant->stock;
                    }
                }

                if ($currentQty + $quantity > $stock) {
                    echo json_encode(['success' => false, 'message' => 'Rất tiếc, chỉ còn ' . $stock . ' sản phẩm trong kho.']);
                    exit;
                }

                $this->cartModel->addItem($userId, $id, $variant_id, $quantity);
                $_SESSION['cart'] = $this->cartModel->getItems($userId);
            } else {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // Check stock for guest
                $currentQty = 0;
                if (isset($_SESSION['cart'][$cartKey])) {
                    $currentQty = $_SESSION['cart'][$cartKey]['quantity'];
                }

                $stock = $product->stock;
                if ($variant_id > 0) {
                    require_once 'app/models/VariantModel.php';
                    $vModel = new VariantModel($this->db);
                    $variant = $vModel->getVariantById($variant_id);
                    if ($variant) {
                        $stock = $variant->stock;
                    }
                }

                if ($currentQty + $quantity > $stock) {
                    echo json_encode(['success' => false, 'message' => 'Rất tiếc, chỉ còn ' . $stock . ' sản phẩm trong kho.']);
                    exit;
                }

                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
                } else {
                    $discount = isset($product->discount_percent) ? (int)$product->discount_percent : 0;
                    
                    // Lấy giá và ảnh từ Variant if có
                    $price = $product->price;
                    $vName = "";
                    $vImg = $product->image;

                    if ($variant_id > 0) {
                        if (!isset($vModel)) {
                            require_once 'app/models/VariantModel.php';
                            $vModel = new VariantModel($this->db);
                        }
                        if (!isset($variant)) {
                            $variant = $vModel->getVariantById($variant_id);
                        }
                        if ($variant) {
                            $price = $variant->price > 0 ? $variant->price : $product->price;
                            $vName = " - Mẫu: " . $variant->name;
                            $vImg = !empty($variant->image) ? $variant->image : $product->image;
                        }
                    }

                    $finalPrice = ($discount > 0) ? $price * (1 - $discount/100) : $price;

                    $_SESSION['cart'][$cartKey] = [
                        'id' => $id,
                        'product_id' => $id,
                        'variant_id' => $variant_id,
                        'name' => $product->name . $vName,
                        'price' => $finalPrice,
                        'image' => $vImg,
                        'stock' => $stock,
                        'quantity' => $quantity
                    ];
                }
            }
            
            $cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
            echo json_encode([
                'success' => true,
                'cartCount' => $cartCount,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
        }
        exit;
    }

    public function buyNow($id)
    {
        $variant_id = isset($_GET['variant_id']) ? (int)$_GET['variant_id'] : 0;
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            $cartKey = $id . ($variant_id > 0 ? '_' . $variant_id : '');
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId && isset($product->user_id) && $userId == $product->user_id) {
                $_SESSION['error_message'] = "Không thể mua sản phẩm của chính bạn";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Check stock
            $currentQty = 0;
            if (isset($_SESSION['cart'][$cartKey])) {
                $currentQty = $_SESSION['cart'][$cartKey]['quantity'];
            }
            
            $stock = $product->stock;
            if ($variant_id > 0) {
                require_once 'app/models/VariantModel.php';
                $vModel = new VariantModel($this->db);
                $variant = $vModel->getVariantById($variant_id);
                if ($variant) {
                    $stock = $variant->stock;
                }
            }

            if ($currentQty + $quantity > $stock) {
                $_SESSION['error_message'] = "Sản phẩm này đã hết hàng hoặc đạt giới hạn tồn kho.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            if ($userId) {
                $this->cartModel->addItem($userId, $id, $variant_id, $quantity);
                $_SESSION['cart'] = $this->cartModel->getItems($userId);
            } else {
                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
                } else {
                    $discount = isset($product->discount_percent) ? (int)$product->discount_percent : 0;
                    
                    // Lấy giá và ảnh từ Variant if có
                    $price = $product->price;
                    $vName = "";
                    $vImg = $product->image;

                    if ($variant_id > 0) {
                        if (!isset($vModel)) {
                            require_once 'app/models/VariantModel.php';
                            $vModel = new VariantModel($this->db);
                        }
                        if (!isset($variant)) {
                            $variant = $vModel->getVariantById($variant_id);
                        }
                        if ($variant) {
                            $price = $variant->price > 0 ? $variant->price : $product->price;
                            $vName = " - Mẫu: " . $variant->name;
                            $vImg = !empty($variant->image) ? $variant->image : $product->image;
                        }
                    }

                    $finalPrice = ($discount > 0) ? $price * (1 - $discount/100) : $price;

                    $_SESSION['cart'][$cartKey] = [
                        'id' => $id,
                        'product_id' => $id,
                        'variant_id' => $variant_id,
                        'name' => $product->name . $vName,
                        'price' => $finalPrice,
                        'image' => $vImg,
                        'stock' => $stock,
                        'quantity' => $quantity
                    ];
                }
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Cart/checkout&ids=' . $cartKey);
        exit;
    }

    public function remove($id)
    {
        // $id ở đây thực tế là cartKey (pid_vid)
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $parts = explode('_', $id);
            $pid = (int)$parts[0];
            $vid = isset($parts[1]) ? (int)$parts[1] : 0;
            $this->cartModel->removeItem($userId, $pid, $vid);
            $_SESSION['cart'] = $this->cartModel->getItems($userId);
        } else {
            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
            $userId = $_SESSION['user_id'] ?? null;
            
            foreach ($_POST['quantities'] as $cartKey => $quantity) {
                $qty = (int)$quantity;
                $parts = explode('_', $cartKey);
                $pid = (int)$parts[0];
                $vid = isset($parts[1]) ? (int)$parts[1] : 0;

                // Validate stock
                $product = $this->productModel->getProductById($pid);
                $stock = $product ? $product->stock : 0;
                if ($vid > 0) {
                    require_once 'app/models/VariantModel.php';
                    $vModel = new VariantModel($this->db);
                    $variant = $vModel->getVariantById($vid);
                    if ($variant) {
                        $stock = $variant->stock;
                    }
                }
                if ($qty > $stock) {
                    $qty = $stock;
                }

                if ($userId) {
                    if ($qty > 0) {
                        $this->cartModel->updateQuantity($userId, $pid, $qty, $vid);
                    } else {
                        $this->cartModel->removeItem($userId, $pid, $vid);
                    }
                } else {
                    if ($qty > 0 && isset($_SESSION['cart'][$cartKey])) {
                        $_SESSION['cart'][$cartKey]['quantity'] = $qty;
                    } elseif ($qty <= 0 && isset($_SESSION['cart'][$cartKey])) {
                        unset($_SESSION['cart'][$cartKey]);
                    }
                }
            }
            
            if ($userId) {
                $_SESSION['cart'] = $this->cartModel->getItems($userId);
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
        exit;
    }

    public function updateAjax()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null; // $id ở đây là cartKey (pid_vid)
            $quantity = (int)($_POST['quantity'] ?? 1);
            $userId = $_SESSION['user_id'] ?? null;

            if ($id) {
                $parts = explode('_', $id);
                $pid = (int)$parts[0];
                $vid = isset($parts[1]) ? (int)$parts[1] : 0;

                if ($userId) {
                    if ($quantity > 0) {
                        // Check stock
                        $product = $this->productModel->getProductById($pid);
                        $stock = $product ? $product->stock : 0;
                        if ($vid > 0) {
                            require_once 'app/models/VariantModel.php';
                            $vModel = new VariantModel($this->db);
                            $variant = $vModel->getVariantById($vid);
                            if ($variant) {
                                $stock = $variant->stock;
                            }
                        }

                        if ($quantity > $stock) {
                            echo json_encode(['success' => false, 'message' => 'Rất tiếc, chỉ còn ' . $stock . ' sản phẩm trong kho.']);
                            exit;
                        }

                        $this->cartModel->updateQuantity($userId, $pid, $quantity, $vid);
                    } else {
                        $this->cartModel->removeItem($userId, $pid, $vid);
                    }
                    $_SESSION['cart'] = $this->cartModel->getItems($userId);
                } else {
                    if (isset($_SESSION['cart'][$id])) {
                        if ($quantity > 0) {
                            // Check stock for guest
                            $product = $this->productModel->getProductById($pid);
                            $stock = $product ? $product->stock : 0;
                            if ($vid > 0) {
                                require_once 'app/models/VariantModel.php';
                                $vModel = new VariantModel($this->db);
                                $variant = $vModel->getVariantById($vid);
                                if ($variant) {
                                    $stock = $variant->stock;
                                }
                            }

                            if ($quantity > $stock) {
                                echo json_encode(['success' => false, 'message' => 'Rất tiếc, chỉ còn ' . $stock . ' sản phẩm trong kho.']);
                                exit;
                            }

                            $_SESSION['cart'][$id]['quantity'] = $quantity;
                        } else {
                            unset($_SESSION['cart'][$id]);
                        }
                    }
                }

                $totalAmount = 0;
                $cartCount = 0;
                $itemSubtotal = 0;

                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $cid => $item) {
                        $totalAmount += $item['price'] * $item['quantity'];
                        $cartCount += $item['quantity'];
                        if ($cid == $id) {
                            $itemSubtotal = $item['price'] * $item['quantity'];
                        }
                    }
                }

                echo json_encode([
                    'success' => true,
                    'itemSubtotal' => number_format($itemSubtotal, 0, ',', '.') . ' ₫',
                    'totalAmount' => number_format($totalAmount, 0, ',', '.') . ' ₫',
                    'cartCount' => $cartCount
                ]);
                exit;
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
            $db = (new Database())->getConnection();
            $orderModel = new OrderModel($db);

            // Get selected IDs from form
            $selectedIdsStr = $_POST['selected_ids'] ?? '';
            $selectedIds = !empty($selectedIdsStr) ? explode(',', $selectedIdsStr) : array_keys($_SESSION['cart']);

            $cartToProcess = [];
            foreach ($selectedIds as $sid) {
                if (isset($_SESSION['cart'][$sid])) {
                    $item = $_SESSION['cart'][$sid];
                    $pid = (int)$item['product_id'];
                    $vid = isset($item['variant_id']) ? (int)$item['variant_id'] : 0;
                    
                    // Final stock check
                    $product = $this->productModel->getProductById($pid);
                    $stock = $product ? $product->stock : 0;
                    if ($vid > 0) {
                        require_once 'app/models/VariantModel.php';
                        $vModel = new VariantModel($this->db);
                        $variant = $vModel->getVariantById($vid);
                        if ($variant) {
                            $stock = $variant->stock;
                        }
                    }

                    if ($item['quantity'] > $stock) {
                        $_SESSION['error_message'] = "Sản phẩm '" . $item['name'] . "' hiện chỉ còn " . $stock . " trong kho. Vui lòng cập nhật lại giỏ hàng.";
                        header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
                        exit;
                    }

                    $cartToProcess[$sid] = $item;
                }
            }

            if (empty($cartToProcess)) {
                header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
                exit;
            }

            $name = $_POST['customer_name'] ?? '';
            $phone = $_POST['customer_phone'] ?? '';
            $email = $_POST['customer_email'] ?? '';
            $password = $_POST['customer_password'] ?? '123456'; // Default if empty
            $note = $_POST['order_note'] ?? '';
            $paymentMethod = $_POST['payment_method'] ?? 'cod';
            $shippingCost = $_POST['shipping_cost'] ?? 0;
            
            $selectedAddressId = $_POST['selected_address_id'] ?? null;
            $fullAddress = '';

            // Nếu có địa chỉ đã lưu, lấy thông tin từ đó (ưu tiên hơn form nhập tay)
            if ($selectedAddressId) {
                require_once 'app/models/AddressModel.php';
                $addrModel = new AddressModel($db);
                $addr = $addrModel->getById($selectedAddressId);
                if ($addr) {
                    $name = $addr->name;
                    $phone = $addr->phone;
                    $fullAddress = trim("$addr->address_line, $addr->ward, $addr->district, $addr->city");
                }
            }

            // Validate số điện thoại SAU KHI đã xác định phone từ địa chỉ hoặc form
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                echo "<script>alert('Vui lòng nhập đúng 10 số điện thoại!'); window.history.back();</script>";
                exit;
            }

            if (empty($fullAddress)) {
                $addr1 = $_POST['address_1'] ?? '';
                $addr2 = $_POST['address_2'] ?? '';
                $prov = $_POST['province'] ?? '';
                $dist = $_POST['district'] ?? '';
                $ward = $_POST['ward'] ?? '';
                $fullAddress = trim("$addr1 $addr2, $ward, $dist, $prov");
            }

            // 1. Xác định userId: ưu tiên session (đã đăng nhập), nếu không thì tạo mới
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            } else {
                $userId = $orderModel->createQuickUser($name, $email, $phone, $fullAddress, $password);
            }

            if ($userId) {
                // 2. Calculate total
                $total = 0;
                foreach ($cartToProcess as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
                $shippingCost = $_POST['shipping_cost'] ?? 0;
                $total += (int)$shippingCost;

                // 3. Create order with address snapshot
                $orderId = $orderModel->createOrder($userId, $total, [
                    'recipient_name'    => $name,
                    'recipient_phone'   => $phone,
                    'recipient_address' => $fullAddress,
                    'note'              => $note,
                    'payment_method'    => $paymentMethod,
                    'shipping_fee'      => $shippingCost
                ]);

                if ($orderId) {
                    // 4. Add order details
                    foreach ($cartToProcess as $cartKey => $item) {
                        $pid = (int)$item['product_id'];
                        $vid = isset($item['variant_id']) ? (int)$item['variant_id'] : 0;
                        
                        $orderModel->addOrderDetail($orderId, $pid, $item['quantity'], $item['price'], $vid);
                        $this->productModel->decreaseStockAndIncreaseSold($pid, $item['quantity']);
                        
                        // Nếu có variant, cũng nên giảm kho của variant đó
                        if ($vid > 0) {
                            require_once 'app/models/VariantModel.php';
                            $vModel = new VariantModel($db);
                            $vModel->updateVariantStock($vid, $item['quantity']);
                        }

                        // 5. Remove ONLY ordered items from cart
                        if ($userId) {
                            $this->cartModel->removeItem($userId, $pid, $vid);
                        }
                        unset($_SESSION['cart'][$cartKey]);
                    }

                    // 6. Lưu orderId và total vào session để hiển thị trang success
                    $_SESSION['last_order_id'] = $orderId;
                    $_SESSION['last_order_total'] = $total;

                    // 7. Gửi thông báo cho Admin
                    require_once 'app/models/NotificationModel.php';
                    $notificationModel = new NotificationModel($db);
                    
                    $stmtAdmins = $db->query("SELECT id FROM user WHERE role = 'admin'");
                    $admins = $stmtAdmins->fetchAll(PDO::FETCH_OBJ);
                    foreach ($admins as $admin) {
                        $msg = "Có đơn đặt hàng mới #" . $orderId . " trị giá " . number_format($total, 0, ',', '.') . " đ";
                        $link = 'index.php?url=Dashboard/orders';
                        $notificationModel->addNotification($admin->id, $msg, $link);
                    }

                    // 8. Redirect đến trang success
                    header('Location: ' . BASE_URL . 'index.php?url=Cart/orderSuccess');
                    exit;
                }
            }
            
            echo "Có lỗi xảy ra trong quá trình đặt hàng. Vui lòng thử lại.";
        } else {
            header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
            exit;
        }
    }

    public function orderSuccess()
    {
        if (!isset($_SESSION['last_order_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Product/index');
            exit;
        }
        $orderId = $_SESSION['last_order_id'];
        $total   = $_SESSION['last_order_total'];
        // Xóa khỏi session sau khi lấy
        unset($_SESSION['last_order_id'], $_SESSION['last_order_total']);

        require_once 'app/views/cart/order_success.php';
    }

    public function reorder($orderId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId && isset($_COOKIE['guest_user_id'])) {
            $userId = $_COOKIE['guest_user_id'];
        }

        if ($userId) {
            require_once 'app/models/OrderModel.php';
            $orderModel = new OrderModel((new Database())->getConnection());
            $order = $orderModel->getOrderById($orderId);
            
            if ($order && !empty($order->items)) {
                // Xóa giỏ hàng hiện tại trước khi "Mua Lại" theo yêu cầu user
                $this->cartModel->clear($userId);
                unset($_SESSION['cart']); 
                
                foreach ($order->items as $item) {
                    $vid = isset($item->variant_id) ? (int)$item->variant_id : 0;
                    $qty = isset($item->quantity) ? (int)$item->quantity : 1;
                    $this->cartModel->addItem($userId, $item->product_id, $vid, $qty);
                }
            }
        }
        
        header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
        exit;
    }
}
?>
