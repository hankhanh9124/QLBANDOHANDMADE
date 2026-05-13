// public/js/orders.js

document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = document.getElementById('cancelOrderModal');
    const cancelBtns = document.querySelectorAll('.btn-show-cancel-modal');
    const closeBtn = document.querySelector('.btn-not-now');
    const confirmBtn = document.getElementById('btnConfirmCancel');
    const reasonItems = document.querySelectorAll('.reason-item');
    let selectedOrderId = null;
    let selectedReason = null;

    // Open Modal
    cancelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            selectedOrderId = this.getAttribute('data-order-id');
            cancelModal.style.display = 'block';
            resetModal();
        });
    });

    // Close Modal
    closeBtn.addEventListener('click', function() {
        cancelModal.style.display = 'none';
        selectedOrderId = null;
    });

    // Handle Radio Selection
    reasonItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active from all
            reasonItems.forEach(i => i.classList.remove('active'));
            // Add to clicked
            this.classList.add('active');
            selectedReason = this.getAttribute('data-reason');
            
            // Activate confirm button
            confirmBtn.classList.add('active');
            confirmBtn.disabled = false;
        });
    });

    function resetModal() {
        reasonItems.forEach(i => i.classList.remove('active'));
        confirmBtn.classList.remove('active');
        confirmBtn.disabled = true;
        selectedReason = null;
    }

    // Confirm Cancellation
    confirmBtn.addEventListener('click', function() {
        if (!selectedOrderId || !selectedReason) return;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

        const formData = new FormData();
        formData.append('order_id', selectedOrderId);
        formData.append('reason', selectedReason);

        fetch('index.php?url=Page/cancelOrder', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - reload to show updated status
                window.location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra khi hủy đơn hàng.');
                this.disabled = false;
                this.innerHTML = 'HỦY ĐƠN HÀNG';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối server.');
            this.disabled = false;
            this.innerHTML = 'HỦY ĐƠN HÀNG';
        });
    });

    // Close when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target == cancelModal) {
            cancelModal.style.display = 'none';
            selectedOrderId = null;
        }
    });
});
