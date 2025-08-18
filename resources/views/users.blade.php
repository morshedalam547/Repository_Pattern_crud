@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">👥 User Management</h2>

    <!-- Alerts -->
    <div id="alertContainer"></div>

    <!-- Add/Edit User Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">Add / Edit User</div>
        <div class="card-body">
            <form id="userForm" class="row g-3">
                @csrf
                <input type="hidden" name="user_id" id="user_id">
                <div class="col-md-4 col-sm-12">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" required>
                </div>
                <div class="col-md-4 col-sm-12">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-3 col-sm-12">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="col-md-1 col-sm-12">
                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- User List -->
    <div class="card shadow-sm">
        <div class="card-header">User List</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        $('#alertContainer').html(alertHtml);
        setTimeout(() => { $('.alert').alert('close'); }, 3000);
    }

    // Load all users
    function loadUsers() {
        $.get('/users', function (data) {
            let rows = '';
            data.forEach(user => {
                rows += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info editBtn" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${user.id}"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>`;
            });
            $('#userTableBody').html(rows);
        });
    }

    loadUsers();

    // Add/Edit user
    $('#userForm').submit(function(e) {
        e.preventDefault();

        let userId = $('#user_id').val();
        let url = userId ? '/users/' + userId : '/users';

        // form data serialize
        let formData = $(this).serializeArray();

        // যদি update হয়, Laravel এর জন্য _method=PUT যোগ করো
        if(userId){
            formData.push({name: "_method", value: "PUT"});
        }

        $.ajax({
            url: url,
            type: 'POST', // সবসময় POST পাঠাও
            data: $.param(formData),
            success: function(response){
                showAlert('success', userId ? 'User updated successfully!' : 'User added successfully!');
                loadUsers();
                $('#userForm')[0].reset();
                $('#submitBtn').text('Add');
                $('#user_id').val('');
            },
            error: function(xhr){
                let message = xhr.responseJSON?.message || 'Something went wrong';
                showAlert('danger', message);
            }
        });
    });

    // Edit button click
    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');
        $('#user_id').val(id);
        $('#name').val($(this).data('name'));
        $('#email').val($(this).data('email'));
        $('#password').val('');
        $('#submitBtn').text('Update');
    });

    // Delete user
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        if(confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: '/users/' + id,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() {
                    showAlert('success', 'User deleted successfully!');
                    loadUsers();
                },
                error: function(){
                    showAlert('danger', 'Failed to delete user');
                }
            });
        }
    });

});
</script>
@endpush
