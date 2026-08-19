// import { getDocument } from '../vendor/pdfjs/pdf.mjs'; // import getDocument method from pdf.mjs

// pdfjsLib.GlobalWorkerOptions.workerSrc = '../vendor/pdfjs/pdf.worker.mjs'; // specify workerSrc property


// add click event to card container
document.getElementById("cardContainer").addEventListener("click", function (event) { viewPDF(event) });

document.getElementById("pdfPrevPageBtn").addEventListener('click', onPrevPage);
document.getElementById('pdfNextPageBtn').addEventListener('click', onNextPage);

const pdfViewerModal = new bootstrap.Modal("#pdfViewerModal");
const pdfViewerModalElement = document.getElementById("pdfViewerModal");

let pdfViewerModalTitle = pdfViewerModalElement.querySelector(".modal-title");
let pdfViewerModalBody = pdfViewerModalElement.querySelector(".modal-body");
let pdfViewerModalBodyInnerWidth = 0;
let pdfURL = null;

pdfViewerModalElement.addEventListener('shown.bs.modal', () => {
    pdfViewerModalBodyInnerWidth = pdfViewerModalBody.clientWidth - (pdfViewerModalBody.offsetWidth - pdfViewerModalBody.clientWidth);
    loadPDF(pdfURL);
});

function viewPDF(event) {
    // 1. Prevent the anchor tag from jumping to the top of the page or opening a new tab
    event.preventDefault();

    const card = event.target.closest(".card");
    if (!card) return; // Safety check in case they click outside the card

    const cardTitle = card.querySelector(".card-title").textContent;
    const cardLink = card.querySelector(".btn-download").href;

    pdfURL = cardLink;
    pdfViewerModalTitle.textContent = cardTitle;

    // 2. NEW: Update the "Download" button inside the modal to point to the correct file
    const modalDownloadBtn = document.getElementById("publicationModalSaveBtn");
    if(modalDownloadBtn) {
        modalDownloadBtn.href = cardLink;
        modalDownloadBtn.download = cardTitle + ".pdf";
    }

    pdfViewerModal.show();
}

var pdfDoc = null,
    currentPage = 1,
    pageRendering = false,
    pageNumPending = null,
    pageZoom = 1,
    canvas = document.getElementById('pageCanvas'),
    ctx = canvas.getContext('2d');


function loadPDF(pdfURL) {
    var url = pdfURL;

    // UPDATE THIS LINE to use pdfjsLib
    pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('pdfPageCount').textContent = pdfDoc.numPages;

        renderPage(currentPage);
    });
}


function renderPage(pageNum) {
    pageRendering = true;

    pdfDoc.getPage(pageNum).then(function (page) {
        const defaultPageViewport = page.getViewport({ scale: 1 });

        // 1. Get the LIVE dimensions of the modal body container
        const containerWidth = pdfViewerModalBody.clientWidth - 40; // 40px for safe padding
        const containerHeight = pdfViewerModalBody.clientHeight - 40;

        // 2. Calculate both width and height scales
        const widthScale = containerWidth / defaultPageViewport.width;
        const heightScale = containerHeight / defaultPageViewport.height;

        // 3. UX MAGIC: Pick the smaller scale so the entire page fits on screen!
        const finalScale = Math.min(widthScale, heightScale) * pageZoom;

        const viewport = page.getViewport({ scale: finalScale });

        canvas.height = viewport.height;
        canvas.width = viewport.width;

        var renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };
        var renderTask = page.render(renderContext);

        renderTask.promise.then(function () {
            pageRendering = false;

            if (pageNumPending !== null) {
                renderPage(pageNumPending);
                pageNumPending = null;
            }
        });
    });

    document.getElementById("pdfPageNum").textContent = pageNum;
}

function queueRenderPage(pageNum) {
    if (pageRendering) {
        pageNumPending = pageNum;
    } else {
        renderPage(pageNum);
    }
}

