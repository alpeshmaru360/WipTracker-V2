$(document).ready(function () {
    $('.select2').select2();
    $(".select2-search__field").css('display', 'none');
    $(".select2-selection__choice__remove select2-selection__rendered").css('display', 'none');
    $(".select2 button").css('display', 'none');
    $(".project_table_section").css('display', 'none');
})

$('.select2').on('click', function () {
    $(".select2-search__field").css('display', 'block');
});

$("form").on('submit', function (e) {
    var tableBody = $(".project_table_section tbody");
    var rowCount = tableBody.find('tr').length;

    // If no rows exist, add a mandatory row
    if (rowCount === 0) {
        e.preventDefault(); // Prevent form submission
        addRow(true, false); // Add a mandatory row
        $('.select2').select2(); // Initialize Select2 for new row
        alert("At least one row is required in the table. A mandatory row has been added. Please fill it out and submit again.");
        return false;
    }
    // Optional: Validate that all required fields in the rows are filled
    var isValid = true;
    tableBody.find('tr').each(function () {
        var row = $(this);
        var requiredInputs = row.find('input[required], select[required]');
        requiredInputs.each(function () {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid'); // Highlight empty required fields
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    if (!isValid) {
        e.preventDefault();
        alert("Please fill all required fields in the table before submitting.");
        return false;
    }
    // If rows exist and are valid, allow form submission
    return true;
});