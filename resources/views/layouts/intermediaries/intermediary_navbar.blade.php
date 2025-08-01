<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('page-title')</title>
  <link rel="icon" href="{!! asset('dist/img/aims_logo.png') !!}" />
  <!-- export -->
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
  <!-- /export -->

  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
  <!--  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" /> -->
  <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" />

  <!-- <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"> -->
  <!--  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script> -->

  <!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- Font Awesome -->

  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href=" {{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href=" {{asset('plugins/jqvmap/jqvmap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href=" {{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css') }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css') }}">
  <!-- Google Font: Source Sans Pro -->

  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <style type="text/css">
    .content {
      display: none;
    }

    .preloader {
      margin: 0;
      position: absolute;
      top: 50%;
      left: 50%;
      margin-right: -50%;
      transform: translate(-50%, -50%);

      * {
        font-family: Algerian;
      }
    }

    * {
      font-family: times new romans;
      font-size: 13px;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed  layout-navbar-fixed" style="height: auto;">



  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white  navbar-light text-white " style="background-color: #2c6aa0;">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars" style="background-color: white"></i></a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
          <span class="brand-text font-weight-light" style="font-size: 20px"><strong>
              <h3>INTERMEDIARY PORTAL
            </strong></h3> </span>

        </li>
      </ul>

      <!-- SEARCH FORM -->
      <form class="form-inline ml-5">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn-navbar" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>

      <!-- Right Side Of Navbar -->
      <ul class="navbar-nav ml-auto">
        <!-- Authentication Links -->
        @guest
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
        </li>
        @if (Route::has('register'))
        <li class="nav-item">
          <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
        </li>
        @endif

        @else
        <li class="nav-item dropdown">
          <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="color: white">
            {{ Auth::user()->name }}<span class="caret"></span>
          </a>

          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
              {{ __('Logout') }}
            </a>
            <a class="dropdown-item" href="{{route('profile.index')}}">
              Profile
            </a>


            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </div>
        </li>
        @endguest
      </ul>




      </ul>
    </nav>

    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="#" class="brand-link" style="background-color: #2c6aa0;">
        <img src="{{asset('dist/img/aims_logo.png') }}" alt="aims Logo" class="brand-image img-square elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">brokerage</span>

      </a>


      <!-- Sidebar -->
      <div class="sidebar">


        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="{{url('brokerage/intermediary/dashboard')}}" class="nav-link">
                <i class="fa fa-home"></i>

                <p>
                  Dashboard

                </p>
              </a>
            </li>



            <li class="nav-item has-treeview">
              <a href="{{route('policylists.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Policy List


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('policystatements.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Policy Statements


                </p>
              </a>
            </li>


            <li class="nav-item has-treeview">
              <a href="{{route('policyrenewals.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Policy Renewal


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Claims
                  <i class="fas fa-angle-left right"></i>

                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{url('brokerage/intermediary/claimsrequirements')}}" class="nav-link">
                    <i class="fas fa-minus"></i>
                    <p>Claim Requirements</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('lossnotifications.index')}}" class="nav-link">
                    <i class="fas fa-minus"></i>
                    <p>FNOL</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('claimstatements.index')}}" class="nav-link">
                    <i class="fas fa-minus"></i>
                    <p>Claims Statements</p>
                  </a>
                </li>

              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('proposals.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Proposal


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('covernotes.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Issue Cover Note


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('quotations.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Quotations


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('commission.index')}}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <p>
                  Commissions


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fa fa-barcode"></i>
                <p>
                  Set Portal Theme


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{url('brokerage/intermediary/chats')}}" class="nav-link">
                <i class="fa fa-comments"></i>
                <p>
                  Chats

                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('feedbacks.index')}}" ref="" class="nav-link">
                <i class="fa fa-envelope"></i>
                <p>
                  feedback


                </p>
              </a>
            </li>

            <li class="nav-item has-treeview">
              <a href="{{route('faqs.index')}}" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  FAQS

                </p>
              </a>
            </li>

          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- start container -->
    <div class="content-wrapper">


      <br>


      <!-- loader -->
      <div class="preloader">
        <img src="{{asset('dist/img/loading_spinner.gif') }}">

      </div>
      <!-- /loader -->
      <div class="content">
        <div class="container">
          @yield('content')
        </div>
      </div>
      <!-- /.content -->
    </div>

  </div>
  <!-- end container -->
  <footer class="main-footer fixed-bottom bg-dark ">
    <marquee>
      <!-- To the right -->
      <div class=" d-none d-sm-inline">
        Beyond the horizons
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2020 <a href="https://www.aimsoft.co.ke/">Aims Crm</a>.</strong> All rights reserved.
    </marquee>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    <!-- export -->
    <!--  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <!-- /export -->
    </div>
    <!-- ./wrapper -->


    <script type="text/javascript">
      $(function() {
        $(".preloader").fadeOut(1000, function() {
          $(".content").fadeIn(2000);

        });

      });
    </script>

    <!-- jQuery -->

    <!-- <script src="{{asset('plugins/jquery/jquery.min.js') }}"></script> -->
    <!-- jQuery UI 1.11.4 -->
    <!-- <script src="{{asset('plugins/jquery-ui/jquery-ui.min.js') }}') }}"></script> -->
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- Bootstrap 4 -->
    <!-- <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script> -->
    <!-- ChartJS -->
    <script src="{{asset('plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{asset('plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{asset('plugins/moment/moment.min.j') }}s"></script>
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{asset('plugins/summernote/summernote-bs4.min.j') }}s"></script>
    <!-- overlayScrollbars -->
    <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{asset('dist/js/pages/dashboard.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{asset('dist/js/demo.js') }}"></script>
</body>
<script>
  /** add active class and stay opened when selected */
  var url = window.location;

  // for sidebar menu entirely but not cover treeview
  $('ul.nav-sidebar a').filter(function() {
    return this.href == url;
  }).addClass('active');

  // for treeview
  $('ul.nav-treeview a').filter(function() {
    return this.href == url;
  }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
</script>


</html>