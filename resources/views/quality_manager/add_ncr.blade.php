@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<style type="text/css">
    .preview-image-container {
        position: relative;
        display: inline-block;
        margin: 5px;
    }

    .preview-image {
        max-width: 100px;
        max-height: 100px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }

    .remove-image {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 5px 8px;
        font-size: 12px;
        cursor: pointer;
    }
</style>
<section class="bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-9 col-lg-7 col-xl-12">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <form action="{{ route('ncr.generate') }}" enctype="multipart/form-data" method="post">
                                @csrf
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="cia_no">CIA No<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="cia_no" class="form-control" name="cia_no" required
                                            value="{{ $cia_no }}" readonly />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="ncr_type">Types of NCR<span
                                                class="text-danger">*</span></label>
                                        <select id="ncr_type" class="form-control" name="ncr_type" required>
                                            <option value="" disabled selected>Select Type of NCR</option>
                                            <option value="Improvement Internal">Improvement Internal</option>
                                            <option value="Internal Audit">Internal Audit</option>
                                            <option value="Customer Complains">Customer Complains</option>
                                            <option value="Non-Conformity-Internal">Non-Conformity-Internal</option>
                                            <option value="Supplier Non-Conformity">Supplier Non-Conformity</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="related_dep">Related Department <span
                                                class="text-danger">*</span></label>
                                        <select id="related_dep" class="form-control" name="related_dep" required>
                                            <option value="" disabled selected>Select Related Department</option>
                                            <option value="Quality">Quality</option>
                                            <option value="Engineering">Engineering</option>
                                            <option value="Production">Production</option>
                                            <option value="Customer">Customer</option>
                                        </select>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label" for="project">Project No<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="project_no" class="form-control" name="project_no"
                                            required placeholder="Enter Project No" />
                                    </div>

                                </div>
                                <div class="row mt-3">
                                    <div class="col-4">
                                        <label class="form-label" for="project">Project Name<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="project" class="form-control" name="project" required
                                            placeholder="Enter Project Name" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label" for="po">PO<span class="text-danger">*</span></label>
                                        <input type="text" id="po" class="form-control" name="po" required
                                            placeholder="PO-YYYY-XXXXX" pattern="PO-\d{4}-\d+"
                                            title="Format: PO-YYYY-XXXXX (X can be any number of digits)" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label" for="article_number">Article Number<span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="article_number" class="form-control"
                                            name="article_number" required placeholder="Enter Article Number" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="ncr_description">NCR Description<span
                                                class="text-danger">*</span></label>
                                        <textarea id="ncr_description" class="w-100 pb-2 pt-2" name="ncr_description"
                                            required placeholder="Enter NCR Description"></textarea>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label" for="material_description">Material Description<span
                                                class="text-danger">*</span></label>
                                        <textarea id="material_description" class="w-100 pb-2 pt-2"
                                            name="material_description" required
                                            placeholder="Enter Material Description"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="quantity">Quantity<span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="quantity" class="form-control" name="quantity" required
                                            placeholder="Enter Quantity" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="name_surname">Full name<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="name_surname" class="form-control" name="name_surname"
                                            required placeholder="Enter Full name" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="detected_department">
                                            The Department detected the nonconformity
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="detected_department" class="form-control" name="detected_department"
                                            required>
                                            <option value="" disabled selected>Select Department</option>
                                            <option value="Quality">Quality</option>
                                            <option value="Engineering">Engineering</option>
                                            <option value="Production">Production</option>
                                            <option value="Customer">Customer</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="signature">Signature (Image Upload)<span
                                                class="text-danger">*</span></label>
                                        <input type="file" id="signature" class="w-100 pb-2 pt-2" name="signature"
                                            accept="image/*" required onchange="previewSignature()" />
                                        <div class="mt-3">
                                            <img id="signature-preview" src="#" alt="Image Preview"
                                                style="display: none; max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label">Activity Schedule Type<span
                                                class="text-danger">*</span></label>
                                        <div class="d-flex justify-content-start flex-wrap">
                                            <div class="form-check me-4">
                                                <input class="form-check-input" type="checkbox"
                                                    name="activity_schedule_type[]" id="corrective_action" value="1">
                                                <label class="form-check-label" for="corrective_action">Corrective
                                                    Action</label>
                                            </div>
                                            <div class="form-check me-4">
                                                <input class="form-check-input" type="checkbox"
                                                    name="activity_schedule_type[]" id="correction" value="2">
                                                <label class="form-check-label" for="correction">Correction</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="activity_schedule_type[]" id="is_improvement_action"
                                                    value="3">
                                                <label class="form-check-label" for="is_improvement_action">Improvement
                                                    Action</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="planned_action_date">Planned Action Date<span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="planned_action_date" class="form-control"
                                            name="planned_action_date" required />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="action_to_prevent_misuse">ACTION TAKEN TO PREVENT
                                            MISUSE</label>
                                        <textarea id="action_to_prevent_misuse" class="form-control"
                                            name="action_to_prevent_misuse"
                                            placeholder="Enter Action Taken to Prevent Misuse"></textarea>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label" for="root_cause">ROOT CAUSE</label>
                                        <textarea id="root_cause" class="form-control" name="root_cause"
                                            placeholder="Enter Root Cause"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="related_authorized_personnel">Related Authorized
                                            Personnel</label>
                                        <input type="text" id="related_authorized_personnel" class="form-control"
                                            name="related_authorized_personnel"
                                            placeholder="Enter Related Authorized Personnel" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="related_authorized_personnel_signature">Related
                                            Authorized Personnel Signature</label>
                                        <input type="file" id="related_authorized_personnel_signature"
                                            class="w-100 pb-2 pt-2" name="related_authorized_personnel_signature"
                                            accept="image/*" onchange="previewRelatedAuthorizedPersonnelSignature()" />
                                        <div class="mt-3">
                                            <img id="related-authorized-personnel-signature-preview" src="#"
                                                alt="Image Preview"
                                                style="display: none; max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="quality_management_representative">Quality
                                            Management Representative</label>
                                        <input type="text" id="quality_management_representative" class="form-control"
                                            name="quality_management_representative"
                                            placeholder="Enter Quality Management Representative" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label"
                                            for="corrective_preventive_action">CORRECTIVE/PREVENTIVE ACTION</label>
                                        <textarea id="corrective_preventive_action" class="form-control"
                                            name="corrective_preventive_action"
                                            placeholder="Enter Corrective/Preventive Action"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">Action Follow up</label>
                                        <div class="d-flex justify-content-start flex-wrap">
                                            <div class="form-check me-4">
                                                <input type="radio" id="nonconformity_corrected" name="action_follow_up"
                                                    value="Nonconformity is corrected" class="form-check-input">
                                                <label class="form-check-label"
                                                    for="nonconformity_corrected">Nonconformity is corrected</label>
                                            </div>
                                            <div class="form-check me-4">
                                                <input type="radio" id="nonconformity_not_corrected"
                                                    name="action_follow_up" value="Nonconformity is not corrected"
                                                    class="form-check-input">
                                                <label class="form-check-label"
                                                    for="nonconformity_not_corrected">Nonconformity is not
                                                    corrected</label>
                                            </div>
                                            <div class="form-check me-4">
                                                <input type="radio" id="additional_time" name="action_follow_up"
                                                    value="Additional Time" class="form-check-input">
                                                <label class="form-check-label" for="additional_time">Additional
                                                    Time</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="follow_up">Follow up</label>
                                        <input type="text" id="follow_up" class="form-control" name="follow_up"
                                            placeholder="Enter Follow up" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="action_closed_date">Action closed date</label>
                                        <input type="date" id="action_closed_date" class="form-control"
                                            name="action_closed_date" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label class="form-label" for="related_authorized_personnel_final">Related
                                            Authorized Personnel</label>
                                        <input type="text" id="related_authorized_personnel_final" class="form-control"
                                            name="related_authorized_personnel_final"
                                            placeholder="Enter Related Authorized Personnel" />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="quality_management_representative_date">Quality
                                            Management Representative/Date</label>
                                        <input type="text" id="quality_management_representative_date"
                                            class="form-control" name="quality_management_representative_date"
                                            placeholder="Enter Quality Management Representative/Date" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label" for="ncr_photos">NCR Photos</label>
                                        <input type="file" id="ncr_photos" class="w-100 pb-2 pt-2" name="ncr_photos[]"
                                            accept="image/*" multiple onchange="previewNcrPhotos(this)" />
                                        <div id="ncr-photos-preview" class="mt-3 d-flex flex-wrap"></div>
                                    </div>
                                </div>
                                <!-- Submit Button -->
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-lg">Generate NCR</button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function previewSignature() {
        const fileInput = document.getElementById('signature');
        const preview = document.getElementById('signature-preview');
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    function previewRelatedAuthorizedPersonnelSignature() {
        const fileInput = document.getElementById('related_authorized_personnel_signature');
        const preview = document.getElementById('related-authorized-personnel-signature-preview');
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

    function previewNcrPhotos(input) {
        const preview = document.getElementById('ncr-photos-preview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'preview-image-container';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';

                    const deleteBtn = document.createElement('span');
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.className = 'remove-image';
                    deleteBtn.onclick = function () {
                        imgContainer.remove();
                        // Remove the file from the input
                        const dt = new DataTransfer();
                        const {
                            files
                        } = input;
                        for (let i = 0; i < files.length; i++) {
                            if (index !== i) dt.items.add(files[i]);
                        }
                        input.files = dt.files;
                    };

                    imgContainer.appendChild(img);
                    imgContainer.appendChild(deleteBtn);
                    preview.appendChild(imgContainer);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('form').on('submit', function (e) {
            e.preventDefault();

            // Validate PO format
            var poInput = $('#po');
            var poValue = poInput.val();
            var poPattern = /^PO-\d{4}-\d+$/;

            if (!poPattern.test(poValue)) {
                alert('Please enter a valid PO number in the format PO-YYYY-XXXXX (X can be any number of digits)');
                poInput.focus();
                return false;
            }

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        alert(response.message);

                        // Create an anchor tag and trigger download
                        var link = document.createElement('a');
                        link.href = response.pdf_url;
                        link.download = response.pdf_url.split('/').pop(); // Extract filename from URL
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Redirect to the NCR route after a short delay
                        setTimeout(function () {
                            window.location.href = response.redirect_url;
                        }, 2000); // Redirect after 2 seconds
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr) {
                    alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong!'));
                }
            });
        });
    });
</script>
<script>
    document.getElementById('project_no').addEventListener('input', function () {
        var projectNo = this.value;
        if (projectNo) {
            fetch(`/get-project-name?project_no=${projectNo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.project_name) {
                        document.getElementById('project').value = data.project_name;
                    } else {
                        document.getElementById('project').value = '';
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            document.getElementById('project').value = '';
        }
    });
</script>
<script>
    document.getElementById('project_no').addEventListener('input', function () {
        var projectNo = this.value;
        if (projectNo) {
            fetch(`/get-project-name?project_no=${projectNo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.project_name) {
                        document.getElementById('project').value = data.project_name;
                    } else {
                        document.getElementById('project').value = '';
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            document.getElementById('project').value = '';
        }
    });
</script>
@endsection