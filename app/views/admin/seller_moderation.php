<?php
// app/views/admin/seller_moderation.php
$requests = $requests ?? [];
?>
<!-- app/views/admin/seller_moderation.php -->
<div class="container-fluid my-5 px-md-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: #333;">Duyệt Người Bán Mới</h2>
        <div class="badge badge-primary px-3 py-2"><?php echo count($requests); ?> yêu cầu đang chờ</div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Người đăng ký</th>
                        <th class="border-0 py-3">Tên Shop</th>
                        <th class="border-0 py-3">Loại sản phẩm</th>
                        <th class="border-0 py-3">Ngày gửi</th>
                        <th class="border-0 px-4 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-5"></i>
                                Không có yêu cầu nào cần xử lý.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($request->user_name ?? ''); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($request->user_email ?? ''); ?></div>
                                </td>
                                <td class="py-3 align-middle font-weight-bold" style="color: #75c794;">
                                    <?php echo htmlspecialchars($request->shop_name ?? ''); ?>
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="badge badge-light px-2 py-1"><?php echo htmlspecialchars($request->product_types ?? ''); ?></span>
                                </td>
                                <td class="py-3 align-middle text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($request->created_at)); ?>
                                </td>
                                <td class="px-4 py-3 align-middle text-right">
                                    <button class="btn btn-sm btn-outline-info mr-1" data-toggle="modal" data-target="#modal-<?php echo $request->id; ?>">
                                        Chi tiết
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Admin/approveSeller/<?php echo $request->id; ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Bạn có chắc muốn phê duyệt shop này?')">
                                        Duyệt
                                    </a>
                                </td>
                            </tr>

                            <!-- Detail Modal -->
                            <div class="modal fade" id="modal-<?php echo $request->id; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 text-white" style="background: #75c794;">
                                            <h5 class="modal-title">Chi tiết đăng ký: <?php echo htmlspecialchars($request->shop_name ?? ''); ?></h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <h6 class="font-weight-bold text-uppercase small text-muted mb-3">Thông tin Shop</h6>
                                                    <p><strong>Tên Shop:</strong> <?php echo htmlspecialchars($request->shop_name ?? ''); ?></p>
                                                    <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($request->shop_description ?? '')); ?></p>
                                                    <p><strong>Sản phẩm:</strong> <?php echo htmlspecialchars($request->product_types ?? ''); ?></p>
                                                    <p><strong>Tài khoản:</strong> <?php echo htmlspecialchars($request->bank_account ?? ''); ?></p>

                                                    <?php if ($request->portfolio_links): ?>
                                                        <p><strong>Portfolio:</strong> <a href="<?php echo $request->portfolio_links; ?>" target="_blank"><?php echo $request->portfolio_links; ?></a></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-5">
                                                    <h6 class="font-weight-bold text-uppercase small text-muted mb-3">Giấy tờ xác minh</h6>
                                                    <?php if ($request->identity_proof): ?>
                                                        <img src="<?php echo BASE_URL . 'public/uploads/verification/' . $request->identity_proof; ?>" class="img-fluid rounded shadow-sm border">
                                                    <?php else: ?>
                                                        <div class="alert alert-light text-center py-4 border">Không có ảnh</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                            <button class="btn btn-danger" data-toggle="collapse" data-target="#reject-form-<?php echo $request->id; ?>">
                                                Từ chối
                                            </button>
                                            <a href="<?php echo BASE_URL; ?>index.php?url=Admin/approveSeller/<?php echo $request->id; ?>" class="btn btn-success">Duyệt Shop</a>
                                        </div>
                                        <!-- Reject Reason Form -->
                                        <div class="collapse p-4 bg-light border-top" id="reject-form-<?php echo $request->id; ?>">
                                            <form action="<?php echo BASE_URL; ?>index.php?url=Admin/rejectSeller" method="POST">
                                                <input type="hidden" name="request_id" value="<?php echo $request->id; ?>">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Lý do từ chối:</label>
                                                    <textarea name="reason" class="form-control" placeholder="VD: Ảnh CCCD mờ, Tên shop không phù hợp..." required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger btn-block">Xác nhận Từ chối</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.table td, .table th { border-top: 1px solid #f8f9fa; }
.modal-lg { max-width: 900px; }
</style>
