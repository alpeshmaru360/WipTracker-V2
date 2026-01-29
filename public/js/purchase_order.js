// Set the worker source for pdf.js
pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.min.js";

function extractPONumber(input) {
    const table = document.getElementById("pdfTable");
    const tbody = table.querySelector("tbody");
    tbody.innerHTML = ""; // Clear existing rows
    const file = input.files[0];

    if (file && file.type === "application/pdf") {
        const reader = new FileReader();
        reader.onload = (e) => {
            const typedarray = new Uint8Array(e.target.result);

            pdfjsLib
                .getDocument(typedarray)
                .promise.then((pdf) => {
                    const numPages = pdf.numPages;
                    const tableData = [];
                    const promises = [];

                    // Extract text from all pages
                    for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                        promises.push(
                            pdf.getPage(pageNum).then((page) => {
                                return page.getTextContent().then((textContent) => {
                                    // Combine items into lines based on their y-coordinate
                                    const lines = {};
                                    textContent.items.forEach((item) => {
                                        const y = Math.round(item.transform[5]); // Round Y-coordinate to group similar lines
                                        if (!lines[y]) {
                                            lines[y] = [];
                                        }
                                        lines[y].push(item.str);
                                    });

                                    // Create an array of lines sorted by y-coordinate (top to bottom)
                                    const sortedLines = Object.keys(lines)
                                        .sort((a, b) => b - a) // Sort descending to match PDF rendering order
                                        .map((y) => lines[y].join(" ").trim());

                                    return sortedLines;
                                });
                            })
                        );
                    }

                    // Process all pages
                    Promise.all(promises).then((allPagesLines) => {
                        // Extract fields from the first page only
                        extractFields(allPagesLines[0]);

                        // Extract table data from all pages
                        for (let pageLines of allPagesLines) {
                            const pageTableData = extractTableData(pageLines);
                            tableData.push(...pageTableData);
                        }

                        console.log("Combined Table Data from All Pages:", tableData);
                        displayTableData(tableData);
                    }).catch((error) => {
                        console.error("Error processing PDF pages:", error);
                        alert("Error processing PDF pages. Please try again or enter the details manually.");
                        clearAllFields();
                    });
                })
                .catch((error) => {
                    console.error("Error loading PDF:", error);
                    alert("Error loading PDF. Please try again or enter the details manually.");
                    clearAllFields();
                });
        };
        reader.readAsArrayBuffer(file);
    }
}

function extractFields(sortedLines) {
    // Extract PO Number
    const poLine = sortedLines.find((line) => /PO-\d{4}-\d+\s+Order No\./.test(line));
    if (poLine) {
        const poNumberMatch = poLine.match(/PO-\d{4}-\d+/);
        if (poNumberMatch && poNumberMatch[0]) {
            document.getElementById("PO_number").value = poNumberMatch[0];
        } else {
            showPONotFoundAlert();
        }
    } else {
        showPONotFoundAlert();
    }

    // Extract Order Date
    const orderDateLine = sortedLines.find((line) => /Order Date\s+[A-Za-z]+\s+\d{1,2},\s+\d{4}/.test(line));
    if (orderDateLine) {
        const orderDateMatch = orderDateLine.match(/Order Date\s+([A-Za-z]+\s+\d{1,2},\s+\d{4})/);
        if (orderDateMatch && orderDateMatch[1]) {
            document.getElementById("order_date").value = orderDateMatch[1];
        }
    }

    // Extract Payment Terms
    const paymentTermsLine = sortedLines.find((line) => /Payment Terms/.test(line));
    if (paymentTermsLine) {
        const paymentTermsMatch = paymentTermsLine.match(/Payment Terms\s*(.*)/);
        document.getElementById("payment_terms").value = (paymentTermsMatch && paymentTermsMatch[1]) ? paymentTermsMatch[1].trim() : "N/A";
    } else {
        document.getElementById("payment_terms").value = "N/A";
    }

    // Extract Shipment Method
    const shipmentMethodLine = sortedLines.find((line) => /Shipment Method/.test(line));
    if (shipmentMethodLine) {
        const shipmentMethodMatch = shipmentMethodLine.match(/Shipment Method\s*(.*)/);
        document.getElementById("shipment_method").value = (shipmentMethodMatch && shipmentMethodMatch[1]) ? shipmentMethodMatch[1].trim() : "N/A";
    } else {
        document.getElementById("shipment_method").value = "N/A";
    }

    // Extract Supplier (assuming it's always the third line in the sorted array)
    if (sortedLines.length >= 3) {
        const supplierName = sortedLines[2].trim();
        document.getElementById("supplier").value = supplierName;
    } else {
        document.getElementById("supplier").value = "";
        console.log("Supplier not found: Not enough lines in the sorted array");
    }

    // Extract Currency
    const currency = extractCurrency(sortedLines);
    if (currency) {
        document.getElementById("currency").value = currency;
    }
}

