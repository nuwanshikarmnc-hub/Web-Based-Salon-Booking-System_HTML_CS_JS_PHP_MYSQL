// Mobile menu
document.getElementById('menuIcon')?.addEventListener('click', () => {
    document.getElementById('navMenu').classList.toggle('show-menu');
});

// Load services
fetch('/saloon/php/get_services.php')
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(services => {
        const grid = document.getElementById('servicesGrid');
        if (!grid) return;
        grid.innerHTML = '';
        if (!services || services.length === 0) {
            grid.innerHTML = '<div style="text-align:center;padding:40px;">No services found</div>';
            return;
        }
        services.forEach(svc => {
            grid.innerHTML += `
                <label class="service-card">
                    <input type="checkbox" name="services[]" value="${svc.name}">
                    <div class="service-card-inner">
                        <div class="check-mark"><i class="fas fa-check-circle"></i></div>
                        <div class="svc-icon"><i class="fas ${svc.icon || 'fa-spa'}"></i></div>
                        <h4>${svc.name}</h4>
                        <div class="svc-meta">
                            <span class="svc-time">🕒 ${svc.duration || 'N/A'}</span>
                            <span class="svc-price">${svc.price || 'N/A'}</span>
                        </div>
                    </div>
                </label>
            `;
        });
    })
    .catch(error => {
        document.getElementById('servicesGrid').innerHTML = '<div style="text-align:center;padding:40px;color:#c0392b;">Failed to load services</div>';
        console.error('Services error:', error);
    });

// Load staff
fetch('/saloon/php/get_staff.php')
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(staff => {
        const grid = document.getElementById('staffGrid');
        if (!grid) return;
        grid.innerHTML = '';
        if (!staff || staff.length === 0) {
            grid.innerHTML = '<div style="text-align:center;padding:40px;">No staff found</div>';
            return;
        }
        staff.forEach(member => {
            grid.innerHTML += `
                <label class="staff-card-item">
                    <input type="radio" name="beautician" value="${member.name}">
                    <div class="staff-card-inner">
                        <div class="check-mark"><i class="fas fa-check-circle"></i></div>
                        <div class="staff-avatar"><i class="fas fa-user-circle"></i></div>
                        <h4>${member.name}</h4>
                        <div class="staff-spec">${member.role}</div>
                        <div class="staff-exp">${member.experience || 0}+ years</div>
                    </div>
                </label>
            `;
        });
    })
    .catch(error => {
        document.getElementById('staffGrid').innerHTML = '<div style="text-align:center;padding:40px;color:#c0392b;">Failed to load staff</div>';
        console.error('Staff error:', error);
    });

// Date picker
const dateInput = document.getElementById('appt_date');
if (dateInput) {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    dateInput.min = `${yyyy}-${mm}-${dd}`;
    
    dateInput.addEventListener('change', () => {
        const chosen = new Date(dateInput.value + 'T00:00:00');
        if (chosen.getDay() === 0) {
            dateInput.value = '';
            showError('err-date', 'We are closed on Sundays. Please select another day.');
        } else {
            hideError('err-date');
        }
        hideError('err-time');
    });
}

let currentStep = 1;

function setStep(n) {
    document.querySelectorAll('.form-step').forEach((s, i) => {
        s.classList.toggle('active', i + 1 === n);
    });
    document.querySelectorAll('.step').forEach((s, i) => {
        s.classList.toggle('active', i + 1 === n);
        s.classList.toggle('done', i + 1 < n);
    });
    currentStep = n;
    window.scrollTo({ top: document.querySelector('.steps-bar').offsetTop - 10, behavior: 'smooth' });
    if (n === 5) buildSummary();
}

function goStep(from, to) {
    if (validateStep(from)) setStep(to);
}

