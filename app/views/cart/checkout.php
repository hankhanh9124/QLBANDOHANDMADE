<?php include 'app/views/shares/header.php'; ?>

<?php
// Logic chuẩn bị dữ liệu
$selectedAddr = null;
if (!empty($addresses)) {
    foreach ($addresses as $addr) {
        if ($addr->is_default) {
            $selectedAddr = $addr;
            break;
        }
    }
    if (!$selectedAddr && !empty($addresses)) $selectedAddr = $addresses[0];
}

$checkoutItems = [];
$subtotal = 0;
if (isset($_SESSION['cart']) && !empty($selectedIds)) {
    foreach ($selectedIds as $id) {
        if (isset($_SESSION['cart'][$id])) {
            $checkoutItems[$id] = $_SESSION['cart'][$id];
            $subtotal += $_SESSION['cart'][$id]['price'] * $_SESSION['cart'][$id]['quantity'];
        }
    }
}
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/checkout.css?v=<?php echo time(); ?>">

<div class="checkout-header">
    <div class="container">
        <div class="logo-container">
            <a href="<?php echo BASE_URL; ?>index.php?url=Product/">
                <img src="<?php echo BASE_URL; ?>public/images/logolen.jpg" alt="Logo" style="height: 50px;">
            </a>
            <span class="logo-text">Thanh Toán</span>
        </div>
    </div>
</div>