function extractCurrency(sortedLines) {
    const totalAmountLine = sortedLines.find((line) => /Total Amount/.test(line));
    if (totalAmountLine) {
        const currencyMatch = totalAmountLine.match(/Total Amount\s+(\w+)/);
        if (currencyMatch && currencyMatch[1]) {
            return currencyMatch[1];
        }
    }
    return "";
}

function showPONotFoundAlert() {
    alert("PO number not found in this PDF. Please choose another PDF.");
    clearAllFields();
}

function clearAllFields() {
    document.getElementById("PO_number").value = "";
    document.getElementById("order_date").value = "";
    document.getElementById("payment_terms").value = "";
    document.getElementById("shipment_method").value = "";
    document.getElementById("supplier").value = "";
    document.getElementById("currency").value = "";
    document.getElementById("artical_no").value = "";
}

function extractTableData(lines) {
    const tableData = [];
    let tableStarted = false;
    let headerSkipped = false;
    let buffer = []; // Buffer to accumulate multi-line rows

    for (const line of lines) {
        // Check for the start of the table
        if (line.includes("Position") && line.includes("Amount")) {
            tableStarted = true;
            continue;
        }

        if (tableStarted) {
            // Check for table end
            if (line.includes("Total AED Excl. VAT") || line.includes("Total EUR") || line.includes("Total USD") || line.includes("Notes:") || line.includes("Scope of supply:")) {
                if (buffer.length > 0) {
                    processBuffer(buffer, tableData);
                    buffer = [];
                }
                break;
            }

            if (!headerSkipped) {
                headerSkipped = true;
                continue;
            }

            // Accumulate lines into buffer
            buffer.push(line.trim());

            // If the line starts with a number followed by an article number, it’s a new row
            if (/^\d+\s+\d+/.test(line)) {
                if (buffer.length > 1) {
                    // Process previous buffer if it exists
                    processBuffer(buffer.slice(0, -1), tableData);
                }
                buffer = [line.trim()]; // Start new buffer with current line
            }
        }
    }

    // Process any remaining buffer
    if (buffer.length > 0) {
        processBuffer(buffer, tableData);
    }

    return tableData;
}

