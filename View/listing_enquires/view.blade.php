@extends('layouts.admin')
@section('title', 'Manage Listing')
@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">View Listing Enquiry</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo url('/admin/dashboard'); ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Manage Listing Enquiries</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section id="horizontal-form-layouts">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                    <!-- <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                    </ul>
                </div>-->
                </div>
                <div class="card-content collpase show">
                    <div class="card-body">
                        <form id="" class="form form-horizontal" method="post" enctype="multipart/form-data">   
                       {{csrf_field()}}
                        <h4 class="form-section"><i class="ft-clipboard"></i>Listing Enquiry Details</h4>
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Advertiser Name</label>
                                <div class="col-md-9">
                                <p>{{ @$viewListingEnquiries->user->first_name }} {{ @$viewListingEnquiries->user->last_name }}</p>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Lead Type</label>
                                <div class="col-md-9">
                                <p>{{ @$viewListingEnquiries->enquiry_type }}</p>
                                </div>
                            </div>                                                   
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Lead Name</label>
                                <div class="col-md-9">
                                    <p>{{ @$viewListingEnquiries->name }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Lead Email</label>
                                <div class="col-md-9">
                                <p>{{ @$viewListingEnquiries->email }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Contact</label>
                                <div class="col-md-9">
                                <p>{{ @$viewListingEnquiries->phone }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Time To Call</label>
                                <div class="col-md-9">
                                <p>{{ @$viewListingEnquiries->time_to_call }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput5">Message</label>
                                <div class="col-md-9">
                                <p>{{ @$viewListingEnquiries->message }}</p>
                                </div>
                            </div>
                            <div class="form-actions right">
                                <button type="button" onclick="location.href='{{ url('admin/listing_enquiries') }}'" class="btn btn-outline-info edit-profile-btn">
                                    <i class="la la-check-circle-o"></i> Back
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
