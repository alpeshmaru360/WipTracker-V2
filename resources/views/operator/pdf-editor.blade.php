<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PDF Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .tool-button.active {background-color: #e9ecef;}
        #pdfContainer {max-height: calc(100vh - 200px);overflow: auto;}
        .tools-container {position: sticky;top: 0;background: white;z-index: 100;padding: 10px 0;border-bottom: 1px solid #dee2e6;}
    </style>
</head>
<body>
    <div class="modal fade" id="pdfEditorModal" tabindex="-1" aria-labelledby="pdfEditorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfEditorModalLabel">PDF Editor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tools-container">
                        <div class="btn-group mb-2">
                            <button id="textTool" class="btn btn-outline-secondary tool-button">
                                <i class="fas fa-font"></i> Text
                            </button>
                            <button id="drawTool" class="btn btn-outline-secondary tool-button">
                                <i class="fas fa-pencil-alt"></i> Draw
                            </button>
                            <button id="clearTool" class="btn btn-outline-secondary">
                                <i class="fas fa-trash"></i> Clear
                            </button>
                        </div>

                        <div id="textFormatting" class="btn-group ms-2" style="display: none;">
                            <button id="boldBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-bold"></i>
                            </button>
                            <button id="italicBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-italic"></i>
                            </button>
                            <button id="underlineBtn" class="btn btn-outline-secondary">
                                <i class="fas fa-underline"></i>
                            </button>
                            <select id="fontSizeSelect" class="btn btn-outline-secondary">
                                <option value="12">12</option>
                                <option value="14">14</option>
                                <option value="16">16</option>
                                <option value="18">18</option>
                                <option value="20">20</option>
                                <option value="24">24</option>
                                <option value="36">36</option>
                            </select>
                            <select id="fontFamilySelect" class="btn btn-outline-secondary">
                                <option value="Arial">Arial</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Courier New">Courier New</option>
                            </select>
                            <input type="color" id="textColorPicker" class="form-control form-control-color">
                        </div>

                        <div id="drawingControls" class="btn-group ms-2">
                            <input type="color" id="strokeColor" class="form-control form-control-color">
                            <input type="range" id="strokeWidth" class="form-range" min="1" max="20" value="2">
                        </div>

                        <button id="saveBtn" class="btn btn-primary float-end">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>

                    <div id="pdfContainer">
                        <canvas id="pdfCanvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pdf-editor')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        document.addEventListener('DOMContentLoaded', function() {
            let pdfDoc = null;
            let canvas = null;
            let fabricCanvas = null;
            let currentPage = 1;
            let currentPdfUrl = '';
            let projectId = null;
            let activeObject = null;

            async function loadPDF(url) {
                try {
                    currentPdfUrl = url;
                    const loadingTask = pdfjsLib.getDocument(url);
                    pdfDoc = await loadingTask.promise;
                    renderPage(currentPage);
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    alert('Error loading PDF file');
                }
            }

            async function renderPage(pageNumber) {
                try {
                    const page = await pdfDoc.getPage(pageNumber);
                    const viewport = page.getViewport({ scale: 1.5 });
                    
                    const container = document.getElementById('pdfContainer');
                    const containerWidth = container.clientWidth;
                    const scale = containerWidth / viewport.width;
                    const scaledViewport = page.getViewport({ scale: scale * 0.95 });
                    
                    canvas.height = scaledViewport.height;
                    canvas.width = scaledViewport.width;
                    
                    const renderContext = {
                        canvasContext: canvas.getContext('2d'),
                        viewport: scaledViewport
                    };
                    
                    await page.render(renderContext).promise;

                    fabricCanvas.setWidth(canvas.width);
                    fabricCanvas.setHeight(canvas.height);
                    fabricCanvas.setBackgroundImage(canvas.toDataURL(), fabricCanvas.renderAll.bind(fabricCanvas));
                } catch (error) {
                    console.error('Error rendering page:', error);
                }
            }

            function initializeFabricCanvas() {
                canvas = document.getElementById('pdfCanvas');
                fabricCanvas = new fabric.Canvas('pdfCanvas', {
                    isDrawingMode: false
                });
                
                fabricCanvas.on('selection:created', function(e) {
                    activeObject = e.target;
                    if (activeObject.type === 'i-text') {
                        showTextFormatting();
                    }
                });

                fabricCanvas.on('selection:cleared', function() {
                    activeObject = null;
                    hideTextFormatting();
                });
            }

            function initializeTools() {
                document.getElementById('textTool').addEventListener('click', function() {
                    const text = new fabric.IText('Click to edit text', {
                        left: 100,
                        top: 100,
                        fontSize: 20,
                        fontFamily: 'Arial',
                        fill: '#000000'
                    });
                    fabricCanvas.add(text);
                    fabricCanvas.setActiveObject(text);
                });

                document.getElementById('drawTool').addEventListener('click', function() {
                    fabricCanvas.isDrawingMode = !fabricCanvas.isDrawingMode;
                    this.classList.toggle('active');
                    if (fabricCanvas.isDrawingMode) {
                        fabricCanvas.freeDrawingBrush.width = parseInt(document.getElementById('strokeWidth').value, 10);
                        fabricCanvas.freeDrawingBrush.color = document.getElementById('strokeColor').value;
                    }
                });

                document.getElementById('clearTool').addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear all annotations?')) {
                        const backgroundImage = fabricCanvas.backgroundImage;
                        fabricCanvas.clear();
                        if (backgroundImage) {
                            fabricCanvas.setBackgroundImage(backgroundImage, fabricCanvas.renderAll.bind(fabricCanvas));
                        }
                    }
                });

                document.getElementById('saveBtn').addEventListener('click', async function() {
                    try {
                        const dataUrl = fabricCanvas.toDataURL({
                            format: 'png',
                            quality: 1
                        });

                        const response = await fetch('/save-edited-pdf', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                pdfData: dataUrl,
                                projectId: projectId
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            alert('PDF saved successfully');
                            bootstrap.Modal.getInstance(document.getElementById('pdfEditorModal')).hide();
                        } else {
                            throw new Error(result.message);
                        }
                    } catch (error) {
                        console.error('Error saving PDF:', error);
                        alert('Error saving PDF: ' + error.message);
                    }
                });

                // Text formatting controls
                document.getElementById('boldBtn').addEventListener('click', function() {
                    if (!activeObject) return;
                    activeObject.set('fontWeight', activeObject.fontWeight === 'bold' ? 'normal' : 'bold');
                    fabricCanvas.renderAll();
                });

                document.getElementById('italicBtn').addEventListener('click', function() {
                    if (!activeObject) return;
                    activeObject.set('fontStyle', activeObject.fontStyle === 'italic' ? 'normal' : 'italic');
                    fabricCanvas.renderAll();
                });

                document.getElementById('underlineBtn').addEventListener('click', function() {
                    if (!activeObject) return;
                    activeObject.set('underline', !activeObject.underline);
                    fabricCanvas.renderAll();
                });

                document.getElementById('fontSizeSelect').addEventListener('change', function() {
                    if (!activeObject) return;
                    activeObject.set('fontSize', parseInt(this.value, 10));
                    fabricCanvas.renderAll();
                });

                document.getElementById('fontFamilySelect').addEventListener('change', function() {
                    if (!activeObject) return;
                    activeObject.set('fontFamily', this.value);
                    fabricCanvas.renderAll();
                });

                document.getElementById('textColorPicker').addEventListener('input', function() {
                    if (!activeObject) return;
                    activeObject.set('fill', this.value);
                    fabricCanvas.renderAll();
                });

                document.getElementById('strokeColor').addEventListener('change', function() {
                    fabricCanvas.freeDrawingBrush.color = this.value;
                });

                document.getElementById('strokeWidth').addEventListener('change', function() {
                    fabricCanvas.freeDrawingBrush.width = parseInt(this.value, 10);
                });
            }

            const modal = document.getElementById('pdfEditorModal');
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const pdfUrl = button.getAttribute('data-pdf-url');
                projectId = button.getAttribute('data-project-id');
                
                initializeFabricCanvas();
                initializeTools();
                loadPDF(pdfUrl);
            });

            modal.addEventListener('hidden.bs.modal', function() {
                if (fabricCanvas) {
                    fabricCanvas.dispose();
                }
                pdfDoc = null;
                currentPage = 1;
                currentPdfUrl = '';
                projectId = null;
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });

            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (pdfDoc) {
                        renderPage(currentPage);
                    }
                }, 250);
            });
        });
    </script>
</body>
</html>

