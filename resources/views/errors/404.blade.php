@extends('errors::minimal', ['withCTA' => true])

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('We couldn\'t find the page you\'re looking for.' ))
