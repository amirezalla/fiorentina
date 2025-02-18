@php
    use GuzzleHttp\Client;
    use Symfony\Component\DomCrawler\Crawler;
    use Illuminate\Http\Request;
@endphp

@php
    // Create an HTTP client instance.
    $client = new Client();

    // Fetch the remote page.
    $response = $client->get('https://www.tuttocampo.it/Italia/Primavera/GironeA/Classifica');
    $html = $response->getBody()->getContents();

    // Use DomCrawler to parse the HTML.
    $crawler = new Crawler($html);

    // Try to extract the element with the class .table_ranking.
    try {
        $tableRankingHtml = $crawler->filter('.table_ranking')->html();
    } catch (\Exception $e) {
        $tableRankingHtml = '<p>Ranking table not found.</p>';
    }
@endphp

{{ $tableRankingHtml }}
