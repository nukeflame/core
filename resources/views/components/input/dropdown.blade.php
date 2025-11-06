<select name="make" id="make" class="form-control checkempty make" onchange="changeMake(this.value)" required>
    <option value="">Vehicle Make</option>
    @foreach ($models as $model)
        <option value="{{ $model->make }}">{{ $model->make }}</option>
    @endforeach
</select>
