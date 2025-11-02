<div class="modal fade" id="mergeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Merge Contacts</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Master Contact *</label>
          <select id="primaryContactId" class="form-control">
            <option value="">Select Master Contact</option>
            @foreach($contacts as $contact)
              <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->email }})</option>
            @endforeach
          </select>
          <div id="primaryContactIdError"></div>
        </div>
        <div class="form-group">
          <label>Secondary Contact *</label>
          <select id="secondaryContactId" class="form-control">
            <option value="">Select Secondary Contact</option>
            @foreach($contacts as $contact)
              <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->email }})</option>
            @endforeach
          </select>
          <div id="secondaryContactIdError"></div>
        </div>
        <div class="form-group mt-3 text-end">
          <button id="confirmMergeBtn" class="btn btn-success">Merge Contacts</button>
        </div>
      </div>
    </div>
  </div>
</div>
