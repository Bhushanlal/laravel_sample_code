@extends('layouts.admin')
@section('title', 'Manage Activities')
@section('content')
<div class="content-header row">
  <div class="content-header-left col-md-6 col-12 mb-2">
    <h3 class="content-header-title">Listing Enquiries Section</h3>
    <div class="row breadcrumbs-top">
      <div class="breadcrumb-wrapper col-12">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo url('/admin/dashboard'); ?>">Home</a>
          </li>
          <li class="breadcrumb-item active"><a href="javascript::void(0);">Listing Enquiries</a>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>
<div class="content-body">
  <div class="row" id="default">
    <div class="col-12">
      <div class="card" style="padding: 0 15px 15px 15px;">
        <div class="card-header">
          <h4 class="card-title">Manage Listing Enquiries</h4>
          <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
              <li><a href="{{ url('/admin/listing_enquiries') }}"><i class="ft-rotate-cw"></i></a></li>
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
          </div>
        </div>
        <div class="card-content collapse show administrator-page">
          @include('elements.message')
          <!-- <div class="table-actions-header col-12" style="padding:0"> -->
          <!-- <div class="row"> -->
          <!-- <div class="col-12 col-sm-4 col-md-5"> -->
          <!-- <form method="get" id="applyDateRange" action="" role="form">
          <div class="input-group">
          <label style="margin:10px"><strong>Date Filter:</strong></label>
          <fieldset style="margin-right: 4px">
          <input autocomplete="off" type="text" id="date_range" name="daterange" value="" placeholder="" class="form-control input-md"/>
          <input type="hidden" name="StartDate" value=""/>
          <input type="hidden" name="EndDate" value=""/>
          <button id="btnDateRangeSubmit" style="display:none" type="submit" name="submit" value="">Submit</button>
        </fieldset>
      </div>
    </form> -->
    <!-- </div> -->
    <!-- <div class="col-12 col-md-5 col-sm-8"> -->
    <!-- <form method="get" id="" action="" role="form">
    <div class="input-group">
    <label style="margin:10px"><strong>Manage Filter:</strong></label>
    <fieldset style="margin-right: 5px">
    <select class="form-control" name="action" id="basicSelect">

    <option value="">Select Role</option>
  </select>
  <input type="hidden" name="Select_Admin" value="">
  <button id="" type="submit" style="display:none;" name="submit" value="submit">Submit</button>
</fieldset>
<div class="mb-0 mb-sm-1">
<button id="" class="btn btn-primary bg-info border-info" type="button" name="submit" value="submit">Submit</button>
</div>

</div>

</form> -->
<!-- </div> -->
<!-- <div class="col-12 col-sm-12 col-md-2 mb-0 mb-sm-1"> -->
<!-- <form id="searchForm" method="get" action="javascript:void(0);" role="search">
<div class="input-group pull-right" style="margin:0 0px 15px 0px;max-width: 400px">
<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
<input autocomplete="off" name="search" type="text" class="form-control input-md" placeholder="Search.." aria-describedby="button-addon6">
<div class="input-group-append">
<button class="btn btn-primary bg-info border-info searchButton" type="button"> <i class="ft-search"></i></button>
<input type="hidden" name="action" value="/listing_enquiries">
</div>
</div>
</form> -->
<!-- </div> -->
<!-- </div> -->
<!-- </div> -->
<div class="card-body btn-div py-0 text-right mb-2 b-block">
  <a href="javascript:void(0);" rel="le" class="ajax-chart-btn active" ref="week">Week</a><a href="javascript:void(0);" rel="le" class="ajax-chart-btn" ref="month">Month</a>
</div>
<div class="card-body" style="position: relative; overflow: hidden;">
  <span class="dateRange font-weight-bold">{{ Carbon\Carbon::now()->startOfWeek()->format('jS M Y').' - '.Carbon\Carbon::now()->endOfWeek()->format('jS M Y') }}</span>
  <span class="monthDate font-weight-bold d-none">{{ Carbon\Carbon::now()->startOfMonth()->format('jS M Y').' - '.Carbon\Carbon::now()->endOfMonth()->format('jS M Y') }}</span>
  <div class="graphLoader d-none">
    <div class="loader">
      <div class="spinner"></div>
    </div>
  </div>
  <div id="area-stacked"></div>
</div>
<div class="bordr-table table-responsive" id="dynamicContent">
  <table class="table mb-0">
    <thead>
      <tr>
        <th>Sr. No</th>
        <th>Lead Name</th>
        <th>Lead Email</th>
        <th>Lead Contact</th>
        <th>Lead Type</th>
        <th>Date</th>
        <th>Advertiser Name</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $counter = 1;
      if (count($listingEnquiries)) {
        foreach ($listingEnquiries as $key => $value) {
          ?>
          <tr class="<?php echo!empty($value->is_read) ? 'read' : 'unread' ?>">
            <td>{{ $counter }}</td>
            <td>{{ @$value->name }}</td>
            <td>{{ @$value->email }}</td>
            <td>{{ @$value->phone }}</td>
            <td>{{ @$value->enquiry_type }}</td>
            <td>{{ @$value->created_at->format('d/m/Y') }}</td>
            <td>{{ @$value->user->first_name}} {{ @$value->user->last_name}}</td>
            <td>
              <?php
              $encryptId = Helper::encryptDataId($value->id);
              ?>
              <a href="{{ url('/admin/listing_enquiries/view/'.$encryptId) }}"><i title="View Enquiry" class="ft-eye"></i></a>
            </td>
          </tr>
          <?php
          $counter++;
        }
      } else {
        ?>
        <tr>
          <td class="text-center" colspan="7">No records found.</td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <?php
  if (count($listingEnquiries)) {
    ?>
    {!! $listingEnquiries->appends(['role'=>Request::get('role'),'view' => Request::get('view'),'page'=>Request::get('page'),'_token'=>csrf_token()])->render() !!}
  <?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
@endsection
