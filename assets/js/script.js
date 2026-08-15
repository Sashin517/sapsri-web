function toggleNavIcon(icon) {
  icon.classList.toggle("open");
}
// impact area clicking navigation start

function addNavigation(selector, url) {
  // 1. Select all elements that match the class
  const elements = document.querySelectorAll(selector);

  // 2. Loop through the list of found elements
  elements.forEach((element) => {
    // 3. Add a click event listener to each one
    element.addEventListener("click", function () {
      // 4. On click, navigate to the specified URL
      console.log(`Navigating to ${url}...`);
      window.location.href = url;
    });
  });
}

// --- helper function for each unique impact area --- //
addNavigation(".impact-area-01", "/climate-and-biodiversity");
addNavigation(".impact-area-02", "/sustainable-agriculture");
addNavigation(".impact-area-03", "/finance-and-governance");
addNavigation(".impact-area-04", "/gender-inclusion");

// impact area clicking navigation end

const scrollers = document.querySelectorAll(".ha-scroller");

if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
  addAnimation();
}

function addAnimation() {
  scrollers.forEach((scroller) => {
    scroller.setAttribute("data-animated", true);

    const scrollerInner = scroller.querySelector(".ha-scroller__inner");
    const scrollerContent = Array.from(scrollerInner.children);

    scrollerContent.forEach((item) => {
      const duplicatedItem = item.cloneNode(true);
      duplicatedItem.setAttribute("aria-hidden", true);
      scrollerInner.appendChild(duplicatedItem);
    });
  });
}

const swiper =
  document.querySelector(".swiper") &&
  new Swiper(".swiper", {
    slidesPerView: 1,
    spaceBetween: 10,
    loop: true,

    autoplay: {
      delay: 2500,
      pauseOnMouseEnter: true,
    },

    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },

    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
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

/// Home Latest News Section ///
function createCardMarkup(item) {
  const id = item.id ?? null;
  const coverImage = item.cover_image ? `/project-sedna/${item.cover_image}` : "";
  const title = item.title ?? "Untitled";
  const description = item.description ?? "...";
  const publishedDate = item.published_date ? formatTimeAgo(item.published_date) : "";
  return `
        <div class="swiper-slide">
            <div class="card news-card h-100 shadow-sm rounded-4 overflow-hidden">
            
                <img src="${coverImage}" class="card-img-top" alt="${title}" style="height: 180px; object-fit: cover;">
            
                <div class="card-body">
                    <h5 class="card-title news-card-title">${title}</h5>
                    <a href="post?id=${id}" class="stretched-link"></a>
                    <p class="card-text">${description}</p>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">Posted on ${publishedDate}</small>
                                            
                    <span class="btn btn-sm btn-dark rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </span>
                </div>
          
            </div>
        </div>
      `;
}

async function initNewsSection() {
  const wrapper = document.getElementById("newsWrapper");
  const prevBtn = document.getElementById("newsPrevBtn");
  const nextBtn = document.getElementById("newsNextBtn");

  try {
    const response = await fetch("actions/get-latest-posts.php");

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const newsList = await response.json();

    if (!newsList || newsList.length === 0) {
      wrapper.innerHTML = `
            <div class="w-100 py-4 text-center">
              <div class="alert alert-secondary d-inline-block px-4 mb-0" role="alert">
                No news articles available at the moment.
              </div>
            </div>`;
      prevBtn.classList.add("d-none");
      nextBtn.classList.add("d-none");
      return;
    }

    wrapper.innerHTML = newsList.map(createCardMarkup).join("");

    const totalSlides = newsList.length;
    const canLoop = totalSlides >= 5;

    if (totalSlides <= 1) {
      prevBtn.classList.add("d-none");
      nextBtn.classList.add("d-none");
    }

    const newsSwiper = new Swiper("#newsSwiper", {
      slidesPerView: 1, // Width: 300px - 575px (Minimum required)
      spaceBetween: 16,

      breakpoints: {
        576: {
          slidesPerView: 2, // Mobile landscape / Small tablets
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 3, // Medium screens / Tablets
          spaceBetween: 24,
        },
        1200: {
          slidesPerView: 4, // Desktop (Shows 4 cards default)
          spaceBetween: 24,
        },
      },

      autoplay: canLoop
        ? {
            delay: 3500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          }
        : false,
      loop: canLoop,
      navigation: {
        nextEl: "#newsNextBtn",
        prevEl: "#newsPrevBtn",
        disabledClass: "swiper-button-custom-disabled",
      },
    });
  } catch (error) {
    console.error("Failed to load news cards:", error);
    wrapper.innerHTML = `
          <div class="w-100 py-4 text-center">
            <div class="alert alert-danger d-inline-block px-4 mb-0" role="alert">
              Unable to load latest news right now. Please try again later.
            </div>
          </div>`;

    prevBtn.classList.add("d-none");
    nextBtn.classList.add("d-none");
  }
}

function formatTimeAgo(dateTime) {
  const isoStr = dateTime.replace(" ", "T");
  const pastDate = new Date(isoStr);
  const nowDate = new Date();

  const secondsAgo = Math.floor((nowDate - pastDate) / 1000);

  const intervals = [
    { label: "year", seconds: 31536000 },
    { label: "month", seconds: 2592000 },
    { label: "day", seconds: 86400 },
    { label: "hour", seconds: 3600 },
    { label: "minute", seconds: 60 },
    { label: "second", seconds: 1 },
  ];

  for (const interval of intervals) {
    const count = Math.floor(secondsAgo / interval.seconds);
    if (count >= 1) {
      const rtf = new Intl.RelativeTimeFormat("en", { numeric: "auto" });
      return rtf.format(-count, interval.label);
    }
  }

  return "just now";
}

document.addEventListener("DOMContentLoaded", initNewsSection);
