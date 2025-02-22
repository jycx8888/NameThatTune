function redirectToDashboard() {
    window.location.href = "admin_adminDashboard.php";
}

function validateNewUsername() {
    const username = document.getElementById('usernameInput').value;
            
    if (username === "") {
        showWarning('Username cannot be empty.');
        return false;
    }

    return true;
}

function validateNewPassword() {
    const password = document.getElementById('newPasswordInput').value;
    const confirmPassword = document.getElementById('confirmPasswordInput').value;
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%#*?&])[A-Za-z\d@$!%#*?&]{8,14}$/;

    if (password === "") {
        showWarning('New Password cannot be empty.');
        return false;
    }

    if (confirmPassword === "") {
        showWarning('Confirm Password cannot be empty.');
           return false;
    }

    if (password !== confirmPassword) {
        showWarning('Passwords do not match.');
        return false;
    }

    if (!passwordRegex.test(password)) {
        showWarning('Password must be 8-14 characters long, include at least one capital letter, one number, and one special character.');
           return false;
    }

    return true;
}

document.getElementById('login').addEventListener('click', function() {
    document.getElementById('hamburger-menu').classList.toggle('open');
});

function showPopup(popupId) {
    document.getElementById(popupId).classList.add('show');
}

function closePopup(popupId) {
    document.getElementById(popupId).classList.remove('show');
}

function togglePasswordVisibility(fieldId, iconElement) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(iconElement);
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        iconElement.src = 'Icon/show.png'; 
    } else {
        passwordField.type = 'password';
        iconElement.src = 'Icon/hide.png';
    }
}

function toggleMenu() {
    document.getElementById('hamburger-menu').classList.toggle('open');
}

function toggleSubmenu(submenuId) {
    const submenu = document.getElementById(submenuId);
    submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
}

function toggleVolumeControl() {
    const volumeControl = document.getElementById('volumeControl');
    volumeControl.style.display = volumeControl.style.display === 'block' ? 'none' : 'block';
}

function confirmLogout() {
    document.getElementById('logoutOverlay').classList.add('show');
}

function closeLogoutPopup() {
    document.getElementById('logoutOverlay').classList.remove('show');
}

function logout() {
    window.location.href = 'admin_logout.php';
}

document.getElementById('volumeSlider').addEventListener('input', function() {
    const volume = this.value;
    console.log('Volume:', volume);
});

function showWarning(message) {
    const warningPopup = document.getElementById('warningPopup');
    const warningMessage = document.getElementById('warningMessage');
    warningMessage.textContent = message;
    warningPopup.classList.add('show');
}
        
function closeWarningPopup() {
    const warningPopup = document.getElementById('warningPopup');
    warningPopup.classList.remove('show');
}