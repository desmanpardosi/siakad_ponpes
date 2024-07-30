@extends('layouts.main')
@section('title', __('Kategori'))
@section('custom-css')
@endsection
@section('content')
<div class="row">
  <div class="col-sm-4">
    <div class="widget-box widget-color-blue3">
      <div class="widget-header">
        <h4 class="widget-title lighter smaller">Pilih Kategori e-Book</h4>
      </div>
      <div class="widget-body">
        <div class="widget-main padding-8">
          @if(count($categories) > 0)
          <ul id="tree2" class="tree" role="tree">
            @foreach($categories as $key => $m)
              <li class="tree-branch tree-open" role="treeitem">
                <div class="tree-branch-header" onclick="view('{{ route("home") }}/?category={{ $m->id_category }}')">
                  <span class="tree-branch-name">
                    <i class="icon-folder blue ace-icon fa fa-book"></i>
                    <span class="tree-label">{{ $m->category_name }} ({{ $m->count }})</span>
                  </span>
                </div>
                @if(!empty($m->subcategories))
                <ul class="tree-branch-children" role="group">
                @foreach($m->subcategories as $key2 => $s)
                  <li class="tree-item" role="treeitem" onclick="view('{{ route("home") }}/?category={{ $s["id_category"] }}')">
                    <span class="tree-item-name">
                      <i class="icon-folder blue ace-icon fa fa-book grey"></i>
                      <span class="tree-label">{{ $s["category_name"] }} ({{ $s["count"] }})</span>
                    </span>
                  </li>
                @endforeach
                </ul>
                @endif
              </li>
            @endforeach
          </ul>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('custom-js')
<script>
  function view(url){
      window.open(url, "_self");
  }
</script>
@endsection