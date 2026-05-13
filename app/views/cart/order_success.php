<?php include 'app/views/shares/header.php'; ?>

<style>
    :root {
        --green-main: #75c794;
        --green-dark: #4fad74;
        --green-light: #e8f8ee;
        --green-lighter: #f2fbf5;
        --green-shadow: rgba(117, 199, 148, 0.35);
    }

    .success-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(145deg, #f2fbf5 0%, #e6f6eb 100%);
    }

    .success-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 24px 64px rgba(117, 199, 148, 0.2);
        padding: 52px 48px;
        max-width: 540px;
        width: 100%;
        text-align: center;
        animation: slideUp 0.5s cubic-bezier(.22,.68,0,1.2);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(40px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ---- Icon ---- */
    .success-icon-wrap {
        width: 108px;
        height: 108px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green-main), var(--green-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 10px 30px var(--green-shadow);
        animation: popIn 0.45s 0.25s cubic-bezier(.22,.68,0,1.4) both;
    }

    @keyframes popIn {
        from { opacity: 0; transform: scale(0.4); }
        to   { opacity: 1; transform: scale(1); }
    }

    .success-icon-wrap i {
        font-size: 52px;
        color: #fff;
    }

    /* ---- Confetti ---- */
    .confetti-row {
        font-size: 1.6rem;
        letter-spacing: 8px;
        margin-bottom: 14px;
        animation: float 2.2s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-7px); }
    }

    /* ---- Title ---- */
    .success-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1a2d1c;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .success-sub {
        color: #7a9a80;
        font-size: 0.975rem;
        line-height: 1.65;
        margin-bottom: 28px;
    }

    /* ---- Info Box ---- */
    .order-info-box {
        background: var(--green-lighter);
        border: 1.5px solid #c5e8d0;
        border-radius: 14px;
        padding: 18px 22px;
        text-align: left;
        margin-bottom: 26px;
    }

    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px dashed #c5e8d0;
        font-size: 0.93rem;
    }

    .info-row:last-child { border-bottom: none; }

    .info-label {
        color: #6a9770;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .info-label i { color: var(--green-main); width: 16px; }

    .info-value {
        font-weight: 700;
        color: #1a2d1c;
    }

    .badge-order-id {
        background: linear-gradient(135deg, var(--green-main), var(--green-dark));
        color: #fff;
        border-radius: 20px;
        padding: 3px 14px;
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .badge-status {
        background: var(--green-light);
        color: var(--green-dark);
        border: 1px solid #a8ddb8;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .total-price { color: var(--green-dark); }

    /* ---- Note ---- */
    .email-note {
        font-size: 0.85rem;
        color: #92b498;
        margin-bottom: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .email-note i { color: var(--green-main); }

    /* ---- Buttons ---- */
    .btn-row {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .btn-view-order {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--green-main), var(--green-dark));
        color: #fff !important;
        text-decoration: none !important;
        padding: 13px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.93rem;
        box-shadow: 0 6px 20px var(--green-shadow);
        transition: all 0.28s ease;
    }

    .btn-view-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(79, 173, 116, 0.5);
        color: #fff !important;
        text-decoration: none !important;
    }

    .btn-continue {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        color: var(--green-dark) !important;
        text-decoration: none !important;
        padding: 12px 26px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.93rem;
        border: 2px solid var(--green-main);
        transition: all 0.28s ease;
    }

    .btn-continue:hover {
        background: var(--green-main);
        color: #fff !important;
        transform: translateY(-2px);
        text-decoration: none !important;
    }

    .btn-support {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: transparent;
        color: #9ab5a0 !important;
        text-decoration: none !important;
        padding: 10px 22px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.88rem;
        border: 1.5px solid #d3e8d9;
        transition: all 0.25s ease;
    }

    .btn-support:hover {
        border-color: var(--green-main);
        color: var(--green-dark) !important;
        text-decoration: none !important;
    }
</style>

<div class="success-wrapper">
    <div class="success-card">

        <!-- Icon vòng tròn xanh -->
        <div class="success-icon-wrap">
            <i class="fas fa-check"></i>
        </div>

        <!-- Emoji confetti -->
        <div class="confetti-row">🎉🛍️🎊</div>

        <!-- Tiêu đề -->
        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-sub">
            Cảm ơn bạn đã tin tưởng <strong>GÌ CŨNG MÓC</strong>.<br>
            Đơn hàng của bạn đã được nhận và đang chờ xác nhận.
        </p>

        <!-- Thông tin đơn hàng -->
        <div class="order-info-box">
            <div class="info-row">
                <span class="info-label"><i class="fas fa-hashtag"></i> Mã đơn hàng</span>
                <span class="badge-order-id">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-money-bill-wave"></i> Tổng thanh toán</span>
                <span class="info-value total-price"><?php echo number_format($total, 0, ',', '.'); ?> ₫</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-truck"></i> Trạng thái</span>
                <span class="badge-status">✔ Chờ xác nhận</span>
            </div>
        </div>

        <!-- Ghi chú email -->
        <p class="email-note">
            <i class="fas fa-envelope"></i>
            Xác nhận đơn hàng sẽ được gửi đến email của bạn sớm nhất.
        </p>

        <!-- Nút hành động -->
        <div class="btn-row">
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders" class="btn-view-order">
                <i class="fas fa-box-open"></i> Xem đơn hàng
            </a>
            <a href="<?php echo BASE_URL; ?>index.php?url=Product/index" class="btn-continue">
                <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
            </a>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/contact" class="btn-support">
                <i class="fas fa-headset"></i> Liên hệ hỗ trợ
            </a>
        </div>

    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
