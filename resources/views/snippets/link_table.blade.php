<table id="{{$table_id}}" class="table table-hover">
    <thead>
        <tr>
            <th>Link Ending</th>
            <th class="hidden-xs">Long Link</th>
            <th class="hidden-xs">Clicks</th>
            <th class="hidden-xs">Date</th>
            @if ($table_id == "admin_links_table")
            {{-- Show action buttons only if admin view --}}
            <th class="hidden-xs">Creator</th>
            <th class="hidden-xs">Disable</th>
            <th class="hidden-xs">Delete</th>
            @endif
	    <th>Title</th>
	    <th class="hidden-xs">Description</th>
	    <th class="hidden-xs">Image</th>
        </tr>
    </thead>
</table>
