<?php
// Prevent any errors or notices from breaking the XML format
ini_set('display_errors', 0);
error_reporting(0);

// Set the header so the browser and search engines read this as an XML file
header("Content-Type: text/xml; charset=utf-8");

// --- UPDATED: Live subfolder URL ---
$baseUrl = "https://sapsri.lk/project-sedna"; 

require_once 'includes/connection.php';
Database::setUpConnection();
$conn = Database::$connection;

// Helper function to create an XML URL node
function createUrlNode($url, $lastmod = null, $changefreq = 'weekly', $priority = '0.8') {
    $xml = "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    if ($lastmod) {
        // Format to ISO 8601 (W3C Datetime) standard for sitemaps
        $date = date('c', strtotime($lastmod));
        $xml .= "    <lastmod>{$date}</lastmod>\n";
    }
    $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
    $xml .= "    <priority>{$priority}</priority>\n";
    $xml .= "  </url>\n";
    return $xml;
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// ==========================================
// 1. STATIC PAGES
// ==========================================
// Array of your static pages (excluding dashboard/auth files)
$staticPages = [
    '' => ['priority' => '1.0', 'freq' => 'daily'], // Homepage (index.php)
    '/pages/about-us.php' => ['priority' => '0.9', 'freq' => 'monthly'],
    '/pages/news.php' => ['priority' => '0.9', 'freq' => 'daily'],
    '/pages/ongoing-projects.php' => ['priority' => '0.9', 'freq' => 'weekly'],
    '/pages/past-projects.php' => ['priority' => '0.8', 'freq' => 'weekly'],
    '/pages/publications.php' => ['priority' => '0.8', 'freq' => 'weekly'],
    '/pages/people.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    '/pages/climate-and-biodiversity.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    '/pages/finance-and-governance.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    '/pages/gender-inclusion.php' => ['priority' => '0.7', 'freq' => 'monthly'],
    '/pages/sustainable-agriculture.php' => ['priority' => '0.7', 'freq' => 'monthly']
];

foreach ($staticPages as $path => $meta) {
    echo createUrlNode($baseUrl . $path, date('c'), $meta['freq'], $meta['priority']);
}

if ($conn) {
    // ==========================================
    // 2. DYNAMIC ONGOING PROJECTS
    // ==========================================
    $ongoing_query = "SELECT id, updated_at FROM projects WHERE status = 'published' AND project_phase = 'ongoing'";
    $ongoing_res = $conn->query($ongoing_query);
    if ($ongoing_res) {
        while ($row = $ongoing_res->fetch_assoc()) {
            $url = $baseUrl . "/pages/ongoing-project.php?id=" . $row['id'];
            echo createUrlNode($url, $row['updated_at'], 'weekly', '0.9');
        }
    }

    // ==========================================
    // 3. DYNAMIC PAST PROJECTS
    // ==========================================
    $past_query = "SELECT id, updated_at FROM projects WHERE status = 'published' AND project_phase = 'past'";
    $past_res = $conn->query($past_query);
    if ($past_res) {
        while ($row = $past_res->fetch_assoc()) {
            $url = $baseUrl . "/pages/past-project.php?id=" . $row['id'];
            echo createUrlNode($url, $row['updated_at'], 'monthly', '0.8');
        }
    }

    // ==========================================
    // 4. DYNAMIC NEWS POSTS
    // ==========================================
    $posts_query = "SELECT id, updated_at FROM posts WHERE status = 'published'";
    $posts_res = $conn->query($posts_query);
    if ($posts_res) {
        while ($row = $posts_res->fetch_assoc()) {
            $url = $baseUrl . "/pages/post.php?id=" . $row['id'];
            echo createUrlNode($url, $row['updated_at'], 'monthly', '0.8');
        }
    }
}

echo '</urlset>';
?>