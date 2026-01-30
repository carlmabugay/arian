@extends('errors::minimal', ['withCTA' => false])

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('The server encountered an internal error  and was unable to complete your request.'))
