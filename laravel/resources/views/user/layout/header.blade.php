<a href="#" class="sidebar-toggle" data-toggleclass="sidebar-open" data-target="body"> </a>
<nav class=" mr-auto my-auto">
    <ul class="nav align-items-center">
        <li class="nav-item">
            <p style="font-size: 22px;padding-top:12px;margin-left: 20px;color:#fff;">Restaurante Manager</b></p>
        </li>
    </ul>
</nav>
<nav class=" ml-auto">
    <ul class="nav align-items-center">
        <li class="nav-item" style="padding-top:12px;">
            @if(Auth::user()->open == 0)
                <a href="{{ Asset('close') }}" onclick="return confirm('Desea Cerrar el comercio?')">
                    <button type="button" class="btn m-b-15 ml-2 mr-2 btn-outline-success">Comercio Abierto</button>
                </a>
            @else
                <a href="{{ Asset('close') }}" onclick="return confirm('Desea abrir el comercio?')">
                    <button type="button" class="btn m-b-15 ml-2 mr-2 btn-outline-danger">Comercio Cerrado</button>
                </a>
            @endif
        </li>
    </ul>
</nav>