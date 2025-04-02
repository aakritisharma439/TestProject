<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dynamic Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .remove-btn { background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .container { max-width: 800px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Dynamic Form</h2>
    <div id="error-container" class="alert alert-danger d-none"></div>
    <form id="dynamicForm" class="card p-4 shadow">
        <div id="formContainer">
            <div class="row g-2 align-items-center mb-3 form-row">
                <div class="col-md-3">
                    <input type="text" name="name[]" class="form-control" placeholder="Name" minlength="2" maxlength="20" oninput="validateName(this)" required>
                    <span class="error-message text-danger"></span>
                </div>
                <div class="col-md-3">
                    <input type="email" name="email[]" class="form-control" placeholder="Email" required>
                    <span class="error-message text-danger"></span>
                </div>
                <div class="col-md-2">
                    <input type="text" name="mobile[]" class="form-control" placeholder="Mobile No" minlength="10" maxlength="10" oninput="validateMobile(this)" required>
                    <span class="error-message text-danger"></span>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="gender[]" required>
                        <option value="" disabled selected>Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="error-message text-danger"></span>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-btn" onclick="removeRow(this)">X</button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" id="addMore" class="btn btn-primary">Add More</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </form>
    <div class="mt-4">
        <h4>Form Data</h4>
        <table class="table table-bordered" id="data">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
                @if ($data->isNotEmpty())
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ucfirst($item->name) }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->mobile }}</td>
                            <td>{{ $item->gender }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">No data found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {   //to add more fields on click over add more btn
        $('#addMore').click(function() {
            let rowHtml = `
                <div class="row g-2 align-items-center mb-3 form-row">
                    <div class="col-md-3">
                        <input type="text" name="name[]" class="form-control" placeholder="Name" minlength="2" maxlength="20" oninput="validateName(this)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email[]" class="form-control" placeholder="Email">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="mobile[]" class="form-control" placeholder="Mobile No" minlength="10" maxlength="10" oninput="validateMobile(this)" required>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="gender[]" required>
                            <option value="" disabled selected>Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-btn" onclick="removeRow(this)">X</button>
                    </div>
                </div>`;
            $('#formContainer').append(rowHtml);
        });

        $('#dynamicForm').submit(function(e) {  //to store the data using ajax
            e.preventDefault();
            let formData = new FormData(this);
            $('.error-message').remove();
            
            $.ajax({
                url: "{{ route('submit-form') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({ 
                            icon: "success", 
                            title: "Success", 
                            text: response.message 
                        }).then(() => {
                            $('#dynamicForm').trigger("reset");
                        });
                        updateTable(response.data);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(field, messages) {
                            let matches = field.match(/^(\w+)\.(\d+)$/);
                            if (matches) {
                                let fieldName = matches[1];
                                let index = matches[2];
                                let inputField = $(`[name="${fieldName}[]"]`).eq(index);
                                inputField.after(`<span class="error-message text-danger">${messages[0]}</span>`);
                            }
                        });
                    } else {
                        $("#error-container").removeClass("d-none").text(xhr.responseJSON.message || "Something went wrong!");
                    }
                }
            });
        });
    });
    
    function removeRow(button) {  //to remove the add more
        if ($('.form-row').length > 1) {
            $(button).closest('.form-row').remove();
        }
    }
    function validateMobile(input) {
            input.value = input.value.replace(/\D/g, ''); // it will allow only numeric values
    }
    function validateName(input) {
            $(input).val($(input).val().replace(/[^a-zA-Z\s]/g, '')); // it will allow only letters
    }
    
    function fetchLatestData() {  //this function will fetch the latest data after storing the data
        $.ajax({
            url: "{{ route('fetch-data') }}",
            type: "GET",
            success: function(response) {
                updateTable(response.data);
            }
        });
    }

    function updateTable(data) { //this function will update the table if new entry comes
        let tableBody = $("#data tbody");  
        tableBody.empty();  

    if (data.length === 0) {  
        tableBody.append(`
            <tr>
                <td colspan="4" class="text-center">No data found</td>
            </tr>
        `);  
    } else {  
        data.forEach(item => {  
            tableBody.prepend(`  
                <tr>  
                    <td>${item.name.charAt(0).toUpperCase() + item.name.slice(1)}</td>  
                    <td>${item.email}</td>  
                    <td>${item.mobile}</td>  
                    <td>${item.gender}</td>  
                </tr>  
            `);  
        });  
    }  
}

setInterval(fetchLatestData, 5000);
</script>
</body>
</html>