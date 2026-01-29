const API_BASE_URL = "https://api.chatpdf.com";
API_KEY='sec_belqwvMtPQ2jTxFpejznE52Y2pV0iObm'



async function extractPONumber(input) {
    const file = input.files[0];
    if (!file || file.type !== "application/pdf") {
        alert("Please upload a valid PDF file.");
        return;
    }

    try {
        
        // Step 1: Upload the PDF
        const uploadResponse = await uploadPDF(file);
        if (!uploadResponse || !uploadResponse.pdfId) {
            alert("Failed to upload the PDF.");
            return;
        }

        const pdfId = uploadResponse.pdfId;
        console.log("PDF uploaded successfully. PDF ID:", pdfId);

        // Step 2: Query the PDF for details
        const queries = [
            { key: "PO_number", query: "Find the Purchase Order (PO) number" },
            { key: "order_date", query: "Extract the Order Date" },
            { key: "payment_terms", query: "Extract Payment Terms" },
            { key: "shipment_method", query: "Extract the Shipment Method" },
        ];

        for (const { key, query } of queries) {
            const response = await queryPDF(pdfId, query);
            if (response && response.content) {
                document.getElementById(key).value = response.content.trim();
                console.log(`${key}: ${response.content.trim()}`);
            } else {
                console.warn(`Could not extract ${key}`);
                alert(`Could not extract ${key}. Please enter it manually.`);
            }
        }

        // Step 3: Extract table data
        const tableQuery = "Extract all table data starting from the headers to Total AED Excl. VAT";
        const tableResponse = await queryPDF(pdfId, tableQuery);
        if (tableResponse && tableResponse.content) {
            const tableData = parseTableData(tableResponse.content);
            displayTableData(tableData);
        } else {
            console.warn("No table data found.");
            alert("No table data found in the PDF.");
        }
    } catch (error) {
        console.error("An error occurred:", error);
        alert("An error occurred while processing the PDF. Please try again.");
    }
}

async function uploadPDF(file) {
    const formData = new FormData();
    formData.append("file", file);

    const response = await fetch(`${API_BASE_URL}/pdfs`, {
        method: "POST",
        headers: {
            Authorization: `Bearer ${API_KEY}`,
        },
        body: formData,
    });

    if (!response.ok) {
        console.error("Failed to upload PDF:", response.statusText);
        throw new Error("PDF upload failed.");
    }

    return response.json();
}

async function queryPDF(pdfId, question) {
    const response = await fetch(`${API_BASE_URL}/pdfs/${pdfId}/query`, {
        method: "POST",
        headers: {
            Authorization: `Bearer ${API_KEY}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ question }),
    });

    if (!response.ok) {
        console.error("Failed to query PDF:", response.statusText);
        throw new Error("PDF query failed.");
    }

    return response.json();
}

function parseTableData(rawText) {
    const lines = rawText.split("\n");
    const tableData = [];

    for (const line of lines) {
        const row = line.split(/\s+/);
        tableData.push(row);
    }

    return tableData;
}

function displayTableData(tableData) {
    if (!tableData || tableData.length === 0) {
        console.error("No table data found.");
        return;
    }

    const table = document.getElementById("pdfTable");
    table.innerHTML = "";

    const thead = document.createElement("thead");
    const headerRow = document.createElement("tr");
    tableData[0].forEach((header) => {
        const th = document.createElement("th");
        th.textContent = header;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    for (let i = 1; i < tableData.length; i++) {
        const row = document.createElement("tr");
        tableData[i].forEach((cell) => {
            const td = document.createElement("td");
            td.textContent = cell;
            row.appendChild(td);
        });
        tbody.appendChild(row);
    }
    table.appendChild(tbody);

    document.getElementById("pdfTableContainer").style.display = "block";
}
