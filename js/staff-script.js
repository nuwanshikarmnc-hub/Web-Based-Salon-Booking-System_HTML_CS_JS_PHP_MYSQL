document.addEventListener('DOMContentLoaded', function() {
    const staffGrid = document.getElementById('staffGrid');
    if (!staffGrid) return;
    
    function generateStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        
        let starsHtml = '';
        for (let i = 0; i < fullStars; i++) {
            starsHtml += '<i class="fas fa-star" style="color: #ffb83b;"></i>';
        }
        if (hasHalfStar) {
            starsHtml += '<i class="fas fa-star-half-alt" style="color: #ffb83b;"></i>';
        }
        for (let i = 0; i < emptyStars; i++) {
            starsHtml += '<i class="far fa-star" style="color: #ffb83b;"></i>';
        }
        return starsHtml;
    }
    
    // FETCH FROM PHP API
    fetch('/saloon/php/get_staff.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(staffMembers => {
            console.log('Staff loaded:', staffMembers.length, 'members');
            
            if (!staffMembers || staffMembers.length === 0) {
                staffGrid.innerHTML = '<div style="padding:40px;text-align:center;">No staff found.</div>';
                return;
            }
            
            staffGrid.innerHTML = '';
            
            staffMembers.forEach(member => {
                const card = document.createElement('div');
                card.className = 'staff-card';
                
                const imgDiv = document.createElement('div');
                imgDiv.className = 'card-img';
                
                const img = document.createElement('img');
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.alt = member.name;
                
                img.onerror = function() {
                    this.style.display = 'none';
                    const placeholder = document.createElement('div');
                    placeholder.className = 'fallback-avatar';
                    placeholder.style.background = "linear-gradient(145deg, #eedbc8, #e2cfb7)";
                    placeholder.style.display = "flex";
                    placeholder.style.alignItems = "center";
                    placeholder.style.justifyContent = "center";
                    placeholder.style.fontSize = "3.5rem";
                    placeholder.style.fontFamily = "'Cormorant Garamond', serif";
                    placeholder.style.color = "#8c623f";
                    // Use first letters of name as initials
                    const nameParts = member.name.split(' ');
                    let initials = '';
                    if (nameParts.length >= 2) {
                        initials = nameParts[0][0] + nameParts[1][0];
                    } else {
                        initials = member.name.substring(0, 2);
                    }
                    placeholder.innerText = initials.toUpperCase();
                    imgDiv.innerHTML = '';
                    imgDiv.appendChild(placeholder);
                };
                
                // Try to load image - if not found, fallback to initials
                const imageName = member.imageFile || 'placeholder.jpg';
                img.src = '/saloon/images/' + imageName;
                imgDiv.appendChild(img);
                
                const infoDiv = document.createElement('div');
                infoDiv.className = 'staff-info';
                
                const nameEl = document.createElement('h3');
                nameEl.className = 'staff-name';
                nameEl.innerText = member.name;
                
                const roleEl = document.createElement('div');
                roleEl.className = 'staff-role';
                roleEl.innerText = member.role;
                
                const ratingContainer = document.createElement('div');
                ratingContainer.className = 'staff-rating';
                ratingContainer.style.cssText = 'margin:12px 0; display:flex; align-items:center; justify-content:center; gap:8px; flex-wrap:wrap;';
                
                const starsSpan = document.createElement('span');
                starsSpan.innerHTML = generateStars(member.rating || 5);
                starsSpan.style.cssText = 'font-size:0.9rem; letter-spacing:2px;';
                
                const ratingText = document.createElement('span');
                ratingText.style.cssText = 'font-size:0.8rem; color:#b47d4e; font-weight:500;';
                ratingText.innerHTML = `${member.rating || 5} ★ (${member.reviewCount || 0} reviews)`;
                
                ratingContainer.appendChild(starsSpan);
                ratingContainer.appendChild(ratingText);
                
                const qualEl = document.createElement('div');
                qualEl.className = 'qualification';
                qualEl.innerHTML = `<i class="fas fa-graduation-cap" style="margin-right:8px; color:#c99262;"></i> ${member.qualifications || 'Professional'}`;
                
                const specContainer = document.createElement('div');
                specContainer.className = 'specialization';
                if (member.specializations && Array.isArray(member.specializations)) {
                    member.specializations.forEach(spec => {
                        const badge = document.createElement('span');
                        badge.className = 'specialty-badge';
                        badge.innerText = spec;
                        specContainer.appendChild(badge);
                    });
                }
                
                const expEl = document.createElement('div');
                expEl.className = 'experience';
                expEl.innerHTML = `<i class="fas fa-calendar-alt"></i> ${member.experience || 0}+ years expertise`;
                
                infoDiv.appendChild(nameEl);
                infoDiv.appendChild(roleEl);
                infoDiv.appendChild(ratingContainer);
                infoDiv.appendChild(qualEl);
                infoDiv.appendChild(specContainer);
                infoDiv.appendChild(expEl);
                card.appendChild(imgDiv);
                card.appendChild(infoDiv);
                staffGrid.appendChild(card);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            staffGrid.innerHTML = '<div style="padding:40px;text-align:center;color:#c0392b;"><i class="fas fa-exclamation-triangle"></i> Failed to load staff.<br><small>' + error.message + '</small></div>';
        });
});

// Mobile menu
const menuIcon = document.getElementById('menuIcon');
const navMenu = document.getElementById('navMenu');
if (menuIcon && navMenu) {
    menuIcon.addEventListener('click', function() {
        navMenu.classList.toggle('show-menu');
    });
}