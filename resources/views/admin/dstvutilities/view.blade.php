@extends('admin.layout.grid')

@section('grid-title')
    <h2> DSTV UTILITIES </h2>
@endsection

@section('grid-content')
    <div class="card">
        <div class="body">
            <div style="display: flex; justify-content: space-between;">
                <a style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 2px;"
                   href="/sl-admin/saveddstvrechargecard">Saved DSTV Recharge Card</a>
                <a style="color:white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 2px;"
                   href="/sl-admin/dstvtransaction">MY TRANSACTIONS</a>
            </div>
            <div style="margin-top: 20px; display: flex; justify-content: space-between;">
                <input id="meterNumberInput" placeholder="Recharge Card Number" style="width: 90%; padding: 10px;"/>
                <button id="openModalBtn"
                        style="width: 9%; color: white; background-color: green; border: none; border-radius: 2px; padding: 10px 20px;">SAVE
                    AS
                </button>
            </div>
            <div id="detailsBox" style="margin-top: 10px; display: none; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                <p><b>Name:</b> Salieu Kamara</p>
                <p><b>Current Bouquet:</b> DFDFDFDFRESDADS</p>
                <p><b>Due Date:</b> 2022-09-20</p>
                <p><b>Subscription Amount:</b> NLe 625362</p>
            </div>
            <div id="dropdownContainer" style="margin-top: 10px; display: none; position: relative;">
                <div id="dropdownPlaceholder" style="width: 100%; padding: 10px; background-color: #fff; border: 1px solid #ddd; cursor: pointer;">
                    What do you want to do?
                </div>
                <div id="dropdownOptions" style="display: none; position: absolute; width: 100%; background-color: #fff; border: 1px solid #ddd; border-top: none; z-index: 1;">
                    <div class="dropdown-option" style="padding: 10px; cursor: pointer;" data-value="CHANGE CURRENT BOUQUET">CHANGE CURRENT BOUQUET</div>
                    <div class="dropdown-option" style="padding: 10px; cursor: pointer;" data-value="RENEW CURRENT BOUQUET">RENEW CURRENT BOUQUET</div>
                </div>
            </div>
            <div id="bouquetDropdownContainer" style="margin-top: 10px; display: none; position: relative;">
                <div id="bouquetDropdownPlaceholder" style="width: 100%; padding: 10px; background-color: #fff; border: 1px solid #ddd; cursor: pointer;">
                    Select a Bouquet
                </div>
                <div id="bouquetDropdownOptions" style="display: none; position: absolute; width: 100%; background-color: #fff; border: 1px solid #ddd; border-top: none; z-index: 1;">
                    <div class="bouquet-dropdown-option" style="padding: 10px; cursor: pointer;" data-value="75000">Premium</div>
                    <div class="bouquet-dropdown-option" style="padding: 10px; cursor: pointer;" data-value="535000">Compact Plus</div>
                    <div class="bouquet-dropdown-option" style="padding: 10px; cursor: pointer;" data-value="340000">Compact</div>
                    <div class="bouquet-dropdown-option" style="padding: 10px; cursor: pointer;" data-value="200000">Confam</div>
                    <div class="bouquet-dropdown-option" style="padding: 10px; cursor: pointer;" data-value="115000">Yanga</div>
                    <div class="bouquet-dropdown-option" style="padding: 10px; cursor: pointer;" data-value="85000">Padi</div>
                </div>
            </div>
            <input placeholder="Paying Amount" style="width: 100%; padding: 10px; margin-bottom: 10px; margin-top: 10px;"/>
            <input placeholder="Enter E-Mail" style="width: 100%; padding: 10px;margin-bottom: 10px;margin-top: 10px;"/>
            <input placeholder="Recipient Mobile Number" style="width: 100%; padding: 10px; margin-bottom: 10px;"/>
            <div id="paypal-button-container" style="margin-top: 20px; margin-left: auto; margin-right: auto; width: 30%; justify-content: space-between; z-index: 500;"></div>
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
    <form action="{{ route('admin.recharge.card') }}" method="POST">
    @csrf
    <div id="myModal" class="modal">
        <div class="modal-content" style="width: 50%;">
            <span class="close">&times;</span>
            <h3>Add Meter</h3>
            <input type="hidden" name="recharge_card_number" id="meterNumberHidden">
            <input type="text" name="recharge_card_name" placeholder="Recharge Card Name" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <input type="text" name="user_name" placeholder="User Name" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <div style="text-align: center;">
                <button type="submit" id="submitBtn" style="color: white; background-color: #3a61c4; padding: 10px 20px; border: none; border-radius: 2px;">Submit</button>
            </div>
        </div>
    </div>
