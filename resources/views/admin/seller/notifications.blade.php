@extends('admin.layout.main')
<style>
    [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
        position: relative !important;
        left: 0 !important;
        opacity: 1 !important;
    }
</style>
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
                        <th><input type="checkbox" class="filled-in chk-col-blue"></th>
                        <th>ID</th>
                        <th>Notification Type
                        </th>
                        <th>Notification Message
                        </th>
                        <th>
                        </th>
                    </tr>
                </tbody>
                <tbody>
                    @foreach ($notification as $item)
                    <tr>
                        <td><input type="checkbox" class="filled-in chk-col-blue"></td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->app_type }}</td>
                        <td>{{ $item->notification }}</td>
                        <td style="text-align: end;">
                            <button class="btn btn-primary" onclick="toggleButtons(this,{{$item->id}})">View</button>
                          </td>
                       </tr>
                    @endforeach
                       
                       <tr>
                        
                        <td colspan="5">
                        <div class="extra-buttons" style="display: none;text-align:end;" id="extrabtn">
                            <button class="btn btn-sucess">Button 1</button>
                            <button class="btn btn-danger">Button 2</button>
                          </div>
                        </td>
                       </tr>
                </tbody>
               
            </table>
        </div>
        </div>
    </div>

    <!-- Modal HTML -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="responseModalLabel">Notification</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="responseMessage">
        <!-- API response message will go here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


@endsection

@push('scripts')

    {{--<script src="{{ url('admin/plugins/morrisjs/morris.js') }}"></script>--}}
    {{--<script src="{{ url('admin/js/pages/charts/morris.js') }}"></script>--}}
    <script src="{{ url('admin/js/pages/app.js') }}"></script>
    <script>
        function toggleButtons(viewButton,id) {
            console.log(viewButton);
            
            const extraButtons = document.getElementById("extrabtn");
            // Find the sibling div containing the extra buttons
        
            // Toggle the visibility of the extra buttons
            if (extraButtons.style.display === "none") {
                extraButtons.style.display = "block";
            } else {
                extraButtons.style.display = "none";
            }



             // Call the API
    fetch(`/sl-admin/admin/notification/${id}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);

        // Check if the request was successful
        const responseMessageElement = document.getElementById('responseMessage');
        if (data.success) {
            responseMessageElement.innerHTML = data.message;
        } else {
            responseMessageElement.innerHTML = 'An error occurred: ' + data.message;
        }

        // Show the modal
        $('#responseModal').modal('show');
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('responseMessage').innerHTML = 'Failed to retrieve the message.';
        $('#responseModal').modal('show');
    });
}



        </script>
@endpush