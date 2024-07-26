@extends('layouts.app')

@section('title')
    History Persediaan Masuk
@endsection

@push('after-app-script')
    <script>
        $(document).ready(function() {
            const rupiah = (number) => {
                return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR"
                }).format(number);
            }

            var mainTable = $('#datatable').DataTable({
                ajax: "{{ route('transaksi.persediaan-masuk.history') }}",
                columns: [{
                        data: "kode_transaksi"
                    },
                    {
                        data: "created_at"
                    },
                    {
                        data: "total_qty"
                    },
                    {
                        data: "kode_transaksi",
                        render: function(data, type, row, meta) {
                            let html =
                                '<button class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">' +
                                '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                            return html;
                        }
                    }
                ],
            });

            var DTbody = $('#datatable tbody');
            $(DTbody).on('click', '.btn-detail', function() {
                let selectedData = '';
                let kode_transaksi = '';
                let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
                selectedData = mainTable.row(indexRow).data();
                kode_transaksi = selectedData.kode_transaksi;
                $("#kode-transaksi").text(selectedData.kode_transaksi);
                $('#detail-datatable').DataTable().clear();
                $('#detail-datatable').DataTable().destroy();
                $('#detail-datatable').DataTable({
                    ajax: {
                        "type": "GET",
                        "url": "{{ route('transaksi.persediaan-masuk.history.detail') }}",
                        "data": {
                            'kode_transaksi': kode_transaksi
                        }
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: "nama_barang",
                            name: "nama_barang"
                        },
                        {
                            data: "harga_barang",
                            render: function(data, type, row) {
                                return rupiah(data);
                            }
                        },
                        {
                            data: "qty",
                            name: "qty"
                        }
                    ]
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
            @if (session()->has('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-all me-2"></i>
                    {{ session('msg') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-body">

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Kode Penjualan</th>
                                <th>Tanggal Penjualan</th>
                                <th>Quantity Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
    {{-- </div> --}}
    <div class="modal modal-lg fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail <span id="kode-transaksi"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered dt-responsive nowrap w-100" id="detail-datatable">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Harga Barang</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
