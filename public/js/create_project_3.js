$(document).on('click', '.download_excel_bom', function() {
        var csrf_token = "{{csrf_token()}}";
        var quotation_number = $('.assembly_quotation_ref').val();
        var full_article_number = $(this).data('article_number');
        var item_id = $(this).data('item_id');
        var item_name = $(this).data('item_name');
        $.ajax({
            url: "{{route('getBOM')}}",
            type: "POST",
            data: {
                quotation_number: quotation_number,
                full_article_number: full_article_number,
                item_id: item_id,
                item_name: item_name
            },
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },

            success: function(response) {
                if (response.item_name == "Atmos") {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";

                    csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;
                    console.log(response.data.items);
                    if (response.data.items && Array.isArray(response.data.items)) {
                        let cart = response.data.atmosCart;
                        if (cart.is_accesories_manual != "1") {
                            csvContent += "\nItems:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            response.data.items.forEach(item => {
                                csvContent += `${item.item_description},${item.wilo_artilce_no}, ,${item.unit_price},${item.qty},${item.total_price}\n`;
                            });
                        }
                    }

                    if (response.data.atmosBOMitems && Array.isArray(response.data.atmosBOMitems)) {
                        let cart = response.data.atmosCart;
                        if (cart.is_accesories_manual != "1") {
                            csvContent += "\nAtmos BOM Items:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            response.data.atmosBOMitems.forEach(item => {
                                if (item.item_description != "Assembly Cost" && item.item_description != "Testing Cost" && item.item_description != "Balancing Cost") {

                                    csvContent += `"${item.item_description}","${item.wilo_artilce_no}",  ,"${item.unit_price}","${item.qty}","${item.total_price}"\n`;
                                }
                            });
                        }
                    }

                    if (response.data.atmosBOMitemsSupervisor) {
                        let cart = response.data.atmosCart;
                        if (cart.is_accesories_manual != "1") {
                            let supervisor = response.data.atmosBOMitemsSupervisor;
                            csvContent += `${supervisor.item_description},${supervisor.wilo_artilce_no},  ,${supervisor.unit_price},${supervisor.qty},${supervisor.total_price}\n`;
                        }
                    }

                    if (response.data && typeof response.data.adderData === "object") {
                        let adderData = Object.values(response.data.adderData);
                        if (adderData.length > 0) {
                            csvContent += "\nAtmos Adder ids Details:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            adderData.forEach(item => {
                                csvContent += `${item.name},'', ${item.id},${item.price},1,${item.price}\n`;
                            });
                        } else {
                            console.warn("AdderData is empty after conversion.");
                        }
                    } else {
                        console.warn("AdderData is not found or is not an object.");
                    }

                    if (response.data.atmosCart) {
                        let cart = response.data.atmosCart;
                        if (cart.is_bareshaft_selection != "1") {
                            csvContent += "\nAtmos Cart Details:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            const itemDesc = `${cart.power}KW ${cart.no_of_pole}P ${cart.efficiency} ${cart.voltage}V ${cart.frequency}Hz ${cart.brand} ${cart.application == 1 ? "constant" : "Variable"} Speed`;

                            csvContent += `${itemDesc}, -- ,  ,${cart.accesories_price},1,${cart.accesories_price}\n`;
                        }
                    }

                    if (response.data.atmosCart) {
                        let cart = response.data.atmosCart;
                        if (cart.is_accesories_manual == "1") {
                            csvContent += "\nAtmos Cart Details:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            csvContent += `Accessories-Manual, -- ,  ,${cart.accesories_price},1,${cart.accesories_price}\n`;
                        }
                    }

                    if (response.data.atmosCart) {
                        let cart = response.data.atmosCart;
                        if (cart.is_accesories_manual == "1") {
                            csvContent += "\nAtmos Cart Details:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            csvContent += `${cart.pump_name},${cart.full_article_number},  ,${cart.price},${cart.qty},${cart.total_price}\n`;
                        }
                    }

                    let encodedUri = encodeURI(csvContent);
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    // link.setAttribute("download", `BOM_${item_name}_${full_article_number}.csv`);
                    link.setAttribute("download", `BOM_${quotation_number}_${item_name}_${full_article_number}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                if (response.item_name == "SCP") {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";

                    csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;

                    if (response.data.items && Array.isArray(response.data.items)) {
                        let cart = response.data.items;
                        csvContent += "\nItems:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        response.data.items.forEach(item => {
                            csvContent += `${item.item_description},${item.wilo_artilce_no},  ,${item.unit_price},${item.qty},${item.total_price}\n`;
                        });
                    }

                    if (response.data && typeof response.data.adderData === "object") {
                        let adderData = Object.values(response.data.adderData);
                        if (adderData.length > 0) {
                            csvContent += "\nSCP Adder ids Details:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            adderData.forEach(item => {
                                csvContent += `${item.name},'', ${item.id},${item.price},1,${item.price}\n`;
                            });
                        } else {
                            console.warn("AdderData is empty after conversion.");
                        }
                    } else {
                        console.warn("AdderData is not found or is not an object.");
                    }

                    if (response.data) {
                        let cart = response.data.scpCart;
                        csvContent += "\nSCP Cart Details:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        const itemDesc = `${cart.power}KW ${cart.no_of_pole}P ${cart.efficiency} ${cart.voltage}V ${cart.frequency}Hz ${cart.brand} ${cart.application == 1 ? "constant" : "Variable"} Speed`;

                        csvContent += `${itemDesc}, -- ,  ,${response.data.motor_price},1,${response.data.motor_price}\n`;
                    }

                    if (response.data) {
                        let cart = response.data.scpCart;
                        let article_number = response.data.article_number;
                        csvContent += "\nSCP Cart Details:\n";
                   
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        csvContent += `${cart.pump_name}, ${article_number} ,  ,${cart.bare_pump_price},1,${cart.bare_pump_price}\n`;
                    }
                    
                    let encodedUri = encodeURI(csvContent);
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    //link.setAttribute("download", `BOM_${quotation_number}.csv`);
                    link.setAttribute("download", `BOM_${quotation_number}_${item_name}_${full_article_number}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                if (response.item_name == "Control Panel") {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";

                    csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;
                    if (response.data.items && Array.isArray(response.data.items)) {
                        let cart = response.data.items;
                        csvContent += "\nItems:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        response.data.items.forEach(item => {
                            csvContent += `${item.item_description},${item.wilo_artilce_no},  ,${item.price},${item.qty},${item.total_price}\n`;
                        });
                    }

                    let encodedUri = encodeURI(csvContent);
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    // link.setAttribute("download", `BOM_${quotation_number}.csv`);
                    link.setAttribute("download", `BOM_${quotation_number}_${item_name}_${full_article_number}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                if (response.item_name == "Booster") {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";

                    csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;

                    if (response.data.boosterCartData) {
                        let boosterCartData = response.data.boosterCartData;
                        let pump_qty = response.data.boosterCartData.booster_cp_data[0].no_of_pump_id;
                        csvContent += "\nItems:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        csvContent += `"${boosterCartData.model_no}","${boosterCartData.booster_article_number}",  ,"${boosterCartData.pump_price}","${pump_qty}","${boosterCartData.pump_price * pump_qty}"\n`;
                    }

                    if (response.data.items && Array.isArray(response.data.items)) {
                        let cart = response.data.items;
                        csvContent += "\nItems:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        response.data.items.forEach(item => {
                            csvContent += `"${item.item_description}","${item.wilo_artilce_no}",  ,"${item.price}","${item.qty}","${item.price * item.qty}"\n`;
                        });
                    }

                    if (response.data.cpBoosterItems && Array.isArray(response.data.cpBoosterItems)) {
                        let cart = response.data.cpBoosterItems;
                        csvContent += "\nControl Panel Items:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        response.data.cpBoosterItems.forEach(item => {
                            csvContent += `"${item.item_description}","${item.wilo_artilce_no}",  ,"${item.price}","${item.qty}","${item.total_price}"\n`;
                        });
                    }


                    let encodedUri = encodeURI(csvContent);
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    // link.setAttribute("download", `BOM_${quotation_number}.csv`);
                    link.setAttribute("download", `BOM_${quotation_number}_${item_name}_${full_article_number}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                if (response.item_name == "Fire-Fighting") {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";
                    csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;
                    if (response.data.items && Array.isArray(response.data.items)) {
                        let cart = response.data.items;
                        csvContent += "\nItems:\n";
                        csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                        response.data.items.forEach(item => {
                            csvContent += `"${item.description}","${item.article_number}", ,"${item.unit_price}","${item.qty}","${item.total_price}"\n`;

                        });
                    }

                    let encodedUri = encodeURI(csvContent);
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    // link.setAttribute("download", `BOM_${quotation_number}.csv`);
                    link.setAttribute("download", `BOM_${quotation_number}_${item_name}_${full_article_number}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }

        });
    });