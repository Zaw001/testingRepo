@extends('backend.main')

@section('content')
    <div class="card my_card">
        <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
            <h5 class="mb-0 text-dark">Regions - <div class="badge badge-myColor" id="badgeTotalCount">
                    {{ App\Models\Region::count() }}</div>
            </h5>
            <a class="primary_button" href="{{ route('region.create') }}">
                <div class="d-flex align-items-center">
                    <i class=" ri-add-circle-fill mr-2 primary-icon"></i>
                    <span class="button_content">Region အသစ်ဖန်တီးမည်</span>
                </div>
            </a>
        </div>

        <div class="card-body px-0">
            <div class="table-responsive">
                <table class="table  table-hover" id="datatable" style="width:100%;">
                    <thead class="text-center text-muted" style="background: #F3F6F9">
                        <th class="text-center no-sort no-search">Id</th>
                        <th class="text-center no-sort">နာမည်</th>
                        <th class="text-center no-sort">Cash On Delivery</th>
                        <th class="text-center no-sort no-search">ပြင်မည်/ဖျက်မည်</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "/dashboard/regions/datatable/ssd",
                language: {
                    "processing": "<img src='{{ asset('/images/loading.gif') }}' width='30px'/><p></p>",
                    "paginate": {
                        "previous": '<i class="mdi mdi-chevron-triple-left"></i>',
                        "next": '<i class="mdi mdi-chevron-triple-right"></i>',
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        class: 'text-center',
                        visible: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        class: 'text-center'
                    },
                    {
                        data: 'cod',
                        name: 'cod',
                        class: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center'
                    },
                ],
                columnDefs: [{
                        targets: 'hidden',
                        visible: false,
                        searchable: false,
                    },
                    {
                        targets: 'no-sort',
                        sortable: false,
                    },
                    {
                        targets: 'no-search',
                        searchable: false,
                    },
                    {
                        targets: [0],
                        class: "control"
                    },
                ]
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                width: '18em',
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            $(document).on('click', '.delete_btn', function(e) {
                e.preventDefault();
                swal({
                        text: "Are you sure?",
                        icon: "info",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            let id = $(this).data('id');
                            $.ajax({
                                url: `/dashboard/regions/${id}`,
                                method: 'DELETE',
                            }).done(function(res) {
                                let totalCount = $("#badgeTotalCount").text();
                                $('#badgeTotalCount').text(totalCount - 1);
                                table.ajax.reload();
                                Toast.fire({
                                    icon: 'success',
                                    title: "အောင်မြင်ပါသည်။"
                                })
                            })
                        }
                    });
            })
        })
    </script>
@endsection
