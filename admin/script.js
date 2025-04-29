document.addEventListener('DOMContentLoaded', function() {
    // Category-Subcategory dependency
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    
    if (categorySelect && subcategorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            // Enable all options first
            Array.from(subcategorySelect.options).forEach(option => {
                option.style.display = 'block';
            });
            
            if (categoryId) {
                // Hide options that don't belong to selected category
                Array.from(subcategorySelect.options).forEach(option => {
                    if (option.value && option.dataset.category !== categoryId) {
                        option.style.display = 'none';
                    }
                });
                
                // Reset to default option
                subcategorySelect.value = '';
            }
        });
    }
    
    // Color-specific image uploads
    const colorInput = document.getElementById('color-input');
    const colorImagesContainer = document.getElementById('color-images-container');
    const colorSpecificImages = document.getElementById('color-specific-images');
    
    if (colorInput) {
        colorInput.addEventListener('input', function() {
            updateColorImageInputs();
        });
        
        // Initialize on page load if there are already colors
        if (colorInput.value) {
            updateColorImageInputs();
        }
    }
    
    function updateColorImageInputs() {
        const colors = colorInput.value.split(',').map(c => c.trim()).filter(c => c);
        
        // Clear existing inputs
        colorImagesContainer.innerHTML = '';
        colorSpecificImages.innerHTML = '<h4>Color-Specific Images</h4><p>Upload images that will change when a color is selected</p>';
        
        if (colors.length > 0) {
            colors.forEach(color => {
                // Add to color images container
                const colorDiv = document.createElement('div');
                colorDiv.className = 'color-image-input';
                colorDiv.innerHTML = `
                    <label>Image for ${color}:</label>
                    <input type="file" name="color_image_${color}" accept="image/*">
                `;
                colorImagesContainer.appendChild(colorDiv);
                
                // Add to color-specific images section
                const specificDiv = document.createElement('div');
                specificDiv.className = 'color-image-input';
                specificDiv.innerHTML = `
                    <label>Additional Images for ${color}:</label>
                    <input type="file" name="color_additional_${color}[]" accept="image/*" multiple>
                `;
                colorSpecificImages.appendChild(specificDiv);
            });
        }
    }
    
    // Initialize any other needed functionality
    initializeFormValidation();
});

function initializeFormValidation() {
    // Add form validation as needed
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Basic validation example
            const requiredInputs = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
}