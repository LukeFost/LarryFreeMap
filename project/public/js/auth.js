document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const message = document.getElementById('message');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            console.log('Submitting to:', 'auth_handler.php');
            const response = await fetch('auth_handler.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Server response:', result);
            
            message.textContent = result.message || 'An error occurred';
            message.style.display = 'block';
            message.className = 'message ' + (result.success ? 'success' : 'error');
            
            if (result.success && result.data && result.data.redirect) {
                setTimeout(() => {
                    window.location.href = result.data.redirect;
                }, 1500);
            }
        } catch (error) {
            console.error('Error:', error);
            message.textContent = 'An error occurred. Please try again. ' + error.message;
            message.style.display = 'block';
            message.className = 'message error';
        }
    });
});
