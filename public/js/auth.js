document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const message = document.getElementById('message');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = new URL(this.action, window.location.origin);
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            message.textContent = result.message;
            message.style.display = 'block';
            message.className = 'message ' + (result.success ? 'success' : 'error');
            
            if (result.success && result.data.redirect) {
                setTimeout(() => {
                    window.location.href = result.data.redirect;
                }, 1500);
            }
        } catch (error) {
            message.textContent = 'An error occurred. Please try again.';
            message.style.display = 'block';
            message.className = 'message error';
        }
    });
});
