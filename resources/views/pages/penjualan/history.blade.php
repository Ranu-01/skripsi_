@extends('layouts.app')

@section('title')
    History Transaksi Penjualan
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
                ajax: "{{ route('transaksi.kasir.history') }}",
                columns: [{
                        data: "kode_transaksi"
                    },
                    {
                        data: "created_at",
                        render: function(data, type, row) {
                            return data.substr(0, 10);
                        }
                    },
                    {
                        data: "grand_total",
                        render: function(data, type, row, meta) {
                            return rupiah(data);
                        }
                    },

                    {
                        data: "kode_transaksi",
                        render: function(data, type, row, meta) {
                            let html =
                                '<button class="btn btn-info waves-effect waves-light btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal">' +
                                '  <i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
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
                        "url": "{{ route('transaksi.kasir.history.detail') }}",
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
                            data: "quantity",
                            name: "quantity"
                        },
                        {
                            data: "sub_total",
                            render: function(data, type, row) {
                                return rupiah(data);
                            }
                        }
                    ],
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
                                <th>Grand Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
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
                                <th>Harga</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
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

    {{-- chart --}}
    <div class="col-xl-14">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Pie Chart</h4>
                {{-- <script>
                var options = {
                    series: [{
                    name: 'penjualan',
                    data: @json($data),
                  }],
                    chart: {
                    height: 350,
                    type: 'bar',
                  },
                  plotOptions: {
                    bar: {
                      borderRadius: 10,
                      dataLabels: {
                        position: 'top', // top, center, bottom
                      },
                    }
                  },
                  dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                      fontSize: '12px',
                      colors: ["#304758"]
                    }
                  },
          
                  xaxis: {
                    categories: @json($data),
                    position: 'top',
                    axisBorder: {
                      show: false
                    },
                    axisTicks: {
                      show: false
                    },
                    crosshairs: {
                      fill: {
                        type: 'gradient',
                        gradient: {
                          colorFrom: '#D8E3F0',
                          colorTo: '#BED1E6',
                          stops: [0, 100],
                          opacityFrom: 0.4,
                          opacityTo: 0.5,
                        }
                      }
                    },
                    tooltip: {
                      enabled: true,
                    }
                  },
                  yaxis: {
                    axisBorder: {
                      show: true
                    },
                    axisTicks: {
                      show: true,
                    },
                    labels: {
                      show: true,
                    }
          
                  },
                  title: {
                    text: 'Jumlah sampah per kategori',
                    floating: true,
                    offsetY: 330,
                    align: 'center',
                    style: {
                      color: '#444'
                    }
                  }
                  };
          
                  var chart = new ApexCharts(document.querySelector("#chart"), options);
                  chart.render();
          
              new DataTable('#table2', {
                  // order: [[5,'desc']]
              });
          </script> --}}

                <div id="pie-chart" data-colors='["--bs-primary","--bs-warning", "--bs-danger","--bs-info", "--bs-success"]'
                    class="e-charts"></div>
            </div>
        </div>
    </div>
@endsection
