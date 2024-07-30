@extends('layouts.main')
@section('title', '')
@section('content')
<form method="get">
  <div class="widget-box">
    <div class="widget-header">
      <h4 class="widget-title">Catatan Tambahan</h4>
    </div>
    <div class="widget-body">
      <div class="widget-main no-padding">
        <div class="form-group row">
          <div class="col-sm-12">
            <textarea class="form-control" style="width: 100% !important;" id="note" name="note" rows="5"></textarea>
          </div>
        </div>
      </div>
      <div class="widget-toolbox padding-8 clearfix">
        <button type="submit" class="btn btn-md btn-success pull-right" id="confirm" name="confirm" value="Y">
          <span class="bigger-110">Setujui</span>
        </button>
      </div>
    </div>
  </div>
</form>
@endsection
@section('custom-js')
@endsection