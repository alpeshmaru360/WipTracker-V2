@extends('layouts.main')
@section('content')

<link href="{{ asset('css/operator.css') }}" rel="stylesheet" />

<div class="operator_dashboard_page main_section bg-white my-4 mx-0 mx-md-3 dashboard_heading">
    <div class="row">
        <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6 col-10"></div>
        <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6 col-2">
            <div class="float-right">
                <button id="cameraButton" onclick="openCamera()" class="mr-4 mt-3 project_check_status">
                    <i class="p-1 m-1 fa fa-camera project_view_icon"></i>
                </button>
                <div id="qrReader" style="width: 250px; height: 250px; display: none;"></div>
                <p id="qrCodeResult"> <span id="resultText"></span></p>
            </div>
        </div>
    </div>
    <div class="row"></div>
    <div class="mx-3 mx-md-5">
        <div class=" table-responsive mt-3">
            <table class="table table-hover table-bordered w-100 text-center" id="project_table_high_priority">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Date</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Assigned Qty</th>
                        <th scope="col" class="project_table_heading p-1">Estimated Readiness</th>
                        <th scope="col" class="project_table_heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($product as $val)
                    <tr>
                        <td>{{ $val->created_at->format('d-M-Y') }}</td>
                        <td>{{ $val->projects ? $val->projects['project_no'] : 'N/A' }}</td>
                        <td>{{ $val->projects ? $val->projects['project_name'] : 'N/A' }}</td>
                        <td>{{ $val->projects ? $val->product['full_article_number'] : 'N/A' }}</td>
                        <td>{{ $val->projects ? $val->product['description'] : 'N/A' }}</td>                        
                        <td>{{ $val->qty }}</td>
                        <td>{{ $val->projects && $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-M-Y') : 'N/A' }}</td>
                        {{--<td>
                            <a class="project_check_status pt-1 pb-1 ml-3 {{ !$val->mrf_date_auth ? 'disabled' : '' }}"
                            href="{{ route('OperatorProductType', ['product_id' => $val->product_id, 'redirect' => 1]) }}">
                                <i class="p-1 m-1 fa fa-eye project_view_icon"></i>
                            </a>
                        </td>--}}
                        <td>
                            <a class="project_check_status pt-1 pb-1 ml-3"
                            href="{{ route('OperatorProductType', ['product_id' => $val->product_id, 'redirect' => 1]) }}">
                                <i class="p-1 m-1 fa fa-eye project_view_icon"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>                
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#project_table_high_priority').DataTable({
            paging: true,
            pageLength: 5,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#project_table_high_priority').removeClass('dataTable');
    });
    $(document).ready(function() {
        $('#project_table_law_priority').DataTable({
            paging: true,
            pageLength: 5,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: false,
            ordering: false
        });
        $('#project_table_law_priority').removeClass('dataTable');
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
    let qrCodeReader;

    // Function to open the camera and start the QR code scanner
    function openCamera() {
        document.getElementById("qrReader").style.display = "block"; // Show the QR scanner area
        document.getElementById("cameraButton").style.display = "none";
        qrCodeReader = new Html5Qrcode("qrReader");
        qrCodeReader.start({
                facingMode: "environment"
            }, // Use the rear camera
            {
                fps: 10, // Scanning frequency
                qrbox: {
                    width: 250,
                    height: 250
                } // Set scan area
            },
            (decodedText) => {
                document.getElementById("resultText").innerText = decodedText; // Show result
                qrCodeReader.stop(); // Stop the scanner after successful scan
                document.getElementById("qrReader").style.display = "none"; // Hide scanner area

                // Redirect to the scanned URL
                if (isValidUrl(decodedText)) {
                    window.location.href = decodedText;
                } else {
                    alert("Scanned text is not a valid URL.");
                }
            },
            (errorMessage) => {
                console.log("Scanning error: ", errorMessage);
            }
        ).catch((err) => {
            console.error("Failed to start QR scanner: ", err);
        });
    }

    // Function to check if the scanned text is a valid URL
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
</script>
@endsection