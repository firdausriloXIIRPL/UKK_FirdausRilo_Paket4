// Preview image before upload
function previewImage(input) {
    const preview = document.getElementById('preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                preview.innerHTML = '';
                preview.appendChild(img);
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirm delete
function confirmDelete(message = 'Yakin ingin menghapus data ini?') {
    return confirm(message);
}

// Auto hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});

// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

// Add mobile menu button
if (window.innerWidth <= 768) {
    const topBar = document.querySelector('.top-bar');
    if (topBar && !document.querySelector('.menu-toggle')) {
        const menuBtn = document.createElement('button');
        menuBtn.className = 'btn btn-primary menu-toggle';
        menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        menuBtn.onclick = toggleSidebar;
        topBar.insertBefore(menuBtn, topBar.firstChild);
    }
}

// Table search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    const filter = input.value.toUpperCase();
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let txtValue = tr[i].textContent || tr[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'var(--danger)';
            isValid = false;
        } else {
            input.style.borderColor = 'var(--gray)';
        }
    });
    
    return isValid;
}

// Close alert manually
function closeAlert(element) {
    element.style.opacity = '0';
    setTimeout(() => {
        element.remove();
    }, 300);
}

// Add close button to alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.cssText = 'background:none; border:none; font-size:20px; cursor:pointer; margin-left:auto;';
        closeBtn.onclick = function() {
            closeAlert(alert);
        };
        alert.appendChild(closeBtn);
    });
});
