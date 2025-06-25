@extends('admin.layout.main')

@section('content')

<form action="{{ route('admin.seller-themes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="theme_name">Theme File</label>
        <input type="file" name="theme_name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload Theme</button>
</form>

<div class="row" style="margin-top:15px">
    @foreach ($sellerThemes as $theme)
        <div class="col-md-2 mb-2">
            <div class="card">
                <div class="card-body text-center">
                    <p class="card-title">Theme ID: {{ $theme->id }}</p>
                    <img src="{{ asset('storage/' . $theme->theme_name) }}" alt="Theme Image" class="img-fluid" width="100">
                    <!-- <div class="mt-2">
                        <a href="{{ route('admin.seller-themes.destroy', $theme->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this theme?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </div> -->

                    <div class="mt-2">
                        <form action="{{ route('admin.seller-themes.destroy', $theme->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this theme?');">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>


@endsection
