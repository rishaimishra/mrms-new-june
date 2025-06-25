@extends('admin.layout.main')

@section('content')

  
    <div class="card">
        <div class="header">
            <h2>
                Collection and Payments
            </h2>
            <div style="padding: 20px;">
            <form action="{{ url('sl-admin/upload_payment_collection') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="file" name="payment_collection_file">
                <button type="submit" class="btn btn-primary" style="margin-top:20px;">Submit</button>
            </form>
        </div>
          
        </div>
        <div class="body">
            <form action="{{ url('sl-admin/export_payment_excel') }}" method="post">
                @csrf
            <button type="submit" class="btn btn-primary">Export Excel</button>
            </form>
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                       
                        <th>Seller ID
                        </th>
                        <th>Seller Name
                        </th>
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
                        <th>Item Loaned
                        </th>
                        <th>Balance
                        </th>
                        <th>Action
                        </th>
                    </tr>
                </tbody>
                <tbody>
                  @foreach ($payments as $payment)
                       <tr>
                        <td>{{ $payment->id }}</td>
                        
                        <td>{{ $payment->user->id }}</td>
                        <td>{{ $payment->user->name }}</td>
                        <td>{{ $payment->customer_name }}</td>
                        <td>{{ $payment->telephone }}</td>
                        <td>{{ $payment->nin }}</td>
                        <td>{{ $payment->loan_type }}</td>
                        <td>{{ $payment->loan_term }}</td>
                        <td>{{ $payment->item_loaned }}</td>
                        <td>{{ $payment->balance }}</td>
                        <td>
                            <form action="{{ route('admin.single_export_payment_excel') }}" method="post">
                                @csrf
                                <input type="hidden" name="seller_id" value="{{ $payment->user->id }}">
                            <button type="submit" class="btn btn-primary">Download Excel</button>
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

@push('scripts')

    {{--<script src="{{ url('admin/plugins/morrisjs/morris.js') }}"></script>--}}
    {{--<script src="{{ url('admin/js/pages/charts/morris.js') }}"></script>--}}
    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush