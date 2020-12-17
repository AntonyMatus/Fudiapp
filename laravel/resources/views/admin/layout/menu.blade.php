@inject('admin', 'App\Admin')


@php($page = Request::segment(2))
<style type="text/css">
.menu-item{
	border-bottom: 1px solid #222222 !important;
}
</style>
<div class="admin-sidebar-brand">
	<!-- begin sidebar branding-->
	<!-- <img class="admin-brand-logo" src="{{Asset('assets/img/icon.png') }}" width="40" alt="admin Logo"><br /> -->
	<span class="admin-brand-content font-secondary">
		<a href="{{ Asset(env('admin').'/home') }}">  {{ Auth::guard('admin')->user()->name }}</a>
	</span>
	<!-- end sidebar branding-->
	<div class="ml-auto">
		<!-- sidebar pin-->
		<a href="#" class="admin-pin-sidebar btn-ghost btn btn-rounded-circle"></a>
		<!-- sidebar close for mobile device-->
		<a href="#" class="admin-close-sidebar"></a>
	</div>
</div>
<div class="admin-sidebar-wrapper js-scrollbar">
<ul class="menu">
<li class="menu-item @if($page === 'home' || $page == 'setting') active @endif">
<a href="#" class="open-dropdown menu-link">
<span class="menu-label">
<span class="menu-name">Dashboard
<span class="menu-arrow"></span>
</span>

</span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-shape-outline "></i>
</span>
</a>
<!--submenu-->
<ul class="sub-menu">
    @if($admin->hasPerm('Dashboard - Inicio'))
<li class="menu-item">
<a href="{{ Asset(env('admin').'/home') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Inicio</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-home">
</i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Dashboard - Configuraciones'))
<li class="menu-item ">
<a href="{{ Asset(env('admin').'/setting') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Configuraciones</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-message-settings-variant">

</i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Dashboard - Categorias'))
<li class="menu-item ">
<a href="{{ Asset(env('admin').'/category') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Categorias</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-message-settings-variant">

</i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Dashboard - Textos de la aplicacion'))
<li class="menu-item ">
<a href="{{ Asset(env('admin').'/text/add') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Texto de la aplicación</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-message-settings-variant">

</i>
</span>
</a>
</li>
@endif
</ul>
</li>



@if($admin->hasPerm('Subaccount'))
<li class="menu-item @if($page === 'adminUser') active @endif">
    <a href="{{ Asset(env('admin').'/adminUser') }}" class="menu-link">
    <span class="menu-label"><span class="menu-name">Administrar SubCuentas</span></span>
    <span class="menu-icon">
     <i class="mdi mdi-map-marker"></i>
    </span>
    </a>
    </li>
@endif

@if($admin->hasPerm('Banners'))
<li class="menu-item @if($page === 'slider' || $page == 'banner') active @endif">
<a href="#" class="open-dropdown menu-link">
<span class="menu-label">
<span class="menu-name">Banners
<span class="menu-arrow"></span>
</span>

</span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-image-filter "></i>
</span>
</a>
<!--submenu-->
<ul class="sub-menu">
<li class="menu-item">
<a href="{{ Asset(env('admin').'/slider') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Slider de Bienvenida</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-image-filter">

</i>
</span>
</a>
</li>

<li class="menu-item ">
<a href="{{ Asset(env('admin').'/banner') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Banners</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-image">

</i>
</span>
</a>
</li>
</ul>
</li>
@endif

@if($admin->hasPerm('Administrar Ciudades'))
<li class="menu-item @if($page === 'city') active @endif">
<a href="{{ Asset(env('admin').'/city') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Administrar ciudades</span></span>
<span class="menu-icon">
 <i class="mdi mdi-map-marker"></i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Paginas de la aplicacion'))
<li class="menu-item @if($page === 'page') active @endif">
<a href="{{ Asset(env('admin').'/page/add') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Páginas de aplicaciones</span></span>
<span class="menu-icon">
 <i class="mdi mdi-file"></i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Adminisrtar Restaurantes'))
<li class="menu-item @if($page === 'user') active @endif">
<a href="{{ Asset(env('admin').'/user') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Administrar restaurantes</span></span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-home"></i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Ofertas de descuento'))
<li class="menu-item @if($page === 'offer') active @endif">
<a href="{{ Asset(env('admin').'/offer') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Ofertas de descuento</span></span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-calendar"></i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Repartidores'))
<!-- Repartidores -->
<li class="menu-item @if($page === 'delivery' || $page == 'report_staff') active @endif">
	<a href="#" class="open-dropdown menu-link">
		<span class="menu-label">
			<span class="menu-name">Repartidores
				<span class="menu-arrow"></span>
			</span>
		</span>
		<span class="menu-icon">
			<i class="mdi mdi-account-clock"></i>
		</span>
	</a>
