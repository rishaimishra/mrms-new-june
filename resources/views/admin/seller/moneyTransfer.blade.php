@extends('admin.layout.main')

@section('content')

  
    <div class="card">
        <div class="header">
            <h2>
                Money Transfer
            </h2>
            <div style="padding: 20px;">
            <form action="{{ url('sl-admin/upload_money_transfer') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="seller">Select Seller</label>
                <select name="seller_id" id="seller" class="form-control">
                    <option value="">-- Select a Seller --</option>
                    @foreach ($seller_list as $seller)
                        <option value="{{ $seller->id }}">
                            {{ $seller->name ?? $seller->email ?? 'Unknown Seller' }}
                        </option>
                    @endforeach
                </select>
                <input type="file" name="money_transfer_file">
                <button type="submit" class="btn btn-primary" style="margin-top:20px;">Submit</button>
            </form>
        </div>
          
        </div>
        <div class="body">
            <form action="{{ url('sl-admin/export_money_transfer_excel') }}" method="post">
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
                        <th>Sender Name
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
                        <td>{{ $payment->sender_name }}</td>
                        <td>
                            <form action="{{ route('admin.single_export_moneytrans_excel') }}" method="post">
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

    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush