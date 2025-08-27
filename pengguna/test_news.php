<?php
// Test file to check news data
require_once 'news_fetcher.php';

echo "<h1>News Data Test</h1>";
echo "<pre>";
print_r($news_data);
echo "</pre>";

echo "<h2>URLs:</h2>";
foreach ($news_data as $index => $news) {
    echo "News $index: " . $news['url'] . "<br>";
}
?>
