document.addEventListener('DOMContentLoaded', () => {
    // Initialize all interactive elements
    initializePhotoUpload();
    initializePaymentButtons();
    initializeFormValidation();
    initializeNavigation();
});

function initializePhotoUpload() {
    const uploadContainer = document.getElementById('uploadContainer');
    const photoInput = document.getElementById('petPhotos');
    const photoPreview = document.getElementById('photoPreview');

    if (uploadContainer && photoInput && photoPreview) {
        // Drag and drop functionality
        uploadContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadContainer.classList.add('dragover');
        });

        uploadContainer.addEventListener('dragleave', () => {
            uploadContainer.classList.remove('dragover');
        });

        uploadContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadContainer.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        photoInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    photoPreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function initializePaymentButtons() {
    const amountButtons = document.querySelectorAll('.amount-btn');
    const customAmount = document.querySelector('.custom-amount');

    if (amountButtons.length && customAmount) {
        amountButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                amountButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                customAmount.value = '';
            });
        });

        customAmount.addEventListener('input', () => {
            amountButtons.forEach(btn => btn.classList.remove('active'));
        });
    }
}

function initializeFormValidation() {
    const donationForm = document.querySelector('.donation-form');
    
    if (donationForm) {
        donationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Basic form validation
            const requiredFields = donationForm.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            if (isValid) {
                // Submit form
                submitForm(donationForm);
            }
        });
    }
}

function initializeNavigation() {
    const hamburger = document.querySelector('.hamburger-menu');
    const navMenu = document.querySelector('.nav-links');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }
}

async function submitForm(form) {
    try {
        const formData = new FormData(form);
        // Add your form submission logic here
        
        // Show success message
        showMessage('Success! Thank you for your submission.', 'success');
    } catch (error) {
        showMessage('An error occurred. Please try again.', 'error');
    }
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
} 

