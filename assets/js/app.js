document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initDropdowns();
    initModals();
    initTabs();
    initFormValidation();
    initSearchDebounce();
    initTooltips();
});

function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    
    if (sidebarToggle && sidebar) {
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true' && window.innerWidth > 992) {
            sidebar.classList.add('collapsed');
        }
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }
    
    if (mobileMenuToggle && sidebar && mobileOverlay) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            mobileOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        });
        
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992 && sidebar) {
            sidebar.classList.remove('mobile-open');
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
}

function initDropdowns() {
    document.addEventListener('click', function(e) {
        const allDropdowns = document.querySelectorAll('.dropdown');
        allDropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    });
    
    document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.closest('.dropdown');
            const isActive = dropdown.classList.contains('active');
            
            document.querySelectorAll('.dropdown').forEach(function(d) {
                d.classList.remove('active');
            });
            
            if (!isActive) {
                dropdown.classList.add('active');
            }
        });
    });
}

function initModals() {
    document.querySelectorAll('[data-modal]').forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    document.querySelectorAll('.modal-close, [data-dismiss="modal"]').forEach(function(closeBtn) {
        closeBtn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });
    
    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
        backdrop.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-backdrop');
            if (modalId) {
                closeModal(modalId);
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                closeModal(activeModal.id);
            }
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.querySelector('[data-modal-backdrop="' + modalId + '"]');
    
    if (modal) {
        modal.classList.add('active');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        const firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
        if (firstInput) {
            setTimeout(function() { firstInput.focus(); }, 100);
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.querySelector('[data-modal-backdrop="' + modalId + '"]');
    
    if (modal) {
        modal.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }
}

window.openModal = openModal;
window.closeModal = closeModal;

function initTabs() {
    document.querySelectorAll('.tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            const tabGroup = this.closest('.tabs');
            const tabPanels = document.querySelectorAll('[data-tab-panel]');
            const targetPanel = this.getAttribute('data-tab');
            
            tabGroup.querySelectorAll('.tab').forEach(function(t) {
                t.classList.remove('active');
            });
            this.classList.add('active');
            
            tabPanels.forEach(function(panel) {
                if (panel.getAttribute('data-tab-panel') === targetPanel) {
                    panel.style.display = 'block';
                } else {
                    panel.style.display = 'none';
                }
            });
        });
    });
}

function initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            form.querySelectorAll('[required]').forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    showFieldError(field, 'This field is required');
                } else {
                    field.classList.remove('is-invalid');
                    hideFieldError(field);
                }
            });
            
            form.querySelectorAll('[type="email"]').forEach(function(field) {
                if (field.value && !isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    showFieldError(field, 'Please enter a valid email address');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
        
        form.querySelectorAll('[required]').forEach(function(field) {
            field.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'This field is required');
                } else {
                    this.classList.remove('is-invalid');
                    hideFieldError(this);
                }
            });
            
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                    hideFieldError(this);
                }
            });
        });
    });
}

function showFieldError(field, message) {
    hideFieldError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'form-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function hideFieldError(field) {
    const existingError = field.parentNode.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function initSearchDebounce() {
    document.querySelectorAll('[data-search]').forEach(function(input) {
        let timeout;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const form = this.closest('form');
            timeout = setTimeout(function() {
                if (form) form.submit();
            }, 500);
        });
    });
}

function initTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(function(el) {
        el.classList.add('tooltip');
    });
}

function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        const originalText = button.innerHTML;
        button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"></polyline></svg> Copied!';
        setTimeout(function() {
            button.innerHTML = originalText;
        }, 2000);
    });
}

window.copyToClipboard = copyToClipboard;

function confirmAction(message) {
    return confirm(message);
}

window.confirmAction = confirmAction;

function showNotification(message, type) {
    type = type || 'info';
    const notification = document.createElement('div');
    notification.className = 'alert alert-' + type + ' notification-toast';
    notification.innerHTML = '<span>' + message + '</span>';
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; animation: slideIn 0.3s ease;';
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 3000);
}

window.showNotification = showNotification;

const style = document.createElement('style');
style.textContent = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }';
document.head.appendChild(style);

function handleAjaxForm(form, callback) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
        }
        
        fetch(form.action, {
            method: form.method || 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (callback) callback(data);
            if (data.success) {
                showNotification(data.message || 'Success!', 'success');
            } else {
                showNotification(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(function(error) {
            showNotification('An error occurred. Please try again.', 'danger');
        })
        .finally(function() {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });
}

window.handleAjaxForm = handleAjaxForm;

function sameAsCurrentAddress() {
    const checkbox = document.getElementById('same_address');
    if (checkbox && checkbox.checked) {
        const fields = ['house_street', 'barangay', 'city', 'province', 'zip'];
        fields.forEach(function(field) {
            const curr = document.getElementById('curr_' + field);
            const perm = document.getElementById('perm_' + field);
            if (curr && perm) {
                perm.value = curr.value;
            }
        });
    }
}

window.sameAsCurrentAddress = sameAsCurrentAddress;

function initMultiStepForm() {
    const form = document.querySelector('.multi-step-form');
    if (!form) return;
    
    const steps = form.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step');
    let currentStep = 0;
    
    function showStep(index) {
        steps.forEach(function(step, i) {
            step.style.display = i === index ? 'block' : 'none';
        });
        
        stepIndicators.forEach(function(indicator, i) {
            indicator.classList.remove('active', 'completed');
            if (i < index) {
                indicator.classList.add('completed');
            } else if (i === index) {
                indicator.classList.add('active');
            }
        });
        
        currentStep = index;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    form.querySelectorAll('.btn-next').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const currentStepEl = steps[currentStep];
            const requiredFields = currentStepEl.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (isValid && currentStep < steps.length - 1) {
                showStep(currentStep + 1);
            } else if (!isValid) {
                const firstInvalid = currentStepEl.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    });
    
    form.querySelectorAll('.btn-prev').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        });
    });
    
    showStep(0);
}

window.initMultiStepForm = initMultiStepForm;

function handleDepartmentChange() {
    const dept = document.getElementById('department');
    const gradeLevel = document.getElementById('grade_level');
    const strandGroup = document.getElementById('strand_group');
    
    if (!dept || !gradeLevel) return;
    
    dept.addEventListener('change', function() {
        const options = gradeLevel.querySelectorAll('option');
        options.forEach(function(opt) {
            if (this.value === 'JHS') {
                opt.style.display = (opt.value.includes('7') || opt.value.includes('8') || opt.value.includes('9') || opt.value.includes('10') || opt.value === '') ? '' : 'none';
            } else if (this.value === 'SHS') {
                opt.style.display = (opt.value.includes('11') || opt.value.includes('12') || opt.value === '') ? '' : 'none';
            }
        }.bind(this));
        
        gradeLevel.value = '';
        
        if (strandGroup) {
            strandGroup.style.display = this.value === 'SHS' ? 'block' : 'none';
        }
    });
}

window.handleDepartmentChange = handleDepartmentChange;
