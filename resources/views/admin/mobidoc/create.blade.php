@extends('admin.layout.edit')
<!--  -->
@section('content')

<form action="{{ route('admin.movie_doc.add') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
      <div class="header">
        <h2>Create New Service Category</h2>
      </div>

      <div class="body">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <div class="form-line">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" required />
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="form-line">
                <label for="sequence">Sequence</label>
                <input type="number" name="sequence" class="form-control" />
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="form-line">
                <label for="sponsor_text">Sponsor Text</label>
                <input type="text" name="sponsor_text" class="form-control" />
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="header">
        <h2>Subscription and Referal fees</h2>
      </div>

      <div class="body">
        <div class="row">
          <div class="col-sm-6 col-12">
            <div class="form-group">
              <label for="demo-select-1">Pay As You Go plan subscription fee (NLe) </label>
              <select name="demo-select-1" class="form-control">
                <option value="0.00">0.00</option>
                <option value="10.00">10.00</option>
                <option value="15.00">15.00</option>
                <option value="25.00">25.00</option>
                <option value="50.00">50.00</option>
                <option value="100.00">100.00</option>
                <option value="250.00">250.00</option>
                <option value="500.00">500.00</option>
                <option value="750.00">750.00</option>
                <option value="1000.00">1000.00</option>
                <option value="1500.00">1500.00</option>
                <option value="2500.00">2500.00</option>
                <option value="5000.00">5000.00</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-12">
            <div class="form-group">
              <label for="demo-select-2">Pay As You Go plan refferal fee (%)</label>
              <select name="demo-select-2" class="form-control">
                <option value="0.00">0.00</option>
                <option value="5.00">5.00</option>
                <option value="7.50">7.50</option>
                <option value="10.00">10.00</option>
                <option value="12.50">12.50</option>
                <option value="15.00">15.00</option>
                <option value="17.50">17.50</option>
                <option value="20.00">20.00</option>
                <option value="25.00">25.00</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-12">
            <div class="form-group">
              <label for="demo-select-3">MSME plan subscription fee (NLe)</label>
              <select name="demo-select-3" class="form-control">
                <option value="0.00">0.00</option>
                <option value="10.00">10.00</option>
                <option value="15.00">15.00</option>
                <option value="25.00">25.00</option>
                <option value="50.00">50.00</option>
                <option value="100.00">100.00</option>
                <option value="250.00">250.00</option>
                <option value="500.00">500.00</option>
                <option value="750.00">750.00</option>
                <option value="1000.00">1000.00</option>
                <option value="1500.00">1500.00</option>
                <option value="2500.00">2500.00</option>
                <option value="5000.00">5000.00</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-12">
            <div class="form-group">
              <label for="demo-select-4">MSME plan refferal fee (%)</label>
              <select name="demo-select-4" class="form-control">
                <option value="0.00">0.00</option>
                <option value="5.00">5.00</option>
                <option value="7.50">7.50</option>
                <option value="10.00">10.00</option>
                <option value="12.50">12.50</option>
                <option value="15.00">15.00</option>
                <option value="17.50">17.50</option>
                <option value="20.00">20.00</option>
                <option value="25.00">25.00</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-12">
            <div class="form-group">
              <label for="demo-select-5">Business plan subscription fee (NLe)</label>
              <select name="demo-select-5" class="form-control">
                <option value="0.00">0.00</option>
                <option value="10.00">10.00</option>
                <option value="15.00">15.00</option>
                <option value="25.00">25.00</option>
                <option value="50.00">50.00</option>
                <option value="100.00">100.00</option>
                <option value="250.00">250.00</option>
                <option value="500.00">500.00</option>
                <option value="750.00">750.00</option>
                <option value="1000.00">1000.00</option>
                <option value="1500.00">1500.00</option>
                <option value="2500.00">2500.00</option>
                <option value="5000.00">5000.00</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 col-12">
            <div class="form-group">
              <label for="demo-select-6">Business Plan refferal fee (%)</label>
              <select name="demo-select-6" class="form-control">
                <option value="0.00">0.00</option>
                <option value="5.00">5.00</option>
                <option value="7.50">7.50</option>
                <option value="10.00">10.00</option>
                <option value="12.50">12.50</option>
                <option value="15.00">15.00</option>
                <option value="17.50">17.50</option>
                <option value="20.00">20.00</option>
                <option value="25.00">25.00</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
      <div class="body">
        <div class="custom-control custom-checkbox">
          <input type="hidden" name="is_active" value="0" />
          <input type="checkbox" name="is_active" value="1" class="custom-control-input filled-in" id="customCheck1" />
          <label class="custom-control-label" for="customCheck1">Active </label>
        </div>
        <button class="btn btn-primary waves-effect" type="submit">Save</button>
      </div>
    </div>
    <div class="card">
      <div class="body">
        <label for="image">Image</label>
        <input type="file" name="mobi_image" id="image" />
      </div>
    </div>

    <div class="card">
      <div class="body">
        <label for="background_images">Background image</label>
        <input name="background_image" type="file" id="background_images" />
      </div>
    </div>
  </div>
</form>

@stop
