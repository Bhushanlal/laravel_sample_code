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
    if (Request::get('page') and !empty(Request::get('page'))) {
      $page = Request::get('page') - 1;
      $counter = $listingEnquiries->perpage() * $page + 1;
    }
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
