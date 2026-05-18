<?php
// app/views/seller/wallet.php

// Calculate stats balances
$availableBalance = $wallet->balance ?? 0;
$processingBalance = $amountProcessing ?? 0;
$totalBalance = $availableBalance + $processingBalance;
$withdrawnBalance = $wallet->total_withdrawn ?? 0;
?>

<!-- Custom Premium CSS & Typography -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<div class="wallet-container py-4 px-3 px-md-4 mt-2" id="wallet-dashboard-root">
    
    <!-- Top Dashboard Header with Theme Switcher -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 pb-3 border-bottom-soft">
        <div>
            <h2 class="dashboard-title text-main mb-1">Ví tiền Người bán</h2>
            <p class="text-secondary small mb-0">Quản lý thu nhập, đối soát doanh thu và rút tiền về tài khoản ngân hàng nhanh chóng.</p>
        </div>
        <div class="d-flex align-items-center mt-3 mt-sm-0 gap-3">
            <!-- Theme Toggle Button -->
            <button type="button" class="btn btn-theme-toggle mr-2" id="themeTogglerBtn" onclick="toggleWalletTheme()" title="Chuyển chế độ Sáng / Tối">
                <i class="fas fa-moon moon-icon"></i>
                <i class="fas fa-sun sun-icon d-none"></i>
                <span class="ml-1 small font-weight-bold">Chế độ tối</span>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?url=Seller" class="text-secondary">Seller</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">Ví của tôi</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Live System Alerts -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-modern alert-success-modern alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon-circle bg-success-soft text-success mr-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <span class="font-weight-bold d-block text-success small">Giao dịch thành công</span>
                    <span class="text-main small"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></span>
                </div>
            </div>
            <button type="button" class="close text-secondary" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-modern alert-danger-modern alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon-circle bg-danger-soft text-danger mr-3">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <span class="font-weight-bold d-block text-danger small">Lỗi giao dịch</span>
                    <span class="text-main small"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></span>
                </div>
            </div>
            <button type="button" class="close text-secondary" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- 1. Card Số Dư Ví (4-Card Glassmorphism Statistics Grid) -->
    <div class="row mb-4">
        <!-- Card 1: Tổng số dư -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-glass-card purple-gradient shadow-soft h-100">
                <div class="card-glow"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between h-100 position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="stats-label text-white-70">Tổng số dư ví</span>
                            <h3 class="stats-value text-white mt-1 mb-0">
                                <?php echo number_format($totalBalance, 0, ',', '.'); ?> <span class="currency-symbol">₫</span>
                            </h3>
                        </div>
                        <div class="stats-icon-wrapper bg-white-15">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                    </div>
                    <div class="stats-helper-text text-white-50 mt-3 mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Số dư khả dụng + tiền đang xử lý
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Có thể rút -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-glass-card pink-gradient shadow-soft h-100">
                <div class="card-glow"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between h-100 position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="stats-label text-white-70">Có thể rút</span>
                            <h3 class="stats-value text-white mt-1 mb-0 animate-pulse">
                                <?php echo number_format($availableBalance, 0, ',', '.'); ?> <span class="currency-symbol">₫</span>
                            </h3>
                        </div>
                        <div class="stats-icon-wrapper bg-white-15">
                            <i class="fas fa-wallet text-white"></i>
                        </div>
                    </div>
                    <div class="stats-helper-text text-white-50 mt-3 mb-0">
                        <i class="fas fa-check-circle mr-1"></i> Sẵn sàng rút về tài khoản của bạn
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Đang xử lý -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-glass-card orange-gradient shadow-soft h-100">
                <div class="card-glow"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between h-100 position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="stats-label text-white-70">Đang xử lý</span>
                            <h3 class="stats-value text-white mt-1 mb-0">
                                <?php echo number_format($processingBalance, 0, ',', '.'); ?> <span class="currency-symbol">₫</span>
                            </h3>
                        </div>
                        <div class="stats-icon-wrapper bg-white-15">
                            <i class="fas fa-hourglass-half text-white animate-spin-slow"></i>
                        </div>
                    </div>
                    <div class="stats-helper-text text-white-50 mt-3 mb-0">
                        <i class="fas fa-clock mr-1"></i> Yêu cầu rút đang chờ duyệt
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Đã rút -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-glass-card green-gradient shadow-soft h-100">
                <div class="card-glow"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between h-100 position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="stats-label text-white-70">Đã rút thành công</span>
                            <h3 class="stats-value text-white mt-1 mb-0">
                                <?php echo number_format($withdrawnBalance, 0, ',', '.'); ?> <span class="currency-symbol">₫</span>
                            </h3>
                        </div>
                        <div class="stats-icon-wrapper bg-white-15">
                            <i class="fas fa-check-double text-white"></i>
                        </div>
                    </div>
                    <div class="stats-helper-text text-white-50 mt-3 mb-0">
                        <i class="fas fa-university mr-1"></i> Tổng số tiền đã chuyển về ngân hàng
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Dashboard Split Layout -->
    <div class="row">
        <!-- LEFT COLUMN: Form Rút Tiền Hàng Đầu -->
        <div class="col-lg-5 mb-4">
            <div class="dashboard-panel shadow-soft h-100">
                <div class="panel-header border-bottom-soft p-4 d-flex justify-content-between align-items-center">
                    <h4 class="panel-title mb-0"><i class="fas fa-university mr-2 text-primary"></i>Rút tiền về Ngân hàng</h4>
                    <span class="badge badge-primary-soft text-primary font-weight-bold">Hạn mức tối đa</span>
                </div>
                
                <!-- Actual HTML Submission Form -->
                <form action="<?php echo BASE_URL; ?>index.php?url=Seller/submitWithdrawal" method="POST" id="withdrawalForm" class="p-4 needs-validation" novalidate>
                    
                    <!-- Bank Selection Dropdown -->
                    <div class="form-group mb-3">
                        <label for="bank_name" class="input-label text-uppercase mb-2">1. Chọn Ngân hàng nhận</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-university input-prefix text-secondary"></i>
                            <select class="form-control select-modern" id="bank_name" name="bank_name" required>
                                <option value="" disabled selected>Chọn ngân hàng thụ hưởng</option>
                                <option value="Vietcombank">Vietcombank (VCB)</option>
                                <option value="Techcombank">Techcombank (TCB)</option>
                                <option value="MB Bank">MB Bank (Quân Đội)</option>
                                <option value="VietinBank">VietinBank</option>
                                <option value="BIDV">BIDV</option>
                                <option value="VPBank">VPBank</option>
                                <option value="ACB">ACB</option>
                                <option value="TPBank">TPBank</option>
                                <option value="Sacombank">Sacombank</option>
                                <option value="VIB">VIB</option>
                                <option value="Agribank">Agribank</option>
                            </select>
                            <div class="invalid-feedback ml-4 pl-3">Vui lòng chọn ngân hàng thụ hưởng.</div>
                        </div>
                    </div>

                    <!-- Bank Account Number -->
                    <div class="form-group mb-3">
                        <label for="bank_account" class="input-label text-uppercase mb-2">2. Số tài khoản</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-credit-card input-prefix text-secondary"></i>
                            <input type="text" class="form-control input-modern" id="bank_account" name="bank_account" placeholder="Nhập chính xác số tài khoản ngân hàng" required pattern="^[0-9]{6,20}$">
                            <div class="invalid-feedback ml-4 pl-3">Vui lòng nhập số tài khoản hợp lệ (chỉ gồm số, 6-20 ký tự).</div>
                        </div>
                    </div>

                    <!-- Account Owner Name (Uppercase automatic conversion) -->
                    <div class="form-group mb-3">
                        <label for="bank_owner" class="input-label text-uppercase mb-2">3. Tên chủ tài khoản (Chữ in hoa không dấu)</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-user-tie input-prefix text-secondary"></i>
                            <input type="text" class="form-control input-modern text-uppercase" id="bank_owner" name="bank_owner" placeholder="Ví dụ: NGUYEN VAN A" required oninput="validateBankOwner(this)">
                            <div class="invalid-feedback ml-4 pl-3">Vui lòng nhập tên chủ tài khoản thụ hưởng.</div>
                        </div>
                    </div>

                    <!-- Withdrawal Amount Input -->
                    <div class="form-group mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="amount" class="input-label text-uppercase mb-0">4. Số tiền muốn rút (₫)</label>
                            <span class="text-secondary small font-weight-bold">
                                Khả dụng: <span class="text-primary"><?php echo number_format($availableBalance, 0, ',', '.'); ?> ₫</span>
                            </span>
                        </div>
                        <div class="input-modern-wrapper mb-2">
                            <span class="input-prefix-currency font-weight-bold text-secondary">₫</span>
                            <input type="number" class="form-control input-modern input-amount-value font-weight-bold" id="amount" name="amount" min="40000" max="<?php echo $availableBalance; ?>" step="1000" placeholder="Nhập số tiền muốn rút (Tối thiểu 40.000đ)" required oninput="calculateWithdrawal(this.value)">
                            <div class="invalid-feedback ml-4 pl-3">Số tiền rút phải lớn hơn hoặc bằng 40.000₫ và tối đa là số dư khả dụng.</div>
                        </div>

                        <!-- Quick select amount options -->
                        <div class="quick-presets d-flex flex-wrap gap-2 mt-2">
                            <button type="button" class="btn btn-preset" onclick="setWithdrawAmount(100000)">100.000 ₫</button>
                            <button type="button" class="btn btn-preset" onclick="setWithdrawAmount(500000)">500.000 ₫</button>
                            <button type="button" class="btn btn-preset" onclick="setWithdrawAmount(1000000)">1.000.000 ₫</button>
                            <button type="button" class="btn btn-preset" onclick="setWithdrawAmount(5000000)">5.000.000 ₫</button>
                            <button type="button" class="btn btn-preset preset-max" onclick="setWithdrawAmount(<?php echo $availableBalance; ?>)">Rút tối đa</button>
                        </div>
                    </div>

                    <!-- Dynamic Fees & Real cash Receive visualizer (Free withdrawal as requested!) -->
                    <div class="alert alert-calculation bg-soft shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom-dotted">
                            <span class="small font-weight-bold text-secondary">Phí giao dịch:</span>
                            <span class="font-weight-bold text-success" id="calc-fee">0 ₫ <span class="badge badge-success-soft ml-1">Miễn phí rút tiền</span></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-main">Tiền thực nhận:</span>
                            <span class="font-weight-bold text-pink animate-scale-up" id="calc-receive" style="font-size: 1.35rem; color: #ff3366;">0 ₫</span>
                        </div>
                    </div>

                    <!-- Big Action Submit (Triggers OTP Verification) -->
                    <button type="button" class="btn btn-primary-modern btn-block py-3 font-weight-bold shadow-sm" onclick="triggerOTPVerification()">
                        <i class="fas fa-shield-alt mr-2"></i> Rút tiền ngay
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: Lịch sử rút tiền & biến động số dư (Tabbed Lists) -->
        <div class="col-lg-7 mb-4">
            <div class="dashboard-panel shadow-soft h-100">
                <div class="panel-header border-bottom-soft p-0">
                    <ul class="nav nav-tabs border-0 px-4 pt-3 flex-row gap-2" id="walletTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link tab-modern active font-weight-bold py-3 text-secondary" id="wdr-tab" data-toggle="tab" href="#wdr-panel" role="tab" aria-controls="wdr-panel" aria-selected="true">
                                <i class="fas fa-university text-primary mr-2"></i> Yêu cầu rút tiền
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link tab-modern font-weight-bold py-3 text-secondary" id="txn-tab" data-toggle="tab" href="#txn-panel" role="tab" aria-controls="txn-panel" aria-selected="false">
                                <i class="fas fa-list-alt text-success mr-2"></i> Lịch sử số dư
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-4">
                    
                    <!-- Tab Panel 1: Withdrawal Requests (Pending, Approved, Completed, Rejected) -->
                    <div class="tab-pane fade show active" id="wdr-panel" role="tabpanel" aria-labelledby="wdr-tab">
                        <?php if (empty($withdrawals)): ?>
                            <div class="text-center py-5">
                                <div class="empty-state-icon bg-warning-soft text-warning mb-3">
                                    <i class="fas fa-university fa-2x"></i>
                                </div>
                                <p class="text-muted font-weight-medium">Chưa có yêu cầu rút tiền nào được tạo.</p>
                                <p class="text-secondary small">Các yêu cầu rút tiền của bạn sẽ hiển thị tại đây kèm theo trạng thái xử lý.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-uppercase small text-muted font-weight-bold border-0">
                                        <tr>
                                            <th class="border-0 px-3">Mã yêu cầu</th>
                                            <th class="border-0">Thời gian tạo</th>
                                            <th class="border-0">Ngân hàng</th>
                                            <th class="border-0">Tài khoản nhận</th>
                                            <th class="border-0">Số tiền rút</th>
                                            <th class="border-0 px-3 text-center">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($withdrawals as $wdr): ?>
                                            <tr class="table-row-hover">
                                                <td class="px-3 font-weight-bold align-middle">
                                                    <code class="code-modern"><?php echo $wdr->request_code; ?></code>
                                                </td>
                                                <td class="align-middle text-secondary small">
                                                    <?php echo date('H:i d/m/Y', strtotime($wdr->created_at)); ?>
                                                </td>
                                                <td class="align-middle text-main font-weight-bold">
                                                    <?php echo htmlspecialchars($wdr->bank_name); ?>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex flex-column">
                                                        <span class="font-weight-bold text-main small"><?php echo htmlspecialchars($wdr->bank_account); ?></span>
                                                        <span class="text-secondary text-uppercase small" style="font-size: 0.75rem;"><?php echo htmlspecialchars($wdr->bank_owner); ?></span>
                                                    </div>
                                                </td>
                                                <td class="align-middle font-weight-bold text-main" style="font-size: 0.95rem;">
                                                    <?php echo number_format($wdr->amount, 0, ',', '.'); ?> ₫
                                                </td>
                                                <td class="align-middle px-3 text-center">
                                                    <?php if ($wdr->status === 'pending'): ?>
                                                        <span class="status-badge status-pending"><i class="fas fa-clock mr-1 animate-pulse"></i> Chờ duyệt</span>
                                                    <?php elseif ($wdr->status === 'approved'): ?>
                                                        <span class="status-badge status-approved"><i class="fas fa-check mr-1"></i> Đã duyệt</span>
                                                    <?php elseif ($wdr->status === 'processing'): ?>
                                                        <span class="status-badge status-processing"><i class="fas fa-sync fa-spin mr-1"></i> Đang xử lý</span>
                                                    <?php elseif ($wdr->status === 'completed'): ?>
                                                        <span class="status-badge status-completed"><i class="fas fa-check-double mr-1"></i> Thành công</span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-rejected" title="Lý do từ chối: Hoàn tiền"><i class="fas fa-times mr-1"></i> Từ chối</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Panel 2: General wallet transaction history logs -->
                    <div class="tab-pane fade" id="txn-panel" role="tabpanel" aria-labelledby="txn-tab">
                        <?php if (empty($transactions)): ?>
                            <div class="text-center py-5">
                                <div class="empty-state-icon bg-success-soft text-success mb-3">
                                    <i class="fas fa-list-alt fa-2x"></i>
                                </div>
                                <p class="text-muted font-weight-medium">Chưa có giao dịch biến động số dư nào.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-uppercase small text-muted font-weight-bold border-0">
                                        <tr>
                                            <th class="border-0 px-3">Mã GD</th>
                                            <th class="border-0">Thời gian</th>
                                            <th class="border-0">Loại</th>
                                            <th class="border-0">Giao dịch gốc</th>
                                            <th class="border-0">Phí Admin (10%)</th>
                                            <th class="border-0 text-right px-3">Biến động số dư</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $txn): ?>
                                            <tr class="table-row-hover">
                                                <td class="px-3 font-weight-bold align-middle">
                                                    <code class="code-modern"><?php echo $txn->transaction_code; ?></code>
                                                </td>
                                                <td class="align-middle text-secondary small">
                                                    <?php echo date('H:i d/m/Y', strtotime($txn->created_at)); ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?php if ($txn->type === 'commission'): ?>
                                                        <span class="badge badge-success-soft text-success px-3 py-2 font-weight-bold" style="border-radius: 30px;">Bán hàng</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger-soft text-danger px-3 py-2 font-weight-bold" style="border-radius: 30px;">Rút tiền</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle text-secondary font-weight-bold small">
                                                    <?php echo $txn->gross_amount ? number_format($txn->gross_amount, 0, ',', '.') . ' ₫' : '-'; ?>
                                                </td>
                                                <td class="align-middle text-danger small">
                                                    <?php echo $txn->admin_fee ? '-' . number_format($txn->admin_fee, 0, ',', '.') . ' ₫' : '-'; ?>
                                                </td>
                                                <td class="align-middle text-right px-3 font-weight-bold" style="font-size: 1.05rem;">
                                                    <?php if ($txn->amount >= 0): ?>
                                                        <span class="text-success">+<?php echo number_format($txn->amount, 0, ',', '.'); ?> ₫</span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><?php echo number_format($txn->amount, 0, ',', '.'); ?> ₫</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- 3. Modal Xác Nhận Giao Dịch và Nhập OTP (Highly Animated, Responsive, Autoshifting inputs) -->
