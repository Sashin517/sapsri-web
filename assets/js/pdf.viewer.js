// Global State
let pdfDoc = null;
let currentPage = 1; // Tracks the LEFTmost page currently in view
let pageZoom = 1;
let isRendering = false;

// DOM Elements
const dragContainer = document.getElementById('pdfDragContainer');
const canvasLeft = document.getElementById('pageCanvasLeft');
const canvasRight = document.getElementById('pageCanvasRight');
const ctxLeft = canvasLeft.getContext('2d');
const ctxRight = canvasRight.getContext('2d');
const pdfViewerModalElement = document.getElementById("pdfViewerModal");
const pdfViewerModal = new bootstrap.Modal(pdfViewerModalElement);

// Initialization Listener
document.getElementById("cardContainer").addEventListener("click", viewPDF);

function viewPDF(event) {
    event.preventDefault();
    const card = event.target.closest(".card");
    if (!card) return;

    const cardTitle = card.querySelector(".card-title").textContent;
    const cardLink = card.querySelector(".btn-download").href;

    document.querySelector(".modal-title").textContent = cardTitle;
    
    const saveBtn = document.getElementById("publicationModalSaveBtn");
    saveBtn.href = cardLink;
    saveBtn.download = cardTitle + ".pdf";

    // Reset State
    pageZoom = 1;
    updateZoomText();
    ctxLeft.clearRect(0, 0, canvasLeft.width, canvasLeft.height);
    ctxRight.clearRect(0, 0, canvasRight.width, canvasRight.height);
    
    pdfViewerModal.show();

    pdfjsLib.getDocument(cardLink).promise.then(doc => {
        pdfDoc = doc;
        document.getElementById('pdfPageCount').textContent = pdfDoc.numPages;
        currentPage = 1;
        renderSpread();
    }).catch(err => console.error("PDF Load Error:", err));
}

// --- BOOK LAYOUT ENGINE ---
async function renderSpread() {
    if (isRendering || !pdfDoc) return;
    isRendering = true;

    // Determine Pages to Render
    let pagesToRender = [];
    if (currentPage === 1) {
        // Cover Page (Single Right)
        pagesToRender = [null, 1]; 
        document.getElementById("pdfPageNum").textContent = "1";
    } else {
        // Spread (Even Left, Odd Right)
        let leftNum = currentPage % 2 === 0 ? currentPage : currentPage - 1;
        let rightNum = leftNum + 1 > pdfDoc.numPages ? null : leftNum + 1;
        pagesToRender = [leftNum, rightNum];
        
        document.getElementById("pdfPageNum").textContent = rightNum ? `${leftNum}-${rightNum}` : `${leftNum}`;
        currentPage = leftNum; // Sync state
    }

    // Toggle Canvas Visibility
    canvasLeft.style.display = pagesToRender[0] ? 'block' : 'none';
    canvasRight.style.display = pagesToRender[1] ? 'block' : 'none';

    try {
        // We get Page 1 just to calculate the baseline aspect ratio for the screen
        const basePage = await pdfDoc.getPage(1);
        const baseViewport = basePage.getViewport({ scale: 1 });
        
        // Calculate "Fit to Screen" Baseline
        const containerW = dragContainer.clientWidth - 60;
        const containerH = dragContainer.clientHeight - 60;
        
        // If two pages, divide available width by 2
        const layoutMultiplier = pagesToRender[0] && pagesToRender[1] ? 2 : 1; 
        
        const scaleW = (containerW / layoutMultiplier) / baseViewport.width;
        const scaleH = containerH / baseViewport.height;
        const baseScale = Math.min(scaleW, scaleH);
        
        const finalScale = baseScale * pageZoom;

        // Render Canvases
        if (pagesToRender[0]) await drawPage(pagesToRender[0], canvasLeft, ctxLeft, finalScale);
        if (pagesToRender[1]) await drawPage(pagesToRender[1], canvasRight, ctxRight, finalScale);
        
    } catch (error) {
        console.error("Rendering Error:", error);
    }
    
    isRendering = false;
}

async function drawPage(num, canvas, ctx, scale) {
    const page = await pdfDoc.getPage(num);
    const viewport = page.getViewport({ scale: scale });
    canvas.height = viewport.height;
    canvas.width = viewport.width;
    await page.render({ canvasContext: ctx, viewport: viewport }).promise;
}

// --- NAVIGATION ---
document.getElementById('pdfPrevPageBtn').addEventListener('click', () => {
    if (currentPage <= 1) return;
    currentPage = currentPage === 2 ? 1 : currentPage - 2;
    renderSpread();
});

document.getElementById('pdfNextPageBtn').addEventListener('click', () => {
    if (currentPage >= pdfDoc.numPages) return;
    currentPage = currentPage === 1 ? 2 : currentPage + 2;
    renderSpread();
});

// --- ZOOM CAPABILITIES ---
function updateZoomText() {
    document.getElementById('pdfZoomLevel').textContent = `${Math.round(pageZoom * 100)}%`;
}

document.getElementById('pdfZoomInBtn').addEventListener('click', () => {
    if (pageZoom >= 3) return; // Max 300%
    pageZoom += 0.25;
    updateZoomText();
    renderSpread();
});

document.getElementById('pdfZoomOutBtn').addEventListener('click', () => {
    if (pageZoom <= 0.5) return; // Min 50%
    pageZoom -= 0.25;
    updateZoomText();
    renderSpread();
});

// --- FIGMA-STYLE PAN TOOL (DRAG TO SCROLL) ---
let isDragging = false;
let startX, startY, scrollLeft, scrollTop;

dragContainer.addEventListener('mousedown', (e) => {
    // Only allow drag if zoomed in enough to cause overflow
    if (dragContainer.scrollWidth <= dragContainer.clientWidth && dragContainer.scrollHeight <= dragContainer.clientHeight) return;
    
    isDragging = true;
    startX = e.pageX - dragContainer.offsetLeft;
    startY = e.pageY - dragContainer.offsetTop;
    scrollLeft = dragContainer.scrollLeft;
    scrollTop = dragContainer.scrollTop;
});

dragContainer.addEventListener('mouseleave', () => isDragging = false);
dragContainer.addEventListener('mouseup', () => isDragging = false);

dragContainer.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.pageX - dragContainer.offsetLeft;
    const y = e.pageY - dragContainer.offsetTop;
    dragContainer.scrollLeft = scrollLeft - (x - startX);
    dragContainer.scrollTop = scrollTop - (y - startY);
});