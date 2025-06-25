@extends('admin.layout.grid')

@section('grid-title')
    <h2>EDSA UTILITIES</h2>
@endsection

@section('grid-content')
<div class="card">
    <div class="body">
        <div style="display: flex; justify-content: space-between;">
            <a style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 2px;"
               href="/sl-admin/savedmeter">MY SAVED METERS</a>
            <a style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 2px;"
               href="/sl-admin/edsatransaction">MY TRANSACTIONS</a>
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: space-between;">
            <input id="meterNumberInput" placeholder="Meter Number" style="width: 90%; padding: 10px;" />
            <button id="openModalBtn"
                    style="width: 9%; color: white; background-color: green; border: none; border-radius: 2px; padding: 10px 20px;">SAVE AS
            </button>
        </div>
        <div style="margin-top: 10px; display: flex; justify-content: space-between;"></div>
        <input placeholder="Paying Amount" style="width: 100%; padding: 10px;margin-bottom: 10px;" />
        <input placeholder="Receipient Mobile Number"
               style="width: 100%; padding: 10px;margin-bottom: 10px;" />

        <div id="paypal-button-container" style="margin-top: 20px; margin-left: auto; margin-right: auto; width: 30%;"></div>

        <div style="margin-top: 20px; display: flex; flex-direction: column; align-items: center;">
            <button id="internetBankingBtn" onclick="handlePaymentMethodChange('internetBanking')" style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 8px; width: 30%; margin-bottom: 10px;">
                Internet Banking
            </button>
            <button id="mobileBankingBtn" onclick="handlePaymentMethodChange('mobileBanking')" style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 8px; width: 30%;">
                Mobile Banking
            </button>
        </div>


        <div id="internetBankList" style="display: none; margin-top: 20px;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-around;">
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/bank.png') }}" alt="Bank 1" style="width: 100%;">
                    Bank 1
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/bank.png') }}" alt="Bank 2" style="width: 100%;">
                    Bank 2
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/bank.png') }}" alt="Bank 2" style="width: 100%;">
                    Bank 3
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/bank.png') }}" alt="Bank 2" style="width: 100%;">
                    Bank 4
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/bank.png') }}" alt="Bank 2" style="width: 100%;">
                    Bank 5
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/bank.png') }}" alt="Bank 2" style="width: 100%;">
                    Bank 6
                </div>
                <!-- Add more banks similarly -->
            </div>
        </div>

        <div id="mobileBankList" style="display: none; margin-top: 20px;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-around;">
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/mmo.png') }}" alt="MMO 1" style="width: 100%;">
                    MMO 1
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/mmo.png') }}" alt="MMO 2" style="width: 100%;">
                    MMO 2
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/mmo.png') }}" alt="MMO 2" style="width: 100%;">
                    MMO 3
                </div>
                <div class="bank-card" style="padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 4px; width: 30%;">
                    <img src="{{ asset('admin/images/mmo.png') }}" alt="MMO 2" style="width: 100%;">
                    MMO 4
                </div>
                <!-- Add more mobile banking options similarly -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<form action="{{ route('admin.save.meter') }}" method="POST">
    @csrf
    <div id="myModal" class="modal">
        <div class="modal-content" style="width: 50%;">
            <span class="close">&times;</span>
            <h3>Add Meter</h3>
            <!-- Hidden input field to hold the meter number value -->
            <input type="hidden" name="meter_number" id="meterNumberHidden">
            <input type="text" name="meter_name" placeholder="Meter Name" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <input type="text" name="user_name" placeholder="User Name" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <div style="text-align: center;">
                <button type="submit" id="submitBtn" style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 2px;">Submit</button>
            </div>
        </div>
    </div>
</form>

<style>
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Higher z-index */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        z-index: 1001; /* Ensure modal content is above other elements */
        width: 50%;
    }

    /* The Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #paypal-button-container {
        z-index: 500;
    }

    button img {
        vertical-align: middle;
        margin-right: 10px;
    }

    input[type="radio"] {
        margin-right: 10px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModalBtn");
        var span = document.getElementsByClassName("close")[0];
        var meterNumberInput = document.getElementById("meterNumberInput");
        var meterNumberHidden = document.getElementById("meterNumberHidden");

        btn.onclick = function () {
            modal.style.display = "block";
            // Get the value of the Meter Number input field
            var meterNumberValue = meterNumberInput.value;
            // Set the value of the hidden input field in the modal
            meterNumberHidden.value = meterNumberValue;
        }

        span.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        var submitBtn = document.getElementById("submitBtn");
        submitBtn.onclick = function () {
            modal.style.display = "none";
        }
    });

    function handlePaymentMethodChange(paymentMethod) {
    var internetBankList = document.getElementById("internetBankList");
    var mobileBankList = document.getElementById("mobileBankList");

    // Hide both lists initially
    internetBankList.style.display = "none";
    mobileBankList.style.display = "none";

    // Deselect all buttons
    document.getElementById("internetBankingBtn").style.backgroundColor = "#3a61c4";
    document.getElementById("mobileBankingBtn").style.backgroundColor = "#3a61c4";

    // Select the clicked button
    if (paymentMethod === "internetBanking") {
        document.getElementById("internetBankingBtn").style.backgroundColor = "#2a51a4";
        internetBankList.style.display = "flex";
    } else if (paymentMethod === "mobileBanking") {
        document.getElementById("mobileBankingBtn").style.backgroundColor = "#2a51a4";
        mobileBankList.style.display = "flex";
    }
}

</script>

<script src="https://www.paypal.com/sdk/js?client-id=test&currency=USD"></script>
<script>
    // Render the PayPal button into #paypal-button-container
    paypal.Buttons({

        // Call your server to set up the transaction
        createOrder: function(data, actions) {
            return fetch('/demo/checkout/api/paypal/order/create/', {
                method: 'post'
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                return orderData.id;
            });
        },

        // Call your server to finalize the transaction
        onApprove: function(data, actions) {
            return fetch('/demo/checkout/api/paypal/order/' + data.orderID + '/capture/', {
                method: 'post'
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                // Three cases to handle:
                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                //   (2) Other non-recoverable errors -> Show a failure message
                //   (3) Successful transaction -> Show confirmation or thank you

                // This example reads a v2/checkout/orders capture response, propagated from the server
                // You could use a different API or structure for your 'orderData'
                var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart(); // Recoverable state, per:
                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                }

                if (errorDetail) {
                    var msg = 'Sorry, your transaction could not be processed.';
                    if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                    if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                    return alert(msg); // Show a failure message (try to avoid alerts in production environments)
                }

                // Successful capture! For demo purposes:
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
                alert('Transaction ' + transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

                // Replace the above to show a success message within this page, e.g.
                // const element = document.getElementById('paypal-button-container');
                // element.innerHTML = '';
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
            });
        }

    }).render('#paypal-button-container');
</script>
@endsection
