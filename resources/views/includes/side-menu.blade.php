<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                @if(Auth::user()->hasAvatar())
                    <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                @else
                    <div class="image-fill img-circle image-fixed-size"></div>
                @endif
            </div>
            <div class="pull-left info">
                <p>{{Auth::user()->name}}</p>
                <a href="javascript:void(0)"><i class="fa fa-circle text-success"></i>Online</a>
            </div>
        </div>

        <!-- search form (Optional) -->
        {{--<form action="#" method="get" class="sidebar-form">--}}
            {{--<div class="input-group">--}}
                {{--<input type="text" name="q" class="form-control" placeholder="Search...">--}}
                {{--<span class="input-group-btn">--}}
                {{--<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>--}}
                {{--</button>--}}
              {{--</span>--}}
            {{--</div>--}}
        {{--</form>--}}
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">Admin menu</li>
            <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="{{route('dashboard')}}"><i class="fa fa-dashboard"></i>&nbsp;<span>Dashboard</span></a>
            </li>
            <li class="{{ Request::is('converters') ? 'active' : '' }}">
                <a href="{{route('converters')}}"><i class="fa fa-link"></i>&nbsp;<span>Converters</span></a>
            </li>
            <li class="{{ Request::is('sets-rules') ? 'active' : '' }}">
                <a href="{{route('sets-rules')}}"><i class="fa fa-link"></i>&nbsp;<span>Advance Converter</span></a>
            </li>

            <li class="{{ Request::is('ubi-upload') ? 'active' : '' }}">
                <a href="{{route('ubi-upload')}}"><i class="fa fa-link"></i>&nbsp;<span>Ubi Upload</span></a>
            </li>

            <li class="{{ Request::is('internal-generate') ? 'active' : '' }}">
                <a href="{{route('internal-generate')}}"><i class="fa fa-link"></i>&nbsp;<span>Internal Generate</span></a>
            </li>

            <li class="{{ Request::is('products-index') ? 'active' : '' }}">
                <a href="{{route('products-index')}}"><i class="fa fa-link"></i>&nbsp;<span>Product List & Pricing</span></a>
            </li>

            <li class="{{ Request::is('invoice-orders-search') ? 'active' : '' }}">
                <a href="{{route('invoice-orders-search')}}"><i class="fa fa-link"></i>&nbsp;<span>Order Search</span></a>
            </li>
			
			<li class="treeview {{ (Request::is('invoice-orders') || Request::is('uploaded-orders') || Request::is('invoicing-index')) ? 'active menu-open' : '' }}">
			  <a href="#">
				<i class="fa fa-table"></i> <span>Invoicing Tools</span>
				<span class="pull-right-container">
					  <i class="fa fa-angle-left pull-right"></i>
					</span>
			  </a>
			  <ul class="treeview-menu {{ (Request::is('invoice-orders') || Request::is('uploaded-orders') || Request::is('invoicing-index') || Request::is('invoiced-index')) ? 'menu-open' : '' }}" style="{{ (Request::is('invoice-orders') || Request::is('uploaded-orders') || Request::is('invoicing-index') || Request::is('invoiced-index')) ? 'display: block' : 'display: none' }};">
				<li class="{{ Request::is('invoice-orders') ? 'active' : '' }}">
					<a href="{{route('invoice-orders')}}"><i class="fa fa-link"></i>&nbsp;<span>Invoice Orders</span></a>
				</li>

				<li class="{{ Request::is('uploaded-orders') ? 'active' : '' }}">
					<a href="{{route('uploaded-orders')}}"><i class="fa fa-link"></i>&nbsp;<span>Upload Order Data</span></a>
				</li>

				<li class="{{ Request::is('invoicing-index') ? 'active' : '' }}">
					<a href="{{route('invoicing-index')}}"><i class="fa fa-link"></i>&nbsp;<span>Generate Invoice</span></a>
				</li>

				<li class="{{ Request::is('invoiced-index') ? 'active' : '' }}">
					<a href="{{route('invoiced-index')}}"><i class="fa fa-link"></i>&nbsp;<span>Invoiced Update</span></a>
				</li>
			  </ul>
			</li>
			
			{{--
            <li class="{{ Request::is('check-pdf') ? 'active' : '' }}">
                <a href="{{route('check-pdf')}}"><i class="fa fa-link"></i>&nbsp;<span>Order check Pdf</span></a>
            </li>
			--}}
			{{--
            <li class="{{ Request::is('converter-csv') ? 'active' : '' }}">
                <a href="{{route('converter-csv')}}"><i class="fa fa-link"></i>&nbsp;<span>Convert CSV</span></a>
            </li>
            <li class="{{ Request::is('tracking') ? 'active' : '' }}">
                <a href="{{route('tracking')}}"><i class="fa fa-link"></i>&nbsp;<span>Tracking</span></a>
            </li>
			--}}
            {{--<li class="treeview">--}}
                {{--<a href="#">--}}
                    {{--<i class="fa fa-link"></i>--}}
                    {{--<span>Multilevel</span>--}}
                    {{--<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>--}}
                {{--</a>--}}
                {{--<ul class="treeview-menu">--}}
                    {{--<li><a href="#">Link in level 2</a></li>--}}
                    {{--<li><a href="#">Link in level 2</a></li>--}}
                {{--</ul>--}}
            {{--</li>--}}
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>