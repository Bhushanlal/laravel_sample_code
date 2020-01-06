@extends('layouts.admin')
@section('title', 'Edit Eblast Campaign')
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/vendors/css/pickers/miniColors/jquery.minicolors.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/vendors/css/pickers/spectrum/spectrum.css') }}">
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">Manage Eblast Campaign</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/admin/dashboard'); ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?php echo url('/admin/banners'); ?>">Eblast Campaign List</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0);">Edit</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row" id="banner-detail-row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-icons">Edit Eblast Campaign Details</h4>
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
                    <div class="card-content collapse show administrator-page">
                        <div class="card-body">
                            <div class="card-text">
                            </div>
                            @include('elements.message')
                            @if ($errors->any())
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{!! $error !!}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <form id="editEblastForm" class="form form-horizontal"  accept-charset="UTF-8" role="form" method="POST" enctype="multipart/form-data" action="<?php echo url('admin/eblast/update'); ?>">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="patch" />
                                <input type="hidden" id="bannerFormData" name="form_content" value="" />
                                @include ('admin.eblast.form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('form-builder-script')
	<script src="{{ asset('admin-assets/vendors/js/pickers/miniColors/jquery.minicolors.min.js') }}"></script>
    <script src="{{ asset('admin-assets/vendors/js/pickers/spectrum/spectrum.js') }}"></script>
    <script src="{{ asset('admin-assets/js/scripts/pickers/colorpicker/picker-color.js') }}"></script>
    <script src="{{asset('admin-assets/js/core/libraries/jquery_ui/jquery-ui.min.js')}}"></script>
    <script src="{{asset('admin-assets/js/form-builder.min.js')}}"></script>
    <script src="{{asset('admin-assets/js/form-render.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            var formData = '{!! @$eblast->form_content !!}';
            if ($('#build-wrap').length > 0) {
                var formBuilder = $('#build-wrap').formBuilder({
                    disabledActionButtons: ['save', 'data'],
                    controlOrder: [
                        'text',
                        'select'
                    ],
                    disableFields: [
                        'autocomplete',
                        'checkbox-group',
                        'checkbox',
                        'file',
                        'header',
                        'hidden',
                        'number',
                        'paragraph',
                        'radio-group',
                        'textarea',
                        'date',
                        'button'
                    ]
                });
                setTimeout(function () {
                    formBuilder.actions.setData(formData);
                }, 1000);
            }
            $('#build-wrap').bind('contentchanged', function() {
                $('#bannerFormData').val(formBuilder.actions.getData('json', true));
            });
            $("#editEblastForm").validate({
                errorElement: 'p',
                errorClass: 'help-block',
                rules: {
                    title: {
                        required: true,
                        minlength: 3,
                        maxlength: 50,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    start_date: {
                        required: true,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    end_date: {
                        blankSpace: true,
                        required: true,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    eblast: {
                        required: true,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    } ,
                    slug: {
                        required: true,
                        minlength: 3,
                        maxlength: 50,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    } ,
                    banner_description: {
                        required: true,
                        minlength: 3,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    button_text: {
                        required: true,
                        minlength: 3,
                        maxlength: 50,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    button_color: {
                        required: true,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    border_color: {
                        required: true,
                        normalizer: function (value) {
                            return $.trim(value);
                        }
                    },
                    tag_name: {
                        required: true,
                        number: true
                    }

                },
                messages: {
                    title: {
                        required: 'Please enter the title.'
                    },
                    start_date: {
                        required: 'Please select the start date.'
                    },
                    end_date: {
                        required: 'Please select the end date.'
                    },
                    eblast: {
                        required: 'Please select the eblast.'
                    },
                    slug: {
                        required: 'Please enter the slug.'
                    },
                    banner_description: {
                        required: 'Please enter the description,'
                    },
                    form_banner: {
                        required: 'Please choose image file.'
                    },
                    button_text: {
                        required: 'Please enter form button text.'
                    },
                    button_color: {
                        required: 'Please choose a button color.'
                    },
                    border_color: {
                        required: 'Please choose a form border color.'
                    },
                    alt_text_banner: {
                        required: 'Please enter Alt Test for Form banner.'
                    },
                    tag_name: {
                        required: 'Please enter valid tag ID (number)',
                        number: 'Please enter a valid tag ID (number).'
                    }
                },
                errorPlacement: function (error, element) {
                    $(element).closest('.form-group').addClass('has-error');
                    if ($(element).next().hasClass('help-block')) {
                        $(element).next().remove();
                    }
                    $(element).after(error);
                    $(element).on('keypress keyup change', function () {
                        var resp = $(this).valid();
                        if (resp === false) {
                            $(element).closest('.form-group').addClass('has-error');
                        } else {
                            $(element).closest('.form-group').removeClass('has-error');
                        }
                    });
                },
                success: function (element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                submitHandler: function (form) {
                    $('#build-wrap').trigger('contentchanged');
                    let bfd = $('#bannerFormData').val();
                    if(bfd.length <= 2) {
                        $('#form-builed-message').html('<p style="color: red;float: right;">Please Build a form first!</p>').show().fadeOut(5000, function() {
                            $(this).hide();
                        });
                        return false;
                    }
                    return true;
                }
            });
        });
        CKEDITOR.replace( 'summary-ckeditor' );        
    </script>
@endsection