function validateStep(step) {
    let ok = true;
    
    if (step === 1) {
        const checked = document.querySelectorAll('input[name="services[]"]:checked');
        if (checked.length === 0) {
            showError('err-services', 'Please select at least one service.');
            ok = false;
        } else {
            hideError('err-services');
        }
    }
    
    if (step === 2) {
        const checked = document.querySelector('input[name="beautician"]:checked');
        if (!checked) {
            alert('Please select a specialist.');
            ok = false;
        }
    }
    
    if (step === 3) {
        const d = document.getElementById('appt_date').value;
        const t = document.getElementById('appt_time').value;
        if (!d) {
            showError('err-date', 'Please select a date.');
            ok = false;
        } else {
            hideError('err-date');
        }
        if (!t) {
            showError('err-time', 'Please select a time.');
            ok = false;
        } else {
            hideError('err-time');
        }
    }
    
    if (step === 4) {
        const name = document.getElementById('cust_name').value.trim();
        const phone = document.getElementById('cust_phone').value.trim();
        const email = document.getElementById('cust_email').value.trim();
        
        // Validate name
        if (!name) {
            showError('err-name', 'Please enter your full name.');
            markError('cust_name');
            ok = false;
        } else {
            hideError('err-name');
            clearError('cust_name');
        }
        
        // Validate phone
        if (!phone || !/^[0-9+\s\-]{7,15}$/.test(phone)) {
            showError('err-phone', 'Please enter a valid phone number (7-15 digits).');
            markError('cust_phone');
            ok = false;
        } else {
            hideError('err-phone');
            clearError('cust_phone');
        }
        
        // Validate email (if provided, must be valid format)
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('err-email', 'Please enter a valid email address (e.g., name@domain.com).');
            markError('cust_email');
            ok = false;
        } else {
            hideError('err-email');
            clearError('cust_email');
        }
    }
    
    return ok;
}

function showError(id, msg) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = msg;
        el.classList.remove('hidden');
    }
}

function hideError(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

function markError(id) {
    document.getElementById(id)?.classList.add('error-field');
}

function clearError(id) {
    document.getElementById(id)?.classList.remove('error-field');
}

function buildSummary() {
    const svcs = [...document.querySelectorAll('input[name="services[]"]:checked')].map(c => c.value);
    document.getElementById('sum-services').textContent = svcs.join(', ') || '—';
    
    const beau = document.querySelector('input[name="beautician"]:checked');
    document.getElementById('sum-beautician').textContent = beau ? beau.value : '—';
    
    const rawDate = document.getElementById('appt_date').value;
    if (rawDate) {
        const d = new Date(rawDate + 'T00:00:00');
        document.getElementById('sum-date').textContent = d.toDateString();
    }
    
    const rawTime = document.getElementById('appt_time').value;
    if (rawTime) {
        const [h, m] = rawTime.split(':');
        const hr = parseInt(h);
        const ampm = hr >= 12 ? 'PM' : 'AM';
        const display = `${hr > 12 ? hr - 12 : hr}:${m} ${ampm}`;
        document.getElementById('sum-time').textContent = display;
    }
    
    document.getElementById('sum-name').textContent = document.getElementById('cust_name').value.trim() || '—';
    document.getElementById('sum-phone').textContent = document.getElementById('cust_phone').value.trim() || '—';
    document.getElementById('sum-email').textContent = document.getElementById('cust_email').value.trim() || '—';
    document.getElementById('sum-notes').textContent = document.getElementById('cust_notes').value.trim() || 'None';
}

// Submit booking
document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate step 4
    if (!validateStep(4)) return;
    
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking availability...';
    
    const formData = new FormData(this);
    
    console.log('=== Sending booking request ===');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch('/saloon/php/process_booking.php', { 
        method: 'POST', 
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(res => {
        console.log('Response data:', res);
        
        if (res.success) {
            const details = res.booking_details;
            const message = `✅ BOOKING CONFIRMED!\n\n` +
                           `📋 Reference: ${details.ref}\n` +
                           `👤 Customer: ${details.name}\n` +
                           `📞 Phone: ${details.phone}\n` +
                           `📧 Email: ${details.email}\n` +
                           `💇 Staff: ${details.staff}\n` +
                           `📅 Date: ${details.date}\n` +
                           `🕐 Time: ${details.time}\n` +
                           `✂️ Services: ${details.services}\n\n` +
                           `Thank you for choosing GlowUp Ladies! ✨\n` +
                           `We look forward to seeing you!`;
            
            alert(message);
            showSuccessModal(res.ref);
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Confirm Booking';
            
        } else if (res.conflict) {
            alert(res.message);
            setStep(3);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Confirm Booking';
            
        } else {
            alert('❌ Error: ' + (res.message || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Confirm Booking';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('❌ Something went wrong. Please try again.\n\nError: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Confirm Booking';
    });
});

function showSuccessModal(ref) {
    document.getElementById('bookingRef').textContent = ref;
    const name = document.getElementById('cust_name').value.trim();
    document.getElementById('successName').textContent = name + '!';
    document.getElementById('successOverlay').classList.add('show');
}

document.getElementById('newBookingBtn')?.addEventListener('click', () => {
    document.getElementById('successOverlay').classList.remove('show');
    document.getElementById('bookingForm').reset();
    document.querySelectorAll('.step').forEach((s, i) => {
        s.classList.toggle('active', i === 0);
        s.classList.toggle('done', false);
    });
    setStep(1);
    window.scrollTo({ top: 0, behavior: 'smooth' });
});