<?php include 'app/views/shares/header.php'; ?>

<div class="container py-5" style="margin-top: 120px;">
    <div class="row">
        <div class="col-md-3">
            <?php include 'app/views/shares/account_sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold">Địa chỉ của tôi</h4>
                        <p class="text-muted small mb-0">Quản lý các địa chỉ nhận hàng của bạn</p>
                    </div>
                    <button class="btn btn-danger px-4 py-2" style="background-color: #ee4d2d; border: none; border-radius: 4px; font-weight: 500; height: 40px;" data-toggle="modal" data-target="#addressModal">
                        <i class="fas fa-plus mr-2"></i> Thêm địa chỉ mới
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($addresses)): ?>
                        <div class="text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-address-8422479-6705708.png" style="width: 200px;" alt="Empty">
                            <p class="text-muted mt-3">Bạn chưa có địa chỉ giao hàng nào.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-item p-4 border-bottom position-relative" style="transition: all 0.2s;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="address-info flex-grow-1">
                                        <div class="d-flex align-items-center mb-1 flex-wrap">
                                            <span class="font-weight-bold mr-3" style="font-size: 1.1rem; color: #222;"><?php echo htmlspecialchars($addr->name); ?></span>
                                            <span class="text-muted d-none d-sm-inline" style="border-left: 1px solid #ddd; padding-left: 12px; height: 14px; margin-top: 4px;"></span>
                                            <span class="text-muted ml-sm-2" style="font-size: 0.95rem;"><?php echo htmlspecialchars($addr->phone); ?></span>
                                            <?php if ($addr->is_default): ?>
                                                <span class="badge badge-danger ml-3 font-weight-normal" style="background-color: #ee4d2d; border-radius: 2px; padding: 4px 8px; font-size: 10px;">Mặc định</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-secondary mt-2" style="font-size: 0.95rem;">
                                            <?php echo htmlspecialchars($addr->address_line); ?>
                                        </div>
                                        <div class="text-secondary" style="font-size: 0.95rem;">
                                            <?php echo htmlspecialchars($addr->ward . ', ' . $addr->district . ', ' . $addr->city); ?>
                                        </div>
                                    </div>
                                    <div class="address-actions text-right d-flex flex-column align-items-end" style="gap: 12px;">
                                        <div class="d-flex" style="gap: 15px;">
                                            <a href="javascript:void(0)" class="text-primary font-weight-500" onclick='editAddress(<?php echo json_encode($addr); ?>)'>Sửa</a>
                                            <?php if (!$addr->is_default): ?>
                                                <a href="<?php echo BASE_URL; ?>index.php?url=Address/delete/<?php echo $addr->id; ?>" class="text-danger font-weight-500" onclick="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')">Xóa</a>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!$addr->is_default): ?>
                                            <button class="btn btn-outline-secondary btn-sm px-3"
                                                style="border-radius: 2px; font-size: 0.85rem; transition: all 0.2s;"
                                                onclick="location.href='<?php echo BASE_URL; ?>index.php?url=Address/setDefault/<?php echo $addr->id; ?>'">
                                                Thiết lập mặc định
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa địa chỉ (Shopee Style) -->
<style>
    /* Shopee Style UI Components */
    .st-input-group {
        position: relative;
        margin-bottom: 25px;
    }

    .st-input-group label {
        position: absolute;
        top: -10px;
        left: 12px;
        background: #fff;
        padding: 0 5px;
        font-size: 12px;
        color: #999;
        font-weight: 400 !important;
        z-index: 10;
        pointer-events: none;
    }

    .st-form-control {
        border: 1px solid #ddd !important;
        border-radius: 2px !important;
        height: 48px !important;
        font-size: 15px !important;
    }

    .st-form-control:focus {
        border-color: #ee4d2d !important;
        box-shadow: none !important;
    }

    .type-btn-group {
        display: flex;
        gap: 12px;
    }

    .type-btn {
        border: 1px solid #ddd;
        padding: 10px 24px;
        border-radius: 2px;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        color: #555;
    }

    .type-btn.active {
        border-color: #ee4d2d;
        color: #ee4d2d;
        background: #fff;
    }

    #addressModal .modal-content {
        border-radius: 4px;
        border: none;
    }

    .btn-submit-sho {
        background: #ee4d2d;
        color: #fff;
        border: none;
        padding: 12px 45px;
        font-size: 15px;
        font-weight: 500;
        border-radius: 2px;
        transition: background 0.2s;
        cursor: pointer;
        min-width: 140px;
    }

    .btn-submit-sho:hover {
        background: #d73211;
        color: #fff;
    }

    .btn-back {
        background: #fff;
        border: 1px solid #ddd;
        color: #555;
        padding: 10px 30px;
        font-size: 15px;
        border-radius: 2px;
        margin-right: 15px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-back:hover {
        background: #f8f8f8;
        border-color: #ccc;
        color: #333;
    }

    /* Shopee-style Address Picker Tabs */
    .address-selector-wrapper {
        position: relative;
    }

    .address-input-container {
        position: relative;
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
        padding-right: 10px;
    }

    .address-input-container input {
        border: none !important;
        background: transparent !important;
        cursor: pointer !important;
    }

    .address-input-icons {
        position: absolute;
        right: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #bbb;
        font-size: 14px;
    }

    .address-tabs {
        display: flex;
        border-bottom: 1px solid #eee;
        background: #fff;
        padding-top: 5px;
    }

    .address-tab {
        flex: 1;
        padding: 12px 5px;
        text-align: center;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    .address-tab.active {
        color: #ee4d2d;
        border-bottom-color: #ee4d2d;
    }

    .address-list-content {
        max-height: 250px;
        overflow-y: auto;
    }

    .address-item {
        padding: 10px 20px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        border-bottom: 1px solid #f9f9f9;
        transition: all 0.2s;
    }

    .address-item:hover {
        background: #fafafa;
        color: #ee4d2d;
    }
</style>

<div class="modal fade" id="addressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title font-weight-bold" id="modalTitle" style="font-size: 1.2rem;">Địa chỉ mới</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?php echo BASE_URL; ?>index.php?url=Address/save" method="POST" id="addressForm">
                <div class="modal-body px-4">
                    <input type="hidden" name="id" id="addr_id">
                    <input type="hidden" name="address_type" id="addr_type_input" value="">

                    <input type="hidden" name="city_text" id="city_text">
                    <input type="hidden" name="district_text" id="district_text">
                    <input type="hidden" name="ward_text" id="ward_text">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="st-input-group">
                                <label>Họ và tên</label>
                                <input type="text" name="name" id="addr_name" class="form-control st-form-control" required placeholder="Họ và tên">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="st-input-group">
                                <label>Số điện thoại</label>
                                <input type="text" name="phone" id="addr_phone" class="form-control st-form-control" required placeholder="Số điện thoại">
                            </div>
                        </div>
                    </div>

                    <div class="st-input-group address-selector-wrapper">
                        <label>Tỉnh/Thành phố, Quận/Huyện, Phường/Xã</label>
                        <div class="address-input-container" id="combined_trigger">
                            <input type="text" id="addr_province_combined" class="form-control st-form-control" readonly placeholder="Chọn địa chỉ..." required>
                            <div class="address-input-icons">
                                <i class="fas fa-caret-down"></i>
                            </div>
                        </div>

                        <div id="selection_area" style="display: none; position: absolute; width: 100%; left: 0; background: white; border: 1px solid #ddd; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 4px; margin-top: 5px;">
                            <div class="address-tabs">
                                <div class="address-tab active" id="tab_province" onclick="switchTab('province')">Tỉnh/Thành phố</div>
                                <div class="address-tab" id="tab_district" onclick="switchTab('district')">Quận/Huyện</div>
                                <div class="address-tab" id="tab_ward" onclick="switchTab('ward')">Phường/Xã</div>
                            </div>
                            <div class="address-list-content" id="addr_list">
                                <div class="p-4 text-center text-muted">Đang tải dữ liệu...</div>
                            </div>
                        </div>
                    </div>

                    <div class="st-input-group">
                        <label>Địa chỉ cụ thể</label>
                        <textarea name="address_line" id="addr_line" class="form-control st-form-control" rows="2" required placeholder="Số nhà, tên đường..."></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="d-block mb-3 text-secondary small">Loại địa chỉ:</label>
                        <div class="type-btn-group">
                            <div class="type-btn" onclick="setAddressType('Nhà Riêng', this)">Nhà Riêng</div>
                            <div class="type-btn" onclick="setAddressType('Văn Phòng', this)">Văn Phòng</div>
                        </div>
                    </div>

                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" name="is_default" class="custom-control-input" id="is_default" value="1">
                        <label class="custom-control-label text-secondary small" for="is_default" style="cursor: pointer;">Đặt làm địa chỉ mặc định</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 justify-content-end">
                    <button type="button" class="btn btn-back" data-dismiss="modal">Trở Lại</button>
                    <button type="submit" class="btn btn-submit-sho">HOÀN THÀNH</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentProvinces = [];
    let currentDistricts = [];
    let currentWards = [];
    let selectedP = null;
    let selectedD = null;
    let currentLevel = 'province';

    document.addEventListener('DOMContentLoaded', function() {
        loadAllData();

        $('#combined_trigger').click(function(e) {
            e.stopPropagation();
            $('#selection_area').toggle();
            if ($('#selection_area').is(':visible')) renderList();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.address-selector-wrapper').length) {
                $('#selection_area').hide();
            }
        });
    });

    async function loadAllData() {
        try {
            const res = await fetch('https://provinces.open-api.vn/api/p/');
            currentProvinces = await res.json();
        } catch (e) {
            console.error('Lỗi tải dữ liệu tỉnh thành');
        }
    }

    function switchTab(level) {
        if (level === 'district' && !selectedP) return;
        if (level === 'ward' && !selectedD) return;

        currentLevel = level;
        $('.address-tab').removeClass('active');
        $(`#tab_${level}`).addClass('active');
        renderList();
    }

    function renderList() {
        const container = $('#addr_list');
        container.empty();
        let data = [];

        if (currentLevel === 'province') data = currentProvinces;
        else if (currentLevel === 'district') data = currentDistricts;
        else data = currentWards;

        if (data.length === 0) {
            container.append('<div class="p-4 text-center text-muted">Đang tải dữ liệu...</div>');
            return;
        }

        data.forEach(item => {
            const div = $('<div class="address-item"></div>').text(item.name);
            div.click(() => handleSelect(item));
            container.append(div);
        });
    }

    async function handleSelect(item) {
        if (currentLevel === 'province') {
            selectedP = item;
            $('#tab_province').text(item.name);
            $('#city_text').val(item.name);

            // Fetch districts
            const res = await fetch(`https://provinces.open-api.vn/api/p/${item.code}?depth=2`);
            const data = await res.json();
            currentDistricts = data.districts;

            selectedD = null;
            $('#tab_district').text('Quận/Huyện');
            $('#tab_ward').text('Phường/Xã');
            switchTab('district');
        } else if (currentLevel === 'district') {
            selectedD = item;
            $('#tab_district').text(item.name);
            $('#district_text').val(item.name);

            // Fetch wards
            const res = await fetch(`https://provinces.open-api.vn/api/d/${item.code}?depth=2`);
            const data = await res.json();
            currentWards = data.wards;

            $('#tab_ward').text('Phường/Xã');
            switchTab('ward');
        } else {
            $('#tab_ward').text(item.name);
            $('#ward_text').val(item.name);

            const combined = `${selectedP.name}, ${selectedD.name}, ${item.name}`;
            $('#addr_province_combined').val(combined);
            $('#selection_area').hide();
        }
    }

    function setAddressType(type, element) {
        const input = document.getElementById('addr_type_input');
        if (element.classList.contains('active')) {
            element.classList.remove('active');
            input.value = '';
        } else {
            document.querySelectorAll('.type-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            input.value = type;
        }
    }

    function editAddress(addr) {
        document.getElementById('modalTitle').innerText = 'Cập nhật địa chỉ';
        document.getElementById('addr_id').value = addr.id;
        document.getElementById('addr_name').value = addr.name;
        document.getElementById('addr_phone').value = addr.phone;
        document.getElementById('addr_line').value = addr.address_line;

        $('#addr_province_combined').val(`${addr.city}, ${addr.district}, ${addr.ward}`);
        document.getElementById('city_text').value = addr.city;
        document.getElementById('district_text').value = addr.district;
        document.getElementById('ward_text').value = addr.ward;

        // Reset Tabs for match
        $('#tab_province').text(addr.city || 'Tỉnh/Thành phố');
        $('#tab_district').text(addr.district || 'Quận/Huyện');
        $('#tab_ward').text(addr.ward || 'Phường/Xã');

        const type = addr.address_type || '';
        document.getElementById('addr_type_input').value = type;
        document.querySelectorAll('.type-btn').forEach(btn => {
            if (btn.innerText.trim() === type) btn.classList.add('active');
            else btn.classList.remove('active');
        });

        document.getElementById('is_default').checked = addr.is_default == 1;
        $('#addressModal').modal('show');
    }

    $('#addressModal').on('hidden.bs.modal', function() {
        document.getElementById('addressForm').reset();
        document.getElementById('modalTitle').innerText = 'Địa chỉ mới';
        document.getElementById('addr_id').value = '';
        $('#addr_province_combined').val('');
        $('#tab_province').text('Tỉnh/Thành phố');
        $('#tab_district').text('Quận/Huyện');
        $('#tab_ward').text('Phường/Xã');
        switchTab('province');
    });

    $('#addressForm').on('submit', function(e) {
        e.preventDefault();
        if (!$('#city_text').val() || !$('#district_text').val() || !$('#ward_text').val()) {
            alert('Vui lòng chọn đầy đủ Tỉnh/Thành phố, Quận/Huyện, Phường/Xã');
            return false;
        }

        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#addressModal').modal('hide');
                    location.reload();
                } else {
                    alert('Thông báo: ' + (res.message || 'Lỗi khi lưu địa chỉ'));
                    btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                alert('Lỗi hệ thống. Vui lòng thử lại sau.');
                btn.prop('disabled', false);
            }
        });
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>