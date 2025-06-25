@extends('admin.layout.main')

@section('content')

  
    <div class="card">
        <div class="header">
            <h2>
                Money Transfer
            </h2>
            <div style="padding: 20px;">
            <form action="{{ url('sl-admin/seller/upload_money_transfer') }}" method="post" enctype="multipart/form-data">
                @csrf
              
                <input type="file" name="money_transfer_file">
                <button type="submit" class="btn btn-primary" style="margin-top:20px;">Submit</button>
            </form>
        </div>
          
        </div>
        <div class="body">
            <form action="{{ url('sl-admin/seller/export_money_transfer_excel') }}" method="post">
                @csrf
            <button type="submit" class="btn btn-primary">Export Excel</button>
            </form>
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                       
                        <th>Customer ID
                        </th>
                        <th>Transaction Code 
                        </th>
                        <th>Sender Name
                        </th>
                        <th>Receiver name
                        </th>
                      
                       
                    </tr>
                </tbody>
                <tbody>
                  @foreach ($payments as $payment)
                       <tr>
                        <td>{{ $payment->id }}</td>
                        
                        <td>{{ $payment->customer_id }}</td>
                        <td>{{ $payment->transaction_code_number }}</td>
                        <td>{{ $payment->sender_name }}</td>
                        <td>{{ $payment->receiver_name }}</td>
                       
                       </tr>
                       @endforeach
                 
                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush