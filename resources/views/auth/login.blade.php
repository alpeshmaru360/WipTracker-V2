@extends('auth.layout')
@section('content')
<section class="midContent loginWrapper" id="midContent">
<div class="container">
<div class="loginWidget br_12">
<div class="d-flex loginWidgetTop">
    <a href="{{url('/')}}"> <img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
</div>
<div class="loginFormWidget" data-aos="fade-left">
<div class="loginFormHeader">
    <h3>Wilo WIP Tracker</h3>
    <h5>Login</h5>
@if($errors->has('email'))
    <p class="alert alert-danger">
        {{ $errors->first('email') }}
    </p>
@endif
@if($errors->has('password'))
    <p class="alert alert-danger">
        {{ $errors->first('password') }}
    </p>
@endif
</div>
<div class="loginFormFields">
<form method="POST" action="{{route('AuthLogin')}}">
    {{ csrf_field() }}
    <div class="formFields fieldTxt">
        <span class="formIcon"><img src="{{asset('fassets/images/loginTxtIcon.png')}}" /></span>
        <input name="email" type="text" class="formInput" placeholder="Enter Email Id">
    </div>

    <div class="formFields fieldPass">
        <span class="formIcon"><img src="{{asset('fassets/images/loginPassIcon.png')}}" /></span>
        <input name="password" type="password" class="formInput" placeholder="Enter Password">
    </div>
   
    
    <div class="loginFormBtn">
        <div class="loginBtn">                            
            <span class="">
            <button type="submit" >Login</button>
            </span>
        </div>                            
    </div>
</form>
</div>
</div>
<div class="">
<!-- <p>© 2021 WILO SE</p> -->
</div>
</div>
</section>
@endsection