<!--submenu-->
<ul class="sub-menu">
	<li class="menu-item">
		<a href="{{ Asset(env('admin').'/delivery') }}" class=" menu-link">
			<span class="menu-label">
				<span class="menu-name">Listado</span>
			</span>
			<span class="menu-icon">
				<i class="icon-placeholder  mdi mdi-image-filter">
			</i>
			</span>
		</a>
	</li>

<li class="menu-item ">
	<a href="{{ Asset(env('admin').'/report_staff') }}" class=" menu-link">
		<span class="menu-label">
			<span class="menu-name">Reportes</span>
		</span>
		<span class="menu-icon">
			<i class="icon-placeholder  mdi mdi-image">
		</i>
		</span>
	</a>
</li>
</ul>
</li>
<!-- Repartidores -->
@endif


@if($admin->hasPerm('Gestion de pedidos'))
<li class="menu-item @if($page === 'order') active @endif">
<a href="#" class="open-dropdown menu-link">
<span class="menu-label">
<span class="menu-name">Gestionar pedidos

<?php
$cOrder = DB::table('orders')->where('status',0)->count();
$rOrder = DB::table('orders')->where('status',1)->count();
if($cOrder > 0)
{
?>

<span class="icon-badge badge-success badge badge-pill">{{ $cOrder }}</span>

<?php } ?>

<span class="menu-arrow"></span>
</span>

</span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-cart"></i>
</span>
</a>
<!--submenu-->
<ul class="sub-menu">

<li class="menu-item">
<a href="{{ Asset(env('admin').'/order?status=0') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Pedidos Nuevos

@if($cOrder > 0)

<span class="icon-badge badge-success badge badge-pill">{{ $cOrder }}</span>

@endif

</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-cart">

</i>
</span>
</a>
</li>


<li class="menu-item">
<a href="{{ Asset(env('admin').'/order?status=1') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Pedidos en Curso

@if($rOrder > 0)

<span class="icon-badge badge-success badge badge-pill">{{ $rOrder }}</span>

@endif

</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-camera-control">

</i>
</span>
</a>
</li>

<li class="menu-item">
<a href="{{ Asset(env('admin').'/order?status=2') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Pedidos Cancelados</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-cancel">

</i>
</span>
</a>
</li>

<li class="menu-item">
<a href="{{ Asset(env('admin').'/order?status=5') }}" class=" menu-link">
<span class="menu-label">
<span class="menu-name">Pedidos Completos</span>
</span>
<span class="menu-icon">
<i class="icon-placeholder  mdi mdi-check-all">

</i>
</span>
</a>
</li>
</ul>
</li>
@endif

@if($admin->hasPerm('Notificaciones push'))
<li class="menu-item @if($page === 'slider' || $page == 'banner') active @endif">
    <a href="#" class="open-dropdown menu-link">
    <span class="menu-label">
    <span class="menu-name">Notificaciones
    <span class="menu-arrow"></span>
    </span>

    </span>
    <span class="menu-icon">
    <i class="icon-placeholder mdi mdi-send "></i>
    </span>
    </a>
    <!--submenu-->
    <ul class="sub-menu">
        <li class="menu-item @if($page === 'push') active @endif">
            <a href="{{ Asset(env('admin').'/push') }}" class="menu-link">
            <span class="menu-label"><span class="menu-name">Notificaciones Push</span></span>
            <span class="menu-icon">
            <i class="icon-placeholder mdi mdi-send"></i>
            </span>
            </a>
        </li>
        @if($admin->hasPerm('Dashboard - Editar Notificaciones'))
        <li class="menu-item ">
            <a href="{{ Asset(env('admin').'/EditNotif') }}" class=" menu-link">
            <span class="menu-label">
            <span class="menu-name">Editar Notificaciones</span>
            </span>
            <span class="menu-icon">
            <i class="icon-placeholder mdi mdi-send">
            </i>
            </span>
            </a>
        </li>
        @endif
    </ul>
    </li>
@endif



@if($admin->hasPerm('Reportes de ventas'))
<li class="menu-item @if($page === 'report') active @endif">
<a href="{{ Asset(env('admin').'/report') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Reporte de ventas</span></span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-file"></i>
</span>
</a>
</li>
@endif

@if($admin->hasPerm('Usuarios Registrados'))
<li class="menu-item @if($page === 'appUser') active @endif">
<a href="{{ Asset(env('admin').'/appUser') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Usuarios Registrados</span></span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-account"></i>
</span>
</a>
</li>
@endif




<li class="menu-item">
<a href="{{ Asset(env('admin').'/logout') }}" class="menu-link">
<span class="menu-label"><span class="menu-name">Cerrar Sesion</span></span>
<span class="menu-icon">
<i class="icon-placeholder mdi mdi-logout"></i>
</span>
</a>
</li>

</ul>
</div>
