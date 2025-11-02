@extends('header')
@section('content')


<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="page-title">Contact Management</h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-primary my-3" id="addContactBtn">Add Contact</button>
                            <button class="btn my-3 btn-warning mergeContact"> Merge Contacts </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" id="searchName" class="form-control" placeholder="Search by Name">
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="searchEmail" class="form-control" placeholder="Search by Email">
                        </div>
                        <div class="col-md-4">
                            <select id="filterGender" class="form-control">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                </div>

                <div class="page-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th class="text-center" style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="contactTableBody">
                        @forelse($contacts as $contact)
                            <tr data-id="{{ $contact->id }}">
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ ucfirst($contact->gender) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning editContact me-1">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger deleteContact">
                                        Delete
                                    </button>
                                
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i> No contacts available.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                      
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


@include('contacts.form')
@include('contacts.merge')
@endsection

@push('admin_custom_scripts')
<script>
    const csrfToken = "{{ csrf_token() }}";
    const customFieldStoreUrl = "{{ route('custom_fields.store') }}";
    const store ="{{ route('contacts.store') }}";
    const merge ="{{ route('contacts.merge') }}";
</script>
<script src="{{ asset('js/custom.js') }}"></script>

@endpush