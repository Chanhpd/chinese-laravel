@extends('layouts.admin')

@section('title', 'Edit Vocabulary - ' . $vocabulary->word)

@section('content')
@include('admin.vocabularies.create', ['vocabulary' => $vocabulary])
@endsection
