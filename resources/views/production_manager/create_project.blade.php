@extends('layouts.main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />

<section class="create_project_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100 px-4">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{route('ProductionManagerProjectCreateDo')}}" enctype="multipart/form-data"
                                method="post">
                                @csrf
                                    <input type="hidden" name="main_pro_name" value="{{$project_name}}">     
                                @if($project_name === 'wi_track')
                                    <input type="hidden" name="id" value="{{ $id }}">
                                @endif
                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="project_name">Project Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        @if($project_name == "wi_track")                                        
                                        <input type="text" name="project_name" class="form-control" value="{{ $project_name_value }}" readonly />
                                        @else
                                        <input type="text" id="project_name" name="project_name" class="form-control" placeholder="Please Enter Project Name" required />
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="assembly_quotation_ref">Assembly Quotation Ref. <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="assembly_quotation_ref" name="assembly_quotation_ref" class="form-control assembly_quotation_ref" 
                                            placeholder="Please Enter Assembly Quotation Reference Number" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="sales_person">Sales Person <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        @if($project_name == "wi_track")
                                        <input type="text" name="sales_person" class="form-control" value="{{$sales_name}}" readonly />
                                        @else
                                        <input type="text" id="sales_person" name="sales_person" class="form-control" placeholder="Please Enter Sales Persons Name" required />
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        @if($project_name == "wi_track")
                                        <input type="text" name="customer_name" class="form-control" value="{{$customer_name}}" readonly />
                                        @else
                                        <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Please Enter Customer Name" required />
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="customer_ref">Customer Ref. <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        @if($project_name == "wi_track")
                                        <input type="text" name="customer_ref" class="form-control"  value="{{$customer_ref_no}}" readonly />
                                        @else
                                        <input type="text" id="customer_ref" name="customer_ref" class="form-control" placeholder="Please Enter Customer Ref No." required />
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label">Customer Documents <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        @if($project_name == "wi_track")
                                        <ul class="list-group">
                                            @if($documents)
                                            @foreach($documents as $document)
                                            @php
                                            $extension = pathinfo($document, PATHINFO_EXTENSION);
                                            @endphp
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-4 pt-0 pb-0">
                                                <span>
                                                    @php
                                                    $iconClass = '';

                                                    if ($extension == "pdf") {
                                                    $iconClass = "fs-22 fa fa-file-pdf";
                                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                                    $iconClass = "fs-22 fa fa-file-word";
                                                    } elseif (in_array($extension, ['xlsx', 'csv'])) {
                                                    $iconClass = "fs-22 fa fa-file-excel";
                                                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $iconClass = "fs-22 fa fa-image";
                                                    } else {
                                                    $iconClass = "fs-22 fa fa-file";
                                                    }
                                                    @endphp
                                                    <i class="file-icon {{ $extension }} {{$iconClass}} icon_color"></i>
                                                    <a href="{{ asset($document) }}" target="_blank">{{ basename($document) }}</a>
                                                </span>
                                            </li>
                                            @endforeach
                                            @endif
                                        </ul>
                                        @else
                                        <input type="file" name="customer_documents[]" class="form-control mb-2"
                                            multiple accept=".pdf, .doc, .docx, .jpg, .jpeg, .png, .xlsx, .csv" required />
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="country_name">Country <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        @if($project_name == "wi_track")
                                        <input type="text" name="country_name" class="form-control" value="{{$country_name}}" readonly />
                                        @else
                                        <input type="text" id="country_name" name="country_name" class="form-control" placeholder="Please Enter Country Name" required />
                                        @endif
                                    </div>
                                </div>  

                                <!-- A Code: 26-12-2025 Start --> 
                                @if($project_name == "wi_track")
                                <input type="hidden" id="sales_order_number" name="sales_order_number">
                                @else                                                    
                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="sales_order_number">Sales Order Number <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="sales_order_number" name="sales_order_number" class="form-control" 
                                        placeholder="Please Enter Sales Order Number" required />
                                    </div>
                                </div>
                                @endif
                                
                                @if($project_name == "wi_track")
                                <input type="hidden" id="currency" name="currency">
                                @else  
                                <!-- A Code: 19-01-2026 Start --> 
                                <input type="hidden" id="currency" name="currency" value="USD">
                                <!-- A Code: 19-01-2026 End -->                                 
                                @endif
                                <!-- A Code: 26-12-2025 End -->                               

                                <div class="row mt-3 mx-4 project_table_section">
                                    <table class="table table-hover table-bordered w-100 text-center" id="quotation-items">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="project_table_heading p-1">SR NO.</th>
                                                <th scope="col" class="project_table_heading p-1">ARTICLE NUMBER</th>
                                                <th scope="col" class="project_table_heading p-1">DESCRIPTION</th>
                                                <th scope="col" class="project_table_heading p-1">QTY</th>
                                                <th scope="col" class="project_table_heading p-1">PRODUCT NAME</th>
                                                <th scope="col" class="project_table_heading p-1">PRODUCT TYPE</th>
                                                <th scope="col" class="project_table_heading p-1">BOM</th>
                                                <th scope="col" class="project_table_heading p-1">DRAWING</th>
                                                <th scope="col" class="project_table_heading p-1">PARTIAL DELIVERY</th>
                                                <th scope="col" class="project_table_heading p-1">UNIT PRICE</th>
                                                <th scope="col" class="project_table_heading p-1">TOTAL PRICE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                                <td class="p-1"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-2">
                                    <button type="submit" class="btn btn-lg">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<input type="hidden" value="{{$project_name}}" name="project_name">
