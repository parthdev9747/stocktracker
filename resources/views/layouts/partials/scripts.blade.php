<script src="{{ URL::asset('build/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ URL::asset('build/js/axios.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/toastify-js/src/toastify.js') }}"></script>
<script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/form-validation.init.js') }}"></script>
<script src="{{ URL::asset('build/js/common.js') }}"></script>

<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>


<script>
    $(document).ready(function() {
        @if (session()->has('success'))
            Toastify({
                text: "{{ session()->get('success') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                className: "bg-success",
            }).showToast();
        @endif
        @if (session()->has('error'))
            Toastify({
                text: "{{ session()->get('error') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                className: "bg-danger",
            }).showToast();
        @endif
        @if (session()->has('warning'))
            Toastify({
                text: "{{ session()->get('warning') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                className: "bg-warning",
            }).showToast();
        @endif
    });
</script>

@stack('js')
