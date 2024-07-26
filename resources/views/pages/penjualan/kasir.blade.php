@extends('layouts.app')

@section('title')
    Kasir
@endsection

@push('after-app-script')
    <script>
        let mainTable = $('#datatable').DataTable({
            lengthMenu: [5, 10, 20, 50, 100],
            ajax: "{{ route('transaksi.kasir') }}",
            "columnDefs": [{
                    className: "nama-barang",
                    "targets": [1]
                },
                {
                    className: "harga-barang",
                    "targets": [2]
                },
            ],
            columns: [{
                    data: 'kode_barang',
                    name: 'kode_barang'
                },
                {
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'harga_barang',
                    name: 'harga_barang',
                    render: function(data, type, row) {
                        return rupiah(data);
                    },
                },
                {
                    data: 'stok',
                    name: 'stok'
                },
                {
                    data: null,
                    render: function() {
                        let html =
                            '<button class="btn btn-success waves-effect waves-light btn-add" data-bs-toggle="modal"' +
                            'data-bs-target="#quantityModal"><i class="bx bxs-cart align-middle font-size-18"></i></button>';
                        return html;
                    },
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        let no = 1;
        let grandTotal = 0;
        let selectedData;
        let keranjang = [];
        let keranjangDatatable;

        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(number);
        }

        $('.stok').hide();
        $('.alert-warning').hide();
        $('.zero').hide();
        $('.alert-success').hide();

        function viewKeranjangDataTable(paramOne) {
            $('#datatable-keranjang').DataTable().clear();
            $('#datatable-keranjang').DataTable().destroy();
            if (paramOne.length > 0) {
                $('#grandTotal').text(rupiah(grandTotal));
                return $('#datatable-keranjang').DataTable({
                    lengthMenu: [5, 10, 20, 50, 100],
                    data: paramOne,
                    columns: [{
                            data: 'no'
                        },
                        {
                            data: 'nama_barang'
                        },
                        {
                            data: 'harga_barang',
                            render: function(data, type, row) {
                                return rupiah(data);
                            }
                        },
                        {
                            data: 'jumlah'
                        },
                        {
                            data: 'subtotal',
                            render: function(data, type, row) {
                                return rupiah(data);
                            }
                        },
                        {
                            data: 'kode_barang',
                            render: function(data, type, row) {
                                return '<button class="btn btn-danger waves-effect waves-light btn-remove"><i class="bx bxs-trash align-middle font-size-18"></i></button>';
                            }
                        }
                    ],
                });
            } else {
                $('#grandTotal').text(rupiah(grandTotal));
                return $('#datatable-keranjang').DataTable({
                    lengthMenu: [5, 10, 20, 50, 100],
                });
            }
        }

        function changeBarangAfterAddKasir(kode_barang, jumlah_pembelian) {
            let found = false;
            if (keranjang.length > 0) {
                for (var key in keranjang) {
                    if (keranjang[key].kode_barang == kode_barang) {
                        grandTotal -= (keranjang[key].subtotal);
                        keranjang[key].jumlah = Number(jumlah_pembelian);
                        keranjang[key].subtotal = Number(keranjang[key].harga_barang) * Number(jumlah_pembelian);
                        grandTotal += keranjang[key].subtotal;
                        found = true;
                        break;
                    }
                    found = false;
                }
                if (found == false) {
                    let keranjangTemp = {
                        "no": no++,
                        "kode_barang": selectedData.kode_barang,
                        "nama_barang": selectedData.nama_barang,
                        "harga_barang": selectedData.harga_barang,
                        "jumlah": Number(jumlah_pembelian),
                        "subtotal": (selectedData.harga_barang) * Number(jumlah_pembelian)
                    }
                    grandTotal += (selectedData.harga_barang) * Number(jumlah_pembelian);
                    keranjang.push(keranjangTemp);
                }
            } else {
                let keranjangTemp = {
                    "no": no++,
                    "kode_barang": selectedData.kode_barang,
                    "nama_barang": selectedData.nama_barang,
                    "harga_barang": selectedData.harga_barang,
                    "jumlah": Number(jumlah_pembelian),
                    "subtotal": (selectedData.harga_barang) * Number(jumlah_pembelian)
                }
                grandTotal += (selectedData.harga_barang) * Number(jumlah_pembelian);
                keranjang.push(keranjangTemp);
            }
        }

        $('#datatable').on('click', '.btn-add', function(e) {
            selectedData = '';
            let indexRow = mainTable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = mainTable.row(indexRow).data();
            $('#label-barang').text(selectedData.nama_barang);
            $('#jumlah_pembelian').val("");
        });

        $('#btn-save-add').on('click', function(e) {
            let jumlah_pembelian = Number($('#jumlah_pembelian').val());
            let kode_barang = selectedData.kode_barang
            if (jumlah_pembelian > Number(selectedData.stok)) {
                $('#quantityModal').modal('toggle');
                $('.stok').show();
            } else if (jumlah_pembelian == "") {
                $('#quantityModal').modal('toggle');
                $('.zero').show();
            } else {
                changeBarangAfterAddKasir(kode_barang, jumlah_pembelian);
                $('#quantityModal').modal('toggle');
                keranjangDatatable = viewKeranjangDataTable(keranjang);
            }
        });

        function changeNumberDelKasir() {
            no = 1;
            for (var key in keranjang) {
                keranjang[key].no = no;
                no++;
            }
        }

        $("#jumlah_pembelian").keypress(function(evt) {
            var key = String.fromCharCode(evt.which);
            if (!(/[0-9]/.test(key))) {
                evt.preventDefault();
            }
        });

        $('#datatable-keranjang').on('click', '.btn-remove', function(e) {
            selectedKeranjang = '';
            let indexRow = keranjangDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedKeranjang = keranjangDatatable.row(indexRow).data();
            console.log(indexRow);
            console.log(selectedKeranjang);
            grandTotal -= selectedKeranjang.subtotal;
            keranjang.splice(indexRow, 1);
            changeNumberDelKasir();
            keranjangDatatable = viewKeranjangDataTable(keranjang);
        });

        $('.btn-close').on('click', function() {
            $('.alert').hide();
        });

        $('#save-transaction').on('click', function() {
            if (keranjang.length > 0 && $('#sale_date').val() != "") {
                $.ajax({
                    type: "post",
                    url: "{{ route('transaksi.kasir.store') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'keranjang': JSON.stringify(keranjang),
                        'grand_total': grandTotal,
                        'tgl_penjualan': $('#sale_date').val()
                    },
                    success: function(response) {
                        no = 1;
                        keranjang = [];
                        $('.alert-success').show();
                        viewKeranjangDataTable(keranjang);
                        grandTotal = 0;
                        $('#grandTotal').text("Rp 0,00");
                        mainTable.clear();
                        mainTable.destroy();
                        mainTable = $('#datatable').DataTable({
                            lengthMenu: [5, 10, 20, 50, 100],
                            ajax: "{{ route('transaksi.kasir') }}",
                            "columnDefs": [{
                                    className: "nama-barang",
                                    "targets": [1]
                                },
                                {
                                    className: "harga-barang",
                                    "targets": [2]
                                },
                            ],
                            // order: [
                            //     [1, 'desc']
                            // ],
                            columns: [{
                                    data: 'kode_barang',
                                    name: 'kode_barang'
                                },
                                {
                                    data: 'nama_barang',
                                    name: 'nama_Barang'
                                },
                                {
                                    data: 'harga_barang',
                                    name: 'harga_barang',
                                    render: function(data, type, row) {
                                        return rupiah(data);
                                    },
                                },
                                {
                                    data: 'stok',
                                    name: 'stok'
                                },
                                {
                                    data: null,
                                    render: function() {
                                        let html =
                                            '<button class="btn btn-success waves-effect waves-light btn-add" data-bs-toggle="modal"' +
                                            'data-bs-target="#quantityModal"><i class="bx bxs-cart align-middle font-size-18"></i></button>';
                                        return html;
                                    },
                                    name: 'action',
                                    orderable: false,
                                    searchable: false
                                },
                            ]
                        });
                    }
                });
            }

        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">@yield('title')</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="alert alert-success alert-dismissible" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            Transaksi berhasil disimpan
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="alert alert-warning alert-dismissible stok" role="alert">
            <i class="mdi mdi-alert-outline me-2"></i>
            Stok barang tidak cukup!!
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="alert alert-warning alert-dismissible zero" role="alert">
            <i class="mdi mdi-alert-outline me-2"></i>
            Jumlah pembelian harus diisi
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Barang Kasir</h4>
                    <div class="my-3 w-25">
                        <label for="sale_date" class="form-label">Tanggal Penjualan</label>
                        <input type="date" name="sale_date" id="sale_date" class="form-control">
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th class="col-md-2">Harga barang</th>
                                <th class="col-md-1">Quantity</th>
                                <th class="col-md-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Keranjang Kasir</h4>
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary waves-effect waves-light" id="save-transaction">
                            <i class="bx bx-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                    <table id="datatable-keranjang" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th class="col-md-2">Harga barang</th>
                                <th class="col-md-1">Quantity</th>
                                <th class="col-md-2">Subtotal</th>
                                <th class="col-md-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tr>
                            <th colspan="4" class="text-center font-size-18">Grand Total</th>
                            <th colspan="2" class="font-size-16"><span id="grandTotal">Rp 0,00</span></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Jumlah Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="nama_barang" class="form-label font-weight-bold" id="label-barang"></label>
                    <input class="form-control" type="text" value="" id="jumlah_pembelian"
                        placeholder="Masukkan Jumlah Pembelian">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save-add">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
