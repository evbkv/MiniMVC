<?php
class User extends Model {
    protected static $table = 'users';
    protected static $fillable = ['email', 'password', 'role'];
    protected static $rules = [
        'email' => 'required|email|unique',
        'password' => 'required|min:8|max:72',
        'role' => 'required'
    ];
    
    public static function create($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::create($data);
    }
    
    public static function update($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::update($id, $data);
    }
}
?>