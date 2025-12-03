@extends('layouts.master-admin')

@section('title','Add Embessy')

@section('conten')

<div class="card">
    <div class="card-header bg-white">
        <h3>Add Embessy</h3>
    </div>

<form action="{{ route('embessydata.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-group">
                <label>Embassy Name</label>
                <input type="text" class="form-control" name="embassy_name">
            </div>
        </div>

         <div class="col-md-12">
            <div class="form-group">
                <label>Region</label>
                <select class="form-control" name="province_id" id="province">
                        <option value="0">-Choose Region-</option>
                    @foreach($provinces as $prov)
                        <option value="{{$prov->id}}">{{$prov->provinces_region}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label for="city">Province</label>
                <select name="city" id="city" class="form-control">
                    <option value="">-Choose Province-</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>City</label>
                <select name="district_id" id="district" class="form-control">
                    <option value="">-Choose City-</option>
                </select>
            </div>
        </div>

         <div class="col-md-12">
            <div class="form-group">
                <label>Latitude</label>
                <input type="text" class="form-control" name="latitude">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Longitude</label>
                <input type="text" class="form-control" name="longitude">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location">
            </div>
        </div>

        <div class="col-md-12">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                Telephone
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <textarea id="summernote" name="telephone">
                </textarea>

            </div>

          </div>
        </div>

         <div class="col-md-12">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                Fax
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <textarea id="summernote4" name="fax">
                </textarea>

            </div>

          </div>
        </div>

        <div class="col-md-12">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                Email
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <textarea id="summernote2" name="email">
                </textarea>

            </div>

          </div>
        </div>

        <div class="col-md-12">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                Website
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <textarea id="summernote3" name="website">
                </textarea>

            </div>

          </div>
        </div>

         <div class="col-md-12">
            <div class="form-group">
                <label>Image</label>
                <input type="file" class="form-control" name="image">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
</div>
@endsection

@push('service')
<script>
  $(function () {
    // Summernote
    $('#summernote').summernote()
    $('#summernote2').summernote()
    $('#summernote3').summernote()
    $('#summernote4').summernote()

  })
</script>

<script>
    $('#province').on('change', function () {
        var provinceId = $(this).val();
        if (provinceId) {
            $.ajax({
                url: '/get-cities/' + provinceId,
                type: 'GET',
                success: function (data) {
                    $('#city').empty();
                    $('#city').append('<option value="">-- Choosse Province --</option>');
                    $.each(data, function (key, city) {
                        $('#city').append('<option value="' + city.id + '">' + city.city + '</option>');
                    });
                }
            });
        } else {
            $('#city').empty();
            $('#city').append('<option value="">-- Choosse Province  --</option>');
        }
    });

     $('#city').on('change', function () {
        let cityID = $(this).val();
        $('#district').html('<option value="">Loading...</option>');

        if (cityID) {
            $.ajax({
                url: '/get-districts/' + cityID,
                type: 'GET',
                success: function (data) {
                    $('#district').empty().append('<option value="">-Choose City-</option>');
                    $.each(data, function (key, district) {
                        $('#district').append('<option value="'+ district.id +'">'+ district.sub_city +'</option>');
                    });
                }
            });
        } else {
            $('#district').html('<option value="">-Choose District-</option>');
        }
    });
</script>
@endpush
