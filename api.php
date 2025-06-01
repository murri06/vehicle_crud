<?php
// api.php - API endpoints with clean URL routing support

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'src/config.php';
if (!isLoggedIn()) {
    http_response_code(400);
    echo json_encode(array("message" => "Немає доступу."));
    die();
}
$canEdit = isAdmin();
include_once 'src/database.php';
include_once 'src/VehicleType.php';

$database = new Database();
$db = $database->getConnection();

$vehicleType = new VehicleType($db);

$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method !== "GET" && !$canEdit) {
    http_response_code(400);
    echo json_encode(array("message" => "Користувач не має доступу."));
    die();
}

$action = '';
$id = null;
$keywords = '';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    $keywords = $_GET['keywords'] ?? '';
} elseif (isset($_GET['route'])) {
    $route = trim($_GET['route'], '/');
    $route_parts = explode('/', $route);

    $action = $route_parts[0] ?? '';


    switch ($action) {
        case 'delete':
        case 'vehicle':
            $id = $route_parts[1] ?? null;
            break;
        case 'search':
            $keywords = $route_parts[1] ?? '';
            break;
    }

    if ($action === 'vehicle') {
        $action = 'readOne';
    }
} else {
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($request_uri, PHP_URL_PATH);

    $path = preg_replace('#^/[^/]*/api/#', '', $path);
    $path = trim($path, '/');

    if ($path) {
        $path_parts = explode('/', $path);
        $action = $path_parts[0];

        switch ($action) {
            case 'delete':
            case 'vehicle':
                $id = $path_parts[1] ?? null;
                break;
            case 'search':
                $keywords = $path_parts[1] ?? '';
                break;
        }

        if ($action === 'vehicle') {
            $action = 'readOne';
        }
    }
}

switch ($request_method) {
    case 'GET':
        switch ($action) {
            case 'readOne':
                if ($id) {
                    readOneVehicleType($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "ID parameter required"));
                }
                break;
            case 'search':
                if ($keywords) {
                    searchVehicleTypes(urldecode($keywords));
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Keywords parameter required"));
                }
                break;
            default:
                readVehicleTypes();
                break;
        }
        break;

    case 'POST':
        if ($action == 'create' || empty($action)) {
            createVehicleType();
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found"));
        }
        break;

    case 'PUT':
        if ($action == 'update' || empty($action)) {
            updateVehicleType();
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found"));
        }
        break;

    case 'DELETE':
        if ($action == 'delete') {
            if ($id) {
                deleteVehicleType($id);
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "ID parameter required"));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function readVehicleTypes()
{
    global $vehicleType;

    $stmt = $vehicleType->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $vehicle_types_arr = array();
        $vehicle_types_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $vehicle_type_item = array(
                "id" => $id,
                "title" => $title,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            $vehicle_types_arr["records"][] = $vehicle_type_item;
        }

        http_response_code(200);
        echo json_encode($vehicle_types_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No vehicle types found."));
    }
}

function readOneVehicleType($id)
{
    global $vehicleType;

    $vehicleType->id = $id;

    if ($vehicleType->readOne()) {
        $vehicle_type_arr = array(
            "id" => $vehicleType->id,
            "title" => $vehicleType->title,
            "created_at" => $vehicleType->created_at,
            "updated_at" => $vehicleType->updated_at
        );

        http_response_code(200);
        echo json_encode($vehicle_type_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Vehicle type does not exist."));
    }
}

function createVehicleType()
{
    global $vehicleType;
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->title)) {
        $vehicleType->title = $data->title;

        if ($vehicleType->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Vehicle type was created successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create vehicle type."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create vehicle type. Data is incomplete."));
    }
}

function updateVehicleType()
{
    global $vehicleType;

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id) && !empty($data->title)) {
        $vehicleType->id = $data->id;
        $vehicleType->title = $data->title;

        if ($vehicleType->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Vehicle type was updated successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update vehicle type."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to update vehicle type. Data is incomplete."));
    }
}

function deleteVehicleType($id)
{
    global $vehicleType;

    $vehicleType->id = $id;

    if ($vehicleType->delete()) {
        http_response_code(200);
        echo json_encode(array("message" => "Vehicle type was deleted successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete vehicle type."));
    }
}

function searchVehicleTypes($keywords)
{
    global $vehicleType;

    $stmt = $vehicleType->search($keywords);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $vehicle_types_arr = array();
        $vehicle_types_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $vehicle_type_item = array(
                "id" => $id,
                "title" => $title,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            $vehicle_types_arr["records"][] = $vehicle_type_item;
        }

        http_response_code(200);
        echo json_encode($vehicle_types_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No vehicle types found."));
    }
}

?>