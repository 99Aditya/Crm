$(".admin-toastr").trigger("click");

function toastr_alert(heading, msg, type) {
    new PNotify({
        title: heading,
        text: msg,
        icon: "icofont icofont-info-circle",
        type: type,
    });
}

function showError(elementId, validationId, message) {
    $(`#${elementId}`).addClass('is-invalid');
    $(`#${validationId}`).text(message).addClass('error-message text-danger').show();
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.error-message').hide().text('');
}

$('body').on('click', '#addContactBtn', function() {
    $('#name').val('');
    $('#email').val('');
    $('#phone').val('');
    $(`input[name="gender"]`).prop('checked', false);
    $('#profile_image').val('');
    $('#additional_file').val('');
    $('#customFieldContainer input').val('');
    $('#searchName').val('');
    $('#searchEmail').val('');
    $('#filterGender').val('');
    $('#contactModal').modal('show');
});

let fieldIndex = 0;
 $('#addCustomField').on('click', function() {
        fieldIndex++;
        $('#newCustomFieldContainer').append(`
            <div class="row mb-2 custom-field-item" data-index="${fieldIndex}">
                <div class="col-md-5">
                    <input type="text" name="custom_fields[${fieldIndex}][label]" class="form-control" placeholder="Field Label" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-custom-field">Remove</button>
                </div>
            </div>
        `);
    });

$('body').on('click', '.remove-custom-field', function() {
    $(this).closest('.custom-field-item').remove();
});

$('#saveCustomFields').on('click', function () {
    let fields = [];
    $('#newCustomFieldContainer input').each(function () {
        let name = $(this).val().trim();
        if (name !== '') {
            fields.push({ field_name: name, field_type: 'text' });
        }
    });
    if (fields.length === 0) {
        alert('Please add at least one custom field.');
        return;
    }
    $.ajax({
        url: customFieldStoreUrl,
        type: "POST",
        data: {
            _token: csrfToken,
            fields: fields
        },
        success: function (res) {
            // Clear temp fields
            $('#newCustomFieldContainer').html('');
            // Append new fields to main section
            if (res.fields && Array.isArray(res.fields)) {
                res.fields.forEach(f => {
                    $('#customFieldContainer').append(`
                        <div class="form-group mb-2">
                            <label>${f.field_name}</label>
                            <input type="text" name="custom_fields[${f.id}]" class="form-control">
                        </div>
                    `);
                });
            }
            toastr_alert('Success', 'Custom fields added successfully!', 'success');
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            toastr_alert('Error', 'Failed to add custom fields.', 'error');
        }
    });
});


$('body').on('submit', '#contactForm', function (e) {
    e.preventDefault();
    let name = $('#name').val().trim();
    let email = $('#email').val().trim();
    let phone = $('#phone').val().trim();
    let gender = $('input[name="gender"]:checked').val();
    let contactId = $("#contact_id").val();
    
    clearErrors();
    let isValid = true;

    if(name === '') {
        showError('name', 'nameError','Please enter a name');
        isValid = false;
    }
    if(email === '') {
        showError('email', 'emailError','Please enter an email');
        isValid = false;
    } else {
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            showError('email', 'emailError','Please enter a valid email');
            isValid = false;
        }
    }
    if(phone === '') {
        showError('phone', 'phoneError','Please enter a phone number');
        isValid = false;  
    }
    if(!gender) {
        showError('genderMale', 'genderError','Please select a gender');
        isValid = false;  
    }

    if (!isValid) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: `/contacts/check-email`,
        data: { email: email, id: contactId, _token: csrfToken},
        success: function(response) {
            if (response.exists) {
                showError('email', 'emailError', 'This email is already taken.');
            } else {
                saveContact(contactId);
            }
        }
    });
});

function saveContact(contactId) {
    var formData = new FormData($('#contactForm')[0]);
    $.ajax({
        type: 'POST',
        url: store,
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            $('#contactForm')[0].reset();
            $('#contactModal').modal('hide');
            if (contactId) {
                toastr_alert('Success', 'Contact updated successfully!', 'success');
            } else {
                toastr_alert('Success', 'Contact added successfully!', 'success');
            }
            loadContacts();

            location.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            toastr_alert('Error', 'Failed to save contact.', 'error');
        }
    });
}


