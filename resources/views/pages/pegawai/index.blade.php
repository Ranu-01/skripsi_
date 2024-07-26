@extends('layouts.app')

@section('title')
    Master Pegawai
@endsection


@push('after-app-script')
    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                ajax: "{{ route('auth.index') }}",
                columns: [{
                        data: "id",
                        render: function(data, type, row, meta) {
                            return meta.row + 1
                        }
                    },
                    {
                        data: "name"
                    },
                    {
                        data: "pegawais[0].alamat"
                    },
                    {
                        data: "username"
                    },
                    {
                        data: "email",
                    },
                    {
                        data: "id",
                        render: function(data, type, row, meta) {
                            let html =
                                "<button class='btn btn-success waves-effect waves-light btn-edit' data-id='" +
                                data +
                                "'><i class='bx bx-edit align-middle me-2 font-size-18'></i>Edit</button>";

                            html +=
                                "<button class='btn btn-danger waves-effect waves-light ms-1 btn-delete' data-id='" +
                                data +
                                "'><i class='bx bx-trash align-middle me-2 font-size-18'></i>Delete</button>";
                            return html;
                        }
                    }
                ],
            });

            $('#btn-add-modal').click(function(e) {
                e.preventDefault();
                $('form#create').find("input[type=text]").val("");
            });

            var DTbody = $('#dataTable tbody');

            DTbody.on('click', '.btn-delete', function() {
                var id = $(this).data("id");
                $.ajax({
                    type: "GET",
                    url: "{{ route('auth.destroy') }}",
                    data: {
                        "id": id
                    },
                    success: function(resp) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: resp.message,
                            timer: 3000
                        });
                        table.ajax.reload();
                    }
                });
            });

            DTbody.on('click', '.btn-edit', function() {
                var id = $(this).data("id");
                $.ajax({
                    type: "GET",
                    url: "{{ route('auth.show') }}",
                    data: {
                        "id": id
                    },
                    success: function(resp) {
                        $('#editModal').modal('toggle');
                        $('#id').val(resp.pegawai.id);
                        $('#nama_pegawai_edit').val(resp.pegawai.name);
                        $('#alamat_pegawai_edit').val(resp.pegawai.pegawais[0].alamat);
                        $('#email_edit').val(resp.pegawai.email);
                        $('#username_edit').val(resp.pegawai.username);
                        $('#password_edit').val(resp.pegawai.password);
                    }
                });
            });

            $('form#create').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('auth.store') }}",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    cache: false,
                    contentType: false,
                }).done(function(resp) {

                    table.ajax.reload();
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: resp.message,
                        timer: 3000
                    });
                    $('#createModal').modal('toggle');
                    $('form#create').find("input[type=text]").val("");
                    $('form#create').find("input[type=password]").val("");
                }).fail(function(resp) {
                    console.log(resp);
                    message = "";
                    if (typeof resp.responseJSON.error.messages.nama_pegawai !== 'undefined') {
                        message += resp.responseJSON.error.messages.nama_pegawai[0];
                    }
                    if (typeof resp.responseJSON.error.messages.alamat_pegawai !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.nama_pegawai !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.alamat_pegawai[0];
                    }
                    if (typeof resp.responseJSON.error.messages.email !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.alamat_pegawai !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.email[0];
                    }
                    if (typeof resp.responseJSON.error.messages.username !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.email !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.username[0];
                    }
                    if (typeof resp.responseJSON.error.messages.password !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.email !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.password[0];
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Warning",
                        text: message,
                        timer: 3000
                    });
                });

            });

            $('form#update').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('auth.store') }}",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    cache: false,
                    contentType: false,
                }).done(function(resp) {

                    table.ajax.reload();
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: resp.message,
                        timer: 3000
                    });
                    $('#editModal').modal('toggle');
                    $('form#update').find("input[type=text]").val("");
                    $('form#update').find("input[type=password]").val("");
                }).fail(function(resp) {
                    console.log(resp);
                    message = "";
                    if (typeof resp.responseJSON.error.messages.nama_pegawai !== 'undefined') {
                        message += resp.responseJSON.error.messages.nama_pegawai[0];
                    }
                    if (typeof resp.responseJSON.error.messages.alamat_pegawai !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.nama_pegawai !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.alamat_pegawai[0];
                    }
                    if (typeof resp.responseJSON.error.messages.email !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.alamat_pegawai !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.email[0];
                    }
                    if (typeof resp.responseJSON.error.messages.username !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.email !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.username[0];
                    }
                    if (typeof resp.responseJSON.error.messages.password !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.email !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.password[0];
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Warning",
                        text: message,
                        timer: 3000
                    });
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data @yield('title')</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                        <li class="breadcrumb-item active">Data @yield('title')</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary waves-effect waves-light" id="btn-add-modal" data-bs-toggle="modal"
                            data-bs-target="#createModal"> <i
                                class="bx bx-list-plus align-middle me-2 font-size-18"></i>Tambah</button>
                    </div>

                    <table id="dataTable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pegawai</th>
                                <th>Alamat</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create">
                    <div class="modal-body">
                        <div class="row">
                            <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="nama_pegawai" id="nama_pegawai">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="alamat_pegawai" class="form-label">Alamat Pegawai</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="alamat_pegawai" id="alamat_pegawai">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="col-md-12">
                                <input class="form-control" type="email" name="email" id="email">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="username" id="username">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="col-md-12">
                                <input class="form-control" type="password" name="password" id="password">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-add">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ubah Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update">
                    <div class="modal-body">
                        <div class="row">
                            <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id">
                                <input class="form-control" type="text" name="nama_pegawai" id="nama_pegawai_edit">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="alamat_pegawai" class="form-label">Alamat Pegawai</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="alamat_pegawai"
                                    id="alamat_pegawai_edit">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="col-md-12">
                                <input class="form-control" type="email" name="email" id="email_edit">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="username" id="username_edit">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="col-md-12">
                                <input class="form-control" type="password" name="password" id="password_edit">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