<div class="container pb-5">
    <form id="checkoutForm" action="<?php echo BASE_URL; ?>index.php?url=Cart/processCheckout" method="POST">
        <!-- Address Section -->
        <div class="checkout-card address-section primary-theme-top">
            <div class="address-title">
                <i class="fas fa-map-marker-alt"></i> Địa Chỉ Nhận Hàng
            </div>
            <div class="address-content">
                <div class="address-info" id="selected-address-display">
                    <?php if ($selectedAddr): ?>
                        <input type="hidden" name="selected_address_id" id="final_selected_address_id" value="<?php echo $selectedAddr->id; ?>">
                        <b><span id="display-name"><?php echo htmlspecialchars($selectedAddr->name); ?></span> <span id="display-phone"><?php echo htmlspecialchars($selectedAddr->phone); ?></span></b>
                        <span class="ml-3" id="display-address">
                            <?php echo htmlspecialchars($selectedAddr->address_line); ?>, 
                            <?php echo htmlspecialchars($selectedAddr->ward); ?>, 
                            <?php echo htmlspecialchars($selectedAddr->district); ?>, 
                            <span id="display-city"><?php echo htmlspecialchars($selectedAddr->city); ?></span>
                        </span>
                        <input type="hidden" id="initial_district" value="<?php echo htmlspecialchars($selectedAddr->district); ?>">
                        <input type="hidden" id="initial_city" value="<?php echo htmlspecialchars($selectedAddr->city); ?>">
                        <?php if ($selectedAddr->is_default): ?>
                            <span class="badge badge-outline-primary ml-2" style="color: var(--primary-theme); border: 1px solid var(--primary-theme); font-size: 10px; padding: 1px 4px;">Mặc định</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-danger">Vui lòng thêm địa chỉ nhận hàng để tiếp tục</div>
                        <input type="hidden" name="selected_address_id" id="final_selected_address_id" value="">
                    <?php endif; ?>
                </div>
                <div class="address-change-btn" data-toggle="modal" data-target="#changeAddressModal">Thay đổi</div>
            </div>
        </div>

        <!-- Product List -->
        <div class="checkout-card">
            <div class="product-table-header">
                <div style="flex: 4;">Sản phẩm</div>
                <div style="flex: 2; text-align: center;">Loại</div>
                <div style="flex: 1; text-align: center;">Đơn giá</div>
                <div style="flex: 1; text-align: center;">Số lượng</div>
                <div style="flex: 1; text-align: right;">Thành tiền</div>
            </div>
            
            <?php foreach ($checkoutItems as $id => $item): ?>
                <div class="product-item">
                    <div class="product-info">
                        <?php 
                        $itemImg = $item['image'];
                        $finalItemImg = (strpos($itemImg, 'public/') === false) ?
                            ((strpos($itemImg, 'uploads/') !== false) ? 'public/' . $itemImg : 'public/uploads/' . $itemImg) :
                            $itemImg;
                        ?>
                        <img src="<?php echo BASE_URL . $finalItemImg; ?>" class="product-img" alt="">
                        <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    </div>
                    <div class="product-variant">
                        <?php echo !empty($item['variant_name']) ? htmlspecialchars($item['variant_name']) : 'Mặc định'; ?>
                    </div>
                    <div class="product-price">
                        ₫<?php echo number_format($item['price'], 0, ',', '.'); ?>
                    </div>
                    <div class="product-qty">
                        <?php echo $item['quantity']; ?>
                    </div>
                    <div class="product-subtotal">
                        ₫<?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Message to Shop -->
            <div style="padding: 20px 30px; background: #fdfdfd; border-top: 1px dashed rgba(0,0,0,.09); display: flex; align-items: center;">
                <div style="font-size: 14px; color: var(--text-22); margin-right: 20px;">Lời nhắn:</div>
                <input type="text" name="order_note" placeholder="Lưu ý cho Người bán..." style="flex: 1; border: 1px solid var(--border-ebeb); padding: 8px 15px; font-size: 14px; outline: none;">
            </div>

            <!-- Shipping Selection -->
            <div style="padding: 20px 30px; background: #f6fffe; border-top: 1px dashed rgba(0,0,0,.09); display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <div style="color: #00bfa5; margin-right: 15px;"><i class="fas fa-truck"></i> Đơn vị vận chuyển:</div>
                    <div>
                        <div style="font-weight: 500; font-size: 14px;">Vận chuyển nhanh</div>
                        <div style="font-size: 12px; color: var(--text-88);">Nhận hàng sau 2-3 ngày</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center;">
                    <div class="mr-4" style="font-size: 14px;" id="shipping-fee-display">₫30.000</div>
                </div>
                <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="30000">
            </div>
            
            <div style="padding: 20px 30px; border-top: 1px dashed rgba(0,0,0,.09); text-align: right; font-size: 14px;">
                Tổng số tiền (<?php echo count($checkoutItems); ?> sản phẩm): 
                <span style="color: var(--primary-theme); font-size: 18px; font-weight: 500; margin-left: 10px;" id="header-final-total">
                    ₫<?php echo number_format($subtotal + 30000, 0, ',', '.'); ?>
                </span>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="checkout-card payment-section">
            <div class="payment-title">Phương thức thanh toán</div>
            <div class="payment-methods">
                <div class="payment-method-item active" data-value="cod" onclick="selectPaymentMethod('cod', this)">
                    Thanh toán khi nhận hàng (COD)
                    <i class="fas fa-check check-icon"></i>
                </div>
                <div class="payment-method-item" data-value="bank_transfer" onclick="selectPaymentMethod('bank_transfer', this)">
                    Chuyển khoản ngân hàng
                    <i class="fas fa-check check-icon"></i>
                </div>
                <input type="hidden" name="payment_method" id="payment_method_input" value="cod">
            </div>

            <!-- Bank Info (hidden by default) -->
            <div id="bank-info" style="display: none; margin-top: 20px; padding: 20px; background: #fafafa; border: 1px solid var(--border-ebeb); border-radius: 2px;">
                <p style="font-weight: 500; margin-bottom: 10px;">Thông tin chuyển khoản:</p>
                <div style="font-size: 14px; line-height: 1.8;">
                    Ngân hàng: <b>Vietcombank</b><br>
                    Số tài khoản: <b>0964325348</b><br>
                    Chủ tài khoản: <b>CHU HOÀNG KHÁNH HÂN</b><br>
                    Nội dung chuyển khoản: <b>THANHTOAN [Số điện thoại của bạn]</b>
                </div>
            </div>
        </div>

        <!-- Final Summary -->
        <div class="checkout-card checkout-summary">
            <div class="summary-row">
                <div class="label">Tổng tiền hàng</div>
                <div class="value">₫<?php echo number_format($subtotal, 0, ',', '.'); ?></div>
            </div>
            <div class="summary-row">
                <div class="label">Phí vận chuyển</div>
                <div class="value" id="summary-shipping-fee">₫30.000</div>
            </div>
            <div class="summary-row total">
                <div class="label">Tổng thanh toán</div>
                <div class="value" id="display-final-total">₫<?php echo number_format($subtotal + 30000, 0, ',', '.'); ?></div>
            </div>
            <div style="margin-top: 20px;">
                <button type="submit" class="place-order-btn">Đặt Hàng</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal 1: Chọn địa chỉ từ danh sách -->
