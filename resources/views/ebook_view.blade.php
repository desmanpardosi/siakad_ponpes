@extends('layouts.main')
@section('title', $ebook->title)
@section('content')
<div class="col-sm-12">
    <div class="widget-box">
      <div class="widget-header widget-header-small">
        <h5 class="widget-title">Dilihat: <b>{{ $ebookSeen }}x</b></h5>
        <div class="widget-toolbar">
          <div class="btn-group" role="group">
            <a class="btn-primary btn-sm btn" type="button" id="prev"><span class="fa fa-chevron-left"></span></a>
            <span style="float:left;padding: 0px 10px 0px 10px;">Page: <select class="no-padding" id="page_num"></select> / <span id="page_count"></span></span>
            <a class="btn-primary btn-sm btn" type="button" id="next"><span class="fa fa-chevron-right"></span></a>
          </div>
          <div class="btn-group" role="group">
            <a class="btn-primary btn-sm btn" type="button" id="zoomout"><span class="fa fa-search-minus"></span></a>
            <a class="btn-default btn-sm btn"><span id="scale"></span></a>
            <a class="btn-primary btn-sm btn" type="button" id="zoomin"><span class="fa fa-search-plus"></span></a>
          </div>
        </div>
      </div>
      <div class="widget-body" style="background-color: #666;">
        <div class="widget-main center">
          <canvas id="canvaspdf" class="noprint"></canvas>
        </div>
        <div class="widget-toolbox clearfix">
          <div class="btn-group" role="group">
              <a class="btn-primary btn-sm btn" type="button" id="prev2"><span class="fa fa-chevron-left"></span></a>
              <span style="float:left;padding: 0px 10px 0px 10px;">Page: <select class="no-padding" id="page_num2"></select> / <span id="page_count2"></span></span>
              <a class="btn-primary btn-sm btn" type="button" id="next2"><span class="fa fa-chevron-right"></span></a>
            </div>
            <div class="btn-group" role="group">
              <a class="btn-primary btn-sm btn" type="button" id="zoomout2"><span class="fa fa-search-minus"></span></a>
              <a class="btn-default btn-sm btn"><span id="scale2"></span></a>
              <a class="btn-primary btn-sm btn" type="button" id="zoomin2"><span class="fa fa-search-plus"></span></a>
            </div>
        </div>
      </div>
    </div>
  </div>
  <script src="/assets/js/jquery.2.1.1.min.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/pdf.js"></script>
  <script>
    var url = "/uploads/ebook/{{ $ebook->filename }}.pdf";

    var pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1.0,
        canvas = document.getElementById("canvaspdf"),
        ctx = canvas.getContext('2d');

    function renderPage(num) {
      pageRendering = true;
      pdfDoc.getPage(num).then(function(page) {
        var viewport = page.getViewport(scale);
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        var renderContext = {
          canvasContext: ctx,
          viewport: viewport
        };
        var renderTask = page.render(renderContext);
        renderTask.promise.then(function () {
          pageRendering = false;
          if (pageNumPending !== null) {
            renderPage(pageNumPending);
            pageNumPending = null;
          }
        });
      });

      $("#page_num").empty();
      $("#page_num2").empty();
      for (let i = 1; i <= pdfDoc.numPages; i++) {
        $("#page_num").append('<option value="'+ i +'">'+ i +'</option>');
        $("#page_num2").append('<option value="'+ i +'">'+ i +'</option>');
      }
      
      selectedPage(num);
      $("#scale").text(eval(scale * 100) + "%");
      $("#scale2").text(eval(scale * 100) + "%");
    }
    
    function queueRenderPage(num) {
      if (pageRendering) {
        pageNumPending = num;
      } else {
        renderPage(num);
      }
    }

    function selectedPage(num){
      $("#page_num").val(num).change();
      $("#page_num2").val(num).change();
    }
    
    function onPrevPage() {
      if (pageNum <= 1) {
        return;
      }
      pageNum--;
      selectedPage(pageNum);
      queueRenderPage(pageNum);
    }
    $("#prev").on("click", onPrevPage);
    $("#prev2").on("click", onPrevPage);
    
    function onNextPage() {
      if (pageNum >= pdfDoc.numPages) {
        return;
      }
      pageNum++;
      selectedPage(pageNum);
      queueRenderPage(pageNum);
    }
    $("#next").on('click', onNextPage);
    $("#next2").on('click', onNextPage);
    
    function onZoomin() {
      if(eval(scale * 100) < 150){
        scale = scale + 0.25;
        queueRenderPage(pageNum);
      }
    }
    $("#zoomin").on('click', onZoomin);
    $("#zoomin2").on('click', onZoomin);

    function onZoomout() {
      if(eval(scale * 100) > 50){
        scale = scale - 0.25;
        queueRenderPage(pageNum);
      }
    }
    $("#zoomout").on('click', onZoomout);
    $("#zoomout2").on('click', onZoomout);

    PDFJS.getDocument(url).then(function (pdfDoc_) {
      pdfDoc = pdfDoc_;
      $("#page_count").text(pdfDoc.numPages);
      $("#page_count2").text(pdfDoc.numPages);
      
      renderPage(pageNum);
    });

    function gotoPage(){
      pageNum = parseInt($("#page_num").val());
      var numPages = PDFJS.pagesCount;
      if((pageNum > numPages) || (pageNum < 1)){
          return;
      }
      queueRenderPage(pageNum);
    }

    function gotoPage2(){
      pageNum = parseInt($("#page_num2").val());
      var numPages = PDFJS.pagesCount;
      if((pageNum > numPages) || (pageNum < 1)){
          return;
      }
      queueRenderPage(pageNum);
    }

    $("#page_num").on("click", gotoPage);
    $("#page_num2").on("click", gotoPage2);
  </script>
@endsection