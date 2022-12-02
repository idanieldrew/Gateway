<form name="myForm" id="myForm" target="_myFrame" action="https://core.paystar.ir/api/pardakht/payment" method="POST">
    @csrf
    <input type="hidden" value="{{$token}}" name="token">
    <input type="submit" value="submit">
</form>

<script type="text/javascript">
    window.onload = function () {
        var auto = setTimeout(function () {
            autoRefresh();
        }, 100);

        function submitform() {
            alert('test');
            document.forms["myForm"].submit();
        }

        function autoRefresh() {
            clearTimeout(auto);
            auto = setTimeout(function () {
                submitform();
                autoRefresh();
            }, 10000);
        }
    }
</script>
