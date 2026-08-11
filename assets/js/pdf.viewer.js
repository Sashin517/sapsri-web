import { getDocument } from '../vendor/pdfjs/pdf.mjs'; // import getDocument method from pdf.mjs

pdfjsLib.GlobalWorkerOptions.workerSrc = '../vendor/pdfjs/pdf.worker.mjs'; // specify workerSrc property


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

    const card = event.target.closest(".card");
    const cardTitle = card.querySelector(".card-title").textContent;
    const cardLink = card.querySelector(".btn-download").href;

    pdfURL = cardLink;
    
    pdfViewerModalTitle.textContent = cardTitle;

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

    getDocument(url).promise.then(function (pdfDoc_) {
        pdfDoc = pdfDoc_;
        document.getElementById('pdfPageCount').textContent = pdfDoc.numPages;

        renderPage(currentPage);
    });

}


function renderPage(pageNum) {

    pageRendering = true;

    pdfDoc.getPage(pageNum).then(function (page) {

        const defaultPageViewport = page.getViewport({ scale: 1 });
        const scaleForModal = (pdfViewerModalBodyInnerWidth / defaultPageViewport.width) * pageZoom;

        const viewport = page.getViewport({ scale: scaleForModal });

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


