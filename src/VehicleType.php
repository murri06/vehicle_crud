<?php
// VehicleType.php - Model class for vehicle types

class VehicleType
{
    public $id;
    public $title;
    public $created_at;
    public $updated_at;
    private $conn;
    private $table_title = "vehicle_types";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_title . " 
                  SET title=:title";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));

        // Bind data
        $stmt->bindParam(":title", $this->title);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all vehicle types
    function read()
    {
        $query = "SELECT * 
                  FROM " . $this->table_title . " 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read single vehicle type
    function readOne()
    {
        $query = "SELECT id, title, created_at, updated_at 
                  FROM " . $this->table_title . " 
                  WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Update vehicle type
    function update()
    {
        $query = "UPDATE " . $this->table_title . " 
                  SET title = :title 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete vehicle type
    function delete()
    {
        $query = "DELETE FROM " . $this->table_title . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Search vehicle types
    function search($keywords)
    {
        $query = "SELECT id, title, created_at, updated_at 
                  FROM " . $this->table_title . " 
                  WHERE title LIKE ? 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->execute();

        return $stmt;
    }
}

?>