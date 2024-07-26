<div class="vertical-menu">

     <div data-simplebar class="h-100">
 
         <!--- Sidemenu -->
         <div id="sidebar-menu">
             <!-- Left Menu Start -->
             <ul class="metismenu list-unstyled" id="side-menu">
 
                 <li class="menu-title" key="t-menu">Menu</li>
 
                 <li>
                     <a href="{{ route('dashboard.index') }}">
                         <i class="bx bx-home-circle"></i>
                         <span key="t-dashboards">Dashboards</span>
                     </a>
                 </li>
 
 
                 <li class="menu-title" key="t-apps">Master Data</li>
 
                 <li>
                     <a href="{{ route('barang.index') }}" class="waves-effect">
                         <i class="bx bxs-component"></i>
                         <span key="t-barang">Barang</span>
                     </a>
                 </li>
 
                 @if (Auth::user()->role == 'admin')
                     <li>
                         <a href="{{ route('auth.index') }}" class="waves-effect">
                             <i class="bx bxs-component"></i>
                             <span key="t-barang">Pegawai</span>
                         </a>
                     </li>
                 @endif
 
 
                 <li class="menu-title" key="t-pages">Utility</li>
                 <li>
                     <a href="{{ route('transaksi.kasir') }}" class="waves-effect">
                         <i class="bx bxs-calculator"></i>
                         <span key="t-kasir">Penjualan</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('transaksi.persediaan-masuk') }}" class="waves-effect">
                         <i class="bx bxs-duplicate"></i>
                         <span key="t-permintaan">Pembelian</span>
                     </a>
                 </li>
 
                 <li class="menu-title" key="t-components">Riwayat</li>
 
                 <li>
                     <a href="{{ route('transaksi.kasir.history') }}" class="waves-effect">
                         <i class="bx bxs-spreadsheet"></i>
                         <span key="t-kasir">Riwayat Penjualan</span>
                     </a>
                 </li>
 
                 <li>
                     <a href="{{ route('transaksi.persediaan-masuk.history') }}" class="waves-effect">
                         <i class="bx bx-notepad"></i>
                         <span key="t-kasir">Riwayat Pembelian</span>
                     </a>
                 </li>
             </ul>
         </div>
         <!-- Sidebar -->
     </div>
 </div>
 