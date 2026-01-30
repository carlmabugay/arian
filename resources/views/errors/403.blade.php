@extends('errors::minimal', ['withCTA' => true])

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'We are sorry, but you do not have access to this page.'))
