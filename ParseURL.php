<?php
//using libraries to parse web page in php
include 'simple_html_dom.php';
//extracting the Wiki URL you want to start with
$url     = $_GET['link'];
$i       = 0;
$j       = 0;
//initialising hops and maxHops variables
$hops    = 0;
$maxHops = 25;
$path    = "";
//calling the function find_First_Link
find_First_Link($url);
//defined function to find first url in the first para of the wiki page
function find_First_Link($url)
{
    global $i, $j, $hops, $maxHops, $path;
	//exracting all html from the wiki page
    $html    = file_get_html($url);
    $heading = "";
	//used if else to structure the data stored inside path variable
    if (empty($path)) {
        $path = $url;
    } else {
        $path = $path . " , " . $url;
    }
    foreach ($html->find('h1#firstHeading') as $head) {
        $heading = strtolower(strip_tags($head));
    }
    //checking if the heading is philosophy
    if (strcmp($heading, "philosophy") == 0) {
        $flag = "reachable";
		//calling store_DB function to store the result in the DB
        store_DB($hops, $path, $flag);
    } else {
		//checking if it reached maxHops
        if ($hops >= $maxHops) {
            $flag = "not reachable";
            store_DB($hops, $path, $flag);
            
        } else {
            $hops = $hops + 1;
            //parsing the first para and trimming of all span,i,sup and table tags and the data inside
            foreach ($html->find('div.mw-parser-output p') as $para) {
                if ($i < 1) {
                    $tags   = array(
                        "span",
                        "i",
                        "sup",
                        "table"
                    );
                    $string = preg_replace('#<(' . implode('|', $tags) . ')(?:[^>]+)?>.*?</\1>#s', '', $para);
					//trimming of () and the data inside () but not in <a> tags
                    $re     = '~<a.*?</a>(*SKIP)(*FAIL)| *\([^()]*\) *~';
                    $subst  = ' ';
                    //$string = preg_replace('/\(([^()]*+|(?R))*\)\s*/', '', $string);
                    $string = preg_replace($re, $subst, $string);
					$dom = new DOMDocument();
                    $internalErrors = libxml_use_internal_errors(true);
					$dom->loadHTML($string);
					libxml_use_internal_errors($internalErrors);

                    //extracting the first <a> tag and href attribute
                    foreach ($dom->getElementsByTagName('a') as $element) {
                        if ($j < 1) {
                            $el     = $element->getAttribute('href');
                            $newurl = "https://en.wikipedia.org" . $el;
                            $html->clear();
                            ini_set('memory_limit', '-1');
                            find_First_Link($newurl);
                            
                        }
                        $j = $j + 1;
                        
                    }
                    
                }
                $i = $i + 1;
            }
        }
    }
}

function store_DB($hops, $path, $flag)
{
    // Connect to database server
    $connection = mysqli_connect("localhost", "username", "password") or die(mysqli_error());
    
    // Select database
    mysqli_select_db($connection, 'DBName') or die("Error in Connection");
    
    $sql = "INSERT INTO `phil`( `hops`, `path`,`reachable`) VALUES ('$hops','$path','$flag')";
    
    if (mysqli_query($connection, $sql)) {
        
    }
    
    else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
        mysqli_close($connection);
    }
	//calling create_json function after storing results in the db
    create_json($hops, $path, $flag);
    
}

function create_json($hops, $path, $flag)
{
	//storing data inside array object
    $data = array(
        'hops' => $hops,
        'path' => $path,
        'flag' => $flag
    );
    header('Content-Type: application/json');
	//encoding and sending it
    echo json_encode($data);
}

?>