<?php 
/**
 * @var object $return
 * @var object|null $order
 */
include 'app/views/dashboard/header.php'; 
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Xử lý yêu cầu #RET-<?php echo str_pad($return->id, 4, '0', STR_PAD_LEFT); ?></h1>
            <p class="text-muted mb-0">Từ khách hàng: <strong><?php echo htmlspecialchars($return->user_name ?? ''); ?></strong></p>
        </div>
        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/returns" class="btn btn-light shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thông tin yêu cầu -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 font-weight-bold d-flex align-items-center" style="color: #21b859;">
                        <i class="fas fa-info-circle mr-2"></i> Chi tiết khiếu nại
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-lg h-100">
                                <label class="small text-muted text-uppercase font-weight-bold mb-2 d-block">Lý do trả hàng</label>
                                <p class="h5 mb-0 text-dark" style="font-weight: 600;"><?php echo htmlspecialchars($return->reason ?? ''); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-lg h-100" style="border-left: 4px solid #e74a3b;">
                                <label class="small text-muted text-uppercase font-weight-bold mb-2 d-block">Số tiền hoàn lại</label>
                                <p class="h4 mb-0 text-danger" style="font-weight: 700;">₫<?php echo number_format($return->amount ?? 0, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="small text-muted text-uppercase font-weight-bold mb-2 d-block">Mô tả từ khách hàng</label>
                        <div class="p-3 rounded" style="background-color: #f8f9fc; border: 1px solid #e3e6f0; font-size: 1.05rem; line-height: 1.6; color: #4a4a4a;">
                            <?php echo nl2br(htmlspecialchars($return->description ?? '')); ?>
                        </div>
                    </div>

                    <label class="small text-muted text-uppercase font-weight-bold mb-3 d-block">Hình ảnh & Video bằng chứng</label>
                    <div class="row no-gutters">
                        <?php if (empty($return->media)): ?>
                            <div class="col-12 text-center py-5 bg-light rounded border" style="border-style: dashed !important; border-width: 2px !important;">
                                <i class="fas fa-image fa-3x text-gray-300 mb-3 d-block"></i>
                                <span class="text-muted">Khách hàng không cung cấp bằng chứng hình ảnh.</span>
                            </div>
                        <?php else: ?>
                            <?php foreach ($return->media as $media): ?>
                                <div class="col-6 col-md-3 p-2">
                                    <div class="evidence-item rounded overflow-hidden shadow-sm" style="height: 160px; position: relative; border: 1px solid #eaecf4;">
                                        <?php if ($media->file_type == 'image'): ?>
                                            <img src="<?php echo BASE_URL . $media->file_path; ?>" class="w-100 h-100" style="object-fit: cover; cursor: pointer; transition: transform 0.3s;" onclick="window.open(this.src)" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                            <div class="evidence-type"><i class="fas fa-image mr-1"></i> Ảnh</div>
                                        <?php else: ?>
                                            <video src="<?php echo BASE_URL . $media->file_path; ?>" class="w-100 h-100" style="object-fit: cover;"></video>
                                            <div class="evidence-overlay d-flex align-items-center justify-content-center" onclick="window.open('<?php echo BASE_URL . $media->file_path; ?>')">
                                                <i class="fas fa-play-circle fa-3x text-white shadow"></i>
                                            </div>
                                            <div class="evidence-type bg-danger text-white"><i class="fas fa-video mr-1"></i> Video</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-shopping-cart mr-2"></i> Đơn hàng: #<?php echo $return->order_number ?? ($return->order_id ?? 'N/A'); ?>
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase" style="border-bottom: 2px solid #f8f9fc;">
                                    <th class="pl-0">Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right pr-0">Đơn giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($order?->items ?? []) as $item): 
                                    $pImg = $item->image ?? '';
                                    $finalPImg = (strpos($pImg, 'public/') === false) ?
                                        ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                                        $pImg;
                                ?>
                                    <tr style="border-bottom: 1px solid #f8f9fc;">
                                        <td class="pl-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo BASE_URL . $finalPImg; ?>" width="55" height="55" class="rounded shadow-sm mr-3" style="object-fit: cover;">
                                                <div>
                                                    <div class="font-weight-bold text-dark" style="font-size: 1rem;"><?php echo htmlspecialchars($item->name ?? ''); ?></div>
                                                    <div class="small text-muted">Mã SP: #<?php echo $item->product_id ?? ''; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge badge-light px-3 py-2" style="font-size: 0.9rem; color: #5a5c69;">x<?php echo $item->quantity; ?></span>
                                        </td>
                                        <td class="text-right py-3 pr-0">
                                            <span class="font-weight-bold" style="color: #4e73df;">₫<?php echo number_format($item->price, 0, ',', '.'); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Xử lý -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden" style="border-radius: 15px;">
                <div class="card-header text-white py-3 border-0" style="background-color: #21b859;">
                    <h6 class="m-0 font-weight-bold d-flex align-items-center">
                        <i class="fas fa-tasks mr-2"></i> Quyết định của Admin
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateReturnStatus" method="POST">
                        <input type="hidden" name="id" value="<?php echo $return->id; ?>">
                        
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Trạng thái mới:</label>
                            <select name="status" class="form-control custom-select-lg" style="height: 50px; border-radius: 10px;">
                                <option value="pending" <?php echo $return->status == 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                <option value="reviewing" <?php echo $return->status == 'reviewing' ? 'selected' : ''; ?>>Đang xem xét bằng chứng</option>
                                <option value="approved" <?php echo $return->status == 'approved' ? 'selected' : ''; ?>>Đồng ý (Chờ gửi hàng/hoàn tiền)</option>
                                <option value="rejected" <?php echo $return->status == 'rejected' ? 'selected' : ''; ?>>Từ chối khiếu nại</option>
                                <option value="refunded" <?php echo $return->status == 'refunded' ? 'selected' : ''; ?>>Đã hoàn tất hoàn tiền</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted mb-2">Ghi chú (Gửi đến khách hàng):</label>
                            <textarea name="note" class="form-control" rows="5" style="border-radius: 10px; resize: none;" placeholder="Vd: Chúng tôi đã nhận được bằng chứng và đang xử lý..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-block py-3 shadow-sm text-white" style="background-color: #21b859ff; border-radius: 10px; font-weight: 700; font-size: 1rem; letter-spacing: 0.5px;">
                            <i class="fas fa-check-circle mr-1"></i> XÁC NHẬN CẬP NHẬT
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lịch sử Timeline -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-history mr-2"></i> Lịch sử xử lý
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="timeline p-4">
                        <?php if (empty($return->history)): ?>
                            <div class="text-center py-3 text-muted small">Chưa có lịch sử cập nhật</div>
                        <?php else: ?>
                            <?php foreach ($return->history as $h): ?>
                                <div class="timeline-item border-left pb-4 pl-4 position-relative" style="border-width: 2px !important; border-color: #e3e6f0 !important;">
                                    <div class="timeline-dot shadow-sm" style="background-color: #21b859; width: 12px; height: 12px; top: 0;"></div>
                                    <div class="small text-muted font-weight-bold mb-1">
                                        <i class="far fa-clock mr-1"></i> <?php echo date('d/m/Y H:i', strtotime($h->created_at)); ?>
                                    </div>
                                    <div class="badge badge-pill badge-light text-uppercase mb-2" style="color: #21b859; font-size: 0.7rem; font-weight: 700; padding: 5px 10px;"><?php echo htmlspecialchars($h->status ?? ''); ?></div>
                                    <div class="small text-dark" style="line-height: 1.5;"><?php echo htmlspecialchars($h->note ?? 'Cập nhật trạng thái'); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed { border: 2px dashed #e3e6f0; }
    .evidence-item:hover .evidence-overlay { opacity: 1; }
    .evidence-overlay { 
        position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
        background: rgba(0,0,0,0.4); opacity: 0; transition: 0.3s; cursor: pointer;
    }
    .evidence-type {
        position: absolute; top: 5px; right: 5px; 
        background: rgba(0,0,0,0.6); color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;
    }
    .timeline-dot {
        width: 12px; height: 12px; border-radius: 50%; 
        position: absolute; left: -7px; top: 4px;
    }
    .timeline-item:last-child { border-left: none !important; }
    .card-header { border-bottom: 1px solid #f8f9fc; }
</style>

<?php include 'app/views/dashboard/footer.php'; ?>
