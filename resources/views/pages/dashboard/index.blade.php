@extends('layouts.app')

@section('title')
    Dashbooard
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
            var table = $('#dataTable').DataTable({
                ajax: "{{ route('dashboard.index') }}",
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                        data: "item_id"
                    },
                    {
                        data: "pembelian"
                    },
                    {
                        data: "penjualan"
                    },
                    {
                        data: "stock"
                    },
                    {
                        data: "sale_date"
                    },
                    {
                        data: "safety_stock"
                    },
                    {
                        data: "avg_peritem"
                    },
                    {
                        data: "min"
                    },
                    {
                        data: "max"
                    },
                    {
                        data: "Q"
                    }
                ],
            });
        });
    </script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">

        <div class="col-xl">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card mini-stats-wid">
                        <div class="card-body">

                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Total Jenis Barang</p>
                                    <h5 class="mb-0">{{ $total_jenis }}</h5>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card blog-stats-wid">
                        <div class="card-body">

                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Total Transaksi ({{ $bulan_tahun }})</p>
                                    <h5 class="mb-0">{{ $total_transaksi }}</h5>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card blog-stats-wid">
                        <div class="card-body">
                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Total Pendapatan ({{ $bulan_tahun }})</p>
                                    <h5 class="mb-0">
                                        @php
                                            $hasil_rupiah =
                                                'Rp ' . number_format($total_pendapatan->total_penjualan, 0, ',', '.');
                                            echo $hasil_rupiah;
                                        @endphp
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card blog-stats-wid">
                        <div class="card-body">
                            <div class="d-flex flex-wrap">
                                <div class="me-3">
                                    <p class="text-muted mb-2">Jumlah Pegawai</p>
                                    <h5 class="mb-0">{{ $total_pegawai }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="card">
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Pembelian</th>
                                <th>Penjualan</th>
                                <th>Stock</th>
                                <th>Bulan Tahun</th>
                                <th>Safety Stock</th>
                                <th>Average Per Item</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Q</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
