document.addEventListener("DOMContentLoaded", function () {
    if (window.soFileUrl) {
        pdfjsLib.getDocument(window.soFileUrl).promise.then((pdf) => {
            let promises = [];
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                promises.push(
                    pdf.getPage(pageNum).then((page) => {
                        return page.getTextContent().then((textContent) => {
                            let lines = {};
                            textContent.items.forEach((item) => {
                                const y = Math.round(item.transform[5]);
                                if (!lines[y]) lines[y] = [];
                                lines[y].push(item.str);
                            });
                            return Object.keys(lines)
                                .sort((a, b) => b - a)
                                .map((y) => lines[y].join(" ").trim());
                        });
                    })
                );
            }

            Promise.all(promises).then((allPagesLines) => {
                extractFields(allPagesLines.flat());
            });
        });
    }
});

function extractFields(pageLines) {
    let salesOrderNumber = null;
    let currency = null;

    for (let i = 0; i < pageLines.length; i++) {
        let line = pageLines[i].trim();

        // Handle "SO-xxxx Order No." pattern
        if (/Order\s*No\./i.test(line) && line.includes("SO-")) {
            // Extract SO number before "Order No."
            const match = line.match(/(SO-\d{4}-\d+)/i);
            if (match) salesOrderNumber = match[1];
        }

        // Handle Currency
        if (/Currency/i.test(line)) {
            const match = line.match(/Currency[:\s]+([A-Z]+)/i);
            if (match) currency = match[1];
        }
    }

    console.log("Extracted Order No.:", salesOrderNumber);
    console.log("Extracted Currency:", currency);

    if (salesOrderNumber) {
        document.getElementById("sales_order_number").value = salesOrderNumber;
    }
    if (currency) {
        document.getElementById("currency").value = currency;
    }
}