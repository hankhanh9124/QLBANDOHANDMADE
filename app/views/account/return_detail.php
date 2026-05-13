<style>
    :root {
        --primary-green: #21b859;
        --light-green: #f6fffe;
        --border-color: #e0e0e0;
        --status-pending: #f6a700;
        --status-reviewing: #21b859;
        --status-approved: #21b859;
        --status-rejected: #ee4d2d;
        --status-refunded: #21b859;
    }
    .return-detail-container {
        max-width: 1000px;
        margin: 30px auto;
    }
    .status-card {
        background: #fff;
        padding: 30px;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    /* Timeline Styles */
    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 40px;
        padding: 0 40px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 60px;
        right: 60px;
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
    }
    .timeline-item {
        position: relative;
        z-index: 2;
        text-align: center;
        width: 120px;
    }
    .timeline-dot {
        width: 32px;
        height: 32px;
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e0e0e0;
        transition: all 0.3s;
    }
    .timeline-item.active .timeline-dot {
        border-color: var(--primary-green);
        background: var(--primary-green);
        color: #fff;
        box-shadow: 0 0 0 4px var(--light-green);
    }
    .timeline-item.completed .timeline-dot {
        border-color: var(--primary-green);
        background: #fff;
        color: var(--primary-green);
    }
    .timeline-text {
        font-size: 13px;
        color: #999;
        font-weight: 500;
    }
    .timeline-item.active .timeline-text {
        color: var(--primary-green);
    }
    .timeline-item.active.rejected .timeline-dot {
        background: var(--status-rejected);
        border-color: var(--status-rejected);
        box-shadow: 0 0 0 4px #fff5f5;
    }
    .timeline-item.active.rejected .timeline-text {
        color: var(--status-rejected);
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    .info-card {
        background: #fff;
        padding: 25px;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .info-title {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f5f5f5;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .media-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .media-item {
        width: 100px;
        height: 100px;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #eee;
        cursor: pointer;
    }
    .media-item img, .media-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .history-list {
        list-style: none;
        padding: 0;
    }
    .history-item {
        padding-left: 20px;
        border-left: 2px solid #f0f0f0;
        position: relative;
        padding-bottom: 20px;
    }
    .history-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 0;
        width: 12px;
        height: 12px;
        background: #ddd;
        border-radius: 50%;
    }
    .history-item.latest::before {
        background: var(--primary-green);
    }
    .history-time {
        font-size: 12px;
        color: #999;
    }
    .history-note {
        font-size: 14px;
        color: #555;
        margin-top: 5px;
    }
</style>

<div class="container return-detail-container">
<?php if (!isset($return) || !$return): ?>
    <div class="alert alert-warning text-center py-5">
        <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block" style="color: #f6a700;"></i>
        <h5>Không tìm thấy yêu cầu trả hàng</h5>
        <p class="text-muted mb-3">Yêu cầu này không tồn tại hoặc bạn không có quyền xem.</p>
        <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại đơn hàng
        </a>
    </div>
<?php else: ?>
    <!-- Status Timeline -->
    <div class="status-card">
        <div class="timeline">
            <?php 
            $steps = [
                'pending'   => 'Yêu cầu gửi',
                'reviewing' => 'Đang xem xét',
                'approved'  => 'Đã phê duyệt',
                'refunded'  => 'Hoàn tiền'
            ];

            if ($return->status == 'rejected') {
                $steps['rejected'] = 'Bị từ chối';
                unset($steps['approved'], $steps['refunded']);
            }

            $currentStatus = $return->status;
            $foundCurrent  = false;

            foreach ($steps as $key => $label) {
                $class = '';
                if ($key == $currentStatus) {
                    $class = 'active';
                    if ($key == 'rejected') $class .= ' rejected';
                    $foundCurrent = true;
                } elseif (!$foundCurrent) {
                    $class = 'completed';
                }

                // Xác định icon
                if ($class == 'completed') {
                    $icon = '<i class="fas fa-check"></i>';
                } elseif (strpos($class, 'rejected') !== false) {
                    $icon = '<i class="fas fa-times"></i>';
                } else {
                    $icon = '<i class="fas fa-dot-circle"></i>';
                }

                echo '<div class="timeline-item ' . $class . '">';
                echo '  <div class="timeline-dot">' . $icon . '</div>';
                echo '  <div class="timeline-text">' . htmlspecialchars($label) . '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="text-center">
            <?php if ($return->status == 'pending'): ?>
                <div style="color: var(--status-pending); font-weight: 500;">Chờ phản hồi từ người bán</div>
                <div class="text-muted small mt-1">Yêu cầu của bạn đang được chuyển đến người bán để xem xét.</div>
            <?php elseif ($return->status == 'reviewing'): ?>
                <div style="color: var(--status-reviewing); font-weight: 500;">Người bán đang xem xét bằng chứng</div>
                <div class="text-muted small mt-1">Vui lòng theo dõi tin nhắn nếu người bán cần thêm thông tin.</div>
            <?php elseif ($return->status == 'refunded'): ?>
                <div style="color: var(--status-refunded); font-weight: 500;">Hoàn tiền thành công</div>
                <div class="text-muted small mt-1">Số tiền ₫<?php echo number_format($return->amount, 0, ',', '.'); ?> đã được hoàn lại.</div>
            <?php elseif ($return->status == 'rejected'): ?>
                <div style="color: var(--status-rejected); font-weight: 600; font-size: 1.1rem;">Rất tiếc, yêu cầu của bạn đã bị từ chối</div>
                <div class="mt-3 p-3 rounded" style="background: #fff5f5; border: 1px solid #fed7d7; display: inline-block; text-align: left; max-width: 500px;">
                    <div class="font-weight-bold mb-1" style="color: #c53030;"><i class="fas fa-info-circle mr-2"></i>Lý do từ chối từ Người bán:</div>
                    <div style="color: #742a2a; font-size: 14px; line-height: 1.5;">
                        <?php 
                        $latestNote = '';
                        foreach (($return->history ?? []) as $h) {
                            if ($h->status == 'rejected' && !empty($h->note)) {
                                $latestNote = $h->note;
                                break;
                            }
                        }
                        echo !empty($latestNote) ? nl2br(htmlspecialchars($latestNote)) : 'Người bán không cung cấp lý do chi tiết. Vui lòng chat với người bán để biết thêm thông tin.';
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="detail-grid">
        <div class="left-col">
            <!-- Product Info -->
            <div class="info-card">
                <div class="info-title">Sản phẩm yêu cầu trả hàng</div>
                <?php foreach (($order?->items ?? []) as $item): 
                    $pImg = $item->image ?? '';
                    $finalPImg = (strpos($pImg, 'public/') === false) ?
                        ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                        $pImg;
                ?>
                    <div class="d-flex align-items-center p-2 mb-2 rounded" style="background: #fcfcfc; border: 1px solid #f5f5f5;">
                        <img src="<?php echo BASE_URL . $finalPImg; ?>" width="60" height="60" class="rounded mr-3" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <div style="font-weight: 500; font-size: 14px; color: #333;"><?php echo htmlspecialchars($item->name ?? ''); ?></div>
                            <div class="small text-muted">Số lượng: x<?php echo $item->quantity ?? 0; ?></div>
                        </div>
                        <div class="text-right">
                            <div style="color: #ee4d2d; font-weight: 500;">₫<?php echo number_format($item->price ?? 0, 0, ',', '.'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Details -->
            <div class="info-card">
                <div class="info-title">Thông tin yêu cầu</div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Lý do:</label>
                    <div style="font-weight: 500;"><?php echo htmlspecialchars($return->reason ?? ''); ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Mô tả:</label>
                    <div style="font-size: 14px; line-height: 1.6; color: #666;"><?php echo nl2br(htmlspecialchars($return->description ?? '')); ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Số tiền hoàn lại:</label>
                    <div style="color: var(--primary-green); font-size: 18px; font-weight: 600;">₫<?php echo number_format($return->amount ?? 0, 0, ',', '.'); ?></div>
                </div>
            </div>

            <!-- Proof Media -->
            <div class="info-card">
                <div class="info-title">Hình ảnh / Video bằng chứng</div>
                <div class="media-grid">
                    <?php if (empty($return->media)): ?>
                        <div class="text-muted small">Không có bằng chứng được tải lên.</div>
                    <?php else: ?>
                        <?php foreach ($return->media as $media): ?>
                            <div class="media-item" onclick="viewFullMedia('<?php echo BASE_URL . $media->file_path; ?>', '<?php echo $media->file_type; ?>')">
                                <?php if ($media->file_type == 'image'): ?>
                                    <img src="<?php echo BASE_URL . $media->file_path; ?>">
                                <?php else: ?>
                                    <video src="<?php echo BASE_URL . $media->file_path; ?>" muted></video>
                                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#fff; pointer-events:none;"><i class="fas fa-play"></i></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="right-col">
            <!-- Timeline History -->
            <div class="info-card">
                <div class="info-title">Lịch sử tiến trình</div>
                <ul class="history-list">
                    <?php foreach (($return->history ?? []) as $index => $h): ?>
                        <li class="history-item <?php echo $index === 0 ? 'latest' : ''; ?>">
                            <div class="history-time"><?php echo date('H:i d/m/Y', strtotime($h->created_at)); ?></div>
                            <div class="history-note"><?php echo htmlspecialchars($h->note ?? ''); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Chat Button -->
            <?php 
            $firstItem = $order?->items[0] ?? null;
            $pImg = $firstItem ? ($firstItem->image ?? '') : '';
            $finalPImg = (strpos($pImg, 'public/') === false) ?
                ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                $pImg;
            ?>
            <?php if ($firstItem): ?>
            <button class="btn btn-primary w-100 open-chat-with-product" 
                data-id="<?php echo $firstItem->product_id ?? ''; ?>"
                data-seller-id="<?php echo (isset($firstItem->seller_role) && $firstItem->seller_role === 'admin') ? 0 : ($firstItem->seller_id ?? 0); ?>"
                data-name="<?php echo htmlspecialchars($firstItem->name ?? ''); ?>"
                data-price="<?php echo number_format($firstItem->price ?? 0, 0, ',', '.'); ?>₫"
                data-image="<?php echo BASE_URL . $finalPImg; ?>"
                style="background: var(--primary-green); border: none; padding: 12px;">
                <i class="fas fa-comments mr-2"></i> Chat với Người bán
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Simple Media Modal -->
<div id="mediaModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; display:none; align-items:center; justify-content:center;" onclick="this.style.display='none'">
    <div id="mediaContent" style="max-width:90%; max-height:90%;"></div>
</div>

<script>
    function viewFullMedia(path, type) {
        const modal = document.getElementById('mediaModal');
        const content = document.getElementById('mediaContent');
        if (type === 'image') {
            content.innerHTML = `<img src="${path}" style="max-width:100%; max-height:90vh; border-radius:4px;">`;
        } else {
            content.innerHTML = `<video src="${path}" controls autoplay style="max-width:100%; max-height:90vh; border-radius:4px;"></video>`;
        }
        modal.style.display = 'flex';
    }
</script>
