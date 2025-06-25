@extends('admin.layout.main')

@section('content')

  
    <div class="card">
        <div class="header">
            <h2>
                Collection and Payments
            </h2>
            <div style="padding: 20px;">
            <form action="{{ url('sl-admin/seller/upload_payment_collection') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="payment_collection_file">
                <button type="submit" class="btn btn-primary" style="margin-top:20px;">Submit</button>
            </form>
        </div>
          
        </div>
        <div class="body">
        <form action="{{ url('sl-admin/seller_export_payment_excel') }}" method="post">
                @csrf
            <button type="submit" class="btn btn-primary">Export Excel</button>
            </form>
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name
                        </th>
                        <th>Telephone
                        </th>
                        <th>NIN
                        </th>
                        <th>Loan Type
                        </th>
                        <th>Loan Term
                        </th>
                        <th>Payment
                        </th>
                        <th>Balance
                        </th>
                    </tr>
                </tbody>
                <tbody>
                  
                @foreach ($payments as $payment)
                       <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->customer_name }}</td>
                        <td>{{ $payment->telephone }}</td>
                        <td>{{ $payment->nin }}</td>
                        <td>{{ $payment->loan_type }}</td>
                        <td>{{ $payment->loan_term }}</td>
                        <td>{{ $payment->payment }}</td>
                        <td>{{ $payment->balance }}</td>
                       </tr>
                       @endforeach
                 
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