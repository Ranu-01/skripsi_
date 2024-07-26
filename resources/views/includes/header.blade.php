<header id="page-topbar">
     <div class="navbar-header">
         <div class="d-flex">
             <!-- LOGO -->
             <div class="navbar-brand-box">
                 <h4 class="text-white mt-4">Toko Buket</h4>
             </div>
 
             <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                 <i class="fa fa-fw fa-bars"></i>
             </button>
 
         </div>
 
         <div class="d-flex">
 
             <div class="dropdown d-inline-block">
                 <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                     data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img class="rounded-circle header-profile-user"
                         src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                     <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ Auth::user()->name }}</span>
                     <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                 </button>
                 <div class="dropdown-menu dropdown-menu-end">
                     <!-- item-->
                     <div class="dropdown-divider"></div>
                     <a class="dropdown-item text-danger" href="{{ route('auth.logout') }}"><i
                             class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                             key="t-logout">Logout</span></a>
                 </div>
             </div>
 
         </div>
     </div>
 </header>
 