<div class="modal fade" id="changeAddressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px;">
            <div class="modal-header px-4 pt-4 border-0">
                <h5 class="modal-title" style="font-size: 1.25rem; color: #222;">Địa Chỉ Của Tôi</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body px-4" style="max-height: 450px; overflow-y: auto;" id="address-list-container">
                <?php if (!empty($addresses)): ?>
                    <?php foreach ($addresses as $addr): ?>
                        <div class="address-modal-item px-4 py-3 d-flex align-items-start border-bottom <?php echo $addr->is_default ? 'active' : ''; ?>"
                            data-id="<?php echo $addr->id; ?>"
                            data-name="<?php echo htmlspecialchars($addr->name); ?>"
                            data-phone="<?php echo htmlspecialchars($addr->phone); ?>"
                            data-summary="<?php echo htmlspecialchars($addr->address_line); ?>, <?php echo htmlspecialchars($addr->ward); ?>, <?php echo htmlspecialchars($addr->district); ?>, <?php echo htmlspecialchars($addr->city); ?>"
                            data-district="<?php echo htmlspecialchars($addr->district); ?>"
                            data-city="<?php echo htmlspecialchars($addr->city); ?>"
                            data-default="<?php echo $addr->is_default; ?>"
                            onclick="selectFromModal(<?php echo $addr->id; ?>)"
                            style="cursor: pointer; transition: background 0.2s;">

                            <div class="custom-control custom-radio mr-3 mt-1">
                                <input type="radio" name="modal_addr_radio" class="custom-control-input" <?php echo ($selectedAddr && $selectedAddr->id == $addr->id) ? 'checked' : ''; ?>>
                                <label class="custom-control-label"></label>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <span style="font-weight: 600; font-size: 15px; color: #333;"><?php echo htmlspecialchars($addr->name); ?></span>
                                    <span class="mx-2" style="color: #ccc;">|</span>
                                    <span style="color: #888; font-size: 14px;"><?php echo htmlspecialchars($addr->phone); ?></span>
                                    <a href="javascript:void(0)" class="ml-auto small" style="color: var(--primary-theme);" onclick="event.stopPropagation(); openEditFromList('<?php echo rawurlencode(json_encode($addr)); ?>')">Cập nhật</a>
                                </div>
                                <div class="text-secondary small mb-1" style="line-height: 1.4;">
                                    <?php echo htmlspecialchars($addr->address_line); ?>
                                </div>
                                <div class="text-secondary small mb-2">
                                    <?php echo htmlspecialchars($addr->ward); ?>, <?php echo htmlspecialchars($addr->district); ?>, <?php echo htmlspecialchars($addr->city); ?>
                                </div>
                                <?php if ($addr->is_default): ?>
                                    <span style="color: var(--primary-theme); border: 1px solid var(--primary-theme); font-size: 10px; padding: 1px 4px;">Mặc định</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer px-4 py-3 border-0">
                <button type="button" class="btn btn-light px-4 mr-2" data-dismiss="modal">Trở Lại</button>
                <button type="button" class="btn btn-primary px-4" style="background-color: var(--primary-theme); border: none;" onclick="openAddNewAddress()">
                    <i class="fas fa-plus mr-1"></i> Thêm Địa Chỉ Mới
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Thêm/Cập nhật địa chỉ chi tiết -->
<!-- Giữ nguyên modal cũ nhưng đổi màu nút thành Blue -->
<div class="modal fade" id="addNewAddressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px;">
            <div class="modal-header px-4 pt-4 border-0">
                <h5 class="modal-title font-weight-bold" id="modal_addr_title" style="font-size: 1.25rem; color: #333;">Địa chỉ mới</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="checkoutCombinedAddressForm" method="POST">
                <div class="modal-body px-4 pt-4" style="max-height: 480px; overflow-y: auto;">
                    <input type="hidden" name="id" id="modal_addr_id">
                    <input type="hidden" name="address_type" id="addr_type_input" value="">
                    <input type="hidden" name="city_text" id="modal_city_text">
                    <input type="hidden" name="district_text" id="modal_district_text">
                    <input type="hidden" name="ward_text" id="modal_ward_text">

                    <div class="row">
                        <div class="col-6 pr-1">
                            <div class="form-group">
                                <label class="small font-weight-bold">Họ và tên</label>
                                <input type="text" name="name" id="modal_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6 pl-1">
                            <div class="form-group">
                                <label class="small font-weight-bold">Số điện thoại</label>
                                <input type="text" name="phone" id="modal_phone" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group position-relative">
                        <label class="small font-weight-bold">Tỉnh/ Thành phố, Quận/Huyện, Phường/Xã</label>
                        <input type="text" id="modal_province_combined" class="form-control" readonly placeholder="Chọn địa chỉ..." style="cursor: pointer; background: #fff;">
                        
                        <div id="selection_area" style="display: none; position: absolute; width: 100%; left: 0; background: white; border: 1px solid #ddd; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 4px; margin-top: 5px;">
                            <!-- Address Tab Logic as before -->
                            <div class="d-flex border-bottom">
                                <div class="p-2 flex-grow-1 text-center cursor-pointer address-tab active" id="tab_province" onclick="switchTab('province')">Tỉnh/Thành</div>
                                <div class="p-2 flex-grow-1 text-center cursor-pointer address-tab" id="tab_district" onclick="switchTab('district')">Quận/Huyện</div>
                                <div class="p-2 flex-grow-1 text-center cursor-pointer address-tab" id="tab_ward" onclick="switchTab('ward')">Phường/Xã</div>
                            </div>
                            <div id="addr_list" style="max-height: 250px; overflow-y: auto;">
                                <div class="p-4 text-center text-muted">Đang tải dữ liệu...</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold">Địa chỉ cụ thể</label>
                        <textarea name="address_line" id="modal_address_line" class="form-control" placeholder="Số nhà, tên đường..." required></textarea>
                    </div>

                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" name="is_default" class="custom-control-input" id="modal_is_default" value="1">
                        <label class="custom-control-label text-secondary small" for="modal_is_default" style="cursor: pointer;">Đặt làm địa chỉ mặc định</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" onclick="backToAddressList()">Trở Lại</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--primary-theme); border: none;">HOÀN THÀNH</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function selectPaymentMethod(method, el) {
        $('.payment-method-item').removeClass('active');
        $(el).addClass('active');
        $('#payment_method_input').val(method);
        
        if (method === 'bank_transfer') {
            $('#bank-info').slideDown();
        } else {
            $('#bank-info').slideUp();
        }
    }

    function selectFromModal(addrId) {
        const item = $(`.address-modal-item[data-id="${addrId}"]`);
        if (!item.length) return;
        
        $('#final_selected_address_id').val(addrId);
        $('#display-name').text(item.attr('data-name'));
        $('#display-phone').text(item.attr('data-phone'));
        $('#display-address').text(item.attr('data-summary'));
        
        $('#changeAddressModal').modal('hide');
        updateShippingFee();
    }

    function openAddNewAddress() {
        $('#changeAddressModal').modal('hide');
        $('#modal_addr_id').val('');
        $('#modal_name').val('');
        $('#modal_phone').val('');
        $('#modal_address_line').val('');
        $('#modal_province_combined').val('');
        $('#addNewAddressModal').modal('show');
    }

    function openEditFromList(addrJson) {
        const addr = JSON.parse(decodeURIComponent(addrJson));
        $('#changeAddressModal').modal('hide');
        $('#modal_addr_id').val(addr.id);
        $('#modal_name').val(addr.name);
        $('#modal_phone').val(addr.phone);
        $('#modal_address_line').val(addr.address_line);
        $('#modal_province_combined').val(`${addr.city}, ${addr.district}, ${addr.ward}`);
        $('#modal_city_text').val(addr.city);
        $('#modal_district_text').val(addr.district);
        $('#modal_ward_text').val(addr.ward);
        $('#addNewAddressModal').modal('show');
    }

    function backToAddressList() {
        $('#addNewAddressModal').modal('hide');
        $('#changeAddressModal').modal('show');
    }

    // Tích hợp logic API địa chỉ
    let currentProvinces = [];
    let currentDistricts = [];
    let currentWards = [];
    let selectedP = null;
    let selectedD = null;
    let currentLevel = 'province';

    $(document).ready(function() {
        loadProvinces();
        $('#modal_province_combined').click(function() {
            $('#selection_area').toggle();
        });

        $('#checkoutCombinedAddressForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'index.php?url=Address/save',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert(res.message || 'Lỗi lưu địa chỉ');
                    }
                }
            });
        });
    });

    async function loadProvinces() {
        try {
            const res = await fetch('https://provinces.open-api.vn/api/p/');
            currentProvinces = await res.json();
            renderAddrList();
        } catch (e) {}
    }

    function switchTab(level) {
        currentLevel = level;
        $('.address-tab').removeClass('active');
        $(`#tab_${level}`).addClass('active');
        renderAddrList();
    }

    function renderAddrList() {
        const container = $('#addr_list');
        container.empty();
        let data = [];
        if (currentLevel === 'province') data = currentProvinces;
        else if (currentLevel === 'district') data = currentDistricts;
        else data = currentWards;

        data.forEach(item => {
            const div = $('<div class="p-2 border-bottom cursor-pointer" style="font-size: 14px;"></div>').text(item.name);
            div.click(() => handleAddrSelect(item));
            container.append(div);
        });
    }

    async function handleAddrSelect(item) {
        if (currentLevel === 'province') {
            selectedP = item;
            $('#tab_province').text(item.name);
            $('#modal_city_text').val(item.name);
            const res = await fetch(`https://provinces.open-api.vn/api/p/${item.code}?depth=2`);
            const data = await res.json();
            currentDistricts = data.districts;
            switchTab('district');
        } else if (currentLevel === 'district') {
            selectedD = item;
            $('#tab_district').text(item.name);
            $('#modal_district_text').val(item.name);
            const res = await fetch(`https://provinces.open-api.vn/api/d/${item.code}?depth=2`);
            const data = await res.json();
            currentWards = data.wards;
            switchTab('ward');
        } else {
            $('#tab_ward').text(item.name);
            $('#modal_ward_text').val(item.name);
            $('#modal_province_combined').val(`${selectedP.name}, ${selectedD.name}, ${item.name}`);
            $('#selection_area').hide();
        }
    }

    // --- LOGIC PHÍ VẬN CHUYỂN ---
    function updateShippingFee() {
        const subtotal = <?php echo $subtotal; ?>;
        let shippingFee = 30000; // Mặc định
        let shippingText = "Vận chuyển nhanh";

        // 1. Kiểm tra Freeship (từ 250k)
        if (subtotal >= 250000) {
            shippingFee = 0;
            shippingText = "Miễn phí vận chuyển (Đơn hàng từ 250k)";
        } else {
            // 2. Lấy thông tin địa chỉ hiện tại
            let district = "";
            let city = "";

            const addressId = $('#final_selected_address_id').val();
            if (addressId) {
                const item = $(`.address-modal-item[data-id="${addressId}"]`);
                if (item.length) {
                    district = item.attr('data-district') || "";
                    city = item.attr('data-city') || "";
                } else {
                    // Nếu là lúc mới load trang, lấy từ hidden input (dành cho địa chỉ mặc định ban đầu)
                    district = $('#initial_district').val() || "";
                    city = $('#initial_city').val() || "";
                }
            }

            const c = city.toLowerCase();
            if (c.includes("hồ chí minh")) {
                const d = district.toLowerCase();
                
                // Khu vực gần: 25K (Gò Vấp, Phú Nhuận, Bình Thạnh, Tân Bình)
                if (d.includes("gò vấp") || d.includes("phú nhuận") || d.includes("bình thạnh") || d.includes("tân bình")) {
                    shippingFee = 25000;
                }
                // Khu vực xa: 30k-35k (Quận 1, 3, 5, 7, 10, 11, TP Thủ Đức) -> Dùng 30K
                else if (d.includes("quận 1") || d.includes("quận 3") || d.includes("quận 5") || d.includes("quận 7") || 
                         d.includes("quận 10") || d.includes("quận 11") || d.includes("thủ đức") || d.includes("quận 2") || d.includes("quận 9")) {
                    shippingFee = 30000;
                }
                // Ngoại thành: 45k (Hóc Môn, Bình Chánh, Nhà Bè, Củ Chi) -> Dùng 45K
                else if (d.includes("hóc môn") || d.includes("bình chánh") || d.includes("nhà bè") || d.includes("củ chi") || d.includes("cần giờ")) {
                    shippingFee = 45000;
                }
            } else if (city !== "") {
                // Tỉnh thành khác: 60k
                shippingFee = 60000;
            }
        }

        // Cập nhật UI
        const feeFormatted = shippingFee === 0 ? "Miễn phí" : "₫" + shippingFee.toLocaleString('vi-VN');
        $('#shipping-fee-display').text(feeFormatted);
        $('#summary-shipping-fee').text(feeFormatted);
        $('#shipping_cost_input').val(shippingFee);
        
        // Cập nhật tổng thanh toán
        const total = subtotal + shippingFee;
        $('#display-final-total').text("₫" + total.toLocaleString('vi-VN'));
        $('#header-final-total').text("₫" + total.toLocaleString('vi-VN'));
    }

    $(document).ready(function() {
        updateShippingFee();
    });
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .address-tab.active { color: var(--primary-blue); border-bottom: 2px solid var(--primary-blue); font-weight: bold; }
    .address-modal-item.active { background-color: var(--primary-blue-light); border-left: 3px solid var(--primary-blue); }
</style>

<?php include 'app/views/shares/footer.php'; ?>