@endsection

@section('scripts')
    <script>
        var getQuotationItemsUrl = "{{ route('GetQuotationItems') }}";
        var getBOMUrl = "{{ route('getBOM') }}";
        var productTypes = @json($product_type->pluck('project_type_name')->filter()->values() ?? []);
        console.log('Product Types:', productTypes);
    </script>

    <script>
        $(document).on('click', '.download_excel_bom', function () {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
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

                success: function (response) {
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

                                    csvContent += `"${item.item_description}","${item.wilo_artilce_no}", ,"${item.unit_price}","${item.qty}","${item.total_price}"\n`;
                                    }
                                });
                            }
                        }

                        if (response.data.atmosBOMitemsSupervisor) {
                            let cart = response.data.atmosCart;
                            if (cart.is_accesories_manual != "1") {
                                let supervisor = response.data.atmosBOMitemsSupervisor;
                                csvContent += `${supervisor.item_description},${supervisor.wilo_artilce_no}, ,${supervisor.unit_price},${supervisor.qty},${supervisor.total_price}\n`;
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

                                csvContent += `${itemDesc}, -- , ,${cart.accesories_price},1,${cart.accesories_price}\n`;
                            }
                        }

                        if (response.data.atmosCart) {
                            let cart = response.data.atmosCart;
                            if (cart.is_accesories_manual == "1") {
                                csvContent += "\nAtmos Cart Details:\n";
                                csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                                csvContent += `Accessories-Manual, -- , ,${cart.accesories_price},1,${cart.accesories_price}\n`;
                            }
                        }

                        if (response.data.atmosCart) {
                            let cart = response.data.atmosCart;
                            if (cart.is_accesories_manual == "1") {
                                csvContent += "\nAtmos Cart Details:\n";
                                csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                                csvContent += `${cart.pump_name},${cart.full_article_number}, ,${cart.price},${cart.qty},${cart.total_price}\n`;
                            }
                        }

                        let encodedUri = encodeURI(csvContent);
                        let link = document.createElement("a");
                        link.setAttribute("href", encodedUri);
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
                                csvContent += `${item.item_description},${item.wilo_artilce_no}, ,${item.unit_price},${item.qty},${item.total_price}\n`;
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

                            csvContent += `${response.data.scp_master_motor_prices_item_desc}, ${response.data.scp_master_motor_prices_article_number} , ,${response.data.motor_price},1,${response.data.motor_price}\n`;
                        }

                        if (response.data) {
                            let cart = response.data.scpCart;
                            let article_number = response.data.article_number;
                            csvContent += "\nSCP Cart Details:\n";

                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            csvContent += `${cart.pump_name}, ${article_number} , ,${cart.bare_pump_price},1,${cart.bare_pump_price}\n`;
                        }

                        let encodedUri = encodeURI(csvContent);
                        let link = document.createElement("a");
                        link.setAttribute("href", encodedUri);
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
                                csvContent += `${item.item_description},${item.wilo_artilce_no}, ,${item.price},${item.qty},${item.total_price}\n`;
                            });
                        }

                        let encodedUri = encodeURI(csvContent);
                        let link = document.createElement("a");
                        link.setAttribute("href", encodedUri);
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
                            csvContent += `"${boosterCartData.model_no}","${boosterCartData.booster_article_number}", ,"${boosterCartData.pump_price}","${pump_qty}","${boosterCartData.pump_price * pump_qty}"\n`;
                        }

                        if (response.data.items && Array.isArray(response.data.items)) {
                            let cart = response.data.items;
                            csvContent += "\nItems:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            response.data.items.forEach(item => {
                                csvContent += `"${item.item_description}","${item.wilo_artilce_no}", ,"${item.price}","${item.qty}","${item.price * item.qty}"\n`;
                            });
                        }

                        if (response.data.cpBoosterItems && Array.isArray(response.data.cpBoosterItems)) {
                            let cart = response.data.cpBoosterItems;
                            csvContent += "\nControl Panel Items:\n";
                            csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
                            response.data.cpBoosterItems.forEach(item => {
                                csvContent += `"${item.item_description}","${item.wilo_artilce_no}", ,"${item.price}","${item.qty}","${item.total_price}"\n`;
                            });
                        }

                        let encodedUri = encodeURI(csvContent);
                        let link = document.createElement("a");
                        link.setAttribute("href", encodedUri);
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
                        link.setAttribute("download", `BOM_${quotation_number}_${item_name}_${full_article_number}.csv`);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>

    <script>
        $(".assembly_quotation_ref").on('blur keypress', function() {
            var quotation_number = $(this).val();
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var tableBody = $(".project_table_section tbody");
            tableBody.empty();
            $("form input[name^='quotation_items']").remove();

            // update Total Price
            $(document).on('input', '.editable-qty, .edit_qty, .edit_unit_price', function() {
                var $row = $(this).closest('tr');
                var index = $(this).data('index');

                // Get QTY
                var qty = parseFloat($row.find('.editable-qty').val() || $row.find('.edit_qty').val()) || 0;

                // Get UNIT PRICE
                var unitPrice = parseFloat($row.find('.edit_unit_price').val() || $row.find('.editunitprice').val() || $row.find('td').eq(9).text()) || 0;

                // Calculate TOTAL
                var totalPrice = (qty * unitPrice).toFixed(2);

                // Show live total in visible input/cell
                if ($row.find('.edit_total_price').length) {
                    $row.find('.edit_total_price').val(totalPrice);
                } else {
                    $row.find('td').eq(10).text(totalPrice);
                }

                // Update hidden total and unit price fields
                $(`.hidden-total_price-${index}`).val(totalPrice);
                $(`.hidden-unit_price-${index}`).val(unitPrice);
            });

            $.ajax({
                url: getQuotationItemsUrl,
                type: "POST",
                data: {
                    quotation_number: quotation_number
                },
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
                success: function(response) {
                    var tableBody = $(".project_table_section tbody");
                    tableBody.empty();
                    // When we are having data for table
                    if (response.data && response.data.length > 0) {
                        $("#customer_name").val(response.data[0].customer_name || '');
                        $("#country_name").val(response.data[0].country || '');
                        response.data.forEach(function(item, index) {
                            // CHANGE START: Build productTypeOptions dynamically using productTypes array
                            var productTypeOptions = `<option value="" disabled ${!item.product_type ? 'selected' : ''}>Select product type</option>`;
                            productTypes.forEach(function(type) {
                                productTypeOptions += `<option value="${type}" ${item.product_type === type ? 'selected' : ''}>${type}</option>`;
                            });
                            // CHANGE END

                            var row = `
                                <tr>
                                    <td class="p-1">${item.sr_no}</td>
                                    <td class="p-1">${item.full_article_number}</td>
                                    <td class="p-1">
                                        <input type="text" name="quotation_items[${index}][description]" value="${item.description}" 
                                        class="form-control editable-description w-300" data-index="${index}" required>
                                    </td>
                                    <td class="p-1">
                                        <input type="number" name="quotation_items[${index}][qty]" value="${item.qty}" 
                                        class="form-control editable-qty" data-index="${index}" min="1">
                                    </td>
                                    <td class="cart_model_name" name="cart_model_name">${item.product_type}</td>
                                    <td class="p-1">
                                        <select class="form-control select2 product-type-dropdown" name="quotation_items[${index}][product_type]" 
                                        data-index="${index}" required>
                                            ${productTypeOptions}
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <a href="javascript:void(0);" class="download_excel_bom" data-item_id="${item.item_id}" 
                                        data-quotation_number="" data-article_number="${item.full_article_number}" title="BOM" data-item_name="${item.product_type}">
                                            <i class="p-2 m-1 fa fa-file project_icon"></i>
                                        </a>
                                    </td>
                                    <td class="p-1"> 
                                        <input type="checkbox" class="download_excel_drawing-checkbox" data-index="${index}">
                                    </td>
                                    <td class="p-1">
                                        <input type="checkbox" class="partial-delivery-checkbox" data-index="${index}">
                                    </td>

                                    <td class="p-1">
                                        ${item.unit_price}
                                    </td>

                                    <td class="p-1">
                                        ${item.total_price}
                                    </td>

                                    <td class="d-none"> 
                                        <input type="hidden" class="quotation_from_pricing_tool" data-index="${index}" value="1">
                                    </td>
                                </tr>
                            `;

                            tableBody.append(row);
                            var hiddenFields = `
                                <input type="hidden" name="quotation_items[${index}][sr_no]" value="${item.sr_no}">
                                <input type="hidden" name="quotation_items[${index}][full_article_number]" value="${item.full_article_number}">
                                <input type="hidden" name="quotation_items[${index}][description]" value="${item.description}" class="hidden-description-${index}">
                                <input type="hidden" name="quotation_items[${index}][item_id]" value="${item.item_id}">
                                <input type="hidden" name="quotation_items[${index}][qty]" value="${item.qty}" class="hidden-qty-${index}">
                                <input type="hidden" name="quotation_items[${index}][cart_model_name]" value="${item.product_type}" class="hidden-cart_model_name-${index}">
                                <input type="hidden" name="quotation_items[${index}][product_type]" value="${item.product_type}" class="hidden-product-type-${index}">
                                <input type="hidden" name="quotation_items[${index}][download_excel_bom]" value="3" class="hidden-download_excel_bom-checkbox-${index}">
                                <input type="hidden" name="quotation_items[${index}][download_excel_drawing]" value="0" class="hidden-download_excel_drawing-checkbox-${index}">
                                <input type="hidden" name="quotation_items[${index}][partial_delivery]" value="1" class="hidden-partial-delivery-${index}">

                                <input type="hidden" name="quotation_items[${index}][unit_price]"  class=hidden-unit_price-${index}" value="${item.unit_price}">
                                <input type="hidden" name="quotation_items[${index}][total_price]" class="hidden-total_price-${index}" value="${item.total_price}">

                                <input type="hidden" name="quotation_items[${index}][quotation_from_pricing_tool]" value="1" class="quotation_from_pricing_tool-${index}">
                            `;

                            $("form").append(hiddenFields);

                            // Update hidden description when editable description changes
                            $(document).on('input', '.editable-description', function() {
                                var index = $(this).data('index');
                                var newValue = $(this).val();
                                $(`.hidden-description-${index}`).val(newValue);
                            });

                            // Existing event handlers
                            $(document).on('input', '.editable-qty', function() {
                                var index = $(this).data('index');
                                var newValue = $(this).val();
                                $(`.hidden-qty-${index}`).val(newValue);
                            });
                            $(document).on('input', '.editable-qty', function() {
                                var $row = $(this).closest('tr');
                                var qty = parseFloat($(this).val()) || 0;
                                // Try input field first, fallback to text if not found
                                var unitPrice = parseFloat($row.find('.editunitprice').val()) || parseFloat($row.find('td').eq(9).text()) || 0;
                                var totalPrice = (qty * unitPrice).toFixed(2);
                                $(`.hidden-total_price-${$(this).data('index')}`).val(totalPrice);

                                // Update both input (if exists) and cell
                                $row.find('.edittotalprice').val(totalPrice); // if there is a hidden/input
                                $row.find('td').eq(10).text(totalPrice); // if cell is text (10th col)
                            });

                            var $dropdown = $(`select[name="quotation_items[${index}][product_type]"]`);
                            $(`.hidden-product-type-${index}`).val($dropdown.val());

                            $dropdown.on("change", function() {
                                var selectedIndex = $(this).data("index");
                                var selectedValue = $(this).val();
                                $(`.hidden-product-type-${selectedIndex}`).val(selectedValue);
                            });

                            $(document).on('change', '.partial-delivery-checkbox', function() {
                                var index = $(this).data('index');
                                var isChecked = $(this).is(':checked');
                                $(`.hidden-partial-delivery-${index}`).val(isChecked ? 2 : 1);
                            });

                            $(document).on('change', '.download_excel_drawing-checkbox', function() {
                                var index = $(this).data('index');
                                var isChecked = $(this).is(':checked');
                                $(`.hidden-download_excel_drawing-checkbox-${index}`).val(isChecked ? 1 : 0);
                            });
                        });
                        $(".project_table_section").css('display', 'block');
                        $(".add-new-row").addClass('d-none');
                    } else {
                        // Rest of the success function remains unchanged

                        let isMandatoryRowAdded = false;
                        let rowIndex = 0;
                        $(".project_table_section").css('display', 'block');
                        if (!isMandatoryRowAdded) {
                            addRow(true, false); // Add mandatory row
                            isMandatoryRowAdded = true; // Set flag to prevent further additions
                        }

                        // Function to add a row (mandatory or optional)
                        function addRow(isMandatory = false, removeBtn = false) {
                            console.log(removeBtn);
                            // CHANGE START: Build productTypeOptions dynamically using productTypes array for manual rows
                            var productTypeOptions = '<option value="">Select product type</option>';
                            productTypes.forEach(function(type) {
                                productTypeOptions += `<option value="${type}">${type}</option>`;
                            });
                            // CHANGE END                            

                            // A Code: 09-01-2026 Start
                            var rowHtml = `
                                <tr class="quotation-row" data-index="${rowIndex}">
                                    <td class="p-1">
                                        <div class="input-group">
                                            <input type="text" class="form-control edit_sr_no" 
                                            name="quotation_items[${rowIndex}][sr_no]" 
                                            placeholder="Enter SR No" 
                                            data-index="${rowIndex}" ${isMandatory ? 'required' : ''}>
                                            
                                            ${!removeBtn ? '' : '<button type="button" class="btn btn-danger remove-row" data-index="' + rowIndex + '"><i class="fa fa-trash"></i></button>'}
                                        
                                        </div>
                                    </td>
                                    <td class="p-1"><input type="text" class="form-control edit_article_number" name="quotation_items[${rowIndex}][full_article_number]" placeholder="Enter Full Article Number" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}></td>

                                    <td class="p-1"><input type="text" class="form-control edit_desc w-300" name="quotation_items[${rowIndex}][description]" placeholder="Enter Description" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}></td>

                                    <td class="p-1"><input type="number" class="form-control edit_qty" name="quotation_items[${rowIndex}][qty]" placeholder="Enter Quantity" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}  step="1" min="1"></td>

                                    <td class="p-1">
                                        <select class="form-control select2 edit_product_type_manual trx_addons_attrib_" name="quotation_items[${rowIndex}][cart_model_name]" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}>
                                            <option value="">Select Item Type</option>
                                            <option value="Atmos">Atmos</option>
                                            <option value="Booster">Booster</option>
                                            <option value="SCP">SCP</option>
                                            <option value="Control Panel">Control Panel</option>
                                            <option value="Fire-Fighting">Fire-Fighting</option>
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <select class="form-control select2 product-type-dropdown edit_product_type trx_addons_attrib_" name="quotation_items[${rowIndex}][product_type]" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}>
                                            ${productTypeOptions}  <!-- Updated to use dynamic options -->
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <input type="checkbox" class="download_excel_bom-checkbox" data-index="${rowIndex}" 
                                        name="quotation_items[${rowIndex}][download_excel_bom]" checked disabled>
                                    </td>
                                    <td class="p-1">
                                        <input type="checkbox" class="download_excel_drawing-checkbox" data-index="${rowIndex}"  
                                        name="quotation_items[${rowIndex}][download_excel_drawing]">
                                    </td>
                                    <td class="p-1">
                                        <input type="checkbox" class="partial-delivery-checkbox" data-index="${rowIndex}" name="quotation_items[${rowIndex}][partial_delivery]">
                                    </td>

                                    <td class="p-1">
                                        <input type="number" class="form-control edit_unit_price" name="quotation_items[${rowIndex}][unit_price]" placeholder="Enter Unit Price" data-index="${rowIndex}" ${isMandatory ? 'required' : ''} min="1" step="0.01">
                                    </td>

                                    <td class="p-1">
                                        <input type="number" class="form-control edit_total_price" name="quotation_items[${rowIndex}][total_price]" placeholder="Enter total Price" data-index="${rowIndex}" ${isMandatory ? 'required' : ''} min="1" step="0.01">
                                    </td>

                                    <td class="d-none"> 
                                        <input type="hidden" class="quotation_from_pricing_tool" data-index="${rowIndex}" value="0" name="quotation_items[${rowIndex}][quotation_from_pricing_tool]">
                                    </td>
                                </tr>
                            `;
                            // A Code: 09-01-2026 End

                            tableBody.append(rowHtml);

                            // A Code: 09-01-2026 Start
                            var hiddenFields = `
                                <input type="hidden" name="quotation_items[${rowIndex}][sr_no]" value="" class="hidden-sr_no-${rowIndex}" ${isMandatory ? 'required' : ''}>
                                <input type="hidden" name="quotation_items[${rowIndex}][full_article_number]" value="" class="hidden-full_article_number-${rowIndex}" ${isMandatory ? 'required' : ''}>
                                <input type="hidden" name="quotation_items[${rowIndex}][description]" value="" class="hidden-description-${rowIndex}" ${isMandatory ? 'required' : ''}>
                                <input type="hidden" name="quotation_items[${rowIndex}][qty]" value="0" class="hidden-qty-${rowIndex}" ${isMandatory ? 'required' : ''}>
                                <input type="hidden" name="quotation_items[${rowIndex}][cart_model_name]" value="" class="hidden-cart_model_name-${rowIndex}" ${isMandatory ? 'required' : ''}>
                                <input type="hidden" name="quotation_items[${rowIndex}][product_type]" value="" class="hidden-product-type-${rowIndex}" ${isMandatory ? 'required' : ''}>
                                <input type="hidden" name="quotation_items[${rowIndex}][download_excel_bom]" value="1" class="hidden-download_excel_bom-checkbox-${rowIndex}">
                                <input type="hidden" name="quotation_items[${rowIndex}][download_excel_drawing]" value="0" class="hidden-download_excel_drawing-checkbox-${rowIndex}">
                                <input type="hidden" name="quotation_items[${rowIndex}][partial_delivery]" value="1" class="hidden-partial-delivery-${rowIndex}">
                                <input type="hidden" name="quotation_items[${rowIndex}][item_id]" value="1" class="hidden-item_id-${rowIndex}">
                                <input type="hidden" name="quotation_items[${rowIndex}][quotation_from_pricing_tool]" value="0" class="quotation_from_pricing_tool-${rowIndex}">
                            `;
                            // A Code: 09-01-2026 End

                            $("form").append(hiddenFields);

                            // Event handlers for the row
                            $(document).on('input', '.edit_sr_no', function() {
                                var index = $(this).data('index');
                                var newValue = $(this).val();
                                $(`.hidden-sr_no-${index}`).val(newValue);
                            });

                            $(document).on('input', '.edit_article_number', function() {
                                var index = $(this).data('index');
                                var newValue = $(this).val();
                                $(`.hidden-full_article_number-${index}`).val(newValue);
                            });

                            $(document).on('input', '.edit_desc', function() {
                                var index = $(this).data('index');
                                var newValue = $(this).val();
                                $(`.hidden-description-${index}`).val(newValue);
                            });

                            $(document).on('input', '.edit_qty', function() {
                                var index = $(this).data('index');
                                var newValue = $(this).val();
                                $(`.hidden-qty-${index}`).val(newValue);
                            });

                            var $dropdownCart = $(`select[name="quotation_items[${rowIndex}][cart_model_name]"]`);

                            $(`.hidden-cart_model_name-${rowIndex}`).val($dropdownCart.val() || '');
                            $dropdownCart.on("change", function() {
                                var selectedIndex = $(this).data("index");
                                var selectedValue = $(this).val();
                                $(`.hidden-cart_model_name-${selectedIndex}`).val(selectedValue || '');
                            });

                            var $dropdownProduct = $(`select[name="quotation_items[${rowIndex}][product_type]"]`);

                            $(`.hidden-product-type-${rowIndex}`).val($dropdownProduct.val() || ''); // Note: Fixed typo from rowIndex to rowRowIndex? No, it's rowIndex.
                            $dropdownProduct.on("change", function() {
                                var selectedIndex = $(this).data("index");
                                var selectedValue = $(this).val();
                                $(`.hidden-product-type-${selectedIndex}`).val(selectedValue || '');
                            });

                            $(document).on('change', '.download_excel_bom-checkbox', function() {
                                var index = $(this).data('index');
                                var isChecked = $(this).is(':checked');
                                $(`.hidden-download_excel_bom-checkbox-${index}`).val(isChecked ? 1 : 0);
                            });

                            $(document).on('change', '.download_excel_drawing-checkbox', function() {
                                var index = $(this).data('index');
                                var isChecked = $(this).is(':checked');
                                $(`.hidden-download_excel_drawing-checkbox-${index}`).val(isChecked ? 1 : 0);
                            });

                            $(document).on('change', '.partial-delivery-checkbox', function() {
                                var index = $(this).data('index');
                                var isChecked = $(this).is(':checked');
                                $(`.hidden-partial-delivery-${index}`).val(isChecked ? 2 : 1);
                            });

                            rowIndex++;
                        }

                        if ($(".add-new-row").hasClass("d-none")) {
                            $(".add-new-row").removeClass("d-none");
                        }

                        // Add the "Add Row" button
                        if ($('.add-new-row').length === 0) {
                            tableBody.after('<div class="mt-3 px-1"><button type="button" class="btn btn-primary mb-3 add-new-row d-none">Add Row</button></div>');
                        }

                        // Handle adding new optional rows                       
                        $(".add-new-row").off('click').on('click', function() {
                            addRow(true, true);
                        });

                        // Handle removing optional rows
                        $(document).on('click', '.remove-row', function() {
                            var index = $(this).data('index');
                            $(`tr[data-index="${index}"]`).remove();
                            $(`.hidden-sr_no-${index}, 
                        .hidden-full_article_number-${index}, 
                        .hidden-description-${index}, .hidden-qty-${index}, 
                        .hidden-cart_model_name-${index}, 
                        .hidden-product-type-${index}, 
                        .hidden-download_excel_bom-checkbox-${index}, 
                        .hidden-download_excel_drawing-checkbox-${index}, 
                        .hidden-partial-delivery-${index}, 

                        .hidden-total_price-${index}, 
                        .hidden-unit_price-${index}, 

                        .hidden-item_id-${index}, .quotation_from_pricing_tool-${index}`).remove();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>

    <script src="{{ asset('js/create_project.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
@endsection