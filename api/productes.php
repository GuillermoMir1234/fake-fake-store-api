<?php
include '../includes/errorHandler.proc.php';
include '../includes/dbConnect.proc.php';

// Peticions GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Retornar totes les categories
    if (isset($_GET['categories']) && $_GET['categories'] === 'all') {
        $result = $db->query("SELECT DISTINCT(category) FROM productes ORDER BY category");
        $categories = [];
        while ($categoria = $result->fetchArray(SQLITE3_ASSOC)){
            $categories[] = $categoria['category'];
        }
        //header('Content-Type: application/json');
        echo json_encode($categories);

    // Retornar tots els productes d'una categoria
    } else if(isset($_GET['category'])) {
        $stmt = $db->prepare("SELECT * FROM productes WHERE category = :cat ORDER BY title");
        $stmt->bindValue(':cat', $_GET['category'], SQLITE3_TEXT);
        $result = $stmt->execute();
        $productes = [];
        while ($producte = $result->fetchArray(SQLITE3_ASSOC)){
            $productes[] = [
                "id" => $producte['id'],
                "title" => $producte['title'],
                "price" => $producte['price'],
                "image" => $producte['image'],
                "rating" => [
                    "rate" => $producte['rating.rate'],
                    "count" => $producte['rating.count']
                ]
            ];
        }
        //header('Content-Type: application/json');
        echo json_encode($productes);

    // Retornar un producte concret
    } else if(isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM productes WHERE id = :id");
        $stmt->bindValue(':id', $_GET['id'], SQLITE3_TEXT);
        $result = $stmt->execute();
        $producte = [];
        if ($producte = $result->fetchArray(SQLITE3_ASSOC)){
            $producte = [
                "id" => $producte['id'],
                "title" => $producte['title'],
                "description" => $producte['description'],
                "price" => $producte['price'],
                "category" => $producte['category'],
                "image" => $producte['image'],
                "rating" => [
                    "rate" => $producte['rating.rate'],
                    "count" => $producte['rating.count']
                ]
            ];
        }

        //header('Content-Type: application/json');
        echo json_encode($producte);

    // Retornar tots els productes
    } else {
        $result = $db->query("SELECT * FROM productes ORDER BY title");
        $productes = [];
        while ($producte = $result->fetchArray(SQLITE3_ASSOC)){
            $productes[] = [
                "id" => $producte['id'],
                "title" => $producte['title'],
                "price" => $producte['price'],
                "image" => $producte['image'],
                "rating" => [
                    "rate" => $producte['rating.rate'],
                    "count" => $producte['rating.count']
                ]
            ];
        }
        //header('Content-Type: application/json');
        echo json_encode($productes);
    }


// Peticions POST
} else if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['title']) && isset($input['price']) && isset($input['description']) && isset($input['category']) && isset($input['image'])) {
        $stmt = $db->prepare("INSERT INTO productes (title, price, description, category, image, `rating.rate`, `rating.count`) VALUES (:title, :price, :description, :category, :image, 0, 0)");
        $stmt->bindValue(':title', $input['title'], SQLITE3_TEXT);
        $stmt->bindValue(':price', $input['price'], SQLITE3_FLOAT);
        $stmt->bindValue(':description', $input['description'], SQLITE3_TEXT);
        $stmt->bindValue(':category', $input['category'], SQLITE3_TEXT);
        $stmt->bindValue(':image', $input['image'], SQLITE3_TEXT);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => "Producte afegit correctament"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al inserir el producte"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Falten camps obligatoris"]);
    }
// Peticions PUT
} else if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id === null || !isset($input['title'], $input['price'], $input['description'], $input['category'], $input['image'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Falten l\'identificador o camps obligatoris']);
    } else {
        $stmt = $db->prepare(
            "UPDATE productes SET title = :title, price = :price, description = :description, category = :category, image = :image WHERE id = :id"
        );
        $stmt->bindValue(':title', $input['title'], SQLITE3_TEXT);
        $stmt->bindValue(':price', $input['price'], SQLITE3_FLOAT);
        $stmt->bindValue(':description', $input['description'], SQLITE3_TEXT);
        $stmt->bindValue(':category', $input['category'], SQLITE3_TEXT);
        $stmt->bindValue(':image', $input['image'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Producte actualitzat correctament']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error en l\'actualització del producte']);
        }
    }
// Peticions PATCH
} else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    // Leer el cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Falta l\'identificador del producte']);
    } else {
        // Obtener los campos válidos que se pueden actualizar
        $campos_validos = ['title', 'price', 'description', 'category', 'image'];
        $campos_a_actualizar = [];

        foreach ($campos_validos as $campo) {
            if (isset($input[$campo])) {
                $campos_a_actualizar[$campo] = $input[$campo];
            }
        }

        if (empty($campos_a_actualizar)) {
            http_response_code(400);
            echo json_encode(['error' => 'No s\'ha proporcionat cap camp per actualitzar']);
        } else {
            // Construir dinámicamente la consulta SQL
            $set_clause = [];
            foreach ($campos_a_actualizar as $campo => $valor) {
                $set_clause[] = "$campo = :$campo";
            }
            $set_clause_str = implode(', ', $set_clause);

            $stmt = $db->prepare("UPDATE productes SET $set_clause_str WHERE id = :id");
            foreach ($campos_a_actualizar as $campo => $valor) {
                $tipo = is_numeric($valor) ? SQLITE3_FLOAT : SQLITE3_TEXT;
                $stmt->bindValue(":$campo", $valor, $tipo);
            }
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

            if ($stmt->execute()) {
                echo json_encode(['success' => 'Producte actualitzat parcialment']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error en l\'actualització del producte']);
            }
        }
    }

// Peticions DELETE
} else if($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    $id = $_GET['id'] ?? null;
    if ($id === null) {
        parse_str(file_get_contents("php://input"), $params);
        $id = $params['id'] ?? null;
    }
    if ($id !== null) {
        $stmt = $db->prepare("DELETE FROM productes WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        if ($stmt->execute()) {
            echo json_encode(["success" => "Producte eliminat correctament"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No s'ha pogut eliminar el producte"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Falta l'identificador del producte"]);
    }
    
} else {
    http_response_code(400);
    echo json_encode(["error" => "Petició no acceptada"]);
}
?>