</form>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            z-index: 1001;
            width: 50%;
        }

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
    </style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("openModalBtn");
    var span = document.getElementsByClassName("close")[0];
    var meterNumberInput = document.getElementById("meterNumberInput");
    var meterNumberHidden = document.getElementById("meterNumberHidden");
    var detailsBox = document.getElementById("detailsBox");
    var dropdownContainer = document.getElementById("dropdownContainer");
    var dropdownPlaceholder = document.getElementById("dropdownPlaceholder");
    var dropdownOptions = document.getElementById("dropdownOptions");
    var dropdownOptionItems = document.querySelectorAll('.dropdown-option');
    var bouquetDropdownContainer = document.getElementById("bouquetDropdownContainer");
    var bouquetDropdownPlaceholder = document.getElementById("bouquetDropdownPlaceholder");
    var bouquetDropdownOptions = document.getElementById("bouquetDropdownOptions");
    var bouquetDropdownOptionItems = document.querySelectorAll('.bouquet-dropdown-option');

    btn.onclick = function () {
        modal.style.display = "block";
        var meterNumberValue = meterNumberInput.value;
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

    meterNumberInput.addEventListener('input', function() {
        if (meterNumberInput.value.length >= 6) {
            detailsBox.style.display = 'block';
            dropdownContainer.style.display = 'block';
        } else {
            detailsBox.style.display = 'none';
            dropdownContainer.style.display = 'none';
            bouquetDropdownContainer.style.display = 'none'; // Hide the second dropdown if the input length is less than 6
        }
    });

    var submitBtn = document.getElementById("submitBtn");
    submitBtn.onclick = function () {
        modal.style.display = "none";
    }

    dropdownPlaceholder.onclick = function () {
        if (dropdownOptions.style.display === "none") {
            dropdownOptions.style.display = "block";
        } else {
            dropdownOptions.style.display = "none";
        }
    }

    dropdownOptionItems.forEach(function(item) {
        item.onclick = function() {
            dropdownPlaceholder.innerText = this.innerText;
            dropdownPlaceholder.dataset.value = this.dataset.value;
            dropdownOptions.style.display = "none";
            if (this.dataset.value === "CHANGE CURRENT BOUQUET") {
                bouquetDropdownContainer.style.display = "block";
            } else {
                bouquetDropdownContainer.style.display = "none";
            }
        }
    });

    bouquetDropdownPlaceholder.onclick = function () {
        if (bouquetDropdownOptions.style.display === "none") {
            bouquetDropdownOptions.style.display = "block";
        } else {
            bouquetDropdownOptions.style.display = "none";
        }
    }

    bouquetDropdownOptionItems.forEach(function(item) {
        item.onclick = function() {
            bouquetDropdownPlaceholder.innerText = this.innerText;
            bouquetDropdownPlaceholder.dataset.value = this.dataset.value;
            bouquetDropdownOptions.style.display = "none";
            
            // Update the paying amount input field with the selected bouquet's data value
            var selectedBouquetValue = parseFloat(this.dataset.value); // Parse the value to a floating-point number
            var payingAmountInput = document.querySelector('input[placeholder="Paying Amount"]');
            payingAmountInput.value = selectedBouquetValue.toFixed(2); // Set the value with two decimal places
        }
    });

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
    paypal.Buttons({
        createOrder: function(data, actions) {
            return fetch('/demo/checkout/api/paypal/order/create/', {
                method: 'post'
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                return orderData.id;
            });
        },
        onApprove: function(data, actions) {
            return fetch('/demo/checkout/api/paypal/order/' + data.orderID + '/capture/', {
                method: 'post'
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                var errorDetail = Array.isArray(orderData.details) && orderData.details[0];
                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart();
                }
                if (errorDetail) {
                    var msg = 'Sorry, your transaction could not be processed.';
                    if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                    if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                    return alert(msg);
                }
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
                alert('Transaction '+ transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');
            });
        }
    }).render('#paypal-button-container');
</script>

@endsection
