// JavaScript untuk Sistem Manajemen Barang

document.addEventListener('DOMContentLoaded', function() {
    
    // Konfirmasi sebelum menghapus data
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // Validasi form tambah/ubah barang
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const numericFields = form.querySelectorAll('input[type="number"]');
            let isValid = true;

            numericFields.forEach(field => {
                if (field.value < 0) {
                    alert('Nilai tidak boleh negatif!');
                    field.focus();
                    isValid = false;
                    e.preventDefault();
                }
            });

            // Validasi harga jual > harga beli
            const hargaJual = form.querySelector('input[name="harga_jual"]');
            const hargaBeli = form.querySelector('input[name="harga_beli"]');
            
            if (hargaJual && hargaBeli && parseFloat(hargaJual.value) <= parseFloat(hargaBeli.value)) {
                alert('Harga jual harus lebih besar dari harga beli!');
                hargaJual.focus();
                isValid = false;
                e.preventDefault();
            }

            return isValid;
        });
    });

    // Auto-format angka untuk input harga
    const priceInputs = document.querySelectorAll('input[name="harga_jual"], input[name="harga_beli"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toLocaleString('id-ID');
            }
        });

        input.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '');
        });
    });

    // Search functionality untuk tabel
    const addSearchFunctionality = () => {
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            // Cek jika sudah ada search box
            if (!table.previousElementSibling || !table.previousElementSibling.classList.contains('search-container')) {
                const searchContainer = document.createElement('div');
                searchContainer.className = 'search-container';
                searchContainer.innerHTML = `
                    <input type="text" placeholder="Cari data..." class="search-input">
                    <button class="search-btn">Cari</button>
                    <button class="reset-btn">Reset</button>
                `;
                
                table.parentNode.insertBefore(searchContainer, table);

                const searchInput = searchContainer.querySelector('.search-input');
                const searchBtn = searchContainer.querySelector('.search-btn');
                const resetBtn = searchContainer.querySelector('.reset-btn');

                const performSearch = () => {
                    const searchTerm = searchInput.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                };

                searchBtn.addEventListener('click', performSearch);
                searchInput.addEventListener('keyup', performSearch);
                
                resetBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => row.style.display = '');
                });
            }
        });
    };

    // Panggil search functionality
    addSearchFunctionality();

    // Notifikasi sukses
    const showNotification = (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    };

    // CSS animations untuk notifikasi
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .search-container {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }
        .search-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .search-btn, .reset-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .search-btn {
            background: #7b4bff;
            color: white;
        }
        .reset-btn {
            background: #6c757d;
            color: white;
        }
    `;
    document.head.appendChild(style);

    // Cek jika ada parameter success di URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        showNotification('Operasi berhasil dilakukan!', 'success');
    }

});

// Fungsi untuk preview gambar sebelum upload
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    const reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        preview.style.display = 'none';
    }
}

// assets/js/auth.js

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const loginBtn = document.querySelector('.login-btn');
    const passwordInput = document.getElementById('password');
    
    // Add password toggle functionality
    const passwordGroup = document.querySelector('.input-group:has(#password)');
    if (passwordInput && passwordGroup) {
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'toggle-password';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        
        // Add icon library if not present
        if (!document.querySelector('link[href*="font-awesome"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
            document.head.appendChild(link);
        }
        
        toggleBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? 
                '<i class="fas fa-eye"></i>' : 
                '<i class="fas fa-eye-slash"></i>';
        });
        
        passwordGroup.appendChild(toggleBtn);
    }
    
    // Form submission handling
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = passwordInput.value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                showError('Harap isi semua field!');
                return;
            }
            
            // Add loading state
            if (loginBtn) {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                loginBtn.innerHTML = 'Memproses...';
            }
        });
    }
    
    // Auto-focus username field
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.focus();
    }
    
    // Add hover effect to login info
    const loginInfo = document.querySelector('.login-info');
    if (loginInfo) {
        loginInfo.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        loginInfo.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
});

function showError(message) {
    let errorDiv = document.querySelector('.error-message');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        const form = document.querySelector('.login-form');
        if (form) {
            form.parentNode.insertBefore(errorDiv, form);
        }
    }
    
    errorDiv.textContent = message;
    
    // Remove error after 5 seconds
    setTimeout(() => {
        errorDiv.style.opacity = '0';
        errorDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 300);
    }, 5000);
}