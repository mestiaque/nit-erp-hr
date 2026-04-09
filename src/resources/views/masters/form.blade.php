@extends('admin.layouts.app')

@section('title')
<title>{{ $entity['title'] }}</title>
@endsection

@section('contents')
<div class="flex-grow-1 p-4">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">{{ $item->exists ? 'Edit' : 'Create' }} {{ $entity['title'] }}</h4>
        </div>
        <div class="card-body">
            <form method="post" action="{{ $item->exists ? route('hr-center.masters.update', [$entityKey, $item->id]) : route('hr-center.masters.store', $entityKey) }}">
                @csrf
                @if($item->exists)
                    @method('put')
                @endif

                <div class="row">
                    @php($formContext = 'page')
                    @include('hr::masters.partials.fields')
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <a href="{{ route('hr-center.masters.index', $entityKey) }}" class="btn btn-light btn-sm">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