<div class="modal fade" id="otpConfirmModal" tabindex="-1" role="dialog" aria-labelledby="otpConfirmModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-modern border-0 shadow-lg">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 justify-content-center text-center">
                <div class="modal-header-container">
                    <div class="shield-icon-circle mb-3 mx-auto">
                        <i class="fas fa-shield-alt text-primary animate-pulse"></i>
                    </div>
                    <h5 class="modal-title font-weight-bold text-main" id="otpConfirmModalLabel">Xác thực OTP giao dịch</h5>
                    <p class="text-secondary small mt-2">Mã xác thực OTP đã được gửi bằng tin nhắn SMS đến số điện thoại bán hàng đã đăng ký của bạn.</p>
                </div>
            </div>

            <div class="modal-body p-4 text-center">
                <!-- Transaction Info Details Card -->
                <div class="alert alert-calculation bg-soft p-3 mb-4 text-left" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small font-weight-bold text-secondary">Ngân hàng thụ hưởng:</span>
                        <span class="font-weight-bold text-main small" id="confirm-bank">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small font-weight-bold text-secondary">Số tài khoản nhận:</span>
                        <span class="font-weight-bold text-main small" id="confirm-account">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small font-weight-bold text-secondary">Tiền thực nhận:</span>
                        <span class="font-weight-bold text-pink" id="confirm-receive" style="font-size: 1.15rem; color: #ff3366;">0 ₫</span>
                    </div>
                </div>

                <label class="input-label text-uppercase mb-3 font-weight-bold">Nhập mã xác thực gồm 6 chữ số</label>
                
                <!-- 6-digit OTP Inputs -->
                <div class="otp-inputs-grid d-flex justify-content-center gap-2 mb-3">
                    <input type="text" class="form-control otp-digit-box" maxlength="1" oninput="shiftOTPFocus(this, 1)" onkeydown="backspaceOTPFocus(event, 1)" autofocus required>
                    <input type="text" class="form-control otp-digit-box" maxlength="1" oninput="shiftOTPFocus(this, 2)" onkeydown="backspaceOTPFocus(event, 2)" required>
                    <input type="text" class="form-control otp-digit-box" maxlength="1" oninput="shiftOTPFocus(this, 3)" onkeydown="backspaceOTPFocus(event, 3)" required>
                    <input type="text" class="form-control otp-digit-box" maxlength="1" oninput="shiftOTPFocus(this, 4)" onkeydown="backspaceOTPFocus(event, 4)" required>
                    <input type="text" class="form-control otp-digit-box" maxlength="1" oninput="shiftOTPFocus(this, 5)" onkeydown="backspaceOTPFocus(event, 5)" required>
                    <input type="text" class="form-control otp-digit-box" maxlength="1" oninput="shiftOTPFocus(this, 6)" onkeydown="backspaceOTPFocus(event, 6)" required>
                </div>

                <!-- OTP Resend & Timer -->
                <div class="otp-timer-container py-2 mb-3">
                    <span class="small text-secondary" id="otpCountdownMessage">
                        Gửi lại mã OTP sau: <span class="text-primary font-weight-bold" id="otpTimerValue">60</span> giây
                    </span>
                    <button type="button" class="btn btn-link btn-sm text-primary font-weight-bold p-0 d-none" id="otpResendBtn" onclick="resendOTPCode()">
                        Gửi lại mã xác thực
                    </button>
                </div>

                <div class="invalid-feedback d-none" id="otpErrorText" style="font-size: 0.85rem; color: #ff3366;">
                    Mã xác thực OTP không chính xác. Vui lòng nhập lại.
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 border-radius-10 flex-grow-1" data-dismiss="modal">Hủy giao dịch</button>
                <button type="button" class="btn btn-primary-modern px-4 py-2 border-radius-10 flex-grow-1 font-weight-bold" id="confirmTxnSubmitBtn" onclick="submitOTPTransaction()">
                    <span class="submit-text">Xác nhận giao dịch</span>
                    <span class="submit-spinner d-none"><i class="fas fa-sync fa-spin mr-1"></i> Đang rút...</span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- WALLET STYLING (GLASSMORPHISM, MODERN 2026, DARK/LIGHT) -->
