@extends('admin.layout.main')

@section('content')

  
    <div class="card">
        <div class="header">
            <h2>
                Notification
            </h2>
          
        </div>
        <div class="body">
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Notification Type
                        </th>
                        <th>Notification Message
                        </th>
                        <th>Action
                        </th>
                    </tr>
                </tbody>
                <tbody>
                  
                       <tr>
                        <td>1</td>
                        <td>delivery</td>
                        <td>Shipment delivered</td>
                        <td><button>view</button></td>
                       </tr>
                 
                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection

@push('scripts')

    {{--<script src="{{ url('admin/plugins/morrisjs/morris.js') }}"></script>--}}
    {{--<script src="{{ url('admin/js/pages/charts/morris.js') }}"></script>--}}
    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush