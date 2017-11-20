<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="token" id="token" value="{{ csrf_token() }}">
    <title>{{$title or ''}}- Monte Maria | Accounting System</title>
    <link rel="shortcut icon" href="http://localhost/iBilling_%5bv4.5.6.0%5d/ibilling/application/storage/icon/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="57x57" href='{{ asset("assets/application/storage/icon/apple-icon-57x57.png") }}'>
    <link rel="apple-touch-icon" sizes="60x60" href='{{ asset("assets/application/storage/icon/apple-icon-60x60.png") }}'>
    <link rel="apple-touch-icon" sizes="72x72" href='{{ asset("assets/application/storage/icon/apple-icon-72x72.png") }}'>
    <link rel="apple-touch-icon" sizes="76x76" href='{{ asset("assets/application/storage/icon/apple-icon-76x76.png") }}'>
    <link rel="apple-touch-icon" sizes="114x114" href='{{ asset("assets/application/storage/icon/apple-icon-114x114.png") }}'>
    <link rel="apple-touch-icon" sizes="120x120" href='{{ asset("assets/application/storage/icon/apple-icon-120x120.png") }}'>
    <link rel="apple-touch-icon" sizes="144x144" href='{{ asset("assets/application/storage/icon/apple-icon-144x144.png") }}'>
    <link rel="apple-touch-icon" sizes="152x152" href='{{ asset("assets/application/storage/icon/apple-icon-152x152.png") }}'>
    <link rel="apple-touch-icon" sizes="180x180" href='{{ asset("assets/application/storage/icon/apple-icon-180x180.png") }}'>
    <link rel="icon" type="image/png" sizes="192x192"  href='{{ asset("assets/application/storage/icon/favicon-192x192.png") }}'>
    <link rel="icon" type="image/png" sizes="32x32" href='{{ asset("assets/application/storage/icon/favicon-32x32.png") }}'>
    <link rel="icon" type="image/png" sizes="96x96" href='{{ asset("assets/application/storage/icon/favicon-96x96.png") }}'>
    <link rel="icon" type="image/png" sizes="16x16" href='{{ asset("assets/application/storage/icon/favicon-16x16.png") }}'>
    <link rel="manifest" href='{{ asset("assets/application/storage/icon/manifest.json") }}'>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content='{{ asset("assets/application/storage/icon/ms-icon-144x144.png") }}'>
    <meta name="theme-color" content="#ffffff">

    <link href='{{ asset("assets/ui/theme/ibilling/css/bootstrap.min.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/theme/ibilling/lib/fa/css/font-awesome.min.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/theme/ibilling/lib/icheck/skins/all.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/lib/css/animate.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/lib/toggle/bootstrap-toggle.min.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/theme/ibilling/fonts/open-sans/open-sans.css?ver=4.0.1") }}' rel="stylesheet">


   
    <link href='{{ asset("assets/ui/theme/ibilling/css/style.css?ver=2.0.1") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/theme/ibilling/css/component.css?ver=2.0.1") }}' rel="stylesheet">
  

    <link href='{{ asset("assets/ui/lib/icons/css/ibilling_icons.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/theme/ibilling/css/material.css") }}' rel="stylesheet">

    <link href='{{ asset("assets/ui/theme/ibilling/css/dark.css") }}' rel="stylesheet">
    <link href='{{ asset("assets/ui/lib/s2/css/select2.min.css") }}' rel="stylesheet">

    <link href='{{ asset("assets/ui/lib/dp/dist/datepicker.min.css") }}' rel="stylesheet">
    @yield('css')

      <link href='{{ asset("assets/ui/theme/ibilling/css/custom.css") }}' rel="stylesheet">
</head>


<body class="">

    
<section>
    <div id="wrapper">
      <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
          <ul class="nav" id="side-menu">

            <li class="nav-header">
              <div class="dropdown profile-element"> 
                <span>
                  <img src='{{ asset("assets/ui/lib/imgs/default-user-avatar.png") }}'  class="img-circle" style="max-width: 64px;" alt="">
                </span>
                <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                  <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">john</strong>
                  </span> <span class="text-muted text-xs block">My Account <b class="caret"></b></span> </span> 
                </a>
                <ul class="dropdown-menu animated fadeIn m-t-xs">
                  <li><a href="#">Edit Profile</a></li>
                  <li><a href="#">Change Password</a></li>

                  <li class="divider"></li>
                  <li><a href="#">Logout</a></li>
                </ul>
              </div>
            </li>
            <li >
              <a href="#"><i class="fa fa-tachometer"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <li class="">
              <a href="#"><i class="icon-users"></i> <span class="nav-label">Accounts</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                  <li><a href="{{ action('CustomerController@index') }}">Customer</a></li>

                  <li><a href="{{ action('SupplierController@index') }}">Supplier</a></li>
                  
              </ul>
            </li>
           
            <li class="">
              <a href="#"><i class="fa fa-money" aria-hidden="true"></i><span class="nav-label">Sales</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                  <li><a href="{{ action('CashinvoiceController@index') }}">Cash Invoice</a></li>
                  <li><a href="{{ action('ChargeinvoiceController@index') }}">Charge Invoice</a></li>
              </ul>
            </li>
            <li class="">
              <a href="#"><i class="fa fa-money" aria-hidden="true"></i><span class="nav-label">Expenses</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                  <li><a href="{{ action('PurchaseorderController@index') }}">Purchase Order</a></li>
                  <li><a href="{{ action('VoucherController@index') }}">Voucher</a></li>
                  <li class=""><a href="{{ action('PettycashController@index') }}">Petty Cash</a></li>
                  <li><a href="{{ action('JoborderController@index') }}">Job Order</a></li>
                  
              </ul>
            </li>
            <li class="">
              <a href="#"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="nav-label">Other Forms</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
              
                  <li><a href="{{ action('DepositController@index') }}">Deposit</a></li>
                  <li><a href="{{ action('AmortizationController@index') }}">Table of Amortization</a></li>
                  <li><a href="{{ action('BankreconcilationController@index') }}">Bank Reconciliation</a></li>
              </ul>
            </li>
            <li class="">
              <a href="#"><i class="fa fa-money" aria-hidden="true"></i><span class="nav-label">Payments</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                  <li><a href="{{ action('PaymentController@index') }}">Customer Payments</a></li>
                  <li><a href="{{ action('VouchersPaymentController@index') }}">Voucher Payments</a></li>
              </ul>
            </li>
            <li class="">
              <a href="#"><i class="fa fa-table" aria-hidden="true"></i><span class="nav-label">Inventory</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                  <li><a href="{{ action('ItemController@index') }}">Item</a></li>
                  <li><a href="{{ action('DeliveryreceiptController@index') }}">Delivery Receipt</a></li>
                  <li><a href="{{ action('InventoryController@index') }}">Mixing</a></li>
              </ul>
            </li>
            <li >
              <a href="{{ action('ChartofaccountController@index') }}"><i class="fa fa-bar-chart"></i> <span class="nav-label">Chart of Account</span></a>
            </li>
            <li class="#">
              <a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i><span class="nav-label">Report Form</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                 
                  <li><a href="{{ action('ReportsFormController@index') }}">Daily Sales</a></li>
                  <li><a href="{{ action('ReportsFormController@cash_sales_index') }}">Daily Cash Sales</a></li>
              </ul>
            </li>
            <li class="#">
              <a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i><span class="nav-label">Report</span><span class="fa arrow"></span></a>
              <ul class="nav nav-second-level">
                 
                  <li><a href="{{ action('ReportsController@inventory_report') }}">Inventory</a></li>
                  <li><a href="{{ action('ReportsController@purchases_report_view') }}">Purchases</a></li>
                  <li><a href="{{ action('ReportsController@sales_report') }}">Sales</a></li>
                  <li><a href="{{ action('ReportsController@recievable_report') }}">Account Receivable</a></li>
                  <li><a href="{{ action('ReportsController@ap_vendor_view') }}">Vendor And Payable</a></li>
                  <li><a href="{{ action('ReportsController@balance_sheet_view') }}">Balance Sheet</a></li>
                  <li><a href="{{ action('ReportsController@trialbalance_view') }}">Trial Balance</a></li>
                  <li><a href="{{ action('ReportsController@profitloss_view') }}">Profit & Loss</a></li>
                  <li><a href="{{ action('Reports2Controller@egg_production') }}">Egg Collection</a></li>
                  <li><a href="{{ action('ReportsFormController@collection_report') }}">Collection</a></li>
                  

              </ul>
            </li>

          </ul>
        </div>
      </nav>
      <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
          <nav class="navbar navbar-default white-bg" role="navigation" style="margin-bottom: 0">

          <!--   <img class="logo" style="max-height: 40px; width: auto;" src='{{ asset("assets/application/storage/system/logo.png") }}' alt="Logo"> -->
            <div class="navbar-header">
              <a class="navbar-minimalize minimalize-styl-2 btn btn-primary btn-flat" href="#"><i class="fa fa-dedent"></i> </a>

            </div>
            <ul class="nav navbar-top-links navbar-right pull-right">
              <li class="hidden-xs">
                <form class="navbar-form full-width" method="post" action="#">
                  <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Search Customers...">
                    <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                  </div>
                </form>
                </li>
     

                <li class="dropdown navbar-user">
                  <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">

                  <img src="{{ asset('assets/ui/lib/imgs/default-user-avatar.png') }}" alt="">

                  <span class="hidden-xs">Welcome john</span> <b class="caret"></b>
                  </a>
                  <ul class="dropdown-menu animated fadeIn">
                    <li class="arrow"></li>
                    <li><a href="#">Edit Profile</a></li>
                    <li><a href="#">Change Password</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Logout</a></li>
                  </ul>
                </li>

                <li>
              
              </li>
            </ul>
          </nav>
        </div>

        

        @yield('content')

        <div id="right-sidebar">
          <div class="sidebar-container">

              <ul class="nav nav-tabs navs-3">

                  <li class="active"><a data-toggle="tab" href="#tab-1">
                          Notes
                      </a></li>
                  <li class=""><a data-toggle="tab" href="#tab-3">
                          <i class="fa fa-gear"></i>
                      </a></li>
              </ul>

              <div class="tab-content">


                  <div id="tab-1" class="tab-pane active">

                      <div class="sidebar-title">
                          <h3> <i class="fa fa-file-text-o"></i> Quick Notes</h3>

                      </div>

                      <div style="padding: 10px">

                          <form class="form-horizontal push-10-t push-10" method="post" onsubmit="return false;">

                              <div class="form-group">
                                  <div class="col-xs-12">
                                      <div class="form-material floating">
                                          <textarea class="form-control" id="ib_admin_notes" name="ib_admin_notes" rows="10"></textarea>
                                          <label for="ib_admin_notes">What's on your mind?</label>
                                      </div>

                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="col-xs-12">
                                      <button class="btn btn-sm btn-success" type="submit" id="ib_admin_notes_save"><i class="fa fa-check"></i> Save</button>
                                  </div>
                              </div>
                          </form>
                          </div>




                  </div>
                  <div id="tab-3" class="tab-pane">

                      <div class="sidebar-title">
                          <h3><i class="fa fa-gears"></i> Settings</h3>

                      </div>

                      <div class="setings-item">
                          <h4>Theme Color</h4>

                          <ul id="ib_theme_color" class="ib_theme_color">

                              <li><a href="#"><span class="light"></span></a></li>
                              <li><a href="#"><span class="blue"></span></a></li>
                              <li><a href="#"><span class="dark"></span></a></li>
                          </ul>


                      </div>
                      <div class="setings-item">
                          <span>
                              Fold Sidebar by Default ?
                          </span>
                          <div class="switch">
                              <div class="onoffswitch">
                                  <input type="checkbox" name="r_fold_sidebar"  class="onoffswitch-checkbox" id="r_fold_sidebar">
                                  <label class="onoffswitch-label" for="r_fold_sidebar">
                                      <span class="onoffswitch-inner"></span>
                                      <span class="onoffswitch-switch"></span>
                                  </label>
                              </div>
                          </div>
                      </div>


                  </div>
              </div>

          </div>
        </div>
  </div>
</section>
<!-- BEGIN PRELOADER -->

<input type="hidden" id="_lan" name="_lan" value="en">
<!-- END PRELOADER -->
<!-- Mainly scripts -->

<!-- <script src='//code.jquery.com/jquery-1.12.4.js'></script> -->
{!! Html::script('assets/js/jquery-1.10.2.js')  !!}
{!! Html::script('assets/js/vue.js')  !!}
{!! Html::script('assets/js/vue-resource.js') !!}
<script src='{{ asset("assets/ui/theme/ibilling/js/jquery-ui-1.10.4.min.js") }}'></script>
<script>
    var _L = [];

        var config_animate = 'No';
        
_L['Working'] = 'Working';
 
</script>
<script src='{{ asset("assets/ui/theme/ibilling/js/bootstrap.min.js") }}'></script>
<script src='{{ asset("assets/ui/theme/ibilling/js/jquery.metisMenu.js") }}'></script>
<script src='{{ asset("assets/ui/theme/ibilling/js/jquery.slimscroll.min.js") }}'></script>

<script src='{{ asset("assets/ui/lib/moment/moment-with-locales.min.js") }}'></script>

  <script>
      moment.locale('en');
  </script>

<script src='{{ asset("assets/ui/lib/blockui.js") }}'></script>
<script src='{{ asset("assets/ui/lib/app.js") }}'></script>
<script src='{{ asset("assets/ui/lib/toggle/bootstrap-toggle.min.js") }}'></script>

<script src='{{ asset("assets/ui/theme/ibilling/lib/progress.js") }}'></script>
<script src='{{ asset("assets/ui/theme/ibilling/lib/bootbox.min.js") }}'></script>

<!-- iCheck -->
<script src='{{ asset("assets/ui/theme/ibilling/lib/icheck/icheck.min.js") }}'></script>
<script src='{{ asset("assets/ui/theme/ibilling/js/theme.js") }}'></script>

<script src='{{ asset("assets/ui/lib/s2/js/select2.min.js") }}'></script>
<script src='{{ asset("assets/ui/lib/s2/js/i18n/en.js") }}'></script>
<script src='{{ asset("assets/ui/lib/add-contact.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/dp/dist/datepicker.min.js") }}'></script>
        
<!-- <script>
    jQuery(document).ready(function() {
        // initiate layout and plugins

 
        
});
</script> -->

@yield('script')
@stack('scripts')
</body>

</html>
