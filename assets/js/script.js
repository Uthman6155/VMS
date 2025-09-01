// Additional custom JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // Remember me functionality
    const rememberCheckbox = document.getElementById('remember');
    if (rememberCheckbox) {
        rememberCheckbox.addEventListener('change', function() {
            localStorage.setItem('remember_me', this.checked);
        });
        
        // Check if "remember me" was set previously
        if (localStorage.getItem('remember_me') === 'true') {
            rememberCheckbox.checked = true;
        }
    }
});