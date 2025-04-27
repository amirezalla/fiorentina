@php
    $url = url('/diretta?match_id=' . urlencode($matchId));
@endphp



<script>
    window.location.replace(@js($url));
</script>
