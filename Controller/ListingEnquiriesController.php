<?php

namespace App\Http\Controllers\Admin;

use App\BusinessPostEnquiry;
use Carbon\Carbon;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Helper;

class ListingEnquiriesController extends Controller
{
  public function index(Request $request) {
    if ($request->isMethod('post')) {
      $listingEnquiries = BusinessPostEnquiry::with('user')->latest()->paginate(100);
      $view = 'admin.listing_enquires.paginate';
    }else {
      $listingEnquiries =  BusinessPostEnquiry::with('user')->latest()->paginate(100);
      $view = 'admin.listing_enquires.index';
    }
    $startWeek = Carbon::now()->startOfWeek();
    $endWeek = Carbon::now()->endOfWeek();
    // $weeklistingEnquiries =  BusinessPostEnquiry::select(\DB::raw('count(*) as count'), \DB::raw('DATE(created_at) as date'))->whereBetween('created_at', [$startWeek, $endWeek])->orWhereNull('date')->groupBy('date')->get();
    $graphArray =  Helper::getLeadDatewise('business_post_enquiries',$startWeek,$endWeek);
    return view($view,compact('listingEnquiries','graphArray'));
  }

  public function show($id) {
    try{
      $id = Helper::decryptDataId($id);
      $viewListingEnquiries = BusinessPostEnquiry::with('User')->where('id', $id)->first();
      return view('admin.listing_enquires.view',compact('viewListingEnquiries'));
    }catch(\Exception $e){
      return redirect('/admin/listing_enquiries')->with('error_message', 'Something went wrong, please try again');
    }
  }

  // common ajax function for inquery
  public function ajaxChart(Request $request) {
    if($request->duration == 'week')
    {
      $startWeek = Carbon::now()->startOfWeek();
      $endWeek = Carbon::now()->endOfWeek();
      // $startWeek = Carbon::create(2019,06,17,0,0,0);
      // $endWeek = Carbon::create(2019,06,23,0,0,0);
    }
    if($request->duration == 'month')
    {
      $startWeek = Carbon::now()->startOfMonth();
      $endWeek = Carbon::now()->endOfMonth();
      // $startWeek = Carbon::create(2019,06,01,0,0,0);
      // $endWeek = Carbon::create(2019,06,30,0,0,0);
    }
    // get data for listing enquery
    if($request->type == 'le')
    $graphArray =  Helper::getLeadDatewise('business_post_enquiries',$startWeek,$endWeek);
    // get data for business post enquiries
    if($request->type == 'bae')
    $graphArray =  Helper::getLeadDatewise('business_form_contents',$startWeek,$endWeek,['type'=>'banner-ad']);
    // get data for elast enquiries
    if($request->type == 'ebe')
    $graphArray =  Helper::getLeadDatewise('business_form_contents',$startWeek,$endWeek,['type'=>'eblast']);
    // get data for e-commerce
    if($request->type == 'ecom')
    {
      $whereData = [
                ['is_old', 0],
                ['province_id', '!=', 1]
              ];
      $graphArray =  Helper::getLeadDatewise('users',$startWeek,$endWeek,$whereData,'lis');
    }
    return $graphArray;
  }
}
