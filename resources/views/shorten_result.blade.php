@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/shorten_result.css' />
@endsection

@section('content')
<h3>Shortened URL</h3>
<input type='text' class='result-box form-control' value='{{$short_url}}' />
<a href='{{route('index')}}' class='btn btn-info back-btn'>Shorten another</a>
<img src="{{$screenshot}}" alt="" />
Title: {{$title}}
<br />
Description: {{$description}}
<br />
<img src="{{$image}}" />
@endsection

@section('js')
<script src='/js/shorten_result.js'></script>
@endsection
