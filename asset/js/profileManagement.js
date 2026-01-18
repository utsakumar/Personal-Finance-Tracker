document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (window.feather) feather.replace();

    // Set current date
    const currentDateElement = document.getElementById("current-date");
    if (currentDateElement) {
        const now = new Date();
        currentDateElement.textContent = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Load current user profile data
    function loadCurrentUserProfile() {
        fetch('../controller/fetch_current_user.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const user = data.data;
                    // Populate form fields
                    const firstNameField = document.getElementById('firstName');
                    const lastNameField = document.getElementById('lastName');
                    const emailField = document.getElementById('email');
                    const phoneField = document.getElementById('phone');
                    
                    if (firstNameField) firstNameField.value = user.u_fname || '';
                    if (lastNameField) lastNameField.value = user.u_lname || '';
                    if (emailField) emailField.value = user.u_email || '';
                    if (phoneField) phoneField.value = user.u_phone || '';
                    
                    // Update display name and email
                    const displayName = document.getElementById('displayName');
                    const displayEmail = document.getElementById('displayEmail');
                    if (displayName) {
                        displayName.textContent = `${user.u_fname || ''} ${user.u_lname || ''}`.trim() || 'User';
                    }
                    if (displayEmail) {
                        displayEmail.textContent = user.u_email || 'No email';
                    }
                    
                    // Load preferences from localStorage (or set defaults)
                    const savedPreferences = localStorage.getItem('userPreferences');
                    if (savedPreferences) {
                        try {
                            const preferences = JSON.parse(savedPreferences);
                            const currencyField = document.getElementById('currency');
                            const languageField = document.getElementById('language');
                            const timezoneField = document.getElementById('timezone');
                            
                            if (currencyField && preferences.currency) {
                                currencyField.value = preferences.currency;
                            }
                            if (languageField && preferences.language) {
                                languageField.value = preferences.language;
                            }
                            if (timezoneField && preferences.timezone) {
                                timezoneField.value = preferences.timezone;
                            }
                        } catch (e) {
                            console.error('Error parsing preferences:', e);
                        }
                    } else {
                        // Set default values
                        const currencyField = document.getElementById('currency');
                        const languageField = document.getElementById('language');
                        const timezoneField = document.getElementById('timezone');
                        
                        if (currencyField) currencyField.value = 'USD';
                        if (languageField) languageField.value = 'English';
                        if (timezoneField) timezoneField.value = 'UTC';
                    }
                } else {
                    console.error('Failed to load user profile:', data.message);
                    // Set default display values
                    const displayName = document.getElementById('displayName');
                    const displayEmail = document.getElementById('displayEmail');
                    if (displayName) displayName.textContent = 'User';
                    if (displayEmail) displayEmail.textContent = 'No email';
                }
            })
            .catch(error => {
                console.error('Error loading profile:', error);
                // Set default display values on error
                const displayName = document.getElementById('displayName');
                const displayEmail = document.getElementById('displayEmail');
                if (displayName) displayName.textContent = 'User';
                if (displayEmail) displayEmail.textContent = 'No email';
            });
    }

    // Load profile data on page load
    loadCurrentUserProfile();

    // Profile form handling
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Validate password confirmation
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                alert('New password and confirm password do not match!');
                return;
            }
            
            // Get form data
            const formData = new FormData(profileForm);
            
            // Save preferences to localStorage
            const preferences = {
                currency: document.getElementById('currency').value,
                language: document.getElementById('language').value,
                timezone: document.getElementById('timezone').value
            };
            localStorage.setItem('userPreferences', JSON.stringify(preferences));
            
            // Send to server
            fetch('../controller/update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                    // Reload profile data
                    loadCurrentUserProfile();
                    // Clear password fields
                    document.getElementById('currentPassword').value = '';
                    document.getElementById('newPassword').value = '';
                    document.getElementById('confirmPassword').value = '';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating profile:', error);
                alert('An error occurred while updating your profile.');
            });
        });
    }

    // Avatar change handling
    function handleAvatarChange(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                return;
            }
            
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size should be less than 5MB.');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarImg = document.getElementById('profilePicture');
                if (avatarImg) {
                    avatarImg.src = e.target.result;
                    // Save to localStorage for persistence
                    localStorage.setItem('profileAvatar', e.target.result);
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Make handleAvatarChange globally available for inline onclick
    window.handleAvatarChange = handleAvatarChange;
    
    // Load saved avatar if exists
    const savedAvatar = localStorage.getItem('profileAvatar');
    if (savedAvatar) {
        const avatarImg = document.getElementById('profilePicture');
        if (avatarImg) {
            avatarImg.src = savedAvatar;
        }
    }
});
