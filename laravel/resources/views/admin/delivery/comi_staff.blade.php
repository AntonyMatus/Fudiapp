<h1 style="font-size: 20px">Cargos de comisión para Socios Repartidores</h1>
<div class="card py-3 m-b-30">
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="c_type_staff">Tipo de comisión</label>
                <select name="c_type_staff" class="form-control">
                    <option value="1" @if($data->c_type_staff == 1) selected @endif>valor en %</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="c_value_staff">Valor de la comisión</label>
                <input type="text" name="c_value_staff" id="c_value_staff" value="{{$data->c_value_staff}}" class="form-control">
            </div>
        </div>
    </div>
</div>