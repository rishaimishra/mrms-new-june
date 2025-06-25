@extends('admin.layout.main')

@section('content')

    <div class="card">
        <h5 style="padding: 20px">Upload file</h5>
        <div style="padding: 20px;">
            <form action="{{ url('sl-admin/seller/upload-sea-air-frights') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="sea_air_freight_file">
                <button type="submit" class="btn btn-primary" style="margin-top:20px;">Submit</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="header">
            <h2>
                Sea and air frieghts
            </h2>

            <form action="{{ url('sl-admin/seller/freightExport') }}" method="post">
                @csrf
            <button type="submit" class="btn btn-primary">Export Excel</button>
            </form>

        </div>
        <div class="body">
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Container No</th>
                        <!-- <th>Seller Name</th> -->
                        <th>Date Uploaded</th>
                        <th>Region</th>
                        <th>Transport Type</th>
                        <th>Action</th> 
                    </tr>
                </tbody>
                <tbody>
                   @foreach ($sea_frieghts as $freight)
                       <tr>
                        <td>{{ $freight->id }}</td>
                        <td>{{ $freight->container_batch_no }}</td>
                        <!-- <td>{{ $freight->seller_name }}</td> -->
                        <td>{{ $freight->created_at }}</td>
                        <td>{{ $freight->region }}</td>
                        <td>{{ $freight->transport_type }}</td>
                        <td>
                            <form action="{{ url('sl-admin/single_export_frieght_excel_seller') }}" method="post">
                                @csrf
                                <input type="hidden" name="container_batch_no" value="{{ $freight->container_batch_no }}">
                            <button type="submit" class="btn btn-primary">Export Excel</button>
                            </form>
                        </td>
                       </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection
