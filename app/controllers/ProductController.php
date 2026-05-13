<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/VariantModel.php');
class ProductController
{
    private $productModel;
    private $db;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function restrictToAdmin()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }
    }

    private function getGroupInfoByCategory($categoryName)
    {
        $categoryName = trim($categoryName);
        $groups = [
            'handmade' => [
                'label' => 'Sản Phẩm Handmade',
                'categories' => ['Búp bê len', 'Thú bông len', 'Túi len', 'Nón, khăn len', 'Đồ gia dụng handmade']
            ],
            'tools' => [
                'label' => 'Dụng cụ Đan - Móc',
                'categories' => ['Kim móc các loại', 'Kim đan các loại', 'Phụ kiện hỗ trợ đan móc len', 'Dụng cụ đan – móc']
            ],
            'flowers' => [
                'label' => 'Hoa Len',
                'categories' => ['Hoa lẻ', 'Hoa lẻ ', 'Hoa bó', 'Hoa mix ngẫu nhiên']
            ],
            'yarn' => [
                'label' => 'Len - Sợi',
                'categories' => ['Sợi tự nhiên', 'Sợi tổng hợp', 'Sợi chuyên móc thú bông']
            ],
            'keychain' => [
                'label' => 'Móc Khóa',
                'categories' => ['Móc khóa', 'Móc khoá', 'Móc khóa len']
            ]
        ];

        foreach ($groups as $type => $info) {
            foreach ($info['categories'] as $cat) {
                if (trim($cat) === $categoryName) {
                    return [
                        'type' => $type,
                        'label' => $info['label'],
                        'link' => BASE_URL . 'index.php?url=Product/group/' . $type
                    ];
                }
            }
        }
        return null;
    }

    private function isStaff()
    {
        return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'seller');
    }

    private function restrictToStaff()
    {
        if (!$this->isStaff()) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }
    }
    public function index()
    {
        $banners = $this->productModel->getBanners('hero'); // Carousel banners
        $systemMaxPrice = $this->productModel->getMaxPrice();

        // Đọc cấu hình từ settings.json
        $settings = [];
        if (file_exists('app/config/settings.json')) {
            $settings = json_decode(file_get_contents('app/config/settings.json'), true) ?? [];
        }
        $sectionFeatured = $settings['section_featured'] ?? [];
        $sectionCategories = $settings['section_categories'] ?? [];

        // 0. All Products Section (New)
        $allFeaturedIds = $sectionFeatured['all_products']['ids'] ?? [];
        $products = !empty($allFeaturedIds)
            ? $this->productModel->getProductsByIds($allFeaturedIds, $sectionFeatured['all_products']['orders'] ?? [])
            : [];

        $allBanners = $this->productModel->getBanners('all_products');
        $allBanner = !empty($allBanners) ? $allBanners[0]->image : 'hero_banner_full.png';

        // 1. Handmade Section - lấy sản phẩm đã tích chọn
        $handmadeFeaturedIds = $sectionFeatured['handmade']['ids'] ?? [];
        $handmadeProducts = !empty($handmadeFeaturedIds)
            ? $this->productModel->getProductsByIds($handmadeFeaturedIds, $sectionFeatured['handmade']['orders'] ?? [])
            : [];
        $handmadeBanners = $this->productModel->getBanners('handmade');
        $handmadeBanner = !empty($handmadeBanners) ? $handmadeBanners[0]->image : 'featured_handmade.png';

        // 2. Tools Section
        $toolsFeaturedIds = $sectionFeatured['tools']['ids'] ?? [];
        $toolsProducts = !empty($toolsFeaturedIds)
            ? $this->productModel->getProductsByIds($toolsFeaturedIds, $sectionFeatured['tools']['orders'] ?? [])
            : [];
        $toolsBanners = $this->productModel->getBanners('tools');
        $toolsBanner = !empty($toolsBanners) ? $toolsBanners[0]->image : 'tools_banner_placeholder.png';

        // 3. Flowers Section
        $flowersFeaturedIds = $sectionFeatured['flowers']['ids'] ?? [];
        $flowersProducts = !empty($flowersFeaturedIds)
            ? $this->productModel->getProductsByIds($flowersFeaturedIds, $sectionFeatured['flowers']['orders'] ?? [])
            : [];
        $flowersBanners = $this->productModel->getBanners('flowers');
        $flowersBanner = !empty($flowersBanners) ? $flowersBanners[0]->image : 'flowers_banner_placeholder.png';

        // 4. Yarn Section
        $yarnFeaturedIds = $sectionFeatured['yarn']['ids'] ?? [];
        $yarnProducts = !empty($yarnFeaturedIds)
            ? $this->productModel->getProductsByIds($yarnFeaturedIds, $sectionFeatured['yarn']['orders'] ?? [])
            : [];
        $yarnBanners = $this->productModel->getBanners('yarn');
        $yarnBanner = !empty($yarnBanners) ? $yarnBanners[0]->image : 'yarn_banner_placeholder.png';

        $search_title = "";
        $breadcrumbs = null;
        $show_sidebar = false;
        $is_home = true;

        include 'app/views/product/list.php';
    }
    public function category($id)
    {
        $maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;
        $products = $this->productModel->getProductsByCategory($id, null, $maxPrice);
        $category = (new CategoryModel($this->db))->getCategoryById($id);
        $search_title = $category ? $category->name : "Danh mục";
        $banners = $this->productModel->getBanners();
        $systemMaxPrice = $this->productModel->getMaxPrice();

        $breadcrumbs = [
            'Trang chủ' => BASE_URL . 'index.php?url=Product/'
        ];

        $groupInfo = $this->getGroupInfoByCategory($search_title);
        if ($groupInfo) {
            $breadcrumbs[$groupInfo['label']] = $groupInfo['link'];
        } else {
            $breadcrumbs['Sản phẩm'] = BASE_URL . 'index.php?url=Product/';
        }

        $breadcrumbs[$search_title] = '#';
        $current_url = "Product/category/$id";
        $show_sidebar = true;

        include 'app/views/product/list.php';
    }
    public function show($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }
        require_once 'app/models/ReviewModel.php';
        require_once 'app/helpers/SessionHelper.php';

        $product = $this->productModel->getProductById($id);
        if ($product) {
            $reviewModel = new ReviewModel($this->db);
            $reviews = $reviewModel->getReviewsByProductId($id);

            $user_id = $_SESSION['user_id'] ?? null;
            $hasReviewed = false;
            if ($user_id) {
                $hasReviewed = $reviewModel->hasUserReviewed($id, $user_id);
            }

            $variantModel = new VariantModel($this->db);
            $variants = $variantModel->getVariantsByProductId($id);

            // Tìm giá rẻ nhất hinh hiển thị mặc định
            $minPrice = $product->price;
            $hasVariants = !empty($variants);
            if ($hasVariants) {
                foreach ($variants as $v) {
                    if ($v->price > 0 && ($v->price < $minPrice || $minPrice == 0)) {
                        $minPrice = $v->price;
                    }
                }
            }

            $breadcrumbs = [
                'Trang chủ' => BASE_URL . 'index.php?url=Product/'
            ];

            if (!empty($product->category_name)) {
                $groupInfo = $this->getGroupInfoByCategory($product->category_name);
                if ($groupInfo) {
                    $breadcrumbs[$groupInfo['label']] = $groupInfo['link'];
                }
                $breadcrumbs[$product->category_name] = BASE_URL . 'index.php?url=Product/category/' . ($product->category_id ?? '');
            } else {
                $breadcrumbs['Sản phẩm'] = BASE_URL . 'index.php?url=Product/all';
            }
            $breadcrumbs[$product->name] = '#';

            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }
    public function add()
    {
        $this->restrictToStaff();
        $categories = (new CategoryModel($this->db))->getCategories();
        include_once 'app/views/product/add.php';
    }
    public function save()
    {
        $this->restrictToStaff();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            // Lọc chỉ các ký tự số từ chuỗi nhập vào (ví dụ "650.000 đ" -> "650000")
            $price_raw = $_POST['price'] ?? '0';
            $price = (int) preg_replace('/[^\d]/', '', $price_raw);

            $errors = [];
            if ($price <= 0) {
                $errors[] = "Lỗi: Giá sản phẩm phải là số nguyên và lớn hơn 0.";
            }
            if (empty(trim($name))) {
                $errors[] = "Lỗi: Tên sản phẩm không được để trống.";
            }

            if (!empty($errors)) {
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
                return;
            }

            $category_id = $_POST['category_id'] ?? null;
            $stock = $_POST['stock'] ?? 0;
            $sold = $_POST['sold'] ?? 0;
            $rating = $_POST['rating'] ?? 0.0;
            $discount_percent = isset($_POST['discount_percent']) ? (int)$_POST['discount_percent'] : 0;
            $location = $_POST['location'] ?? 'Tp. Hồ Chí Minh';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $image = "";
                }
            } else {
                $image = "";
            }
            $user_id = $_SESSION['user_id'] ?? 1;
            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image, $stock, $sold, $rating, $discount_percent, $location, $user_id);

            if (!is_array($result) && $result > 0) {
                $productId = $result;

                // XỬ LÝ PHÂN LOẠI (VARIANTS) NẾU CÓ
                if (isset($_POST['variant_names']) && !empty($_POST['variant_names'])) {
                    require_once 'app/models/VariantModel.php';
                    $vModel = new VariantModel($this->db);

                    foreach ($_POST['variant_names'] as $index => $vName) {
                        if (!empty(trim($vName))) {
                            $vPrice_raw = $_POST['variant_prices'][$index] ?? '0';
                            $vPrice = (int) preg_replace('/[^\d]/', '', $vPrice_raw);
                            $vStock = isset($_POST['variant_stocks'][$index]) ? (int)$_POST['variant_stocks'][$index] : 0;
                            $vImg = "";

                            // Xử lý upload ảnh cho từng variant
                            if (isset($_FILES['variant_images']['name'][$index]) && $_FILES['variant_images']['error'][$index] == 0) {
                                $fileData = [
                                    'name'     => $_FILES['variant_images']['name'][$index],
                                    'type'     => $_FILES['variant_images']['type'][$index],
                                    'tmp_name' => $_FILES['variant_images']['tmp_name'][$index],
                                    'error'    => $_FILES['variant_images']['error'][$index],
                                    'size'     => $_FILES['variant_images']['size'][$index]
                                ];
                                try {
                                    $vImg = $this->uploadImage($fileData);
                                } catch (Exception $e) {
                                }
                            }

                            // Chỉ lưu nếu có tên mẫu
                            if (!empty(trim($vName))) {
                                $vModel->addVariant($productId, $vName, $vImg, $vPrice, $vStock);
                            }
                        }
                    }
                }

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'seller') {
                    // Gửi thông báo cho Admin (User ID 1)
                    require_once 'app/models/NotificationModel.php';
                    $nModel = new NotificationModel($this->db);
                    $nModel->addNotification(1, "Sản phẩm mới '" . $name . "' từ người bán đang chờ phê duyệt.", "index.php?url=Dashboard/products");
                    
                    $_SESSION['success_message'] = "Sản phẩm đã được gửi và đang chờ Admin phê duyệt.";
                    header('Location: ' . BASE_URL . 'index.php?url=Seller/pendingProducts');
                } else {
                    $_SESSION['success_message'] = "Sản phẩm đã được thêm thành công.";
                    header('Location: ' . BASE_URL . 'index.php?url=Product');
                }
                exit;
            } elseif (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                $_SESSION['error_message'] = "Đã xảy ra lỗi khi lưu sản phẩm.";
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'seller') {
                    header('Location: ' . BASE_URL . 'index.php?url=Product/myProducts');
                } else {
                    header('Location: ' . BASE_URL . 'index.php?url=Product');
                }
                exit;
            }
        }
    }
    public function edit($id)
    {
        $this->restrictToStaff();
        $product = $this->productModel->getProductById($id);

        // Security check: Only the owner can edit the product
        if (!isset($product->user_id) || $product->user_id != $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Bạn không có quyền chỉnh sửa sản phẩm này.";
            $redirect = ($_SESSION['user_role'] === 'admin') ? 'Dashboard/products' : 'Product';
            header('Location: ' . BASE_URL . 'index.php?url=' . $redirect);
            exit;
        }
        $categories = (new CategoryModel($this->db))->getCategories();

        require_once 'app/models/VariantModel.php';
        $variantModel = new VariantModel($this->db);
        $variants = $variantModel->getVariantsByProductId($id);

        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }
    public function update()
    {
        $this->restrictToStaff();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $product = $this->productModel->getProductById($id);

            // Security check: Only the owner can update the product
            if (!isset($product->user_id) || $product->user_id != $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Bạn không có quyền cập nhật sản phẩm này.";
                $redirect = ($_SESSION['user_role'] === 'admin') ? 'Dashboard/products' : 'Product';
                header('Location: ' . BASE_URL . 'index.php?url=' . $redirect);
                exit;
            }
            $name = $_POST['name'];
            $description = $_POST['description'];

            // Lọc chỉ ký tự số
            $price_raw = $_POST['price'] ?? '0';
            $price = (int) preg_replace('/[^\d]/', '', $price_raw);

            $errors = [];
            if ($price <= 0) {
                $errors[] = "Lỗi: Giá sản phẩm phải là số nguyên và lớn hơn 0.";
            }

            $category_id = $_POST['category_id'];
            $stock = $_POST['stock'] ?? 0;
            $sold = $_POST['sold'] ?? 0;
            $rating = $_POST['rating'] ?? 0.0;
            $discount_percent = isset($_POST['discount_percent']) ? (int)$_POST['discount_percent'] : 0;
            $location = $_POST['location'] ?? 'Tp. Hồ Chí Minh';

            $remove_existing = $_POST['remove_existing_image'] ?? '0';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    // Nếu lỗi khi chọn file mới, xem user có gửi lệnh xóa file cũ hay không
                    $image = ($remove_existing === '1') ? '' : ($_POST['existing_image'] ?? '');
                }
            } else {
                // Nếu ko có file mới upload, check xem user có bấm xóa ảnh cũ hay ko
                if ($remove_existing === '1') {
                    $image = '';
                } else {
                    $image = $_POST['existing_image'] ?? '';
                }
            }
            $status = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'approved' : 'pending';
            $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image, $stock, $sold, $rating, $discount_percent, $location, $status);
            if ($edit) {
                require_once 'app/models/VariantModel.php';
                $vModel = new VariantModel($this->db);

                // 1. Xóa các variant bị user xóa trong form
                if (!empty($_POST['deleted_variant_ids'])) {
                    $dIds = explode(',', $_POST['deleted_variant_ids']);
                    foreach ($dIds as $dVId) {
                        if ((int)$dVId > 0) {
                            $vModel->deleteVariant((int)$dVId);
                        }
                    }
                }

                // 2. Cập nhật các variant đang có
                if (isset($_POST['existing_variant_ids'])) {
                    foreach ($_POST['existing_variant_ids'] as $vId) {
                        $vName = $_POST['existing_variant_names'][$vId] ?? '';
                        $vPrice_raw = $_POST['existing_variant_prices'][$vId] ?? '0';
                        $vPrice = (int) preg_replace('/[^\d]/', '', $vPrice_raw);
                        $vStock = $_POST['existing_variant_stocks'][$vId] ?? 0;

                        $vImg = null;
                        if (isset($_FILES['existing_variant_images']['name'][$vId]) && $_FILES['existing_variant_images']['error'][$vId] == 0) {
                            $fileData = [
                                'name'     => $_FILES['existing_variant_images']['name'][$vId],
                                'type'     => $_FILES['existing_variant_images']['type'][$vId],
                                'tmp_name' => $_FILES['existing_variant_images']['tmp_name'][$vId],
                                'error'    => $_FILES['existing_variant_images']['error'][$vId],
                                'size'     => $_FILES['existing_variant_images']['size'][$vId]
                            ];
                            try {
                                $vImg = $this->uploadImage($fileData);
                            } catch (Exception $e) {
                            }
                        }

                        $vModel->updateVariant($vId, $vName, $vImg, $vPrice, $vStock);
                    }
                }

                // 3. Thêm các mẫu mới
                if (isset($_POST['new_variant_names'])) {
                    foreach ($_POST['new_variant_names'] as $index => $vName) {
                        if (!empty(trim($vName))) {
                            $vPrice_raw = $_POST['new_variant_prices'][$index] ?? '0';
                            $vPrice = (int) preg_replace('/[^\d]/', '', $vPrice_raw);
                            $vStock = $_POST['new_variant_stocks'][$index] ?? 0;
                            $vImg = "";

                            if (isset($_FILES['new_variant_images']['name'][$index]) && $_FILES['new_variant_images']['error'][$index] == 0) {
                                $fileData = [
                                    'name'     => $_FILES['new_variant_images']['name'][$index],
                                    'type'     => $_FILES['new_variant_images']['type'][$index],
                                    'tmp_name' => $_FILES['new_variant_images']['tmp_name'][$index],
                                    'error'    => $_FILES['new_variant_images']['error'][$index],
                                    'size'     => $_FILES['new_variant_images']['size'][$index]
                                ];
                                try {
                                    $vImg = $this->uploadImage($fileData);
                                } catch (Exception $e) {
                                }
                            }

                            // Chỉ lưu nếu có tên mẫu
                            if (!empty(trim($vName))) {
                                $vModel->addVariant($id, $vName, $vImg, $vPrice, $vStock);
                            }
                        }
                    }
                }

                $_SESSION['success_message'] = ($_SESSION['user_role'] === 'admin') ? "Sản phẩm đã được cập nhật thành công." : "Sản phẩm đã được cập nhật và đang chờ Admin duyệt lại.";
                $redirectUrl = ($_SESSION['user_role'] === 'admin') ? 'Dashboard/products' : 'Product/myProducts';
                header('Location: ' . BASE_URL . 'index.php?url=' . $redirectUrl);
                exit;
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }
    public function delete($id)
    {
        $this->restrictToStaff();
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            $_SESSION['error_message'] = "Không tìm thấy sản phẩm.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Security check: Only the owner or Admin can delete the product
        $isOwner = (isset($product->user_id) && $product->user_id == $_SESSION['user_id']);
        $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

        if (!$isOwner && !$isAdmin) {
            $_SESSION['error_message'] = "Bạn không có quyền xóa sản phẩm này.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Case 1: Admin is deleting someone else's product (must provide reason)
        if ($isAdmin && !$isOwner) {
            $reason = $_POST['rejection_reason'] ?? 'Sản phẩm vi phạm chính sách.';
            if ($this->productModel->updateProductStatus($id, 'rejected', $reason)) {
                $_SESSION['success_message'] = "Sản phẩm của Seller đã được gỡ bỏ kèm lý do.";
            } else {
                $_SESSION['error_message'] = "Đã xảy ra lỗi khi gỡ bỏ sản phẩm.";
            }
        } 
        // Case 2: Owner (or Admin deleting their own product) - Hard delete
        else {
            if ($this->productModel->deleteProduct($id)) {
                $_SESSION['success_message'] = "Sản phẩm đã được xóa thành công.";
            } else {
                $_SESSION['error_message'] = "Đã xảy ra lỗi khi xóa sản phẩm.";
            }
        }
        
        $redirectUrl = ($_SESSION['user_role'] === 'admin') ? 'Dashboard/products' : 'Product/myProducts';
        header('Location: ' . BASE_URL . 'index.php?url=' . $redirectUrl);
        exit;
    }
    private function uploadImage($file)
    {
        $target_dir = "public/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        // Sinh tên file duy nhất tránh trùng lặp ghi đè ảnh cũ và lỗi trình duyệt cache ảnh
        $newFileName = uniqid('prod_') . '_' . time() . '.' . $imageFileType;
        $target_file = $target_dir . $newFileName;

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG, GIF, WEBP.");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $newFileName;
    }
    public function addToCart($id)
    {
        header('Location: ' . BASE_URL . 'index.php?url=Cart/add/' . $id);
        exit;
    }
    public function list()
    {
        $products = $this->productModel->getProducts();
        require_once 'app/views/product/list.php';
    }

    public function uploadBanner()
    {
        $this->restrictToAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_images'])) {
            $files = $_FILES['banner_images'];
            $target_dir = "public/images/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === 0) {
                    $filename = time() . '_' . basename($files['name'][$i]);
                    $target_file = $target_dir . $filename;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    $check = getimagesize($files['tmp_name'][$i]);
                    $is_image = ($check !== false && in_array($imageFileType, ["jpg", "jpeg", "png", "gif"]));
                    $is_pdf = ($imageFileType === 'pdf');

                    if ($is_image || $is_pdf) {
                        if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                            $this->productModel->addBanner($filename);
                        }
                    }
                }
            }
            $_SESSION['success_message'] = "Banners đã được cập nhật!";
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?url=Product');
        header('Location: ' . $redirect);
        exit;
    }

    public function uploadBannerQR($id)
    {
        $this->restrictToAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_image'])) {
            $file = $_FILES['qr_image'];
            if ($file['error'] === 0) {
                $target_dir = "public/images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $filename = 'qr_' . time() . '_' . basename($file['name']);
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $this->productModel->updateBannerQR($id, $filename);
                    $_SESSION['success_message'] = "Mã QR đã được cập nhật!";
                }
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?url=Dashboard/banners')));
        exit;
    }

    public function deleteBannerQR($id)
    {
        $this->restrictToAdmin();
        $banner = $this->productModel->getBannerById($id);
        if ($banner && $banner->qr_image) {
            $file_path = "public/images/" . $banner->qr_image;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            $this->productModel->updateBannerQR($id, null);
            $_SESSION['success_message'] = "Mã QR đã được xóa!";
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?url=Dashboard/banners')));
        exit;
    }

    public function updateBannerQRPosition($id)
    {
        $this->restrictToAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_position'])) {
            $position = $_POST['qr_position'];
            $this->productModel->updateBannerQRPosition($id, $position);
            $_SESSION['success_message'] = "Vị trí mã QR đã được cập nhật!";
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?url=Dashboard/banners')));
        exit;
    }

    public function deleteBanner($id)
    {
        $this->restrictToAdmin();
        $banner = $this->productModel->getBannerById($id);

        if ($banner) {
            $file_path = "public/images/" . $banner->image;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            $this->productModel->deleteBanner($id);
            $_SESSION['success_message'] = "Đã xóa banner.";
        } else {
            $_SESSION['error_message'] = "Không tìm thấy banner để xóa.";
        }
        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?url=Product');
        header('Location: ' . $redirect);
        exit;
    }

    public function submitReview()
    {
        require_once 'app/models/ReviewModel.php';
        require_once 'app/helpers/SessionHelper.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $comment = $_POST['comment'] ?? '';
            $user_id = $_SESSION['user_id'] ?? null;

            if ($product_id && $rating && $user_id) {
                $reviewModel = new ReviewModel($this->db);
                // Check again to prevent duplicate post
                if (!$reviewModel->hasUserReviewed($product_id, $user_id)) {
                    $success = $reviewModel->addReview($product_id, $user_id, $rating, $comment);
                    if ($success) {
                        $this->productModel->updateProductRating($product_id);
                        $_SESSION['success_message'] = "Cảm ơn bạn đã đánh giá sản phẩm!";
                    }
                }
            }
            header('Location: ' . BASE_URL . 'index.php?url=Product/show/' . $product_id);
            exit;
        }
    }

    public function all()
    {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $minPrice = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (int)$_GET['min_price'] : null;
        $maxPrice = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (int)$_GET['max_price'] : null;
        $seller_id = isset($_GET['seller_id']) ? (int)$_GET['seller_id'] : null;

        $products = $this->productModel->searchProductsFiltered('', $minPrice, $maxPrice, $seller_id, $sort);

        $systemMaxPrice = $this->productModel->getMaxPrice();
        if ($maxPrice === null) $maxPrice = $systemMaxPrice;

        $search_title = "Khám phá toàn bộ sản phẩm";

        $breadcrumbs = [
            'Trang chủ' => BASE_URL . 'index.php?url=Product/',
            'Toàn bộ sản phẩm' => '#'
        ];

        $current_url = 'Product/all' . ($seller_id ? '&seller_id=' . $seller_id : '');
        if ($minPrice !== null) $current_url .= '&min_price=' . $minPrice;
        if ($maxPrice !== null) $current_url .= '&max_price=' . $maxPrice;
        if ($sort !== 'newest') $current_url .= '&sort=' . $sort;
        $banners = $this->productModel->getBanners();
        $show_sidebar = true;
        require_once 'app/views/product/list.php';
    }

    public function seller($id)
    {
        $id = (int)$id;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $minPrice = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (int)$_GET['min_price'] : null;
        $maxPrice = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (int)$_GET['max_price'] : null;

        $products = $this->productModel->searchProductsFiltered('', $minPrice, $maxPrice, $id, $sort);
        $systemMaxPrice = $this->productModel->getMaxPrice();
        if ($maxPrice === null) $maxPrice = $systemMaxPrice;

        $userModel = new UserModel($this->db);
        $user = $userModel->getUserById($id);
        $search_title = $user ? "Sản phẩm của: " . $user->name : "Sản phẩm của người bán";

        $breadcrumbs = [
            'Trang chủ' => BASE_URL . 'index.php?url=Product/',
            'Người bán' => '#'
        ];

        $current_url = "Product/seller/$id";
        if ($minPrice !== null) $current_url .= '&min_price=' . $minPrice;
        if ($maxPrice !== null) $current_url .= '&max_price=' . $maxPrice;
        if ($sort !== 'newest') $current_url .= '&sort=' . $sort;
        $banners = $this->productModel->getBanners();
        $show_sidebar = true;
        require_once 'app/views/product/list.php';
    }

    public function search()
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $minPrice = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (int)$_GET['min_price'] : null;
        $maxPrice = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (int)$_GET['max_price'] : null;

        $products = $this->productModel->searchProductsFiltered($keyword, $minPrice, $maxPrice);

        $systemMaxPrice = $this->productModel->getMaxPrice();
        if ($maxPrice === null) $maxPrice = $systemMaxPrice;

        $search_title = $keyword !== '' ? "Kết quả tìm kiếm cho: '" . htmlspecialchars($keyword) . "'" : "Tất cả sản phẩm";
        $is_search_page = true; // Still keep this for specific search logic if needed

        $breadcrumbs = [
            'Trang chủ' => BASE_URL . 'index.php?url=Product/',
            'Tìm kiếm' => '#'
        ];

        $current_url = 'Product/search';
        $banners = $this->productModel->getBanners();
        $show_sidebar = true;
        require_once 'app/views/product/list.php';
    }

    public function myProducts()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }
        $allProducts = $this->productModel->getProductsBySeller($_SESSION['user_id']);
        
        // Chỉ hiện sản phẩm đã được duyệt trong mục "Sản phẩm của tôi"
        $products = array_filter($allProducts, function($p) {
            return $p->status === 'approved';
        });
        
        require_once 'app/views/product/my_products.php';
    }

    public function searchAjax()
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        if ($keyword === '') {
            echo json_encode([]);
            return;
        }

        $products = $this->productModel->searchProducts($keyword);

        $results = [];
        foreach ($products as $product) {
            $pImg = $product->image;
            $finalPImg = (strpos($pImg, 'public/') === false) ?
                ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                $pImg;

            $results[] = [
                'id' => $product->id,
                'name' => htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'),
                'price' => number_format($product->price, 0, ',', '.') . ' ₫',
                'old_price' => $product->price >= 200000 ? number_format($product->price * 1.2, 0, ',', '.') . ' ₫' : null,
                'image' => BASE_URL . $finalPImg,
                'url' => BASE_URL . 'index.php?url=Product/show/' . $product->id
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($results);
    }
    public function group($type)
    {
        $groupNames = [];
        $title = "Sản phẩm";
        switch ($type) {
            case 'handmade':
                $groupNames = ['Búp bê len', 'Thú bông len', 'Túi len', 'Nón, khăn len', 'Đồ gia dụng handmade'];
                $title = "Sản Phẩm Handmade";
                break;
            case 'tools':
                $groupNames = ['Kim móc các loại', 'Kim đan các loại', 'Phụ kiện hỗ trợ đan móc len', 'Dụng cụ đan – móc'];
                $title = "Dụng cụ Đan - Móc";
                break;
            case 'flowers':
                $groupNames = ['Hoa lẻ', 'Hoa bó', 'Hoa mix ngẫu nhiên'];
                $title = "Hoa Len";
                break;
            case 'yarn':
                $groupNames = ['Sợi tự nhiên', 'Sợi tổng hợp', 'Sợi chuyên móc thú bông'];
                $title = "Len - Sợi";
                break;
            case 'keychain':
                $groupNames = ['Móc khóa', 'Móc khoá', 'Móc khóa len', 'Móc Khóa'];
                $title = "Móc Khóa";
                break;
        }
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $minPrice = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (int)$_GET['min_price'] : null;
        $maxPrice = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (int)$_GET['max_price'] : null;
        $products = $this->productModel->getProductsByCategoryNames($groupNames, $minPrice, $maxPrice, $sort);
        $search_title = $title;
        $systemMaxPrice = $this->productModel->getMaxPrice();
        $banners = $this->productModel->getBanners();

        $breadcrumbs = [
            'Trang chủ' => BASE_URL . 'index.php?url=Product/',
            $title => '#'
        ];
        $current_url = "Product/group/$type";
        if ($minPrice !== null) $current_url .= '&min_price=' . $minPrice;
        if ($maxPrice !== null) $current_url .= '&max_price=' . $maxPrice;
        if ($sort !== 'newest') $current_url .= '&sort=' . $sort;
        $show_sidebar = true;

        require_once 'app/views/product/list.php';
    }

    public function addVariant()
    {
        $this->restrictToAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $name = $_POST['variant_name'];
            $price_raw = $_POST['variant_price'] ?? '0';
            $stock = $_POST['variant_stock'] ?? 0;

            // Xóa ký tự không phải số trong giá
            $price = (int) preg_replace('/[^\d]/', '', $price_raw);

            if (isset($_FILES['variant_image']) && $_FILES['variant_image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['variant_image']);
                    $variantModel = new VariantModel($this->db);
                    $variantModel->addVariant($product_id, $name, $image, $price, $stock);
                    $_SESSION['success_message'] = "Đã thêm mẫu mới!";
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Lỗi: " . $e->getMessage();
                }
            } else {
                $_SESSION['error_message'] = "Vui lòng chọn hình ảnh cho mẫu.";
            }
            header('Location: ' . BASE_URL . 'index.php?url=Product/show/' . $product_id);
            exit;
        }
    }

    public function deleteVariant($id)
    {
        $this->restrictToAdmin();
        $variantModel = new VariantModel($this->db);
        $variant = $variantModel->getVariantById($id);

        if ($variant) {
            $product_id = $variant->product_id;
            $file_path = "public/uploads/" . $variant->image;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            $variantModel->deleteVariant($id);
            $_SESSION['success_message'] = "Đã xóa mẫu.";
            header('Location: ' . BASE_URL . 'index.php?url=Product/show/' . $product_id);
        } else {
            $_SESSION['error_message'] = "Không tìm thấy mẫu.";
            header('Location: ' . BASE_URL . 'index.php?url=Product');
        }
        exit;
    }

    public function updateSectionBanner($section)
    {
        $this->restrictToAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_image'])) {
            $image = $_FILES['banner_image'];
            $targetDir = 'public/images/';

            // Generate unique name
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = "banner_" . $section . "_" . time() . "." . $extension;
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                $this->productModel->updateSectionBanner($section, $fileName);
                $_SESSION['success_message'] = "Đã cập nhật banner cho mục " . $section;
            } else {
                $_SESSION['error_message'] = "Không thể tải ảnh lên.";
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Product/#' . $section);
        exit;
    }

    public function deleteSectionBanner($section)
    {
        $this->restrictToAdmin();
        if ($this->productModel->deleteBannerBySection($section)) {
            $_SESSION['success_message'] = "Đã xóa banner cho mục " . $section . " thành công!";
        } else {
            $_SESSION['error_message'] = "Mục này hiện đang dùng ảnh mặc định, không có gì để xóa.";
        }
        header('Location: ' . BASE_URL . 'index.php?url=Product/#' . $section);
        exit;
    }

    public function manageFeatured($section_id = 'all_products')
    {
        $this->restrictToAdmin();
        $allProducts = $this->productModel->getProducts(); // Lấy tất cả để tích chọn

        // Load all categories from DB
        $allCategories = (new CategoryModel($this->db))->getCategories();

        $defaultTitles = [
            'all_products' => 'TẤT CẢ SẢN PHẨM',
            'handmade' => 'SẢN PHẨM HANDMADE',
            'tools' => 'DỤNG CỤ ĐAN - MÓC',
            'flowers' => 'HOA LEN NGHỆ THUẬT',
            'yarn' => 'LEN - SỢI CAO CẤP'
        ];
        $currentTitle = isset($defaultTitles[$section_id]) ? $defaultTitles[$section_id] : 'Quản Lý Sản Phẩm Trưng Bày';

        // Load saved settings for this section
        $selectedCategoryNames = [];
        $featuredIds = [];
        $featuredOrders = [];
        if (file_exists('app/config/settings.json')) {
            $settings = json_decode(file_get_contents('app/config/settings.json'), true);
            if (!empty($settings['section_titles'][$section_id])) {
                $currentTitle = $settings['section_titles'][$section_id];
            }
            if (!empty($settings['section_categories'][$section_id])) {
                $selectedCategoryNames = $settings['section_categories'][$section_id];
            }
            // Đọc sản phẩm featured theo section
            if (!empty($settings['section_featured'][$section_id])) {
                $featuredIds = $settings['section_featured'][$section_id]['ids'] ?? [];
                $featuredOrders = $settings['section_featured'][$section_id]['orders'] ?? [];
            }
        }

        include 'app/views/product/manage_featured.php';
    }

    public function saveFeatured($section_id = 'all_products')
    {
        $this->restrictToAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $featured_ids = isset($_POST['featured_ids']) ? $_POST['featured_ids'] : [];
            $orders = isset($_POST['orders']) ? $_POST['orders'] : [];

            $dir = 'app/config/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $settings = [];
            if (file_exists($dir . 'settings.json')) {
                $settings = json_decode(file_get_contents($dir . 'settings.json'), true) ?? [];
            }

            // Lưu tên Tiêu đề trang
            if (isset($_POST['page_title'])) {
                if (!isset($settings['section_titles'])) {
                    $settings['section_titles'] = [];
                }
                $settings['section_titles'][$section_id] = trim($_POST['page_title']);
            }

            // Lưu danh mục đã chọn cho section này
            if (isset($_POST['section_category_names'])) {
                if (!isset($settings['section_categories'])) {
                    $settings['section_categories'] = [];
                }
                $settings['section_categories'][$section_id] = $_POST['section_category_names'];
            } else {
                // Nếu không có danh mục nào được chọn, xóa cấu hình cũ
                if (isset($settings['section_categories'][$section_id])) {
                    unset($settings['section_categories'][$section_id]);
                }
            }

            // Lưu sản phẩm featured THEO SECTION (không global nữa)
            if (!isset($settings['section_featured'])) {
                $settings['section_featured'] = [];
            }

            // Tạo mảng orders cho section này
            $sectionOrders = [];
            foreach ($featured_ids as $id) {
                $sectionOrders[$id] = isset($orders[$id]) ? (int)$orders[$id] : 0;
            }

            $settings['section_featured'][$section_id] = [
                'ids' => $featured_ids,
                'orders' => $sectionOrders
            ];

            file_put_contents($dir . 'settings.json', json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            $_SESSION['success_message'] = "Đã lưu thiết lập trưng bày cho mục này.";
        }
        header('Location: ' . BASE_URL . 'index.php?url=Product/manageFeatured/' . $section_id);
        exit;
    }

    public function updateSectionTitleAjax()
    {
        $this->restrictToAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $section_id = $_POST['section_id'] ?? '';
            $new_title = trim($_POST['title'] ?? '');

            if ($section_id && $new_title) {
                $dir = 'app/config/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);

                $settings = [];
                if (file_exists($dir . 'settings.json')) {
                    $settings = json_decode(file_get_contents($dir . 'settings.json'), true) ?? [];
                }

                if (!isset($settings['section_titles'])) {
                    $settings['section_titles'] = [];
                }
                $settings['section_titles'][$section_id] = $new_title;

                if (file_put_contents($dir . 'settings.json', json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
                    echo json_encode(['success' => true, 'message' => 'Đã cập nhật tiêu đề']);
                    exit;
                }
            }
        }
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật']);
        exit;
    }
    public function likeAjax($id)
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thích sản phẩm']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->productModel->toggleLike($id, $userId);

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xử lý lượt thích']);
        }
        exit;
    }
}
