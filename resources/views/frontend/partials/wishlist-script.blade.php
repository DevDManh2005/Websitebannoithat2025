<style>
    .toggle-wishlist-btn {
        background-color: transparent;
        border: 1px solid #dc3545;
        color: #dc3545;
        transition: all 0.2s ease-in-out;
    }
    .toggle-wishlist-btn:hover {
        background-color: #dc3545;
        color: #fff;
    }
    .toggle-wishlist-btn.active {
        background-color: #dc3545;
        color: #fff;
    }
    /* Đảm bảo icon trái tim luôn là solid khi active */
    .toggle-wishlist-btn .fa-heart {
        font-weight: 400; /* far */
    }
    .toggle-wishlist-btn.active .fa-heart {
        font-weight: 900; /* fas */
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thêm CSRF token vào header của tất cả các request AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.toggle-wishlist-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            const productId = this.dataset.productId;

            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '{{ route("login.form") }}';
                    throw new Error('Unauthorized');
                }
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.status === 'success') {
                    this.classList.toggle('active', data.is_added);
                }
            })
            .catch(error => {
                if (error.message !== 'Unauthorized') {
                    console.error('Error:', error);
                }
            });
        });
    });
});
</script>