$(document).on('click', '.editContact', function () {
    let id = $(this).closest('tr').data('id');
    $('#searchName').val('');
    $('#searchEmail').val('');
    $('#filterGender').val('');
    $.ajax({
        url: `/edit-contact/${id}`,
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            

            if (!res || !res.contact) {
                toastr_alert('Error', 'Invalid contact data received.', 'error');
                return;
            }
            

            $('#contactForm')[0].reset();
            $('#contact_id').val(res.contact.id);

            $('#name').val(res.contact.name);
            $('#email').val(res.contact.email);
            $('#phone').val(res.contact.phone);
            $(`input[name="gender"][value="${res.contact.gender}"]`).prop('checked', true);

            $('#customFieldContainer input').val('');

            if (res.customValues) {
                $.each(res.customValues, function (fieldId, value) {
                    $(`input[name="custom_fields[${fieldId}]"]`).val(value);
                });
            }

        
            $('#profileImagePreviewContainer').hide();
            $('#profileImagePreview').attr('src', '');

            $('#additionalFilePreviewContainer').hide();
            $('#additionalFilePreview').attr('href', '#').text('');

            if (res.contact.profile_image_url) {
                $('#profileImagePreview').attr('src', res.contact.profile_image_url);
                $('#profileImagePreviewContainer').show();
            }

            if (res.contact.additional_file_url) {
                const filename = res.contact.additional_file_name || res.contact.additional_file_url.split('/').pop();
                $('#additionalFilePreview').attr('href', res.contact.additional_file_url).text(filename);
                $('#additionalFilePreviewContainer').show();
            }

            $('#contactModal').modal('show');
        },
       
        error: function (xhr) {
            console.error(xhr.responseText);
            toastr_alert('Error', 'Failed to load contact details.', 'error');
        }
    });
});


$(document).on('click', '.deleteContact', function(){
    let id = $(this).closest('tr').data('id');
    if(confirm('Are you sure?')) {
        $.ajax({
            type: 'GET',
            url: `/delete-contact/${id}`,
            success: res => { loadContacts(); }
        });
    }
});

$(document).on('click', '.mergeContact', function () {
    let primaryId = $(this).data('id');
    $('#primaryContactId').val(primaryId);
    $('#searchName').val('');
    $('#searchEmail').val('');
    $('#filterGender').val('');
    $('#mergeModal').modal('show');
});

$('#confirmMergeBtn').on('click', function () {
    let primaryId = $('#primaryContactId').val();
    let secondaryId = $('#secondaryContactId').val();
  
    clearErrors();
    
    let isValid = true;

    if(primaryId === '') {
         showError('primaryContactId', 'primaryContactIdError','Please select a primary contact');
        isValid = false;
    }
    if (secondaryId === '') {
        showError('secondaryContactId','secondaryContactIdError','Please select a secondary contact');
        isValid = false;
    }else if (primaryId === secondaryId) {
        showError('secondaryContactId','secondaryContactIdError','Primary and secondary contacts must be different');
        isValid = false;
    }
    if (!isValid) {
        return;
    }
    
    if (!confirm("Are you sure you want to merge these contacts?")) {
        return; 
    }

    $.ajax({
        url: merge,
        type: "POST",
        data: {
            _token: csrfToken,
            primary_id: primaryId,
            secondary_id: secondaryId
        },
        success: function (res) {
            $('#mergeModal').modal('hide');
            toastr_alert('Success', 'Contacts merged successfully!', 'success');
            loadContacts();

        },
        error: function (xhr) {
            console.error(xhr.responseText);
            toastr_alert('Error', 'Failed to merge contacts.', 'error');
        }
    });
});


function loadContacts(filters = {}) {
    // console.log(filters);
    
    $.ajax({
        url: '/get-contacts',
        type: 'GET',
        data: filters,
        dataType: 'json',

        success: function (res) {
            let rows = '';

            if (res.contacts && res.contacts.length > 0) {
                $.each(res.contacts, function (index, contact) {
                    rows += `
                        <tr data-id="${contact.id}">
                            <td>${contact.name ?? ''}</td>
                            <td>${contact.email ?? ''}</td>
                            <td>${contact.phone ?? ''}</td>
                            <td>${contact.gender ? contact.gender.charAt(0).toUpperCase() + contact.gender.slice(1) : ''}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning editContact me-1">Edit</button>
                                <button class="btn btn-sm btn-outline-danger deleteContact">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                rows = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i> No contacts available.
                        </td>
                    </tr>
                `;
            }

            $('#contactTableBody').html(rows);
        },

        error: function (xhr) {
            console.error(xhr.responseText);
            $('#contactTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        Failed to load contacts. Please try again.
                    </td>
                </tr>
            `);
        }
    });
}

$('#searchName, #searchEmail, #filterGender').on('input change', function () {
    let filters = {
        name: $('#searchName').val(),
        email: $('#searchEmail').val(),
        gender: $('#filterGender').val(),
    };
    loadContacts(filters);
});