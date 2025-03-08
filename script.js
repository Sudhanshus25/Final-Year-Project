document.addEventListener("DOMContentLoaded", function () { 
    const gallery = document.querySelector(".scrolling-gallery");
    const images = document.querySelectorAll(".sliding-image");
    const bullets = document.querySelectorAll(".slider-pagination-bullets");

    let index = 0;
    const totalImages = images.length;
    const imageWidth = images[0].offsetWidth + 16; // Image width + gap

    function updatePagination() {
        bullets.forEach((bullet) => {
            bullet.classList.remove(
                "bullet-active-prev-prev",
                "bullet-active-prev",
                "bullet-active",
                "bullet-active-next",
                "bullet-active-next-next"
            );
        });

        // Apply classes only when not at the last bullet
        if (index < totalImages - 1) {
            if (index - 2 >= 0) bullets[index - 2].classList.add("bullet-active-prev-prev");
            if (index - 1 >= 0) bullets[index - 1].classList.add("bullet-active-prev");
            if (index + 1 < totalImages) bullets[index + 1].classList.add("bullet-active-next");
            if (index + 2 < totalImages) bullets[index + 2].classList.add("bullet-active-next-next");
        }

        // Always apply the active class
        bullets[index].classList.add("bullet-active");
    }

    function slideImages() {
        index++;

        if (index >= totalImages) {
            // Jump back to the start when reaching the last image
            index = 0;
            gallery.style.transition = "none";
            gallery.scrollLeft = 0;
            setTimeout(() => {
                gallery.style.transition = "transform 0.75s ease-in-out";
            }, 50);
        } else {
            // gallery.style.transition = "transform 0.75s ease-in-out";
            gallery.scrollLeft = index * imageWidth;
        }

        updatePagination(); // Update bullets when slide changes
    }

    updatePagination(); // Initialize pagination
    setInterval(slideImages, 4500);
});