<!-- ============================================== -->
<style>
    /* 2026 Design System and Typography integration */
    #wallet-dashboard-root {
        font-family: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }
    
    /* Design Variables Definition */
    .wallet-container {
        --bg-app: #f8fafc;
        --bg-card: rgba(255, 255, 255, 0.85);
        --bg-panel: #ffffff;
        --border-card: rgba(226, 232, 240, 0.8);
        --border-soft: #e2e8f0;
        --text-main: #0f172a;
        --text-secondary: #64748b;
        --glass-blur: blur(16px);
        --shadow-soft: 0 12px 30px -10px rgba(0, 0, 0, 0.05), 0 2px 8px rgba(0, 0, 0, 0.02);
        --bg-soft-box: #f8fafc;
        --input-bg: #f8fafc;
        --input-border: #cbd5e1;
        --tab-hover-bg: rgba(194, 37, 92, 0.04);
        --code-bg: #f1f5f9;
        
        transition: background-color 0.4s ease, border-color 0.4s ease;
        background-color: var(--bg-app);
        color: var(--text-main);
        border-radius: 20px;
    }

    /* Dark Mode Variable Override Triggered by Javascript */
    .wallet-container.dark-theme {
        --bg-app: #090d16;
        --bg-card: rgba(15, 23, 42, 0.85);
        --bg-panel: #0f172a;
        --border-card: rgba(51, 65, 85, 0.7);
        --border-soft: #1e293b;
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --shadow-soft: 0 15px 35px -12px rgba(0, 0, 0, 0.4), 0 2px 10px rgba(0, 0, 0, 0.15);
        --bg-soft-box: rgba(30, 41, 59, 0.4);
        --input-bg: #1e293b;
        --input-border: #334155;
        --tab-hover-bg: rgba(255, 51, 102, 0.08);
        --code-bg: #1e293b;
    }

    .border-bottom-soft {
        border-bottom: 1px solid var(--border-soft);
    }
    .text-main {
        color: var(--text-main) !important;
    }
    .text-secondary {
        color: var(--text-secondary) !important;
    }

    /* Title Styling */
    .dashboard-title {
        font-weight: 800;
        font-size: 1.8rem;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, var(--text-main) 30%, #7928ca 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Premium Theme Toggle Button */
    .btn-theme-toggle {
        background-color: var(--bg-card);
        border: 1px solid var(--border-card);
        color: var(--text-main);
        padding: 8px 16px;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-theme-toggle:hover {
        transform: translateY(-2px);
        background-color: var(--border-soft);
    }
    .dark-theme .moon-icon {
        display: none !important;
    }
    .dark-theme .sun-icon {
        display: inline-block !important;
        color: #fcc419;
    }

    /* Stats Glassmorphism Cards */
    .stats-glass-card {
        border: 1px solid var(--border-card);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-soft);
    }
    .stats-glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1);
    }
    .stats-label {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .stats-value {
        font-weight: 800;
        font-size: 1.85rem;
        letter-spacing: -1px;
    }
    .currency-symbol {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .stats-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .stats-helper-text {
        font-size: 0.78rem;
        font-weight: 500;
    }
    
    /* Stats Background Gradients */
    .purple-gradient {
        background: linear-gradient(135deg, #6b21a8 0%, #7928ca 100%);
    }
    .pink-gradient {
        background: linear-gradient(135deg, #be185d 0%, #e11d48 100%);
    }
    .orange-gradient {
        background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);
    }
    .green-gradient {
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
    }
    .bg-white-15 {
        background-color: rgba(255, 255, 255, 0.18) !important;
    }
    .text-white-70 {
        color: rgba(255, 255, 255, 0.75) !important;
    }
    .text-white-50 {
        color: rgba(255, 255, 255, 0.6) !important;
    }

    /* Dashboard Panel Box */
    .dashboard-panel {
        background-color: var(--bg-panel);
        border: 1px solid var(--border-card);
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        transition: all 0.4s ease;
    }
    .panel-title {
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: -0.3px;
        color: var(--text-main);
    }
    .badge-primary-soft {
        background-color: rgba(194, 37, 92, 0.08);
        color: #ff3366 !important;
        border-radius: 30px;
        padding: 5px 12px;
        font-size: 0.8rem;
    }

    /* Form Modern Inputs */
    .input-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-secondary);
        letter-spacing: 0.6px;
    }
    .input-modern-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .input-prefix {
        position: absolute;
        left: 18px;
        font-size: 1.1rem;
        z-index: 10;
        transition: color 0.3s;
    }
    .input-prefix-currency {
        position: absolute;
        left: 20px;
        font-size: 1.25rem;
        z-index: 10;
    }
    .input-modern, .select-modern {
        background-color: var(--input-bg) !important;
        border: 1.5px solid var(--input-border) !important;
        border-radius: 14px !important;
        color: var(--text-main) !important;
        height: auto !important;
        padding: 14px 16px 14px 48px !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: none !important;
    }
    .input-modern:focus, .select-modern:focus {
        border-color: #ff3366 !important;
        background-color: var(--bg-panel) !important;
        box-shadow: 0 0 0 4px rgba(255, 51, 102, 0.12) !important;
    }
    .input-modern:focus + .input-prefix {
        color: #ff3366 !important;
    }

    /* Fast Selection Buttons */
    .btn-preset {
        background-color: var(--bg-soft-box);
        border: 1px dashed var(--input-border);
        color: var(--text-main);
        font-size: 0.82rem;
        font-weight: 700;
        padding: 8px 14px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .btn-preset:hover {
        background-color: #ff3366;
        color: #fff !important;
        border-color: #ff3366;
        transform: translateY(-1px);
    }
    .preset-max {
        border-style: solid;
        background-color: rgba(255, 51, 102, 0.08);
        border-color: #ff3366;
        color: #ff3366;
    }
    .preset-max:hover {
        background-color: #ff3366 !important;
        color: white !important;
    }

    /* Calculations Box */
    .alert-calculation {
        background-color: var(--bg-soft-box);
        border: 1px dashed var(--border-card);
        border-radius: 14px;
    }
    .border-bottom-dotted {
        border-bottom: 1.5px dotted var(--border-card);
    }

    /* Big Action buttons */
    .btn-primary-modern {
        background: linear-gradient(135deg, #7928ca 0%, #ff3366 100%);
        border: none;
        color: white;
        border-radius: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 20px -5px rgba(255, 51, 102, 0.3);
    }
    .btn-primary-modern:hover {
        box-shadow: 0 15px 25px -5px rgba(255, 51, 102, 0.45);
        transform: translateY(-2px);
        color: white;
    }
    .btn-primary-modern:disabled {
        background: var(--input-border);
        box-shadow: none;
        cursor: not-allowed;
    }

    /* Tab styles */
    .nav-tabs {
        border-bottom: 1px solid var(--border-soft) !important;
    }
    .tab-modern {
        border: none !important;
        background: transparent !important;
        position: relative;
        font-size: 0.95rem;
        padding-bottom: 14px !important;
        transition: color 0.3s ease;
    }
    .tab-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #ff3366;
        border-radius: 10px;
        transform: scaleX(0);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .tab-modern:hover {
        color: #ff3366 !important;
    }
    .tab-modern.active {
        color: #ff3366 !important;
    }
    .tab-modern.active::after {
        transform: scaleX(1);
    }

    /* Tables Styles */
    .table th {
        font-weight: 700;
        font-size: 0.78rem;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid var(--border-soft) !important;
    }
    .table td {
        border-top: 1px solid var(--border-soft) !important;
        padding: 16px 8px !important;
    }
    .table-row-hover {
        transition: background-color 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: var(--bg-soft-box);
    }
    .code-modern {
        background-color: var(--code-bg);
        color: var(--text-main);
        padding: 4px 8px;
        border-radius: 8px;
        font-family: monospace;
        font-weight: 700;
    }

    /* Modern color-coded Badges */
    .status-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
    }
    .status-pending {
        background-color: rgba(252, 196, 25, 0.12);
        color: #e67e22;
    }
    .status-approved {
        background-color: rgba(55, 178, 77, 0.12);
        color: #2b8a3e;
    }
    .status-processing {
        background-color: rgba(34, 139, 230, 0.12);
        color: #1864ab;
    }
    .status-completed {
        background-color: rgba(12, 166, 120, 0.15);
        color: #0ca678;
    }
    .status-rejected {
        background-color: rgba(225, 29, 72, 0.12);
        color: #e11d48;
    }

    /* Modern Alert Styles */
    .alert-modern {
        border: 1px solid var(--border-card);
        border-radius: 14px;
        background-color: var(--bg-card);
        padding: 16px 20px;
    }
    .alert-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .bg-success-soft {
        background-color: rgba(55, 178, 77, 0.15);
    }
    .bg-danger-soft {
        background-color: rgba(225, 29, 72, 0.15);
    }

    /* Shield Modal styles */
    .modal-modern {
        background-color: var(--bg-panel);
        border-radius: 24px;
        border: 1px solid var(--border-card);
    }
    .shield-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background-color: rgba(255, 51, 102, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border: 1px solid rgba(255, 51, 102, 0.15);
    }
    .otp-digit-box {
        width: 50px !important;
        height: 58px !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        text-align: center !important;
        border: 1.5px solid var(--input-border) !important;
        border-radius: 12px !important;
        background-color: var(--input-bg) !important;
        color: var(--text-main) !important;
        transition: all 0.2s ease !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .otp-digit-box:focus {
        border-color: #ff3366 !important;
        box-shadow: 0 0 0 4px rgba(255, 51, 102, 0.12) !important;
        background-color: var(--bg-panel) !important;
    }
    .border-radius-10 {
        border-radius: 12px !important;
    }

    /* Animation and effect helpers */
    .animate-pulse {
        animation: pulseEffect 2s infinite;
    }
    @keyframes pulseEffect {
        0% { transform: scale(1); }
        50% { transform: scale(1.03); }
        100% { transform: scale(1); }
    }
    .animate-spin-slow {
        animation: spinSlow 3s linear infinite;
    }
    @keyframes spinSlow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-scale-up {
        animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    @keyframes scaleUp {
        0% { transform: scale(0.9); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .empty-state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .bg-warning-soft {
        background-color: rgba(252, 196, 25, 0.12);
    }
    
    /* Gap helper since Bootstrap 4 doesn't support flex gap well on all browsers */
    .gap-2 { gap: 8px; }
    .gap-3 { gap: 16px; }
</style>

<!-- ============================================== -->
<!-- WALLET INTERACTIVE LOGIC (OTP, DYNAMIC CALC, THEME) -->
<!-- ============================================== -->
<script>
    // Live calculations and presets selector
    function calculateWithdrawal(val) {
        const inputVal = parseInt(val) || 0;
        const confirmReceiveText = document.getElementById('calc-receive');
        
        // Fee is always 0đ as requested
        const formattedAmount = inputVal.toLocaleString('vi-VN') + ' ₫';
        confirmReceiveText.textContent = formattedAmount;
        
        // Add smooth scale animation on calculation
        confirmReceiveText.classList.remove('animate-scale-up');
        void confirmReceiveText.offsetWidth; // Trigger reflow
        confirmReceiveText.classList.add('animate-scale-up');
    }

    function setWithdrawAmount(amountVal) {
        const amountInput = document.getElementById('amount');
        amountInput.value = amountVal;
        calculateWithdrawal(amountVal);
        
        // Trigger validation reset
        amountInput.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function validateBankOwner(input) {
        // Automatically convert owner input to UPPERCASE with no accents
        let rawVal = input.value.toUpperCase();
        
        // Convert Vietnamese accented letters to standard English letters
        const map = {
            'À':'A','Á':'A','Ả':'A','Ã':'A','Ạ':'A','Ă':'A','Ằ':'A','Ắ':'A','Ẳ':'A','Ẵ':'A','Ặ':'A','Â':'A','Ầ':'A','Ấ':'A','Ẩ':'A','Ẫ':'A','Ậ':'A',
            'È':'E','É':'E','Ẻ':'E','Ẽ':'E','Ẹ':'E','Ê':'E','Ề':'E','Ế':'E','Ể':'E','Ễ':'E','Ệ':'E',
            'Ì':'I','Í':'I','Ỉ':'I','Ĩ':'I','Ị':'I',
            'Ò':'O','Ó':'O','Ỏ':'O','Õ':'O','Ọ':'O','Ô':'O','Ồ':'O','Ố':'O','Ổ':'O','Ỗ':'O','Ộ':'O','Ơ':'O','Ờ':'O','Ớ':'O','Ở':'O','Ỡ':'O','Ợ':'O',
            'Ù':'U','Ú':'U','Ủ':'U','Ũ':'U','Ụ':'U','Ư':'U','Ừ':'U','Ứ':'U','Ử':'U','Ữ':'U','Ự':'U',
            'Ỳ':'Y','Ý':'Y','Ỷ':'Y','Ỹ':'Y','Ỵ':'Y',
            'Đ':'D'
        };
        
        for (let key in map) {
            rawVal = rawVal.replace(new RegExp(key, 'g'), map[key]);
        }
        
        // Remove special chars, leave letters and spaces only
        rawVal = rawVal.replace(/[^A-Z\s]/g, '');
        input.value = rawVal;
    }

    // Modern 6-digit OTP Box Auto shifting Focus
    function shiftOTPFocus(input, index) {
        // Strip non-numeric inputs
        input.value = input.value.replace(/[^0-9]/g, '');
        
        if (input.value.length === 1 && index < 6) {
            const nextInput = document.querySelectorAll('.otp-digit-box')[index];
            if (nextInput) nextInput.focus();
        }
        
        // If final box is completed, auto-trigger check
        if (index === 6 && input.value.length === 1) {
            document.getElementById('confirmTxnSubmitBtn').focus();
        }
        
        checkOTPFilled();
    }

    function backspaceOTPFocus(event, index) {
        if (event.key === 'Backspace' && event.target.value === '' && index > 1) {
            const prevInput = document.querySelectorAll('.otp-digit-box')[index - 2];
            if (prevInput) {
                prevInput.focus();
                prevInput.value = '';
            }
        }
    }

    function checkOTPFilled() {
        const digits = document.querySelectorAll('.otp-digit-box');
        let filled = true;
        digits.forEach(d => {
            if (d.value === '') filled = false;
        });
        
        const submitBtn = document.getElementById('confirmTxnSubmitBtn');
        if (filled) {
            submitBtn.classList.remove('btn-outline-secondary');
            submitBtn.classList.add('btn-primary-modern');
        }
    }

    // OTP Simulated Timer Countdown
    let otpCountdownInterval = null;
    function startOTPTimer() {
        clearInterval(otpCountdownInterval);
        const countdownMsg = document.getElementById('otpCountdownMessage');
        const resendBtn = document.getElementById('otpResendBtn');
        const timerVal = document.getElementById('otpTimerValue');
        
        countdownMsg.classList.remove('d-none');
        resendBtn.classList.add('d-none');
        
        let secondsLeft = 60;
        timerVal.textContent = secondsLeft;
        
        otpCountdownInterval = setInterval(() => {
            secondsLeft--;
            timerVal.textContent = secondsLeft;
            
            if (secondsLeft <= 0) {
                clearInterval(otpCountdownInterval);
                countdownMsg.classList.add('d-none');
                resendBtn.classList.remove('d-none');
            }
        }, 1000);
    }

    function resendOTPCode() {
        // Simulate sending alert toast
        alert('Mã xác thực OTP mới đã được gửi lại vào số điện thoại của bạn!');
        
        // Clear old inputs
        document.querySelectorAll('.otp-digit-box').forEach(box => box.value = '');
        document.querySelectorAll('.otp-digit-box')[0].focus();
        
        // Restart countdown
        startOTPTimer();
    }

    // OTP Modal Trigger and Form Validation Integration
    function triggerOTPVerification() {
        const form = document.getElementById('withdrawalForm');
        
        // Trigger Bootstrap standard form validation
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        // Fill Bank details dynamically inside OTP Modal
        const bankName = document.getElementById('bank_name').value;
        const bankAccount = document.getElementById('bank_account').value;
        const amount = parseInt(document.getElementById('amount').value) || 0;

        document.getElementById('confirm-bank').textContent = bankName;
        document.getElementById('confirm-account').textContent = bankAccount;
        document.getElementById('confirm-receive').textContent = amount.toLocaleString('vi-VN') + ' ₫';

        // Clear previous OTP boxes
        document.querySelectorAll('.otp-digit-box').forEach(box => box.value = '');
        document.getElementById('otpErrorText').classList.add('d-none');

        // Show Modal
        $('#otpConfirmModal').modal('show');
        
        // Start countdown and focus first box
        startOTPTimer();
        setTimeout(() => {
            document.querySelectorAll('.otp-digit-box')[0].focus();
        }, 500);
    }

    function submitOTPTransaction() {
        const digits = document.querySelectorAll('.otp-digit-box');
        let otpCode = '';
        digits.forEach(d => otpCode += d.value);

        if (otpCode.length < 6) {
            document.getElementById('otpErrorText').classList.remove('d-none');
            document.getElementById('otpErrorText').textContent = 'Vui lòng nhập đầy đủ 6 chữ số của mã xác thực OTP.';
            return;
        }

        // OTP verification simulation (Always accept '123456' or any valid 6 digits for testing demo!)
        const submitBtn = document.getElementById('confirmTxnSubmitBtn');
        const submitText = submitBtn.querySelector('.submit-text');
        const submitSpinner = submitBtn.querySelector('.submit-spinner');

        // Show Loading state
        submitBtn.disabled = true;
        submitText.classList.add('d-none');
        submitSpinner.classList.remove('d-none');

        setTimeout(() => {
            // Submit the actual HTML form
            document.getElementById('withdrawalForm').submit();
        }, 1500); // 1.5 seconds loading for high-end professional feel
    }

    // Theme toggle utility (Light Mode and Dark Mode variable toggle)
    function toggleWalletTheme() {
        const root = document.getElementById('wallet-dashboard-root');
        const btn = document.getElementById('themeTogglerBtn');
        const moon = btn.querySelector('.moon-icon');
        const sun = btn.querySelector('.sun-icon');
        const btnText = btn.querySelector('span');

        if (root.classList.contains('dark-theme')) {
            root.classList.remove('dark-theme');
            moon.classList.remove('d-none');
            sun.classList.add('d-none');
            btnText.textContent = 'Chế độ tối';
            localStorage.setItem('wallet-theme', 'light');
        } else {
            root.classList.add('dark-theme');
            moon.classList.add('d-none');
            sun.classList.remove('d-none');
            btnText.textContent = 'Chế độ sáng';
            localStorage.setItem('wallet-theme', 'dark');
        }
    }

    // Keep theme preference saved local
    document.addEventListener("DOMContentLoaded", function() {
        const savedTheme = localStorage.getItem('wallet-theme');
        if (savedTheme === 'dark') {
            const root = document.getElementById('wallet-dashboard-root');
            const btn = document.getElementById('themeTogglerBtn');
            const moon = btn.querySelector('.moon-icon');
            const sun = btn.querySelector('.sun-icon');
            const btnText = btn.querySelector('span');
            
            root.classList.add('dark-theme');
            moon.classList.add('d-none');
            sun.classList.remove('d-none');
            btnText.textContent = 'Chế độ sáng';
        }
    });
</script>