function processBuffer(buffer, tableData) {
    if (buffer.length === 0) return;

    // Combine buffer into a single line
    let combinedLine = buffer.join(" ").trim();

    // Preprocess the line to handle commas in numbers (e.g., "1,524.90" -> "1524.90")
    combinedLine = combinedLine.replace(/(\d+),(\d+)/g, "$1$2");

    // Split into cells, preserving the description
    const cells = [];
    let currentCell = "";
    let inDescription = false;

    // Split the line into tokens while preserving spaces in descriptions
    const tokens = combinedLine.split(/\s+/);
    let i = 0;

    // Position No (always the first token)
    if (i < tokens.length) {
        cells.push(tokens[i]); // Position No
        i++;
    }

    // Article Number (always the second token)
    if (i < tokens.length) {
        cells.push(tokens[i]); // Article Number
        i++;
    }

    // Vendor Item No (check if the next token is a number)
    let vendorItemNo = "blank";
    if (i < tokens.length && /^[0-9]+$/.test(tokens[i])) {
        vendorItemNo = tokens[i];
        i++;
    } else {
        vendorItemNo = "blank";
    }
    cells.push(vendorItemNo);

    // Initial Description: Collect tokens until we find Quantity (a number) followed by Unit of Measure (e.g., "PIECE")
    let initialDescriptionParts = [];
    let quantityIndex = -1;

    // Look for Quantity (a number) followed by Unit of Measure (e.g., "PIECE")
    for (let j = i; j < tokens.length - 1; j++) {
        if (/^\d+$/.test(tokens[j]) && /^[A-Z]+$/.test(tokens[j + 1])) {
            quantityIndex = j;
            break;
        }
    }

    // Collect initial description tokens up to the Quantity field
    if (quantityIndex !== -1) {
        initialDescriptionParts = tokens.slice(i, quantityIndex);
        i = quantityIndex;
    } else {
        // Fallback: Look for Direct Unit Cost or Amount to determine the end of description
        for (let j = i; j < tokens.length; j++) {
            if (/^\d+(\.\d+)?$/.test(tokens[j])) {
                quantityIndex = j;
                break;
            }
        }
        if (quantityIndex !== -1) {
            initialDescriptionParts = tokens.slice(i, quantityIndex);
            i = quantityIndex;
        } else {
            initialDescriptionParts = tokens.slice(i);
            i = tokens.length;
        }
    }

    // Quantity (a number)
    let quantity = "0";
    if (i < tokens.length && /^\d+$/.test(tokens[i])) {
        quantity = tokens[i];
        i++;
    }
    cells.push(quantity);

    // Unit of Measure (e.g., "PIECE")
    let unitOfMeasure = "PIECE";
    if (i < tokens.length && /^[A-Z]+$/.test(tokens[i])) {
        unitOfMeasure = tokens[i];
        i++;
    }
    cells.push(unitOfMeasure);

    // VAT % (might be "N/A" or a percentage like "5%")
    let vatPercent = "N/A";
    if (i < tokens.length) {
        if (tokens[i].match(/^\d+%$/)) {
            vatPercent = tokens[i].replace("%", "");
            i++;
        } else if (tokens[i] === "N/A") {
            vatPercent = "N/A";
            i++;
        }
    }
    cells.push(vatPercent);

    // Direct Unit Cost (a number, possibly with decimals)
    let directUnitCost = 0;
    if (i < tokens.length && /^\d+(\.\d+)?$/.test(tokens[i])) {
        directUnitCost = parseFloat(tokens[i]) || 0;
        i++;
    }
    cells.push(directUnitCost);

    // VAT Amount (a number, possibly 0 or missing)
    let vatAmount = 0;
    if (vatPercent === "N/A") {
        vatAmount = 0; // If VAT % is N/A, VAT Amount should be 0
    } else if (i < tokens.length && /^\d+(\.\d+)?$/.test(tokens[i])) {
        vatAmount = parseFloat(tokens[i]) || 0;
        i++;
    }
    cells.push(vatAmount);

    // Amount (the next numeric field after VAT Amount, or calculate if not present)
    let amount = 0;
    if (i < tokens.length && /^\d+(\.\d+)?$/.test(tokens[i])) {
        amount = parseFloat(tokens[i]) || 0;
        i++;
    } else {
        // Fallback: Calculate Amount as Direct Unit Cost × Quantity + VAT Amount
        amount = (directUnitCost * parseFloat(quantity)) + vatAmount;
    }
    cells.push(amount);

    // Additional Description: Collect remaining tokens, but exclude "Total" or total amount values
    let additionalDescriptionParts = [];
    if (i < tokens.length) {
        let remainingTokens = tokens.slice(i);
        for (let j = 0; j < remainingTokens.length; j++) {
            // Stop if we encounter "Total", a currency code, or a numeric value that matches or exceeds the row's amount
            if (
                remainingTokens[j].toLowerCase() === "total" || // Stop at "Total"
                ["EUR", "USD", "AED"].includes(remainingTokens[j]) || // Stop at currency codes
                (/^\d+(\.\d+)?$/.test(remainingTokens[j]) &&
                    (parseFloat(remainingTokens[j]) >= amount || // Stop if numeric value matches or exceeds the row's amount
                        (j < remainingTokens.length - 1 && ["EUR", "USD", "AED"].includes(remainingTokens[j + 1])))) // Or if followed by a currency code
            ) {
                break;
            }
            additionalDescriptionParts.push(remainingTokens[j]);
        }
    }

    // Combine initial and additional description parts
    let descriptionParts = [...initialDescriptionParts, ...additionalDescriptionParts];
    let description = descriptionParts.join(" ").trim();
    // Remove the line that adds quotes around numbers
    // description = description.replace(/\b\d+\b/g, "'$&'"); // Removed this line
    cells.push(description);

    // Log the extracted row for debugging
    console.log("Extracted Row Cells:", cells);
    console.log("Tokens for debugging:", tokens);

    // Check if this looks like a valid row
    if (!/^\d+$/.test(cells[0]) || cells.length < 10) {
        console.warn("Invalid row, skipping:", combinedLine);
        return;
    }

    const rowData = {
        position_no: cells[0],
        artical_no: cells[1],
        vendor_item_no: cells[2],
        description: cells[9], // Description is at index 9
        quantity: cells[3],
        unit_of_measure: cells[4],
        vat_per: cells[5],
        direct_unit_cost: cells[6],
        vat_amount: cells[7],
        amount: cells[8],
    };
    tableData.push(rowData);
}

