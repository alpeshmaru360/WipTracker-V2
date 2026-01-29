@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/currency_converter.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />

<div class="main_container d-flex">

    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid currency_container">
            <h1>Currency Converter Table [1 EURO]</h1>
            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="table table-bordered text-center  currency-table">
                <thead>
                    <tr>
                        <th>Lable</th>
                        <th>Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1_AED</td>
                        @foreach($currencyConverters as $currency)
                            <td>{{ $currency->{'1_AED'} }}</td>
                        @endforeach
                        <td>
                            <button class="btn btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editAedModal"
                                    data-currency="{{ json_encode($currencyConverters) }}">
                                Edit 
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>1_USD</td>
                        @foreach($currencyConverters as $currency)
                            <td>{{ $currency->{'1_USD'} }}</td>
                        @endforeach
                        <td>
                            <button class="btn btn-primary mt-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editUsdModal"
                                    data-currency="{{ json_encode($currencyConverters) }}">
                                Edit 
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>1_EUR</td>
                        @foreach($currencyConverters as $currency)
                            <td>{{ $currency->{'1_EUR'} }}</td>
                        @endforeach
                        <td>
                            <button class="btn btn-primary mt-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editEurModal"
                                    data-currency="{{ json_encode($currencyConverters) }}">
                                Edit 
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Modal for Editing 1_AED -->
    <div class="modal fade" id="editAedModal" tabindex="-1" aria-labelledby="editAedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editAedForm" method="POST" action="{{ route('currency.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAedModalLabel">Edit AED</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="aedCurrencyId">
                        <div class="mb-3">
                            
                            <input type="text" class="form-control" id="aedInput" name="1_AED">
                            <span id="aedError"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                       
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Editing 1_USD -->
    <div class="modal fade" id="editUsdModal" tabindex="-1" aria-labelledby="editUsdModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editUsdForm" method="POST" action="{{ route('currency.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUsdModalLabel">Edit USD</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="usdCurrencyId">
                        <div class="mb-3">
                           
                            <input type="text" class="form-control" id="usdInput" name="1_USD">
                            <span id="usdError"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                       
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Editing 1_EUR -->
    <div class="modal fade" id="editEurModal" tabindex="-1" aria-labelledby="editEurModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editEurForm" method="POST" action="{{ route('currency.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEurModalLabel">Edit EUR</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="eurCurrencyId">
                        <div class="mb-3">
                            
                            <input type="text" class="form-control" id="eurInput" name="1_EUR">
                            <span id="eurError"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                       
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const editButtons = document.querySelectorAll('.btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const currencyData = JSON.parse(this.dataset.currency);

            if (this.dataset.bsTarget === "#editAedModal") {
                const currency = currencyData[0]; 
                document.getElementById('aedCurrencyId').value = currency.id;
                document.getElementById('aedInput').value = currency['1_AED'];
            }

            if (this.dataset.bsTarget === "#editUsdModal") {
                const currency = currencyData[0]; 
                document.getElementById('usdCurrencyId').value = currency.id;
                document.getElementById('usdInput').value = currency['1_USD'];
            }

            if (this.dataset.bsTarget === "#editEurModal") {
                const currency = currencyData[0]; 
                document.getElementById('eurCurrencyId').value = currency.id;
                document.getElementById('eurInput').value = currency['1_EUR'];
            }
        });
    });

    function validateForm(formId, inputId, errorId) {
        const form = document.getElementById(formId);
        const inputField = document.getElementById(inputId);
        const errorMessage = document.getElementById(errorId);

        form.addEventListener('submit', function (event) {
            if (inputField.value.trim() === "") {
                event.preventDefault(); 
                errorMessage.textContent = "This field is required.";
                errorMessage.style.color = "red";
                inputField.classList.add("is-invalid"); 
            } else {
                errorMessage.textContent = ""; 
                inputField.classList.remove("is-invalid"); 
            }
        });

        inputField.addEventListener("input", function () {
            if (inputField.value.trim() !== "") {
                errorMessage.textContent = "";
                inputField.classList.remove("is-invalid");
            }
        });
    }

    validateForm('editAedForm', 'aedInput', 'aedError');
    validateForm('editUsdForm', 'usdInput', 'usdError');
    validateForm('editEurForm', 'eurInput', 'eurError');
});


</script>


@endsection