<?

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

if(!(isset($_GET['name'])))
{
    $output = new JsonOutput('false', 'Invalid parameters', '');
    header('Cache-Control: no-cache, must-revalidate');
    header("content-type:application/json");
    echo json_encode($output);
    return;
}

$name = addslashes(strip_tags(trim($_GET['name'])));
$img_data = file_get_contents(getenv('OPENSHIFT_DATA_DIR') . $name);

$check = getimagesize(getenv('OPENSHIFT_DATA_DIR') . $name);
header("Content-type: ".$check["mime"]);
echo $img_data;
?>