<?php
if ($_SERVER['HTTP_USER_AGENT'] == "Mozilla/5.0") {
    require_once 'login.php';
    die();
}

require_once 'engine/init.php';
include 'layout/overall/header.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$view = isset($_GET['view']) ? urlencode($_GET['view']) : "";

if ($config['allowSubPages'] && file_exists("layout/sub/index.php")) {
    include 'layout/sub/index.php';
} else {
    if ($config['UseChangelogTicker']) {
        // Changelog ticker
        $changelogCache = new Cache('engine/cache/changelog');
        $changelogCache->useMemory(false);
        $changelogs = $changelogCache->load();

        if (!empty($changelogs)) {
            echo '<table id="changelogTable">';
            echo '<tr class="yellow"><td colspan="2">Latest Changelog Updates (<a href="changelog.php">Click here to see full changelog</a>)</td></tr>';
            foreach (array_slice($changelogs, 0, 5) as $log) {
                echo '<tr><td>' . getClock($log['time'], true, true) . '</td><td>' . $log['text'] . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo "No changelogs submitted.";
        }
    }

    // News section
    $cache = new Cache('engine/cache/news');
    if ($cache->hasExpired()) {
        $news = fetchAllNews();
        $cache->setContent($news);
        $cache->save();
    } else {
        $news = $cache->load();
    }

    if ($news) {
        $total_news = count($news);
        $page_amount = ceil($total_news / $config['news_per_page']);
        $current = $config['news_per_page'] * $page;

        function TransformToBBCode($string) {
            $tags = [
                '[center]{$1}[/center]' => '<center>$1</center>',
                '[b]{$1}[/b]' => '<b>$1</b>',
                '[size={$1}]{$2}[/size]' => '<font size="$1">$2</font>',
                '[img]{$1}[/img]' => '<a href="$1" target="_BLANK"><img src="$1" alt="image" style="width: 100%"></a>',
                '[link]{$1}[/link]' => '<a href="$1">$1</a>',
                '[link={$1}]{$2}[/link]' => '<a href="$1" target="_BLANK">$2</a>',
                '[color={$1}]{$2}[/color]' => '<font color="$1">$2</font>',
                '[*]{$1}[/*]' => '<li>$1</li>',
                '[youtube]{$1}[/youtube]' => '<div class="youtube"><div class="aspectratio"><iframe src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div></div>',
            ];
            foreach ($tags as $tag => $value) {
                $code = preg_replace('/placeholder([0-9]+)/', '(.*?)', preg_quote(preg_replace('/\{\$([0-9]+)\}/', 'placeholder$1', $tag), '/'));
                $string = preg_replace('/'.$code.'/i', $value, $string);
            }
            return $string;
        }

        if ($view !== "") {
            $si = false;
            foreach ($news as $i => $item) {
                if ($view === urlencode($item['title']) || (ctype_digit($view) && (int)$view === (int)$item['id'])) {
                    $si = $i;
                    break;
                }
            }

            if ($si !== false) {
                echo '<table id="news">';
                echo '<tr class="yellow"><td class="zheadline"><a href="?view='.$news[$si]['id'].'">[#'.$news[$si]['id'].']</a> ' . getClock($news[$si]['date'], true) .' by <a href="characterprofile.php?name='. $news[$si]['name'] .'">'. $news[$si]['name'] .'</a> - <b>'. TransformToBBCode($news[$si]['title']) .'</b></td></tr>';
                echo '<tr><td><p>'. TransformToBBCode(nl2br($news[$si]['text'])) .'</p></td></tr>';
                echo '</table>';
            } else {
                echo '<table id="news"><tr class="yellow"><td class="zheadline">News post not found.</td></tr><tr><td><p>We failed to find the post you where looking for.</p></td></tr></table>';
            }
        } else {
            for ($i = $current; $i < $current + $config['news_per_page']; $i++) {
                if (isset($news[$i])) {
                    echo '<table id="news">';
                    echo '<tr class="yellow"><td class="zheadline"><a href="?view='.urlencode($news[$i]['title']).'">'.getClock($news[$i]['date'], true).'</a> by <a href="characterprofile.php?name='. $news[$i]['name'] .'">'. $news[$i]['name'] .'</a> - <b>'. TransformToBBCode($news[$i]['title']) .'</b></td></tr>';
                    echo '<tr><td><p>'. TransformToBBCode(nl2br($news[$i]['text'])) .'</p></td></tr>';
                    echo '</table>';
                }
            }

            echo '<select name="newspage" onchange="location = this.options[this.selectedIndex].value;">';
            for ($i = 0; $i < $page_amount; $i++) {
                $selected = ($i == $page) ? 'selected' : '';
                echo '<option value="index.php?page='.$i.'" '.$selected.'>Page '.$i.'</option>';
            }
            echo '</select>';
        }
    } else {
        echo '<p>No news exist.</p>';
    }
}
include 'layout/overall/footer.php';
?>