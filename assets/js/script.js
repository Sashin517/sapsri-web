function toggleNavIcon(icon) {
    icon.classList.toggle("open")
}
// impact area clicking navigation start

function addNavigation(selector, url) {
    // 1. Select all elements that match the class
    const elements = document.querySelectorAll(selector);

    // 2. Loop through the list of found elements
    elements.forEach(element => {
        // 3. Add a click event listener to each one
        element.addEventListener('click', function() {
            // 4. On click, navigate to the specified URL
            console.log(`Navigating to ${url}...`);
            window.location.href = url;
        });
    });
}

// --- helper function for each unique impact area --- //
addNavigation('.impact-area-01', '/climate-and-biodiversity');
addNavigation('.impact-area-02', '/sustainable-agriculture');
addNavigation('.impact-area-03', '/finance-and-governance');
addNavigation('.impact-area-04', '/gender-inclusion');

// impact area clicking navigation end


const scrollers = document.querySelectorAll(".ha-scroller");

if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {

    addAnimation();
}

function addAnimation() {

    scrollers.forEach((scroller) => {
        scroller.setAttribute("data-animated", true);

        const scrollerInner = scroller.querySelector('.ha-scroller__inner');
        const scrollerContent = Array.from(scrollerInner.children);

        scrollerContent.forEach(item => {
            const duplicatedItem = item.cloneNode(true);
            duplicatedItem.setAttribute("aria-hidden", true);
            scrollerInner.appendChild(duplicatedItem);
        })
    })
}

const swiper = document.querySelector(".swiper") && new Swiper('.swiper', {

    slidesPerView: 1,
    spaceBetween: 10,
    loop: true,

    autoplay: {
        delay: 2500,
        pauseOnMouseEnter: true,
    },

    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },

    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    breakpoints: {

        576: {
            slidesPerView: 2,
            spaceBetween: 15,
        },

        640: {
            slidesPerView: 2,
            spaceBetween: 15,
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 15,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
    },
});