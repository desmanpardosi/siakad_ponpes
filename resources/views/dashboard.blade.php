@extends('layouts.main')
@section('title', __('Dasbor'))
@section('content')
        <form class="form-search">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="ace-icon fa fa-book"></i>
                        </span>

                        <input type="text" class="form-control" placeholder="Cari e-Book...">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-sm">
                                <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                Cari
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </form>
@endsection
@section('custom-js')

@endsection