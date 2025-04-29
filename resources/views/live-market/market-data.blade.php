<!DOCTYPE html>
<html>

<head>
    <title>Market Data</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="market-data"></div>

    <script>
        const eventSource = new EventSource('/market-data');
        const marketDataDiv = document.getElementById('market-data');

        eventSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            marketDataDiv.innerHTML = `
                <pre>${JSON.stringify(data, null, 2)}</pre>
            `;
        };

        eventSource.onerror = function(error) {
            console.error('EventSource failed:', error);
            eventSource.close();
        };
    </script>
</body>

</html>