function onPrevPage() {
    if (currentPage <= 1) {
        return;
    }
    currentPage--;
    queueRenderPage(currentPage);
}




function onNextPage() {
    if (currentPage >= pdfDoc.numPages) {
        return;
    }
    currentPage++;
    queueRenderPage(currentPage);
}

// ==========================================
// --- SMART SCROLL & KEYBOARD CAPABILITY ---
// ==========================================

let scrollThrottle = null;

// 1. Desktop & Trackpad Scroll (Wheel Event)
pdfViewerModalBody.addEventListener('wheel', function (e) {
    // Check if user is at the physical boundaries of the scrollable container
    const atBottom = Math.abs(this.scrollHeight - this.scrollTop - this.clientHeight) <= 2;
    const atTop = this.scrollTop <= 0;

    // If trying to scroll past the boundaries
    if ((e.deltaY > 0 && atBottom) || (e.deltaY < 0 && atTop)) {
        e.preventDefault(); // Stop rubber-banding bounce effect
        
        // Throttle to prevent jumping 5 pages with one fast scroll flick
        if (scrollThrottle || pageRendering) return;
        scrollThrottle = setTimeout(() => { scrollThrottle = null; }, 600); 

        if (e.deltaY > 0 && currentPage < pdfDoc.numPages) {
            onNextPage(); // Scrolled down at bottom -> Next Page
            setTimeout(() => { this.scrollTop = 0; }, 100); // Reset scroll to top
        } else if (e.deltaY < 0 && currentPage > 1) {
            onPrevPage(); // Scrolled up at top -> Prev Page
            setTimeout(() => { this.scrollTop = 0; }, 100); // Reset scroll to top
        }
    }
});

// 2. Mobile & Tablet Swipe (Touch Events)
let touchStartY = 0;
pdfViewerModalBody.addEventListener('touchstart', e => {
    touchStartY = e.touches[0].clientY;
}, { passive: true });

pdfViewerModalBody.addEventListener('touchend', function(e) {
    const touchEndY = e.changedTouches[0].clientY;
    const deltaY = touchStartY - touchEndY; // Positive = swiped up (scrolling down the page)
    
    const atBottom = Math.abs(this.scrollHeight - this.scrollTop - this.clientHeight) <= 2;
    const atTop = this.scrollTop <= 0;

    if (pageRendering) return;

    // Swiped up significantly at the bottom
    if (deltaY > 40 && atBottom && currentPage < pdfDoc.numPages) {
        onNextPage();
        setTimeout(() => { this.scrollTop = 0; }, 100);
    } 
    // Swiped down significantly at the top
    else if (deltaY < -40 && atTop && currentPage > 1) {
        onPrevPage();
        setTimeout(() => { this.scrollTop = 0; }, 100);
    }
});

// 3. Keyboard Navigation (Arrows & PageUp/PageDown)
document.addEventListener('keydown', function(e) {
    // Only trigger if the modal is currently open
    if (pdfViewerModalElement.classList.contains('show') && !pageRendering) {
        const atBottom = Math.abs(pdfViewerModalBody.scrollHeight - pdfViewerModalBody.scrollTop - pdfViewerModalBody.clientHeight) <= 2;
        const atTop = pdfViewerModalBody.scrollTop <= 0;

        if ((e.key === 'ArrowRight' || e.key === 'ArrowDown' || e.key === 'PageDown') && currentPage < pdfDoc.numPages) {
            if (atBottom || e.key === 'ArrowRight') {
                e.preventDefault();
                onNextPage();
                setTimeout(() => { pdfViewerModalBody.scrollTop = 0; }, 100);
            }
        } 
        else if ((e.key === 'ArrowLeft' || e.key === 'ArrowUp' || e.key === 'PageUp') && currentPage > 1) {
            if (atTop || e.key === 'ArrowLeft') {
                e.preventDefault();
                onPrevPage();
                setTimeout(() => { pdfViewerModalBody.scrollTop = 0; }, 100);
            }
        }
    }
});
