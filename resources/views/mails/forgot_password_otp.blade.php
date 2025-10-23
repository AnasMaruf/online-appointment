@extends('mails.layout') @section('content') Hi {{ $user->name }},<br />

Anda melakukan request lupa password, berikut OTP: {{ $otp }}, <br />
Jika Anda tidak melakukan request lupa password, abaikan email ini. @endsection
