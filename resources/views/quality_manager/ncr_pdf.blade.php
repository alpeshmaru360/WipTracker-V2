<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCR Form</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .form-group label {
            font-weight: bold;
            display: inline-block;
            width: 40%;
            vertical-align: top;
            color: #34495e;
        }

        .form-group p {
            display: inline-block;
            width: 59%;
            margin: 0;
            vertical-align: top;
            padding: 5px;
            background: #ecf0f1;
            border-radius: 4px;
        }

        .signature-img,
        .ncr-photo {
            max-width: 150px;
            max-height: 75px;
            margin: 5px 0;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
        }

        .photo-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #d1e7dd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Non-Conformance Report (NCR)</h1>

        <table>
            <tr>
                <th>CIA No</th>
                <td>{{ $cia_no }}</td>
                <th>Related Dep</th>
                <td>{{ $related_dep }}</td>
            </tr>
            <tr>
                <th>Project</th>
                <td>{{ $project }}</td>
                <th>PO</th>
                <td>{{ $po }}</td>
            </tr>
        </table>

        <div class="form-group">
            <label>Material Description:</label>
            <p>{{ $material_description }}</p>
        </div>

        <div class="form-group">
            <label>NCR Description:</label>
            <p>{{ $ncr_description }}</p>
        </div>

        <table>
            <tr>
                <th>Article Number</th>
                <td>{{ $article_number }}</td>
                <th>Quantity</th>
                <td>{{ $quantity }}</td>
            </tr>
        </table>

        <div class="form-group">
            <label>Name Surname:</label>
            <p>{{ $name_surname }}</p>
        </div>

        <div class="form-group">
            <label>Signature:</label>
            <div class="logo">
                <img src="file://{{ $signature }}" alt="Signature" style="width: 200px; height: auto;">
                <!-- <p>{{ $signature }}</p> -->
            </div>

            {{--
             <img src="{{asset('signatures/main/logo_1.png')}}">
            <p><img src="{{'/home/wiptracker/domains/wiptracker.360websitedemo.com/public_html/public/signatures/main/' . basename($signature)}}" alt="Signature" class="signature-img"></p>
            <p>{{ basename($signature) }}</p>
            --}}
        </div>

        <div class="form-group">
            <label>Department detected the nonconformity:</label>
            <p>{{ $detected_department }}</p>
        </div>

        <div class="form-group">
            <label>Activity Schedule Type:</label>
            <p>
                @foreach($activity_schedule_type as $type)
                {{ $type == 1 ? 'Corrective Action' : '' }}
                {{ $type == 2 ? 'Correction' : '' }}
                {{ $type == 3 ? 'Improvement Action' : '' }}
                @if(!$loop->last), @endif
                @endforeach
            </p>
        </div>

        <div class="form-group">
            <label>ROOT CAUSE:</label>
            <p>{{ $root_cause }}</p>
        </div>

        <div class="form-group">
            <label>ACTION TAKEN TO PREVENT MISUSE:</label>
            <p>{{ $action_to_prevent_misuse }}</p>
        </div>

        <div class="form-group">
            <label>Planned Action Date:</label>
            <p>{{ $planned_action_date }}</p>
        </div>

        <div class="form-group">
            <label>Related Authorized Personnel:</label>
            <p>{{ $related_authorized_personnel }}</p>
        </div>

        <div class="form-group">
            <label>Related Authorized Personnel Signature:</label>
            <div class="logo">
                <img src="file://{{ $related_authorized_personnel_signature }}" alt="Signature" style="width: 200px; height: auto;">
            </div>
            {{--
                <p><img src="{{ '/home/wiptracker/domains/wiptracker.360websitedemo.com/public_html/public/signatures/related/' . basename($related_authorized_personnel_signature) }}" alt="Signature" class="signature-img"></p>
            <p>{{ basename($related_authorized_personnel_signature) }}</p>
            --}}
        </div>

        <div class="form-group">
            <label>Quality Management Representative:</label>
            <p>{{ $quality_management_representative }}</p>
        </div>

        <div class="form-group">
            <label>Action Follow up:</label>
            <p>
                @if($is_nonconformity_corrected)
                Nonconformity is corrected
                @elseif($is_nonconformity_not_corrected)
                Nonconformity is not corrected
                @elseif($is_additional_time)
                Additional Time
                @else
                Not specified
                @endif
            </p>
        </div>

        <div class="form-group">
            <label>CORRECTIVE/PREVENTIVE ACTION:</label>
            <p>{{ $corrective_preventive_action }}</p>
        </div>

        <div class="form-group">
            <label>Follow up:</label>
            <p>{{ $follow_up }}</p>
        </div>

        <div class="form-group">
            <label>Action closed date:</label>
            <p>{{ $action_closed_date }}</p>
        </div>

        <div class="form-group">
            <label>Related Authorized Personnel (Final):</label>
            <p>{{ $related_authorized_personnel_final }}</p>
        </div>

        <div class="form-group">
            <label>Quality Management Representative/Date:</label>
            <p>{{ $quality_management_representative_date }}</p>
        </div>

        {{-- <div class="form-group">
            <label>NCR Photos:</label>
            <div class="photo-container">
                @foreach($ncr_photos as $photo)
                <img src="file://{{ $photo }}" alt="NCR Photo" class="ncr-photo" style="width: 200px; height: auto;">
                @endforeach
            </div>
        </div> --}}
    </div>
</body>

</html>