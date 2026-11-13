document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.getElementById('menuIcon');
    const navMenu = document.getElementById('navMenu');

    if (menuIcon && navMenu) {
        menuIcon.addEventListener('click', function (e) {
            e.stopPropagation();
            navMenu.classList.toggle('show-menu');
        });

        document.querySelectorAll('.main-nav a').forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('show-menu');
            });
        });

        document.body.addEventListener('click', function (e) {
            if (navMenu.classList.contains('show-menu') &&
                !navMenu.contains(e.target) &&
                !menuIcon.contains(e.target)) {
                navMenu.classList.remove('show-menu');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') navMenu.classList.remove('show-menu');
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) navMenu.classList.remove('show-menu');
        });
    }
});

function handleSubmit() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value.trim();

    if (!firstName || !email || !message) {
        alert('Please fill in your name, email, and message.');
        return;
    }

    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('subject', subject);
    formData.append('message', message);

    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;

    fetch('/saloon/php/save_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const successMsg = document.getElementById('successMsg');
            successMsg.style.display = 'block';
            
            document.getElementById('firstName').value = '';
            document.getElementById('lastName').value = '';
            document.getElementById('email').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('subject').value = '';
            document.getElementById('message').value = '';
            
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 5000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Something went wrong. Please try again.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}