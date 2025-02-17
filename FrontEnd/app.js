document.addEventListener('DOMContentLoaded', () => {
    // Initialize all interactive elements
    initializePhotoUpload();
    initializePaymentButtons();
    initializeFormValidation();
    initializeNavigation();
    initializeStatsAnimation();
});

// ... existing functions remain unchanged ...

// Add this new function for stats animation
function initializeStatsAnimation() {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Initialize stats cards animation
    document.querySelectorAll('.stat-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
}

// ... rest of your existing functions remain unchanged ... 