function displayTableData(tableData) {
    if (!tableData || tableData.length === 0) {
        console.error("No table data found");
        return;
    }

    const table = document.getElementById("pdfTable");
    const tbody = table.querySelector("tbody");
    tbody.innerHTML = ""; // Clear existing rows

    // Get conversion rates
    const aed_euro = parseFloat($(".aedValue").val()) || 1;
    const usd_euro = parseFloat($(".usdValue").val()) || 1;
    console.log("Conversion Rates:", { aed_euro, usd_euro });

    // Get the selected currency
    const selectedCurrency = document.getElementById("currency").value.toUpperCase();

    // Display all rows
    for (let i = 0; i < tableData.length; i++) {
        const row = document.createElement("tr");
        const amount = parseFloat(tableData[i].amount) || 0;

        let amountEUR;
        if (selectedCurrency === "AED") {
            amountEUR = amount * aed_euro;
        } else if (selectedCurrency === "USD") {
            amountEUR = amount * usd_euro;
        } else if (selectedCurrency === "EUR") {
            amountEUR = amount; // No conversion needed if currency is already EUR
        } else {
            console.warn("Unexpected currency type:", selectedCurrency);
            amountEUR = amount;
        }

        const amountEURFormatted = amountEUR.toFixed(2);
        const amountFormatted = amount.toFixed(2);

        const newRowData = [
            tableData[i].position_no,
            tableData[i].artical_no,
            tableData[i].vendor_item_no,
            tableData[i].description,
            tableData[i].quantity,
            tableData[i].unit_of_measure,
            tableData[i].vat_per || "N/A",
            tableData[i].direct_unit_cost.toFixed(2),
            tableData[i].vat_amount.toFixed(2),
            amountFormatted,
            amountEURFormatted,
        ];

        newRowData.forEach((cell) => {
            const td = document.createElement("td");
            td.textContent = cell || "N/A";
            // Do NOT set contenteditable="true" to prevent manual editing
            row.appendChild(td);
        });

        tbody.appendChild(row);
    }

    // Set the article number in the input field from the first row
    if (tableData.length > 0) {
        document.getElementById("artical_no").value = tableData[0].artical_no;
    }

    document.getElementById("pdfTableContainer").style.display = "block";
}