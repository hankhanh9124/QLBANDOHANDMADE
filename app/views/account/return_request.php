<?php
/**
 * @var object $order
 */
?>
<style>
    :root {
        --primary-green: #21b859;
        --light-green: #f6fffe;
        --border-color: #e0e0e0;
    }
    .return-container {
        max-width: 800px;
        margin: 30px auto;
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .return-header {
        padding: 20px 30px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        background: var(--light-green);
    }
    .return-header h4 {
        margin: 0;
        color: var(--primary-green);
        font-weight: 500;
    }
    .return-body {
        padding: 30px;
    }
    .product-summary {
        display: flex;
        padding: 15px;
        background: #fafafa;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        margin-bottom: 25px;
    }
    .product-summary img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 15px;
        border-radius: 2px;
    }
    .form-group label {
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
        color: #555;
    }
    .custom-select-return {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        outline: none;
        transition: border-color 0.2s;
    }
    .custom-select-return:focus {
        border-color: var(--primary-green);
    }
    .upload-area {
        border: 2px dashed var(--border-color);
        border-radius: 4px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fdfdfd;
    }
    .upload-area:hover {
        border-color: var(--primary-green);
        background: var(--light-green);
    }
    .upload-area i {
        font-size: 2rem;
        color: #aaa;
        margin-bottom: 10px;
    }
    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    .preview-item {
        width: 100px;
        height: 100px;
        position: relative;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    .preview-item img, .preview-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .submit-btn {
        background: var(--primary-green);
        color: #fff;
        border: none;
        padding: 12px 40px;
        border-radius: 2px;
        font-weight: 500;
        width: 100%;
        margin-top: 20px;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .submit-btn:hover {
        opacity: 0.9;
    }
</style>

<div class="container">
    <div class="return-container">
        <div class="return-header">
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders" class="mr-3 text-muted"><i class="fas fa-arrow-left"></i></a>
            <h4>Yêu Cầu Trả Hàng / Hoàn Tiền</h4>
        </div>
        
        <form action="<?php echo BASE_URL; ?>index.php?url=Return/submit" method="POST" enctype="multipart/form-data" class="return-body">
            <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
            <input type="hidden" name="amount" value="<?php echo $order->total; ?>">

            <div class="product-summary">
                <?php 
                $firstItem = $order?->items[0] ?? null;
                $pImg = $firstItem ? ($firstItem->image ?? '') : '';
                $finalPImg = (strpos($pImg, 'public/') === false) ?
                    ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                    $pImg;
                ?>
                <img src="<?php echo BASE_URL . $finalPImg; ?>" alt="">
                <div>
                    <div style="font-weight: 500;"><?php echo htmlspecialchars($firstItem?->name ?? 'Đơn hàng #' . ($order->id ?? '')); ?></div>
                    <div class="text-muted small">Mã đơn hàng: #<?php echo $order->id; ?></div>
                    <div class="text-danger mt-1">Số tiền hoàn: ₫<?php echo number_format($order->total, 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label>Lý do Trả hàng/Hoàn tiền</label>
                <select name="reason" class="custom-select-return" required>
                    <option value="" disabled selected>Chọn lý do phù hợp...</option>
                    <option value="Sản phẩm khác với mô tả">Sản phẩm khác với mô tả</option>
                    <option value="Sản phẩm bị lỗi/hư hỏng">Sản phẩm bị lỗi/hư hỏng</option>
                    <option value="Giao sai sản phẩm">Giao sai sản phẩm</option>
                    <option value="Giao thiếu sản phẩm">Giao thiếu sản phẩm</option>
                    <option value="Sản phẩm đã qua sử dụng/hàng giả">Sản phẩm đã qua sử dụng/hàng giả</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label>Mô tả chi tiết</label>
                <textarea name="description" class="custom-select-return" rows="4" placeholder="Vui lòng cung cấp thêm thông tin về tình trạng sản phẩm để shop xử lý nhanh hơn..." required></textarea>
            </div>

            <div class="form-group">
                <label>Bằng chứng (Ảnh/Video)</label>
                <div class="upload-area" onclick="document.getElementById('mediaInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p class="m-0">Nhấn để tải lên ảnh hoặc video bằng chứng</p>
                    <p class="text-muted small">Tối đa 5 file, dung lượng mỗi file không quá 10MB</p>
                    <input type="file" name="media[]" id="mediaInput" multiple hidden accept="image/*,video/*" onchange="previewFiles(this)">
                </div>
                <div id="previewContainer" class="preview-container"></div>
            </div>

            <button type="submit" class="submit-btn">GỬI YÊU CẦU</button>
        </form>
    </div>
</div>

<script>
    function previewFiles(input) {
        const container = document.getElementById('previewContainer');
        container.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    
                    if (file.type.startsWith('image/')) {
                        item.innerHTML = `<img src="${e.target.result}">`;
                    } else if (file.type.startsWith('video/')) {
                        item.innerHTML = `<video src="${e.target.result}" muted></video><div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#fff;"><i class="fas fa-play"></i></div>`;
                    }
                    
                    container.appendChild(item);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>
