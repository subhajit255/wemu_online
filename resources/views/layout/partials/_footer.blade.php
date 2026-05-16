<!--begin::Javascript-->
<script>
    var baseUrl = "{{ url('/') }}";
</script>

<script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/custom_js/cdn/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom_js/cdn/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/custom_js/cdn/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/custom_js/cdn/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/js/custom_js/cdn/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-multiselect.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>
<script src="{{ asset('assets/js/operations.js') }}"></script>
<script src="{{ asset('assets/js/common.js') }}"></script>

<!--end::Custom Javascript-->

<script>
    $(document).ready(function() {
        const pageHeading = $('.page-heading').html();
        if (pageHeading != '') {
            $('.title').html(`${pageHeading} | WEMU`);
        } else {
            $('.title').html(`WEMU`);
        }
    });
    $(document).ready(function() {
        $(".dataTable").dataTable({});
    });
    $(window).on('load', function() {
        $("#preloader").fadeOut(500);
    });

    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end
                .format('YYYY-MM-DD'));
        });
    });
</script>

<!--end::Javascript-->
@stack('script')