<div class="modal fade" id="contactModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contact Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="contactForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="contact_id" name="id" value="">

            <div class="form-group mb-2">
                <label>Name *</label>
                <input type="text" name="name" id="name" class="form-control" >
                <div id="nameError"></div>
            </div>

            <div class="form-group mb-2">
                <label>Email *</label>
                <input type="email" name="email" id="email" class="form-control">
                <div id="emailError"></div>
            </div>

            <div class="form-group mb-2">
                <label>Phone *</label>
                <input type="text" name="phone" id="phone" class="form-control">
                <div id="phoneError"></div>
            </div>

            <div class="form-group mb-2">
                <p>Gender *</p>                    
                <div class="form-radio">
                    <div class="radio radiofill radio-inline">
                        <label>
                            <input type="radio" name="gender" class="gender" value="male"> 

                            <i class="helper"></i>Male
                        </label>
                    </div>
                    <div class="radio radiofill radio-inline">
                        <label>
                        <input type="radio" name="gender" class="gender" value="female"> 
                            <i class="helper"></i>Female
                        </label>
                    </div>
                </div>
                <div id="genderError"></div>
            </div>

            <div class="form-group mb-2">
                <label>Profile Image</label>
                <input type="file" name="profile_image" class="form-control">
            </div>

            <div class="form-group mb-2">
                <label>Additional File</label>
                <input type="file" name="additional_file" class="form-control">
            </div>


            <!-- Preview containers for edit -->
                <div id="filePreviewContainer" class="row mb-3">

                    <!-- Profile Image Preview -->
                    <div id="profileImagePreviewContainer" class="col-md-6" style="display:none;">
                        <label class="d-block">Current Profile Image</label>
                        <img id="profileImagePreview" 
                            src="" 
                            alt="Profile Image" 
                            class="img-fluid rounded border" 
                            style="max-height:40px; object-fit:cover;">
                    </div>

                    <!-- Additional File Preview -->
                    <div id="additionalFilePreviewContainer" class="col-md-6" style="display:none;">
                        <label class="d-block">Attached File</label>
                        <a id="additionalFilePreview" 
                        href="#" 
                        target="_blank" 
                        class="d-inline-block text-truncate w-100" 
                        style="max-width:100%;">
                        View File
                        </a>
                    </div>

                </div>
            <hr>

            {{-- Dynamic Custom Field Section --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Custom Fields</h5>
                <button type="button" class="btn btn-primary btn-sm" id="addCustomField">
                    <i class="fa fa-plus"></i> Add More
                </button>
            </div>

            <div id="customFieldContainer">
                {{-- Existing custom fields loaded from DB --}}
                @foreach($customFields as $field)
                    <div class="form-group mb-2">
                        <label>{{ $field->field_name }}</label>
                        <input type="text" name="custom_fields[{{ $field->id }}]" class="form-control">
                    </div>
                @endforeach
            </div>

            {{-- Hidden section for creating new custom fields dynamically --}}
            <div class="mt-3">
                <div id="newCustomFieldContainer"></div>
                <button type="button" id="saveCustomFields" class="btn btn-success btn-sm mt-2">
                    Save Custom Fields
                </button>
            </div>

            <hr>
            <button type="submit" class="btn btn-success mt-3 w-100">Save Contact</button>
        </form>
      </div>
    </div>
  </div>
</div>
