document.addEventListener("click", function(event) {
    if (event.target.classList.contains("gallery-img")) {
        const modalImage = document.getElementById("modalImage");
        modalImage.src = event.target.src;
    }
});
