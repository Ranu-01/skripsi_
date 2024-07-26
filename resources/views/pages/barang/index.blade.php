@extends('layouts.app')

@section('title')
    Master Barang
@endsection


@push('after-app-script')
    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                ajax: "{{ route('barang.index') }}",
                columns: [{
                        data: "kode_barang"
                    },
                    {
                        data: "nama_barang"
                    },
                    {
                        data: "harga_barang",
                        render: function(data, type, row, meta) {
                            return rupiah(data);
                        }
                    },
                    {
                        data: "stok"
                    },
                    {
                        data: "safety_stok",
                        render: function(data, type, row, meta) {
                            return Math.round(data);
                        }
                    },
                    {
                        data: "rop",
                        render: function(data, type, row, meta) {
                            return Math.round(data);
                        }
                    },
                    {
                        data: "kode_barang",
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

            const rupiah = (number) => {
                return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR"
                }).format(number);
            }

            $('form#create').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('barang.store') }}",
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
                }).fail(function(resp) {
                    message = "";
                    if (typeof resp.responseJSON.error.messages.nama_barang !== 'undefined') {
                        message += resp.responseJSON.error.messages.nama_barang[0];
                    }
                    if (typeof resp.responseJSON.error.messages.harga_barang !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.nama_barang[0] !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.harga_barang[0];
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Warning",
                        text: message,
                        timer: 3000
                    });
                });

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
                    url: "{{ route('barang.destroy') }}",
                    data: {
                        "kode_barang": id
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
                    url: "{{ route('barang.show') }}",
                    data: {
                        "kode_barang": id
                    },
                    success: function(resp) {

                        $('#editModal').modal('toggle');
                        $('#kode_barang').val(resp.barang[0].kode_barang);
                        $('#nama_barang_edit').val(resp.barang[0].nama_barang);
                        $('#harga_barang_edit').val(resp.barang[0].harga_barang);
                    }
                });
            });

            $('form#update').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('barang.store') }}",
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
                }).fail(function(resp) {

                    message = "";
                    if (typeof resp.responseJSON.error.messages.nama_barang !== 'undefined') {
                        message += resp.responseJSON.error.messages.nama_barang;
                    }
                    if (typeof resp.responseJSON.error.messages.harga_barang !== 'undefined') {
                        if (typeof resp.responseJSON.error.messages.nama_barang[0] !==
                            'undefined') {
                            message += " & ";
                        }
                        message += resp.responseJSON.error.messages.harga_barang[0];
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
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga Barang</th>
                                <th>Quantity</th>
                                <th>Safety Stock</th>
                                <th>ROP</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
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
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create">
                    <div class="modal-body">

                        <div class="row">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="nama_barang" id="nama_barang">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="harga_barang" class="form-label">Harga Barang</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="harga_barang" id="harga_barang">
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
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <div class="col-md-12">
                                <input type="hidden" name="kode_barang" id="kode_barang">
                                <input class="form-control" type="text" name="nama_barang" id="nama_barang_edit">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <label for="harga_barang" class="form-label">Harga Barang</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="harga_barang" id="harga_barang_edit">
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
