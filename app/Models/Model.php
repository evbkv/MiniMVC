<?php
class Model {
    protected static $table;
    protected static $fillable = [];
    protected static $rules = [];
    
    protected static function db() {
        static $db = null;
        if ($db === null) {
            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, ENVIRONMENT === 'development' ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        return $db;
    }
    
    public static function query($sql, $params = []) {
        $stmt = self::db()->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(is_int($key) ? $key + 1 : ":$key", $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }
    
    public static function all() {
        $sql = "SELECT * FROM " . static::$table;
        return self::query($sql)->fetchAll();
    }
    
    public static function find($id) {
        $sql = "SELECT * FROM " . static::$table . " WHERE id = ?";
        return self::query($sql, [$id])->fetch();
    }
    
    public static function where($conditions) {
        $where = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $where[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $sql = "SELECT * FROM " . static::$table . " WHERE " . implode(' AND ', $where);
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function create($data) {
        $filteredData = self::filterFillable($data);
        self::validate($filteredData);
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";
        self::query($sql, $filteredData);
        return self::db()->lastInsertId();
    }
    
    public static function update($id, $data) {
        $filteredData = self::filterFillable($data);
        self::validate($filteredData, $id);
        
        $set = [];
        foreach ($filteredData as $key => $value) {
            $set[] = "$key = :$key";
        }
        $sql = "UPDATE " . static::$table . " SET " . implode(', ', $set) . " WHERE id = :id";
        $filteredData['id'] = $id;
        return self::query($sql, $filteredData)->rowCount();
    }
    
    public static function delete($id) {
        $sql = "DELETE FROM " . static::$table . " WHERE id = ?";
        return self::query($sql, [$id])->rowCount();
    }
    
    protected static function filterFillable($data) {
        if (empty(static::$fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip(static::$fillable));
    }
    
    protected static function validate($data, $id = null) {
        if (empty(static::$rules)) {
            return true;
        }
        
        $errors = [];
        foreach (static::$rules as $field => $rules) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $rules);
            
            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && (is_null($value) || $value === '')) {
                    $errors[$field][] = "Field $field is required";
                }
                
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "Field $field must be at least $min characters";
                    }
                }
                
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "Field $field must be at most $max characters";
                    }
                }
                
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "Field $field must be a valid email";
                }
                
                if ($rule === 'unique' && !$id) {
                    $existing = self::where([$field => $value]);
                    if (!empty($existing)) {
                        $errors[$field][] = "Field $field must be unique";
                    }
                }
                
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = "Field $field must be numeric";
                }
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return true;
    }
}

class ValidationException extends Exception {
    protected $errors;
    
    public function __construct($errors) {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }
    
    public function getErrors() {
        return $this->errors;
    }
}
?>