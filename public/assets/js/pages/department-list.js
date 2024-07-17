$(function() {
    var $table = $('#departments_table');
  
    $table.bootstrapTable({
      url: $table.data('url'),
      queryParams: queryDepartments,
      responseHandler: function(res) {
        return {
          "total": res.total,
          "rows": res.data
        };
      },
      columns: [
        { field: 'id', title: 'ID', sortable: true },
        { field: 'name', title: 'Name', sortable: true },
        { field: 'description', title: 'Description', sortable: true },
        { field: 'status', title: 'Status', sortable: true },
        { field: 'actions', title: 'Actions', formatter: actionsFormatter }
      ]
    });
  
    // Attach event listener for delete button
    $table.on('click', '.delete-department', function() {
      var id = $(this).data('id');
      deleteDepartment(id);
    });
  });
  
  function queryDepartments(params) {
    console.log('queryDepartments params:', params);
    return {
      limit: params.limit,
      offset: params.offset,
      search: params.search,
      sort: params.sort,
      order: params.order,
    };
  }
  
  function actionsFormatter(value, row, index) {
    console.log('actionsFormatter row:', row);
    return `
      <div class="btn-group">
        <a href="{{ url('/departments/') }}/${row.id}/edit" class="btn btn-sm btn-primary edit-department">
          <i class="bx bx-edit"></i>
        </a>
        <button type="button" class="btn btn-sm btn-danger delete-department" data-id="${row.id}">
          <i class="bx bx-trash"></i>
        </button>
      </div>
    `;
  }
  
  function deleteDepartment(id) {
    // Add your delete department logic here
    console.log('Deleting department with ID:', id);
  }